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
    ];

    protected $casts = [
        'pagado' => 'boolean',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
public function pagos()
{
    return $this->hasMany(Pago::class, 'financiamiento_id');
}


}
