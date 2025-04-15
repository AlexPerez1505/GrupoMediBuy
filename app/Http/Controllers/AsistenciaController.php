<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use Carbon\Carbon;
use App\Models\User;  // Importa el modelo User
class AsistenciaController extends Controller
{
    public function index()
    {
        $usuarios = User::all(); // Obtener todos los usuarios
        return view('asistencias', compact('usuarios')); // Pasa la variable usuarios a la vista
    }
    
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'estado' => 'required|in:asistencia,falta,permiso,vacaciones,retardo',
        ]);
    
        // Convertir la fecha
        $fecha = Carbon::parse($request->fecha);
    
        // Verificar si ya tiene asistencia en el día
        $existeAsistencia = Asistencia::where('user_id', $request->user_id)
                                      ->whereDate('fecha', $fecha->toDateString())
                                      ->exists();
    
     // Supongamos que $fechaSeleccionada es la fecha que has elegido o la que has guardado en la base de datos
$fechaSeleccionada = \Carbon\Carbon::parse($fecha)->format('d-m-Y');

// Verificar si ya existe una asistencia para esa fecha
if ($existeAsistencia) {
    // Mostrar mensaje con la fecha dinámica
    session()->flash('error_asistencia', 'Este usuario ya tiene una registro el ' . $fechaSeleccionada . '.');
    return redirect()->back();
}
    
        // Obtener usuario
        $usuario = User::find($request->user_id);
    
        switch ($request->estado) {
            case 'asistencia':
                $usuario->increment('asistencias');
                break;
    
            case 'falta':
                $usuario->increment('faltas');
                break;
    
            case 'permiso':
                if ($usuario->permisos <= 0) {
                    session()->flash('error_permiso', 'El usuario no tiene permisos disponibles.');
                    return redirect()->back();
                }
                $usuario->increment('permisos_utilizados');
                $usuario->decrement('permisos');
                break;
    
            case 'vacaciones':
                if ($usuario->vacaciones_disponibles <= 0) {
                    session()->flash('error_vacaciones', 'El usuario no tiene vacaciones disponibles.');
                    return redirect()->back();
                }
                $usuario->increment('vacaciones_utilizadas');
                $usuario->decrement('vacaciones_disponibles');
                break;
    
            case 'retardo':
                $retardosActuales = $usuario->retardos;
                $usuario->increment('retardos');
    
                if (($retardosActuales + 1) % 3 == 0) {
                    $usuario->increment('faltas');
                } else {
                    $usuario->increment('asistencias');
                }
                break;
        }
    
        // Guardar la asistencia
        Asistencia::create([
            'user_id' => $request->user_id,
            'fecha' => $fecha,
            'hora' => $request->hora,
            'estado' => $request->estado,
        ]);
    
        session()->flash('success', 'Asistencia registrada correctamente.');
        return redirect()->back();
    }
    public function obtenerAsistenciasQuincena()
    {
        // Obtener el rango de fechas de la quincena
        $inicio = Carbon::now()->day <= 15 ? Carbon::now()->startOfMonth() : Carbon::now()->startOfMonth()->addDays(15);
        $fin = $inicio->copy()->addDays(14);

        // Obtener las asistencias en el rango de fechas
        $asistencias = Asistencia::whereBetween('fecha', [$inicio, $fin])->get();
        return view('reporte-asistencias', compact('asistencias'));
    }
}
