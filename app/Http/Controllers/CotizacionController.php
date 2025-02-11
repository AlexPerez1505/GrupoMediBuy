<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Registro;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;



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
    
        // Decodificar los productos asegurando que sea un array
        $productos = json_decode($cotizacion->productos, true);
    
        if (!is_array($productos)) {
            $productos = []; // Evitar errores si hay algún problema con el JSON
        }
     // Si cliente está almacenado como JSON
    $cliente = json_decode($cotizacion->cliente, true); 
        // Generar el PDF con la vista y los datos
        $pdf = PDF::loadView('cotizacion.pdf', [
            'cotizacion' => $cotizacion, 
            'productos' => $productos
        ]);
    
        return $pdf->download('cotizacion_' . $cotizacion->id . '.pdf');
    }
    



}

