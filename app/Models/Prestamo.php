<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'registro_id',
        'cliente_id',
        'fecha_prestamo',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'estado',
        'condiciones_prestamo',
        'observaciones',
        'user_name',
        'firmaDigital',
    ];

    public function registro()
    {
        return $this->belongsTo(Registro::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
