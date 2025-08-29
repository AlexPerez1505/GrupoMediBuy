<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\EntregaGuiaController;
use App\Http\Controllers\ProcesoEquipoController;
use Illuminate\Support\Facades\Route;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Controllers\CamionetaController;
use App\Http\Controllers\FichaTecnicaController;
use App\Http\Controllers\SolicitudMaterialController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\MovimientoController;
use App\Models\User;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ValoracionController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\RemisionController;
use App\Models\Cliente;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CartaGarantiaController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\AparatoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemParteController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistFirmaController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\WhatsappPromotionController;
use App\Http\Controllers\WhatsappWebhookController;
use App\Http\Controllers\WhatsappInboxController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\CashTransactionController;
// routes/web.php
use App\Http\Controllers\PromoController;

// Rutas de autenticación (sin middleware 'auth')
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('Login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas para recuperación de contraseña (sin middleware 'auth')
Route::get('/reset-password/{token}', function ($token) {
    return view('reset_password', ['token' => $token]);
})->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Agrupar todas las demás rutas dentro de 'auth'
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('registro');
    });

    Route::get('/inventario', [RegistroController::class, 'mostrarProductos'])->name('inventario');
    Route::post('/registro/guardar', [RegistroController::class, 'guardarRegistro'])->name('registro.guardar');
    Route::get('/obtener-detalles/{id}', [RegistroController::class, 'obtenerDetalles']);

    Route::get('/auth/change-password', [UserController::class, 'showChangePasswordForm'])->name('auth.change-password');
    Route::post('/auth/change-password', [UserController::class, 'updatePassword'])->name('auth.update-password');

    Route::get('/buscar-registros', [CotizacionController::class, 'buscarRegistros']);

    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');


    Route::get('/remisiones', function () { return view('remisiones'); });
    Route::get('/agenda', function () { return view('agenda'); });



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

    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::post('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
    Route::post('/perfil/foto', [PerfilController::class, 'updatePhoto'])->name('perfil.updatePhoto');

    Route::get('/usuarios', [PerfilController::class, 'allUsers']);

    Route::get('/historial-cotizaciones', function () {
        $cotizaciones = Cotizacion::all();
        return view('historial', compact('cotizaciones'));
    })->name('historial-cotizaciones');

    Route::get('/cotizaciones/descargarPDF/{id}', [CotizacionController::class, 'descargarPDF'])->name('cotizaciones.descargarPDF');

    Route::post('/eventos', [EventoController::class, 'store']);
    Route::get('/eventos', [EventoController::class, 'index']);
    Route::put('/eventos/{id}', [EventoController::class, 'update']);
    Route::get('evento/{id}', [EventoController::class, 'show']);
    Route::delete('/eventos/{id}', [EventoController::class, 'destroy']);

    Route::get('/servicio', function () { return view('servicio'); });
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/agregar', function () { return view('agregar'); })->name('users.create');

    // Rutas para asistencias
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
    Route::get('/asistencias/quincena', [AsistenciaController::class, 'obtenerAsistenciasQuincena'])->name('asistencias.quincena');

    Route::get('/paquetes', [PaqueteController::class, 'index']);
    Route::post('/paquetes', [PaqueteController::class, 'store']);
    Route::get('/cotizaciones', [PaqueteController::class, 'index'])->name('cotizaciones');
    Route::get('/paquete/{id}/productos', [PaqueteController::class, 'getProductosDePaquete']);
    Route::get('/paquetes/search', [PaqueteController::class, 'search'])->name('paquetes.search');

    // Rutas para guías y entregas
    Route::get('/guias', [GuiaController::class, 'create'])->name('guias.create');
    Route::post('/guias', [GuiaController::class, 'store'])->name('guias.store');

    Route::get('/entrega', [EntregaGuiaController::class, 'create'])->name('entrega.create');
    Route::post('/entrega', [EntregaGuiaController::class, 'store'])->name('entrega.store');

    // Vista con entregas
    Route::get('/entregas', [EntregaGuiaController::class, 'index'])->name('entregas.index');

    
});
Route::get('/inventario/buscar', [RegistroController::class, 'vistaBuscar'])->name('inventario.buscar');
Route::get('/inventario/buscar/resultado', [RegistroController::class, 'buscarSubmit'])->name('inventario.buscar.submit');
// Ruta para mostrar el detalle de un equipo/registro por su ID
Route::get('/inventario/{id}', [App\Http\Controllers\RegistroController::class, 'mostrarProductoDetalle'])->name('inventario.detalle');
Route::get('/inventario', [RegistroController::class, 'mostrarProductos'])->name('inventario');
Route::get('/inventario/main', [RegistroController::class, 'mostrarProductos'])->name('inventario.main');
Route::get('/registros/{id}/imprimir-barcode', [\App\Http\Controllers\RegistroController::class, 'imprimirBarcode'])
    ->name('registros.imprimirBarcode');


Route::get('/procesos/{id}', [ProcesoEquipoController::class, 'mostrarProceso'])->name('procesos.mostrar');
Route::post('/procesos/{id}/guardar', [ProcesoEquipoController::class, 'guardarProceso'])->name('procesos.guardar');

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
Route::get('/procesos/{id}/stock', [ProcesoEquipoController::class, 'stock'])->name('proceso.stock');


Route::get('/solicitudes', [SolicitudMaterialController::class, 'index'])->name('solicitudes.index');
Route::get('/solicitudes/crear', [SolicitudMaterialController::class, 'create'])->name('solicitudes.create');
Route::post('/solicitudes', [SolicitudMaterialController::class, 'store'])->name('solicitudes.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/solicitudes', [SolicitudMaterialController::class, 'pendientes'])->name('solicitudes.admin');
    Route::put('/solicitudes/{solicitud}/planta', [SolicitudMaterialController::class, 'marcarComoEnPlanta'])->name('solicitudes.marcarEnPlanta');
    Route::put('/solicitudes/{solicitud}/entregar', [SolicitudMaterialController::class, 'entregar'])->name('solicitudes.entregar');
    Route::put('/solicitudes/{solicitud}/rechazar', [SolicitudMaterialController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::get('/mis-solicitudes/ajax', [SolicitudMaterialController::class, 'misSolicitudesAjax'])->name('solicitudes.ajax');
    Route::get('/solicitudes/listado', [SolicitudMaterialController::class, 'listado'])->name('solicitudes.listado');
});

Route::get('/eventos/usuarios', [EventoController::class, 'usuarios']);


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

// Ver historial de asistencias de un usuario
Route::get('/asistencias/historial', [AsistenciaController::class, 'verHistorial'])->name('asistencias.historial');
Route::get('/asistencias/quincena', [AsistenciaController::class, 'obtenerAsistenciasQuincena'])->name('asistencias.quincena');
Route::get('/asistencia/verificar', [App\Http\Controllers\AsistenciaController::class, 'verificarAsistencia'])->name('asistencia.verificar');
Route::get('/mi-historial', [AsistenciaController::class, 'miHistorial'])
    ->name('mi-historial')
    ->middleware('auth');


// --- PAGOS ---
Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
Route::get('/pagos/{item}', [PagoController::class, 'index'])->name('pagos.index');
Route::get('/pagos/{pago}/recibo', [VentaController::class, 'reciboPago'])->name('pagos.recibo');
Route::get('/pagos/{item}/recibo', [PagoController::class, 'generarPDF'])->name('pagos.recibo.pdf');
Route::get('/pagos/generar-pdf', [PagoController::class, 'generarPDF'])->name('pagos.generarPDF');
Route::get('/pagos/seguimiento/{item_id}', [PagoController::class, 'seguimientoInteligente'])->name('pagos.seguimiento');

// --- CUENTAS POR COBRAR ---
Route::get('/cuentas-por-cobrar', [RemisionController::class, 'cuentasPorCobrar'])->name('remisions.cuentasPorCobrar');

// --- PAGOS FINANCIAMIENTO ---
Route::put('/pagos_financiamiento/{id}/marcar-pagado', [VentaController::class, 'marcarPagado'])->name('pagos.marcarPagado');
Route::get('/ventas/pendientes', [VentaController::class, 'pendientes'])->name('ventas.pendientes');
// --- VENTAS ---
Route::middleware('auth')->group(function () {
    Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/deudores', [VentaController::class, 'deudores'])->name('ventas.deudores');
    Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf');
});
Route::middleware('web')->get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/{venta}/pagos', [VentaController::class, 'indexPagos'])->name('ventas.pagos.index');
Route::post('/ventas/{venta}/pagos', [VentaController::class, 'storePago'])->name('ventas.pagos.store');
Route::get('/ventas/{venta}/edit', [VentaController::class, 'edit'])->name('ventas.edit');
Route::put('/ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');
Route::get('/api/ventas', [VentaController::class, 'apiVentas']);
Route::get('/venta/{venta}/recibo-final', [VentaController::class, 'reciboFinal'])->name('venta.recibo.final');

// --- CARTA GARANTÍA ---
Route::get('/carta-garantia', [CartaGarantiaController::class, 'index'])->name('carta.index');
Route::get('/carta-garantia/create', [CartaGarantiaController::class, 'create'])->name('carta.create');
Route::post('/carta-garantia', [CartaGarantiaController::class, 'store'])->name('carta.store');
Route::get('/carta-garantia/{id}/descargar', [CartaGarantiaController::class, 'descargar'])->name('carta.descargar');
Route::delete('/carta-garantia/{id}', [CartaGarantiaController::class, 'destroy'])->name('carta.destroy');

// --- RECIBOS ---
Route::get('/verificar-recibo', [ReciboController::class, 'formularioVerificacion'])->name('recibo.verificar');
Route::post('/verificar-recibo', [ReciboController::class, 'verificar'])->name('recibo.verificar.post');

// --- NOTIFICACIONES ---
Route::post('/financiamientos/notificar/{pago}', [NotificacionPagoController::class, 'reenviar']);

// --- CLIENTES ---
Route::get('/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('clientes.buscar');
Route::get('/encontrar-clientes', [PropuestaController::class, 'encontrarClientes'])->name('clientes.encontrar');

// --- SEGUIMIENTOS ---
Route::get('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'index'])->name('seguimientos.index');
Route::post('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'store'])->name('seguimientos.store');
Route::delete('/seguimientos/{id}', [SeguimientoController::class, 'destroy'])->name('seguimientos.destroy');


Route::get('/whatsapp/enviar/{venta}', [App\Http\Controllers\WhatsAppController::class, 'enviarRecordatorio']);
Route::patch('/seguimientos/{id}/completar', [SeguimientoController::class, 'completar'])->name('seguimientos.completar');

Route::get('/cuentas/crear', [CuentaController::class, 'create'])->name('cuentas.create');
Route::post('/cuentas', [CuentaController::class, 'store'])->name('cuentas.store');
Route::get('/cuentas', [CuentaController::class, 'index'])->name('cuentas.index');
Route::delete('/cuentas/{id}', [CuentaController::class, 'destroy'])->name('cuentas.destroy');
Route::get('/cuentas/exportar/pdf', [CuentaController::class, 'exportarPDF'])->name('cuentas.exportar.pdf');
Route::get('/cuentas/{cuenta}/edit', [CuentaController::class, 'edit'])->name('cuentas.edit');
Route::put('/cuentas/{cuenta}', [CuentaController::class, 'update'])->name('cuentas.update');

// Mostrar listado de propuestas
Route::get('propuestas', [PropuestaController::class, 'index'])->name('propuestas.index');

// Formulario para crear nueva propuesta
Route::get('propuestas/create', [PropuestaController::class, 'create'])->name('propuestas.create');

// Guardar nueva propuesta
Route::post('propuestas', [PropuestaController::class, 'store'])->name('propuestas.store');

// Ver detalles de una propuesta
Route::get('propuestas/{propuesta}', [PropuestaController::class, 'show'])->name('propuestas.show');

// Formulario para editar propuesta
Route::get('propuestas/{propuesta}/edit', [PropuestaController::class, 'edit'])->name('propuestas.edit');


// Actualizar propuesta
Route::put('propuestas/{propuesta}', [PropuestaController::class, 'update'])->name('propuestas.update');
Route::patch('propuestas/{propuesta}', [PropuestaController::class, 'update']);

// Eliminar propuesta
Route::delete('propuestas/{propuesta}', [PropuestaController::class, 'destroy'])->name('propuestas.destroy');

// Generar PDF de la propuesta
Route::get('propuestas/{propuesta}/pdf', [PropuestaController::class, 'pdf'])->name('propuestas.pdf');
Route::get('/recepciones/create', [RecepcionController::class, 'create'])->name('recepciones.create');
Route::post('/recepciones', [RecepcionController::class, 'store'])->name('recepciones.store');

Route::get('/recepciones', [RecepcionController::class, 'index'])->name('recepciones.index');
Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
Route::get('/recepciones/create/{pedido}', [RecepcionController::class, 'createDesdePedido'])->name('recepciones.createDesdePedido');
Route::post('/recepciones/store', [RecepcionController::class, 'storeDesdePedido'])->name('recepciones.storeDesdePedido');
Route::get('/recepciones/timeline', [RecepcionController::class, 'showHistorialGlobal'])->name('recepciones.timeline.global');
Route::get('/reporte-pedidos', [\App\Http\Controllers\PedidoController::class, 'formReporte'])->name('pedidos.reporte.form');
Route::get('/recepciones/pdf', [RecepcionController::class, 'exportarPDF'])->name('recepciones.timeline.pdf');






use App\Models\ModuloUso;
use Illuminate\Support\Facades\Auth;

Route::get('/home', function () {
    $tusAccesos = collect([
        '/publicaciones',
        '/inventario',
        '/ventas',
        '/propuestas',
        '/remisions',
        '/ventas/deudores',
        '/clientes',
        '/agenda',
        '/camionetas',
        '/perfil',
        '/fichas',
        '/carta-garantia',
        '/solicitudes/crear',
        '/cuentas',
        '/prestamos',
        '/usuarios',
        '/pedidos',
        route('asistencias.index', [], false),
    ]);

   $modulosRecientes = ModuloUso::where('user_id', Auth::id())
    ->where('updated_at', '>=', now()->subHours(12))
    ->orderByDesc('updated_at')
    ->get()
    ->filter(function ($modulo) use ($tusAccesos) {
        return !$tusAccesos->contains(url($modulo->ruta));
    })
    ->take(6);


    return view('home', compact('modulosRecientes'));
});



// Clientes
Route::post('/clientes/check_unique', [ClienteController::class, 'checkUnique'])->name('clientes.check_unique');
Route::get('/clientes', [ClienteController::class, 'getClients'])->name('clientes.get');
Route::get('/clientes/vista', [ClienteController::class, 'index'])->name('clientes.index');
Route::post('/clientes/update-asesor', [ClienteController::class, 'updateAsesor'])->name('clientes.updateAsesor');
Route::get('/clientes', [VentaController::class, 'clientes'])->name('clientes.search');
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
Route::post('/clientes/check-unique', [ClienteController::class, 'checkUnique'])->name('clientes.check-unique');

Route::get('/registros-disponibles', [RegistroController::class, 'registrosStock']);
Route::get('/registro-info/{id}', [RegistroController::class, 'info']);





Route::get('orden/create', [OrdenController::class, 'create'])->name('orden.create');
Route::post('orden',       [OrdenController::class, 'store'])->name('orden.store');
Route::get('orden/{orden}/pdf', [OrdenController::class, 'pdf'])->name('orden.pdf');
Route::resource('aparatos', AparatoController::class);

Route::get('aparatos/{aparato}/checklist-items', [AparatoController::class, 'checklistItems']);


// Resourceful routes
Route::resource('items', ItemController::class);
Route::resource('item-partes', ItemParteController::class);
Route::resource('checklists', ChecklistController::class);
Route::resource('checklist-firmas', ChecklistFirmaController::class);
Route::resource('incidentes', IncidenteController::class);
Route::resource('evidencias', EvidenciaController::class);

Route::get('checklists/{checklist}/proceso', [App\Http\Controllers\ChecklistController::class, 'proceso'])
    ->name('checklists.proceso');
Route::post('checklists/{checklist}/proceso/guardar-paso', [App\Http\Controllers\ChecklistController::class, 'guardarPaso'])
    ->name('checklists.proceso.guardarPaso');




Route::get('/ventas/{venta}/productos', [VentaController::class, 'productos'])->name('ventas.productos');


// Mostrar el formulario del checklist para una venta completa
Route::get('/checklists/{ventaId}/wizard', [ChecklistController::class, 'wizard'])->name('checklists.wizard');
Route::post('/checklists/{ventaId}/guardar', [ChecklistController::class, 'guardarPaso'])->name('checklists.guardarPaso');
Route::get('/checklists/{ventaId}/resumen', [ChecklistController::class, 'resumen'])->name('checklists.resumen');


Route::prefix('checklists/{venta}')->middleware('auth')->group(function() {
    Route::get('wizard',        [ChecklistController::class, 'wizard'])->name('checklists.wizard');
    Route::get('ingenieria',    [ChecklistController::class, 'ingenieria'])->name('checklists.ingenieria');
    Route::post('ingenieria',   [ChecklistController::class, 'guardarIngenieria'])->name('checklists.guardarIngenieria');
    Route::get('embalaje',      [ChecklistController::class, 'embalaje'])->name('checklists.embalaje');
    Route::post('embalaje',     [ChecklistController::class, 'guardarEmbalaje'])->name('checklists.guardarEmbalaje');
    Route::get('entrega',       [ChecklistController::class, 'entrega'])->name('checklists.entrega');
    Route::post('entrega',      [ChecklistController::class, 'guardarEntrega'])->name('checklists.guardarEntrega');
});



// GET: Mostrar el formulario de recepción hospitalaria
Route::get('/recepcion-hospital/{checklist}', [App\Http\Controllers\ChecklistController::class, 'recepcionHospital'])->name('recepcion-hospital');

// POST: Guardar la recepción hospitalaria
Route::post('/recepcion-hospital/{checklist}', [App\Http\Controllers\ChecklistController::class, 'guardarRecepcionHospital'])->name('recepcion-hospital.guardar');

Route::get('/checklists/{checklist}/descargar-pdf', [App\Http\Controllers\ChecklistController::class, 'descargarPdf'])->name('checklists.descargar-pdf');

Route::get('registros/{id}/imprimir-barcode', [App\Http\Controllers\RegistroController::class, 'imprimirBarcode'])->name('registros.imprimir-barcode');

Route::put('/ventas/{venta}/pagos-financiamiento', [VentaController::class, 'updatePagosFinanciamiento'])
    ->name('ventas.pagosFinanciamiento.update');


Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos/cards', [ProductoController::class, 'cardsVista'])->name('productos.cards');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
Route::get('/ventas/{venta}/etiqueta', [VentaController::class, 'etiqueta'])->name('ventas.etiqueta');

// routes/web.php
// routes/web.php
Route::resource('prestamos', PrestamoController::class); // incluye create/show/etc
Route::post('registros/lookup', [PrestamoController::class, 'lookupBySerie'])->name('registros.lookup');

// (Opcional) alias por si tienes enlaces viejos a 'prestamos.wizard'
Route::get('prestamos/wizard', fn () => redirect()->route('prestamos.create'))
     ->name('prestamos.wizard');
// routes/web.php
Route::get('prestamos/{id}/pdf', [PrestamoController::class, 'pdf'])->name('prestamos.pdf');

Route::middleware(['auth'])->group(function () {
// Compatibilidad con rutas viejas "direct"
Route::get('/promos/whatsapp/direct', [WhatsappPromotionController::class, 'directCreate'])
    ->name('promos.whatsapp.direct.create');

Route::post('/promos/whatsapp/direct', [WhatsappPromotionController::class, 'directSend'])
    ->name('promos.whatsapp.direct.send');

        // ->middleware('throttle:wa-promos'); // <- opcional si quieres limitar la tasa
});

// ✅ Envío por WhatsApp (Plantilla fija doc_pdf_utility_v1 – botón único en la vista)
Route::post('/propuestas/{propuesta}/whatsapp/plantilla', [PropuestaController::class, 'sendWhatsappTemplateRemision'])
    ->name('propuestas.whatsapp.plantilla');

// (Opcional) Endpoints usados para depurar / cargar plantillas
Route::get('/whatsapp/templates', [PropuestaController::class, 'whatsappTemplates'])
    ->name('whatsapp.templates');

Route::get('/whatsapp/debug', [PropuestaController::class, 'whatsappDebug'])
    ->name('whatsapp.debug');

Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf');
Route::post('/ventas/{venta}/whatsapp/plantilla', [VentaController::class, 'sendWhatsappTemplateRemision'])
    ->name('ventas.whatsapp.plantilla');


// ✅ Chat interno (requiere auth)
Route::middleware('auth')->group(function () {
    Route::get ('/whatsapp/inbox', [WhatsappInboxController::class, 'index'])->name('wa.inbox');
    Route::get('/whatsapp/inbox/fetch', [WhatsappInboxController::class, 'fetchThreads'])->name('wa.inbox.fetch');
    Route::get ('/whatsapp/chat/{msisdn}', [WhatsappInboxController::class, 'show'])->name('wa.chat');
    Route::post('/whatsapp/chat/{msisdn}', [WhatsappInboxController::class, 'send'])->name('wa.chat.send');
    Route::get ('/whatsapp/chat/{msisdn}/fetch', [WhatsappInboxController::class, 'fetch'])->name('wa.chat.fetch');
    Route::get('/whatsapp/media/{mediaId}', [WhatsappMediaController::class, 'show'])
        ->name('wa.media');
    Route::post('/whatsapp/claim/{msisdn}', [WhatsappWebhookController::class, 'claimByAgent'])
        ->name('wa.claim');
    Route::post('/whatsapp/close/{msisdn}', [WhatsappWebhookController::class, 'closeByAgent'])
        ->name('wa.close');
});
Route::middleware(['auth'])->group(function () { 
    Route::get('/transactions',        [CashTransactionController::class,'index'])->name('transactions.index');
    Route::get('/transactions/create', [CashTransactionController::class,'create'])->name('transactions.create');

    // Tabs (AJAX)
    Route::post('/transactions/allocation',            [CashTransactionController::class,'storeAllocation'])->name('transactions.allocation.store');
    Route::post('/transactions/disbursement/direct',   [CashTransactionController::class,'storeDisbursementDirect'])->name('transactions.disbursement.direct');
    Route::post('/transactions/disbursement/qr/start', [CashTransactionController::class,'startDisbursementWithQr'])->name('transactions.disbursement.qr.start');
    Route::post('/transactions/return',                [CashTransactionController::class,'storeReturn'])->name('transactions.return.store');

    // Dashboard AJAX
    Route::get('/transactions/data/chart',   [CashTransactionController::class,'apiChart'])->name('transactions.chart');
    Route::get('/transactions/data/metrics', [CashTransactionController::class,'apiMetrics'])->name('transactions.metrics');
    Route::get('/transactions/data/list',    [CashTransactionController::class,'apiTransactions'])->name('transactions.list');

    // Poll QR
    Route::get('/transactions/qr/status/{token}', [CashTransactionController::class,'qrStatus'])->name('transactions.qr.status');

    // >>> Recibo PDF (lo genera si no existe y lo muestra inline)
    Route::get('/transactions/{transaction}/receipt', [CashTransactionController::class,'receipt'])
        ->name('transactions.receipt');
});

// Rutas públicas para el celular del usuario (sin login)
Route::get('/qr/{token}',  [CashTransactionController::class, 'showQrForm'])->name('transactions.qr.show');
Route::post('/qr/{token}', [CashTransactionController::class, 'ackDisbursementWithQr'])->name('transactions.qr.ack');
Route::middleware(['auth']) // opcional
    ->prefix('promos')
    ->group(function () {
        Route::get('/promo-todo', [PromoController::class,'create'])->name('promos.promo_todo.form');
        Route::post('/promo-todo', [PromoController::class,'send'])->name('promos.promo_todo.send');
    });
        Route::resource('paquetes', PaqueteController::class);