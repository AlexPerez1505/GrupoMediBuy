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
        // Validar los campos del formulario
        // Validar los campos del formulario
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
    
            // Variables para almacenar rutas
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
    
            // Manejar video
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
            $firma = null;
            if ($request->firmaDigital) {
                $decodedImage = base64_decode($request->firmaDigital);
                $nombreFirma = 'firma_' . time() . '.png';
                Storage::put('public/firmas/' . $nombreFirma, $decodedImage);
                $firma = Storage::url('public/firmas/' . $nombreFirma);
                Log::info('Firma digital guardada correctamente.', ['ruta' => $firma]);
            }
    
            // Crear el registro en la base de datos con columnas separadas
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
    
// Función para mostrar los productos en el DataTable
public function mostrarProductos()
{
    // Obtener todos los registros (productos)
    $productos = Registro::all();

    // Crear una colección vacía si la vista espera procesos
    $procesos = collect(); 

    // Retornar la vista con los productos y procesos vacíos
    return view('inventario', compact('productos', 'procesos'));
}
public function obtenerDetalles($id)
{
    // Cargar el registro con la relación 'procesos'
    $registro = Registro::with('procesos.fichaTecnica')->findOrFail($id);

    // Definir los estados posibles
    $estados = [
        1 => 'En Stock',
        2 => 'Vendido',
        3 => 'En Mantenimiento',
        4 => 'Defectuoso',
    ];

    // Obtener el estado actual del registro
    $estadoActual = $estados[$registro->estado_actual] ?? 'Estado desconocido';

    // Obtener todos los procesos asociados al registro
    $procesos = $registro->procesos->map(function ($proceso) {
        // Verificar y ajustar la ruta de la ficha técnica
        $fichaArchivo = $proceso->fichaTecnica ? $proceso->fichaTecnica->archivo : null;
        $defectos = is_array($proceso->defectos)
    ? implode("\n", array_filter($proceso->defectos)) // filtra vacíos si es array
    : (trim($proceso->defectos) !== '' && strtolower($proceso->defectos) !== 'null'
        ? $proceso->defectos
        : null);





        if ($fichaArchivo) {
            // Verifica si la ruta ya tiene el prefijo 'fichas_tecnicas/'
            if (!str_contains($fichaArchivo, 'fichas_tecnicas/')) {
                $fichaArchivo = 'fichas_tecnicas/' . $fichaArchivo;
            }
        }

        return [
            'id' => $proceso->id,
            'descripcion_proceso' => $proceso->descripcion_proceso ?? 'No disponible',
            'evidencia1' => $proceso->evidencia1 ? asset('storage/' . $proceso->evidencia1) : null,
            'evidencia2' => $proceso->evidencia2 ? asset('storage/' . $proceso->evidencia2) : null,
            'evidencia3' => $proceso->evidencia3 ? asset('storage/' . $proceso->evidencia3) : null,
            'video' => $proceso->video ? asset('storage/' . $proceso->video) : null,
            'ficha_tecnica_archivo' => $fichaArchivo, // Agregar la ruta de la ficha técnica
            'ficha_tecnica_nombre' => $proceso->fichaTecnica ? $proceso->fichaTecnica->nombre : 'No disponible', // Agregar el nombre
            'defectos' => $defectos, // Aquí se asigna directamente la cadena
        ];
    });

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
        'video' => $registro->video, // Incluye el video
        'procesos' => $procesos,
    ]);





    // Retornar la vista con la información del registro y sus procesos
    return view('inventario', compact('registro', 'procesos', 'estadoActual'));
}
// En RegistroController.php
public function obtenerProcesosPendientes($id)
{
    // Cargar el registro con la relación 'procesos'
    $registro = Registro::with('procesos')->findOrFail($id);

    // Definir los procesos disponibles
    $todosLosProcesos = ['hojalateria', 'mantenimiento', 'stock', 'finalizado'];

    // Obtener los procesos ya completados
    $procesosCompletados = $registro->procesos->pluck('descripcion_proceso')->toArray();

    // Filtrar los procesos pendientes
    $procesosPendientes = array_diff($todosLosProcesos, $procesosCompletados);

    return response()->json($procesosPendientes);
}




    
}
