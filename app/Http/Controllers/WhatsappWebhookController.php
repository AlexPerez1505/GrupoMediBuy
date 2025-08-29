<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ChatMessage;
use App\Models\Cliente;
use App\Models\ChatFlow;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class WhatsappWebhookController extends Controller
{
    /** GET /api/webhooks/whatsapp (verificación de Meta) */
    public function verify(Request $req)
    {
        $token  = (string) config('services.whatsapp.verify_token');
        $mode   = $req->query('hub_mode', $req->query('hub.mode'));
        $verify = $req->query('hub_verify_token', $req->query('hub.verify_token'));
        $chall  = $req->query('hub_challenge', $req->query('hub.challenge'));

        if ($mode === 'subscribe' && hash_equals($token, (string) $verify)) {
            return response($chall, 200);
        }
        return response('Forbidden', 403);
    }

    /** POST /api/webhooks/whatsapp (eventos entrantes y estatus) */
    public function receive(Request $request)
    {
        $raw = $request->getContent();
        Log::info('WA_WEBHOOK_RAW', ['raw' => $raw]);

        $payload = json_decode($raw, true) ?: [];
        Log::info('WA_WEBHOOK_IN', ['body' => $payload]);

        $changes = [];
        if (isset($payload['entry'][0]['changes'])) {
            foreach ($payload['entry'] as $entry) {
                foreach ($entry['changes'] as $change) $changes[] = $change;
            }
        } elseif (isset($payload['field'], $payload['value'])) {
            $changes[] = $payload;
        } else {
            Log::warning('WA_WEBHOOK_UNKNOWN_SHAPE', ['payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        $wa      = app(WhatsAppService::class);
        $ourE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));
        $appTz   = config('app.timezone', 'UTC');

        foreach ($changes as $change) {
            $value = $change['value'] ?? [];

            /* ========== 1) Mensajes entrantes ========== */
            foreach (($value['messages'] ?? []) as $msg) {
                try {
                    $type  = $msg['type'] ?? 'text';
                    $from  = WhatsAppService::normalizeMsisdn($msg['from'] ?? '');
                    $to    = $ourE164;
                    $wamid = $msg['id'] ?? null;

                    // WhatsApp manda epoch segundos (UTC). Guardamos en la zona del app (MX).
                    $ts = isset($msg['timestamp'])
                        ? Carbon::createFromTimestamp((int) $msg['timestamp'], 'UTC')->setTimezone($appTz)
                        : now($appTz);

                    // Cliente con nombre/apellido + comentario “Creado desde WhatsApp”
                    $cliente = $this->ensureCliente($from, $value);

                    // Persistir/obtener flujo por número
                    $flow = ChatFlow::firstOrCreate(['from' => $from], ['step' => 'start']);

                    // Normalizar contenido
                    $storedType = $type;
                    $textRaw    = null;
                    $textNorm   = null;
                    $mediaId = null; $mediaLink = null; $filename = null;

                    if ($type === 'text') {
                        $textRaw  = (string) ($msg['text']['body'] ?? '');
                        $textNorm = mb_strtolower(trim($textRaw));
                    } elseif ($type === 'image') {
                        $mediaId   = $msg['image']['id']   ?? null;
                        $mediaLink = $msg['image']['link'] ?? null;
                    } elseif ($type === 'document') {
                        $mediaId    = $msg['document']['id']       ?? null;
                        $mediaLink  = $msg['document']['link']     ?? null;
                        $filename   = $msg['document']['filename'] ?? null;
                    } elseif ($type === 'interactive') {
                        $itype = $msg['interactive']['type'] ?? null;
                        if ($itype === 'button_reply') {
                            $textRaw  = (string) ($msg['interactive']['button_reply']['title'] ?? '');
                            $textNorm = mb_strtolower((string) ($msg['interactive']['button_reply']['id'] ?? $textRaw));
                        } elseif ($itype === 'list_reply') {
                            $textRaw  = (string) ($msg['interactive']['list_reply']['title'] ?? '');
                            $textNorm = mb_strtolower((string) ($msg['interactive']['list_reply']['id'] ?? $textRaw));
                        } else {
                            $textRaw = 'interactive';
                        }
                        $storedType = 'text';
                    } elseif ($type === 'button') {
                        // Respuesta rápida (Quick Reply) de PLANTILLA
                        $btnText    = (string) data_get($msg, 'button.text', '');
                        $btnPayload = (string) data_get($msg, 'button.payload', $btnText);
                        $textRaw    = $btnText ?: $btnPayload;
                        $textNorm   = mb_strtolower(trim($btnPayload ?: $btnText));
                        $storedType = 'text';
                    }

                    // Guardar mensaje entrante
                    ChatMessage::updateOrCreate(
                        ['wamid' => $wamid],
                        [
                            'cliente_id'     => $cliente->id,
                            'from'           => $from,
                            'to'             => $to,
                            'direction'      => 'in',
                            'type'           => $storedType,
                            'text'           => $textRaw,
                            'media_id'       => $mediaId,
                            'media_link'     => $mediaLink,
                            'media_filename' => $filename,
                            'wa_timestamp'   => $ts,              // 👈 MX
                            'status'         => 'received',
                            'raw'            => $msg,
                        ]
                    );

                    /* ==== CALL CENTER MODE: ¿Responde el bot o ya está en mano de asesor? ==== */
                    if (!$this->shouldBotReply($flow)) {
                        // Conversación bajo control de asesor → no responder automáticamente.
                        Log::info('BOT_SKIPPED_HANDOVER', ['from' => $from]);
                        continue;
                    }

                    /* —— CSAT capturado desde QUICK REPLY (type: button) —— */
                    if (($msg['type'] ?? '') === 'button') {
                        $t = mb_strtolower(trim((string) data_get($msg, 'button.payload', data_get($msg, 'button.text', ''))));
                        $score = null;
                        if (str_contains($t, 'excelente') || $t === '5') $score = 5;
                        elseif (str_contains($t, 'buena') || $t === '3') $score = 3;
                        elseif (str_contains($t, 'mala') || $t === '1') $score = 1;

                        if (!is_null($score)) {
                            $ctx = $flow->context ?? [];
                            $ctx['csat'] = [
                                'pending' => false,
                                'score'   => $score,
                                'at'      => now($appTz)->toIso8601String(),
                            ];
                            // Reactiva modo automático
                            $this->flowSet($flow, 'menu', array_merge($ctx, ['handover' => false]));

                            $wa->sendText($from, "¡Gracias por tu calificación ({$score}/5)! 💚\nSi necesitas algo más, escribe *hola* para ver el menú.");
                            $this->storeOut($cliente->id, $to, $from, 'text', "Agradecimiento CSAT ({$score})", $appTz);
                            continue;
                        }
                    }

                    /* ——— Entradas para mostrar menú ——— */
                    if ($textNorm && in_array($textNorm, ['hola','buenas','hi','menu'])) {
                        $this->flowSet($flow, 'menu');
                        $wa->sendMainMenu($from);
                        $this->storeOut($cliente->id, $to, $from, 'interactive', 'MENÚ PRINCIPAL', $appTz);
                        continue;
                    }

                    /* ——— Selecciones interactivas ——— */
                    if (($msg['type'] ?? null) === 'interactive' && $textNorm) {
                        $handled = $this->handleInteractiveSelection($flow, $textNorm, $from, $to, $wa, $cliente->id, $appTz);
                        if ($handled) continue;
                    }

                    /* ——— Flujo conversacional ——— */
                    if ($textNorm) {
                        $reply = $this->formFlow($flow, $textNorm, $wa, $from, $to, $cliente->id, $appTz);
                        if ($reply) {
                            $wa->sendText($from, $reply);
                            $this->storeOut($cliente->id, $to, $from, 'text', $reply, $appTz);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('WA_INCOMING_ERROR', [
                        'ex' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                }
            }

            /* ========== 2) Estatus de mensajes salientes ========== */
            foreach (($value['statuses'] ?? []) as $st) {
                try {
                    $id     = $st['id']     ?? null;
                    $status = $st['status'] ?? null; // sent, delivered, read, failed
                    // Si quieres guardar cuándo llegó el status, conviértelo a MX, pero NO toques wa_timestamp.
                    $statusAt = isset($st['timestamp'])
                        ? Carbon::createFromTimestamp((int) $st['timestamp'], 'UTC')->setTimezone($appTz)
                        : null;

                    if ($id) {
                        $upd = ['status' => $status];
                        // Si tienes columna status_at, descomenta:
                        // if ($statusAt) $upd['status_at'] = $statusAt;

                        ChatMessage::where('wamid', $id)->update($upd);
                        Log::info('WA_STATUS_UPDATE', ['wamid' => $id, 'status' => $status]);
                    }
                } catch (\Throwable $e) {
                    Log::error('WA_STATUS_ERROR', [
                        'ex' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                }
            }
        }

        return response()->json(['ok' => true]);
    }

    /* =================== Helpers =================== */

    /** Crea/retorna Cliente con nombre, apellido y comentario “Creado desde WhatsApp” */
    private function ensureCliente(string $msisdn, array $value): Cliente
    {
        $fullName = trim((string) data_get($value, 'contacts.0.profile.name', ''));
        $nombre = 'Desconocido';
        $apellido = '-';

        if ($fullName !== '') {
            $parts = preg_split('/\s+/', $fullName, 2);
            $nombre = $parts[0] ?? 'Desconocido';
            $apellido = $parts[1] ?? '-';
        }

        $cliente = Cliente::firstOrCreate(
            ['telefono' => $msisdn],
            ['nombre' => $nombre, 'apellido' => $apellido, 'comentarios' => 'Creado desde WhatsApp']
        );

        if (empty($cliente->comentarios)) {
            $cliente->comentarios = 'Creado desde WhatsApp';
            $cliente->save();
        }

        return $cliente;
    }

    /** Decide si el bot debe responder: no responde si está en espera_asesor o con handover activo */
    private function shouldBotReply(ChatFlow $flow): bool
    {
        $ctx = $flow->context ?? [];
        if (!empty($ctx['handover'])) return false;                 // conversación en mano de asesor
        if (($flow->step ?? '') === 'espera_asesor') return false;  // usuario pidió asesor
        return true;
    }

    /** Marca paso/ctx del flujo */
    private function flowSet(ChatFlow $flow, string $step, array $mergeCtx = []): void
    {
        $ctx = $flow->context ?? [];
        if ($mergeCtx) $ctx = array_merge($ctx, $mergeCtx);
        $flow->step = $step;
        $flow->context = $ctx;
        $flow->save();
    }

    /** Guarda salientes del bot con hora en la zona del app (MX) */
    private function storeOut(int $clienteId, string $fromOur, string $toUser, string $type, string $text, string $appTz): void
    {
        ChatMessage::create([
            'wamid'        => uniqid('out_'),
            'cliente_id'   => $clienteId,
            'from'         => $fromOur,
            'to'           => $toUser,
            'direction'    => 'out',
            'type'         => $type,
            'text'         => $text,
            'wa_timestamp' => now($appTz),  // 👈 MX
            'status'       => 'sent',
        ]);
    }

    /**
     * Botones principales + botones de formulario + CSAT (interactivos)
     */
    private function handleInteractiveSelection(
        ChatFlow $flow,
        string $id,
        string $toUser,
        string $fromOur,
        WhatsAppService $wa,
        int $clienteId,
        string $appTz
    ): bool {
        $id = trim($id);

        // —— CSAT (desde lista/botones interactivos) —— //
        if (preg_match('/^csat_(\d)$/', $id, $m)) {
            $score = max(1, min(5, (int)$m[1]));
            $ctx   = $flow->context ?? [];
            $ctx['csat'] = [
                'pending' => false,
                'score'   => $score,
                'at'      => now($appTz)->toIso8601String(),
            ];
            // Reinicia al modo automático (menu)
            $this->flowSet($flow, 'menu', $ctx);

            $wa->sendText($toUser, "¡Gracias por tu calificación de {$score}/5! 💚\n\nSi necesitas algo más, escribe *hola* para ver el menú.");
            $this->storeOut($clienteId, $fromOur, $toUser, 'text', "Gracias por CSAT {$score}/5", $appTz);
            return true;
        }
        if ($id === 'csat_skip') {
            $ctx = $flow->context ?? [];
            $ctx['csat'] = [
                'pending' => false,
                'score'   => null,
                'at'      => now($appTz)->toIso8601String(),
            ];
            $this->flowSet($flow, 'menu', $ctx);

            $wa->sendText($toUser, "¡Gracias! Si necesitas algo más, escribe *hola* para ver el menú.");
            $this->storeOut($clienteId, $fromOur, $toUser, 'text', "CSAT omitido por el usuario", $appTz);
            return true;
        }

        // —— Botones del menú principal —— //
        if (in_array($id, ['cotizar','comprar','info','asesor'])) {
            if ($id === 'cotizar') {
                $this->flowSet($flow, 'cotizar_nombre', ['cotizar' => []]);
                $wa->sendInteractiveButtons(
                    $toUser,
                    "Perfecto, vamos a cotizar. ¿Cómo te llamas? (Nombre)",
                    [
                        ['id' => 'cancelar', 'title' => 'Cancelar'],
                    ],
                    'Puedes escribir tu nombre o cancelar.'
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Inicio cotización: pedir nombre.', $appTz);
                return true;
            }
            if ($id === 'comprar') {
                $this->flowSet($flow, 'comprar_equipo');
                $wa->sendText($toUser, "🛒 Excelente, ¿qué equipo deseas comprar?");
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir equipo a comprar.', $appTz);
                return true;
            }
            if ($id === 'info') {
                $this->flowSet($flow, 'info_equipo');
                $wa->sendText($toUser, "ℹ️ Indícame el equipo del que deseas más información.");
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir equipo para info.', $appTz);
                return true;
            }
            if ($id === 'asesor') {
                // El usuario pide humano → desactivamos bot dejando el flujo en espera_asesor
                $this->flowSet($flow, 'espera_asesor', ['handover' => true]); // 👈 marca handover
                $wa->sendText($toUser, "✅ En un momento un asesor te contactará.");
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Aviso espera asesor.', $appTz);
                return true;
            }
        }

        // —— Botones del formulario de cotización —— //
        if ($id === 'cancelar') {
            $this->flowSet($flow, 'menu');
            $wa->sendText($toUser, "❌ Proceso cancelado. Escribe *hola* para ver el menú.");
            $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Proceso cancelado.', $appTz);
            return true;
        }

        if ($id === 'omitir_email') {
            $ctx = $flow->context ?? ['cotizar' => []];
            $ctx['cotizar']['email'] = null;
            $this->flowSet($flow, 'cotizar_ubicacion', $ctx);
            $wa->sendInteractiveButtons(
                $toUser,
                "¿De dónde nos escribes? (Ciudad/Estado/País)",
                [['id' => 'cancelar', 'title' => 'Cancelar']],
                'Ejemplo: CDMX, México'
            );
            $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir ubicación (email omitido).', $appTz);
            return true;
        }

        if ($id === 'cotizar_confirmar') {
            $this->flowSet($flow, 'espera_asesor', ['handover' => true]); // 👈 listo para asesor
            $wa->sendText($toUser, "✅ ¡Gracias! Un asesor preparará tu cotización en PDF y te la enviará aquí.");
            $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Cotización confirmada.', $appTz);
            return true;
        }

        if ($id === 'cotizar_corregir') {
            $wa->sendInteractiveList(
                $toUser,
                "¿Qué dato deseas corregir?",
                "Elegir campo",
                [[
                    'title' => 'Campos',
                    'rows' => [
                        ['id' => 'fix_nombre',    'title' => 'Nombre'],
                        ['id' => 'fix_apellido',  'title' => 'Apellido'],
                        ['id' => 'fix_email',     'title' => 'Email'],
                        ['id' => 'fix_ubicacion', 'title' => 'Ciudad/Estado/País'],
                        ['id' => 'fix_equipo',    'title' => 'Equipo a cotizar'],
                    ],
                ]],
                'Selecciona uno'
            );
            $this->flowSet($flow, 'cotizar_corregir_campo');
            $this->storeOut($clienteId, $fromOur, $toUser, 'interactive', 'Lista de campos a corregir.', $appTz);
            return true;
        }

        if (in_array($id, ['fix_nombre','fix_apellido','fix_email','fix_ubicacion','fix_equipo'])) {
            $campo = substr($id, 4);
            $this->flowSet($flow, 'cotizar_corregir_valor', ['fix' => $campo]);
            $label = [
                'nombre' => 'tu *Nombre*',
                'apellido' => 'tu *Apellido*',
                'email' => 'tu *Email* (opcional, o escribe *omitir*)',
                'ubicacion' => 'tu *Ciudad/Estado/País*',
                'equipo' => 'el *equipo* a cotizar',
            ][$campo] ?? $campo;

            $wa->sendInteractiveButtons(
                $toUser,
                "Envíame {$label}.",
                [['id' => 'cancelar', 'title' => 'Cancelar']],
                'Corrigiendo dato'
            );
            $this->storeOut($clienteId, $fromOur, $toUser, 'text', "Solicitar nuevo valor para {$campo}.", $appTz);
            return true;
        }

        return false; // no manejado
    }

    /**
     * Flujo general + formulario de cotización (texto libre) + CSAT
     */
    private function formFlow(
        ChatFlow $flow,
        string $text,
        WhatsAppService $wa,
        string $toUser,
        string $fromOur,
        int $clienteId,
        string $appTz
    ): ?string {
        // atajos comunes
        if ($text === 'cancelar') {
            $this->flowSet($flow, 'menu', ['handover' => false]); // por si estaba marcado
            return "❌ Proceso cancelado. Escribe *hola* para ver el menú.";
        }

        switch ($flow->step ?? 'start') {
            case 'start':
                return "👋 Escribe *hola* para ver el menú.";

            case 'menu':
                if ($text === 'cotizar' || $text === '1') {
                    $this->flowSet($flow, 'cotizar_nombre', ['cotizar' => []]);
                    return "Perfecto, vamos a cotizar. ¿Cómo te llamas? (Nombre) — puedes enviar *cancelar* para abortar.";
                }
                if ($text === 'comprar' || $text === '2') {
                    $this->flowSet($flow, 'comprar_equipo');
                    return "🛒 ¿Qué equipo deseas comprar?";
                }
                if ($text === 'info' || $text === '3' || str_contains($text, 'informacion') || str_contains($text, 'información')) {
                    $this->flowSet($flow, 'info_equipo');
                    return "ℹ️ Indícame el equipo del que deseas más información.";
                }
                if ($text === 'asesor' || $text === '4') {
                    // El usuario pide humano → bloquea bot
                    $this->flowSet($flow, 'espera_asesor', ['handover' => true]);
                    return "✅ En breve te contactará un asesor.";
                }
                return "Para comenzar, escribe *hola* y elige una opción.";

            /* ====== Formulario Cotización ====== */
            case 'cotizar_nombre':
                $ctx = $flow->context ?? ['cotizar' => []];
                $ctx['cotizar']['nombre'] = $this->ucfirstWords($text);
                $this->flowSet($flow, 'cotizar_apellido', $ctx);

                $wa->sendInteractiveButtons(
                    $toUser,
                    "Gracias, {$ctx['cotizar']['nombre']}. ¿Cuál es tu *Apellido*?",
                    [['id' => 'cancelar', 'title' => 'Cancelar']]
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir apellido.', $appTz);
                return null;

            case 'cotizar_apellido':
                $ctx = $flow->context ?? ['cotizar' => []];
                $ctx['cotizar']['apellido'] = $this->ucfirstWords($text);
                $this->flowSet($flow, 'cotizar_email', $ctx);

                $wa->sendInteractiveButtons(
                    $toUser,
                    "¿Tu *email*? (opcional). Puedes escribirlo o tocar *Omitir*.",
                    [
                        ['id' => 'omitir_email', 'title' => 'Omitir'],
                        ['id' => 'cancelar', 'title' => 'Cancelar'],
                    ],
                    'Ejemplo: nombre@dominio.com'
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir email.', $appTz);
                return null;

            case 'cotizar_email':
                $ctx = $flow->context ?? ['cotizar' => []];
                if ($text !== 'omitir' && !$this->isValidEmail($text)) {
                    return "✉️ El email no parece válido. Envíalo de nuevo o escribe *omitir*.";
                }
                $ctx['cotizar']['email'] = ($text === 'omitir') ? null : $text;
                $this->flowSet($flow, 'cotizar_ubicacion', $ctx);

                $wa->sendInteractiveButtons(
                    $toUser,
                    "¿De dónde nos escribes? (Ciudad/Estado/País)",
                    [['id' => 'cancelar', 'title' => 'Cancelar']],
                    'Ejemplo: CDMX, México'
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir ubicación.', $appTz);
                return null;

            case 'cotizar_ubicacion':
                $ctx = $flow->context ?? ['cotizar' => []];
                $ctx['cotizar']['ubicacion'] = $this->ucfirstWords($text);
                $this->flowSet($flow, 'cotizar_equipo', $ctx);

                $wa->sendInteractiveButtons(
                    $toUser,
                    "¿Qué *equipo* deseas cotizar? (ej. Torre de Laparoscopía)",
                    [['id' => 'cancelar', 'title' => 'Cancelar']]
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'text', 'Pedir equipo a cotizar.', $appTz);
                return null;

            case 'cotizar_equipo':
                $ctx = $flow->context ?? ['cotizar' => []];
                $ctx['cotizar']['equipo'] = $text;
                $this->flowSet($flow, 'cotizar_resumen', $ctx);

                $resumen = $this->quoteSummary($ctx['cotizar']);
                $wa->sendInteractiveButtons(
                    $toUser,
                    "Por favor confirma tu solicitud:\n\n{$resumen}",
                    [
                        ['id' => 'cotizar_confirmar', 'title' => 'Confirmar'],
                        ['id' => 'cotizar_corregir',  'title' => 'Corregir'],
                        ['id' => 'cancelar',          'title' => 'Cancelar'],
                    ],
                    'Revisa tus datos'
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'interactive', 'Resumen de cotización.', $appTz);
                return null;

            case 'cotizar_resumen':
                $ctx = $flow->context ?? ['cotizar' => []];
                $resumen = $this->quoteSummary($ctx['cotizar'] ?? []);
                $wa->sendInteractiveButtons(
                    $toUser,
                    "Revisa y elige una acción:\n\n{$resumen}",
                    [
                        ['id' => 'cotizar_confirmar', 'title' => 'Confirmar'],
                        ['id' => 'cotizar_corregir',  'title' => 'Corregir'],
                        ['id' => 'cancelar',          'title' => 'Cancelar'],
                    ],
                    'Revisa tus datos'
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'interactive', 'Repetir resumen.', $appTz);
                return null;

            case 'cotizar_corregir_campo':
                return "Elige un campo a corregir en la lista.";

            case 'cotizar_corregir_valor':
                $ctx = $flow->context ?? ['cotizar' => []];
                $campo = $ctx['fix'] ?? null;
                if (!$campo) {
                    $this->flowSet($flow, 'cotizar_resumen', $ctx);
                    return "No identifiqué el campo a corregir. Volvamos al resumen.";
                }

                if ($campo === 'email') {
                    if ($text !== 'omitir' && !$this->isValidEmail($text)) {
                        return "✉️ El email no parece válido. Envíalo de nuevo o escribe *omitir*.";
                    }
                    $ctx['cotizar']['email'] = ($text === 'omitir') ? null : $text;
                } elseif ($campo === 'nombre') {
                    $ctx['cotizar']['nombre'] = $this->ucfirstWords($text);
                } elseif ($campo === 'apellido') {
                    $ctx['cotizar']['apellido'] = $this->ucfirstWords($text);
                } elseif ($campo === 'ubicacion') {
                    $ctx['cotizar']['ubicacion'] = $this->ucfirstWords($text);
                } elseif ($campo === 'equipo') {
                    $ctx['cotizar']['equipo'] = $text;
                }

                unset($ctx['fix']);
                $this->flowSet($flow, 'cotizar_resumen', $ctx);

                $resumen = $this->quoteSummary($ctx['cotizar']);
                $wa->sendInteractiveButtons(
                    $toUser,
                    "Actualicé el dato. Revisa tu solicitud:\n\n{$resumen}",
                    [
                        ['id' => 'cotizar_confirmar', 'title' => 'Confirmar'],
                        ['id' => 'cotizar_corregir',  'title' => 'Corregir'],
                        ['id' => 'cancelar',          'title' => 'Cancelar'],
                    ]
                );
                $this->storeOut($clienteId, $fromOur, $toUser, 'interactive', 'Resumen actualizado.', $appTz);
                return null;

            /* ====== Otros flujos ====== */
            case 'comprar_equipo':
                $this->flowSet($flow, 'cotizacion_compra', ['equipo_comprar' => $text]);
                return "¿Ya cuentas con una cotización de este equipo? (responde sí o no)";

            case 'cotizacion_compra':
                if ($text === 'si' || $text === 'sí') {
                    $this->flowSet($flow, 'folio_compra');
                    return "Por favor indícame el *folio o número de la cotización*.";
                }
                if ($text === 'no') {
                    $this->flowSet($flow, 'espera_asesor', ['handover' => true]); // asesor se encargará
                    return "✅ Perfecto, un asesor te ayudará a generar la cotización antes de la compra.";
                }
                return "Responde *sí* o *no*, por favor.";

            case 'folio_compra':
                $this->flowSet($flow, 'espera_asesor', ['folio' => $text, 'handover' => true]);
                return "📑 Gracias. Un asesor validará tu folio y te apoyará con el proceso de compra.";

            case 'info_equipo':
                $this->flowSet($flow, 'espera_asesor', ['equipo_info' => $text, 'handover' => true]);
                return "📘 Gracias. Un asesor te compartirá la información detallada del equipo.";

            case 'espera_asesor':
                // Ya está en mano de asesor → no contestamos más
                return null;

            /* ====== CSAT en espera (texto libre) ====== */
            case 'csat_wait':
                // Acepta 1..5 u 'omitir' por texto
                if (in_array($text, ['1','2','3','4','5'])) {
                    $score = (int)$text;
                    $ctx = $flow->context ?? [];
                    $ctx['csat'] = [
                        'pending' => false,
                        'score'   => $score,
                        'at'      => now($appTz)->toIso8601String(),
                    ];
                    $this->flowSet($flow, 'menu', $ctx);
                    return "¡Gracias por tu calificación de {$score}/5! 💚\n\nEscribe *hola* para ver el menú.";
                }
                if (in_array($text, ['omitir','omit','skip','excelente','buena','mala'])) {
                    // Permite responder con palabras
                    $score = null;
                    if ($text === 'excelente') $score = 5;
                    elseif ($text === 'buena') $score = 3;
                    elseif ($text === 'mala') $score = 1;
                    $ctx = $flow->context ?? [];
                    $ctx['csat'] = [
                        'pending' => false,
                        'score'   => $score,
                        'at'      => now($appTz)->toIso8601String(),
                    ];
                    $this->flowSet($flow, 'menu', $ctx);
                    return $score === null
                        ? "¡Gracias! Escribe *hola* para ver el menú."
                        : "¡Gracias por tu calificación de {$score}/5! 💚\n\nEscribe *hola* para ver el menú.";
                }
                // Si manda otra cosa, reenvía instrucción
                return "Por favor responde con un número del *1* al *5*, o escribe *omitir*.";
        }

        return "🤖 No entendí tu mensaje. Escribe *hola* para ver el menú.";
    }

    /** Helpers de presentación */
    private function ucfirstWords(string $s): string
    {
        $s = mb_strtolower(trim($s));
        return mb_convert_case($s, MB_CASE_TITLE, "UTF-8");
    }

    private function isValidEmail(string $s): bool
    {
        return filter_var($s, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function quoteSummary(array $d): string
    {
        $nombre    = $d['nombre']    ?? '-';
        $apellido  = $d['apellido']  ?? '-';
        $email     = $d['email']     ?? 'No proporcionado';
        $ubicacion = $d['ubicacion'] ?? '-';
        $equipo    = $d['equipo']    ?? '-';

        return "👤 *Nombre:* {$nombre} {$apellido}\n".
               "✉️ *Email:* {$email}\n".
               "📍 *Ubicación:* {$ubicacion}\n".
               "🧰 *Equipo:* {$equipo}";
    }

    /* ============================================================
     * Reclamar una conversación por un agente (handover ON)
     * ============================================================ */
    public function claimByAgent(Request $request, string $msisdn)
    {
        $agentId   = $request->integer('agent_id');
        $agentName = trim((string)$request->input('agent_name'));
        $flow = ChatFlow::firstOrCreate(['from' => WhatsAppService::normalizeMsisdn($msisdn)], ['step' => 'start']);

        $ctx = $flow->context ?? [];
        $ctx['handover'] = true;
        if ($agentId)   $ctx['agent']['id']   = $agentId;
        if ($agentName) $ctx['agent']['name'] = $agentName;

        $flow->step    = 'espera_asesor';
        $flow->context = $ctx;
        $flow->save();

        return response()->json(['ok' => true, 'flow' => $flow]);
    }

    /* ============================================================
     * Cerrar conversación (handover OFF) + enviar CSAT (plantilla) + reiniciar
     * ============================================================ */
    public function closeByAgent(Request $request, string $msisdn)
    {
        $appTz = config('app.timezone', 'UTC');
        $wa    = app(WhatsAppService::class);

        // Normaliza número y carga/crea flujo
        $toUser = WhatsAppService::normalizeMsisdn($msisdn);
        $flow   = ChatFlow::firstOrCreate(['from' => $toUser], ['step' => 'start']);
        $ctx    = is_array($flow->context) ? $flow->context : [];

        // Apaga handover y deja estado de csat_wait (el bot vuelve a responder)
        $ctx['handover'] = false;
        $ctx['csat'] = ['pending' => true];

        $flow->step    = 'csat_wait';
        $flow->context = $ctx;
        $flow->save();

        // Datos para variables de la PLANTILLA csat_soporte_postchat
        $cliente = Cliente::where('telefono', $toUser)->first();
        $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: 'Cliente';
        $agentNameReq  = trim((string)$request->input('agent_name'));
        $agentNameCtx  = (string) data_get($ctx, 'agent.name', '');
        $asesorNombre  = $agentNameReq !== '' ? $agentNameReq : ($agentNameCtx !== '' ? $agentNameCtx : 'nuestro equipo');

        // Enviar plantilla de CSAT (Quick Reply buttons)
        $lang = $wa->pickTemplateLanguage('csat_soporte_postchat') ?? 'es_MX';
        $res  = $wa->sendTemplateText($toUser, 'csat_soporte_postchat', $lang, [$clienteNombre, $asesorNombre]);

        Log::info('WA_CSAT_TEMPLATE_SENT', ['to'=>$toUser, 'lang'=>$lang, 'resp'=>$res->json()]);

        // Registrar el mensaje saliente en DB (marcamos como texto informativo)
        $our   = (string) config('services.whatsapp.phone_e164');
        $cid   = optional($cliente)->id;
        if ($cid) {
            $this->storeOut($cid, preg_replace('/\D+/', '', $our), $toUser, 'text', 'Solicitud de CSAT (plantilla)', $appTz);
        }

        return response()->json(['ok' => true]);
    }
}
