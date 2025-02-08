<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'telefono',
        'subtotal',
        'iva',
        'total',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'cotizacion_producto')
                    ->withPivot('cantidad', 'precio', 'subtotal')
                    ->withTimestamps();
    }
}
