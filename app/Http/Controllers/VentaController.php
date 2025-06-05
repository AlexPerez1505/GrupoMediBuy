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

class VentaController extends Controller
{
    // Método para devolver clientes con filtro (para fetch AJAX)
    public function clientes(Request $request)
    {
        $search = $request->input('search', '');

        $clientes = Cliente::query()
            ->where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('apellido', 'LIKE', "%{$search}%")
            ->get(['id', 'nombre', 'apellido', 'telefono', 'email', 'comentarios']); // 👈 asegúrate de incluir el id

        return response()->json($clientes);
    }

    public function create()
    {
        // Ya no necesitas cargar todos los clientes aquí, si usas búsqueda AJAX
        $productos = Producto::all();
         $paquetes = Paquete::all(); // Agrega esta línea para obtener los paquetes
         $cartas = CartaGarantia::all();
        return view('venta.create', compact('productos', 'paquetes', 'cartas'));
    }

public function store(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'subtotal' => 'required|numeric',
        'total' => 'required|numeric',
        'productos_json' => 'required|json',
        'pagos_json' => 'nullable|json',
        'carta_garantia_id' => 'nullable|exists:carta_garantias,id',
    ]);

    $venta = Venta::create([
        'cliente_id' => $request->cliente_id,
        'lugar' => $request->lugar,
        'nota' => $request->nota,
        'user_id' => auth()->id(),
        'subtotal' => $request->subtotal,
        'descuento' => $request->descuento,
        'envio' => $request->envio,
        'iva' => $request->iva,
        'total' => $request->total,
        'plan' => $request->plan,
        'detalle_financiamiento' => null, // ya no se usa
        'carta_garantia_id' => $request->carta_garantia_id,
    ]);

    $productos = json_decode($request->productos_json, true);
    foreach ($productos as $p) {
        $producto = Producto::where('tipo_equipo', $p['equipo'])->first();

        VentaProducto::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto ? $producto->id : null,
            'cantidad' => $p['cantidad'],
            'precio_unitario' => $p['precio_unitario'],
            'subtotal' => $p['subtotal'],
            'sobreprecio' => $p['sobreprecio'],
        ]);
    }
if ($request->filled('pagos_json')) {
    $pagos = json_decode($request->input('pagos_json'), true);

    if (is_array($pagos)) {
        foreach ($pagos as $pago) {
            PagoFinanciamiento::create([
                'venta_id' => $venta->id,
                'descripcion' => $pago['descripcion'] ?? '',
                'fecha_pago' => \Carbon\Carbon::parse($pago['mes']),
                'monto' => $pago['cuota'] ?? 0,
            ]);
        }
    }
}

\Log::info('Pagos recibidos:', ['pagos_json' => $request->pagos_json]);

    return redirect()->route('ventas.show', $venta->id)->with('success', 'Venta guardada exitosamente.');
}

public function show(Venta $venta)
{
    // Carga relaciones necesarias, incluyendo pagosFinanciamiento
    $venta->load(['productos.producto', 'cliente', 'usuario', 'pagosFinanciamiento']);

    return view('venta.show', compact('venta'));
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

    // Obtener el plan de la venta
    $plan = $venta->plan; // 👈 Se obtiene el plan aquí

    // Generar el PDF de la venta
    $pdfVenta = PDF::loadView('venta.pdf', compact('venta', 'qr', 'pagos', 'totalPagado', 'plan')) // 👈 Lo pasamos a la vista
                   ->setPaper('a4', 'portrait');

    // Guardar temporalmente el PDF de la venta
    $rutaVenta = storage_path("app/public/temp/venta_{$venta->id}.pdf");
    file_put_contents($rutaVenta, $pdfVenta->output());

    // Obtener ruta carta garantía
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

    // Fusionar los PDFs
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

    return response()->download($rutaFinal, "venta_{$venta->id}.pdf");

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

        VentaProducto::where('venta_id', $venta->id)->delete();

        $productos = json_decode($request->productos_json, true);

        foreach ($productos as $p) {
            if (isset($p['producto_id']) && !empty($p['producto_id'])) {
                $producto = Producto::find($p['producto_id']);
                if (!$producto) {
                    return redirect()->back()->withErrors('Producto no encontrado para el ID: ' . $p['producto_id']);
                }
                VentaProducto::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $p['cantidad'],
                    'precio_unitario' => $p['precio_unitario'],
                    'subtotal' => $p['subtotal'],
                    'sobreprecio' => $p['sobreprecio'],
                ]);
            } else {
                return redirect()->back()->withErrors('Falta el producto_id para uno de los productos.');
            }
        }

        return redirect()->route('ventas.show', $venta->id)->with('success', 'Venta actualizada exitosamente.');
    }


    public function edit($id)
    {
        $venta = Venta::with('productos', 'cliente')->findOrFail($id);
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




public function reciboPago($pagoId)
{
    $pago = Pago::with('venta.cliente')->findOrFail($pagoId);

    $pdf = PDF::loadView('venta.recibo', compact('pago'))
              ->setPaper('a6', 'portrait');

    return $pdf->stream("recibo_pago_{$pago->id}.pdf");
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

    $ventas = \App\Models\Venta::with(['cliente', 'pagos'])
        ->where('plan', '!=', 'contado')
        ->whereRaw('
    (SELECT COALESCE(SUM(monto), 0) 
     FROM pagos_financiamiento 
     WHERE pagos_financiamiento.venta_id = ventas.id 
       AND pagos_financiamiento.pagado = 1
    ) < ventas.total
')

        ->get();

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
    }

    $clientes = \App\Models\Cliente::orderBy('nombre')->get();

    return view('venta.deudores', compact('ventas', 'clientes'));
}






}
