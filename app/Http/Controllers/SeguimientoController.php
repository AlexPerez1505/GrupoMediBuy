<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Seguimiento;
use App\Models\Cliente;

class SeguimientoController extends Controller
{
    public function index($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);
        $seguimientos = $cliente->seguimientos;

        return view('seguimientos.index', compact('cliente', 'seguimientos'));
    }

    public function store(Request $request, $clienteId)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
        ]);

        $cliente = Cliente::findOrFail($clienteId);

        $cliente->seguimientos()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_seguimiento' => $request->fecha, // ✅ CORREGIDO
        ]);

        return redirect()->back()->with('success', 'Seguimiento guardado.');
    }

    public function destroy($id)
    {
        $seguimiento = Seguimiento::findOrFail($id);
        $seguimiento->delete();

        return redirect()->back()->with('success', 'Seguimiento eliminado.');
    }
public function completar($id)
{
    $seguimiento = Seguimiento::findOrFail($id);
    $seguimiento->completado = now(); // Esto guarda la fecha y hora actuales
    $seguimiento->save();

    return redirect()->back()->with('success', 'Seguimiento marcado como completado.');
}
}
