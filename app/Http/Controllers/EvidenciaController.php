<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use Illuminate\Http\Request;

class EvidenciaController extends Controller
{
    public function index()
    {
        return response()->json(Evidencia::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'user_id' => 'required|exists:users,id',
            'archivo' => 'required|string', // Si es archivo real, cambia a file y usa Storage
            'tipo' => 'nullable|string',
        ]);
        $evidencia = Evidencia::create($request->all());
        return response()->json($evidencia, 201);
    }

    public function show($id)
    {
        $evidencia = Evidencia::findOrFail($id);
        return response()->json($evidencia);
    }

    public function update(Request $request, $id)
    {
        $evidencia = Evidencia::findOrFail($id);
        $evidencia->update($request->all());
        return response()->json($evidencia);
    }

    public function destroy($id)
    {
        $evidencia = Evidencia::findOrFail($id);
        $evidencia->delete();
        return response()->json(['message' => 'Evidencia eliminada.']);
    }
}
