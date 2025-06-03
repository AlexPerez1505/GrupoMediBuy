<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Registro;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PrestamoController extends Controller
{
    // Mostrar todos los préstamos
    public function index()
    {
        $prestamos = Prestamo::with('registro', 'cliente')->latest()->get();
        return view('prestamos.index', compact('prestamos'));
    }

    // Mostrar formulario para crear préstamo
    public function create()
    {
        $registros = Registro::select('id', 'numero_serie', 'subtipo_equipo', 'marca', 'modelo')->get();
        $clientes = Cliente::all();
        return view('prestamos.create', compact('registros', 'clientes'));
    }
    

    // Guardar nuevo préstamo
    public function store(Request $request)
    {
        $request->validate([
            'registro_id' => 'required|exists:registros,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion_estimada' => 'required|date|after_or_equal:fecha_prestamo',
            'fecha_devolucion_real' => 'nullable|date|after_or_equal:fecha_prestamo',
            'estado' => 'required|in:activo,devuelto,retrasado,cancelado,vendido',
            'condiciones_prestamo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firmaDigital' => 'nullable|string',
        ]);

        $data = $request->except('firmaDigital');
        $data['user_name'] = Auth::user()->name ?? 'Desconocido';

        // Procesar firma digital
        if ($request->firmaDigital) {
            $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $request->firmaDigital);
            $decodedImage = base64_decode($base64Image);

            if ($decodedImage !== false) {
                $nombreFirma = 'firma_' . time() . '.png';
                Storage::put('public/firmas/' . $nombreFirma, $decodedImage);
                $data['firmaDigital'] = Storage::url('firmas/' . $nombreFirma);
                Log::info('Firma digital guardada correctamente.', ['ruta' => $data['firmaDigital']]);
            } else {
                Log::error('Error al decodificar la firma.');
            }
        }

        Prestamo::create($data);

        return redirect()->route('prestamos.index')->with('success', 'Préstamo registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $registros = Registro::all();
        $clientes = Cliente::all();
        return view('prestamos.edit', compact('prestamo', 'registros', 'clientes'));
    }

    // Actualizar préstamo
    public function update(Request $request, $id)
    {
        $prestamo = Prestamo::findOrFail($id);

        $request->validate([
            'registro_id' => 'required|exists:registros,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion_estimada' => 'required|date|after_or_equal:fecha_prestamo',
            'fecha_devolucion_real' => 'nullable|date|after_or_equal:fecha_prestamo',
            'estado' => 'required|in:activo,devuelto,retrasado,cancelado,vendido',
            'condiciones_prestamo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firmaDigital' => 'nullable|string',
        ]);

        $data = $request->except('firmaDigital');
        $data['user_name'] = Auth::user()->name ?? $prestamo->user_name;

        // Procesar firma digital si se envió una nueva
        if ($request->firmaDigital) {
            $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $request->firmaDigital);
            $decodedImage = base64_decode($base64Image);

            if ($decodedImage !== false) {
                $nombreFirma = 'firma_' . time() . '.png';
                Storage::put('public/firmas/' . $nombreFirma, $decodedImage);
                $data['firmaDigital'] = Storage::url('firmas/' . $nombreFirma);
                Log::info('Firma digital actualizada correctamente.', ['ruta' => $data['firmaDigital']]);
            } else {
                Log::error('Error al decodificar la firma en actualización.');
            }
        }

        $prestamo->update($data);

        return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado correctamente.');
    }

    // Eliminar préstamo
    public function destroy($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $prestamo->delete();

        return redirect()->route('prestamos.index')->with('success', 'Préstamo eliminado correctamente.');
    }
}
