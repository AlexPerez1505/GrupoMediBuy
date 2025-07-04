<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoPago extends Model
{
    protected $table = 'documentos_pago'; // ✅ nombre correcto de la tabla

    protected $fillable = ['pago_id', 'nombre_original', 'ruta_archivo'];

    public function pago()
    {
        return $this->belongsTo(PagoFinanciamiento::class, 'pago_id'); // ✅ clave foránea correcta
    }
}
