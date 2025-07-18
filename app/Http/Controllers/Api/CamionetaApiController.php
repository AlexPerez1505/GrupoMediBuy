<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Camioneta;


class CamionetaApiController extends Controller
{
    public function index()
    {
        return response()->json(Camioneta::all(), 200);
    }

    public function show($id)
    {
        $camioneta = Camioneta::find($id);
        if (!$camioneta) {
            return response()->json(['message' => 'No encontrada'], 404);
        }
        return response()->json($camioneta, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:50|unique:camionetas',
            'vin' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'anio' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'tipo_motor' => 'nullable|string|max:50',
            'capacidad_carga' => 'nullable|numeric',
            'tipo_combustible' => 'nullable|string|max:50',
            'fecha_adquisicion' => 'nullable|date',
            'ultimo_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'ultima_verificacion' => 'nullable|date',
            'proxima_verificacion' => 'nullable|date',
            'kilometraje' => 'nullable|numeric',
            'rendimiento_litro' => 'nullable|numeric',
            'costo_llenado' => 'nullable|numeric',
            'tarjeta_circulacion' => 'nullable|string|max:255',
            'verificacion' => 'nullable|string|max:255',
            'tenencia' => 'nullable|string|max:255',
            'seguro' => 'nullable|string|max:255',
        ]);

        // Manejo sencillo para foto (opcional)
        if ($request->hasFile('fotos')) {
            $path = $request->file('fotos')->store('public/fotos');
            $validated['fotos'] = $path;
        }

        $camioneta = Camioneta::create($validated);
        return response()->json($camioneta, 201);
    }

    public function update(Request $request, $id)
    {
        $camioneta = Camioneta::find($id);
        if (!$camioneta) {
            return response()->json(['message' => 'No encontrada'], 404);
        }

        $validated = $request->validate([
            'placa' => 'sometimes|string|max:50|unique:camionetas,placa,' . $id,
            'vin' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'anio' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'tipo_motor' => 'nullable|string|max:50',
            'capacidad_carga' => 'nullable|numeric',
            'tipo_combustible' => 'nullable|string|max:50',
            'fecha_adquisicion' => 'nullable|date',
            'ultimo_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'ultima_verificacion' => 'nullable|date',
            'proxima_verificacion' => 'nullable|date',
            'kilometraje' => 'nullable|numeric',
            'rendimiento_litro' => 'nullable|numeric',
            'costo_llenado' => 'nullable|numeric',
            'tarjeta_circulacion' => 'nullable|string|max:255',
            'verificacion' => 'nullable|string|max:255',
            'tenencia' => 'nullable|string|max:255',
            'seguro' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('fotos')) {
            $path = $request->file('fotos')->store('public/fotos');
            $validated['fotos'] = $path;
        }

        $camioneta->update($validated);
        return response()->json($camioneta, 200);
    }

    public function destroy($id)
    {
        $camioneta = Camioneta::find($id);
        if (!$camioneta) {
            return response()->json(['message' => 'No encontrada'], 404);
        }
        $camioneta->delete();
        return response()->json(['message' => 'Eliminada correctamente'], 200);
    }
}
