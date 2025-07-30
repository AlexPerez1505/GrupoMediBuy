<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistFirma extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'user_id',
        'rol',
        'firma',
        'fecha_firma',
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
