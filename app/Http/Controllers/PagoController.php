<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\ItemRemision;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    // Registrar un nuevo pago
    public function store(Request $request)
    {
        $request->validate([
            'item_remision_id' => 'required|exists:item_remisions,id',
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
        ]);

        $item = ItemRemision::findOrFail($request->item_remision_id);

        // Crear el pago
        $pago = new Pago();
        $pago->item_remision_id = $item->id;
        $pago->monto = $request->monto;
        $pago->fecha_pago = $request->fecha_pago;
        $pago->metodo_pago = $request->metodo_pago;
        $pago->save();

        // Actualizar valores en ItemRemision
        $item->a_cuenta += $pago->monto;
        $item->restante = max($item->subtotal - $item->a_cuenta, 0);
        $item->save();

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    // Ver historial de pagos
    public function index($item_id)
    {
        $item = ItemRemision::with(['remision.cliente', 'pagos'])->findOrFail($item_id);

        return view('pagos.index', compact('item'));
    }

    // Generar PDF
    public function generarPDF($item_id)
    {
        $item = ItemRemision::with(['remision.cliente', 'pagos'])->findOrFail($item_id);
        $pdf = Pdf::loadView('pagos.recibo', compact('item'));

        return $pdf->stream('recibo_pago_' . $item->id . '.pdf');
    }

    // Vista inteligente de pagos con alertas
    public function seguimientoInteligente($item_id)
    {
        $item = ItemRemision::with(['remision.venta', 'remision.cliente', 'pagos'])->findOrFail($item_id);

        return view('pagos.inteligente', compact('item'));
    }
    public function seguimientoVenta($venta_id)
{
    $venta = Venta::with('cliente', 'pagos')->findOrFail($venta_id);

    // Si los pagos no están relacionados directamente, omite 'pagos' en with()
    $detalleFinanciamiento = $venta->detalle_financiamiento;

    return view('pagos.inteligente_venta', compact('venta', 'detalleFinanciamiento'));
}
}
