<?php
// app/Http/Controllers/WhatsappPromotionController.php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Categoria; // si existe el modelo
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsappPromotionController extends Controller
{
    /**
     * Formulario de envío directo (sin plantilla): imagen + texto.
     * GET /promos/whatsapp/direct
     */
    public function directCreate(Request $request)
    {
        $q         = trim((string)$request->get('q', ''));
        $categoria = $request->get('categoria');

        // Listado de clientes (limit defensivo para UI)
        $clientes = Cliente::query()
            ->when($q, function ($qr) use ($q) {
                $like = "%{$q}%";
                $qr->where(function ($w) use ($like) {
                    $w->where('nombre',  'like', $like)
                      ->orWhere('apellido','like', $like)
                      ->orWhere('telefono','like', $like)
                      ->orWhere('email',   'like', $like)
                      ->orWhere('asesor',  'like', $like);
                });
            })
            ->when($categoria, fn($qr) => $qr->where('categoria_id', $categoria))
            ->orderByRaw("COALESCE(NULLIF(nombre,''),'~') asc")
            ->limit(1000)
            ->get(['id','nombre','apellido','telefono','email','categoria_id','asesor']);

        // Catálogo de categorías (si existe el modelo)
        $categorias = class_exists(Categoria::class)
            ? Categoria::orderBy('nombre')->get(['id','nombre'])
            : collect();

        return view('promociones.whatsapp-direct', compact('clientes','categorias','q','categoria'));
    }

    /**
     * Envía imagen + texto (sin plantilla) a múltiples destinatarios.
     * POST /promos/whatsapp/direct
     */
    public function directSend(Request $request, WhatsAppService $wa)
    {
        // Validaciones de UI
        $data = $request->validate([
            'titulo'            => ['required','string','max:60'],
            'descripcion'       => ['required','string','max:1024'],
            'imagen_file'       => ['required','file','mimes:jpg,jpeg,png','max:5120'], // 5 MB
            'clientes_ids'      => ['nullable','array'],
            'clientes_ids.*'    => ['integer','exists:clientes,id'],
            'clientes_manual'   => ['nullable','string'],
        ]);

        // 1) Construir lista de números a partir de BD
        $numbers = [];
        if (!empty($data['clientes_ids'])) {
            $fromDb = Cliente::whereIn('id', $data['clientes_ids'])
                ->pluck('telefono')
                ->filter()
                ->all();
            $numbers = array_merge($numbers, $fromDb);
        }

        // 2) Agregar números pegados manualmente
        if (!empty($data['clientes_manual'])) {
            $raw = preg_split('/[\r\n,;]+/', (string)$data['clientes_manual']);
            $numbers = array_merge($numbers, array_map('trim', $raw));
        }

        // 3) Normalizar a E.164 (MX: 10 dígitos -> 521XXXX...)
        $numbers = array_values(array_unique(array_filter(array_map(function ($n) {
            return WhatsAppService::normalizeMsisdn((string)$n);
        }, $numbers), fn($n) => !empty($n))));

        // 4) Evitar auto-envío al mismo número del remitente (si está configurado)
        $senderE164 = (string) config('services.whatsapp.phone_e164', env('WHATSAPP_PHONE_NUMBER', ''));
        $senderE164 = preg_replace('/\D+/', '', $senderE164 ?? '');
        if ($senderE164 !== '') {
            $numbers = array_values(array_filter($numbers, function ($n) use ($senderE164) {
                return preg_replace('/\D+/', '', $n) !== $senderE164;
            }));
        }

        if (empty($numbers)) {
            return back()->with('wa_info', 'No seleccionaste destinatarios.')->withInput();
        }

        // 5) Subir imagen UNA sola vez -> obtener media_id
        try {
            $upload  = $wa->uploadMedia($request->file('imagen_file'));
        } catch (\Throwable $e) {
            \Log::error('WA_MEDIA_UPLOAD_EXCEPTION', ['e' => $e->getMessage()]);
            return back()->with('wa_info', 'Ocurrió un error al preparar la subida de imagen.')->withInput();
        }

        $uJson   = $upload->json();
        $mediaId = data_get($uJson, 'id');

        if (!$upload->successful() || !$mediaId) {
            \Log::warning('WA_MEDIA_UPLOAD_FAIL', ['resp' => $uJson]);
            return back()
                ->with('wa_info', 'No se pudo subir la imagen a WhatsApp. Revisa tamaño/tipo de archivo.')
                ->with('wa_fail', [$uJson])
                ->withInput();
        }

        // 6) Enviar a todos los destinatarios
        //    Sugerencia: puedes ajustar el delay si tu WABA es sensible al rate limit.
        $delayMs = (int) env('WA_PROMO_DELAY_MS', 0);

        $ok = [];
        $fail = [];

        // Opcional: dividir en chunks para evitar timeouts muy largos
        $chunks = array_chunk($numbers, 100); // 100 por bloque
        foreach ($chunks as $block) {
            foreach ($block as $n) {
                $resp  = $wa->sendImageByIdPromotion($n, $mediaId, $data['titulo'], $data['descripcion']);
                $json  = $resp->json();
                $wamid = data_get($json, 'messages.0.id');

                if ($resp->successful() && $wamid) {
                    $ok[] = $n;
                    \Log::info('WA_OK_IMG_FREE', ['to' => $n, 'wamid' => $wamid]);
                } else {
                    $code   = data_get($json, 'error.code');
                    $detail = data_get($json, 'error.message') ?: data_get($json, 'error.error_data.details');
                    $fail[] = ['n' => $n, 'code' => $code, 'detail' => $detail, 'raw' => $json];
                    \Log::warning('WA_FAIL_IMG_FREE', ['to' => $n, 'resp' => $json]);
                }

                if ($delayMs > 0) {
                    usleep($delayMs * 1000);
                }
            }
        }

        // 7) Mensaje de resultado
        $msg = count($ok) . " enviado(s), " . count($fail) . " falló(aron).";
        if (count($fail)) {
            // Tip de 24h si aparece 131047 entre los fallos
            $has131047 = collect($fail)->contains(fn($f) => (string)($f['code'] ?? '') === '131047');
            $tip = $has131047
                ? " Algunos destinatarios están fuera de la ventana de 24 h (código 131047). En esos casos debes usar plantilla."
                : "";
            return back()->with('wa_info', $msg . $tip)->with('wa_fail', $fail)->withInput();
        }

        return back()->with('wa_success', $msg . ' (imagen desde archivo, sin plantilla)');
    }
}
