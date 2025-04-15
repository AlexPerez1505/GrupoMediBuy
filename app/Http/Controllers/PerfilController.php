<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class PerfilController extends Controller
{
    // Mostrar el perfil del usuario
    public function index()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        return view('perfil', compact('user'));
    }

    // Actualizar datos personales (nombre, teléfono, correo, etc.)
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validación de los campos
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'cargo' => 'nullable|string|max:255',
            'puesto' => 'nullable|string|max:255',
        ]);

        // Actualizar los datos del usuario
        $user->update($request->only(['name', 'phone', 'email', 'cargo', 'puesto']));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    // Actualizar la foto de perfil
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        // Validar la imagen
        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Si el usuario sube una nueva imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($user->imagen) {
                Storage::delete('public/' . $user->imagen);
            }

            // Guardar la nueva imagen en la carpeta `storage/app/public/perfiles`
            $path = $request->file('imagen')->store('perfiles', 'public');
            $user->imagen = $path;
            $user->save();
        }

        return back()->with('success', 'Foto de perfil actualizada correctamente.');
    }
    public function allUsers()
    {
        // Verifica si el usuario autenticado es "admin"
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }
    
        $usuarios = User::all();
        return view('usuarios', compact('usuarios'));

    }
    

}
