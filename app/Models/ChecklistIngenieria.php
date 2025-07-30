<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistIngenieria extends Model
{
    use HasFactory;

    protected $table = 'checklist_ingenieria';

    protected $fillable = [
        'checklist_id',
        'user_id',
        'componentes', // json
        'incidente',
        'firma_responsable',
        'firma_supervisor',
        'evidencias', // json
    ];

    protected $casts = [
        'componentes' => 'array',
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
