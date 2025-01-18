<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/inventario', [RegistroController::class, 'mostrarProductos'])->name('inventario');

// Ruta para guardar el registro
Route::post('/registro/guardar', [RegistroController::class, 'guardarRegistro'])->name('registro.guardar');
// Ruta para obtener los todos los detalles en mi vista de inventario 
Route::get('/obtener-detalles/{id}', [RegistroController::class, 'obtenerDetalles']);

 