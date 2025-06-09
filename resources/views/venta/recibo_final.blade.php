<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo Final de Venta</title>
    <style>
        @page {
            margin: 40px 50px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #2c3e50;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #dcdcdc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            height: 55px;
            margin-bottom: 5px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 20px;
            color: #407b38;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            color: #555;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #19589d;
            margin-top: 30px;
            margin-bottom: 8px;
            border-bottom: 1px solid #dcdcdc;
            padding-bottom: 5px;
        }

        .client-info {
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .client-info strong {
            display: inline-block;
            width: 140px;
            color: #34495e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #f5f6fa;
            color: #2c3e50;
            padding: 8px;
            font-weight: 600;
            font-size: 12px;
            border: 1px solid #eaeaea;
        }

        td {
            padding: 8px;
            font-size: 12px;
            border: 1px solid #eaeaea;
        }

        .summary {
            margin-top: 25px;
            width: 100%;
        }

        .summary td {
            padding: 10px;
            font-weight: bold;
            font-size: 13px;
        }

        .badge {
            color: #27ae60;
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 40px;
            border-top: 1px solid #dcdcdc;
            padding-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy">
        <h1>Grupo MediBuy</h1>
        <p>RFC EMISOR: TEOA890725GC0 | compras@grupomedibuy.com | 722-448-5191</p>
        <p><strong>Recibo Final de Venta No. 2025-{{ $venta->id }}</strong></p>
        <p>Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Datos del Cliente</div>
    <div class="client-info">
        <p><strong>Nombre:</strong> {{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</p>
        <p><strong>Teléfono:</strong> {{ $venta->cliente->telefono ?? 'N/A' }}</p>
        <p><strong>Asesor:</strong> {{ mb_strtoupper($venta->usuario->name, 'UTF-8') }}</p>
        <p><strong>Fecha de Inicio:</strong> {{ $venta->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Detalle de Productos</div>
    <table>
        <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($venta->productos as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="section-title">Pagos Realizados</div>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Monto</th>
        </tr>
        </thead>
        <tbody>
        @foreach($venta->pagos as $index => $pago)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="section-title">Resumen de la Venta</div>
    <table class="summary">
        <tr>
            <td>Total de venta:</td>
            <td>${{ number_format($venta->total, 2) }}</td>
        </tr>
        <tr>
            <td>Total pagado:</td>
            <td>${{ number_format($venta->pagos->sum('monto'), 2) }}</td>
        </tr>
        <tr>
            <td>Estado:</td>
            <td><span class="badge"> CUENTA LÍQUIDADA</span></td>
        </tr>
    </table>

    <div class="footer">
        Gracias por confiar en Grupo MediBuy.<br>
        Este documento es válido como comprobante de pago completo.<br>
        Consulte términos y condiciones en www.grupomedibuy.com.
    </div>
</div>
</body>
</html>
