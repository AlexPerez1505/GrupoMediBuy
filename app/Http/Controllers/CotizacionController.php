<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Registro;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;



class CotizacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'nullable|string',
            'productos' => 'nullable|array',
            'subtotal' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'iva' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'tipo_pago' => 'nullable|string',
            'plan_pagos' => 'nullable|array',
            'nota' => 'nullable|string',
            'valido_hasta' => 'nullable|date',
            'lugar_cotizacion' => 'nullable|string',
        ]);
        
    
        $cotizacion = Cotizacion::create([
            'cliente' => $request->cliente,
            'productos' => json_encode($request->productos),
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'iva' => $request->iva,
            'total' => $request->total,
            'tipo_pago' => $request->tipo_pago,
            'plan_pagos' => json_encode($request->plan_pagos),
            'nota' => $request->nota,
            'valido_hasta' => $request->valido_hasta,
            'lugar_cotizacion' => $request->lugar_cotizacion,
        ]);
    
        return response()->json(['mensaje' => 'Cotización guardada', 'id' => $cotizacion->id]);
    }

  
    public function descargarPDF($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
    
        $productos = json_decode($cotizacion->productos, true) ?? [];
    
        $createdAt = Carbon::parse($cotizacion->created_at);
        $vigencia = Carbon::parse($cotizacion->valido_hasta);
        $diasRestantes = $createdAt->diffInDays($vigencia);

        // Si la fecha de vigencia ya pasó, se puede ajustar el mensaje
        if ($diasRestantes < 0) {
            $diasRestantes = 'Vencido';
        } else {
            // Redondear los días a un número entero
            $diasRestantes = round($diasRestantes); // Usar round() para redondear al entero más cercano
        }

    
        $pdf = PDF::loadView('cotizacion.pdf', [
            'cotizacion' => $cotizacion,
            'productos' => $productos,
            'diasRestantes' => $diasRestantes
        ]);
    
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="cotizacion_'.$cotizacion->id.'.pdf"');
    }
    
}

