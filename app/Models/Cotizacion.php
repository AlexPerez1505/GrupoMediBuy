<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones'; // Asegura que use la tabla correcta

    protected $fillable = [
        'cliente',
        'productos',
        'subtotal',
        'descuento',
        'iva',
        'total',
        'tipo_pago',
        'plan_pagos',
        'nota',
        'valido_hasta',
        'lugar_cotizacion'
    ];

    protected $casts = [
        'productos' => 'array',
        'plan_pagos' => 'array',
    ];
}
