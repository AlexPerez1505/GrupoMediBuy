<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';

    protected $fillable = [
        'tipo_equipo',
        'subtipo_equipo',
        'subtipo_equipo_otro',
        'numero_serie',
        'marca',
        'modelo',
        'anio',
        'descripcion',
        'estado_actual',
        'fecha_adquisicion',
        'ultimo_mantenimiento',
        'proximo_mantenimiento',
        'evidencia',
        'video',
        'documentoPDF',
        'observaciones',
        'firma_digital',
    ];

    protected $casts = [
        'evidencia' => 'array', 
        'fecha_adquisicion' => 'date',
        'ultimo_mantenimiento' => 'date',
        'proximo_mantenimiento' => 'date',
    ];
}
