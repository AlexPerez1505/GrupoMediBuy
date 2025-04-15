<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoEquipo extends Model
{
    use HasFactory;

    protected $table = 'procesos_equipos';

    protected $fillable = [
        'registro_id',
        'tipo_proceso',
        'descripcion_proceso',
        'evidencia1',
        'evidencia2',
        'evidencia3',
        'video',
        'documento_pdf', // Se añade el campo para almacenar el PDF
        'ficha_tecnica_id', // Debe estar aquí
        'defectos', // Agregar defectos a $fillable
    ];

    // Relación: un proceso pertenece a un registro
    public function registro()
    {
        return $this->belongsTo(Registro::class, 'registro_id', 'id');
    }
    // En el modelo ProcesoEquipo o el que sea
public function fichaTecnica()
{
    return $this->belongsTo(FichaTecnica::class, 'ficha_tecnica_id'); // 'ficha_tecnica_id' es el campo en 'procesos_equipos' que guarda el ID de la ficha
}

}
