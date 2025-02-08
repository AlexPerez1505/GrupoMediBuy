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

        // Crear el cliente en la base de datos
        $cliente = Cliente::create([
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'telefono' => $request->input('telefono'),
            'email' => $request->input('email'),
            'comentarios' => $request->input('comentarios'),
        ]);

        // Redirigir o responder con éxito
        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente',
        ]);
    }

    public function checkUnique(Request $request)
    {
        $telefono = $request->input('telefono');
        $email = $request->input('email');
    
        // Verificar si el teléfono ya está registrado
        $telefonoExistente = Cliente::where('telefono', $telefono)->exists();
        // Verificar si el correo ya está registrado
        $emailExistente = Cliente::where('email', $email)->exists();
    
        // Retornar los mensajes específicos si el teléfono o el correo ya existen
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
        // Obtener los clientes filtrados según el término de búsqueda, si existe
        $search = $request->input('search');
        $clients = Cliente::when($search, function ($query, $search) {
            return $query->where('nombre', 'like', '%' . $search . '%')
                         ->orWhere('apellido', 'like', '%' . $search . '%'); // Buscar también por apellido
        })->get(['nombre', 'apellido']); // Devolver solo nombre y apellido
    
        return response()->json($clients);
    }
    

    
}

