<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacacion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VacacionesController extends Controller
{
    // Mostrar la vista de vacaciones con los datos del usuario
    public function index()
{
    $user = Auth::user();
    $solicitudes = Vacacion::where('user_id', $user->id)->get(); // Obtiene todas las solicitudes del usuario autenticado
    return view('vacaciones', compact('user', 'solicitudes'));
}
public function misSolicitudes()
{
    $solicitudes = Vacacion::where('user_id', auth()->id())->get();
    return view('mis_solicitudes', compact('solicitudes'));
}



    // Procesar la solicitud de vacaciones
    public function solicitar(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $user = Auth::user();
        $dias_solicitados = Carbon::parse($request->fecha_inicio)->diffInDays(Carbon::parse($request->fecha_fin)) + 1;

        if ($dias_solicitados > $user->vacaciones_disponibles) {
            return back()->with('error', 'No tienes suficientes días de vacaciones.');
        }

        // Guardar la solicitud en la base de datos
        Vacacion::create([
            'user_id' => $user->id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'tipo_permiso' => $request->tipo_permiso,
            'justificacion' => $request->justificacion,
            'estatus' => 'Pendiente',
        ]);

        // Descontar los días de vacaciones disponibles
        $user->vacaciones_disponibles -= $dias_solicitados;
        $user->vacaciones_utilizadas += $dias_solicitados;
        $user->save();

        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    // Mostrar todas las solicitudes pendientes (solo admin)
    public function listarSolicitudes()
    {
        $solicitudes = Vacacion::where('estatus', 'Pendiente')->with('user')->get();
        return view('vacaciones_solicitudes', compact('solicitudes'));
    }

    // Ver los detalles de una solicitud (solo admin)
    public function verSolicitud($id)
    {
        $solicitud = Vacacion::with('user')->findOrFail($id);
        return view('vacaciones_detalle', compact('solicitud'));
    }

    // Aprobar solicitud
    public function aprobar($id)
    {
        $solicitud = Vacacion::findOrFail($id);
        $solicitud->estatus = 'Aprobada';
        $solicitud->save();

        // Notificar al empleado (puedes agregar lógica para enviar una notificación aquí)

        return redirect()->route('vacaciones.listar')->with('success', 'Solicitud aprobada.');
    }

    // Rechazar solicitud
    public function rechazar(Request $request, $id)
    {
        // Validar el comentario
        $request->validate([
            'comentario' => 'required|string',
        ]);
    
        $vacacion = Vacacion::findOrFail($id);
        $vacacion->estatus = 'Rechazada';
        $vacacion->comentario = $request->comentario; // Almacena el comentario
        $vacacion->save();
    
        return redirect()->route('vacaciones.listar')->with('success', 'Solicitud rechazada y comentario agregado.');
    }
    
}
