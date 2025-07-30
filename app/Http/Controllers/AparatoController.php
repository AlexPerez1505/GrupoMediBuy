<?php

namespace App\Http\Controllers;

use App\Models\Aparato;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AparatoController extends Controller
{
    /**
     * Mostrar lista de aparatos paginados.
     */
    public function index()
    {
        $aparatos = Aparato::orderBy('nombre')->paginate(15);
        return view('aparatos.index', compact('aparatos'));
    }

    /**
     * Formulario de creación de aparato.
     */
    public function create()
    {
        return view('aparatos.create');
    }

    /**
     * Almacenar un nuevo aparato.
     */
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
            $data['imagen'] = $request->file('imagen')->store('aparatos', 'public');
        }

        Aparato::create($data);

        return redirect()
            ->route('aparatos.index')
            ->with('success', 'Aparato creado');
    }

    /**
     * Mostrar los detalles de un aparato.
     */
    public function show(Aparato $aparato)
    {
        return view('aparatos.show', compact('aparato'));
    }

    /**
     * Formulario de edición de aparato.
     */
    public function edit(Aparato $aparato)
    {
        return view('aparatos.edit', compact('aparato'));
    }

    /**
     * Actualizar datos de un aparato.
     */
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
            // Opcional: eliminar imagen anterior con Storage::delete(...)
            $data['imagen'] = $request->file('imagen')->store('aparatos', 'public');
        }

        $aparato->update($data);

        return redirect()
            ->route('aparatos.index')
            ->with('success', 'Aparato actualizado');
    }

    /**
     * Eliminar un aparato.
     */
    public function destroy(Aparato $aparato)
    {
        $aparato->delete();
        return back()->with('success', 'Aparato eliminado');
    }

    /**
     * Endpoint AJAX: devuelve los ítems de checklist agrupados por categoría.
     */
    public function checklistItems(Aparato $aparato): JsonResponse
    {
        $items = $aparato
            ->checklistItems()           // Relación hasMany ChecklistItem
            ->with('categoria')          // Cargamos la categoría
            ->get()
            ->groupBy(fn($item) => $item->categoria->nombre);

        return response()->json($items);
    }
}
