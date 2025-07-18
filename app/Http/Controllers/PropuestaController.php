<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Propuesta;
use App\Models\PropuestaProducto;
use App\Models\PagoFinanciamientoPropuesta;
use App\Models\CartaGarantia;
use App\Models\FichaTecnica;

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
    $fichas = FichaTecnica::all();

    return view('propuesta.create', compact('productos', 'fichas'));
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
        'ficha_tecnica_id' => 'nullable|exists:fichas_tecnicas,id',
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
            'ficha_tecnica_id' => $request->ficha_tecnica_id,
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
    $propuesta->load(['cliente', 'usuario', 'productos.producto', 'fichaTecnica']);

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

    $rutaFicha = $propuesta->fichaTecnica?->archivo
        ? storage_path("app/public/" . $propuesta->fichaTecnica->archivo)
        : null;

    $pdf = new Fpdi();
    $archivos = [$rutaPropuesta];

    if ($rutaFicha && file_exists($rutaFicha)) {
        $archivos[] = $rutaFicha;
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
    $nombreArchivo = "Cotización_{$propuesta->id}_{$clienteNombre}.pdf";

    return response()->download($rutaFinal, $nombreArchivo);
}
public function show(Propuesta $propuesta)
{
    $propuesta->load(['cliente', 'productos.producto', 'usuario', 'fichaTecnica']);

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
public function edit($id)
{
    $propuesta = Propuesta::with(['productos.producto', 'cliente', 'pagosFinanciamiento', 'fichaTecnica'])->findOrFail($id);
    $productos = Producto::all();
    $fichas = FichaTecnica::all();
    $clientes = Cliente::all(); // <--- Aquí lo agregas

    return view('propuesta.edit', compact('propuesta', 'productos', 'fichas', 'clientes'));
}

public function update(Request $request, $id)
{
    \Log::info('Inicio método update - request recibido', $request->all());
\Log::info('Valor de productos_json', ['productos_json' => $request->productos_json]);

    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'subtotal' => 'required|numeric',
        'total' => 'required|numeric',
        'productos_json' => 'required|json',
        'pagos_json' => 'nullable|json',
        'ficha_tecnica_id' => 'nullable|exists:fichas_tecnicas,id',
        'lugar' => 'required|string',
    ]);

    try {
        $propuesta = Propuesta::findOrFail($id);

        $propuesta->update([
            'cliente_id' => $request->cliente_id,
            'lugar' => $request->lugar,
            'nota' => $request->nota,
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento ?? 0,
            'envio' => $request->envio ?? 0,
            'iva' => $request->iva ?? 0,
            'total' => $request->total,
            'plan' => $request->plan,
            'ficha_tecnica_id' => $request->ficha_tecnica_id,
        ]);

        \Log::info('Propuesta actualizada con ID: ' . $propuesta->id);

        // Actualizar productos
        $propuesta->productos()->delete();
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
            \Log::info('Producto asociado', $p);
        }

        // Actualizar pagos
        $idsConservados = [];

        if ($request->filled('pagos_json')) {
            $pagos = json_decode($request->pagos_json, true);
            \Log::info('Pagos decodificados', ['pagos' => $pagos]);

            if (is_array($pagos)) {
                foreach ($pagos as $pago) {
                    \Log::info('Procesando pago', $pago);

                    if (isset($pago['id']) && $pago['id']) {
                        $pagoExistente = PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                            ->where('id', $pago['id'])
                            ->first();

                        if ($pagoExistente) {
                            $pagoExistente->update([
                                'descripcion' => $pago['descripcion'] ?? '',
                                'fecha_pago' => \Carbon\Carbon::parse($pago['mes']),
                                'monto' => $pago['cuota'] ?? 0,
                            ]);
                            $idsConservados[] = $pagoExistente->id;
                            \Log::info('Pago existente actualizado', ['id' => $pagoExistente->id]);
                        }
                    } else {
                        $nuevoPago = PagoFinanciamientoPropuesta::create([
                            'propuesta_id' => $propuesta->id,
                            'descripcion' => $pago['descripcion'] ?? '',
                            'fecha_pago' => \Carbon\Carbon::parse($pago['mes']),
                            'monto' => $pago['cuota'] ?? 0,
                        ]);
                        $idsConservados[] = $nuevoPago->id;
                        \Log::info('Nuevo pago creado', ['id' => $nuevoPago->id]);
                    }
                }
            } else {
                \Log::warning('El campo pagos_json no es un array válido', ['pagos_json' => $request->pagos_json]);
            }
        } else {
            \Log::warning('No se recibió pagos_json o está vacío');
        }

        // Eliminar pagos que no fueron conservados
        PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
            ->whereNotIn('id', $idsConservados)
            ->delete();

        \Log::info('Pagos actualizados correctamente');

        return redirect()->route('propuestas.show', $propuesta->id)
            ->with('success', 'Propuesta actualizada exitosamente.');
            
    } catch (\Exception $e) {
        \Log::error('Error actualizando propuesta: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all(),
        ]);
        return back()->withErrors('Ocurrió un error al actualizar la propuesta.');
    }
}


}
