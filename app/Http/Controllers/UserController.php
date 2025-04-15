<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'nomina' => 'required|string|unique:users,nomina',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'cargo' => 'required|string',
            'puesto' => 'required|string',
            'vacaciones_disponibles' => 'nullable|integer',
            'vacaciones_utilizadas' => 'nullable|integer',
            'permisos' => 'nullable|integer',
            'retardos' => 'nullable|integer',
            'role' => 'required|string|in:admin,editor,user',
            'imagen' => 'nullable|image|max:2048',
        ]);

        // Subir la imagen si existe
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes', 'public');
        }

        // Crear el usuario y asignar valores, con valores predeterminados para los campos nulos
        User::create([
            'nomina' => $request->nomina,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'cargo' => $request->cargo,
            'puesto' => $request->puesto,
            'vacaciones_disponibles' => $request->vacaciones_disponibles,
            'vacaciones_utilizadas' => $request->vacaciones_utilizadas ?? 0, // Si no se pasa valor, se asigna 0
            'permisos' => $request->permisos ?? 0, // Si no se pasa valor, se asigna 0
            'retardos' => $request->retardos ?? 0, // Si no se pasa valor, se asigna 0
            'role' => $request->role,
            'imagen' => $imagenPath,
        ]);

        // Retornar vista de éxito
        return redirect()->route('users.create')->with('success', 'Usuario registrado exitosamente');
    }
  
public function showChangePasswordForm()
{
    return view('auth.change-password'); // Asegúrate de que esta vista exista
}

/**
 * Actualizar la contraseña del usuario.
 */
public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => [
            'required',
            'string',
            'min:8',
            'regex:/[a-z]/', // Al menos una letra minúscula
            'regex:/[A-Z]/', // Al menos una letra mayúscula
            'regex:/[0-9]/', // Al menos un número
            'regex:/[@$!%*?&]/', // Al menos un carácter especial
            'confirmed' // Debe coincidir con new_password_confirmation
        ],
    ], [
        'new_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'new_password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
        'new_password.confirmed' => 'Las contraseñas no coinciden.',
    ]);

    $user = Auth::user();

    // Verificar que la contraseña actual sea correcta
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
    }

    // Actualizar la contraseña
    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->back()->with('success', '¡Contraseña actualizada correctamente!');
}

}
