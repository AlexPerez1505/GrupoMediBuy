<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    use HasFactory;

    protected $table = 'vacaciones';

    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'tipo_permiso',
        'justificacion',
        'estatus'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
