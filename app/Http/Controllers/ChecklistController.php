<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use App\Models\ChecklistIngenieria;
use App\Models\ChecklistEmbalaje;
use App\Models\ChecklistEntrega;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Fpdi;
use Barryvdh\DomPDF\Facade\Pdf;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * PASO 0: Bienvenida/estado (wizard general del checklist)
     */
  public function wizard($ventaId)
{
    $venta = \App\Models\Venta::findOrFail($ventaId);

    // Checklist global (UN SOLO checklist por venta)
    $checklist = \App\Models\Checklist::firstOrCreate([
        'venta_id' => $ventaId
    ]);

    // Etapas
    $ingenieria = $checklist->ingenieria ?? null;
    $embalaje = $checklist->embalaje ?? null;
    $entrega = $checklist->entrega ?? null;

    if (!$ingenieria) {
        $progresoNombre = "Ingeniería";
        $progresoPorc = 0;
        $rutaSiguiente = 'checklists.ingenieria';
    } elseif (!$embalaje) {
        $progresoNombre = "Embalaje";
        $progresoPorc = 50;
        $rutaSiguiente = 'checklists.embalaje';
    } elseif (!$entrega) {
        $progresoNombre = "Entrega";
        $progresoPorc = 100;
        $rutaSiguiente = 'checklists.entrega';
    } else {
        $progresoNombre = "Checklist Finalizado";
        $progresoPorc = 100;
        $rutaSiguiente = null;
    }

    // Aquí agregamos $checklist al compact
    return view('checklists.wizard', compact(
        'venta',
        'progresoNombre',
        'progresoPorc',
        'rutaSiguiente',
        'checklist'  // <--- IMPORTANTE
    ));
}

    /**
     * PASO 1: Vista de Ingeniería
     */
    public function ingenieria($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);

        $productos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->where('venta_productos.venta_id', $ventaId)
            ->select('venta_productos.*', 'productos.tipo_equipo', 'productos.modelo', 'productos.marca')
            ->get();

        return view('checklists.ingenieria', compact('venta', 'productos'));
    }

    /**
     * Guardar paso de Ingeniería
     */
    public function guardarIngenieria(Request $request, $ventaId)
    {
        $userId = auth()->id();
        if (!$userId) abort(403, 'No autenticado');

        $checklist = Checklist::firstOrCreate([
            'venta_id' => $ventaId,
        ]);

        // Crear registro en checklist_ingenieria
        $ingenieria = new ChecklistIngenieria();
        $ingenieria->checklist_id = $checklist->id;
        $ingenieria->user_id = $userId;
        $ingenieria->componentes = json_encode($request->input('componentes', []));
        $ingenieria->incidente = $request->input('ingenieria_incidente', null);

        // Guardar firmas
        foreach(['firma_responsable', 'firma_supervisor'] as $campo) {
            if ($request->filled($campo)) {
                $imgData = $request->input($campo);
                $imgName = 'firmas/' . Str::uuid() . '.png';
                $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));
                Storage::disk('public')->put($imgName, $img);
                $ingenieria[$campo] = $imgName;
            }
        }

        // Evidencias
        if ($request->hasFile('evidencias')) {
            $paths = [];
            foreach ($request->file('evidencias') as $file) {
                $paths[] = $file->store('ingenieria_evidencias', 'public');
            }
            $ingenieria->evidencias = json_encode($paths);
        }

        $ingenieria->save();

        return redirect()->route('checklists.wizard', $ventaId)
            ->with('success', 'Paso de Ingeniería guardado.');
    }

    /**
     * PASO 2: Vista de Embalaje
     */
    public function embalaje($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);

        $productos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->where('venta_productos.venta_id', $ventaId)
            ->select('venta_productos.*', 'productos.tipo_equipo', 'productos.modelo', 'productos.marca')
            ->get();

        $checklist = Checklist::where('venta_id', $ventaId)->first();
        $ingenieria = $checklist ? $checklist->ingenieria : null;
        $embalaje = $checklist ? $checklist->embalaje : null;

        // Variables para la vista
        $firmaGuardada = $embalaje->firma_responsable ?? null;
        $reporteIngeniero = $ingenieria->incidente ?? null;
        $ingenieriaComponentes = $ingenieria ? json_decode($ingenieria->componentes, true) : null;
        $checklistEmbalaje = $embalaje ?? null;

        return view('checklists.embalaje', compact(
            'venta', 'productos', 'firmaGuardada', 'reporteIngeniero', 'checklistEmbalaje', 'ingenieria', 'ingenieriaComponentes', 'checklist'
        ));
    }

    /**
     * Guardar paso de Embalaje
     */
    public function guardarEmbalaje(Request $request, $ventaId)
    {
        $userId = auth()->id();
        if (!$userId) abort(403, 'No autenticado');

        $checklist = Checklist::firstOrCreate([
            'venta_id' => $ventaId,
        ]);

        $embalaje = new ChecklistEmbalaje();
        $embalaje->checklist_id = $checklist->id;
        $embalaje->user_id = $userId;
        $embalaje->componentes = json_encode($request->input('componentes', []));
        $embalaje->observaciones = $request->input('embalaje_observacion', null);

        // Guardar firmas
        foreach(['firma_responsable', 'firma_supervisor'] as $campo) {
            if ($request->filled($campo)) {
                $imgData = $request->input($campo);
                $imgName = 'firmas/' . Str::uuid() . '.png';
                $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));
                Storage::disk('public')->put($imgName, $img);
                $embalaje[$campo] = $imgName;
            }
        }

        // Evidencias
        if ($request->hasFile('evidencias')) {
            $paths = [];
            foreach ($request->file('evidencias') as $file) {
                $paths[] = $file->store('embalaje_evidencias', 'public');
            }
            $embalaje->evidencias = json_encode($paths);
        }

        $embalaje->save();

        return redirect()->route('checklists.wizard', $ventaId)
            ->with('success', 'Paso de Embalaje guardado.');
    }

public function entrega($ventaId)
{
    $venta = \App\Models\Venta::findOrFail($ventaId);

    // Checklist principal
    $checklist = Checklist::where('venta_id', $ventaId)->first();
    $ingenieria = $checklist ? ChecklistIngenieria::where('checklist_id', $checklist->id)->first() : null;
    $embalaje   = $checklist ? ChecklistEmbalaje::where('checklist_id', $checklist->id)->first() : null;

    // Productos de la venta
    $productos = \DB::table('venta_productos')
        ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
        ->where('venta_productos.venta_id', $ventaId)
        ->select('venta_productos.*', 'productos.tipo_equipo', 'productos.modelo', 'productos.marca', 'productos.id')
        ->get();

    // Verifica si existe un checklist y genera QR dinámico
    $qrHtml = null;
    if ($checklist) {
        $urlRecepcion = url('/recepcion-hospital/' . $checklist->id);
        $qrHtml = QrCode::size(180)->generate($urlRecepcion);
    }

    return view('checklists.entrega', compact(
        'venta', 'productos', 'checklist', 'ingenieria', 'embalaje', 'qrHtml'
    ));
}

public function guardarEntrega(Request $request, $ventaId)
{
    $userId = auth()->id();
    if (!$userId) abort(403, 'No autenticado');

    // Encuentra o crea el checklist principal
    $checklist = Checklist::firstOrCreate([
        'venta_id' => $ventaId,
    ]);

    // Solo debe existir una entrega por checklist
    $entrega = \App\Models\ChecklistEntrega::firstOrNew([
        'checklist_id' => $checklist->id,
    ]);
    $entrega->user_id = $userId;
    $entrega->datos_entrega = json_encode($request->except(['_token', 'firma_cliente', 'firma_entrega', 'evidencias']));
    $entrega->observaciones = $request->input('comentario_final', null);

    // Guardar firmas (opcional)
    foreach(['firma_cliente', 'firma_entrega'] as $campo) {
        if ($request->filled($campo)) {
            $imgData = $request->input($campo);
            $imgName = 'firmas/' . \Str::uuid() . '.png';
            $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));
            \Storage::disk('public')->put($imgName, $img);
            $entrega->$campo = $imgName;
        }
    }

    // Evidencias de archivos
    if ($request->hasFile('evidencias')) {
        $paths = [];
        foreach ($request->file('evidencias') as $file) {
            $paths[] = $file->store('entrega_evidencias', 'public');
        }
        $entrega->evidencias = json_encode($paths);
    }

    $entrega->save();

    return redirect()->route('checklists.wizard', $ventaId)
        ->with('success','Checklist completado');
}
// GET: Mostrar el formulario de recepción hospitalaria
public function recepcionHospital($checklistId)
{
    $checklist = \App\Models\Checklist::findOrFail($checklistId);

    // Embalaje para los componentes
    $embalaje = \App\Models\ChecklistEmbalaje::where('checklist_id', $checklistId)->first();
    $productos = \DB::table('venta_productos')
        ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
        ->where('venta_productos.venta_id', $checklist->venta_id)
        ->select('venta_productos.*', 'productos.tipo_equipo', 'productos.modelo', 'productos.marca', 'productos.id')
        ->get();

    $componentes = [];
    if ($embalaje && $embalaje->componentes) {
        $componentes = is_array($embalaje->componentes)
            ? $embalaje->componentes
            : json_decode($embalaje->componentes, true);
    }

    return view('checklists.recepcion-hospital', compact('checklist', 'productos', 'componentes'));
}


// POST: Guardar recepción hospitalaria
public function guardarRecepcionHospital(Request $request, $checklistId)
{
    $request->validate([
        'nombre_responsable' => 'required|string|max:191',
        'firma_recepcion'    => 'required|string',
        'checklist'          => 'required|array',
    ]);

    $checklist = \App\Models\Checklist::findOrFail($checklistId);

    // Crear recepción hospitalaria
    $recepcion = new \App\Models\ChecklistRecepcion();
    $recepcion->checklist_id = $checklist->id;
    $recepcion->nombre_responsable = $request->input('nombre_responsable');
    $recepcion->checklist = $request->input('checklist');
    $recepcion->observaciones = $request->input('observaciones');

    // Firma
    if ($request->filled('firma_recepcion')) {
        $imgData = $request->input('firma_recepcion');
        $imgName = 'firmas/' . \Str::uuid() . '.png';
        $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));
        \Storage::disk('public')->put($imgName, $img);
        $recepcion->firma = $imgName;
    }

    // Evidencias
    if ($request->hasFile('evidencias')) {
        $paths = [];
        foreach ($request->file('evidencias') as $file) {
            $paths[] = $file->store('recepcion_evidencias', 'public');
        }
        $recepcion->evidencias = $paths;
    }

    $recepcion->save();

    return back()->with('success', '¡Recepción registrada correctamente!');
}

public function descargarPdf($checklistId)
{
    $checklist = \App\Models\Checklist::findOrFail($checklistId);

    // Relacionados
    $venta      = $checklist->venta ?? null;
    $ingenieria = $checklist->ingenieria ?? null;
    $embalaje   = $checklist->embalaje ?? null;
    $recepcion  = $checklist->recepcion ?? null;
    $entrega    = $checklist->entrega ?? null; 

    // Productos
    $productos = [];
    if ($venta) {
        $productos = \DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->where('venta_productos.venta_id', $venta->id)
            ->select(
                'venta_productos.*',
                'productos.tipo_equipo',
                'productos.modelo',
                'productos.marca',
                'productos.id'
            )
            ->get();
    }

    // Pásalo todo al compact
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'checklists.pdf-reporte',
        compact(
            'checklist',
            'venta',
            'ingenieria',
            'embalaje',
            'recepcion',
            'entrega',    // 👈🏻 Aquí va la nueva variable
            'productos'
        )
    )->setPaper('a4', 'portrait');

    $nombre = 'Checklist_Reporte_' . ($venta->folio ?? $checklist->id) . '.pdf';
    return $pdf->download($nombre);
}


}
