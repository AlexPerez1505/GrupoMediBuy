<?php
namespace App\Http\Controllers;

use App\Models\Camioneta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CamionetaController extends Controller
{
    // Muestra el formulario para agregar una nueva camioneta
    public function create()
    {
        return view('camionetas.agregar_camionetas');
    }

    // Guarda una nueva camioneta en la base de datos
   // Guarda una nueva camioneta en la base de datos
   public function store(Request $request)
   {
       // Validación de los datos del formulario
       $validated = $request->validate([
           'placa' => 'required|string|max:255',
           'vin' => 'required|string|max:255',
           'marca' => 'required|string|max:255',
           'modelo' => 'required|string|max:255',
           'anio' => 'required|integer|min:1900|max:'.date('Y'),
           'color' => 'required|string|max:255',
           'tipo_motor' => 'nullable|string|max:255',
           'capacidad_carga' => 'nullable|string|max:255',
           'tipo_combustible' => 'nullable|string|max:255',
           'fecha_adquisicion' => 'nullable|date',
           'ultimo_mantenimiento' => 'nullable|date',
           'proximo_mantenimiento' => 'nullable|date',
           'ultima_verificacion' => 'nullable|date',
           'proxima_verificacion' => 'nullable|date',
           'kilometraje' => 'nullable|integer',
           'rendimiento_litro' => 'nullable|numeric',
           'costo_llenado' => 'nullable|numeric',
           'fotos' => 'nullable|array|max:4',
           'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
           'tarjeta_circulacion' => 'nullable|mimes:pdf|max:5120',
           'verificacion' => 'nullable|mimes:pdf|max:5120',
           'tenencia' => 'nullable|mimes:pdf|max:5120',
           'seguro' => 'nullable|mimes:pdf|max:5120',
       ]);

   // Crear una nueva instancia del modelo Camioneta
$camioneta = new Camioneta();
$camioneta->placa = $validated['placa'];
$camioneta->vin = $validated['vin'];
$camioneta->marca = $validated['marca'];
$camioneta->modelo = $validated['modelo'];
$camioneta->anio = $validated['anio'];
$camioneta->color = $validated['color'];
$camioneta->tipo_motor = $validated['tipo_motor'];
$camioneta->capacidad_carga = $validated['capacidad_carga'];
$camioneta->tipo_combustible = $validated['tipo_combustible'];
$camioneta->fecha_adquisicion = $validated['fecha_adquisicion'];
$camioneta->ultimo_mantenimiento = $validated['ultimo_mantenimiento'];
$camioneta->proximo_mantenimiento = $validated['proximo_mantenimiento'];
$camioneta->ultima_verificacion = $validated['ultima_verificacion'];
$camioneta->proxima_verificacion = $validated['proxima_verificacion'];
$camioneta->kilometraje = $validated['kilometraje'];
$camioneta->rendimiento_litro = $validated['rendimiento_litro'];
$camioneta->costo_llenado = $validated['costo_llenado'];


       // Manejo de archivos de fotos
       if ($request->hasFile('fotos')) {
           $fotosPaths = [];
           foreach ($request->file('fotos') as $foto) {
               // Guardamos cada foto en 'public/fotos' y almacenamos la ruta
               $fotosPaths[] = $foto->store('public/fotos');
           }
           $camioneta->fotos = json_encode($fotosPaths); // Guardamos las rutas de las fotos
       }

       // Manejo de archivos PDF
       try {
           if ($request->hasFile('tarjeta_circulacion')) {
               $camioneta->tarjeta_circulacion = $request->file('tarjeta_circulacion')->store('public/documentos');
           }
           if ($request->hasFile('verificacion')) {
               $camioneta->verificacion = $request->file('verificacion')->store('public/documentos');
           }
           if ($request->hasFile('tenencia')) {
               $camioneta->tenencia = $request->file('tenencia')->store('public/documentos');
           }
           if ($request->hasFile('seguro')) {
               $camioneta->seguro = $request->file('seguro')->store('public/documentos');
           }
       } catch (\Exception $e) {
           return redirect()->route('camionetas.index')->with('error', 'Error al subir los archivos: ' . $e->getMessage());
       }

       // Guardamos la camioneta en la base de datos
       $camioneta->save();

       // Redirigir con un mensaje de éxito
       return redirect()->route('camionetas.index')->with('success', 'Camioneta registrada con éxito.');
   }

    // Mostrar todas las camionetas
    public function index()
    {
        $camionetas = Camioneta::all();
        return view('camionetas.index', compact('camionetas'));
    }

    // Mostrar los detalles de una camioneta
    public function show($id)
    {
        $camioneta = Camioneta::findOrFail($id);
        return view('camionetas.ver_camionetas', compact('camioneta')); 
    }

    // Editar los datos de una camioneta
    public function edit($id)
    {
        $camioneta = Camioneta::findOrFail($id);
        return view('camionetas.edit', compact('camioneta'));
    }

    // Actualizar los datos de la camioneta
public function update(Request $request, $id)
{
    $camioneta = Camioneta::findOrFail($id);

    // Validación de los datos (sin requerir los campos)
    $validated = $request->validate([
        'placa' => 'nullable|string|max:255',
        'vin' => 'nullable|string|max:255',
        'marca' => 'nullable|string|max:255',
        'modelo' => 'nullable|string|max:255',
        'anio' => 'nullable|integer|min:1900|max:' . date('Y'),
        'color' => 'nullable|string|max:255',
        'tipo_motor' => 'nullable|string|max:255',
        'capacidad_carga' => 'nullable|string|max:255',
        'tipo_combustible' => 'nullable|string|max:255',
        'fecha_adquisicion' => 'nullable|date',
        'ultimo_mantenimiento' => 'nullable|date',
        'proximo_mantenimiento' => 'nullable|date',
        'ultima_verificacion' => 'nullable|date',
           'proxima_verificacion' => 'nullable|date',
           'kilometraje' => 'nullable|integer',
           'rendimiento_litro' => 'nullable|numeric',
           'costo_llenado' => 'nullable|numeric',
        'fotos' => 'nullable|array|max:4',
        'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'tarjeta_circulacion' => 'nullable|mimes:pdf|max:5120',
        'verificacion' => 'nullable|mimes:pdf|max:5120',
        'tenencia' => 'nullable|mimes:pdf|max:5120',
        'seguro' => 'nullable|mimes:pdf|max:5120',
    ]);

    // Actualizar los datos de la camioneta solo si están presentes
   // Actualizar los datos de la camioneta solo si están presentes
if ($request->has('placa')) {
    $camioneta->placa = $validated['placa'];
}
if ($request->has('vin')) {
    $camioneta->vin = $validated['vin'];
}
if ($request->has('marca')) {
    $camioneta->marca = $validated['marca'];
}
if ($request->has('modelo')) {
    $camioneta->modelo = $validated['modelo'];
}
if ($request->has('anio')) {
    $camioneta->anio = $validated['anio'];
}
if ($request->has('color')) {
    $camioneta->color = $validated['color'];
}
if ($request->has('tipo_motor')) {
    $camioneta->tipo_motor = $validated['tipo_motor'];
}
if ($request->has('capacidad_carga')) {
    $camioneta->capacidad_carga = $validated['capacidad_carga'];
}
if ($request->has('tipo_combustible')) {
    $camioneta->tipo_combustible = $validated['tipo_combustible'];
}
if ($request->has('fecha_adquisicion')) {
    $camioneta->fecha_adquisicion = $validated['fecha_adquisicion'];
}
if ($request->has('ultimo_mantenimiento')) {
    $camioneta->ultimo_mantenimiento = $validated['ultimo_mantenimiento'];
}
if ($request->has('proximo_mantenimiento')) {
    $camioneta->proximo_mantenimiento = $validated['proximo_mantenimiento'];
}
if ($request->has('ultima_verificacion')) {
    $camioneta->ultima_verificacion = $validated['ultima_verificacion'];
}
if ($request->has('proxima_verificacion')) {
    $camioneta->proxima_verificacion = $validated['proxima_verificacion'];
}
if ($request->has('kilometraje')) {
    $camioneta->kilometraje = $validated['kilometraje'];
}
if ($request->has('rendimiento_litro')) {
    $camioneta->rendimiento_litro = $validated['rendimiento_litro'];
}
if ($request->has('costo_llenado')) {
    $camioneta->costo_llenado = $validated['costo_llenado'];
}


    // Manejo de fotos nuevas
    if ($request->hasFile('fotos')) {
        $fotosPaths = [];
        foreach ($request->file('fotos') as $foto) {
            $fotosPaths[] = $foto->store('public/fotos');
        }
        $camioneta->fotos = json_encode($fotosPaths); // Guardamos las rutas de las fotos
    }

    // Manejo de documentos PDF nuevos
    if ($request->hasFile('tarjeta_circulacion')) {
        $camioneta->tarjeta_circulacion = $request->file('tarjeta_circulacion')->store('public/documentos');
    }
    if ($request->hasFile('verificacion')) {
        $camioneta->verificacion = $request->file('verificacion')->store('public/documentos');
    }
    if ($request->hasFile('tenencia')) {
        $camioneta->tenencia = $request->file('tenencia')->store('public/documentos');
    }
    if ($request->hasFile('seguro')) {
        $camioneta->seguro = $request->file('seguro')->store('public/documentos');
    }

    // Guardamos la camioneta actualizada
    $camioneta->save();

    // Redirigir con un mensaje de éxito
    return redirect()->route('camionetas.index')->with('success', 'Camioneta actualizada con éxito.');
}


    // Eliminar una camioneta
    public function destroy($id)
    {
        $camioneta = Camioneta::findOrFail($id);
        $camioneta->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('camionetas.index')->with('success', 'Camioneta eliminada con éxito.');
    }
}
