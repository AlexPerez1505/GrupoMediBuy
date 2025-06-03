<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remision extends Model
{
    use HasFactory;

    protected $table = 'remisions';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha', // si estás usando una fecha personalizada (si no, puedes quitarlo también)
        'iva',
        'subtotal',
        'aplicar_iva',
        'total',
        'importe_letra',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ItemRemision::class);
    }
    public function venta()
{
    return $this->belongsTo(Venta::class);
}

}
