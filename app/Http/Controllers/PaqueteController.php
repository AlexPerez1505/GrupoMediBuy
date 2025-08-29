<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class PaqueteController extends Controller
{
    public function index()
    {
        $paquetes  = Paquete::with([
            'productos' => function ($q) {
                // IMPORTANTE: incluye precio
                $q->select(
                    'productos.id',
                    \DB::raw('productos.tipo_equipo as nombre'),
                    'productos.tipo_equipo',
                    'productos.marca',
                    'productos.modelo',
                    'productos.imagen',
                    'productos.precio'   // ← aquí está la clave
                );
            }
        ])->withCount('productos')->latest()->get();

        $productos = Producto::orderBy('tipo_equipo')->get();

        return view('cotizaciones', compact('paquetes', 'productos'));
    }

    public function getProductosDePaquete($id)
    {
        $paquete = Paquete::with([
            'productos' => function ($q) {
                $q->select(
                    'productos.id',
                    \DB::raw('productos.tipo_equipo as nombre'),
                    'productos.tipo_equipo',
                    'productos.marca',
                    'productos.modelo',
                    'productos.imagen',
                    'productos.precio'   // ← incluye precio
                );
            }
        ])->findOrFail($id);

        return response()->json([
            'productos' => $paquete->productos
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('search');

        $paquetes = Paquete::where('nombre', 'LIKE', "%{$query}%")
            ->with([
                'productos' => function ($q) {
                    $q->select(
                        'productos.id',
                        \DB::raw('productos.tipo_equipo as nombre'),
                        'productos.tipo_equipo',
                        'productos.marca',
                        'productos.modelo',
                        'productos.imagen',
                        'productos.precio'   // ← incluye precio
                    );
                }
            ])
            ->get();

        return response()->json($paquetes);
    }

    public function create()
    {
        $productos = Producto::orderBy('tipo_equipo')->get();
        return view('paquetes.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            'productos'   => 'required|array',
            'productos.*' => 'exists:productos,id',
            'cantidades'  => 'nullable|array',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('paquetes', 'public');
        }

        $paquete = Paquete::create([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'imagen'      => $data['imagen'] ?? null,
        ]);

        $sync = $this->buildSyncArray($data['productos'], $data['cantidades'] ?? null);
        $paquete->productos()->sync($sync);

        if ($request->ajax()) {
            // recarga productos con precio para el response
            $paquete->load(['productos' => function ($q) {
                $q->select('productos.*');
            }]);
            return response()->json([
                'message' => 'Paquete creado correctamente',
                'paquete' => $paquete
            ]);
        }

        return redirect()->route('productos.cards')->with('success', 'Paquete creado');
    }

    public function edit(Paquete $paquete)
    {
        $paquete->load('productos');
        $productos = Producto::orderBy('tipo_equipo')->get();
        return view('paquetes.edit', compact('paquete', 'productos'));
    }

    public function update(Request $request, Paquete $paquete)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            'productos'   => 'nullable|array',
            'productos.*' => 'exists:productos,id',
            'cantidades'  => 'nullable|array',
        ]);

        if ($request->hasFile('imagen')) {
            if ($paquete->imagen && Storage::disk('public')->exists($paquete->imagen)) {
                Storage::disk('public')->delete($paquete->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('paquetes', 'public');
        }

        $paquete->update([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? $paquete->descripcion,
            'imagen'      => $data['imagen'] ?? $paquete->imagen,
        ]);

        if (isset($data['productos'])) {
            $sync = $this->buildSyncArray($data['productos'], $data['cantidades'] ?? null);
            $paquete->productos()->sync($sync);
        }

        if ($request->ajax()) {
            $paquete->load(['productos' => function ($q) {
                $q->select('productos.*'); // incluye precio
            }]);
            return response()->json([
                'message' => 'Paquete actualizado',
                'paquete' => $paquete
            ]);
        }

        return redirect()->route('productos.cards')->with('success', 'Paquete actualizado');
    }

    public function destroy(Paquete $paquete)
    {
        $paquete->productos()->detach();

        if ($paquete->imagen && Storage::disk('public')->exists($paquete->imagen)) {
            Storage::disk('public')->delete($paquete->imagen);
        }

        $paquete->delete();

        return redirect()->route('productos.cards')->with('success', 'Paquete eliminado');
    }

    private function buildSyncArray(array $ids, ?array $cantidades): array
    {
        $sync = [];
        foreach (array_values($ids) as $i => $id) {
            $qty = 1;
            if (is_array($cantidades)) {
                if (array_key_exists($id, $cantidades)) {
                    $qty = (int) $cantidades[$id];
                } elseif (array_key_exists($i, $cantidades)) {
                    $qty = (int) $cantidades[$i];
                }
            }
            $sync[(int) $id] = ['cantidad' => max(1, $qty)];
        }
        return $sync;
    }
}
