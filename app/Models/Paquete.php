<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paquete extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'imagen'];

    /**
     * Relación muchos a muchos con Producto usando la pivote existente "paquete_producto".
     * Ajusta withPivot/withTimestamps solo si tu pivote tiene esas columnas.
     */
public function productos()
{
    return $this->belongsToMany(Producto::class, 'paquete_producto', 'paquete_id', 'producto_id');
}

}
