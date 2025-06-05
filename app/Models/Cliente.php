<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;  // <-- Importa el trait

class Cliente extends Model
{
    use HasFactory, Notifiable;  // <-- Usa el trait Notifiable

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'comentarios',
    ];
}
