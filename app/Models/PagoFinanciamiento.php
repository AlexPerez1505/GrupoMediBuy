<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoFinanciamiento extends Model
{
    protected $table = 'pagos_financiamiento';

    protected $fillable = [
        'venta_id',
        'descripcion',
        'fecha_pago',
        'monto',
        'pagado',
        'metodo_pago',
        'notificado', // <-- agregado aquí
    ];

    protected $casts = [
        'pagado' => 'boolean',
        'notificado' => 'boolean', // <-- para que se comporte como booleano
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
public function pagos()
{
    return $this->hasMany(Pago::class, 'financiamiento_id');
}
// app/Models/PagoFinanciamiento.php
public function documentos()
{
    return $this->hasMany(DocumentoPago::class, 'pago_id'); // ✅ usa la columna correcta
}
public function pago()
{
    return $this->hasOne(Pago::class, 'financiamiento_id');
}

}
