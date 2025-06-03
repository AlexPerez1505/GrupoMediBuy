<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\Models\Paquete; // Asegúrate de importar el modelo correcto
use App\Models\Pago;
use App\Models\CartaGarantia;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;


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
        'detalle_financiamiento' => 'nullable|string',
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
        'detalle_financiamiento' => $request->detalle_financiamiento,
        'carta_garantia_id' => $request->carta_garantia_id, // ← GUARDADO
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

    return redirect()->route('ventas.show', $venta->id)->with('success', 'Venta guardada exitosamente.');
}


    public function show(Venta $venta)
    {
        $venta->load(['productos.producto', 'cliente', 'usuario', 'pagos']);
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

    return response()->file($rutaFinal);
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

    // Métodos para pagos

    public function storePago(Request $request, $ventaId)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string|max:255',
        ]);

        $venta = Venta::findOrFail($ventaId);

        $pago = Pago::create([
            'venta_id' => $venta->id,
            'monto' => $request->monto,
            'fecha_pago' => $request->fecha_pago,
            'metodo_pago' => $request->metodo_pago,
        ]);

        return redirect()->route('ventas.show', $venta->id)->with('success', 'Pago registrado exitosamente.');
    }

    public function indexPagos($ventaId)
    {
        $venta = Venta::with('pagos')->findOrFail($ventaId);
        $pagos = $venta->pagos;

        // Por ejemplo, podrías retornar una vista con los pagos
        return view('venta.pagos', compact('venta', 'pagos'));
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
public function seguimiento(Venta $venta)
{
    return view('pagos.index', [
        'venta' => $venta,
    ]);
}
}
