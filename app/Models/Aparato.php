<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aparato extends Model
{
    protected $fillable = [
        'nombre',
        'modelo',
        'marca',
        'stock',
        'precio',
        'imagen',
        'tipo', // ✅ AÑADIDO
    ];
}
