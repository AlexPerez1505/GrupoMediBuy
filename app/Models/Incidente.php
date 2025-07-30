<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidente extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'item_parte_id',
        'user_id',
        'descripcion',
        'tipo',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function parte()
    {
        return $this->belongsTo(ItemParte::class, 'item_parte_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
