<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteProducto extends Model
{
    use HasFactory;

    protected $table = 'paquete_producto';
    protected $fillable = ['paquete_id', 'producto_id'];
}
