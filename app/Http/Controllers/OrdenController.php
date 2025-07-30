<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Aparato;
use App\Models\Orden;
use PDF;


class OrdenController extends Controller
{
    /** Mostrar formulario de creación */
    public function create()
    {
        return view('ordenes.create', [
            'clientes' => Cliente::orderBy('nombre')->get(),
            'aparatos' => Aparato::orderBy('tipo')->get(),   // <-- tu modelo Aparato
        ]);
    }
public function store(Request $r)
{
    \Log::info('🛠️ [DEBUG] store() recibido:', $r->all());

    // Validación
    $r->validate([
        'cliente_id'            => 'required|exists:clientes,id',
        'fecha_entrada'         => 'required|date',
        'proximo_mantenimiento' => 'required|in:3,6,12',
        'aparato_id'            => 'required|exists:aparatos,id',
        'checklist'             => 'required|array|min:1',
        'checklist.*'           => 'string', // O ajusta según los valores válidos si quieres más precisión
    ]);

    // Crear la orden
    $orden = Orden::create([
        'cliente_id'            => $r->cliente_id,
        'fecha_entrada'         => $r->fecha_entrada,
        'fecha_mantenimiento'   => now()->format('Y-m-d'),
        'proximo_mantenimiento' => now()->addMonths(intval($r->proximo_mantenimiento))->format('Y-m-d'),
        'aparato_id'            => $r->aparato_id,
        'checklist'             => json_encode($r->checklist), // Aquí conviertes el array a JSON para guardar en texto
    ]);

    \Log::info('🛠️ [DEBUG] Orden guardada:', ['orden_id' => $orden->id, 'checklist' => $orden->checklist]);

    // Redirigir a ruta PDF (ajusta si la ruta necesita parámetros)
    return redirect()->route('orden.pdf', $orden);
}
public function pdf(Orden $orden)
{
    $orden->load('cliente', 'aparato');
    $data = [
        'orden'     => $orden,
        'checklist' => $orden->checklist,
    ];
    $pdf = PDF::loadView('ordenes.pdf', $data)
              ->setPaper('a4','portrait');

    return $pdf->download("OS_{$orden->id}.pdf");
}

}
