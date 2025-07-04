<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    protected $fillable = [
        'cliente_id',
        'lugar',
        'nota',
        'user_id',
        'subtotal',
        'descuento',
        'envio',
        'iva',
        'total',
        'plan',
        'ficha_tecnica_id',
        'carta_garantia_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productos()
    {
        return $this->hasMany(PropuestaProducto::class);
    }

    public function cartaGarantia()
    {
        return $this->belongsTo(CartaGarantia::class);
    }
    public function pagos()
{
    return $this->hasMany(PagoFinanciamientoPropuesta::class);
}
public function pagosFinanciamiento()
{
    return $this->hasMany(\App\Models\PagoFinanciamientoPropuesta::class, 'propuesta_id');
}

}
