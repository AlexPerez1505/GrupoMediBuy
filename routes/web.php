<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\EntregaGuiaController;
use App\Http\Controllers\VacacionesController;
use App\Models\User;
use App\Http\Controllers\ProcesoEquipoController;
use App\Http\Controllers\CamionetaController;
use App\Http\Controllers\FichaTecnicaController;
use App\Http\Controllers\SolicitudMaterialController;
use App\Http\Controllers\ServicioController;

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
Route::get('/productos/buscar', [ProductoController::class, 'search'])->name('productos.search');
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::post('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
    Route::post('/perfil/foto', [PerfilController::class, 'updatePhoto'])->name('perfil.updatePhoto');
});
Route::get('/usuarios', [PerfilController::class, 'allUsers'])->middleware('auth');
Route::get('/clientes/vista', [ClienteController::class, 'index'])->name('clientes.index');

Route::post('/clientes/update-asesor', [ClienteController::class, 'updateAsesor'])->name('clientes.updateAsesor');
Route::get('/historial-cotizaciones', function () {
    $cotizaciones = Cotizacion::all();
    return view('historial', compact('cotizaciones'));
})->name('historial-cotizaciones');
Route::get('/cotizaciones/descargarPDF/{id}', [CotizacionesController::class, 'descargarPDF'])->name('cotizaciones.descargarPDF');

// routes/api.php

Route::post('/eventos', [EventoController::class, 'store']);

Route::get('/agenda', function () { return view('agenda'); });
Route::get('/eventos', [EventoController::class, 'index']);
Route::post('/eventos', [EventoController::class, 'store']);
Route::put('/eventos/{id}', [EventoController::class, 'update']);
Route::get('evento/{id}', [EventoController::class, 'show']);


Route::get('/eventos/usuarios', [EventoController::class, 'usuarios']);



Route::delete('/eventos/{id}', [EventoController::class, 'destroy']);
Route::get('/servicio', function () {
    return view('servicio');
});
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/agregar', function () {
    return view('agregar');
})->name('users.create');
// Rutas para la gestión de asistencias
Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');  // Vista de todas las asistencias
Route::post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');  // Registrar asistencia
Route::get('/asistencias/quincena', [AsistenciaController::class, 'obtenerAsistenciasQuincena'])->name('asistencias.quincena'); 
Route::get('/paquetes', [PaqueteController::class, 'index']);
Route::post('/paquetes', [PaqueteController::class, 'store']);
Route::get('/cotizaciones', [PaqueteController::class, 'index'])->name('cotizaciones');
Route::get('/paquete/{id}/productos', [PaqueteController::class, 'getProductosDePaquete']);
Route::get('/paquetes/search', [PaqueteController::class, 'search'])->name('paquetes.search');
Route::middleware(['auth'])->group(function () {
    Route::get('/guias', [GuiaController::class, 'create'])->name('guias.create');
    Route::post('/guias', [GuiaController::class, 'store'])->name('guias.store');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/entrega', [EntregaGuiaController::class, 'create'])->name('entrega.create');
    Route::post('/entrega', [EntregaGuiaController::class, 'store'])->name('entrega.store'); // ESTA ES LA IMPORTANTE
});
// Ruta para mostrar la vista con las entregas
Route::get('/entregas', [EntregaGuiaController::class, 'index'])->name('entregas.index');
Route::get('/verificar-guia/{numero}', function ($numero) {
    return response()->json(['existe' => Guia::where('numero_rastreo', $numero)->exists()]);
});
Route::get('/entrega/guias', [EntregaGuiaController::class, 'getGuias']);
Route::middleware(['auth'])->group(function () {
    Route::get('/vacaciones', [VacacionesController::class, 'index'])->name('vacaciones.index');
    Route::post('/vacaciones/solicitar', [VacacionesController::class, 'solicitar'])->name('vacaciones.solicitar');
});


    // Ver solicitudes pendientes
    Route::get('/vacaciones/solicitudes', [VacacionesController::class, 'listarSolicitudes'])->name('vacaciones.listar');

    // Ver detalles de una solicitud específica
    Route::get('/vacaciones/{id}', [VacacionesController::class, 'verSolicitud'])->name('vacaciones.ver');

    // Aprobar solicitud
    Route::post('/vacaciones/{id}/aprobar', [VacacionesController::class, 'aprobar'])->name('vacaciones.aprobar');

    // Rechazar solicitud
    Route::post('/vacaciones/{id}/rechazar', [VacacionesController::class, 'rechazar'])->name('vacaciones.rechazar');

    Route::get('/home', function () {
        return view('home');
    });
    
    
    Route::get('/eventos/usuarios', function () {
        return response()->json(App\Models\User::select('id', 'name')->get());
    });
Route::get('/procesos/{id}', [ProcesoEquipoController::class, 'mostrarProceso'])->name('procesos.mostrar');
Route::post('/procesos/{id}/guardar', [ProcesoEquipoController::class, 'guardarProceso'])
    ->name('procesos.guardar')
    ->middleware('auth');


Route::get('/procesos/{id}/hojalateria', function ($id) {
    return view('procesos.hojalateria', ['id' => $id]);
})->name('proceso.hojalateria');
Route::get('/procesos/{id}/vendido', function ($id) {
    return view('procesos.vendido', ['id' => $id]);
})->name('proceso.vendido');

Route::get('/procesos/{id}/mantenimiento', function ($id) {
    return view('procesos.mantenimiento', ['id' => $id]);
})->name('proceso.mantenimiento');

Route::get('/procesos/{id}/stock', function ($id) {
    return view('procesos.stock', ['id' => $id]);
})->name('proceso.stock');

Route::get('/procesos/{id}/defectuoso', function ($id) {
    return view('procesos.defectuoso', ['id' => $id]);
})->name('proceso.defectuoso');
Route::post('/producto/{id}/defectuoso', [ProductoController::class, 'marcarComoDefectuoso']);
Route::get('/procesos/{id}/completados', [ProcesoEquipoController::class, 'obtenerProcesosCompletados']);
Route::get('/mis-solicitudes', [VacacionesController::class, 'misSolicitudes'])->name('mis.solicitudes');




Route::get('/camionetas/agregar', [CamionetaController::class, 'create'])->name('camionetas.create');
Route::post('/camionetas/store', [CamionetaController::class, 'store'])->name('camionetas.store');

Route::post('/camionetas', [CamionetaController::class, 'store'])->name('camionetas.store');
Route::get('/camionetas/{camioneta}/editar', [CamionetaController::class, 'edit'])->name('camionetas.edit');
Route::put('/camionetas/{camioneta}', [CamionetaController::class, 'update'])->name('camionetas.update');
Route::delete('/camionetas/{camioneta}', [CamionetaController::class, 'destroy'])->name('camionetas.destroy');
// Ruta para mostrar los detalles de la camioneta
Route::get('/camionetas/{id}', [CamionetaController::class, 'show'])->name('camionetas.show');
Route::get('/camionetas', [CamionetaController::class, 'index'])->name('camionetas.index');
Route::put('/camionetas/{id}', [CamionetaController::class, 'update'])->name('camionetas.update');




Route::resource('fichas', FichaTecnicaController::class);
Route::get('fichas/{ficha}/download', [FichaTecnicaController::class, 'download'])->name('fichas.download');
Route::get('/cotizaciones/recrear/{id}', [CotizacionController::class, 'recrear'])->name('cotizaciones.recrear');
Route::get('procesos/{id}/stock', [ProcesoEquipoController::class, 'stock'])->name('stock');

Route::get('/solicitudes', [SolicitudMaterialController::class, 'index'])->name('solicitudes.index');
Route::get('/solicitudes/crear', [SolicitudMaterialController::class, 'create'])->name('solicitudes.create');
Route::post('/solicitudes', [SolicitudMaterialController::class, 'store'])->name('solicitudes.store');

// Estas también quedan accesibles sin autenticación
Route::get('/admin/solicitudes', [SolicitudMaterialController::class, 'pendientes'])->name('solicitudes.admin');
Route::put('/solicitudes/{solicitud}/planta', [SolicitudMaterialController::class, 'marcarComoEnPlanta'])->name('solicitudes.marcarEnPlanta');
Route::put('/solicitudes/{solicitud}/entregar', [SolicitudMaterialController::class, 'entregar'])->name('solicitudes.entregar');
Route::put('/solicitudes/{solicitud}/rechazar', [SolicitudMaterialController::class, 'rechazar'])->name('solicitudes.rechazar');


Route::get('/mis-solicitudes/ajax', [SolicitudMaterialController::class, 'misSolicitudesAjax'])->name('solicitudes.ajax');

// En routes/web.php
Route::get('/solicitudes/listado', [App\Http\Controllers\SolicitudMaterialController::class, 'listado'])->name('solicitudes.listado');






Route::get('/cotizaciones/recrear/{id}', [CotizacionController::class, 'recrear'])->name('cotizacion.recrear');


Route::get('/servicio', [ServicioController::class, 'index'])->name('servicio.index');
Route::post('/servicio', [ServicioController::class, 'store'])->name('servicio.store');

Route::get('/detalles/{id}', [ServicioController::class, 'detalles'])->name('detalles.equipo');
Route::get('/inventarioservicio', [ServicioController::class, 'index'])->name('inventario.servicio');
