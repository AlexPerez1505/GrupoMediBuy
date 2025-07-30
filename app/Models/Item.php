<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
    ];

    public function partes()
    {
        return $this->hasMany(ItemParte::class);
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }
    public function componentes()
    {
        return $this->hasMany(\App\Models\ItemParte::class, 'item_id');
    }

}
