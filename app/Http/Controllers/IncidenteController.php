<?php

namespace App\Http\Controllers;

use App\Models\Incidente;
use Illuminate\Http\Request;

class IncidenteController extends Controller
{
    public function index()
    {
        return response()->json(Incidente::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'item_parte_id' => 'nullable|exists:item_partes,id',
            'user_id' => 'required|exists:users,id',
            'descripcion' => 'required|string',
        ]);
        $incidente = Incidente::create($request->all());
        return response()->json($incidente, 201);
    }

    public function show($id)
    {
        $incidente = Incidente::findOrFail($id);
        return response()->json($incidente);
    }

    public function update(Request $request, $id)
    {
        $incidente = Incidente::findOrFail($id);
        $incidente->update($request->all());
        return response()->json($incidente);
    }

    public function destroy($id)
    {
        $incidente = Incidente::findOrFail($id);
        $incidente->delete();
        return response()->json(['message' => 'Incidente eliminado.']);
    }
}
