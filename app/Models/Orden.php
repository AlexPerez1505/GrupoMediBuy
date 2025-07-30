<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';

    protected $fillable = [
        'cliente_id',
        'aparato_id',
        'fecha_entrada',
        'fecha_mantenimiento',
        'proximo_mantenimiento',
        // 'checklist' ya no es necesario aquí
    ];

    protected $casts = [
        'fecha_entrada'         => 'date',
        'fecha_mantenimiento'   => 'date',
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

    /**
     * Ítems de checklist marcados en esta orden
     */
    public function checklistItems()
    {
        return $this->belongsToMany(
            ChecklistItem::class,       // Modelo relacionado
            'orden_checklist_item',     // Tabla pivot
            'orden_id',                 // FK local en pivot
            'checklist_item_id'         // FK foránea en pivot
        );
    }
}
