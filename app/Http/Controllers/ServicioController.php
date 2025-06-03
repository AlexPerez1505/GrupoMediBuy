<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        return view('inventarioservicio', compact('servicios'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_equipo' => 'required|string',
            'subtipo_equipo' => 'required|string',
            'numero_serie' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'año' => 'nullable|integer',
            'descripcion' => 'nullable|string',
            'fecha_inicial' => 'required|date',
            'evidencia1' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'video-evidencia' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/x-matroska,video/mkv',
            'documentoPDF' => 'nullable|file|mimes:pdf|max:10240',
            'firmaDigital' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'user_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida.', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Log::info('Inicio del proceso de guardar registro.');
            $evidencias = [null, null, null];

            for ($i = 1; $i <= 3; $i++) {
                $campo = "evidencia$i";
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $ruta = $request->file($campo)->store('public/evidencias');
                    $evidencias[$i - 1] = Storage::url($ruta);
                    Log::info("Imagen evidencia$i guardada correctamente.", ['ruta' => $ruta]);
                }
            }

            $video = null;
            if ($request->hasFile('video') && $request->file('video')->isValid()) {
                $rutaVideo = $request->file('video')->store('public/videos');
                $video = Storage::url($rutaVideo);
                Log::info('Video guardado correctamente.', ['ruta' => $rutaVideo]);
            }

            $documentoPDF = null;
            if ($request->hasFile('documentoPDF') && $request->file('documentoPDF')->isValid()) {
                $rutaPDF = $request->file('documentoPDF')->store('public/documentos/pdf');
                $documentoPDF = Storage::url($rutaPDF);
                Log::info('Documento PDF guardado correctamente.', ['ruta' => $rutaPDF]);
            }

            // Procesar firma digital
          // Procesar firma digital
  // Procesar firma digital
  $firma = null;
  if ($request->firmaDigital) {
    $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $request->firmaDigital);
    $decodedImage = base64_decode($base64Image);

    if ($decodedImage !== false) {
        $nombreFirma = 'firma_' . time() . '.png';
        // Guardar la firma en la ruta correcta
        Storage::put('firmas/' . $nombreFirma, $decodedImage);

        // Guardar solo el path relativo
        $firma = 'firmas/' . $nombreFirma;

        Log::info('Firma digital guardada correctamente.', ['ruta' => $firma]);
    } else {
        Log::error('Error al decodificar la firma.');
    }
}



            $servicio = Servicio::create([
                'tipo_equipo' => $request->input('tipo_equipo'),
                'subtipo_equipo' => $request->input('subtipo_equipo'),
                'subtipo_equipo_otro' => $request->input('subtipo_equipo_otro'),
                'numero_serie' => $request->input('numero_serie'),
                'marca' => $request->input('marca'),
                'modelo' => $request->input('modelo'),
                'anio' => $request->input('año'),
                'descripcion' => $request->input('descripcion'),
                'estado_actual' => $request->input('estado_actual'),
                'fecha_adquisicion' => $request->input('fecha_inicial'),
                'ultimo_mantenimiento' => $request->input('fecha_mantenimiento'),
                'proximo_mantenimiento' => $request->input('proximo_mantenimiento'),
                'evidencia1' => $evidencias[0],
                'evidencia2' => $evidencias[1],
                'evidencia3' => $evidencias[2],
                'video' => $video,
                'documentoPDF' => $documentoPDF,
                'firma_digital' => $firma,
                'observaciones' => $request->input('observaciones'),
                'user_name' => Auth::user()->name ?? $request->input('user_name'),
            ]);

            Log::info('Registro creado exitosamente.', ['id' => $servicio->id]);

            return response()->json(['success' => true, 'message' => 'Registro guardado exitosamente.']);

        } catch (\Exception $e) {
            Log::error('Error al guardar el registro.', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Hubo un error al guardar el registro.'
            ], 500);
        }
    }
    public function detalles($id) 
    {
        $servicio = Servicio::find($id);
    
        if (!$servicio) {
            return response()->json(['error' => 'Servicio no encontrado.'], 404);
        }
    
        $cleanStoragePath = function ($path) {
            if (!$path) return null;
    
            // Si ya es una URL completa, no la toques
            if (preg_match('/^https?:\/\//', $path)) {
                return $path;
            }
    
            // Si empieza con 'storage/', limpia esa parte
            $cleanedPath = preg_replace('#^/?storage/#', '', $path);
    
            return Storage::url($cleanedPath);
        };
    
        $movimientos = Movimiento::where('servicio_id', $servicio->id)->get()->map(function ($mov) use ($cleanStoragePath) {
            return [
                'id' => $mov->id,
                'descripcion' => $mov->descripcion,
                'evidencia1' => $cleanStoragePath($mov->evidencia1),
                'evidencia2' => $cleanStoragePath($mov->evidencia2),
                'evidencia3' => $cleanStoragePath($mov->evidencia3),
                'video' => $cleanStoragePath($mov->video),
                'tipo_movimiento' => $mov->tipo_movimiento,
                'fecha' => $mov->created_at->format('Y-m-d H:i:s'),
                'checklist' => $mov->checklist,
            ];
        });
    
        // ✅ Usamos directamente la URL guardada en la base de datos
        $firmaUrl = $servicio->firma_digital ? Storage::url($servicio->firma_digital) : null;

    
        return response()->json([
            'tipo_equipo' => $servicio->tipo_equipo,
            'descripcion' => $servicio->descripcion,
            'subtipo_equipo' => $servicio->subtipo_equipo,
            'modelo' => $servicio->modelo,
            'marca' => $servicio->marca,
            'observaciones' => $servicio->observaciones,
            'numero_serie' => $servicio->numero_serie,
            'fecha_adquisicion' => $servicio->fecha_adquisicion,
            'estado_proceso' => $servicio->estado_proceso,
            'anio' => $servicio->anio,
            'documentoPDF' => $cleanStoragePath($servicio->documentoPDF),
            'evidencia1' => $cleanStoragePath($servicio->evidencia1),
            'evidencia2' => $cleanStoragePath($servicio->evidencia2),
            'evidencia3' => $cleanStoragePath($servicio->evidencia3),
            'firma_digital' => $firmaUrl,
            'user_name' => $servicio->user_name,
            'video' => $cleanStoragePath($servicio->video),
            'movimientos' => $movimientos,
        ]);
    }
    
}
