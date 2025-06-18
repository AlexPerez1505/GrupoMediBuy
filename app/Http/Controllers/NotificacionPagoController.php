<?php

namespace App\Http\Controllers;

use App\Models\PagoFinanciamiento;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PagoPendienteHoyMail;
use App\Services\TwilioService;

class NotificacionPagoController extends Controller
{
    public function reenviar(PagoFinanciamiento $pago, TwilioService $twilio)
    {
        Log::info("Intentando reenviar notificaciones para PagoFinanciamiento ID {$pago->id}");

        if (
            !$pago->notificado &&
            $pago->venta &&
            $pago->venta->cliente
        ) {
            $cliente = $pago->venta->cliente;
            $venta = $pago->venta;

            // ✅ ENVÍO DE CORREO
            try {
                if ($cliente->email) {
                    Mail::to($cliente->email)->send(new PagoPendienteHoyMail($venta));
                    Log::info("Correo enviado a {$cliente->email} para venta ID {$venta->id}");
                }
            } catch (\Exception $e) {
                Log::error("Error al enviar el correo: " . $e->getMessage());
            }

            // ✅ ENVÍO DE WHATSAPP
            try {
                $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono);
                if (!str_starts_with($telefono, '521')) {
                    $telefono = '521' . $telefono;
                }

                if (preg_match('/^521[0-9]{10}$/', $telefono)) {
                    $mensaje = "Hola, *$cliente->nombre*. Este es un recordatorio de tu *pago programado hoy* correspondiente al *folio No.2025-{$venta->id}*. Si tienes dudas, contáctanos. ¡Gracias por tu preferencia!";
                    $twilio->enviarWhatsapp($telefono, $mensaje);
                    Log::info("WhatsApp enviado a $telefono para venta ID {$venta->id}");
                } else {
                    Log::warning("Teléfono no válido para WhatsApp: $telefono");
                }
            } catch (\Exception $e) {
                Log::error("Error al enviar WhatsApp: " . $e->getMessage());
            }

            // ✅ Marcar como notificado si al menos uno fue exitoso (puedes ajustar esta lógica si quieres que sea solo si ambos fueron exitosos)
            $pago->notificado = true;
            $pago->save();

            return response()->json(['success' => true]);
        }

        Log::warning("No se pudo enviar: condiciones no válidas para pago ID {$pago->id}");
        return response()->json(['error' => 'No se puede enviar la notificación.'], 400);
    }
}
