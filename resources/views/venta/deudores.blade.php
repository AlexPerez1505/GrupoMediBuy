@extends('layouts.app')
@section('title', 'Financiamientos')
@section('titulo', 'Financiamientos')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/deudores.css') }}?v={{ time() }}">
@php
use Carbon\Carbon;

function extraerPagoInicialDePagos($pagos) {
    foreach ($pagos as $pago) {
        if (stripos($pago->descripcion, 'Pago inicial') !== false || stripos($pago->descripcion, 'Enganche') !== false) {
            return $pago->monto;
        }
    }
    return 0;
}

function extraerFechasDePagos($pagos) {
    $fechas = [];
    foreach ($pagos as $pago) {
        if ($pago->fecha_pago) {
            try {
                $fechas[] = Carbon::parse($pago->fecha_pago);
            } catch (\Exception $e) {
                // Ignorar errores de parseo
            }
        }
    }
    return $fechas;
}

$ventasConPagosProximos = [];

foreach ($ventas as $ventaTmp) {
    $pagosPendientes = $ventaTmp->pagosFinanciamiento->where('pagado', false) ?? collect();
    $fechas = extraerFechasDePagos($pagosPendientes);

    foreach ($fechas as $fecha) {
        if (
            $fecha->isToday() ||
            $fecha->isTomorrow() ||
            $fecha->between(Carbon::now(), Carbon::now()->addDays(7), true)
        ) {
            $ventasConPagosProximos[] = [
                'venta' => $ventaTmp,
                'fecha' => $fecha,
            ];
            break;
        }
    }
}
@endphp
@if(count($ventasConPagosProximos))
<div class="alert alert-warning d-flex align-items-center mb-4" style="background-color: #fff8e1; border: 1px solid #ffca28; border-radius: 6px; padding: 10px 15px; margin-top:90px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="22" height="22" fill="#ff9800" viewBox="0 0 24 24">
        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
    </svg>
    <div>
        <strong>Atención:</strong> Hay {{ count($ventasConPagosProximos) }} venta(s) con pago próximo.
        @foreach($ventasConPagosProximos as $vp)
            @php
              $pagoPendiente = $vp['venta']->pagosFinanciamiento
    ->where('pagado', false)
    ->first(function ($pago) use ($vp) {
        return $pago->fecha_pago && \Carbon\Carbon::parse($pago->fecha_pago)->isSameDay($vp['fecha']);
    });


                $puedeNotificar = $pagoPendiente && !$pagoPendiente->notificado;
            @endphp

            <div class="mt-2">
                <strong>#Venta:</strong> {{ $vp['venta']->id }} – 
                Cliente: {{ optional($vp['venta']->cliente)->nombre }} {{ optional($vp['venta']->cliente)->apellido }} – 
                Fecha: {{ $vp['fecha']->format('d/m/Y') }}

                @if($puedeNotificar)
                    <button class="btn btn-sm btn-outline-primary ms-2 reenviar-btn"
                        data-pago-id="{{ $pagoPendiente->id }}"
                        style="padding: 2px 8px; font-size: 0.8rem;">
                        Reenviar correo
                    </button>
                @else
                    <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">Notificado</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
<div class="container py-4">
    <h1 class="titulo-principal">Ventas con saldo pendiente</h1>

    @if($ventas->isEmpty())
        <div class="no-ventas">No hay ventas pendientes.</div>
    @else
        <div class="row g-4">
            @foreach($ventas as $venta)
                @php
                    $pagosPendientes = $venta->pagosFinanciamiento->where('pagado', false) ?? collect();
                    $pagosPagados = $venta->pagosFinanciamiento->where('pagado', true) ?? collect();

                    $pagoInicial = extraerPagoInicialDePagos($pagosPagados);
                    $totalPagado = $pagosPagados->sum('monto');
                    $restante = $venta->total - $totalPagado;

                    $fechasPago = extraerFechasDePagos($pagosPendientes);
                    $pagoHoy = collect($fechasPago)->contains(fn($f) => $f->isToday());
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card-venta h-100">
                        <div class="card-venta-header d-flex justify-content-between align-items-center">
                            <div>
                                <div class="cliente-nombre">{{ optional($venta->cliente)->nombre }} {{ optional($venta->cliente)->apellido }}</div>
                                <div class="venta-id">#Venta: {{ $venta->id }}</div>
                            </div>
                            @if($restante <= 0)
                                <span class="estado-liquidada badge bg-success">Liquidada</span>
                            @else
                                <span class="estado-pendiente badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </div>

                        <div class="detalle-venta mt-3">
                            @if($pagoHoy && $restante > 0)
                                <div class="alert alert-danger d-flex align-items-center mb-3" style="border: 1px solid #f44336; background: #ffebee;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" fill="#f44336" viewBox="0 0 24 24">
                                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                                    </svg>
                                    <strong>¡El pago de esta venta es hoy!</strong>
                                </div>
                            @endif

                            <p><strong>Emitido:</strong> {{ Carbon::parse($venta->created_at)->format('d/m/Y') }}</p>
                            <p><strong>Total venta:</strong> ${{ number_format($venta->total, 2) }}</p>

                            @if($pagoInicial > 0)
                                <p><strong>Pago inicial:</strong> ${{ number_format($pagoInicial, 2) }}</p>
                            @endif

                            <p><strong>Total pagado:</strong> ${{ number_format($totalPagado, 2) }}</p>
                            <p><strong>Restante:</strong> <span class="restante">${{ number_format($restante, 2) }}</span></p>

                            @if(count($fechasPago))
                                <p><strong>Fechas de pagos pendientes:</strong></p>
                                <ul class="mb-0 ps-3" style="font-size: 0.9rem; color: #555;">
                                    @foreach($fechasPago as $f)
                                        <li>{{ $f->isoFormat('DD [de] MMMM [de] YYYY') }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mt-3 px-2">
                            <a href="{{ route('ventas.pagos.index', $venta->id) }}" 
                               class="btn"
                               style="
                                   background-color: #e0f7fa; 
                                   color: #00796b; 
                                   border: none; 
                                   border-radius: 6px; 
                                   padding: 6px 14px;
                                   font-size: 0.9rem;
                                   font-weight: 500;
                                   box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                   transition: background-color 0.3s ease;
                               "
                               onmouseover="this.style.backgroundColor='#b2ebf2'"
                               onmouseout="this.style.backgroundColor='#e0f7fa'">
                                Ver pagos
                            </a>

                            <a href="{{ route('ventas.show', $venta->id) }}" 
                               class="btn"
                               style="
                                   background-color: #e6f5ea; 
                                   color: #388e3c; 
                                   border: none; 
                                   border-radius: 6px; 
                                   padding: 6px 14px;
                                   font-size: 0.9rem;
                                   font-weight: 500;
                                   box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                   transition: background-color 0.3s ease;
                               "
                               onmouseover="this.style.backgroundColor='#c8e6c9'"
                               onmouseout="this.style.backgroundColor='#e6f5ea'">
                                Ver remisión
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<script>
document.querySelectorAll('.reenviar-btn').forEach(button => {
    button.addEventListener('click', function () {
        const pagoId = this.dataset.pagoId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        this.disabled = true;
        this.textContent = 'Enviando...';

        fetch(`/financiamientos/notificar/${pagoId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                this.textContent = 'Correo enviado';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
            } else {
                this.textContent = 'Error';
                this.disabled = false;
            }
        }).catch(() => {
            this.textContent = 'Error';
            this.disabled = false;
        });
    });
});
</script>

@endsection
