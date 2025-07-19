<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión</title>
            

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/remisiones.css') }}?v={{ time() }}">
</head>
<style>
 .pastel-progress-bar {
    height: 100%;
    border-radius: 18px;
    font-weight: bold;
    color: #555;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: width 0.8s ease-in-out, background 0.4s ease-in-out;
}
.table th,
.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table-borderless th,
.table-borderless td {
    border: none;
}

.table thead {
    border-bottom: 2px solid #eee;
}

.card {
    background-color: #fefefe;
    border-radius: 1rem;
}

.card-header {
    background-color: transparent;
    border-bottom: none;
}

</style>
<body>
<div class="header-container">
@auth
    <div class="back-button">
        <button onclick="window.history.back()" class="menu-icon">
            <img src="{{ asset('images/atras.png') }}" alt="Regresar">
        </button>
    </div>
@endauth

    <h1 class="titulos">Remisión</h1>

    <!-- Línea animada -->
    <div class="gradient-bg-animation"></div>
</div>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mobile-buttons">
        <h2 class="mb-0">Resumen de Venta N.2025-{{ $venta->id }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.pdf', $venta->id) }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>

        </div>
        <div class="d-none d-lg-block position-fixed end-0 bottom-0 p-4 z-3">
            <div class="btn-group-vertical shadow-sm">
                <a href="{{ route('ventas.pdf', $venta->id) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Información general --}}
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100 border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 text-primary-emphasis fw-semibold">Datos de la Venta</div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> {{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</p>
                    <p><strong>Teléfono:</strong> {{ mb_strtoupper($venta->cliente->telefono, 'UTF-8') }}</p>
                    <p><strong>Dirección:</strong> {{ mb_strtoupper($venta->cliente->comentarios, 'UTF-8') }}</p>
                    <p><strong>Asesor de venta:</strong> {{ mb_strtoupper($venta->usuario->name, 'UTF-8') }}</p>
                    <p><strong>Teléfono:</strong> {{ $venta->usuario->phone }}</p>
                    <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Plan:</strong> {{ mb_strtoupper($venta->plan, 'UTF-8') }}</p>
                    <p><strong>Nota:</strong> {{ $venta->nota ? mb_strtoupper($venta->nota, 'UTF-8') : 'N/A' }}</p>
                </div>
            </div>
        </div>

{{-- Productos --}}
<div class="col-md-6 d-flex">
    <div class="card shadow-sm w-100 border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">🛒 Productos Seleccionados</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-borderless mb-0">
                    <thead class="border-bottom">
                        <tr class="text-muted small text-uppercase">
                            <th>Equipo</th>
                            <th>Descripción</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->productos as $item)
                            <tr class="border-bottom">
                                <td>
                                    <img src="{{ $item->producto ? asset('storage/' . $item->producto->imagen) : asset('images/imagen-no-disponible.png') }}"
                                         alt="{{ $item->producto->nombre ?? 'Producto eliminado' }}"
                                         class="rounded shadow-sm"
                                         style="width: 48px; height: 48px; object-fit: cover;">
                                </td>
                                <td>
                                    @if ($item->producto)
                                        <span class="fw-semibold d-block">
                                            {{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}
                                        </span>
                                        <small class="text-muted d-block">
                                            {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                                            {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
                                        </small>

                                        @if ($item->registro)
                                            <small class="text-info-emphasis d-block mt-1">
                                                <i class="bi bi-hash"></i> Serie: {{ $item->registro->numero_serie }}
                                            </small>
                                        @else
                                            <small class="text-warning-emphasis d-block mt-1">
                                                <i class="bi bi-exclamation-circle"></i> Sin número de serie
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-danger fst-italic">Producto eliminado</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->cantidad }}</td>
                                <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <hr class="my-3">

                <div class="d-flex flex-column gap-1 text-end small">
                    <div><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</div>

                    @if($venta->descuento > 0)
                        <div><strong>Descuento:</strong> <span class="text-warning">${{ number_format($venta->descuento, 2) }}</span></div>
                    @endif

                    @if($venta->envio > 0)
                        <div><strong>Envío:</strong> ${{ number_format($venta->envio, 2) }}</div>
                    @endif

                    <div><strong>IVA:</strong> ${{ number_format($venta->iva, 2) }}</div>
                    <div class="fw-bold fs-5 mt-2 text-success-emphasis"><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<br>

 <div class="row g-4">
        {{-- Información general --}}
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100 border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 text-primary-emphasis fw-semibold">Resumen financiero</div>
            <div class="card-body">
                <h5 class="card-title mb-3">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</h5>
@php
    $pagosAprobados = $venta->pagosReales->filter(function($p) {
        return $p->aprobado;
    });

    $totalPagado = $pagosAprobados->sum('monto');
    $saldoRestante = $venta->total - $totalPagado;
    $porcentajePagado = min(100, round(($totalPagado / $venta->total) * 100, 2));

    if ($porcentajePagado < 25) {
    $frase = "¡Empezar es ganar, aunque sea poco!";
} elseif ($porcentajePagado < 50) {
    $frase = "Ya calentaste motores, sigue rodando.";
} elseif ($porcentajePagado < 75) {
    $frase = "Casi en la cima, no mires para atrás.";
} elseif ($porcentajePagado < 100) {
    $frase = "¡La meta se asoma, dale con todo!";
} else {
    $frase = "¡Listo! Has desbloqueado el modo campeón.";
}


    $tooltipText = "Has pagado $" . number_format($totalPagado, 2) . " de $" . number_format($venta->total, 2) . ". " . $frase;
@endphp

<p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
<p><strong>Total pagado:</strong> ${{ number_format($totalPagado, 2) }}</p>
<p><strong>Saldo restante:</strong> ${{ number_format($saldoRestante, 2) }}</p>
<div class="mt-4">
    <label class="form-label fw-semibold mb-2 text-secondary-emphasis">
        Progreso del pago: 
        <span id="porcentajeContador" class="fw-bold">{{ $porcentajePagado }}%</span>
    </label>

    <div class="progress position-relative shadow" style="height: 26px; border-radius: 18px; background-color: #f9f9f9;">
        <div class="progress-bar pastel-progress-bar"
            role="progressbar"
            id="barraProgreso"
            data-porcentaje="{{ $porcentajePagado }}"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ $tooltipText }}"
            aria-valuenow="{{ $porcentajePagado }}"
            aria-valuemin="0"
            aria-valuemax="100">
        </div>
    </div>
    </div>
    <br>
    @if($pagos->every(fn($p) => $p->pagado))
    <a href="{{ route('venta.recibo.final', $venta->id) }}" class="btn btn-success" target="_blank">
        Descargar Recibo Final
    </a>
@endif



            </div>
        </div>
        </div>
    {{-- Plan de Pagos --}}
   <div class="col-md-6 d-flex">
    <div class="card shadow-sm w-100 border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">Plan de pagos</h6>
        </div>
        <div class="card-body">
          
                @if ($venta->plan === 'contado')
                    <div class="col-12 mt-4">
                        <div class="card border-start border-success border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-money-check-alt fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-success fw-bold">Pago registrado correctamente</h5>
                                    <p class="mb-0 text-muted">Gracias por su compra. El pago se realizó en una sola exhibición.</p>
                                </div>
                            </div>
                        </div>
                    </div>
               @else
  @php


    // Detectar el pago inicial por descripción
    $pagoInicial = $pagos->first(function ($pago) {
        return strtolower(trim($pago->descripcion)) === 'pago inicial';
    });

    $montoInicial = $pagoInicial->monto ?? 0;

    // Filtrar pagos que NO son el pago inicial
    $pagosMensuales = $pagos->filter(function ($pago) {
        return strtolower(trim($pago->descripcion)) !== 'pago inicial';
    });

    $plazoMeses = $pagosMensuales->count();
    $total = $venta->total ?? 0;
    $montoFinanciadoBase = $total - $montoInicial;
    $tasaInteresMensual = 0.05;

    // NUEVO: Calcular el total a pagar con intereses usando los pagos reales
    $montoConIntereses = $pagosMensuales->sum('monto');

    $cuotaMensual = $plazoMeses > 0 ? $montoConIntereses / $plazoMeses : 0;

    function moneda($numero) {
        return '$' . number_format($numero, 2, '.', ',');
    }
@endphp

<div class="mb-4">
    <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
        Resumen del Financiamiento
    </h3>
    <table class="table table-sm mt-3">
        <tr>
            <td><strong>Total de la venta:</strong></td>
            <td>{{ moneda($total) }}</td>
        </tr>
        <tr>
            <td><strong>Pago inicial:</strong></td>
            <td>{{ moneda($montoInicial) }}</td>
        </tr>
        <tr>
            <td><strong>Monto financiado (sin intereses):</strong></td>
            <td>{{ moneda($montoFinanciadoBase) }}</td>
        </tr>
        <tr>
            <td><strong>Plazo:</strong></td>
            <td>{{ $plazoMeses }} {{ Str::plural('meses', $plazoMeses) }}</td>
        </tr>
        @if($venta->plan === 'credito')
            <tr>
                <td><strong>Tasa de interés mensual:</strong></td>
                <td>5%</td>
            </tr>
            <tr>
                <td><strong>Total a pagar con intereses:</strong></td>
                <td class="text-primary fw-bold">{{ moneda($montoConIntereses) }}</td>
            </tr>
        @endif
    </table>
</div>
@php
    $mostrarColumnaDocumento = $pagos->contains(function ($p) {
        return $p->documentos && $p->documentos->isNotEmpty();
    });
@endphp

<h5 class="card-title mb-3">Pagos</h5>
<div class="table-responsive">
    <table class="table table-hover align-middle">
<thead class="table-light">
    <tr>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Monto</th>
        <th>Recibo</th>
        @if($mostrarColumnaDocumento)
            <th>Documento PDF</th>
        @endif
        <th>Estado</th>
    </tr>
</thead>

        <tbody>
@forelse ($pagos as $pagoFin)
    <tr>
        <td>{{ $pagoFin->descripcion ?? 'N/A' }}</td>
        <td>{{ \Carbon\Carbon::parse($pagoFin->fecha_pago)->format('d/m/Y') }}</td>
        <td>${{ number_format($pagoFin->monto, 2) }}</td>
        <td>
            @if($pagoFin->pago && $pagoFin->pago->aprobado)
                <a href="{{ route('pagos.recibo', $pagoFin->pago->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">Ver</a>
            @else
                <span class="text-muted small">—</span>
            @endif
        </td>

        @if($mostrarColumnaDocumento)
            <td>
                @if($pagoFin->documentos && $pagoFin->documentos->isNotEmpty())
                    <a href="{{ Storage::url($pagoFin->documentos->first()->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark-pdf"></i> Abrir
                    </a>
                @else
                    <span class="text-muted small">—</span>
                @endif
            </td>
        @endif

        <td>
            @if($pagoFin->pago && $pagoFin->pago->aprobado)
                <span class="badge bg-success">Pagado</span>
            @else
                <span class="badge bg-warning text-dark">Pendiente</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ $mostrarColumnaDocumento ? 6 : 5 }}" class="text-center">No hay pagos programados</td>
    </tr>
@endforelse
</tbody>

    </table>
</div>


        </div>
    </div>
</div>
                @endif
            </div>
<hr>
            <p style="font-weight: 600; font-size: 0.85rem; color: #555; margin-top: 1rem; text-align: center;">
        Si tienes duda, queja o aclaración, manda mensaje al 
        <a href="tel:+7224485191" style="color: #1565c0; text-decoration: none; font-weight: 700;">+52 722 448 5191</a> 
        o al correo 
        <a href="mailto:compras@grupomedibuy.com" style="color: #1565c0; text-decoration: none; font-weight: 700;">compras@grupomedibuy.com</a>.
    </p>

        </div>
    </div>
</div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const barra = document.getElementById('barraProgreso');
    const porcentaje = parseInt(barra.dataset.porcentaje);
    const contador = document.getElementById('porcentajeContador');
    let actual = 0;

    // Define colores pastel
    let color;
    if (porcentaje < 50) {
        color = 'linear-gradient(90deg, #f7b3b3, #f48f8f)'; // rojo pastel
    } else if (porcentaje < 100) {
        color = 'linear-gradient(90deg, #fff3b0, #ffdb58)'; // amarillo pastel
    } else {
        color = 'linear-gradient(90deg, #b0f2b6, #7ed6a3)'; // verde pastel
    }
    barra.style.background = color;

    // Animación
    const animar = setInterval(() => {
        if (actual >= porcentaje) {
            clearInterval(animar);
        } else {
            actual++;
            barra.style.width = actual + '%';
            contador.textContent = actual + '%';
        }
    }, 15);
});
</script>

</html>
