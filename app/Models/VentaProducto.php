<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/VentaProducto.php
class VentaProducto extends Model
{
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'sobreprecio',
        'registro_id' // 👈 ¡Esto faltaba!
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function registro()
    {
        return $this->belongsTo(Registro::class, 'registro_id');
    }
}

