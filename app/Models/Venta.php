<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\VentaProducto;
use App\Models\User;

class Venta extends Model
{
protected $fillable = [
    'cliente_id', 'lugar', 'nota', 'user_id',
    'subtotal', 'descuento', 'envio', 'iva', 'total', 'plan', 'detalle_financiamiento',
    'carta_garantia_id'  // <-- Aquí lo agregas
];


    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function pagos()
{
    return $this->hasMany(Pago::class);
}
public function cartaGarantia()
{
    return $this->belongsTo(CartaGarantia::class, 'carta_garantia_id');
}
public function remision()
{
    return $this->belongsTo(Remision::class);
}

}
