<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiDynamicChecklistService;

class AiChecklistController extends Controller
{
    public function suggest(Request $r, AiDynamicChecklistService $ai)
    {
        // Soporta ambos: nuevo set de campos y compatibilidad con nombre_equipo
        $data = $r->validate([
            // Nuevo esquema
            'equipo'        => ['nullable','string','max:180'],
            'marca'         => ['nullable','string','max:120'],
            'modelo'        => ['nullable','string','max:120'],
            'numero_serie'  => ['nullable','string','max:140'],
            'observaciones' => ['nullable','string','max:1000'],

            // Compatibilidad previa
            'nombre_equipo' => ['nullable','string','max:255'],

            // Contexto
            'servicio'      => ['nullable','in:preventivo,correctivo,mixto'],
            'sintomas'      => ['nullable','string','max:1000'],
        ]);

        // Resolver texto libre “rico” a partir de los nuevos campos.
        // Prioridad: equipo+marca+modelo => nombre_equipo (legacy)
        $partes = array_filter([
            $data['equipo']  ?? null,
            $data['marca']   ?? null,
            $data['modelo']  ?? null,
        ], fn($v)=> filled($v));

        $freeText = trim(implode(' ', $partes));
        if (blank($freeText)) {
            $freeText = (string)($data['nombre_equipo'] ?? '');
        }

        if (blank($freeText)) {
            return response()->json([
                'error' => 'Debes enviar al menos equipo/marca/modelo o nombre_equipo.'
            ], 422);
        }

        // Contexto estructurado para el modelo
        $context = [
            'locale'       => 'es',
            'servicio'     => $data['servicio'] ?? 'preventivo',
            'sintomas'     => $data['sintomas'] ?? null,
            'observaciones'=> $data['observaciones'] ?? null,
            'equipo'       => $data['equipo'] ?? null,
            'marca'        => $data['marca'] ?? null,
            'modelo'       => $data['modelo'] ?? null,
            'numero_serie' => $data['numero_serie'] ?? null,
        ];

        $json = $ai->generateChecklist(
            $freeText,
            $context,
            ['return_meta'=>false]
        );

        return response()->json($json);
    }
}
