<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        // Obtener todos los productos registrados
         $productos = Producto::orderBy('tipo_equipo', 'asc')->get();

        // Pasar los productos a la vista 'cotizaciones'
        return view('cotizaciones', compact('productos'));  // 'cotizaciones' es el nombre de tu vista
    }
        public function create()
    {
        return view('productos.create');
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'tipo_equipo' => 'required|string|max:255',
        'modelo'      => 'required|string|max:255',
        'marca'       => 'required|string|max:255',
        'precio'      => 'required|numeric|min:0',
        'imagen'      => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
    ]);

    $stock = $request->input('stock', 1);

    $imagenPath = null;
    if ($request->hasFile('imagen')) {
        $imagenPath = $request->file('imagen')->store('productos', 'public');
    }

    $producto = Producto::create([
        'tipo_equipo' => $validated['tipo_equipo'],
        'stock'       => $stock,
        'modelo'      => $validated['modelo'],
        'marca'       => $validated['marca'],
        'precio'      => $validated['precio'],
        'imagen'      => $imagenPath,
    ]);

    if ($request->ajax()) {
        return response()->json([
            'message' => 'Producto creado exitosamente',
            'producto' => $producto
        ]);
    }

    return redirect('/productos/cards')->with('success', 'Producto creado exitosamente');
}

    public function search(Request $request)
    {
        $search = $request->input('search');
    
        $productos = Producto::where('tipo_equipo', 'like', "%$search%")
            ->orWhere('modelo', 'like', "%$search%")
            ->orWhere('marca', 'like', "%$search%")
            ->orderBy('tipo_equipo', 'asc') // Agregar orden alfabético
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'tipo_equipo' => strtoupper($producto->tipo_equipo),
                    'modelo' => strtoupper($producto->modelo),
                    'marca' => strtoupper($producto->marca),
                    'precio' => $producto->precio,
                    'imagen' => $producto->imagen,
                    'stock' => $producto->stock,
                ];
            });
    
        return response()->json($productos);
    }
    public function buscar(Request $request)
    {
        // Validar el término de búsqueda
        $request->validate([
            'termino' => 'required|string|max:255',
        ]);
    
        // Buscar productos que coincidan con el término de búsqueda
        $productos = Producto::where('tipo_equipo', 'like', '%' . $request->termino . '%')
                             ->orWhere('modelo', 'like', '%' . $request->termino . '%')
                             ->get();
    
        // Retornar los productos como respuesta JSON
        return response()->json($productos);
    }
public function cardsVista()
{
    $productos = Producto::orderBy('tipo_equipo')->get();
    return view('productos-cards', compact('productos'));
}
public function edit($id)
{
    // Buscar el producto
    $producto = Producto::findOrFail($id);

    // Retornar la vista de edición
    return view('productos.edit', compact('producto'));
}
public function update(Request $request, $id)
{
    $producto = Producto::findOrFail($id);

    $request->validate([
        'tipo_equipo' => 'required|string|max:255',
        'marca' => 'nullable|string|max:255',
        'modelo' => 'nullable|string|max:255',
        'precio' => 'nullable|numeric',
        'imagen' => 'nullable|image|max:2048',
    ]);

    $producto->tipo_equipo = $request->tipo_equipo;
    $producto->marca = $request->marca;
    $producto->modelo = $request->modelo;
    $producto->precio = $request->precio;

    if ($request->hasFile('imagen')) {
        $path = $request->file('imagen')->store('productos', 'public');
        $producto->imagen = $path;
    }

    $producto->save();

return redirect()->route('productos.cards')->with('success', 'Producto actualizado correctamente');

}
public function destroy($id)
{
    $producto = Producto::findOrFail($id);

    // Si el producto tiene imagen en storage, la borramos
    if ($producto->imagen && \Storage::disk('public')->exists($producto->imagen)) {
        \Storage::disk('public')->delete($producto->imagen);
    }

    $producto->delete();

    return redirect()->route('productos.cards')->with('success', 'Producto eliminado correctamente');
}



}
    