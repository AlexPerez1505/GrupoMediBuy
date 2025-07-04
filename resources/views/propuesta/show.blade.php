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

    .boton-pdf-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 1rem;
    }

    .btn-rojo {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1.8px solid #fca5a5;
        padding: 0.5rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }

    .btn-rojo:hover {
        background-color: #fca5a5;
        color: #7f1d1d;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
    }

    .btn-rojo:active {
        transform: scale(0.97);
    }

    /* Responsive: centrar en móviles */
    @media (max-width: 576px) {
        .boton-pdf-wrapper {
            justify-content: center;
        }

        .btn-rojo {
            width: 100%;
            max-width: 280px;
            text-align: center;
        }
    }

</style>

<div class="header-container">
    @auth
    <div class="back-button">
        <button onclick="window.history.back()" class="menu-icon">
            <img src="{{ asset('images/atras.png') }}" alt="Regresar">
        </button>
    </div>
    @endauth
    <h1 class="titulos">Cotización</h1>
    <div class="gradient-bg-animation"></div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="mb-0">Resumen de Cotización N.2025{{ $propuesta->id }}</h2>
<div class="boton-pdf-wrapper">
    <a href="{{ route('propuestas.pdf', $propuesta->id) }}" class="btn-rojo" target="_blank">
         Descargar PDF
    </a>
</div>
    </div>
    <div class="row g-4">
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100 border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h6 class="mb-0 text-primary-emphasis fw-semibold">📋 Información General</h6>
                </div>
               <div class="card-body">
    <p><strong>Cliente:</strong> {{ $propuesta->cliente->nombre }} {{ $propuesta->cliente->apellido }}</p>
    <p><strong>Teléfono:</strong> {{ $propuesta->cliente->telefono ?? 'No proporcionado' }}</p>
    <p><strong>Correo:</strong> {{ $propuesta->cliente->correo ?? 'No proporcionado' }}</p>
    <p><strong>Dirección:</strong> {{ $propuesta->cliente->comentarios ?? 'Sin comentarios' }}</p>
    <p><strong>Lugar de Cotización:</strong> {{ $propuesta->lugar }}</p>
    <p><strong>Nota:</strong> {{ $propuesta->nota ?? 'Sin nota' }}</p>
    <p><strong>Realizada por:</strong> {{ $propuesta->usuario->name }}</p>
    <p><strong>Plan:</strong> {{ ucfirst($propuesta->plan) }}</p>
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
                        @foreach ($propuesta->productos as $item)
                            <tr class="border-bottom">
                                <td>
                                    <img src="{{ $item->producto ? asset('storage/' . $item->producto->imagen) : asset('images/imagen-no-disponible.png') }}"
                                         alt="{{ $item->producto->nombre ?? 'Producto eliminado' }}"
                                         class="rounded shadow-sm"
                                         style="width: 48px; height: 48px; object-fit: cover;">
                                </td>
                                <td>
                                    @if ($item->producto)
                                        <span class="fw-semibold d-block">{{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}</span>
                                        <small class="text-muted">
                                            {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                                            {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
                                        </small>
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
                    <div><strong>Subtotal:</strong> ${{ number_format($propuesta->subtotal, 2) }}</div>

                    @if($propuesta->descuento > 0)
                        <div><strong>Descuento:</strong> <span class="text-warning">${{ number_format($propuesta->descuento, 2) }}</span></div>
                    @endif

                    @if($propuesta->envio > 0)
                        <div><strong>Envío:</strong> ${{ number_format($propuesta->envio, 2) }}</div>
                    @endif

                    <div><strong>IVA:</strong> ${{ number_format($propuesta->iva, 2) }}</div>
                    <div class="fw-bold fs-5 mt-2 text-success-emphasis"><strong>Total:</strong> ${{ number_format($propuesta->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
@if($propuesta->pagos->count() > 0)
<div class="row mt-4">
    {{-- Plan de Pagos y Resumen Financiero (izquierda) --}}
    <div class="col-md-6 d-flex">
        <div class="card shadow-sm w-100 border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 text-primary-emphasis fw-semibold">💳 Plan de Pagos (Presupuesto)</h6>
            </div>
            <div class="card-body d-flex gap-4 flex-wrap">

               <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
                    Resumen del Financiamiento
                </h3>

                {{-- Resumen Financiero --}}
                @php
                    $pagos = $propuesta->pagos;

                    $pagoInicial = $pagos->first(function ($pago) {
                        return strtolower(trim($pago->descripcion)) === 'pago inicial';
                    });
                    $montoInicial = $pagoInicial->monto ?? 0;

                    $pagosMensuales = $pagos->filter(function ($pago) {
                        return strtolower(trim($pago->descripcion)) !== 'pago inicial';
                    });

                    $plazoMeses = $pagosMensuales->count();
                    $total = $propuesta->total ?? 0;
                    $montoFinanciadoBase = $total - $montoInicial;
                    $tasaInteresMensual = 0.05;
                    $montoConIntereses = $pagosMensuales->sum('monto');
                    $cuotaMensual = $plazoMeses > 0 ? $montoConIntereses / $plazoMeses : 0;

                    function moneda($numero) {
                        return '$' . number_format($numero, 2, '.', ',');
                    }
                @endphp
                <div style="flex: 1 1 35%; min-width: 280px; max-width: 320px;">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><strong>Total de la Cotización:</strong></td>
                            <td>{{ moneda($total) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Pago inicial estimado:</strong></td>
                            <td>{{ moneda($montoInicial) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Monto financiado estimado (sin intereses):</strong></td>
                            <td>{{ moneda($montoFinanciadoBase) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Plazo estimado:</strong></td>
                            <td>{{ $plazoMeses }} {{ Str::plural('mes', $plazoMeses) }}</td>
                        </tr>

                        @if($propuesta->plan === 'credito' && $plazoMeses > 0)
                            <tr>
                                <td><strong>Tasa de interés mensual estimada:</strong></td>
                                <td>{{ ($tasaInteresMensual * 100) }}%</td>
                            </tr>
                            <tr>
                                <td><strong>Total a pagar con intereses estimado:</strong></td>
                                <td class="text-primary fw-bold">{{ moneda($montoConIntereses) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cuota mensual estimada:</strong></td>
                                <td>{{ moneda($cuotaMensual) }}</td>
                            </tr>
                        @endif
                    </table>
                     </div>
                  {{-- Tabla Plan de Pagos --}}
                <div style="flex: 1 1 60%; min-width: 320px;">
                    <table class="table align-middle table-borderless mb-0">
                        <thead class="border-bottom text-muted small text-uppercase">
                            <tr>
                                <th>Descripción</th>
                                <th>Fecha de Pago</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($propuesta->pagos as $pago)
                            <tr class="border-bottom">
                                <td>{{ mb_strtoupper($pago->descripcion, 'UTF-8') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                <td>${{ number_format($pago->monto, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico circular (derecha) --}}
<div class="col-md-6 d-flex">
    <div class="card shadow-sm w-100 border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">🍩 Distribución por Subtotal</h6>
        </div>
        <div class="card-body d-flex justify-content-center align-items-center">
            <canvas id="graficoSubtotales" height="220" style="max-width: 100%;"></canvas>
        </div>
    </div>
</div>
</div>
@endif



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataSubtotales = {
        labels: @json($labels),
        datasets: [{
            data: @json($valores),
            backgroundColor: [
                '#AEC6CF', // pastel blue
                '#FFDAB9', // pastel peach
                '#CBAACB', // pastel purple
                '#B5EAD7', // pastel green
                '#FFDAC1', // pastel orange
                '#FFB7B2', // pastel pink
                '#E2F0CB', // pastel mint
                '#FDCBFF', // pastel lavender
                '#B0E0E6', // powder blue
                '#D8BFD8'  // thistle
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    };

    const configSubtotales = {
        type: 'doughnut',
        data: dataSubtotales,
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#6c757d',
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw;
                            return `${label}: $${value.toLocaleString()}`;
                        }
                    }
                }
            }
        }
    };

    new Chart(document.getElementById('graficoSubtotales'), configSubtotales);
});
</script>
</html>