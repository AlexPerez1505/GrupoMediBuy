<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\Models\PagoFinanciamiento;
use App\Models\Paquete; // Asegúrate de importar el modelo correcto
use App\Models\Pago;
use App\Models\CartaGarantia;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use App\Mail\PagoPendienteHoyMail;
use App\Mail\PagoPendienteHoyAdminMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;  // <-- Aquí IMPORTA Str correctamente
use App\Models\DocumentoPago;
use App\Models\Registro;
use Illuminate\Support\Facades\DB;


class VentaController extends Controller
{
// Método para devolver clientes con filtro (para fetch AJAX)
// Método alternativo para devolver clientes con filtro (para fetch AJAX)
public function buscarClientes(Request $request)
{
    $search = $request->input('search', '');

    $clientes = Cliente::query()
        ->where('nombre', 'LIKE', "%{$search}%")
        ->orWhere('apellido', 'LIKE', "%{$search}%")
        ->get(['id', 'nombre', 'apellido', 'telefono', 'email', 'comentarios']);

    return response()->json($clientes);
}

public function create()
{
    $productos = Producto::all();
    $paquetes = Paquete::all(); // Paquetes existentes
    $cartas = CartaGarantia::all(); // Cartas de garantía

    // Traer registros disponibles para selección de número de serie
    $registros = Registro::where('estado_actual', 'stock')->get();

    return view('venta.create', compact('productos', 'paquetes', 'cartas', 'registros'));
}
public function store(Request $request)
{
    $request->validate([
        'cliente_id'          => 'required|exists:clientes,id',
        'subtotal'            => 'required|numeric',
        'total'               => 'required|numeric',
        'productos_json'      => 'required|json',
        'pagos_json'          => 'nullable|json',
        'carta_garantia_id'   => 'nullable|exists:carta_garantias,id',
        'meses_garantia'     => 'required|integer|in:6,9,12,15,18',
    ]);

    \Log::info('Iniciando creación de venta...');
    \Log::info('Request completo:', $request->all());

    // 1️⃣ Crear la venta
    $venta = Venta::create([
        'cliente_id'            => $request->cliente_id,
        'lugar'                 => $request->lugar,
        'nota'                  => $request->nota,
        'user_id'               => auth()->id(),
        'subtotal'              => $request->subtotal,
        'descuento'             => $request->descuento,
        'envio'                 => $request->envio,
        'iva'                   => $request->iva,
        'total'                 => $request->total,
        'plan'                  => $request->plan,
        'detalle_financiamiento'=> null,
        'carta_garantia_id'     => $request->carta_garantia_id,
         'meses_garantia'     => $request->meses_garantia, 
    ]);
    \Log::info('Venta creada:', ['venta_id' => $venta->id]);

    // 2️⃣ Procesar productos
    $productos = json_decode($request->productos_json, true);
    \Log::info('Productos recibidos:', $productos);

    foreach ($productos as $p) {
        \Log::info('Procesando producto:', $p);

        $producto = \App\Models\Producto::find($p['producto_id']);
        if (! $producto) {
            \Log::warning('Producto no encontrado:', ['id' => $p['producto_id']]);
            continue;
        }

        // Si registro_id es array (uno por cada unidad)
        $series = $p['registro_id'] ?? null;
        if (is_array($series) && count($series) > 0) {
            // Creamos una fila por cada número de serie
            foreach ($series as $serieId) {
                \App\Models\VentaProducto::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => 1,
                    'precio_unitario' => $p['precio_unitario'],
                    'subtotal'        => $p['subtotal'] / count($series), // prorrateamos si lo deseas
                    'sobreprecio'     => $p['sobreprecio'] / count($series),
                    'registro_id'     => $serieId,
                ]);
                \Log::info("VentaProducto creado para serie {$serieId}.");

                // Actualizar registro y crear ProcesoEquipo
                $registro = \App\Models\Registro::find($serieId);
                if ($registro) {
                    $registro->estado_proceso = 'vendido';
                    $registro->save();
                    \Log::info("Registro {$serieId} marcado como vendido.");

                    try {
                        \App\Models\ProcesoEquipo::create([
                            'registro_id'          => $serieId,
                            'tipo_proceso'         => 'vendido',
                            'descripcion_proceso'  => "Equipo vendido (venta #{$venta->id})",
                            'ficha_tecnica_id'     => null,
                            'defectos'             => null,
                            'evidencia1'           => null,
                            'evidencia2'           => null,
                            'evidencia3'           => null,
                            'video'                => null,
                            'documento_pdf'        => null,
                        ]);
                        \Log::info("ProcesoEquipo 'vendido' creado para registro {$serieId}.");
                    } catch (\Exception $e) {
                        \Log::error("Error al crear ProcesoEquipo para registro {$serieId}: {$e->getMessage()}");
                    }
                } else {
                    \Log::warning("Registro no encontrado al actualizar estado (id: {$serieId}).");
                }
            }

        } else {
            // Caso sin números de serie (venta estándar)
            \App\Models\VentaProducto::create([
                'venta_id'        => $venta->id,
                'producto_id'     => $producto->id,
                'cantidad'        => $p['cantidad'],
                'precio_unitario' => $p['precio_unitario'],
                'subtotal'        => $p['subtotal'],
                'sobreprecio'     => $p['sobreprecio'],
                'registro_id'     => null,
            ]);
            \Log::info("VentaProducto creado sin registro_id (producto {$producto->id}).");
        }
    }

    // 3️⃣ Procesar pagos de financiamiento
    if ($request->filled('pagos_json')) {
        $pagos = json_decode($request->input('pagos_json'), true);
        \Log::info('Pagos recibidos:', $pagos);

        foreach ($pagos as $pago) {
            \App\Models\PagoFinanciamiento::create([
                'venta_id'    => $venta->id,
                'descripcion' => $pago['descripcion'] ?? '',
                'fecha_pago'  => \Carbon\Carbon::parse($pago['mes']),
                'monto'       => $pago['cuota'] ?? 0,
            ]);
        }
    }

    return redirect()
        ->route('ventas.show', $venta->id)
        ->with('success', 'Venta guardada exitosamente.');
}


public function index()
{
    $ventas = Venta::with(['cliente', 'usuario'])
                   ->orderBy('created_at', 'desc') // los más recientes primero
                   ->get();

    return view('venta.index', compact('ventas'));
}
public function pdf(Venta $venta)
{
    $venta->load(['cliente', 'usuario', 'productos.producto', 'pagos', 'cartaGarantia']);
    $pagos = $venta->pagos;
    $totalPagado = $pagos->sum('monto');

    $url = route('ventas.show', $venta->id);

    $qr = base64_encode(
        QrCode::format('svg')
            ->size(120)
            ->generate($url)
    );

    $plan = $venta->plan;

    $pdfVenta = PDF::loadView('venta.pdf', compact('venta', 'qr', 'pagos', 'totalPagado', 'plan'))
                   ->setPaper('a4', 'portrait');

    $rutaVenta = storage_path("app/public/temp/venta_{$venta->id}.pdf");
    file_put_contents($rutaVenta, $pdfVenta->output());

    $rutaCarta = $venta->cartaGarantia?->archivo
        ? storage_path("app/public/" . $venta->cartaGarantia->archivo)
        : null;

    if (!$rutaCarta) {
        dd("No existe ruta para carta de garantía");
    }
    if (!file_exists($rutaVenta)) {
        dd("Archivo PDF venta NO existe en: " . $rutaVenta);
    }
    if (!file_exists($rutaCarta)) {
        dd("Archivo PDF carta garantía NO existe en: " . $rutaCarta);
    }

    $pdf = new Fpdi();
    $archivos = [$rutaVenta, $rutaCarta];

    foreach ($archivos as $archivo) {
        $pageCount = $pdf->setSourceFile($archivo);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tpl = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);
        }
    }

    $rutaFinal = storage_path("app/public/temp/final_venta_{$venta->id}.pdf");
    $pdf->Output($rutaFinal, 'F');

    if (!file_exists($rutaFinal)) {
        dd("El archivo PDF final NO se generó");
    }

    // Limpiar el nombre del cliente para que sea válido en el nombre del archivo
    $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', $venta->cliente->nombre);

    // Usar "Remisión" en lugar de "venta"
    $nombreArchivo = "Remisión_{$venta->id}_{$clienteNombre}.pdf";

    return response()->download($rutaFinal, $nombreArchivo);
}


public function update(Request $request, $id)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'lugar' => 'required|string|max:255',
        'nota' => 'nullable|string',
        'subtotal' => 'required|numeric',
        'descuento' => 'nullable|numeric',
        'envio' => 'nullable|numeric',
        'iva' => 'nullable|numeric',
        'total' => 'required|numeric',
        'plan' => 'nullable|string|max:255',
        'productos_json' => 'required',
    ]);

    $venta = Venta::findOrFail($id);

    $venta->update([
        'cliente_id' => $request->cliente_id,
        'lugar' => $request->lugar,
        'nota' => $request->nota,
        'subtotal' => $request->subtotal,
        'descuento' => $request->descuento,
        'envio' => $request->envio,
        'iva' => $request->iva,
        'total' => $request->total,
        'plan' => $request->plan,
    ]);

    // Eliminar productos anteriores
    VentaProducto::where('venta_id', $venta->id)->delete();

    $productos = json_decode($request->productos_json, true);

    foreach ($productos as $p) {
        if (isset($p['producto_id']) && !empty($p['producto_id'])) {
            $producto = Producto::find($p['producto_id']);
            if (!$producto) {
                return redirect()->back()->withErrors('Producto no encontrado: ' . $p['producto_id']);
            }

            VentaProducto::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $p['cantidad'],
                'precio_unitario' => $p['precio_unitario'],
                'sobreprecio' => $p['sobreprecio'],
                'subtotal' => $p['subtotal'],
            ]);
        } else {
            return redirect()->back()->withErrors('Falta producto_id en uno de los productos.');
        }
    }

    // Pagos
    if ($request->has('pagos_financiamiento')) {
        foreach ($request->pagos_financiamiento as $pagoId => $datos) {
            // Si es nuevo
            if (Str::startsWith($pagoId, 'nuevo_')) {
                if (!empty($datos['eliminar'])) continue;

                $pago = PagoFinanciamiento::create([
                    'venta_id' => $venta->id,
                    'fecha_pago' => $datos['fecha_pago'],
                    'monto' => $datos['monto'],
                    'descripcion' => $datos['descripcion'] ?? 'Pago planeado',
                ]);

                if ($request->hasFile("pagos_financiamiento.$pagoId.documento")) {
                    $archivo = $request->file("pagos_financiamiento.$pagoId.documento");

                    if ($archivo->isValid() && $archivo->extension() === 'pdf') {
                        $ruta = $archivo->store('public/documentos_pagos');

                        DocumentoPago::create([
                            'pago_id' => $pago->id,
                            'ruta_archivo' => $ruta,
                            'nombre_original' => $archivo->getClientOriginalName(),
                        ]);
                    }
                }

            } else {
                // Existente
                $pago = PagoFinanciamiento::find($pagoId);
                if (!$pago) continue;

                if (!empty($datos['eliminar'])) {
                    // Eliminar documento si existe
                    $documento = DocumentoPago::where('pago_id', $pago->id)->first();
                    if ($documento) {
                        Storage::delete($documento->ruta_archivo);
                        $documento->delete();
                    }

                    $pago->delete();
                    continue;
                }

                $pago->update([
                    'fecha_pago' => $datos['fecha_pago'],
                    'monto' => $datos['monto'],
                    'descripcion' => $datos['descripcion'] ?? $pago->descripcion,
                ]);

                // Reemplazar documento si hay uno nuevo
                if ($request->hasFile("pagos_financiamiento.$pagoId.documento")) {
                    $archivo = $request->file("pagos_financiamiento.$pagoId.documento");

                    if ($archivo->isValid() && $archivo->extension() === 'pdf') {
                        $documento = DocumentoPago::where('pago_id', $pago->id)->first();

                        if ($documento) {
                            Storage::delete($documento->ruta_archivo);
                            $documento->delete();
                        }

                        $ruta = $archivo->store('public/documentos_pagos');

                        DocumentoPago::create([
                            'pago_id' => $pago->id,
                            'ruta_archivo' => $ruta,
                            'nombre_original' => $archivo->getClientOriginalName(),
                        ]);
                    }
                }
            }
        }
    }

    return redirect()->route('ventas.show', $venta->id)->with('success', 'Venta actualizada correctamente.');
}




public function edit($id)
{
    $venta = Venta::with(['productos.producto', 'cliente', 'pagoFinanciamiento'])->findOrFail($id);
    $clientes = Cliente::all();
    $productos = Producto::all();

    return view('venta.edit', compact('venta', 'clientes', 'productos'));
}

    public function search(Request $request)
{
    $query = $request->input('search');

    $paquetes = Paquete::with('productos')
        ->when($query, function ($q) use ($query) {
            $q->where('nombre', 'like', '%' . $query . '%');
        })
        ->get();

    return response()->json($paquetes);
}
   // Métodos para pagos

public function storePago(Request $request, $ventaId)
{
    $request->validate([
        'monto' => 'required|numeric|min:0.01',
        'fecha_pago' => 'required|date',
        'metodo_pago' => 'required|string|max:255',
    ]);

    $venta = Venta::findOrFail($ventaId);

    // Buscar financiamiento relacionado
    $financiamiento = \App\Models\PagoFinanciamiento::where('venta_id', $ventaId)
        ->where('monto', $request->monto)
        ->where('fecha_pago', $request->fecha_pago)
        ->first();

    // Crear el pago
    $pago = new \App\Models\Pago();
    $pago->venta_id = $venta->id;
    $pago->monto = $request->monto;
    $pago->fecha_pago = $request->fecha_pago;
    $pago->metodo_pago = $request->metodo_pago;
    $pago->aprobado = false; // <-- PAGO PENDIENTE

    if ($financiamiento) {
        $pago->financiamiento_id = $financiamiento->id;
        // NO marcar como pagado aún
    }

    $pago->save();

    return redirect()->route('ventas.pagos.index', $venta->id)
        ->with('success', 'Pago registrado exitosamente. Está pendiente de aprobación.');
}

public function show(Venta $venta)
{
    $venta->load([
        'cliente',
        'productos.producto',
        'usuario',
        'cartaGarantia',
        'remision',
    ]);

    // Traer TODOS los pagos planeados, y si existe, el pago real asociado
$pagos = \App\Models\PagoFinanciamiento::with(['pago', 'documentos'])
    ->where('venta_id', $venta->id)
    ->orderBy('fecha_pago')
    ->get();



    return view('venta.show', compact('venta', 'pagos'));
}



public function indexPagos($ventaId)
{
    $venta = Venta::with('cliente')->findOrFail($ventaId);

    // Obtener todos los pagos registrados para esa venta
    $pagos = \App\Models\Pago::where('venta_id', $ventaId)
        ->orderBy('fecha_pago')
        ->get();

    // Obtener los financiamientos relacionados a esa venta
    $financiamientos = \App\Models\PagoFinanciamiento::where('venta_id', $ventaId)->get();

    return view('venta.pagos', compact('venta', 'pagos', 'financiamientos'));
}

public function reciboPago($pagoId)
{
    $pago = Pago::with('venta.cliente')->findOrFail($pagoId);

    $pdf = PDF::loadView('venta.recibo', compact('pago'))
              ->setPaper('a6', 'portrait');

    return $pdf->stream("recibo_pago_{$pago->id}.pdf");
}
public function reciboFinal(Venta $venta)
{
    // Verificar si todos los pagos están liquidados
    $pendientes = PagoFinanciamiento::where('venta_id', $venta->id)
        ->where('pagado', false)
        ->count();

    if ($pendientes > 0) {
        return redirect()->back()->with('error', 'Aún hay pagos pendientes.');
    }

    // Cargar cliente, productos, total pagado, etc.
    $venta->load(['cliente', 'productos.producto']);

    $totalPagado = Pago::where('venta_id', $venta->id)->sum('monto');

    $pdf = PDF::loadView('venta.recibo_final', [
        'venta' => $venta,
        'totalPagado' => $totalPagado,
    ])->setPaper('a5', 'portrait');

    return $pdf->stream("recibo_final_venta_{$venta->id}.pdf");
}

public function marcarPagado(Request $request, $pagoFinanciamientoId)
{
    $pinIngresado = $request->input('pin');
    $pinCorrecto = env('APROBACION_PIN');

    if ($pinIngresado !== $pinCorrecto) {
        return redirect()->back()->with('error', 'PIN inválido. Intenta nuevamente.');
    }

    $financiamiento = \App\Models\PagoFinanciamiento::findOrFail($pagoFinanciamientoId);
    $financiamiento->pagado = true;
    $financiamiento->save();

    // Actualizar pago asociado
    $pago = \App\Models\Pago::where('financiamiento_id', $financiamiento->id)->first();
    if ($pago) {
        $pago->aprobado = true;
        $pago->save();
    }

    return redirect()->back()->with('success', 'Pago aprobado correctamente.');
}


public function deudores()
{
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
    Carbon::setLocale('es');

    function parsearFechaEnEsp($fechaTexto) {
        $fmt = new \IntlDateFormatter('es_ES', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
        $fmt->setPattern("d 'de' MMMM 'de' y");
        $timestamp = $fmt->parse($fechaTexto);
        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }

    $ventas = \App\Models\Venta::with(['cliente', 'pagos'])->get();

    foreach ($ventas as $v) {
        if (!empty($v->detalle_financiamiento['fecha']) && is_string($v->detalle_financiamiento['fecha'])) {
            try {
                $fechaTexto = $v->detalle_financiamiento['fecha'];
                $fechaCarbon = parsearFechaEnEsp($fechaTexto);
                $v->detalle_financiamiento['fecha_carbon'] = $fechaCarbon;
            } catch (\Exception $e) {
                logger()->error("Error al parsear fecha: $fechaTexto", ['error' => $e->getMessage()]);
                $v->detalle_financiamiento['fecha_carbon'] = null;
            }
        }

        // Buscar pagos programados para HOY que no han sido pagados
        $pagosHoy = \App\Models\PagoFinanciamiento::where('venta_id', $v->id)
            ->whereDate('fecha_pago', Carbon::today())
            ->where('pagado', false)
            ->get();

        foreach ($pagosHoy as $pago) {
            if (!$pago->notificado) {
                try {
                    // Correo al cliente
                    if (!empty($v->cliente->email)) {
                        Mail::to($v->cliente->email)->send(new PagoPendienteHoyMail($v));
                        Log::info("Correo enviado al cliente: {$v->cliente->email}");
                    }

                    // Correos a administradores
                    $adminEmails = User::where('role', 'admin')
                        ->whereNotNull('email')
                        ->pluck('email')
                        ->filter();

                    Log::debug('Correos de administradores: ' . implode(', ', $adminEmails->toArray()));

                    foreach ($adminEmails as $adminEmail) {
                        Mail::to($adminEmail)->send(new PagoPendienteHoyAdminMail($v));
                        Log::info("Correo enviado al administrador: {$adminEmail}");
                    }

                    // Marcar como notificado
                    $pago->notificado = true;
                    $pago->save();
                } catch (\Exception $e) {
                    logger()->error("Error al enviar notificaciones: " . $e->getMessage());
                }
            }
        }
    }

    $clientes = \App\Models\Cliente::orderBy('nombre')->get();
    return view('venta.deudores', compact('ventas', 'clientes'));
}
public function pendientes()
{
    $ventas = DB::table('ventas')
        ->leftJoin('checklists', function ($join) {
            $join->on('ventas.id', '=', 'checklists.venta_id')
                ->where('checklists.finalizado', 1);
        })
        ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id') // ← AÑADIDO
        ->select(
            'ventas.id',
            'clientes.nombre as cliente', // ← corregido: obtenemos el nombre del cliente
            'ventas.created_at as fecha'
        )
        ->whereNull('checklists.id')
        ->groupBy('ventas.id', 'clientes.nombre', 'ventas.created_at') // ← todos los campos del select
        ->orderBy('ventas.created_at', 'desc')
        ->get();

    return view('venta.pendientes', compact('ventas'));
}

public function productos($ventaId)
{
    $productos = DB::table('venta_productos')
        ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
        ->where('venta_productos.venta_id', $ventaId)
        ->select(
            'venta_productos.*',
            DB::raw("CONCAT(productos.tipo_equipo, ' ', productos.marca, ' ', productos.modelo) AS nombre_producto")
        )
        ->get();

    $venta = DB::table('ventas')->where('id', $ventaId)->first();

    return view('venta.productos', compact('venta', 'productos'));
}
public function updatePagosFinanciamiento(Request $request, Venta $venta)
{
    if ($request->has('pagos_financiamiento')) {
        foreach ($request->pagos_financiamiento as $pagoId => $datos) {
            // Nuevo pago
            if (Str::startsWith($pagoId, 'nuevo_')) {
                if (!empty($datos['eliminar'])) continue;

                PagoFinanciamiento::create([
                    'venta_id' => $venta->id,
                    'fecha_pago' => $datos['fecha_pago'],
                    'monto' => $datos['monto'],
                    'descripcion' => $datos['descripcion'] ?? 'Pago planeado',
                ]);
            } else {
                // Existente
                $pago = PagoFinanciamiento::find($pagoId);
                if (!$pago) continue;

                if (!empty($datos['eliminar'])) {
                    $pago->delete();
                    continue;
                }

                $pago->update([
                    'fecha_pago' => $datos['fecha_pago'],
                    'monto' => $datos['monto'],
                    'descripcion' => $datos['descripcion'] ?? $pago->descripcion,
                ]);
            }
        }
    }

    return redirect()->back()->with('success', 'Pagos actualizados correctamente.');
}

}




