<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Cliente;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function store(Request $request)
    {
        Nota::create($request->all());
        return redirect()->back()->with('success', 'Nota guardada.');
    }

    public function destroy(Nota $nota)
    {
        $nota->delete();
        return redirect()->back()->with('success', 'Nota eliminada.');
    }
}
