<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\User;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index()
    {
        $usuarios = User::all(); // Obtener todos los usuarios
        return view('asistencias', compact('usuarios'));
    }
public function store(Request $request) 
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'fecha' => 'required|date',
        'hora' => 'required|date_format:H:i',
        'estado' => 'required|in:asistencia,falta,permiso,vacaciones,retardo,salida',
    ]);

    $fecha = Carbon::parse($request->fecha);
    $userId = $request->user_id;
    $estado = $request->estado;

    // Estados que solo pueden tener un registro por día por usuario (excluye salida)
    $estadosUnicos = ['asistencia', 'falta', 'permiso', 'vacaciones', 'retardo'];

    if (in_array($estado, $estadosUnicos)) {
        // Verificar si ya hay cualquier registro con esos estados para el usuario y fecha
        $registroExistente = Asistencia::where('user_id', $userId)
            ->whereDate('fecha', $fecha->toDateString())
            ->whereIn('estado', $estadosUnicos)
            ->exists();

        if ($registroExistente) {
            session()->flash('error_asistencia', 'Ya existe un registro de asistencia, falta, permiso, vacaciones o retardo para este usuario en la fecha ' . $fecha->format('d-m-Y') . '.');
            return redirect()->back()->withInput();
        }
    }

    if ($estado === 'salida') {
        // Validar que exista entrada de asistencia o retardo
        $asistenciaEntrada = Asistencia::where('user_id', $userId)
            ->whereDate('fecha', $fecha->toDateString())
            ->whereIn('estado', ['asistencia', 'retardo'])
            ->first();

        if (!$asistenciaEntrada) {
            session()->flash('error_asistencia', 'No se puede registrar salida porque no existe asistencia o retardo ese día para el usuario.');
            return redirect()->back()->withInput();
        }

        if ($asistenciaEntrada->hora_salida) {
            session()->flash('error_asistencia', 'Ya existe una hora de salida registrada para este usuario en esta fecha.');
            return redirect()->back()->withInput();
        }

        $asistenciaEntrada->hora_salida = $request->hora;
        $asistenciaEntrada->save();

        session()->flash('success', 'Hora de salida registrada correctamente.');
        return redirect()->back();
    }

    $usuario = User::find($userId);

    // Verificar permisos y vacaciones disponibles antes de incrementar
    if ($estado === 'permiso' && $usuario->permisos <= 0) {
        session()->flash('error_permiso', 'El usuario no tiene permisos disponibles.');
        return redirect()->back()->withInput();
    }

    if ($estado === 'vacaciones' && $usuario->vacaciones_disponibles <= 0) {
        session()->flash('error_vacaciones', 'El usuario no tiene vacaciones disponibles.');
        return redirect()->back()->withInput();
    }

    // Procesar incrementos y decrementos según estado
    switch ($estado) {
        case 'asistencia':
            $usuario->increment('asistencias');
            break;

        case 'falta':
            $usuario->increment('faltas');
            break;

        case 'permiso':
            $usuario->increment('permisos_utilizados');
            $usuario->decrement('permisos');
            break;

        case 'vacaciones':
            $usuario->increment('vacaciones_utilizadas');
            $usuario->decrement('vacaciones_disponibles');
            break;

        case 'retardo':
            $retardosActuales = $usuario->retardos;
            $usuario->increment('retardos');

            // Cada 3 retardos se suma una falta
            if (($retardosActuales + 1) % 3 == 0) {
                $usuario->increment('faltas');
            } else {
                $usuario->increment('asistencias');
            }
            break;
    }

    Asistencia::create([
        'user_id' => $userId,
        'fecha' => $fecha,
        'hora' => $request->hora,
        'estado' => $estado,
    ]);

    session()->flash('success', 'Registro de ' . $estado . ' guardado correctamente.');
    return redirect()->back();
}




    public function obtenerAsistenciasQuincena()
    {
        $inicio = Carbon::now()->day <= 15
            ? Carbon::now()->startOfMonth()
            : Carbon::now()->startOfMonth()->addDays(15);

        $fin = $inicio->copy()->addDays(14);

        $asistencias = Asistencia::whereBetween('fecha', [$inicio, $fin])->get();

        return view('reporte-asistencias', compact('asistencias'));
    }

public function verHistorial(Request $request)
{
    $usuarios = User::all();
    $id = $request->query('id');
    $asistencias = [];

    if ($id) {
        $query = Asistencia::where('user_id', $id);

        // Filtro por rango de fechas si se envían
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        $asistencias = $query->orderBy('fecha', 'desc')->get();
    }

    return view('historial-asistencias', compact('usuarios', 'asistencias', 'id'));
}
public function miHistorial(Request $request)
{
    $user = Auth::user();
    $asistencias = Asistencia::where('user_id', $user->id)
        ->when($request->filled('fecha_inicio'), fn($q) =>
            $q->whereDate('fecha', '>=', $request->fecha_inicio))
        ->when($request->filled('fecha_fin'), fn($q) =>
            $q->whereDate('fecha', '<=', $request->fecha_fin))
        ->get();

    return view('mi-historial', compact('asistencias'));
}
    
public function verificarAsistencia(Request $request)
{
    $userId = $request->user_id;
    $fecha = Carbon::parse($request->fecha)->toDateString();

    $registro = Asistencia::where('user_id', $userId)
        ->whereDate('fecha', $fecha)
        ->whereIn('estado', ['asistencia', 'retardo'])
        ->exists();

    return response()->json(['tieneEntrada' => $registro]);
}
}
