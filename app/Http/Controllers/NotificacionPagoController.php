<?php

namespace App\Http\Controllers;

use App\Models\PagoFinanciamiento;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PagoPendienteHoyMail;
use App\Mail\PagoPendienteHoyAdminMail;
use App\Services\TwilioService;
use App\Models\User;

class NotificacionPagoController extends Controller
{
    public function reenviar(PagoFinanciamiento $pago, TwilioService $twilio)
    {
        Log::info("Intentando reenviar notificaciones para PagoFinanciamiento ID {$pago->id}");

        // Asegurarse de cargar las relaciones necesarias
        $pago->load('venta.cliente');

        $venta = $pago->venta;
        $cliente = $venta?->cliente;

        if (!$pago->notificado || !$venta || !$cliente) {
            Log::warning("No se puede enviar la notificación. Datos incompletos o ya notificado.");
            return response()->json(['error' => 'No se puede enviar la notificación.'], 400);
        }

        // ✅ Enviar correo al cliente
        try {
            if ($cliente->email) {
                Mail::to($cliente->email)->send(new PagoPendienteHoyMail($venta));
                Log::info("📧 Correo enviado al cliente: {$cliente->email} (venta ID {$venta->id})");
            }
        } catch (\Exception $e) {
            Log::error("❌ Error al enviar correo al cliente: " . $e->getMessage());
        }

        // ✅ Enviar correo a administradores
        try {
            $adminEmails = User::where('role', 'admin')
                ->whereNotNull('email')
                ->pluck('email')
                ->filter();

            Log::debug('Correos de administradores: ' . implode(', ', $adminEmails->toArray()));

            foreach ($adminEmails as $adminEmail) {
                Log::debug("→ Enviando correo a admin: {$adminEmail}");
                Mail::to($adminEmail)->send(new PagoPendienteHoyAdminMail($venta));
                Log::info("📧 Correo enviado al administrador: {$adminEmail}");
            }

            if ($adminEmails->isEmpty()) {
                Log::warning("⚠️ No se encontraron correos de administradores.");
            }
        } catch (\Exception $e) {
            Log::error("❌ Error al enviar correo a administradores: " . $e->getMessage());
        }

        // ✅ Enviar WhatsApp
        try {
            $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono);
            if (!str_starts_with($telefono, '521')) {
                $telefono = '521' . $telefono;
            }

            if (preg_match('/^521[0-9]{10}$/', $telefono)) {
                $mensaje = "Hola, *$cliente->nombre*. Este es un recordatorio de tu *pago programado hoy* correspondiente al *folio No.2025-{$venta->id}*. Si tienes dudas, contáctanos. ¡Gracias por tu preferencia!";
                $twilio->enviarWhatsapp($telefono, $mensaje);
                Log::info("📲 WhatsApp enviado a $telefono (venta ID {$venta->id})");
            } else {
                Log::warning("⚠️ Teléfono no válido para WhatsApp: $telefono");
            }
        } catch (\Exception $e) {
            Log::error("❌ Error al enviar WhatsApp: " . $e->getMessage());
        }

        // ✅ Marcar como notificado
        $pago->notificado = true;
        $pago->save();

        return response()->json(['success' => true]);
    }
}
