<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'lugar',
        'casetas',
        'gasolina',
        'viaticos',
        'adicional',
        'descripcion',
        'total',
    ];
}
