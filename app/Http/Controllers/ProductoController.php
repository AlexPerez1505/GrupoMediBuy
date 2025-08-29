<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Paquete;
use App\Models\Familia; // ← USAMOS FAMILIAS
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('tipo_equipo', 'asc')->get();
        return view('cotizaciones', compact('productos'));
    }

    public function create()
    {
        $familias = Familia::orderBy('nombre')->get();
        return view('productos.create', compact('familias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_equipo' => 'required|string|max:255',
            'modelo'      => 'required|string|max:255',
            'marca'       => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'imagen'      => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            'familias'    => 'nullable|array',
            'familias.*'  => 'exists:familias,id',
        ]);

        $stock = $request->input('stock', 1);
        $imagenPath = $request->hasFile('imagen')
            ? $request->file('imagen')->store('productos', 'public')
            : null;

        $producto = Producto::create([
            'tipo_equipo' => $validated['tipo_equipo'],
            'stock'       => $stock,
            'modelo'      => $validated['modelo'],
            'marca'       => $validated['marca'],
            'precio'      => $validated['precio'],
            'imagen'      => $imagenPath,
        ]);

        // Sync familias
        $producto->familias()->sync($request->input('familias', []));

        if ($request->ajax()) {
            return response()->json([
                'message'  => 'Producto creado exitosamente',
                'producto' => $producto->load('familias')
            ]);
        }

        return redirect()->route('productos.cards')->with('success', 'Producto creado exitosamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $productos = Producto::where('tipo_equipo', 'like', "%$search%")
            ->orWhere('modelo', 'like', "%$search%")
            ->orWhere('marca',  'like', "%$search%")
            ->orderBy('tipo_equipo', 'asc')
            ->get()
            ->map(function ($producto) {
                return [
                    'id'          => $producto->id,
                    'tipo_equipo' => strtoupper($producto->tipo_equipo),
                    'modelo'      => strtoupper($producto->modelo),
                    'marca'       => strtoupper($producto->marca),
                    'precio'      => $producto->precio,
                    'imagen'      => $producto->imagen,
                    'stock'       => $producto->stock,
                ];
            });

        return response()->json($productos);
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        $productos = Producto::where('tipo_equipo', 'like', '%' . $request->termino . '%')
                             ->orWhere('modelo', 'like', '%' . $request->termino . '%')
                             ->get();

        return response()->json($productos);
    }

    public function cardsVista()
    {
        // Productos con FAMILIAS para mostrar chips y mejorar búsqueda
        $productos = Producto::with('familias')->orderBy('tipo_equipo')->get();

        // Paquetes + sus productos (con precio)
        $paquetes = Paquete::withCount('productos')
            ->with(['productos' => fn($q) => $q->select('productos.*')])
            ->latest()
            ->get();

        return view('productos-cards', compact('productos', 'paquetes'));
    }

    public function edit($id)
    {
        $producto = Producto::with('familias')->findOrFail($id);
        $familias = Familia::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'familias'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'tipo_equipo' => 'required|string|max:255',
            'marca'       => 'nullable|string|max:255',
            'modelo'      => 'nullable|string|max:255',
            'precio'      => 'nullable|numeric',
            'imagen'      => 'nullable|image|max:2048',
            'familias'    => 'nullable|array',
            'familias.*'  => 'exists:familias,id',
        ]);

        $producto->fill([
            'tipo_equipo' => $request->tipo_equipo,
            'marca'       => $request->marca,
            'modelo'      => $request->modelo,
            'precio'      => $request->precio,
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();

        // Sync familias
        $producto->familias()->sync($request->input('familias', []));

        return redirect()->route('productos.cards')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.cards')->with('success', 'Producto eliminado correctamente');
    }
}
