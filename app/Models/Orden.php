<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';

    protected $fillable = [
        'cliente_id',
        'aparato_id',           // <-- cambió aquí
        'fecha_entrada',
        'fecha_mantenimiento',
        'proximo_mantenimiento',
        'checklist',
    ];

    protected $casts = [
        'checklist' => 'array',
        'fecha_entrada' => 'date',
        'fecha_mantenimiento' => 'date',
        'proximo_mantenimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

public function aparato()
{
    return $this->belongsTo(Aparato::class, 'aparato_id');
}

}
