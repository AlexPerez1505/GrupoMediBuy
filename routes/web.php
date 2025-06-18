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
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ValoracionController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\RemisionController;
use App\Http\Controllers\PagoController;
use App\Models\Cliente;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CartaGarantiaController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\NotificacionPagoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SeguimientoController;


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



Route::post('/servicio', [ServicioController::class, 'store'])->name('servicio.store');

Route::get('/detalles/{id}', [ServicioController::class, 'detalles'])->name('detalles.equipo');
Route::get('/inventario/servicio', [ServicioController::class, 'index'])->name('inventarioservicio');

// Mostrar formulario de cada tipo de movimiento
Route::get('/movimientos/salida-mantenimiento/{id}', [MovimientoController::class, 'salidaMantenimiento'])->name('movimientos.salidaMantenimiento');
Route::get('/movimientos/entrada-mantenimiento/{id}', [MovimientoController::class, 'entradaMantenimiento'])->name('movimientos.entradaMantenimiento');
Route::get('/movimientos/salida-dueno/{id}', [MovimientoController::class, 'salidaDueno'])->name('movimientos.salidaDueno');
Route::get('/movimientos/entrada-dueno/{id}', [MovimientoController::class, 'entradaDueno'])->name('movimientos.entradaDueno');

// Guardar movimiento (envío del formulario)
Route::post('/movimientos/guardar/{id}', [MovimientoController::class, 'guardar'])->name('movimientos.guardar');
Route::resource('prestamos', PrestamoController::class);
Route::get('/publicaciones', [PublicacionController::class, 'index'])->name('publicaciones.index');
Route::get('/publicaciones/crear', [PublicacionController::class, 'create'])->name('publicaciones.create');
Route::post('/publicaciones', [PublicacionController::class, 'store'])->name('publicaciones.store');
Route::post('/publicaciones/{id}/like', [PublicacionController::class, 'like'])->name('publicaciones.like');
Route::get('/publicaciones/fetch', [PublicacionController::class, 'fetchPublicaciones'])->name('publicaciones.fetch');
Route::get('/publicaciones/ultima-actualizacion', [PublicacionController::class, 'ultimaActualizacion'])->name('publicaciones.ultimaActualizacion');
Route::get('/publicaciones/{id}', [PublicacionController::class, 'show'])->name('publicaciones.show');

Route::post('/valorar', [ValoracionController::class, 'guardarValoracion'])->name('valorar')->middleware('auth');
Route::get('/cotizaciones', [CotizacionController::class, 'mostrarFormulario']);

// Mostrar un registro específico (GET para llenar el formulario)
Route::get('/registro/{id}', [App\Http\Controllers\RegistroController::class, 'mostrarRegistro'])->name('registro.mostrar');

// Actualizar un registro (PUT con AJAX)
Route::put('/registro/{id}', [App\Http\Controllers\RegistroController::class, 'actualizarRegistro'])->name('registro.actualizar');

Route::delete('/registro/{id}', [RegistroController::class, 'eliminarRegistro'])->name('registro.eliminar');

Route::resource('remisions', RemisionController::class);
Route::get('remisions/{remision}/descargar-pdf', [RemisionController::class, 'descargarPdf'])->name('remisions.descargarPdf');
Route::get('/validar-telefono', function (\Illuminate\Http\Request $request) {
    $valor = preg_replace('/\D/', '', $request->valor);
    $existe = Cliente::where('telefono', $valor)->exists();
    return response()->json(['existe' => $existe]);
});

Route::get('/validar-email', function (\Illuminate\Http\Request $request) {
    $valor = $request->valor;
    $existe = Cliente::where('email', $valor)->exists();
    return response()->json(['existe' => $existe]);
});

// routes/web.php
Route::get('/cotizaciones/duplicar/{id}', [CotizacionController::class, 'duplicar'])->name('cotizaciones.duplicar');
Route::get('/cuentas-por-cobrar', [RemisionController::class, 'cuentasPorCobrar'])->name('remisions.cuentasPorCobrar');
Route::put('/pagos_financiamiento/{id}/marcar-pagado', [VentaController::class, 'marcarPagado'])->name('pagos.marcarPagado');
Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
Route::get('/pagos/{pago}/recibo', [VentaController::class, 'reciboPago'])->name('pagos.recibo');
Route::get('/pagos/{item}', [PagoController::class, 'index'])->name('pagos.index');
Route::get('/pagos/{item}/recibo', [PagoController::class, 'generarPDF'])->name('pagos.recibo.pdf');
Route::get('/pagos/generar-pdf', [PagoController::class, 'generarPDF'])->name('pagos.generarPDF');
Route::middleware('auth')->group(function () {
    Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/deudores', [VentaController::class, 'deudores'])->name('ventas.deudores');
    // Nueva ruta para PDF
    Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf');
});
Route::middleware('web')->get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

Route::get('ventas/{venta}/edit', [VentaController::class, 'edit'])->name('ventas.edit');
Route::put('ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');

// Ver historial de asistencias de un usuario
Route::get('/asistencias/historial', [AsistenciaController::class, 'verHistorial'])->name('asistencias.historial');
Route::get('/asistencias/quincena', [AsistenciaController::class, 'obtenerAsistenciasQuincena'])->name('asistencias.quincena');
Route::get('/asistencia/verificar', [App\Http\Controllers\AsistenciaController::class, 'verificarAsistencia'])->name('asistencia.verificar');
Route::get('/mi-historial', [AsistenciaController::class, 'miHistorial'])
    ->name('mi-historial')
    ->middleware('auth');



// Guardar un nuevo pago para una venta



Route::get('/clientes', [VentaController::class, 'clientes'])->name('clientes.search');
Route::get('/api/ventas', [VentaController::class, 'apiVentas']);
Route::get('/carta-garantia', [CartaGarantiaController::class, 'index'])->name('carta.index');
Route::get('/carta-garantia/create', [CartaGarantiaController::class, 'create'])->name('carta.create');
Route::post('/carta-garantia', [CartaGarantiaController::class, 'store'])->name('carta.store');
Route::get('/carta-garantia/{id}/descargar', [CartaGarantiaController::class, 'descargar'])->name('carta.descargar');
Route::delete('/carta-garantia/{id}', [CartaGarantiaController::class, 'destroy'])->name('carta.destroy');
Route::get('/pagos/seguimiento/{item_id}', [PagoController::class, 'seguimientoInteligente'])->name('pagos.seguimiento');
Route::get('/ventas/{venta}/pagos', [VentaController::class, 'indexPagos'])->name('ventas.pagos.index');
Route::post('/ventas/{venta}/pagos', [VentaController::class, 'storePago'])->name('ventas.pagos.store');



Route::get('/verificar-recibo', [ReciboController::class, 'formularioVerificacion'])->name('recibo.verificar');
Route::post('/verificar-recibo', [ReciboController::class, 'verificar'])->name('recibo.verificar.post');

Route::get('/venta/{venta}/recibo-final', [VentaController::class, 'reciboFinal'])->name('venta.recibo.final');




Route::post('/financiamientos/notificar/{pago}', [NotificacionPagoController::class, 'reenviar']);
// Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create'); // NUEVA RUTA
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');        // NUEVA RUTA
Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');



Route::post('/clientes/update-asesor', [ClienteController::class, 'updateAsesor']);


// Categorías
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('/categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');


// Seguimientos
Route::get('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'index'])->name('seguimientos.index');
Route::post('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'store'])->name('seguimientos.store');
Route::delete('/seguimientos/{id}', [SeguimientoController::class, 'destroy'])->name('seguimientos.destroy');

Route::get('/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('clientes.buscar');
Route::post('/clientes/check-unique', [ClienteController::class, 'checkUnique'])->name('clientes.check-unique');


Route::get('/whatsapp/enviar/{venta}', [App\Http\Controllers\WhatsAppController::class, 'enviarRecordatorio']);
Route::patch('/seguimientos/{id}/completar', [SeguimientoController::class, 'completar'])->name('seguimientos.completar');


