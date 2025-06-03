@extends('layouts.app')

@section('content')

<style>
    body {
        background-color: #f7fbff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h3 {
        color: #26547c;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.5rem;
    }


    .container {
        background-color: #ffffff;
        border: 1px solid #e3edf7;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(38, 84, 124, 0.05);
        padding: 30px;
        max-width: 800px;
        margin-top:95px !important;
    }

    .btn-success {
        background-color: #b2f0e4;
        color: #00695c;
        border: none;
        font-weight: 500;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background-color: #8ce3d2;
        color: #004d40;
    }

    .btn-secondary {
        background-color: #e3edf7;
        color: #26547c;
        border: none;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: #d0e6f7;
    }

    .table {
        margin-top: 15px;
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background-color: #eaf6ff;
    }

    .table th, .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.95rem;
    }

    .table th {
        color: #26547c;
        font-weight: 600;
    }

    .table td {
        color: #333;
    }

    .progress-container {
        width: 100%;
        background-color: #f1f9ff;
        border-radius: 12px;
        overflow: hidden;
        margin: 20px 0;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .progress-bar {
        height: 22px;
        background-color: #e0f7f5;
        position: relative;
        border-radius: 12px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #b2f0e4, #8ce3d2);
        width: 0%;
        transition: width 0.6s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #004d40;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        h3 {
            font-size: 1.25rem;
        }

        .table th, .table td {
            font-size: 0.85rem;
            padding: 10px;
        }
    }
</style>
@php
    use Carbon\Carbon;

    function extraerPagos($detalle) {
        $lineas = explode(';', $detalle);
        $pagos = [];

        foreach ($lineas as $linea) {
            if (preg_match('/([a-záéíóúñ ]+)-\s*(\d{2}) de ([a-z]+) de (\d{4}):\s*\$([\d,\.]+)/iu', $linea, $m)) {
                $fecha = Carbon::createFromFormat('d \d\e F \d\e Y', "{$m[2]} de {$m[3]} de {$m[4]}")
                        ->locale('es')->translatedFormat('Y-m-d');
                $pagos[] = [
                    'tipo' => trim($m[1]),
                    'fecha' => $fecha,
                    'monto' => floatval(str_replace([','], '', $m[5]))
                ];
            }
        }

        return $pagos;
    }

    $pagosEsperados = !empty($detalleFinanciamiento) ? extraerPagos($detalleFinanciamiento) : [];
@endphp


<div class="container mt-5">
    <h3>Seguimiento Inteligente de Pagos - {{ $item->nombre_item }}</h3>
    <p><strong>Cliente:</strong> {{ $item->remision->cliente->nombre }}</p>
    <p><strong>Total:</strong> ${{ number_format($item->subtotal, 2) }} | 
       <strong>Pagado:</strong> ${{ number_format($item->a_cuenta, 2) }} | 
       <strong>Restante:</strong> ${{ number_format($item->restante, 2) }}</p>

    <!-- Alerta de próximos pagos -->
    @php $proximos = []; @endphp
    @foreach($pagosProgramados as $pago)
        @php
            $fechaTexto = $pago[2];
            $fechaCarbon = Carbon::createFromFormat('d \d\e F \d\e Y', str_replace(['de '], [''], $fechaTexto))->locale('es');
            $formatoYmd = $fechaCarbon->format('Y-m-d');

            $diasRestantes = $fechaCarbon->diffInDays($hoy, false);

            if ($diasRestantes <= 7 && $diasRestantes >= 0 && !in_array($formatoYmd, $pagosRealizados)) {
                $proximos[] = [
                    'etiqueta' => ucfirst($pago[1]),
                    'fecha' => $fechaCarbon->translatedFormat('d/m/Y'),
                    'monto' => number_format(str_replace(',', '', $pago[3]), 2)
                ];
            }
        @endphp
    @endforeach

    @if(count($proximos) > 0)
        <div class="alert alert-warning">
            <strong>Atención:</strong> Tienes pagos próximos:
            <ul class="mb-0">
                @foreach($proximos as $prox)
                    <li>{{ $prox['etiqueta'] }} el <strong>{{ $prox['fecha'] }}</strong> por <strong>${{ $prox['monto'] }}</strong></li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Pagos realizados -->
    <h5 class="mt-4">Historial de Pagos</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Método de Pago</th>
            </tr>
        </thead>
        <tbody>
            @forelse($item->pagos as $pago)
                <tr>
                    <td>{{ Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                    <td>${{ number_format($pago->monto, 2) }}</td>
                    <td>{{ $pago->metodo_pago }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Sin pagos registrados aún.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabla de pagos programados -->
    <h5 class="mt-4">Pagos Programados</h5>
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Fecha Programada</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagosProgramados as $pago)
                @php
                    $fechaTexto = $pago[2];
                    $fechaCarbon = Carbon::createFromFormat('d \d\e F \d\e Y', str_replace(['de '], [''], $fechaTexto))->locale('es');
                    $formatoYmd = $fechaCarbon->format('Y-m-d');
                    $pagado = in_array($formatoYmd, $pagosRealizados);
                @endphp
                <tr>
                    <td>{{ ucfirst($pago[1]) }}</td>
                    <td>{{ $fechaCarbon->translatedFormat('d/m/Y') }}</td>
                    <td>${{ number_format(str_replace(',', '', $pago[3]), 2) }}</td>
                    <td>
                        @if($pagado)
                            <span class="badge bg-success">Pagado</span>
                        @elseif($fechaCarbon->isPast())
                            <span class="badge bg-danger">Vencido</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Volver</a>
</div>
