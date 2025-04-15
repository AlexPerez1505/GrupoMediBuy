<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Importar el modelo User para verificar si el usuario existe.

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // La vista de login
    }

    public function login(Request $request)
    {
        // Validación de los datos del formulario
        $credentials = $request->validate([
            'nomina' => ['required', 'string'],
            'contrasena' => ['required', 'string'],
        ]);

        // Verificamos si el usuario existe
        $user = User::where('nomina', $credentials['nomina'])->first();

        // Si el usuario no existe
        if (!$user) {
            return back()->withErrors([
                'nomina' => 'Usuario incorrecto.',
            ]);
        }

        // Intentamos hacer login con las credenciales proporcionadas
        if (Auth::attempt(['nomina' => $credentials['nomina'], 'password' => $credentials['contrasena']])) {
            $request->session()->regenerate();
            return redirect('/home');
        }

        // Si el login falla debido a la contraseña incorrecta
        return back()->withErrors([
            'contrasena' => 'Contraseña incorrecta.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
