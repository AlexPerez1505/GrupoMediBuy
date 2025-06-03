<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Venta #{{ $venta->id }}</title>
    <style>
        @page { margin: 0cm 0cm; }

        body {
            font-family: Arial, sans-serif;
            margin: 2cm 1.5cm 2cm 1.5cm;
            font-size: 12px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            top: 20px;
            width: 90%;
        }

        .logo {
            width: 180px;
            height: auto;
        }

        .venta-info {
            font-size: 14px;
            margin-right: 43px;
            text-align: right;
            display: flex;
            align-items: center;
            line-height: 1;
        }

        .info-box {
            border: 1px solid #ffffff;
            padding: 10px;
            margin-top: 10px;
            background-color: #ffffff;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .table th, .table td {
            border: 1px solid #ffffff;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: rgba(30, 115, 190, 0.8);
            color: white;
        }

        .highlight {
            font-weight: bold;
            background-color: #1e73be;
            color: white;
            padding: 5px;
            display: inline-block;
        }

        .total-box {
            text-align: right;
            font-size: 13px;
            padding: 10px;
            margin-top: 20px;
        }

        .footer-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 95%;
            padding: 15px;
        }

        .footer {
            text-align: center;
            padding: 10px;
        }

        .footer img {
            width: 90%;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
    <div class="venta-info">
        <span><strong>Remisión:</strong><br><span style="color: red;">No.2025-{{ $venta->id }}</span></span>
    </div>
</div>
<div class="info-box">
<p><strong>CLIENTE:</strong> 
    {{ mb_strtoupper(trim(($venta->cliente->nombre ?? '') . ' ' . ($venta->cliente->apellido ?? '')), 'UTF-8') ?: '<em>DESCONOCIDO</em>' }}
</p>
<p><strong>TELÉFONO:</strong> 
    {!! $venta->cliente->telefono 
        ? mb_strtoupper($venta->cliente->telefono, 'UTF-8') 
        : '<em>DESCONOCIDO</em>' !!}
            <span style="float: right;">
        <strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}
    </span>
</p>
<p><strong>DIRECCIÓN:</strong> 
    {!! $venta->cliente->comentarios 
        ? mb_strtoupper($venta->cliente->comentarios, 'UTF-8') 
        : '<em>DESCONOCIDO</em>' !!}
            <span style="float: right;">
        <strong>RFC Emisor:</strong> <em>TEOA890725GC0</em>
    </span>
</p>
    @if($venta->cliente->email)
    <p><strong>EMAIL:</strong> {{ $venta->cliente->email }}
          <span style="float: right;">
        <strong>EMISOR:</strong> <em>ANAHÍ TELLEZ ORTIZ</em>
    </span>
</p>
@endif
    <p><strong>LUGAR:</strong> {{ $venta->lugar }}
          <span style="float: right;">
        <strong>RÉGIMEN FISCAL:</strong> <em>PERSONA FISICA CON ACTIVIDAD EMPRESARIAL</em>
    </span>
</p>
</div>
<table class="table">
    <thead>
        <tr>
            <th>Equipo</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($venta->productos as $item)
        <tr>
            <td>
                <img src="{{ public_path('storage/' . ($item->producto->imagen ?? 'default.jpg')) }}" width="50" alt="Imagen del producto">
            </td>
            <td>
                {{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}
                {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }}<br>
                {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
            </td>
            <td>{{ $item->cantidad }}</td>
            <td>${{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="total-box">
    @if($venta->subtotal > 0)
        <p><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</p>
    @endif

    @if($venta->descuento > 0)
        <p><strong>Descuento:</strong> ${{ number_format($venta->descuento, 2) }}</p>
    @endif

    @if($venta->envio > 0)
        <p><strong>Envío:</strong> ${{ number_format($venta->envio, 2) }}</p>
    @endif

    @if($venta->iva > 0)
        <p><strong>IVA:</strong> ${{ number_format($venta->iva, 2) }}</p>
    @endif

    <p class="highlight"><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
</div>
@if($venta->nota)
    <p><strong>Nota:</strong> {{ $venta->nota }}</p>
@endif
<div style="text-align: center; margin-top: 40px;">
    <p><strong>Escanea este código QR para acceder a esta venta:</strong></p>
    <img src="data:image/png;base64,{{ $qr }}" alt="QR Code">
</div>
<div class="footer-container">
    <div class="footer">
        <img src="{{ public_path('images/pie.jpeg') }}" alt="Grupo MediBuy">
    </div>
</div>
<div style="page-break-before: always; margin-top: -0.5cm; font-family: Arial, sans-serif;"> 
    <h2 style="color: #1e73be; font-weight: bold; text-align: center; margin-bottom: 1.5rem;">
        Forma de Pago - Remisión 2025-{{ $venta->id }}
    </h2>

    @if($venta->plan === 'contado')
        <div style="margin-bottom: 2rem;">
            <div style="background-color: #eaf7ea; border-left: 4px solid #28a745; padding: 16px;">
                <h3 style="color: #28a745; margin: 0 0 10px;">Pago en una sola exhibición</h3>
                <p style="margin: 0; font-size: 13px; color: #333;">
                    Se ha registrado el pago completo de esta remisión. 
                    Agradecemos su preferencia.
                </p>
            </div>
        </div>
    @else
        @php
            $detalle = $venta->detalle_financiamiento;

            // Extraer todos los pagos con fechas y montos
            preg_match_all('/(Pago inicial|[Pp]rimer pago|[Ss]egundo pago|[Tt]ercer pago|[Cc]uarto pago|[Qq]uinto pago|[Ss]exto pago|[Ss]éptimo pago|[Oo]ctavo pago|[Nn]oveno pago|[Dd]écimo pago)\s*-\s*(\d{2} de \w+ de \d{4}):\s*\$(\d[\d,\.]*)/', $detalle, $coincidencias, PREG_SET_ORDER);

            $pagos = [];
            $montoInicial = 0;

            foreach ($coincidencias as $pago) {
                $etiqueta = ucfirst(trim($pago[1]));
                $fecha = $pago[2];
                $monto = floatval(str_replace([','], '', $pago[3]));

                if (strtolower($etiqueta) === 'pago inicial') {
                    $montoInicial = $monto;
                }

                $pagos[] = "{$etiqueta} - {$fecha}: $" . number_format($monto, 2, '.', ',');
            }

            // Calcular datos financieros
            $total = $venta->total;
            $plazoMeses = count($pagos) - 1; // Excluye pago inicial
            $montoFinanciadoBase = $total - $montoInicial;
            $tasaInteresMensual = 0.05;

            $montoConIntereses = $plazoMeses > 0 
                ? $montoFinanciadoBase * pow(1 + $tasaInteresMensual, $plazoMeses) 
                : $montoFinanciadoBase;

            $cuotaMensual = $plazoMeses > 0 
                ? $montoConIntereses / $plazoMeses 
                : 0;

            // Dividir en 2 columnas
            $col1 = array_slice($pagos, 0, 5);
            $col2 = array_slice($pagos, 5);
        @endphp

        @if (!empty($pagos))
            <div style="margin-bottom: 1.5rem;">
                <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
                    Detalles del Financiamiento
                </h3>

                <table width="100%" style="background-color: #eef4fb; border-left: 4px solid #1e73be; font-size: 13px; padding: 10px;">
                    <tr valign="top">
                        <td width="50%" style="padding-right: 15px;">
                            @foreach ($col1 as $linea)
                                <p style="margin: 0 0 6px; color: #333;"><strong>{{ $linea }}</strong></p>
                            @endforeach
                        </td>
                        <td width="50%">
                            @foreach ($col2 as $linea)
                                <p style="margin: 0 0 6px; color: #333;"><strong>{{ $linea }}</strong></p>
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <div style="margin-bottom: 2rem;">
            <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
                Resumen del Financiamiento
            </h3>
            <table style="width: 100%; font-size: 13px; margin-top: 0.5rem;">
                <tr>
                    <td><strong>Total de la venta:</strong></td>
                    <td>${{ number_format($total, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><strong>Pago inicial:</strong></td>
                    <td>${{ number_format($montoInicial, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><strong>Monto financiado (sin intereses):</strong></td>
                    <td>${{ number_format($montoFinanciadoBase, 2, '.', ',') }}</td>
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
                        <td><span style="color: #1e73be; font-weight: bold;">
                            ${{ number_format($montoConIntereses, 2, '.', ',') }}
                        </span></td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
</div>
<div style="margin-bottom: 2rem;">
    <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
        Términos y Condiciones
    </h3>

    @if($plan === 'contado')
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Este pago es único y debe realizarse al momento de la entrega o en la fecha acordada.</li>
            <li>El equipo será propiedad del cliente una vez confirmado el pago total.</li>
            <li>La garantía del equipo es de 6 meses a partir de la fecha de entrega.</li>
            <li>Los productos están sujetos a disponibilidad. Precios sujetos a cambio sin previo aviso.</li>
        </ul>
    @else
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Todos los pagos deberán realizarse puntualmente según el calendario acordado.</li>
            <li>En caso de retraso en el pago, se aplicará un cargo moratorio del 5% mensual sobre el monto vencido.</li>
            <li>El equipo permanecerá como propiedad de <strong>Grupo MediBuy</strong> hasta la liquidación total del pago.</li>
            <li>La garantía del equipo es de 6 meses a partir de la fecha de entrega.</li>
            <li>Los precios pueden cambiar sin previo aviso. Los productos están sujetos a disponibilidad.</li>
            <li>Cualquier ajuste a las condiciones de pago deberá ser autorizado por escrito por la empresa.</li>
        </ul>
    @endif
</div>


<div style="margin-bottom: 2rem;">
    <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
        Datos Bancarios para Transferencia
    </h3>
    @if (empty($venta->iva) || $venta->iva == 0 || $venta->iva == 0.0)
        <table style="width: 100%; font-size: 13px; margin-bottom: 1rem;">
            <tr><td><strong>Banco:</strong> </td><td>Santander</td></tr>
            <tr><td><strong>Nombre del beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>014 4206 0614 8217 181</td></tr>
            <tr><td><strong>No. de Tarjeta:</strong></td><td>5579 0701 2907 7528</td></tr>
             <tr>
            <td><strong>Concepto:</strong></td>
            <td>Remisión 2025-{{ $venta->id }} - {{ mb_strtoupper($venta->cliente->nombre) }}</td>
        </tr>
        </table>
        <table style="width: 100%; font-size: 13px;">
            <tr><td><strong>Banco:</strong></td> <td>Banamex</td></tr>
            <tr><td><strong>Nombre del beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>002 4209 0432 584 1851</td></tr>
            <tr><td><strong>No. de Tarjeta:</strong></td><td>5256 7861 2056 8690</td></tr>
             <tr>
            <td><strong>Concepto:</strong></td>
            <td>Remisión 2025-{{ $venta->id }} - {{ mb_strtoupper($venta->cliente->nombre) }}</td>
        </tr>
        </table>
    @elseif ($venta->iva > 1)
        <table style="width: 100%; font-size: 13px;">
            <tr><td><strong>Banco:</strong></td> <td>Bancomer</td></tr>
            <tr><td><strong>Nombre del beneficiario:</strong></td><td>Anahí Téllez Ortiz</td></tr>
            <tr><td><strong>Cuenta:</strong></td><td>29 44 26 60 64</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>0121 800 2944 2660 641</td></tr>
            <tr><td><strong>No. de Tarjeta:</strong></td><td>4152 3135 5179 3107</td></tr>
             <tr>
            <td><strong>Concepto:</strong></td>
            <td>Remisión 2025-{{ $venta->id }} - {{ mb_strtoupper($venta->cliente->nombre) }}</td>
        </tr>
        </table>
    @endif
    <p style="margin-top: 0.5rem; font-size: 12px;">
        Por favor, envíe el comprobante de pago al correo: <strong>pagos@grupomedibuy.com</strong> o vía WhatsApp al <strong>+52 722 448 5191</strong>.
    </p>
</div>
</body>
</html>
