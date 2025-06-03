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
        'firma_digital', // <-- ESTE ES EL IMPORTANTE
        'observaciones',
        'user_name'
    ];
        // Relación con los movimientos
        public function movimientos()
        {
            return $this->hasMany(Movimiento::class);
        }
}
