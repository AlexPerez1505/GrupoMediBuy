<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicio';

    protected $fillable = [
        'estado_id',
        'tipo_equipo',
        'subtipo_equipo',
        'numero_serie',
        'marca',
        'modelo',
        'año',
        'descripcion',
        'fecha_adquisicion',
        'evidencia1',
        'evidencia2',
        'evidencia3',
        'video',
        'observaciones',
        'user_name'
    ];
}
