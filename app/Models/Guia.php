<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guia extends Model {
    use HasFactory;

    protected $fillable = ['numero_rastreo', 'peso', 'fecha_recepcion'];

    // Relación con el modelo EntregaGuia
    public function entrega() {
        return $this->hasOne(EntregaGuia::class, 'guia_id');
    }
}
