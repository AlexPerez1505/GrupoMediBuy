<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #2c3e50;
            background-color: #fff;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .header h2 {
            margin: 0;
            color: #1565c0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .details {
            margin-bottom: 1.2rem;
            padding: 1rem;
            background-color: #f5f8fa;
            border-radius: 8px;
        }

        .details p {
            margin: 0.3rem 0;
        }

        .details p strong {
            color: #37474f;
        }

        .divider {
            margin: 2rem 0;
            border-top: 1px solid #ccc;
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .footer p {
            margin: 0.5rem 0;
        }

        .amount {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2e7d32;
        }

        .label {
            font-weight: 500;
            color: #455a64;
        }

        .logo {
            max-height: 50px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Opcional: tu logo aquí --}}
            <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
            <h2>Recibo de Pago</h2>
            <p class="label">N° de recibo: #{{ $pago->id }}</p>
        </div>

        <div class="details">
            <p><strong>Cliente:</strong> {{ $pago->venta->cliente->nombre }} {{ $pago->venta->cliente->apellido }}</p>
            <p><strong>Fecha del pago:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>
            <p><strong>Método de pago:</strong> {{ $pago->metodo_pago }}</p>
        </div>

        <div class="details">
            <p class="label">Monto pagado:</p>
            <p class="amount">${{ number_format($pago->monto, 2) }}</p>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>Gracias por su pago.</p>
            <p>Este recibo es válido como comprobante oficial.</p>
        </div>
    </div>
</body>
</html>
