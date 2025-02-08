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
        $productos = Producto::all(); 

        // Pasar los productos a la vista 'cotizaciones'
        return view('cotizaciones', compact('productos'));  // 'cotizaciones' es el nombre de tu vista
    }
    public function store(Request $request)
    {
        $request->validate([
            'tipo_equipo' => 'required|string|max:255',
            'stock' => 'required|integer|min:1',  // Aseguramos que el stock no sea 0
            'modelo' => 'required|string|max:255',  // Ahora el modelo es obligatorio
            'marca' => 'required|string|max:255',   // Marca también es obligatorio
            'precio' => 'required|numeric|min:0',   // Precio sigue siendo obligatorio
            'imagen' => 'required|image|max:2048',  // Imagen también es obligatoria
        ]);
    
        // Si hay una imagen
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('productos', 'public');
        }
    
        // Crear el producto
        $producto = Producto::create([
            'tipo_equipo' => $request->tipo_equipo,
            'stock' => $request->stock,
            'modelo' => $request->modelo,
            'marca' => $request->marca,
            'precio' => $request->precio,
            'imagen' => $imagenPath ?? null,
        ]);
    
        return response()->json([
            'message' => 'Producto creado exitosamente',
            'producto' => $producto
        ]);
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

}
    