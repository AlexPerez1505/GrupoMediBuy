<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function store(Request $request)
    {
        // Validar datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'comentarios' => 'nullable|string',
        ]);
    
        // Crear el cliente
        $cliente = Cliente::create([
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'telefono' => $request->input('telefono'),
            'email' => $request->input('email') ?: null,
            'comentarios' => $request->input('comentarios'),
        ]);
    
        // Si la petición espera JSON (AJAX o API), responder con JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cliente_id' => $cliente->id,
                'message' => 'Cliente creado exitosamente.'
            ]);
        }
    
        // Si es una petición normal (HTML), redirige
        $redirect = $request->input('redirect_to', route('remisions.create'));
        return redirect($redirect)->with('cliente_creado', true);
    }
    
    
    public function checkUnique(Request $request)
    {
        $telefono = $request->input('telefono');
        $email = $request->input('email');
    
        // Verificar si el teléfono ya está registrado
        $telefonoExistente = Cliente::where('telefono', $telefono)->exists();
    
        // Verificar si el correo ya está registrado solo si no es nulo
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
        // Obtener los clientes filtrados según el término de búsqueda
        $search = $request->input('search');
    
        $clients = Cliente::when($search, function ($query, $search) {
            return $query->where('nombre', 'like', '%' . $search . '%')
                         ->orWhere('apellido', 'like', '%' . $search . '%')
                         ->orWhere('telefono', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%');
        })->get(['nombre', 'apellido', 'telefono', 'email', 'comentarios']); // <--- Agregar estos campos
    
        return response()->json($clients);
    }
    
    public function index()
    {
        $clientes = Cliente::all(); // Obtiene todos los clientes
        return view('clientes', compact('clientes'));
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
    
}

