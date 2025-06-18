<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;  // <-- Importa el trait
use App\Models\Seguimiento;


class Cliente extends Model
{
    use HasFactory, Notifiable;  // <-- Usa el trait Notifiable

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'comentarios',
        'asesor',
        'categoria_id'
    ];
      public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class);
    }
}
