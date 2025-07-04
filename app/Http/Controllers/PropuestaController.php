<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Propuesta;
use App\Models\PropuestaProducto;
use App\Models\PagoFinanciamientoPropuesta;
use App\Models\CartaGarantia;
use PDF;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;

class PropuestaController extends Controller
{
public function encontrarClientes(Request $request)
{
    $search = $request->input('search', '');

    $clientes = Cliente::query();

    if ($search) {
        $clientes->where(function($query) use ($search) {
            $query->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido', 'LIKE', "%{$search}%");
        });
    }

    return response()->json($clientes->get(['id', 'nombre', 'apellido', 'telefono', 'email', 'comentarios']));
}
    public function create()
    {
        $productos = Producto::all();
        $cartas = CartaGarantia::all();

        return view('propuesta.create', compact('productos', 'cartas'));
    }
public function store(Request $request)
{
    \Log::info('Inicio método store - request recibido', $request->all());

    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'subtotal' => 'required|numeric',
        'total' => 'required|numeric',
        'productos_json' => 'required|json',
        'pagos_json' => 'nullable|json',
        'carta_garantia_id' => 'nullable|exists:carta_garantias,id',
        'lugar' => 'required|string',
    ]);

    try {
        $propuesta = Propuesta::create([
            'cliente_id' => $request->cliente_id,
            'lugar' => $request->lugar,
            'nota' => $request->nota,
            'user_id' => auth()->id(),
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento ?? 0,
            'envio' => $request->envio ?? 0,
            'iva' => $request->iva ?? 0,
            'total' => $request->total,
            'plan' => $request->plan,
            'carta_garantia_id' => $request->carta_garantia_id,
        ]);
        \Log::info('Propuesta creada con ID: ' . $propuesta->id);

        $productos = json_decode($request->productos_json, true);
        \Log::info('Productos decodificados', ['productos' => $productos]);

        foreach ($productos as $p) {
            $propuesta->productos()->create([
                'producto_id' => $p['producto_id'],
                'cantidad' => $p['cantidad'],
                'precio_unitario' => $p['precio_unitario'],
                'subtotal' => $p['subtotal'],
                'sobreprecio' => $p['sobreprecio'] ?? 0,
            ]);
            \Log::info('Producto asociado a propuesta', ['producto_id' => $p['producto_id']]);
        }

        if ($request->filled('pagos_json')) {
            $pagos = json_decode($request->pagos_json, true);
            \Log::info('Pagos decodificados', ['pagos' => $pagos]);

            if (is_array($pagos)) {
                foreach ($pagos as $pago) {
                    PagoFinanciamientoPropuesta::create([
                        'propuesta_id' => $propuesta->id,
                        'descripcion' => $pago['descripcion'] ?? '',
                        'fecha_pago' => \Carbon\Carbon::parse($pago['mes']),
                        'monto' => $pago['cuota'] ?? 0,
                    ]);
                    \Log::info('Pago de financiamiento asociado', ['descripcion' => $pago['descripcion'] ?? '']);
                }
            }
        }

        \Log::info('Propuesta guardada exitosamente', ['propuesta_id' => $propuesta->id]);

        return redirect()->route('propuestas.show', $propuesta->id)->with('success', 'Propuesta guardada exitosamente.');
    } catch (\Exception $e) {
        \Log::error('Error guardando propuesta: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all(),
        ]);
        return back()->withErrors('Ocurrió un error al guardar la propuesta.');
    }
}


    public function index()
    {
        $propuestas = Propuesta::with(['cliente', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('propuesta.index', compact('propuestas'));
    }

    public function pdf(Propuesta $propuesta)
    {
        $propuesta->load(['cliente', 'usuario', 'productos.producto', 'cartaGarantia']);

        $url = route('propuestas.show', $propuesta->id);

        $qr = base64_encode(
            \QrCode::format('svg')
                ->size(120)
                ->generate($url)
        );

        $pdfPropuesta = PDF::loadView('propuesta.pdf', compact('propuesta', 'qr'))
            ->setPaper('a4', 'portrait');

        $rutaPropuesta = storage_path("app/public/temp/propuesta_{$propuesta->id}.pdf");
        file_put_contents($rutaPropuesta, $pdfPropuesta->output());

        $rutaCarta = $propuesta->cartaGarantia?->archivo
            ? storage_path("app/public/" . $propuesta->cartaGarantia->archivo)
            : null;

        $pdf = new Fpdi();
        $archivos = [$rutaPropuesta];

        if ($rutaCarta && file_exists($rutaCarta)) {
            $archivos[] = $rutaCarta;
        }

        foreach ($archivos as $archivo) {
            $pageCount = $pdf->setSourceFile($archivo);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        }

        $rutaFinal = storage_path("app/public/temp/final_propuesta_{$propuesta->id}.pdf");
        $pdf->Output($rutaFinal, 'F');

        $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', $propuesta->cliente->nombre);
        $nombreArchivo = "Propuesta_{$propuesta->id}_{$clienteNombre}.pdf";

        return response()->download($rutaFinal, $nombreArchivo);
    }
public function show(Propuesta $propuesta)
{
    $propuesta->load(['cliente', 'productos.producto', 'usuario', 'cartaGarantia']);

    // Gráfico de tipo de equipo
    $agrupados = $propuesta->productos->groupBy(function ($item) {
        return $item->producto->tipo_equipo;
    });

    $tiposEquipo = $agrupados->keys()->values()->all();
    $cantidades = $agrupados->map->count()->values()->all();

    // Gráfico de productos por subtotal (más caro a más barato)
    $productosOrdenados = $propuesta->productos
        ->sortByDesc(function ($item) {
            return $item->subtotal ?? 0;
        })
        ->map(function ($item) {
            $producto = $item->producto;
            return [
                'nombre' => $producto ? $producto->tipo_equipo . ' ' . $producto->modelo : 'Producto eliminado',
                'subtotal' => $item->subtotal ?? 0,
            ];
        });

    $labels = $productosOrdenados->pluck('nombre');
    $valores = $productosOrdenados->pluck('subtotal');

    return view('propuesta.show', compact('propuesta', 'tiposEquipo', 'cantidades', 'labels', 'valores'));
}

}
