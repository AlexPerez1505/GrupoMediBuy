<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SolicitudMaterial;
use App\Notifications\NuevaSolicitudRecibida;
use Illuminate\Http\Request;


class SolicitudMaterialController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudMaterial::where('user_id', auth()->id())->latest()->get();
        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $categorias = [
            'Papelería',
            'Limpieza',
            'Herramientas',
            'Administración',
            'Ventas',
            'Logística y Envíos',
            'Almacén',
            'Mantenimiento de Equipo Médico',
            'Servicio Técnico',
            'Sistemas / TI',
            'Compras',
            'Marketing',
            'Seguridad e Higiene',
            'Mobiliario de Oficina',
            'Uniformes',
            'Publicidad',
            'Capacitación',
            'Combustible y Transporte',
            'Reparaciones Generales',
            'Hojalatería y Pintura', // solo si tienen flotilla o vehículos
            'Otros'
        ];

        return view('solicitudes.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string',
            'material' => 'required|string',
            'cantidad' => 'required|integer|min:1',
            'justificacion' => 'nullable|string',
        ]);
    
        $solicitud = SolicitudMaterial::create([
            'user_id' => auth()->id(),
            'categoria' => $request->categoria,
            'material' => $request->material,
            'cantidad' => $request->cantidad,
            'justificacion' => $request->justificacion,
        ]);
    
        // Enviar notificación por correo al administrador
        $admin = User::where('email', 'al222111300@gmail.com')->first(); // O cambia al correo del admin real
        if ($admin) {
            $admin->notify(new NuevaSolicitudRecibida($solicitud));
        }
    
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud enviada correctamente.');
    }
    
 

    // VISTA ADMIN
    public function pendientes()
    {
        $solicitudes = SolicitudMaterial::whereIn('estado', ['Pendiente', 'En Planta'])->latest()->get();
        return view('solicitudes.admin', compact('solicitudes'));
    }

    public function marcarComoEnPlanta(SolicitudMaterial $solicitud)
    {
        $solicitud->update(['estado' => 'En Planta']);
        return back()->with('success', 'Material marcado como disponible en planta.');
    }

    public function entregar(SolicitudMaterial $solicitud)
    {
        $solicitud->update([
            'estado' => 'Entregado',
            'fecha_entrega' => now(),
            'entregado_por' => auth()->id(),
        ]);
        return back()->with('success', 'Solicitud marcada como entregada.');
    }

    public function rechazar(Request $request, SolicitudMaterial $solicitud)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:255',
        ]);

        $solicitud->update([
            'estado' => 'Rechazada',
            'motivo_rechazo' => $request->motivo_rechazo,
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada con éxito.');
    }
    public function misSolicitudesAjax()
    {
        // Obtiene las solicitudes del usuario autenticado, ordenadas por la fecha más reciente
        $solicitudes = auth()->user()->solicitudes()->latest()->get(); // Esto está bien si la relación está definida correctamente en el modelo User
    
        // Devuelve la vista con las solicitudes pasadas como una variable
        return view('partials.solicitudes', compact('solicitudes'));
    }
public function marcarEnPlanta(SolicitudMaterial $solicitud)
{
    $solicitud->estado = 'En Planta';
    $solicitud->save();

    return response()->json(['success' => true]);
}
// En el controlador SolicitudMaterialController
public function listado()
{
    $solicitudes = SolicitudMaterial::whereIn('estado', ['Pendiente', 'En Planta'])->latest()->get(); // O la lógica que desees para filtrar las solicitudes
    return view('partials.listado', compact('solicitudes')); // Esto renderiza solo el fragmento HTML
}





 

}
