<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Seguimiento;
use Carbon\Carbon;

class ClienteController extends Controller
{
public function index() {
    $clientes = Cliente::with('categoria')->get();

    $seguimientos = Seguimiento::whereNull('completado')->with('cliente')->get();

    $seguimientosPorCliente = $seguimientos->groupBy('cliente_id');

    $alertasGenerales = $seguimientos->filter(function ($s) {
        $fecha = \Carbon\Carbon::parse($s->fecha_seguimiento)->startOfDay();
        $hoy = now()->startOfDay();
        $dias = $hoy->diffInDays($fecha, false);

        // Solo mostrar seguimientos dentro de los próximos 7 días o vencidos
        return $dias <= 7;
    })->map(function ($s) {
        $fecha = \Carbon\Carbon::parse($s->fecha_seguimiento)->startOfDay();
        $dias = now()->startOfDay()->diffInDays($fecha, false);

        return [
            'seguimiento_id' => $s->id,
            'cliente' => $s->cliente,
            'fecha' => $s->fecha_seguimiento,
            'dias' => $dias, // puede ser negativo (vencido), 0 (hoy), o positivo
        ];
    });

    return view('clientes.index', compact('clientes', 'seguimientosPorCliente', 'alertasGenerales'));
}
public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20|unique:clientes,telefono',
        'email' => 'nullable|email|max:255|unique:clientes,email',
        'comentarios' => 'nullable|string',
        'categoria_id' => 'nullable|exists:categorias,id',
    ], [
        'telefono.unique' => 'El teléfono ya está registrado.',
        'email.unique' => 'El correo ya está registrado.',
    ]);

    $cliente = Cliente::create([
        'nombre' => $request->input('nombre'),
        'apellido' => $request->input('apellido'),
        'telefono' => $request->input('telefono'),
        'email' => $request->input('email') ?: null,
        'comentarios' => $request->input('comentarios'),
        'categoria_id' => $request->input('categoria_id'),
    ]);

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'cliente_id' => $cliente->id,
            'message' => 'Cliente creado exitosamente.'
        ]);
    }

    $redirect = $request->input('redirect_to', route('clientes.index'));
    return redirect($redirect)->with('cliente_creado', true);
}


    public function show($id)
    {
        $cliente = Cliente::with(['notas', 'seguimientos', 'categoria'])->findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        $categorias = Categoria::all();
        return view('clientes.edit', compact('cliente', 'categorias'));
    }

   public function update(Request $request, $id)
{
    $cliente = Cliente::findOrFail($id);

    $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20|unique:clientes,telefono,' . $cliente->id,
        'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
        'comentarios' => 'nullable|string',
        'categoria_id' => 'required|exists:categorias,id',
    ], [
        'telefono.unique' => 'El teléfono ya está registrado por otro cliente.',
        'email.unique' => 'El correo ya está registrado por otro cliente.',
    ]);

    $cliente->update([
        'nombre' => $request->nombre,
        'apellido' => $request->apellido,
        'telefono' => $request->telefono,
        'email' => $request->email ?: null,
        'comentarios' => $request->comentarios,
        'categoria_id' => $request->categoria_id,
    ]);

    return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
}


    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }

    public function checkUnique(Request $request)
    {
        $telefono = $request->input('telefono');
        $email = $request->input('email');

        $telefonoExistente = Cliente::where('telefono', $telefono)->exists();
        $emailExistente = !empty($email) ? Cliente::where('email', $email)->exists() : false;

        if ($telefonoExistente && $emailExistente) {
            return response()->json([
                'success' => false,
                'error_telefono' => 'El teléfono ya está registrado.',
                'error_email' => 'El correo ya está registrado.',
            ]);
        } elseif ($telefonoExistente) {
            return response()->json([
                'success' => false,
                'error_telefono' => 'El teléfono ya está registrado.',
            ]);
        } elseif ($emailExistente) {
            return response()->json([
                'success' => false,
                'error_email' => 'El correo ya está registrado.',
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getClients(Request $request)
    {
        $search = $request->input('search');

        $clients = Cliente::when($search, function ($query, $search) {
            return $query->where('nombre', 'like', "%$search%")
                         ->orWhere('apellido', 'like', "%$search%")
                         ->orWhere('telefono', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
        })->get(['nombre', 'apellido', 'telefono', 'email', 'comentarios']);

        return response()->json($clients);
    }

    public function updateAsesor(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:clientes,id',
            'asesor' => 'nullable|string|in:Jesús Tellez,Gabriela Diaz,Joel Diaz,Anahí Tellez'
        ]);

        $cliente = Cliente::findOrFail($request->id);
        $cliente->asesor = $request->asesor;
        $cliente->save();

        return response()->json(['message' => 'Asesor actualizado correctamente']);
    }
    public function create()
{
    $categorias = Categoria::all();
    return view('clientes.create', compact('categorias'));
}

}
