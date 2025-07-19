<?php
namespace App\Http\Controllers;
use App\Models\Registro;
use App\Models\ProcesoEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RegistroController extends Controller
{
    public function guardarRegistro(Request $request) 
    {

        $validator = Validator::make($request->all(), [
            'Tipo_de_Equipo' => 'required|string|max:255',
            'Subtipo_de_Equipo' => 'nullable|string|max:255',
            'Subtipo_de_Equipo_Otro' => 'nullable|string|max:255',
            'Numero_de_Serie' => 'required|string|max:255',
            'Marca' => 'required|string|max:255',
            'Modelo' => 'required|string|max:255',
            'Año' => 'nullable|string|max:4', 
            'descripcion' => 'required|string',
            'estado_actual' => 'nullable|in:1,2,3,4', 
            'fecha_inicial' => 'required|date',
            'fecha_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'evidencia1' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'video-evidencia' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/x-matroska,video/mkv',
            'documentoPDF' => 'nullable|file|mimes:pdf|max:10240',
            'observaciones' => 'nullable|string',
            'firmaDigital' => 'required|string',
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
    
            // Manejo de imágenes separadas
            for ($i = 1; $i <= 3; $i++) {
                $campo = "evidencia$i";
                if ($request->hasFile($campo)) {
                    if ($request->file($campo)->isValid()) {
                        $ruta = $request->file($campo)->store('public/evidencias');
                        $evidencias[$i - 1] = Storage::url($ruta);
                        Log::info("Imagen $i guardada correctamente.", ['ruta' => $ruta]);
                    } else {
                        Log::warning("El archivo $i no es válido.");
                    }
                }
            }
            $video = null;
            if ($request->hasFile('video-evidencia') && $request->file('video-evidencia')->isValid()) {
                $rutaVideo = $request->file('video-evidencia')->store('public/videos');
                $video = Storage::url($rutaVideo);
                Log::info('Video guardado correctamente.', ['ruta' => $rutaVideo]);
            }
    
            // Manejar archivo PDF
            $documentoPDF = null;
            if ($request->hasFile('documentoPDF') && $request->file('documentoPDF')->isValid()) {
                $rutaPDF = $request->file('documentoPDF')->store('public/documentos/pdf');
                $documentoPDF = Storage::url($rutaPDF);
                Log::info('Documento PDF guardado correctamente.', ['ruta' => $rutaPDF]);
            }
    
            // Manejar firma digital
           // Manejar firma digital
$firma = null;
if ($request->firmaDigital) {
    // Quitar encabezado 'data:image/png;base64,'
    $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $request->firmaDigital);
    $decodedImage = base64_decode($base64Image);
    if ($decodedImage !== false) {
        $nombreFirma = 'firma_' . time() . '.png';
        Storage::put('public/firmas/' . $nombreFirma, $decodedImage);
        $firma = Storage::url('firmas/' . $nombreFirma);
        Log::info('Firma digital guardada correctamente.', ['ruta' => $firma]);
    } else {
        Log::error('Error al decodificar la firma.');
    }
}
            $registro = Registro::create([
                'tipo_equipo' => $request->Tipo_de_Equipo,
                'subtipo_equipo' => $request->Subtipo_de_Equipo,
                'subtipo_equipo_otro' => $request->Subtipo_de_Equipo_Otro,
                'numero_serie' => $request->Numero_de_Serie,
                'marca' => $request->Marca,
                'modelo' => $request->Modelo,
                'anio' => $request->Año,
                'descripcion' => $request->descripcion,
                'estado_actual' => $request->estado_actual,
                'fecha_adquisicion' => $request->fecha_inicial,
                'ultimo_mantenimiento' => $request->fecha_mantenimiento,
                'proximo_mantenimiento' => $request->proximo_mantenimiento,
                'evidencia1' => $evidencias[0],
                'evidencia2' => $evidencias[1],
                'evidencia3' => $evidencias[2],
                'video' => $video,
                'documentoPDF' => $documentoPDF,
                'observaciones' => $request->observaciones,
                'firma_digital' => $firma,
                'user_name' => Auth::user()->name,
            ]);
    
            Log::info('Registro creado exitosamente.', ['id' => $registro->id]);
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
public function mostrarProductos()
{
    $productos = Registro::all();

    $procesos = collect(); 
    return view('inventario', compact('productos', 'procesos'));
}
public function obtenerDetalles($id)
{
    // Cargar el registro con la relación 'procesos' y 'fichaTecnica'
    $registro = Registro::with('procesos.fichaTecnica')->findOrFail($id);

    // Definir los estados posibles
    $estados = [
        1 => 'En Stock',
        2 => 'Vendido',
        3 => 'En Mantenimiento',
        4 => 'Defectuoso',
    ];
    $estadoActual = $estados[$registro->estado_actual] ?? 'Estado desconocido';

    // Mapear procesos con los datos requeridos
    $procesos = $registro->procesos->map(function ($proceso) {
        $fichaArchivo = $proceso->fichaTecnica ? $proceso->fichaTecnica->archivo : null;

        $defectos = is_array($proceso->defectos)
            ? implode("\n", array_filter($proceso->defectos))
            : (trim($proceso->defectos) !== '' && strtolower($proceso->defectos) !== 'null'
                ? $proceso->defectos
                : null);

        if ($fichaArchivo && !str_contains($fichaArchivo, 'fichas_tecnicas/')) {
            $fichaArchivo = 'fichas_tecnicas/' . $fichaArchivo;
        }

        return [
            'id' => $proceso->id,
            'descripcion_proceso' => $proceso->descripcion_proceso ?? 'No disponible',
            'evidencia1' => $proceso->evidencia1 ? asset('storage/' . $proceso->evidencia1) : null,
            'evidencia2' => $proceso->evidencia2 ? asset('storage/' . $proceso->evidencia2) : null,
            'evidencia3' => $proceso->evidencia3 ? asset('storage/' . $proceso->evidencia3) : null,
            'video' => $proceso->video ? asset('storage/' . $proceso->video) : null,
            'ficha_tecnica_archivo' => $fichaArchivo,
            'ficha_tecnica_nombre' => $proceso->fichaTecnica ? $proceso->fichaTecnica->nombre : 'No disponible',
            'defectos' => $defectos,
            'created_at' => $proceso->created_at ? $proceso->created_at->format('Y-m-d H:i:s') : null,
        ];
    });

    // Formatear ruta de firma digital
    $firmaPath = $registro->firma_digital;
    if ($firmaPath && str_contains($firmaPath, 'storage/')) {
        $firmaUrl = asset($firmaPath);
    } elseif ($firmaPath) {
        $firmaUrl = asset('storage/' . $firmaPath);
    } else {
        $firmaUrl = null;
    }

    // Retornar la respuesta JSON
    return response()->json([
        'tipo_equipo' => $registro->tipo_equipo,
        'subtipo_equipo' => $registro->subtipo_equipo,
        'numero_serie' => $registro->numero_serie,
        'marca' => $registro->marca,
        'modelo' => $registro->modelo,
        'anio' => $registro->anio,
        'estado_actual' => $estadoActual,
        'fecha_adquisicion' => optional($registro->fecha_adquisicion)->format('Y-m-d'),
        'ultimo_mantenimiento' => optional($registro->ultimo_mantenimiento)->format('Y-m-d'),
        'proximo_mantenimiento' => optional($registro->proximo_mantenimiento)->format('Y-m-d'),
        'descripcion' => $registro->descripcion,
        'observaciones' => $registro->observaciones,
        'evidencia1' => $registro->evidencia1,
        'evidencia2' => $registro->evidencia2,
        'evidencia3' => $registro->evidencia3,
        'documentoPDF' => $registro->documentoPDF,
        'video' => $registro->video,
        'firma_digital' => $firmaUrl,
        'user_name' => $registro->user_name,
        'procesos' => $procesos,
    ]);
}

public function actualizarRegistro(Request $request, $id)
{
    $registro = Registro::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'tipo_equipo' => 'required|string|max:255',
        'subtipo_equipo' => 'nullable|string|max:255',
        'subtipo_equipo_otro' => 'nullable|string|max:255',
        'numero_serie' => 'required|string|max:255',
        'marca' => 'required|string|max:255',
        'modelo' => 'required|string|max:255',
        'anio' => 'nullable|string|max:4',
        'descripcion' => 'required|string',
        'estado_actual' => 'nullable|in:1,2,3,4',
        'fecha_adquisicion' => 'required|date',
        'ultimo_mantenimiento' => 'nullable|date',
        'proximo_mantenimiento' => 'nullable|date',
        'observaciones' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $registro->update([
            'tipo_equipo' => $request->tipo_equipo,
            'subtipo_equipo' => $request->subtipo_equipo,
            'subtipo_equipo_otro' => $request->subtipo_equipo_otro,
            'numero_serie' => $request->numero_serie,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'anio' => $request->anio,
            'descripcion' => $request->descripcion,
            'estado_actual' => $request->estado_actual,
            'fecha_adquisicion' => $request->fecha_adquisicion,
            'ultimo_mantenimiento' => $request->ultimo_mantenimiento,
            'proximo_mantenimiento' => $request->proximo_mantenimiento,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado exitosamente.'
        ]);
    } catch (\Exception $e) {
        Log::error('Error al actualizar el registro.', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'error' => 'Hubo un error al actualizar el registro.'
        ], 500);
    }
}

public function mostrarRegistro($id)
{
    $registro = Registro::findOrFail($id);
    return response()->json($registro);
}

public function eliminarRegistro($id)
{
    try {
        $registro = Registro::findOrFail($id);

        // Si hay archivos, puedes borrarlos físicamente aquí si lo deseas.
        // Storage::delete('ruta');

        $registro->delete();

        return response()->json(['success' => true, 'message' => 'Registro eliminado correctamente.']);
    } catch (\Exception $e) {
        Log::error('Error al eliminar el registro.', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => 'Hubo un error al eliminar el registro.'], 500);
    }
}

public function obtenerProcesosPendientes($id)
{

    $registro = Registro::with('procesos')->findOrFail($id);


    $todosLosProcesos = ['hojalateria', 'mantenimiento', 'stock', 'finalizado'];


    $procesosCompletados = $registro->procesos->pluck('descripcion_proceso')->toArray();

 
    $procesosPendientes = array_diff($todosLosProcesos, $procesosCompletados);

    return response()->json($procesosPendientes);
}
public function registrosStock()
{
    return response()->json(
        \App\Models\Registro::where('estado_proceso', 'stock')->get(['id', 'numero_serie'])
    );
}
public function info($id)
{
    $registro = Registro::findOrFail($id);

    return response()->json([
        'numero_serie'     => $registro->numero_serie,
        'estado_proceso'   => $registro->estado_proceso,
        'tipo_equipo'      => $registro->tipo_equipo,
        'subtipo_equipo'   => $registro->subtipo_equipo,
        'modelo'           => $registro->modelo,
        'marca'            => $registro->marca,
        'evidencia1'       => $registro->evidencia1,
    ]);
}




  
}
