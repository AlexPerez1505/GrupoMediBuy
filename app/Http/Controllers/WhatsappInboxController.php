<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ChatMessage;
use App\Models\Cliente;
use App\Models\ChatFlow;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class WhatsappInboxController extends Controller
{
    /** Bandeja de entrada: conversaciones agrupadas por msisdn (contraparte) */
    public function index()
    {
        $ourE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));

        $base = ChatMessage::query()
            ->select([
                DB::raw("CASE WHEN REPLACE(`from`, '+', '') <> '{$ourE164}' THEN `from` ELSE `to` END AS msisdn"),
                DB::raw('MAX(wa_timestamp) as last_at'),
            ])
            ->groupBy('msisdn')
            ->orderByDesc('last_at')
            ->get();

        $threads = $base->map(function ($row) {
            $msisdn = $row->msisdn;

            $lastMsg = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->orderBy('wa_timestamp', 'desc')
                ->first();

            $digits = preg_replace('/\D+/', '', (string) $msisdn);
            $last10 = substr($digits, -10);
            $cliente = $last10
                ? Cliente::where('telefono', 'like', "%{$last10}%")->first()
                : null;

            $displayName = $cliente
                ? trim(trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))) ?: $msisdn
                : $msisdn;

            $flow = ChatFlow::where('from', $msisdn)->first();
            $ctx  = is_array($flow?->context) ? $flow->context : [];
            $agentName = $ctx['agent']['name'] ?? null;
            $handover  = !empty($ctx['handover']) || (($flow->step ?? '') === 'espera_asesor');

            $unread = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->where('direction', 'in')
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '<>', 'read');
                })
                ->count();

            return (object) [
                'msisdn'       => $msisdn,
                'display_name' => $displayName,
                'last_text'    => $lastMsg?->text ?? null,
                'last_type'    => $lastMsg?->type ?? null,
                'last_at'      => $row->last_at,
                'agent_name'   => $agentName,
                'handover'     => $handover,
                'unread_count' => $unread,
            ];
        });

        return view('whatsapp.inbox', compact('threads'));
    }

    /** Mostrar el chat con un número específico */
    public function show($msisdn)
    {
        $messages = ChatMessage::where(function ($q) use ($msisdn) {
                $q->where('from', $msisdn)->orWhere('to', $msisdn);
            })
            ->orderBy('wa_timestamp', 'asc')
            ->get()
            ->map(function ($m) {
                if (in_array($m->type, ['image', 'document'])) {
                    $m->media_link = $m->media_id
                        ? ( \Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : $m->media_link )
                        : $m->media_link;
                }
                return $m;
            });

        $digits = preg_replace('/\D+/', '', (string) $msisdn);
        $last10 = substr($digits, -10);
        $cliente = $last10
            ? Cliente::where('telefono', 'like', "%{$last10}%")->first()
            : null;

        $displayName = $cliente
            ? trim(trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))) ?: $msisdn
            : $msisdn;

        $flow = ChatFlow::firstOrCreate(['from' => $msisdn], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];
        $agentName = $ctx['agent']['name'] ?? null;
        $handover  = !empty($ctx['handover']);

        return view('whatsapp.chat', [
            'messages'     => $messages,
            'currentUser'  => $msisdn,
            'displayName'  => $displayName,
            'agentName'    => $agentName,
            'handover'     => $handover,
        ]);
    }

    /** Enviar mensaje (texto, imagen o documento) — MANUAL (asesor) */
    public function send(Request $request, $msisdn)
    {
        $request->validate([
            'type' => 'required|string|in:text,image,document',
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10 MB
        ]);

        $type = $request->input('type');
        if ($type === 'text') {
            $request->validate(['text' => 'required|string']);
        } else {
            $request->validate(['file' => 'required|file|max:10240']);
        }

        $to        = WhatsAppService::normalizeMsisdn((string) $msisdn);
        $fromE164  = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));
        $token     = (string) config('services.whatsapp.token');
        $phoneId   = (string) config('services.whatsapp.phone_id');
        $apiVer    = (string) (config('services.whatsapp.api_version') ?? config('services.whatsapp.version', 'v21.0'));
        $url       = "https://graph.facebook.com/{$apiVer}/{$phoneId}/messages";
        $appTz     = config('app.timezone', 'UTC'); // 👈 misma zona que webhook (MX)

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $to,
            'type' => $type,
        ];

        $mediaId  = null;
        $filename = null;

        if ($type === 'text') {
            $payload['text'] = ['body' => $request->input('text')];
        } else {
            $mediaId = $this->uploadMediaToWhatsApp($request->file('file'));
            if (!$mediaId) {
                return response()->json(['message' => 'No se pudo subir el archivo a WhatsApp.'], 422);
            }
            $filename = $request->file('file')->getClientOriginalName();

            if ($type === 'image') {
                $payload['image'] = ['id' => $mediaId];
            } else {
                $payload['document'] = ['id' => $mediaId, 'filename' => $filename];
            }
        }

        // Enviar a la API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            Log::error('WA_SEND_CURL_ERR', ['error' => $err]);
            return response()->json(['message' => 'Error enviando a WhatsApp.'], 500);
        }
        curl_close($ch);

        $res = json_decode($response, true) ?: [];
        Log::info('WA_SEND_RES', ['res' => $res]);

        // ID devuelto por WhatsApp
        $wamid = $res['messages'][0]['id'] ?? uniqid('wamid_');

        // Reclamar conversación por el asesor que envía
        $flow = ChatFlow::firstOrCreate(['from' => $to], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];
        $ctx['handover'] = true;
        if ($user = Auth::user()) {
            $ctx['agent'] = [
                'id'   => $user->id,
                'name' => $user->name ?? ('Agente #' . $user->id),
            ];
        }
        $flow->step    = 'espera_asesor';
        $flow->context = $ctx;
        $flow->save();

        // === Guardar en BD ===
        $chat = ChatMessage::create([
            'wamid'          => $wamid,
            'from'           => $fromE164,
            'to'             => $to,
            'direction'      => 'out',
            'type'           => $type,
            'text'           => $type === 'text' ? $request->input('text') : null,
            'media_id'       => $mediaId,
            'media_filename' => $filename,
            'wa_timestamp'   => now($appTz),  // 👈 MX, igual que webhook
            'status'         => 'sent',
            'raw'            => $res,
        ]);

        // Opcional: forzar wa_timestamp = created_at
        $chat->wa_timestamp = $chat->created_at;
        $chat->save();

        // Media proxy inmediato
        if (in_array($chat->type, ['image', 'document'])) {
            $chat->media_link = $chat->media_id
                ? ( \Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $chat->media_id) : $chat->media_link )
                : $chat->media_link;
        }

        // Respuesta AJAX ya normalizada
        if ($request->ajax()) {
            return response()
                ->json($this->presentMessageForFront($chat, $appTz))
                ->header('Cache-Control','no-store, no-cache, must-revalidate');
        }

        return back()->with('status', 'Mensaje enviado');
    }

    /** Subir media al endpoint /media (retorna media_id) */
    private function uploadMediaToWhatsApp($file): ?string
    {
        $phoneId = (string) config('services.whatsapp.phone_id');
        $apiVer  = (string) (config('services.whatsapp.api_version') ?? config('services.whatsapp.version', 'v21.0'));
        $token   = (string) config('services.whatsapp.token');

        $url = "https://graph.facebook.com/{$apiVer}/{$phoneId}/media";
        $cFile = new \CURLFile(
            $file->getRealPath(),
            $file->getMimeType() ?: 'application/octet-stream',
            $file->getClientOriginalName()
        );

        $data = [
            'file'              => $cFile,
            'messaging_product' => 'whatsapp',
            'type'              => $file->getMimeType() ?: 'application/octet-stream',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            Log::error('WA_UPLOAD_CURL_ERR', ['error' => curl_error($ch)]);
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        $res = json_decode($result, true) ?: [];
        if (!empty($res['error'])) {
            Log::error('WA_UPLOAD_ERR', ['resp' => $res]);
            return null;
        }

        return $res['id'] ?? null;
    }

    /** Polling del chat: mensajes de una conversación (ISO UTC) */
    public function fetch($msisdn)
    {
        $messages = ChatMessage::where(function ($q) use ($msisdn) {
                $q->where('from', $msisdn)->orWhere('to', $msisdn);
            })
            ->orderBy('wa_timestamp', 'asc')
            ->get()
            ->map(function ($m) {
                if (in_array($m->type, ['image', 'document'])) {
                    $m->media_link = $m->media_id
                        ? ( \Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : $m->media_link )
                        : $m->media_link;
                }
                try {
                    // BD -> ISO UTC que el front convierte a MX
                    $m->wa_timestamp = Carbon::parse($m->wa_timestamp)->utc()->toIso8601String();
                } catch (\Throwable $e) {}
                return $m;
            });

        return response()
            ->json($messages)
            ->header('Cache-Control','no-store, no-cache, must-revalidate');
    }

    /** Polling de bandeja: lista de hilos recientes (convierte since a zona app + ETag/304) */
    public function fetchThreads(Request $request)
    {
        $ourE164  = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));
        $sinceIso = $request->query('since');
        $appTz    = config('app.timezone', 'UTC');

        // El front manda 'since' en ISO-UTC; convertimos a zona del app (MX) para comparar con wa_timestamp.
        $sinceApp = null;
        if ($sinceIso) {
            try {
                $sinceApp = Carbon::parse($sinceIso)->setTimezone($appTz);
            } catch (\Throwable $e) {
                $sinceApp = null;
            }
        }

        $q = ChatMessage::query()
            ->select([
                DB::raw("CASE WHEN REPLACE(`from`, '+', '') <> '{$ourE164}' THEN `from` ELSE `to` END AS msisdn"),
                DB::raw('MAX(wa_timestamp) as last_at'),
            ])
            ->when($sinceApp, function ($qq) use ($sinceApp) {
                $qq->where('wa_timestamp', '>', $sinceApp->format('Y-m-d H:i:s'));
            })
            ->groupBy('msisdn')
            ->orderByDesc('last_at')
            ->limit(80);

        $base = $q->get();

        $rows = $base->map(function ($row) use ($appTz) {
            $msisdn = $row->msisdn;

            $lastMsg = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->orderBy('wa_timestamp', 'desc')
                ->first();

            $digits = preg_replace('/\D+/', '', (string) $msisdn);
            $last10 = substr($digits, -10);
            $cliente = $last10
                ? Cliente::where('telefono', 'like', "%{$last10}%")->first()
                : null;

            $display = $cliente
                ? trim(trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))) ?: $msisdn
                : $msisdn;

            $flow = ChatFlow::where('from', $msisdn)->first();
            $ctx  = is_array($flow?->context) ? $flow->context : [];
            $agentName = $ctx['agent']['name'] ?? null;
            $handover  = !empty($ctx['handover']) || (($flow->step ?? '') === 'espera_asesor');

            $unread = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->where('direction', 'in')
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '<>', 'read');
                })
                ->count();

            return [
                'peer'         => $msisdn,
                'display_name' => $display,
                'last_text'    => $lastMsg?->text ?? null,
                // Publicamos en UTC para que el front lo pinte en MX
                'last_at'      => $row->last_at ? Carbon::parse($row->last_at, $appTz)->utc()->toIso8601String() : null,
                'unread_count' => $unread,
                'agent_name'   => $agentName,
                'handover'     => $handover,
            ];
        })->values();

        // ETag/304 sólido y tolerante a 'W/' en If-None-Match
        $etagBase = md5($sinceIso.'|'.$rows->toJson(JSON_UNESCAPED_UNICODE));
        $etag     = '"'.$etagBase.'"';

        $normalizeIfNone = function ($v) {
            if ($v === null) return null;
            $v = trim($v);
            if (str_starts_with($v, 'W/')) $v = substr($v, 2);
            return trim($v, '"');
        };

        $clientTag = $normalizeIfNone($request->headers->get('If-None-Match'));
        if ($clientTag !== null && $clientTag === $normalizeIfNone($etag)) {
            return response('', 304)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                ->header('Vary', 'If-None-Match');
        }

        return response()
            ->json($rows)
            ->header('ETag', $etag)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Vary', 'If-None-Match');
    }

    /* ======================== Helpers ======================== */

    /** Normaliza un mensaje para el front: incluye ISO-UTC y media_link resuelto */
    private function presentMessageForFront(ChatMessage $m, string $appTz): array
    {
        $isoUtc = '';
        try {
            // wa_timestamp está en zona app (MX). Lo publicamos como ISO UTC para el front.
            $isoUtc = Carbon::parse($m->wa_timestamp, $appTz)->utc()->toIso8601String();
        } catch (\Throwable $e) {}

        $mediaLink = null;
        if (in_array($m->type, ['image', 'document'])) {
            $mediaLink = $m->media_id
                ? ( \Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : $m->media_link )
                : $m->media_link;
        }

        return [
            'id'             => $m->id,
            'wamid'          => $m->wamid,
            'from'           => $m->from,
            'to'             => $m->to,
            'direction'      => $m->direction,
            'type'           => $m->type,
            'text'           => $m->text,
            'media_id'       => $m->media_id,
            'media_link'     => $mediaLink,
            'media_filename' => $m->media_filename,
            'wa_timestamp'   => $isoUtc,     // 👈 siempre ISO UTC
            'status'         => $m->status,
        ];
    }
}
