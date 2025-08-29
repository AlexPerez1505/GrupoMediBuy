<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaqueteProducto extends Pivot
{
    // Nombre EXACTO de tu tabla pivote
    protected $table = 'paquete_producto';

    /**
     * Si tu pivote NO tiene columnas created_at / updated_at,
     * déjalo en false. Ponlo en true SOLO si tu tabla las tiene.
     */
    public $timestamps = false;

    /**
     * Columnas que puedes asignar masivamente al crear/actualizar la pivote.
     * Si no tienes 'cantidad' en la tabla, elimínalo de este arreglo.
     */
    protected $fillable = [
        'paquete_id',
        'producto_id',
        // 'cantidad',
    ];

    /**
     * (Opcional) Casts útiles si tienes campos numéricos.
     */
    protected $casts = [
        'paquete_id'  => 'int',
        'producto_id' => 'int',
        // 'cantidad'    => 'int',
    ];

    /**
     * Relaciones de conveniencia
     */
    public function paquete(): BelongsTo
    {
        return $this->belongsTo(Paquete::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
