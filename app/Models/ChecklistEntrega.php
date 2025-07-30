<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistEntrega extends Model
{
    use HasFactory;

    protected $table = 'checklist_entrega';

    protected $fillable = [
        'checklist_id',
        'user_id',
        'datos_entrega', // json
        'observaciones',
        'firma_cliente',
        'firma_entrega',
        'evidencias', // json
    ];

    protected $casts = [
        'datos_entrega' => 'array',
        'evidencias' => 'array',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
