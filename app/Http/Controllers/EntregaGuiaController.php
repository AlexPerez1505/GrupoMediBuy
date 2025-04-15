<?php
namespace App\Http\Controllers;

use App\Models\EntregaGuia;
use App\Models\Guia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntregaGuiaController extends Controller {
    public function create() {
        // Obtener guías que NO tienen una entrega asociada
        $guias = Guia::whereDoesntHave('entrega')->get();
    
        return view('entrega', compact('guias'));
    }
    public function index()
    {
        // Obtener todas las entregas junto con la relación de guías
        $entregas = EntregaGuia::with('guia')->get();

        // Pasar los datos a la vista
        return view('entregas', compact('entregas'));
    }
    public function getGuias(Request $request) {
        $query = Guia::whereDoesntHave('entrega');
    
        if ($request->has('search')) {
            $query->where('numero_rastreo', 'like', '%' . $request->search . '%');
        }
    
        $guias = $query->orderBy('created_at', 'desc')->paginate(6);
    
        return response()->json($guias);
    }
    
    
    
    
    public function store(Request $request) {
        $request->validate([
            'guia_id' => 'required|exists:guias,id',
            'contenido' => 'required',
            'numero_serie' => 'required',
            'observaciones' => 'nullable',
            'destinatario' => 'required',
            'firmaDigital' => 'required',
            'imagen' => 'nullable|mimes:jpg,jpeg,png,gif,webp,heic,heif,bmp,tiff,svg', // Se aceptan más formatos de imagen sin límite de tamaño
        ]);
    
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes', 'public'); // Guarda en storage/app/public/imagenes
        }
    
        EntregaGuia::create([
            'guia_id' => $request->guia_id,
            'entregado_por' => Auth::user()->name,
            'fecha_entrega' => now(),
            'contenido' => $request->contenido,
            'numero_serie' => $request->numero_serie,
            'observaciones' => $request->observaciones,
            'destinatario' => $request->destinatario,
            'firmaDigital' => $request->firmaDigital,
            'imagen' => $imagenPath, // Guarda la ruta de la imagen
        ]);
    
        return redirect()->route('entrega.create')->with('success', 'Guía entregada.');
    }
}

