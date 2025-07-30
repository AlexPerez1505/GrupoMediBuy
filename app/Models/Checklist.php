<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'fecha_inicio',
        'fecha_fin',
        'finalizado',
        'etapa', // si lo usas para saber en qué paso va
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // Relación con etapas:
    public function ingenieria()
    {
        return $this->hasOne(ChecklistIngenieria::class);
    }

    public function embalaje()
    {
        return $this->hasOne(ChecklistEmbalaje::class);
    }

    public function entrega()
    {
        return $this->hasOne(ChecklistEntrega::class);
    }
}
