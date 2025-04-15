<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_equipo',
        'modelo',
        'marca',
        'stock',
        'precio',
        'imagen',
    ];

    public function cotizaciones()
{
    return $this->belongsToMany(Cotizacion::class, 'cotizacion_producto')
                ->withPivot('cantidad', 'precio', 'subtotal')
                ->withTimestamps();
}
public function paquetes()
    {
        return $this->belongsToMany(Paquete::class);
    }
}
