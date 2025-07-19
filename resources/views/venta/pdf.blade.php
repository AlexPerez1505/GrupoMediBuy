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
            margin-top: -20px;
        }

.footer-container {
    position: fixed;
    bottom: 10px;
    left: 0;
    width: 100%;
    padding: 15px;
    text-align: center;
}

.footer {
    display: inline-block;
    text-align: left;
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
       <strong>RÈGIMEN FICAL::</strong> <em>Personas Fìsicas con Actividades Empresariales y Profesionales</em>
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
                <strong>{{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}</strong><br>
                {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} | 
                {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}<br>
                @if($item->registro?->numero_serie)
                    <span style="color: #1a73e8;">Serie: {{ $item->registro->numero_serie }}</span>
                @else
                    <span style="color: #999;">Sin número de serie</span>
                @endif
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
<div style="text-align: center; margin-top: 5px; font-family: Arial, sans-serif;">
    <p style="font-size: 12px; color: #333; margin-bottom: 12px;">
    <p><strong>Escanea este código QR para acceder a esta venta:</strong></p>
    <img src="data:image/png;base64,{{ $qr }}" alt="QR Code" style="width: 100px; height: 100px;">
</div>

<div class="footer-container">
    <div class="footer">
        <table cellpadding="4" cellspacing="0" style="font-family: Arial, sans-serif; font-size: 11px; color: #333;">
            <tr>
                <!-- Columna izquierda con logotipo + datos -->
<td valign="top" align="left">
    <div style="margin-top: 20px;"> <!-- 👈 Ajusta el valor de margin-top según lo que necesites -->
        <table cellpadding="0" cellspacing="0">
            <tr>
                <!-- Firma -->
                <td style="padding-right: 8px;" valign="middle">
                    <img src="{{ public_path('images/firma.png') }}" alt="Firma" width="130">
                </td>
                <!-- Nombre y cargo -->
                <td valign="middle">
                    <div style="font-weight: bold; font-size: 13px;">Anahí Tellez</div>
                    <div style="color: #777;">Gerente General</div>
                </td>
            </tr>
        </table>
    </div>
</td>
                <!-- Separador -->
                <td style="width: 20px;">
                 <!-- Línea divisoria -->
                <td width="2%" style="border-left: 1px solid #ccc;"></td>
</td>
                <!-- Columna derecha con info de contacto -->
                <td valign="top" align="left">
                    <div style="margin-bottom: 3px;"><strong style="color:#555;">Tel:</strong>+52 722 448 5191</div>
                    <div style="margin-bottom: 3px;"><strong style="color:#555;">Email:</strong> ventas@grupomedibuy.com</div>
                    <div style="margin-bottom: 3px;"><strong style="color:#555;">Web:</strong> grupomedibuy.com</div>
                    <div><strong style="color:#555;">Ubicación:</strong> Estado de México C.P. 52060</div>
                </td>
            </tr>
        </table>
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
    // Función para traducir meses inglés -> español
    function traducirMes($fecha) {
        $meses = [
            'January' => 'enero',
            'February' => 'febrero',
            'March' => 'marzo',
            'April' => 'abril',
            'May' => 'mayo',
            'June' => 'junio',
            'July' => 'julio',
            'August' => 'agosto',
            'September' => 'septiembre',
            'October' => 'octubre',
            'November' => 'noviembre',
            'December' => 'diciembre',
        ];

        $dia = $fecha->format('d');
        $mesIngles = $fecha->format('F');
        $mesEspanol = $meses[$mesIngles] ?? $mesIngles;
        $anio = $fecha->format('Y');

        return "{$dia} de {$mesEspanol} de {$anio}";
    }

    // Obtener los pagos desde la relación con la tabla pagos_financiamiento
    $pagosFinanciamiento = $venta->pagosFinanciamiento()->orderBy('fecha_pago')->get();

    $pagos = [];
    $montoInicial = 0;

    foreach ($pagosFinanciamiento as $pago) {
        $etiqueta = ucfirst($pago->descripcion);
        $fechaCarbon = \Carbon\Carbon::parse($pago->fecha_pago);
        $fecha = traducirMes($fechaCarbon);
        $monto = $pago->monto;

        if (strtolower($etiqueta) === 'pago inicial') {
            $montoInicial = $monto;
        }

        $pagos[] = "{$etiqueta} - {$fecha}: $" . number_format($monto, 2, '.', ',');
    }

    // Calcular datos financieros correctamente
    $total = $venta->total;
    $plazoMeses = count($pagos) - 1; // excluye el pago inicial
    $montoFinanciadoBase = $total - $montoInicial;
    $tasaInteresMensual = 0.05;

    // Usar pagos reales para calcular monto con intereses
    $totalPagosMensuales = $pagosFinanciamiento->filter(function ($p) {
        return strtolower($p->descripcion) !== 'pago inicial';
    })->sum('monto');

    $montoConIntereses = $totalPagosMensuales;

    $cuotaMensual = $plazoMeses > 0 
        ? $montoConIntereses / $plazoMeses 
        : 0;

    // Dividir pagos en columnas para mostrar
    $col1 = array_slice($pagos, 0, 5);
    $col2 = array_slice($pagos, 5);
@endphp

@if (!empty($pagos))
    <div style="margin-bottom: 1rem;">
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


<div style="margin-bottom: 1rem;">
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

        </div>
    @endif {{-- Cierre de @else --}}
</div> {{-- Cierre del div principal --}}

<div style="margin-bottom: 2rem;">
    <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
        Términos y Condiciones
    </h3>

    @php
        $mesesGarantia = $venta->meses_garantia ?? 6; // Por si no está definido, por defecto 6 meses
    @endphp

    @if($plan === 'contado')
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Este pago es único y debe realizarse al momento de la entrega o en la fecha acordada.</li>
            <li>El equipo será propiedad del cliente una vez confirmado el pago total.</li>
            <li>La garantía del equipo es de {{ $mesesGarantia }} meses a partir de la fecha de entrega.</li>
            <li>Los productos están sujetos a disponibilidad. Precios sujetos a cambio sin previo aviso.</li>
        </ul>
    @else
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Todos los pagos deberán realizarse puntualmente según el calendario acordado.</li>
            <li>En caso de retraso en el pago, se aplicará un cargo moratorio del 5% mensual sobre el monto vencido.</li>
            <li>El equipo permanecerá como propiedad de <strong>Grupo MediBuy</strong> hasta la liquidación total del pago.</li>
            <li>La garantía del equipo es de {{ $mesesGarantia }} meses a partir de la fecha de entrega.</li>
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
        Por favor, envíe el comprobante de pago al correo: <strong>compras@grupomedibuy.com</strong> o vía WhatsApp al <strong>+52 722 448 5191</strong>.
    </p>
</div>
@if($venta->plan !== 'contado')
<div style="page-break-before: always; font-family: Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.6;">
    <h2 style="text-align: center; color: #1e73be; font-weight: bold; margin-bottom: 1.5rem;">Contrato de Compra-Venta</h2>

    <p><strong>CONTRATO DE COMPRA-VENTA</strong> que celebran por una parte <strong>ANAHÍ TELLEZ ORTIZ</strong>, en su carácter de vendedora, y por la otra <strong>{{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</strong>, en su carácter de comprador, al tenor de las siguientes cláusulas:</p>

    <h4 style="color:#1e73be; margin-top: 1.5rem;">PRIMERA. OBJETO</h4>
    <p>La vendedora se obliga a transferir al comprador la propiedad de los equipos detallados en la remisión número <strong>2025-{{ $venta->id }}</strong>, y este se obliga a pagar el precio convenido.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">SEGUNDA. PRECIO Y FORMA DE PAGO</h4>
    <p>El precio total de la compraventa es de <strong>${{ number_format($venta->total, 2) }} MXN</strong>. 
    El comprador ha convenido en pagar dicha cantidad a crédito conforme al plan de pagos descrito en este documento.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">TERCERA. ENTREGA</h4>
    <p>La entrega de los equipos se hará en el domicilio del comprador o en el lugar designado, en perfectas condiciones de funcionamiento y acompañado de la garantía correspondiente.</p>

    @php
        $mesesGarantia = $venta->meses_garantia ?? 6; // 6 meses por defecto si no existe
    @endphp

    <h4 style="color:#1e73be; margin-top: 1rem;">CUARTA. GARANTÍA</h4>
    <p>
        La vendedora otorga una garantía de {{ $mesesGarantia }} meses sobre el funcionamiento de los equipos, contados a partir de la fecha de entrega.
    </p>

    <h4 style="color:#1e73be; margin-top: 1rem;">QUINTA. RESERVA DE DOMINIO</h4>
    <p>Hasta que no se liquide el total del precio convenido, los equipos seguirán siendo propiedad de la vendedora.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">SEXTA. JURISDICCIÓN</h4>
    <p>Para la interpretación y cumplimiento del presente contrato, las partes se someten a las leyes y tribunales del Estado de México, renunciando a cualquier otro fuero que pudiera corresponderles.</p>

    <div style="margin-top: 2rem;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <p><strong>LA VENDEDORA</strong></p>
                    <br><br>
                    <img src="{{ public_path('images/firma.png') }}" alt="Firma" width="130"><br>
                    <strong>ANAHÍ TÉLLEZ ORTIZ</strong><br>
                    Gerente General
                </td>
                <td style="width: 50%; text-align: center;">
                    <p><strong>EL COMPRADOR</strong></p>
                    <br><br><br><br>
                    <strong>{{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</strong><br>
                    Cliente
                </td>
            </tr>
        </table>
    </div>
</div>
@endif


</body>
</html>
