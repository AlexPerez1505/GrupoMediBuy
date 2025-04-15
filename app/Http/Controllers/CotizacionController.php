<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class CotizacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'nullable|array',
            'productos' => 'nullable|array',
            'subtotal' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'envio' => 'nullable|numeric',
            'iva' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'tipo_pago' => 'nullable|string',
            'plan_pagos' => 'nullable|array',
            'nota' => 'nullable|string',
            'valido_hasta' => 'nullable|date',
            'lugar_cotizacion' => 'nullable|string',
            'registrado_por' => 'nullable|string'
        ]);

        $cotizacion = Cotizacion::create([
            'cliente' => json_encode($request->cliente), // Guardamos como JSON
            'productos' => json_encode($request->productos),
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'envio' => $request->envio,
            'iva' => $request->iva,
            'total' => $request->total,
            'tipo_pago' => $request->tipo_pago,
            'plan_pagos' => json_encode($request->plan_pagos),
            'nota' => $request->nota,
            'valido_hasta' => $request->valido_hasta,
            'lugar_cotizacion' => $request->lugar_cotizacion,
            'registrado_por' => Auth::user()->name,
        ]);

        return response()->json(['mensaje' => 'Cotización guardada', 'id' => $cotizacion->id]);
    }

    public function descargarPDF($id)
    {
        // Buscar la cotización
        $cotizacion = Cotizacion::findOrFail($id);
    
        // Decodificar productos y cliente
        $productos = json_decode($cotizacion->productos, true) ?? [];
        $cliente = json_decode($cotizacion->cliente, true) ?? [];
    
        // Obtener el nombre del cliente y convertirlo a mayúsculas, si no tiene, poner "sin_nombre"
        $nombreCliente = isset($cliente['nombre']) ? strtoupper(Str::slug($cliente['nombre'], '_')) : 'SIN_NOMBRE';
    
        // Validar fechas
        $createdAt = Carbon::parse($cotizacion->created_at);
        $vigencia = $cotizacion->valido_hasta ? Carbon::parse($cotizacion->valido_hasta) : null;
        $diasRestantes = $vigencia ? $createdAt->diffInDays($vigencia) : 'Sin vigencia';
    
        // Si la fecha de vigencia ya pasó, se puede ajustar el mensaje
        if ($diasRestantes < 0) {
            $diasRestantes = 'Vencido';
        } else {
            // Redondear los días a un número entero
            $diasRestantes = round($diasRestantes); // Usar round() para redondear al entero más cercano
        }
    
        // Generar PDF con la vista
        $pdf = PDF::loadView('cotizacion.pdf', [
            'cotizacion' => $cotizacion,
            'productos' => $productos,
            'cliente' => $cliente,
            'diasRestantes' => $diasRestantes
        ]);
    
        // Descargar el PDF con el nombre personalizado (en mayúsculas)
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="COTIZACION_'.$nombreCliente.'.pdf"');
    }
    // En tu controlador
    public function index()
    {
        // Obtener todas las cotizaciones
        $cotizaciones = Cotizacion::all();
    
        // Pasar las cotizaciones a la vista
        return view('historial', compact('cotizaciones'));
    }
    

}
