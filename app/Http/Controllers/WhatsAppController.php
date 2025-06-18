<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function enviarRecordatorio(TwilioService $twilio, $ventaId)
    {
        $venta = Venta::with('cliente')->findOrFail($ventaId);
        $cliente = $venta->cliente;

        // Sanitizar y asegurar formato internacional del número
        $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono); // eliminar espacios y símbolos
        if (!str_starts_with($telefono, '521')) {
            $telefono = '521' . $telefono; // 52 México + 1 para celular
        }

        // Construir mensaje profesional
        $folio = 'No.2025-' . $venta->id;
        $mensaje = "Hola, soy *$cliente->nombre* y tengo el *folio de venta $folio*. Tengo una consulta relacionada con mi pago programado para hoy. ¿Podrían apoyarme? Gracias.";

        try {
            $twilio->enviarWhatsapp($telefono, $mensaje);
            return back()->with('success', 'Mensaje de WhatsApp enviado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al enviar WhatsApp: ' . $e->getMessage());
            return back()->with('error', 'No se pudo enviar el mensaje: ' . $e->getMessage());
        }
    }
}
