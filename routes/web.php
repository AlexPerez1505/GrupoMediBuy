<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('Login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('registro');
    });

    Route::get('/inventario', [RegistroController::class, 'mostrarProductos'])->name('inventario');
    Route::post('/registro/guardar', [RegistroController::class, 'guardarRegistro'])->name('registro.guardar');
    Route::get('/obtener-detalles/{id}', [RegistroController::class, 'obtenerDetalles']);
});

// Ruta para restablecer la contraseña (formularios)
Route::get('/reset-password/{token}', function ($token) {
    return view('reset_password', ['token' => $token]);
})->name('password.reset');

// Ruta para actualizar la contraseña
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Ruta para mostrar el formulario
Route::get('/auth/change-password', [UserController::class, 'showChangePasswordForm'])
    ->name('auth.change-password')
    ->middleware('auth');

// Ruta para procesar el formulario
Route::post('/auth/change-password', [UserController::class, 'updatePassword'])
    ->name('auth.update-password')
    ->middleware('auth');


Route::get('/buscar-registros', [CotizacionController::class, 'buscarRegistros']);




Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::post('/clientes/check_unique', [ClienteController::class, 'checkUnique'])->name('clientes.check_unique');

Route::get('/remisiones', function () {
    return view('remisiones');
});
Route::get('/agenda', function () {
    return view('agenda');
});

Route::get('/clientes', [ClienteController::class, 'getClients'])->name('clientes.get');


Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/cotizaciones', [ProductoController::class, 'index'])->name('cotizaciones.index');

Route::get('/productos/buscar', [ProductoController::class, 'buscar']);

Route::get('/generar-cotizacion/{id}', [CotizacionController::class, 'generarCotizacion'])->name('generarCotizacion');
Route::post('/guardar-cotizacion', [CotizacionController::class, 'guardarCotizacion']);



Route::get('/cotizacion/pdf', [CotizacionController::class, 'generarCotizacionPDF'])->name('cotizacion.pdf');
Route::post('/generar-cotizacion-pdf', [CotizacionController::class, 'generarCotizacionPDF']);


Route::get('/cotizacion/prueba', [CotizacionController::class, 'generarPDF'])->name('cotizacion.prueba');
Route::post('/guardar-cotizacion', [CotizacionController::class, 'store']);

Route::get('/descargar-cotizacion/{id}', [CotizacionController::class, 'descargarPDF']);
