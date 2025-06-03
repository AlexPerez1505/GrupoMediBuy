<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Remision;
use App\Models\Cliente;
use App\Models\ItemRemision;
use NumberFormatter;
use PDF;


class RemisionController extends Controller
{
    public function index()
    {
        $remisiones = Remision::with('cliente', 'user')->latest()->get();
        return view('remisions.index', compact('remisiones'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('remisions.create', compact('clientes'));
    }
    public function store(Request $request)
    {
        // Forzar el valor booleano del checkbox de IVA
        $request->merge([
            'aplicar_iva' => $request->input('aplicar_iva') == 1 ? 1 : 0
        ]);
        
        
    
        // Validación de la solicitud
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'items' => 'required|array|min:1',
            'items.*.unidad' => 'required|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.nombre_item' => 'required|string',
            'items.*.descripcion_item' => 'nullable|string',
            'items.*.importe_unitario' => 'required|numeric|min:0',
            'items.*.a_cuenta' => 'nullable|numeric|min:0',
            'aplicar_iva' => 'required|boolean',
        ]);
    
        // Calcular el subtotal
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['cantidad'] * $item['importe_unitario'];
        }
    
        // Calcular el IVA si está marcado
        $iva = $request->aplicar_iva ? round($subtotal * 0.16, 2) : 0;
    
        // Calcular el total
        $total = $subtotal + $iva;
    
         // Convertir el total a letras correctamente
    $parteEntera = floor($total);
    $centavos = round(($total - $parteEntera) * 100);

    $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
    $letra = strtoupper($formatter->format($parteEntera)) . ' PESOS';
    $letra .= ' CON ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100 M.N.';
    
        // Crear la remisión
        try {
            $remision = Remision::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => \Auth::id(),
                'subtotal' => $subtotal,
                'total' => $total,
                'importe_letra' => $letra,
                'iva' => $iva,
                'aplicar_iva' => $request->aplicar_iva,
            ]);
    
            // Crear los ítems de la remisión
            foreach ($request->items as $item) {
                $cantidad = $item['cantidad'];
                $importe_unitario = $item['importe_unitario'];
                $aCuenta = $item['a_cuenta'] ?? 0;
                $itemSubtotal = $cantidad * $importe_unitario;
                $restante = $itemSubtotal - $aCuenta;
    
                $remision->items()->create([
                    'unidad' => $item['unidad'],
                    'cantidad' => $cantidad,
                    'nombre_item' => $item['nombre_item'],
                    'descripcion_item' => $item['descripcion_item'] ?? null,
                    'importe_unitario' => $importe_unitario,
                    'a_cuenta' => $aCuenta,
                    'subtotal' => $itemSubtotal,
                    'restante' => $restante,
                ]);
            }
    
            return redirect()->route('remisions.show', $remision->id)
                             ->with('success', 'Remisión creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('remisions.index')
                             ->with('error', 'Error al crear la remisión: ' . $e->getMessage());
        }
    }
    
    
    
    
    

    public function show(Remision $remision)
    {
        $remision->load('cliente', 'items'); // Cargar los ítems con la remisión
        return view('remisions.show', compact('remision'));
    }

    public function descargarPdf(Remision $remision)
    {
        $remision->load('cliente', 'usuario', 'items');

        $pdf = PDF::loadView('remisions.pdf', compact('remision'));
        return $pdf->download("remision_{$remision->id}.pdf");
    }
    public function cuentasPorCobrar()
{
    // Obtener remisiones con items donde el saldo restante sea mayor a 0
    $remisiones = Remision::with(['cliente', 'items' => function($query) {
        $query->where('restante', '>', 0);
    }])->get()->filter(function($remision) {
        return $remision->items->isNotEmpty(); // Solo remisiones con ítems pendientes
    });

    return view('remisions.cuentas_por_cobrar', compact('remisiones'));
}

}
