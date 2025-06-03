<?php
namespace App\Http\Controllers;

use App\Models\ProcesoEquipo;
use App\Models\Registro;
use Illuminate\Http\Request;
use App\Models\FichaTecnica;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProcesoEquipoController extends Controller
{
    public function guardarProceso(Request $request, $id)
    {
        try {
            $registro = Registro::findOrFail($id);
    
            // Verificar si ya existe un proceso de ese tipo para el registro
            $procesoExistente = ProcesoEquipo::where('registro_id', $id)
                                             ->where('tipo_proceso', $request->tipo_proceso)
                                             ->exists();
    
            if ($procesoExistente) {
                // Convertir tipo_proceso a algo más legible (por ejemplo: "inspeccion_final" → "Inspección final")
                $tipoLegible = ucwords(str_replace('_', ' ', $request->tipo_proceso));
    
                $mensaje = 'Ya se ha realizado el proceso de tipo "' . $tipoLegible . '" para este equipo.';
    
                if ($request->ajax()) {
                    return response()->json(['message' => $mensaje], 422);
                }
    
                return redirect()->back()->with('error', $mensaje);
            }
    
            // Validación
            $validator = Validator::make($request->all(), [
                'descripcion_proceso' => 'required|string',
                'evidencia1' => 'nullable|file',
                'evidencia2' => 'nullable|file',
                'evidencia3' => 'nullable|file',
                'video' => 'nullable|file',
                'documentoPDF' => 'nullable|file|mimes:pdf|max:102400',
                'ficha_tecnica_id' => 'nullable|exists:fichas_tecnicas,id',
                'defectos' => 'nullable|array',
                'defectos.*' => 'string',
            ]);
    
            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'Errores de validación.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }
    
            // Guardar archivos si existen
            $evidencia1 = $request->file('evidencia1') ? $request->file('evidencia1')->store('procesos', 'public') : null;
            $evidencia2 = $request->file('evidencia2') ? $request->file('evidencia2')->store('procesos', 'public') : null;
            $evidencia3 = $request->file('evidencia3') ? $request->file('evidencia3')->store('procesos', 'public') : null;
            $video = $request->file('video') ? $request->file('video')->store('procesos', 'public') : null;
            $documentoPDF = $request->file('documentoPDF') ? $request->file('documentoPDF')->store('documentos', 'public') : null;
    
            // Crear un nuevo proceso
            $proceso = new ProcesoEquipo([
                'registro_id' => $registro->id,
                'tipo_proceso' => $request->tipo_proceso,
                'descripcion_proceso' => $request->descripcion_proceso,
                'evidencia1' => $evidencia1,
                'evidencia2' => $evidencia2,
                'evidencia3' => $evidencia3,
                'video' => $video,
                'documento_pdf' => $documentoPDF,
                'ficha_tecnica_id' => $request->ficha_tecnica_id,
                'defectos' => json_encode($request->defectos),
            ]);
    
            $proceso->save();
    
            $registro->proceso_id = $proceso->id;
    
            if ($request->tipo_proceso === 'defectuoso') {
                $registro->estado_proceso = 'defectuoso';
            } else {
                $this->actualizarEstado($registro);
            }
    
            $registro->save();
    
            if ($request->ajax()) {
                return response()->json(['message' => 'Proceso guardado con éxito.']);
            }
    
            return redirect()->back()->with('success', 'Proceso guardado con éxito');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Ocurrió un error inesperado.',
                    'error' => $e->getMessage()
                ], 500);
            }
    
            return redirect()->back()->with('error', 'Ocurrió un error al guardar el proceso: ' . $e->getMessage());
        }
    }
    
    
    
    public function stock($id) {
        $fichas = FichaTecnica::all(); // Obtiene las fichas
        return view('procesos.stock', compact('fichas', 'id'));
    }
    
    
    
    
    public function mostrarProceso($id)
    {
        // Obtener el registro
        $registro = Registro::findOrFail($id);
        
        // Recuperar el subtipo_equipo
        $subtipo_equipo = $registro->subtipo_equipo; // O ajusta esto según tu estructura de datos
        
        // Recuperar el mensaje de éxito desde la sesión
        $success = session('success');
        
        // Pasar todo correctamente a la vista
        return view('procesos.hojalateria', [
            'registro' => $registro,
            'subtipo_equipo' => $subtipo_equipo,
            'success' => $success,
            'id' => $id // Asegúrate de pasar el id
        ]);
    }
    
    
    
    
    private function actualizarEstado($registro)
    {
        // Si ya está en un estado final, no hacer nada
        if (in_array($registro->estado_proceso, ['defectuoso', 'vendido'])) {
            return; // No avanzar más
        }
    
        // Avanzar según el flujo de estados
        switch ($registro->estado_proceso) {
            case 'registro':
                $registro->estado_proceso = 'hojalateria';
                break;
            case 'hojalateria':
                $registro->estado_proceso = 'mantenimiento';
                break;
            case 'mantenimiento':
                $registro->estado_proceso = 'stock';
                break;
            case 'stock':
                $registro->estado_proceso = 'vendido';
                break;
        }
    
        $registro->save();
    }
  

    
    
    
}
