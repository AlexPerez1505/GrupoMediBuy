<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistRecepcion extends Model
{
    protected $table = 'checklist_recepciones';

    protected $fillable = [
        'checklist_id',
        'nombre_responsable',
        'checklist',
        'observaciones',
        'firma_recepcion',
        'evidencias',
    ];

    protected $casts = [
        'checklist' => 'array',
        'evidencias' => 'array',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
