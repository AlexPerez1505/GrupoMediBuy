<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Registro;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Throwable;

class PrestamoController extends Controller
{
    /* ===================== Helpers de logging, firma y normalización ===================== */

    private function normalizeSerie(string $s): string
    {
        $s = trim($s);
        $s = preg_replace('/^\s*(s\/?n|sn|serie|serial)\s*[:#\-]?\s*/i', '', $s) ?? $s;
        $s = preg_replace('/[\/\\\\_.\s]+/u', '-', $s) ?? $s;
        $s = preg_replace('/-+/', '-', $s) ?? $s;
        $s = trim($s, '-');
        return $s;
    }

    private function normalizeSerieArray(array $seriales): array
    {
        $norm = [];
        foreach ($seriales as $raw) {
            $n = $this->normalizeSerie((string) $raw);
            if ($n !== '') $norm[$n] = true;
        }
        return array_keys($norm);
    }

    private function ctx(Request $r, string $rid): array
    {
        return [
            'rid'           => $rid,
            'user_id'       => optional(Auth::user())->id,
            'user_name'     => optional(Auth::user())->name,
            'ip'            => $r->ip(),
            'ua'            => substr((string)$r->userAgent(), 0, 200),
            'method'        => $r->method(),
            'path'          => $r->path(),
            'content_length'=> (int) $r->server('CONTENT_LENGTH', 0),
            'referer'       => (string) $r->headers->get('referer'),
        ];
    }

    private function safeLen(?string $s): int
    {
        return $s === null ? 0 : strlen($s);
    }

    /**
     * Guarda la firma y devuelve la URL pública.
     * 1) Intenta base64 (firmaDigital). Si viene vacío/incorrecto, 2) reconstruye desde JSON de trazos (firmaJson) con GD.
     */
    private function storeSignatureOrFail(?string $firmaBase64, ?string $firmaJson, array $logCtx): string
    {
        // 1) Intento base64 si parece no-vacío (data:, suele tener len 6-10)
        $raw = (string) $firmaBase64;
        if ($raw !== '' && strlen($raw) > 10) {
            $rawNoPrefix = preg_replace('#^data:image/[^;]+;base64,#i', '', $raw) ?? $raw;
            $decoded = base64_decode($rawNoPrefix, true);
            if ($decoded !== false && strlen($decoded) > 0) {
                $name = 'firma_'.Str::uuid().'.png';
                Storage::disk('public')->put('firmas/'.$name, $decoded);
                $url = Storage::disk('public')->url('firmas/'.$name);
                Log::info('[firma] guardada desde base64', $logCtx + ['bytes' => strlen($decoded)]);
                return $url;
            }
            Log::warning('[firma] base64 inválido, intento fallback JSON', $logCtx);
        } else {
            Log::warning('[firma] base64 ausente/corto, intento fallback JSON', $logCtx + ['firma_len' => strlen($raw)]);
        }

        // 2) Fallback: JSON de trazos
        if ($firmaJson) {
            $data = json_decode($firmaJson, true);
            if (json_last_error() !== JSON_ERROR_NONE || empty($data['lines']) || !is_array($data['lines'])) {
                Log::warning('[firma] JSON inválido', $logCtx + ['json_error' => json_last_error_msg()]);
                throw ValidationException::withMessages(['firmaDigital' => 'Firma inválida.']);
            }

            $w = max(300, (int)($data['w'] ?? 600));
            $h = max(120, (int)($data['h'] ?? 160));
            $scale = 2; // retina

            if (!function_exists('imagecreatetruecolor')) {
                Log::error('[firma] GD no disponible para fallback JSON', $logCtx);
                throw ValidationException::withMessages(['firmaDigital' => 'No se pudo generar la firma (GD).']);
            }

            $im = imagecreatetruecolor($w * $scale, $h * $scale);
            if (!$im) {
                Log::error('[firma] no pudo crear canvas GD', $logCtx);
                throw ValidationException::withMessages(['firmaDigital' => 'No se pudo generar la firma.']);
            }

            $white = imagecolorallocate($im, 255, 255, 255);
            imagefilledrectangle($im, 0, 0, $w * $scale, $h * $scale, $white);
            if (function_exists('imageantialias')) { @imageantialias($im, true); }
            $black = imagecolorallocate($im, 42, 46, 53);
            imagesetthickness($im, max(2, (int) round(2 * $scale)));

            foreach ($data['lines'] as $line) {
                if (!is_array($line) || count($line) < 2) continue;
                for ($i=1; $i < count($line); $i++) {
                    $x1 = (float)($line[$i-1]['x'] ?? 0) * $scale;
                    $y1 = (float)($line[$i-1]['y'] ?? 0) * $scale;
                    $x2 = (float)($line[$i]['x']   ?? 0) * $scale;
                    $y2 = (float)($line[$i]['y']   ?? 0) * $scale;
                    imageline($im, (int) round($x1), (int) round($y1), (int) round($x2), (int) round($y2), $black);
                }
            }

            ob_start();
            imagepng($im, null, 3);
            $png = ob_get_clean();
            imagedestroy($im);

            if (!$png) {
                Log::error('[firma] fallo imagepng()', $logCtx);
                throw ValidationException::withMessages(['firmaDigital' => 'No se pudo generar la firma.']);
            }

            $name = 'firma_'.Str::uuid().'.png';
            Storage::disk('public')->put('firmas/'.$name, $png);
            $url = Storage::disk('public')->url('firmas/'.$name);
            Log::info('[firma] guardada desde JSON trazos', $logCtx + ['png_bytes' => strlen($png), 'w' => $w, 'h' => $h, 'scale' => $scale]);
            return $url;
        }

        // 3) Sin base64 válido ni JSON -> fallo
        Log::warning('[firma] sin base64 ni JSON', $logCtx);
        throw ValidationException::withMessages(['firmaDigital' => 'La firma no es válida.']);
    }

    /* ===================== CRUD ===================== */

    public function index()
    {
        $prestamos = Prestamo::with(['registros', 'cliente'])->latest()->get();
        return view('prestamos.index', compact('prestamos'));
    }

    public function create()
    {
        return view('prestamos.wizard');
    }

    public function store(Request $request)
    {
        $rid = (string) Str::uuid();
        $t0  = microtime(true);
        $ctx = $this->ctx($request, $rid);

        $serialesInput = (array) $request->input('seriales', []);
        $firmaRaw      = $request->input('firmaDigital');
        $firmaJson     = $request->input('firmaJson');

        Log::info('[prestamos.store] INIT', $ctx + [
            'seriales_count_in' => count($serialesInput),
            'firma_len'         => $this->safeLen($firmaRaw),
            'firma_json_len'    => $this->safeLen($firmaJson),
        ]);

        try {
            // Validación (acepta base64 o JSON de trazos)
            $request->validate([
                'seriales'                   => 'required|array|min:1',
                'seriales.*'                 => 'string',
                'cliente_id'                 => 'required|exists:clientes,id',
                'fecha_prestamo'             => 'required|date',
                'fecha_devolucion_estimada'  => 'required|date|after_or_equal:fecha_prestamo',
                'fecha_devolucion_real'      => 'nullable|date|after_or_equal:fecha_prestamo',
                'estado'                     => 'required|in:activo,devuelto,retrasado,cancelado,vendido',
                'condiciones_prestamo'       => 'nullable|string',
                'observaciones'              => 'nullable|string',
                'firmaDigital'               => 'required_without:firmaJson|string',
                'firmaJson'                  => 'nullable|string',
            ]);

            // Datos base
            $data = $request->except(['firmaDigital', 'firmaJson', 'seriales']);
            $data['user_name'] = Auth::user()->name ?? 'Desconocido';

            // Firma con fallback
            $data['firmaDigital'] = $this->storeSignatureOrFail($firmaRaw, $firmaJson, $ctx);

            // Seriales normalizados
            $serialesNorm = $this->normalizeSerieArray($serialesInput);
            Log::info('[prestamos.store] seriales normalizados', $ctx + [
                'seriales_norm_count' => count($serialesNorm),
                'seriales_norm'       => $serialesNorm,
            ]);

            $omitidosNoExisten = [];
            $omitidosOcupados  = [];
            $adjuntadas        = [];

            DB::beginTransaction();
            try {
                /** crear préstamo */
                /** @var Prestamo $prestamo */
                $prestamo = Prestamo::create($data);
                Log::info('[prestamos.store] prestamo creado', $ctx + ['prestamo_id' => $prestamo->id]);

                if (empty($serialesNorm)) {
                    Log::warning('[prestamos.store] sin seriales válidos', $ctx);
                    throw ValidationException::withMessages(['seriales' => 'No hay números de serie válidos para guardar.']);
                }

                /** cargar y bloquear registros */
                $registros = Registro::whereIn('numero_serie', $serialesNorm)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('numero_serie');

                $encontradosSeries = $registros->keys()->all();
                $omitidosNoExisten = array_values(array_diff($serialesNorm, $encontradosSeries));

                $idsRegistros = $registros->pluck('id')->all();
                if (!empty($idsRegistros)) {
                    DB::table('prestamo_registro')
                        ->whereIn('registro_id', $idsRegistros)
                        ->lockForUpdate()
                        ->get();
                }

                $ocupadosIds = Registro::whereIn('id', $idsRegistros)
                    ->whereHas('prestamos', function ($q) {
                        $q->whereIn('estado', ['activo','retrasado']);
                    })
                    ->pluck('id')->all();

                $omitidosOcupados = $registros->whereIn('id', $ocupadosIds)->pluck('numero_serie')->values()->all();

                $validosIds = $registros->whereNotIn('id', $ocupadosIds)->pluck('id')->values()->all();

                if (empty($validosIds)) {
                    Log::warning('[prestamos.store] todos inválidos (no existen/ocupados), rollback', $ctx + [
                        'omitidos_no_existen' => $omitidosNoExisten,
                        'omitidos_ocupados'   => $omitidosOcupados,
                    ]);
                    throw ValidationException::withMessages([
                        'seriales' => 'Todas las series están inválidas (no existen u ocupadas). No se guardó el paquete.',
                    ]);
                }

                $prestamo->registros()->syncWithoutDetaching($validosIds);
                $adjuntadas = $registros->whereIn('id', $validosIds)->pluck('numero_serie')->values()->all();

                DB::commit();

                Log::info('[prestamos.store] OK', $ctx + [
                    'prestamo_id'   => $prestamo->id,
                    'adjuntadas'    => $adjuntadas,
                    'omit_noexist'  => $omitidosNoExisten,
                    'omit_ocupadas' => $omitidosOcupados,
                    'elapsed_ms'    => round((microtime(true)-$t0)*1000),
                ]);

                $partes = [];
                if (!empty($adjuntadas))        $partes[] = 'Agregadas: '.implode(', ', $adjuntadas);
                if (!empty($omitidosNoExisten)) $partes[] = 'No existen: '.implode(', ', $omitidosNoExisten);
                if (!empty($omitidosOcupados))  $partes[] = 'Ocupadas: '.implode(', ', $omitidosOcupados);

                session()->flash('rid', $rid);
                return redirect()->route('prestamos.index')->with('success', 'Préstamo guardado. RID: '.$rid.(empty($partes)?'':' · '.implode(' · ',$partes)));

            } catch (ValidationException $ve) {
                DB::rollBack();
                Log::warning('[prestamos.store] VALIDATION rollback', $ctx + [
                    'errors'     => $ve->errors(),
                    'elapsed_ms' => round((microtime(true)-$t0)*1000),
                ]);
                session()->flash('rid', $rid);
                throw $ve;
            } catch (QueryException $qe) {
                DB::rollBack();
                Log::error('[prestamos.store] QUERY rollback', $ctx + [
                    'sql'        => $qe->getSql(),
                    'bindings'   => $qe->getBindings(),
                    'message'    => $qe->getMessage(),
                    'elapsed_ms' => round((microtime(true)-$t0)*1000),
                ]);
                session()->flash('rid', $rid);
                return back()->withErrors(['db' => 'Error de base de datos. RID: '.$rid])->withInput();
            } catch (Throwable $e) {
                DB::rollBack();
                Log::error('[prestamos.store] EXCEPTION rollback', $ctx + [
                    'message'    => $e->getMessage(),
                    'trace'      => substr($e->getTraceAsString(), 0, 4000),
                    'elapsed_ms' => round((microtime(true)-$t0)*1000),
                ]);
                session()->flash('rid', $rid);
                return back()->withErrors(['server' => 'Error inesperado. RID: '.$rid])->withInput();
            }
        } catch (ValidationException $veOuter) {
            Log::warning('[prestamos.store] OUTER VALIDATION', $ctx + ['errors' => $veOuter->errors()]);
            session()->flash('rid', $rid);
            throw $veOuter;
        }
    }

    public function edit($id)
    {
        $prestamo = Prestamo::with(['registros','cliente'])->findOrFail($id);
        $clientes = Cliente::orderBy('nombre')->get();

        return view('prestamos.edit', compact('prestamo', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $rid = (string) Str::uuid();
        $t0  = microtime(true);
        $ctx = $this->ctx($request, $rid) + ['prestamo_id' => $id];

        $serialesInput = $request->has('seriales') ? (array) $request->input('seriales', []) : null;
        $firmaRaw      = $request->input('firmaDigital');
        $firmaJson     = $request->input('firmaJson');

        Log::info('[prestamos.update] INIT', $ctx + [
            'seriales_present'   => $serialesInput !== null,
            'seriales_count_in'  => $serialesInput !== null ? count($serialesInput) : null,
            'firma_len'          => $this->safeLen($firmaRaw),
            'firma_json_len'     => $this->safeLen($firmaJson),
        ]);

        try {
            $request->validate([
                'seriales'                   => 'sometimes|array',
                'seriales.*'                 => 'string',
                'cliente_id'                 => 'required|exists:clientes,id',
                'fecha_prestamo'             => 'required|date',
                'fecha_devolucion_estimada'  => 'required|date|after_or_equal:fecha_prestamo',
                'fecha_devolucion_real'      => 'nullable|date|after_or_equal:fecha_prestamo',
                'estado'                     => 'required|in:activo,devuelto,retrasado,cancelado,vendido',
                'condiciones_prestamo'       => 'nullable|string',
                'observaciones'              => 'nullable|string',
                'firmaDigital'               => 'nullable|string',
                'firmaJson'                  => 'nullable|string',
            ]);

            $data = $request->except(['firmaDigital', 'firmaJson', 'seriales']);
            $data['user_name'] = Auth::user()->name ?? optional(Prestamo::find($id))->user_name;

            // Reemplazar firma si envían base64 o JSON
            if ($request->filled('firmaDigital') || $request->filled('firmaJson')) {
                $data['firmaDigital'] = $this->storeSignatureOrFail($firmaRaw, $firmaJson, $ctx);
            }

            $serialesNorm = $serialesInput !== null ? $this->normalizeSerieArray($serialesInput) : null;

            $omitidosNoExisten = [];
            $omitidosOcupados  = [];
            $adjuntadas        = [];

            DB::beginTransaction();
            try {
                /** @var Prestamo $prestamo */
                $prestamo = Prestamo::with('registros')
                    ->whereKey($id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $prestamo->update($data);

                if ($serialesNorm === null) {
                    DB::commit();
                    Log::info('[prestamos.update] OK (sin cambios de series)', $ctx + ['elapsed_ms' => round((microtime(true)-$t0)*1000)]);
                    session()->flash('rid', $rid);
                    return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado. RID: '.$rid);
                }

                if (empty($serialesNorm)) {
                    $prestamo->registros()->sync([]);
                    DB::commit();
                    Log::info('[prestamos.update] paquete vacío asignado', $ctx + ['elapsed_ms' => round((microtime(true)-$t0)*1000)]);
                    session()->flash('rid', $rid);
                    return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado (sin equipos). RID: '.$rid);
                }

                $registros = Registro::whereIn('numero_serie', $serialesNorm)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('numero_serie');

                $encontradosSeries = $registros->keys()->all();
                $omitidosNoExisten = array_values(array_diff($serialesNorm, $encontradosSeries));

                $idsRegistros = $registros->pluck('id')->all();
                if (!empty($idsRegistros)) {
                    DB::table('prestamo_registro')
                        ->whereIn('registro_id', $idsRegistros)
                        ->lockForUpdate()
                        ->get();
                }

                $ocupadosPorOtrosIds = Registro::whereIn('id', $idsRegistros)
                    ->whereHas('prestamos', function ($q) use ($prestamo) {
                        $q->whereIn('estado', ['activo','retrasado'])
                          ->where('prestamos.id', '!=', $prestamo->id);
                    })->pluck('id')->all();

                $omitidosOcupados = $registros->whereIn('id', $ocupadosPorOtrosIds)->pluck('numero_serie')->values()->all();

                $validosIds = $registros->whereNotIn('id', $ocupadosPorOtrosIds)->pluck('id')->values()->all();

                if (empty($validosIds)) {
                    DB::commit();
                    Log::warning('[prestamos.update] sin series válidas, se conserva set actual', $ctx + [
                        'omit_noexist'  => $omitidosNoExisten,
                        'omit_ocupadas' => $omitidosOcupados,
                        'elapsed_ms'    => round((microtime(true)-$t0)*1000),
                    ]);
                    session()->flash('rid', $rid);
                    return redirect()->route('prestamos.index')->with('success', 'Actualizado (sin cambios en equipos). RID: '.$rid.' · No existen: '.implode(', ', $omitidosNoExisten).' · Ocupadas: '.implode(', ', $omitidosOcupados));
                }

                $prestamo->registros()->sync($validosIds);

                $adjuntadas = $registros->whereIn('id', $validosIds)->pluck('numero_serie')->values()->all();

                DB::commit();

                Log::info('[prestamos.update] OK', $ctx + [
                    'adjuntadas'    => $adjuntadas,
                    'omit_noexist'  => $omitidosNoExisten,
                    'omit_ocupadas' => $omitidosOcupados,
                    'elapsed_ms'    => round((microtime(true)-$t0)*1000),
                ]);

                $partes = [];
                if (!empty($adjuntadas))        $partes[] = 'Asignadas: '.implode(', ', $adjuntadas);
                if (!empty($omitidosNoExisten)) $partes[] = 'No existen: '.implode(', ', $omitidosNoExisten);
                if (!empty($omitidosOcupados))  $partes[] = 'Ocupadas en otro paquete: '.implode(', ', $omitidosOcupados);

                session()->flash('rid', $rid);
                return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado. RID: '.$rid.(empty($partes)?'':' · '.implode(' · ',$partes)));

            } catch (ValidationException $ve) {
                DB::rollBack();
                Log::warning('[prestamos.update] VALIDATION rollback', $ctx + [
                    'errors'     => $ve->errors(),
                    'elapsed_ms' => round((microtime(true)-$t0)*1000),
                ]);
                session()->flash('rid', $rid);
                throw $ve;
            } catch (QueryException $qe) {
                DB::rollBack();
                Log::error('[prestamos.update] QUERY rollback', $ctx + [
                    'sql'        => $qe->getSql(),
                    'bindings'   => $qe->getBindings(),
                    'message'    => $qe->getMessage(),
                    'elapsed_ms' => round((microtime(true)-$t0)*1000),
                ]);
                session()->flash('rid', $rid);
                return back()->withErrors(['db' => 'Error de base de datos. RID: '.$rid])->withInput();
            } catch (Throwable $e) {
                DB::rollBack();
                Log::error('[prestamos.update] EXCEPTION rollback', $ctx + [
                    'message'    => $e->getMessage(),
                    'trace'      => substr($e->getTraceAsString(), 0, 4000),
                    'elapsed_ms' => round((microtime(true)-$t0)*1000),
                ]);
                session()->flash('rid', $rid);
                return back()->withErrors(['server' => 'Error inesperado. RID: '.$rid])->withInput();
            }
        } catch (ValidationException $veOuter) {
            Log::warning('[prestamos.update] OUTER VALIDATION', $ctx + ['errors' => $veOuter->errors()]);
            session()->flash('rid', $rid);
            throw $veOuter;
        }
    }

    public function destroy($id)
    {
        $rid = (string) Str::uuid();
        $ctx = ['rid' => $rid, 'prestamo_id' => $id];

        try {
            $prestamo = Prestamo::with('registros')->findOrFail($id);

            DB::transaction(function () use ($prestamo) {
                $prestamo->registros()->detach();
                $prestamo->delete();
            });

            Log::info('[prestamos.destroy] OK', $ctx);
            session()->flash('rid', $rid);
            return redirect()->route('prestamos.index')->with('success', 'Préstamo eliminado. RID: '.$rid);
        } catch (Throwable $e) {
            Log::error('[prestamos.destroy] ERROR', $ctx + [
                'message' => $e->getMessage(),
                'trace'   => substr($e->getTraceAsString(), 0, 2000),
            ]);
            session()->flash('rid', $rid);
            return back()->withErrors(['server' => 'No se pudo eliminar. RID: '.$rid]);
        }
    }

    public function lookupBySerie(Request $request)
    {
        $rid = (string) Str::uuid();
        $ctx = $this->ctx($request, $rid);

        try {
            $request->validate(['numero_serie' => 'required|string']);
            $serie = $this->normalizeSerie($request->input('numero_serie',''));

            if ($serie === '') {
                Log::warning('[lookup] serie vacía', $ctx);
                return response()->json(['ok' => false, 'msg' => 'Serie vacía', 'rid' => $rid], 422);
            }

            $registro = Registro::where('numero_serie', $serie)->first();

            if (!$registro) {
                Log::info('[lookup] no existe', $ctx + ['serie' => $serie]);
                return response()->json(['ok' => false, 'msg' => 'No existe', 'rid' => $rid], 404);
            }

            $ocupado = $registro->prestamos()
                ->whereIn('estado', ['activo','retrasado'])
                ->exists();

            if ($ocupado) {
                Log::info('[lookup] ocupado', $ctx + ['serie' => $serie]);
                return response()->json(['ok' => false, 'msg' => 'Ya está prestado', 'rid' => $rid], 409);
            }

            Log::info('[lookup] OK', $ctx + ['serie' => $serie, 'registro_id' => $registro->id]);
            return response()->json(['ok' => true, 'registro' => $registro, 'rid' => $rid]);
        } catch (ValidationException $ve) {
            Log::warning('[lookup] VALIDATION', $ctx + ['errors' => $ve->errors()]);
            return response()->json(['ok' => false, 'msg' => 'Entrada inválida', 'errors' => $ve->errors(), 'rid' => $rid], 422);
        } catch (Throwable $e) {
            Log::error('[lookup] ERROR', $ctx + ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'msg' => 'Error interno', 'rid' => $rid], 500);
        }
    }

    public function show($id)
    {
        $prestamo = Prestamo::with(['registros','cliente'])->findOrFail($id);
        return view('prestamos.show', compact('prestamo'));
    }

    public function pdf($id)
    {
        $rid = (string) Str::uuid();
        $ctx = ['rid' => $rid, 'prestamo_id' => $id];

        $prestamo = Prestamo::with(['registros','cliente'])->findOrFail($id);

        $firmaBase64 = null;
        if ($prestamo->firmaDigital) {
            $pathFromUrl = parse_url($prestamo->firmaDigital, PHP_URL_PATH) ?: $prestamo->firmaDigital;
            $relative = ltrim(str_replace(['storage/','/storage/'], '', $pathFromUrl), '/');
            $localPath = null;

            try {
                $candidate = Storage::disk('public')->path($relative);
                if (is_file($candidate)) $localPath = $candidate;
            } catch (Throwable $e) { /* ignore */ }

            if (!$localPath) {
                $alt = public_path(ltrim($pathFromUrl, '/'));
                if (is_file($alt)) $localPath = $alt;
            }

            if ($localPath && is_readable($localPath)) {
                $mime = mime_content_type($localPath) ?: 'image/png';
                $firmaBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($localPath));
            } else {
                Log::warning('[prestamos.pdf] firma no resolvible', $ctx + [
                    'firmaDigital' => $prestamo->firmaDigital,
                    'path_resuelto'=> $localPath,
                ]);
            }
        }

        Log::info('[prestamos.pdf] render', $ctx);
        $pdf = Pdf::loadView('prestamos.pdf', [
                    'prestamo'    => $prestamo,
                    'firmaBase64' => $firmaBase64,
                ])->setPaper('a4', 'portrait');

        return $pdf->download('prestamo_'.$prestamo->id.'.pdf');
    }
}
