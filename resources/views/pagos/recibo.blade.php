<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        @page {
            size: letter;
            margin: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            background-color: #ffffff;
            color: #333333;
            margin: 0;
            height: 100%;
            position: relative;
        }

        .contenedor {
            position: absolute;
            bottom: 0;
            left: 60px;
            right: 60px;
            height: 50%;
            border-top: 1px solid #ccc;
            padding: 30px 0;
        }

        h2 {
            text-align: center;
            color: #1d3557;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .resumen {
            padding: 15px 20px;
            background-color: #f7fafd;
            border-left: 4px solid #457b9d;
            border-radius: 6px;
            margin-bottom: 25px;
            max-width: 450px;
        }

        p {
            margin: 5px 0;
            font-size: 13px;
        }

        h4 {
            color: #1d3557;
            font-size: 14px;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
        }

        th {
            background-color: #eaf4fb;
            color: #1d3557;
            font-weight: 600;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e5e5e5;
        }

        .firma {
            margin-top: 35px;
            text-align: right;
            font-size: 12px;
        }

        .firma-linea {
            margin-top: 8px;
            border-top: 1px solid #999;
            width: 220px;
            display: inline-block;
        }

        footer {
            margin-top: 30px;
            font-size: 11px;
            text-align: center;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Recibo de Pago</h2>

        <div class="resumen">
            <p><strong>Cliente:</strong> {{ $item->remision->cliente->nombre }}</p>
            <p><strong>Ítem:</strong> {{ $item->nombre_item }}</p>
            <p><strong>Total:</strong> ${{ number_format($item->subtotal, 2) }}</p>
            <p><strong>Pagado:</strong> ${{ number_format($item->a_cuenta, 2) }}</p>
            <p><strong>Restante:</strong> ${{ number_format($item->restante, 2) }}</p>
        </div>

        <h4>Pagos Realizados</h4>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Método de Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->pagos as $pago)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                        <td>${{ number_format($pago->monto, 2) }}</td>
                        <td>{{ $pago->metodo_pago }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="firma">
            Firma del Responsable:
            <div class="firma-linea"></div>
        </div>

        <footer>
            Grupo Medibuy · www.grupomedibuy.com · Xonacatlán, Estado de México · Tel. 729 390 3384
        </footer>
    </div>
</body>
</html>
