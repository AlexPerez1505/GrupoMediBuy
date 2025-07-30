<?php

namespace App\Http\Controllers;

use App\Models\ChecklistFirma;
use Illuminate\Http\Request;

class ChecklistFirmaController extends Controller
{
    public function index()
    {
        return response()->json(ChecklistFirma::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'user_id' => 'required|exists:users,id',
            'rol' => 'required|in:responsable,supervisor,entregador,receptor',
            'firma' => 'required|string',
            'fecha_firma' => 'required|date',
        ]);
        $firma = ChecklistFirma::create($request->all());
        return response()->json($firma, 201);
    }

    public function show($id)
    {
        $firma = ChecklistFirma::findOrFail($id);
        return response()->json($firma);
    }

    public function update(Request $request, $id)
    {
        $firma = ChecklistFirma::findOrFail($id);
        $firma->update($request->all());
        return response()->json($firma);
    }

    public function destroy($id)
    {
        $firma = ChecklistFirma::findOrFail($id);
        $firma->delete();
        return response()->json(['message' => 'Firma eliminada.']);
    }
}
