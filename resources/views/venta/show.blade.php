<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/remisiones.css') }}?v={{ time() }}">
</head>
<body>
<div class="header-container">
@auth
    <div class="back-button">
        <button onclick="window.location.href='{{ route('perfil') }}'" class="menu-icon">
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
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print"></i> Imprimir
            </button>
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
            <div class="card w-100">
                <div class="card-header">Datos de la Venta</div>
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
            <div class="card w-100">
                <div class="card-header">Productos Seleccionados</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Sobreprecio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($venta->productos as $item)
                                    <tr>
                                        <td>
                                            @if ($item->producto)
                                                <img src="{{ asset('storage/' . $item->producto->imagen) }}" alt="{{ $item->producto->nombre }}" width="50" class="me-2">
                                                {{ $item->producto->nombre }}
                                            @else
                                                <img src="{{ asset('images/imagen-no-disponible.png') }}" alt="Producto eliminado" width="50" class="me-2">
                                                <span class="text-danger">Producto eliminado</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->cantidad }}</td>
                                        <td>${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td>${{ number_format($item->sobreprecio, 2) }}</td>
                                        <td>${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($venta->detalle_financiamiento))
        <div class="row mt-4 g-4">
            {{-- Resumen Financiero --}}
            <div class="col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header">Resumen Financiero</div>
                    <div class="card-body">
                        <p><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</p>
                        <p><strong>Descuento:</strong> ${{ number_format($venta->descuento, 2) }}</p>
                        <p><strong>Envío:</strong> ${{ number_format($venta->envio, 2) }}</p>
                        <p><strong>IVA:</strong> ${{ number_format($venta->iva, 2) }}</p>
                        <p><strong>Total:</strong> <span class="h4 text-success">${{ number_format($venta->total, 2) }}</span></p>
                    </div>
                </div>
            </div>

            {{-- Plan de Pagos --}}
            <div class="col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header">Plan de Pagos</div>
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
                                $detalle = $venta->detalle_financiamiento;
                                preg_match('/Pago inicial:\s*\$([\d,\.]+)/i', $detalle, $matchInicial);
                                $montoInicial = isset($matchInicial[1]) ? floatval(str_replace([',', '$'], '', $matchInicial[1])) : 0;
                                preg_match_all('/(?:primer pago|segundo pago|tercer pago|cuarto pago|quinto pago|sexto pago|séptimo pago|octavo pago|noveno pago|décimo pago|pago)\s*:\s*\$([\d,\.]+)/i', $detalle, $matchesPagos);
                                $pagos = $matchesPagos[1] ?? [];
                                $plazoMeses = count($pagos);
                                $total = $venta->total ?? 0;
                                $montoFinanciadoBase = $total - $montoInicial;
                                $tasaInteresMensual = 0.05;
                                $montoConIntereses = $plazoMeses > 0 ? $montoFinanciadoBase * pow(1 + $tasaInteresMensual, $plazoMeses) : $montoFinanciadoBase;
                                $cuotaMensual = $plazoMeses > 0 ? $montoConIntereses / $plazoMeses : 0;
                                function moneda($numero) { return '$' . number_format($numero, 2, '.', ','); }
                                $lineasDetalle = array_filter(array_map('trim', explode(';', $detalle)));
                                $col1 = array_slice($lineasDetalle, 0, 5);
                                $col2 = array_slice($lineasDetalle, 5);
                            @endphp
                            <div class="mb-4">
                                <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
                                    Resumen del Financiamiento
                                </h3>
                                <table class="table table-sm mt-3">
                                    <tr><td><strong>Total de la venta:</strong></td><td>{{ moneda($total) }}</td></tr>
                                    <tr><td><strong>Pago inicial:</strong></td><td>{{ moneda($montoInicial) }}</td></tr>
                                    <tr><td><strong>Monto financiado (sin intereses):</strong></td><td>{{ moneda($montoFinanciadoBase) }}</td></tr>
                                    <tr><td><strong>Plazo:</strong></td><td>{{ $plazoMeses }} {{ Str::plural('mes', $plazoMeses) }}</td></tr>
                                    @if($venta->plan === 'credito')
                                        <tr><td><strong>Tasa de interés mensual:</strong></td><td>5%</td></tr>
                                        <tr><td><strong>Total a pagar con intereses:</strong></td><td class="text-primary fw-bold">{{ moneda($montoConIntereses) }}</td></tr>
                                    @endif
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    @foreach ($col1 as $linea) <p>{{ $linea }}</p> @endforeach
                                </div>
                                @if(count($col2) > 0)
                                    <div class="col-6">
                                        @foreach ($col2 as $linea) <p>{{ $linea }}</p> @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</body>
</html>
