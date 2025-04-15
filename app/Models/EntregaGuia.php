<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaGuia extends Model {
    use HasFactory;

    protected $fillable = [
        'guia_id',
        'entregado_por',
        'fecha_entrega',
        'contenido',
        'numero_serie',
        'observaciones',
        'destinatario',
        'firmaDigital',
        'imagen', // Agregada la columna imagen
    ];

    public function guia() {
        return $this->belongsTo(Guia::class);
    }
}
