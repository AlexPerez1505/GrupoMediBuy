<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = [
        'cliente_id',
        'contenido',
        'fecha'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
