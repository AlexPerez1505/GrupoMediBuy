<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    public function guardarRegistro(Request $request) 
{
    // Validar los campos del formulario
    $validator = Validator::make($request->all(), [
        'Tipo_de_Equipo' => 'required|string|max:255',
        'Subtipo_de_Equipo' => 'nullable|string|max:255',
        'Subtipo_de_Equipo_Otro' => 'nullable|string|max:255',
        'Numero_de_Serie' => 'required|string|max:255',
        'Marca' => 'required|string|max:255',
        'Modelo' => 'required|string|max:255',
        'Año' => 'nullable|string|max:4', // El año es opcional
        'descripcion' => 'required|string',
        'estado_actual' => 'required|in:1,2,3', // En Stock (1), Vendido (2), En Mantenimiento (3)
        'fecha_inicial' => 'required|date',
        'fecha_mantenimiento' => 'required|date',
        'proximo_mantenimiento' => 'required|date',
        'evidencia' => 'nullable|array|max:3', // Máximo 3 imágenes
        'evidencia.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048', // Cada imagen debe ser válida
        'video-evidencia' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
        'documentoPDF' => 'nullable|file|mimes:pdf|max:10240', // Agregar validación para PDF (máximo 10 MB)
        'observaciones' => 'nullable|string',
        'firmaDigital' => 'required|string', // Base64 de la firma
    ]);

    if ($validator->fails()) {
        Log::warning('Validación fallida.', $validator->errors()->toArray());
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        Log::info('Inicio del proceso de guardar registro.');

        // Inicializar variables
        $evidencia = [];
        $video = null;
        $firma = null;

        // Manejar imágenes
        if ($request->hasFile('evidencia')) {
            foreach ($request->file('evidencia') as $archivo) {
                if ($archivo->isValid()) {
                    $ruta = $archivo->store('public/evidencias');
                    $evidencia[] = Storage::url($ruta);
                    Log::info('Imagen guardada correctamente.', ['ruta' => $ruta]);
                } else {
                    Log::warning('El archivo no es válido.', ['nombre' => $archivo->getClientOriginalName()]);
                }
            }
        } else {
            Log::info('No se encontraron archivos en el campo "evidencia".');
        }

        // Verificar que no se exceda el límite de imágenes
        if (count($evidencia) > 3) {
            Log::warning('Se intentaron subir más de 3 imágenes.', ['evidencia' => $evidencia]);
            return redirect()->back()->with('error', 'Solo se permiten hasta 3 imágenes.')->withInput();
        }

        // Manejar video
        if ($request->hasFile('video-evidencia')) {
            if ($request->file('video-evidencia')->isValid()) {
                $rutaVideo = $request->file('video-evidencia')->store('public/videos');
                $video = Storage::url($rutaVideo);
                Log::info('Video guardado correctamente.', ['ruta' => $rutaVideo]);
            } else {
                Log::warning('El archivo de video no es válido.');
            }
        }
          // Manejar archivo PDF (Ficha Técnica)
          if ($request->hasFile('documentoPDF')) {
            if ($request->file('documentoPDF')->isValid()) {
                $rutaPDF = $request->file('documentoPDF')->store('public/documentos/pdf');
                $documentoPDF = Storage::url($rutaPDF);
                Log::info('Documento PDF guardado correctamente.', ['ruta' => $rutaPDF]);
            } else {
                Log::warning('El archivo PDF no es válido.');
            }
        }

        // Manejar firma digital
        if ($request->firmaDigital) {
            $decodedImage = base64_decode($request->firmaDigital);
            $nombreFirma = 'firma_' . time() . '.png';
            Storage::put('public/firmas/' . $nombreFirma, $decodedImage);
            $firma = Storage::url('public/firmas/' . $nombreFirma);
            Log::info('Firma digital guardada correctamente.', ['ruta' => $firma]);
        }

        // Crear el registro en la base de datos
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
            'evidencia' => json_encode(array_values($evidencia)), // Asegurar un índice limpio
            'video' => $video,
            'documentoPDF' => $documentoPDF,
            'observaciones' => $request->observaciones,
            'firma_digital' => $firma,
        ]);

        Log::info('Registro creado exitosamente.', ['id' => $registro->id]);

        session()->flash('success', 'Registro guardado exitosamente.');
        return redirect()->route('inventario');
    } catch (\Exception $e) {
        Log::error('Error al guardar el registro.', [
            'error' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
        ]);
        return redirect()->back()->with('error', 'Hubo un error al guardar el registro.')->withInput();
    }
}


    // Función para mostrar los productos en el DataTable
    public function mostrarProductos()
    {
        // Obtener todos los registros (productos)
        $productos = Registro::all();

        // Retornar la vista con los productos
        return view('inventario', compact('productos'));
    }
    public function obtenerDetalles($id)
    {
        // Obtener el registro por su ID
        $registro = Registro::findOrFail($id);
    
        // Definir los estados posibles
        $estados = [
            1 => 'En Stock',
            2 => 'Vendido',
            3 => 'En Mantenimiento',
        ];
    
        // Obtener el estado legible basado en el ID de movimiento
    $estadoActual = isset($estados[$registro->estado_actual]) ? $estados[$registro->estado_actual] : 'Estado desconocido';

        
        
        // Si es una solicitud AJAX, retornar JSON
        if (request()->ajax()) {
            return response()->json([
                'tipo_equipo' => $registro->tipo_equipo,
                'subtipo_equipo' => $registro->subtipo_equipo,
                'numero_serie' => $registro->numero_serie,
                'marca' => $registro->marca,
                'modelo' => $registro->modelo,
                'anio' => $registro->anio,
                'estado_actual' => $estadoActual, 
                'fecha_adquisicion' => $registro->fecha_adquisicion ? \Carbon\Carbon::parse($registro->fecha_adquisicion)->format('Y-m-d') : null,
                'ultimo_mantenimiento' => $registro->ultimo_mantenimiento ? \Carbon\Carbon::parse($registro->ultimo_mantenimiento)->format('Y-m-d') : null,
                'proximo_mantenimiento' => $registro->proximo_mantenimiento ? \Carbon\Carbon::parse($registro->proximo_mantenimiento)->format('Y-m-d') : null,
                'descripcion' => $registro->descripcion,
                'observaciones' => $registro->observaciones,
                'evidencia' => $registro->evidencia ? json_decode($registro->evidencia, true) : [],
                'documentoPDF' => $registro->documentoPDF, 
                'video' => $registro->video, // Incluye el video
            ]);
        }
    
        // Si no es AJAX, retornar la vista
        return view('inventario', ['registro' => $registro]);
    }
}
    