<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // Listar todos los items
    public function index()
    {
        $items = Item::all();
        return view('items.index', compact('items'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('items.create');
    }

    // Guardar un nuevo item
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:100|unique:items',
        ]);

        Item::create([
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('items.index')->with('success', 'Item creado correctamente.');
    }

    // Mostrar detalle de un item
    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('items.show', compact('item'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('items.edit', compact('item'));
    }

    // Actualizar un item existente
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:100|unique:items,codigo,' . $item->id,
        ]);
        $item->update([
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
        ]);
        return redirect()->route('items.index')->with('success', 'Item actualizado correctamente.');
    }

    // Eliminar un item
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item eliminado correctamente.');
    }
}
