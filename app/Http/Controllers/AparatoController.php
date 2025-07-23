<?php

namespace App\Http\Controllers;

use App\Models\Aparato;
use Illuminate\Http\Request;

class AparatoController extends Controller
{
    public function index()
    {
        $aparatos = Aparato::orderBy('nombre')->paginate(15);
        return view('aparatos.index', compact('aparatos'));
    }

    public function create()
    {
        return view('aparatos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:255',
            'modelo'  => 'nullable|string|max:255',
            'marca'   => 'nullable|string|max:255',
            'stock'   => 'required|integer|min:1',
            'precio'  => 'required|numeric|min:0',
            'imagen'  => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('aparatos','public');
        }

        Aparato::create($data);
        return redirect()->route('aparatos.index')->with('success','Aparato creado');
    }

    public function show(Aparato $aparato)
    {
        return view('aparatos.show', compact('aparato'));
    }

    public function edit(Aparato $aparato)
    {
        return view('aparatos.edit', compact('aparato'));
    }

    public function update(Request $request, Aparato $aparato)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:255',
            'modelo'  => 'nullable|string|max:255',
            'marca'   => 'nullable|string|max:255',
            'stock'   => 'required|integer|min:1',
            'precio'  => 'required|numeric|min:0',
            'imagen'  => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('imagen')) {
            // opcionalmente borrar imagen anterior...
            $data['imagen'] = $request->file('imagen')->store('aparatos','public');
        }

        $aparato->update($data);
        return redirect()->route('aparatos.index')->with('success','Aparato actualizado');
    }

    public function destroy(Aparato $aparato)
    {
        $aparato->delete();
        return back()->with('success','Aparato eliminado');
    }
}
