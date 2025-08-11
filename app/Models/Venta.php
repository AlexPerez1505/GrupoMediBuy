<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\VentaProducto;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\ProximoPagoNotification;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id',
        'lugar',
        'nota',
        'user_id',
        'subtotal',
        'descuento',
        'envio',
        'iva',
        'total',
        'plan',
        'detalle_financiamiento',
        'carta_garantia_id',
        'meses_garantia',    // ← Lo agregamos aquí
    ];

public function notificarSiPagoProximo()
{
    if (!$this->detalle_financiamiento || !$this->cliente) {
        return;
    }

    // Remover "Pago inicial..." del detalle
    $detalle = removerPagoInicialDelDetalle($this->detalle_financiamiento);

    preg_match_all(
        '/\b(\d{2}[\/\-]\d{2}[\/\-]\d{4}|\d{4}[\/\-]\d{2}[\/\-]\d{2}|\d{2}\s+de\s+\w+\s+de\s+\d{4})\b/i',
        $detalle,
        $matches
    );

    $fechas = $matches[0];
    $hoy = Carbon::now();
    $limite = $hoy->copy()->addDays(7);

    foreach ($fechas as $fechaTexto) {
        try {
            $fecha = Carbon::parse(str_ireplace(' de ', ' ', $fechaTexto));

            if ($fecha->between($hoy, $limite)) {
                $this->cliente->notify(new ProximoPagoNotification($this, $fecha));
                break; // Notifica solo una vez por venta
            }
        } catch (\Exception $e) {
            // Log::warning('Error parseando fecha: ' . $fechaTexto);
        }
    }
}
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
public function pagos()
{
    return $this->hasMany(PagoFinanciamiento::class, 'venta_id');
}

public function cartaGarantia()
{
    return $this->belongsTo(CartaGarantia::class, 'carta_garantia_id');
}
public function remision()
{
    return $this->belongsTo(Remision::class);
}
public function pagosFinanciamiento()
{
    return $this->hasMany(PagoFinanciamiento::class);
}

public function pagosReales()
{
    return $this->hasMany(Pago::class, 'venta_id');
}
public function pagosFinanciamientoConfirmados()
{
    return $this->hasMany(PagoFinanciamiento::class, 'venta_id')->where('pagado', 1);
}
public function pagoFinanciamiento()
{
    return $this->hasMany(PagoFinanciamiento::class, 'venta_id');
}
    public function checklist()
    {
        return $this->hasOne(Checklist::class);
    }
}
