<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Registro;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;



class CotizacionController extends Controller
{
    public function index()
    {
        return view('cotizaciones');
    }

    /**
     * Buscar registros en la base de datos por tipo_equipo, modelo o número de serie.
     */
    public function buscarRegistros(Request $request)
    {
        // Validar que se envíe el parámetro de búsqueda
        if (!$request->has('q') || empty($request->q)) {
            return response()->json(['message' => 'No se proporcionó una consulta'], 400);
        }

        Log::info('🔍 Búsqueda de registros con el término: ' . $request->q);

        // Realizar la búsqueda con LIKE y OR
        $registros = Registro::where('tipo_equipo', 'like', '%' . $request->q . '%')
            ->orWhere('modelo', 'like', '%' . $request->q . '%')
            ->orWhere('numero_serie', 'like', '%' . $request->q . '%')
            ->get();

        // Si no hay resultados, retornar mensaje
        if ($registros->isEmpty()) {
            return response()->json(['message' => 'No se encontraron registros'], 404);
        }

        return response()->json($registros);
    }

    public function guardarCotizacion(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'cliente' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.subtotal' => 'required|numeric|min:0',
        ]);

        try {
            // Crear la cotización
            $cotizacion = Cotizacion::create([
                'cliente' => $request->cliente,
                'telefono' => $request->telefono,
                'subtotal' => $request->subtotal,
                'iva' => $request->iva,
                'total' => $request->total,
            ]);

              // Agregar los productos a la cotización
        foreach ($request->productos as $producto) {
            $cotizacion->productos()->attach($producto['id'], [
                'cantidad' => $producto['cantidad'],
                'subtotal' => $producto['subtotal'],
            ]);
        }

        // Retornar la respuesta con la cotización y los productos
        return response()->json(['mensaje' => 'Cotización guardada con éxito', 'cotizacion' => $cotizacion]);

    } catch (\Exception $e) {
        Log::error("Error al guardar la cotización: " . $e->getMessage());
        return response()->json(['error' => 'Ocurrió un error al guardar la cotización'], 500);
    }
}


public function generarCotizacionPDF(Request $request)
{
    // Datos que recibes del frontend
    $cliente = $request->cliente;
    $telefono = $request->telefono;
    $subtotal = $request->subtotal;
    $iva = $request->iva;
    $total = $request->total;
    $productos = $request->productos;

    // Crea los datos que serán pasados a la vista
    $data = [
        'cliente' => $cliente,
        'telefono' => $telefono,
        'subtotal' => $subtotal,
        'iva' => $iva,
        'total' => $total,
        'productos' => $productos,
    ];

    // Cargar la vista y generar el PDF
    $pdf = PDF::loadView('cotizaciones.pdf', $data);

    // Descargar el archivo PDF
    return $pdf->download('cotizacion.pdf');
}



}

