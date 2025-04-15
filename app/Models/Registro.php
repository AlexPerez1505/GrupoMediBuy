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
        'evidencia1',
        'evidencia2',
        'evidencia3',
        'video',
        'documentoPDF',
        'observaciones',
        'firma_digital',
        'user_name',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'ultimo_mantenimiento' => 'date',
        'proximo_mantenimiento' => 'date',
    ];

    // Relación correcta con ProcesoEquipo
    public function procesos()
    {
        return $this->hasMany(ProcesoEquipo::class, 'registro_id', 'id');
    }
    public function fichaTecnica()
{
    return $this->belongsTo(FichaTecnica::class, 'ficha_tecnica_id');
}

}
