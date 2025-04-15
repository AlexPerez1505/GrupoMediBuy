<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Producto;

class PaqueteController extends Controller
{
    // Obtener todos los paquetes con sus productos
    public function index()
    {
        $paquetes = Paquete::with('productos')->get();
        $productos = Producto::all(); // Si ya los envías, ignora esta línea
        return view('cotizaciones', compact('paquetes', 'productos'));
        
    }
    public function getProductosDePaquete($id)
{
    $paquete = Paquete::with('productos')->findOrFail($id);

    return response()->json([
        'productos' => $paquete->productos
    ]);
}

    
public function search(Request $request)
{
    $query = $request->input('search');
    $paquetes = Paquete::where('nombre', 'LIKE', "%{$query}%")->with('productos')->get();
    
    return response()->json($paquetes);
}


    // Crear un nuevo paquete con productos
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'productos' => 'required|array',
            'productos.*' => 'exists:productos,id',
        ]);

        $paquete = Paquete::create(['nombre' => $request->nombre]);
        $paquete->productos()->attach($request->productos);

        return response()->json(['message' => 'Paquete creado correctamente', 'paquete' => $paquete]);
    }
}

