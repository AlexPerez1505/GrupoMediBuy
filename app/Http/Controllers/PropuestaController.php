<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Propuesta;
use App\Models\PagoFinanciamientoPropuesta;
use App\Models\FichaTecnica;
use PDF;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;

class PropuestaController extends Controller
{
    /* ========================= CRUD BÁSICO ========================= */

    public function encontrarClientes(Request $request)
    {
        $search = (string) $request->input('search', '');

        $clientes = Cliente::query();

        if ($search !== '') {
            $clientes->where(function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('apellido', 'LIKE', "%{$search}%");
            });
        }

        return response()->json(
            $clientes->get(['id', 'nombre', 'apellido', 'telefono', 'email', 'comentarios'])
        );
    }

    public function create()
    {
        $productos = Producto::all();
        $fichas    = FichaTecnica::all();

        return view('propuesta.create', compact('productos', 'fichas'));
    }

    public function store(Request $request)
    {
        \Log::info('Inicio método store - request recibido', $request->all());

        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'subtotal'          => 'required|numeric',
            'total'             => 'required|numeric',
            'productos_json'    => 'required|json',
            'pagos_json'        => 'nullable|json',
            'ficha_tecnica_id'  => 'nullable|exists:fichas_tecnicas,id',
            'lugar'             => 'required|string',
        ]);

        try {
            $propuesta = Propuesta::create([
                'cliente_id'       => $request->cliente_id,
                'lugar'            => $request->lugar,
                'nota'             => $request->nota,
                'user_id'          => auth()->id(),
                'subtotal'         => $request->subtotal,
                'descuento'        => $request->descuento ?? 0,
                'envio'            => $request->envio ?? 0,
                'iva'              => $request->iva ?? 0,
                'total'            => $request->total,
                'plan'             => $request->plan,
                'ficha_tecnica_id' => $request->ficha_tecnica_id,
            ]);

            \Log::info('Propuesta creada', ['id' => $propuesta->id]);

            $productos = json_decode($request->productos_json, true) ?: [];
            \Log::info('Productos decodificados', ['productos' => $productos]);

            foreach ($productos as $p) {
                $propuesta->productos()->create([
                    'producto_id'     => $p['producto_id'],
                    'cantidad'        => $p['cantidad'],
                    'precio_unitario' => $p['precio_unitario'],
                    'subtotal'        => $p['subtotal'],
                    'sobreprecio'     => $p['sobreprecio'] ?? 0,
                ]);
            }

            if ($request->filled('pagos_json')) {
                $pagos = json_decode($request->pagos_json, true);
                \Log::info('Pagos decodificados', ['pagos' => $pagos]);

                if (is_array($pagos)) {
                    foreach ($pagos as $pago) {
                        PagoFinanciamientoPropuesta::create([
                            'propuesta_id' => $propuesta->id,
                            'descripcion'  => $pago['descripcion'] ?? '',
                            'fecha_pago'   => Carbon::parse($pago['mes']),
                            'monto'        => $pago['cuota'] ?? 0,
                        ]);
                    }
                }
            }

            \Log::info('Propuesta guardada', ['propuesta_id' => $propuesta->id]);

            return redirect()
                ->route('propuestas.show', $propuesta->id)
                ->with('success', 'Propuesta guardada exitosamente.');
        } catch (\Throwable $e) {
            \Log::error('Error guardando propuesta', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return back()->withErrors('Ocurrió un error al guardar la propuesta.');
        }
    }

    public function index()
    {
        $propuestas = Propuesta::with(['cliente', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('propuesta.index', compact('propuestas'));
    }

    public function pdf(Propuesta $propuesta)
    {
        $propuesta->load(['cliente', 'usuario', 'productos.producto', 'fichaTecnica']);

        $url = route('propuestas.show', $propuesta->id);

        $qr = base64_encode(
            \QrCode::format('svg')
                ->size(120)
                ->generate($url)
        );

        $pdfPropuesta = PDF::loadView('propuesta.pdf', compact('propuesta', 'qr'))
            ->setPaper('a4', 'portrait');

        // Asegurar carpeta temp
        $dirTemp = storage_path("app/public/temp");
        if (!is_dir($dirTemp)) {
            @mkdir($dirTemp, 0775, true);
        }

        $rutaPropuesta = "{$dirTemp}/propuesta_{$propuesta->id}.pdf";
        file_put_contents($rutaPropuesta, $pdfPropuesta->output());

        $rutaFicha = $propuesta->fichaTecnica?->archivo
            ? storage_path("app/public/" . $propuesta->fichaTecnica->archivo)
            : null;

        $pdf = new Fpdi();
        $archivos = [$rutaPropuesta];

        if ($rutaFicha && file_exists($rutaFicha)) {
            $archivos[] = $rutaFicha;
        }

        foreach ($archivos as $archivo) {
            $pageCount = $pdf->setSourceFile($archivo);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        }

        $rutaFinal = "{$dirTemp}/final_propuesta_{\n$propuesta->id}.pdf";
        // small fix: avoid newline by using braces properly
        $rutaFinal = "{$dirTemp}/final_propuesta_{$propuesta->id}.pdf";
        $pdf->Output($rutaFinal, 'F');

        $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', $propuesta->cliente->nombre);
        $nombreArchivo = "Cotización_{$propuesta->id}_{$clienteNombre}.pdf";

        return response()->download($rutaFinal, $nombreArchivo);
    }

    public function show(Propuesta $propuesta)
    {
        $propuesta->load(['cliente', 'productos.producto', 'usuario', 'fichaTecnica']);

        // Gráfico de tipo de equipo
        $agrupados = $propuesta->productos->groupBy(function ($item) {
            return optional($item->producto)->tipo_equipo ?? 'Sin tipo';
        });

        $tiposEquipo = $agrupados->keys()->values()->all();
        $cantidades  = $agrupados->map->count()->values()->all();

        // Gráfico de productos por subtotal (más caro a más barato)
        $productosOrdenados = $propuesta->productos
            ->sortByDesc(function ($item) {
                return (float) ($item->subtotal ?? 0);
            })
            ->map(function ($item) {
                $producto = $item->producto;
                return [
                    'nombre'   => $producto ? ($producto->tipo_equipo . ' ' . $producto->modelo) : 'Producto eliminado',
                    'subtotal' => (float) ($item->subtotal ?? 0),
                ];
            });

        $labels  = $productosOrdenados->pluck('nombre')->values();
        $valores = $productosOrdenados->pluck('subtotal')->values();

        return view('propuesta.show', compact('propuesta', 'tiposEquipo', 'cantidades', 'labels', 'valores'));
    }

    public function edit($id)
    {
        $propuesta = Propuesta::with(['productos.producto', 'cliente', 'pagosFinanciamiento', 'fichaTecnica'])
            ->findOrFail($id);
        $productos = Producto::all();
        $fichas    = FichaTecnica::all();
        $clientes  = Cliente::all();

        return view('propuesta.edit', compact('propuesta', 'productos', 'fichas', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        \Log::info('Inicio método update - request recibido', $request->all());
        \Log::info('Valor de productos_json', ['productos_json' => $request->productos_json]);

        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'subtotal'          => 'required|numeric',
            'total'             => 'required|numeric',
            'productos_json'    => 'required|json',
            'pagos_json'        => 'nullable|json',
            'ficha_tecnica_id'  => 'nullable|exists:fichas_tecnicas,id',
            'lugar'             => 'required|string',
        ]);

        try {
            $propuesta = Propuesta::findOrFail($id);

            $propuesta->update([
                'cliente_id'       => $request->cliente_id,
                'lugar'            => $request->lugar,
                'nota'             => $request->nota,
                'subtotal'         => $request->subtotal,
                'descuento'        => $request->descuento ?? 0,
                'envio'            => $request->envio ?? 0,
                'iva'              => $request->iva ?? 0,
                'total'            => $request->total,
                'plan'             => $request->plan,
                'ficha_tecnica_id' => $request->ficha_tecnica_id,
            ]);

            \Log::info('Propuesta actualizada', ['id' => $propuesta->id]);

            // Actualizar productos
            $propuesta->productos()->delete();
            $productos = json_decode($request->productos_json, true) ?: [];
            \Log::info('Productos decodificados', ['productos' => $productos]);

            foreach ($productos as $p) {
                $propuesta->productos()->create([
                    'producto_id'     => $p['producto_id'],
                    'cantidad'        => $p['cantidad'],
                    'precio_unitario' => $p['precio_unitario'],
                    'subtotal'        => $p['subtotal'],
                    'sobreprecio'     => $p['sobreprecio'] ?? 0,
                ]);
            }

            // Actualizar pagos
            $idsConservados = [];

            if ($request->filled('pagos_json')) {
                $pagos = json_decode($request->pagos_json, true);
                \Log::info('Pagos decodificados', ['pagos' => $pagos]);

                if (is_array($pagos)) {
                    foreach ($pagos as $pago) {
                        \Log::info('Procesando pago', $pago);

                        if (!empty($pago['id'])) {
                            $pagoExistente = PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                                ->where('id', $pago['id'])
                                ->first();

                            if ($pagoExistente) {
                                $pagoExistente->update([
                                    'descripcion' => $pago['descripcion'] ?? '',
                                    'fecha_pago'  => Carbon::parse($pago['mes']),
                                    'monto'       => $pago['cuota'] ?? 0,
                                ]);
                                $idsConservados[] = $pagoExistente->id;
                            }
                        } else {
                            $nuevoPago = PagoFinanciamientoPropuesta::create([
                                'propuesta_id' => $propuesta->id,
                                'descripcion'  => $pago['descripcion'] ?? '',
                                'fecha_pago'   => Carbon::parse($pago['mes']),
                                'monto'        => $pago['cuota'] ?? 0,
                            ]);
                            $idsConservados[] = $nuevoPago->id;
                        }
                    }
                } else {
                    \Log::warning('El campo pagos_json no es un array válido', ['pagos_json' => $request->pagos_json]);
                }
            } else {
                \Log::info('Sin cambios en pagos (pagos_json vacío)');
            }

            // Eliminar pagos que no fueron conservados
            PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                ->whereNotIn('id', $idsConservados)
                ->delete();

            \Log::info('Pagos actualizados correctamente');

            return redirect()
                ->route('propuestas.show', $propuesta->id)
                ->with('success', 'Propuesta actualizada exitosamente.');
        } catch (\Throwable $e) {
            \Log::error('Error actualizando propuesta', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return back()->withErrors('Ocurrió un error al actualizar la propuesta.');
        }
    }

    /* ======================= PDF PARA WHATSAPP ======================= */

    /**
     * Renderiza + concatena la propuesta y la ficha técnica (si existe)
     * en un PDF final y devuelve [ruta absoluta, nombre de archivo].
     */
    private function buildPropuestaPdf(Propuesta $propuesta): array
    {
        $propuesta->load(['cliente', 'usuario', 'productos.producto', 'fichaTecnica']);

        $url = route('propuestas.show', $propuesta->id);
        $qr  = base64_encode(\QrCode::format('svg')->size(120)->generate($url));

        $pdfPropuesta = \PDF::loadView('propuesta.pdf', compact('propuesta', 'qr'))
            ->setPaper('a4', 'portrait');

        $dirTemp = storage_path('app/public/temp');
        if (!is_dir($dirTemp)) {
            @mkdir($dirTemp, 0775, true);
        }

        $rutaPropuesta = $dirTemp . "/propuesta_{$propuesta->id}.pdf";
        file_put_contents($rutaPropuesta, $pdfPropuesta->output());

        $rutaFicha = $propuesta->fichaTecnica?->archivo
            ? storage_path('app/public/' . $propuesta->fichaTecnica->archivo)
            : null;

        $pdf = new Fpdi();
        $archivos = [$rutaPropuesta];
        if ($rutaFicha && file_exists($rutaFicha)) {
            $archivos[] = $rutaFicha;
        }

        foreach ($archivos as $archivo) {
            $pageCount = $pdf->setSourceFile($archivo);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        }

        $rutaFinal = $dirTemp . "/final_propuesta_{$propuesta->id}.pdf";
        $pdf->Output($rutaFinal, 'F');

        $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', $propuesta->cliente->nombre);
        $filename = "Cotizacion_{$propuesta->id}_{$clienteNombre}.pdf";

        return [$rutaFinal, $filename];
    }

    /* ================== ENVÍO POR WHATSAPP · PLANTILLA ================= */

    /**
     * Envía por plantilla de WhatsApp con HEADER documento (PDF) y BODY flexible.
     * Nota: No forzamos valores por defecto en BODY/URL; si llegan vacíos no se envían
     * para evitar el error 132000 cuando la plantilla tiene menos variables.
     */
    public function sendWhatsappTemplateRemision(Propuesta $propuesta, Request $request, WhatsAppService $wa)
    {
        $request->validate([
            'frase'         => ['nullable', 'string', 'max:200'],
            'site_suffix'   => ['nullable', 'string', 'max:200'],
            'template_name' => ['required', 'string', 'max:128'], // p. ej. doc_pdf_utility_v1
            'template_lang' => ['required', 'string', 'max:12'],  // p. ej. es_MX
        ]);

        $propuesta->load('cliente');

        $to = $wa::normalizeMsisdn((string) ($propuesta->cliente->telefono ?? ''));
        if (!$to) {
            return back()->with('wa_info', 'El cliente no tiene teléfono válido.');
        }

        // 1) Generar PDF final
        try {
            [$path, $filename] = $this->buildPropuestaPdf($propuesta);
        } catch (\Throwable $e) {
            \Log::error('PDF build fail', ['e' => $e->getMessage()]);
            return back()->with('wa_info', 'No se pudo generar el PDF.');
        }

        // 2) Subir a /media
        $upload  = $wa->uploadMediaPath($path, $filename, 'application/pdf');
        $uJson   = $upload->json();
        $mediaId = data_get($uJson, 'id');

        if (!$upload->successful() || !$mediaId) {
            \Log::warning('WA_MEDIA_UPLOAD_FAIL', ['resp' => $uJson]);
            return back()
                ->with('wa_info', 'No se pudo subir el PDF a WhatsApp.')
                ->with('wa_fail', [$uJson]);
        }

        // 3) Enviar plantilla (parámetros dinámicos, sin defaults forzados)
        $templateName  = (string) $request->input('template_name'); // ej. doc_pdf_utility_v1
        $langCode      = (string) $request->input('template_lang'); // ej. es_MX

        $clienteNombre = trim((string) $propuesta->cliente->nombre . ' ' . (string) ($propuesta->cliente->apellido ?? ''));

        // Si vienen vacíos en el form, NO se envían (evita 132000 si la plantilla tiene sólo {{1}})
        $fraseInput = trim((string) $request->input('frase', ''));
        $frase      = ($fraseInput === '') ? null : $fraseInput;

        $siteInput  = trim((string) $request->input('site_suffix', ''));
        $siteSuffix = ($siteInput === '') ? null : $siteInput; // solo si la plantilla tiene botón URL dinámico

        $resp  = $wa->sendTemplateWithDocument(
            to: $to,
            templateName: $templateName,
            langCode: $langCode,
            mediaId: $mediaId,
            filename: $filename,
            clienteNombre: $clienteNombre, // {{1}}
            frase: $frase,                 // {{2}} (solo si no es null)
            btn0UrlSuffix: $siteSuffix,    // botón URL index 0 (solo si no es null)
            btn1UrlSuffix: null
        );

        $json  = $resp->json();
        $wamid = data_get($json, 'messages.0.id');

        if ($resp->successful() && $wamid) {
            \Log::info('WA_OK_TEMPLATE_DOC', [
                'to'    => $to,
                'wamid' => $wamid,
                'tpl'   => $templateName,
                'lang'  => $langCode
            ]);

            // (Opcional) Registrar seguimiento
            try {
                if (method_exists($propuesta, 'seguimientos')) {
                    $propuesta->seguimientos()->create([
                        'descripcion' => "WA enviado (tpl: {$templateName}, lang: {$langCode}, wamid: {$wamid})",
                        'user_id'     => auth()->id(),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('WA_SEGUIMIENTO_SAVE_FAIL', ['e' => $e->getMessage()]);
            }

            return back()->with('wa_success', "Plantilla enviada por WhatsApp ✅ ({$templateName} · {$langCode})");
        }

        // Si error (132000/132001/etc), devolvemos info + sugerencias
        $code   = data_get($json, 'error.code');
        $detail = data_get($json, 'error.message') ?: data_get($json, 'error.error_data.details');

        $suggestions = $wa->groupTemplatesByName($wa->fetchTemplatesSmart(200));

        \Log::warning('WA_FAIL_TEMPLATE_DOC', [
            'to'   => $to,
            'resp' => $json,
            'tpl'  => $templateName,
            'lang' => $langCode
        ]);

        return back()
            ->with('wa_info', "No se pudo enviar la plantilla. Verifica nombre/idioma tal cual existen en tu WABA.")
            ->with('wa_fail', [[
                'n'               => $to,
                'code'            => $code,
                'detail'          => $detail,
                'raw'             => $json,
                'template_tried'  => $templateName,
                'lang_tried'      => $langCode,
            ]])
            ->with('wa_templates_grouped', $suggestions);
    }

    /* =========== API para UI (llenar select con plantillas) =========== */

    public function whatsappTemplates(WhatsAppService $wa)
    {
        $tpls  = $wa->fetchTemplatesSmart(200);    // combinadas (WABA + PHONE_ID)
        $group = $wa->groupTemplatesByName($tpls); // name => [langs...]

        return response()->json([
            'ok'        => true,
            'count'     => count($tpls),
            'grouped'   => $group,
            'templates' => $tpls, // lista plana (debug)
        ]);
    }

    /* (Opcional) Debug: confirma a qué WABA pertenece tu phone_id */
    public function whatsappDebug(WhatsAppService $wa)
    {
        return response()->json([
            'phone_meta'       => $wa->getPhoneNumberMeta(),
            'templates_sample' => array_slice($wa->fetchTemplatesSmart(50), 0, 10),
            'note'             => 'Confirma que whatsapp_business_account.id == WHATSAPP_BUSINESS_ACCOUNT_ID en tu .env',
        ]);
    }
}
