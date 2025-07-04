<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'venta_id',           // nuevo campo para relacionar con ventas
        'item_remision_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
        'aprobado' => 'boolean',
    ];

    // Relación con Venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Relación con ItemRemision (si la necesitas)
    public function item()
    {
        return $this->belongsTo(ItemRemision::class, 'item_remision_id');
    }

public function detalleFinanciamiento()
{
    return $this->belongsTo(PagoFinanciamiento::class, 'financiamiento_id');
}
public function documentos()
{
    return $this->hasMany(DocumentoPago::class);
}


}
