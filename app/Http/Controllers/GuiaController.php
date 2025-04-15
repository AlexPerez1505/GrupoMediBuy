<?php

namespace App\Http\Controllers;

use App\Models\Guia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuiaController extends Controller {
    public function create() {
        return view('guias'); // Asegura que la vista esté en resources/views/guias.blade.php
    }
    
    public function store(Request $request) {
        $request->validate([
            'numero_rastreo' => 'required|max:14|unique:guias,numero_rastreo',
            'peso' => 'required|numeric',
            'fecha_recepcion' => 'required|date',
        ], [
            'numero_rastreo.unique' => 'Esta guía ya ha sido registrada.', // Mensaje personalizado
        ]);
    
        Guia::create($request->all());
    
        return redirect()->route('guias.create')->with('success', 'Guía registrada exitosamente.');
    }
    
}