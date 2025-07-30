<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemParte extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'nombre_parte',
        'codigo_parte',
        'descripcion',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
