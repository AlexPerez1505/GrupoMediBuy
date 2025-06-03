<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión {{ $remision->cliente->nombre ?? 'Desconocido' }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

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
            padding: 0;
        }

        .logo {
            width: 180px;
            height: auto;
        }

        .remision-info {
            font-size: 14px;
            margin-right:43px;
            text-align: right;
            padding: 0;
            display: flex;
            align-items: center;
            height: 100%;
            line-height: 1;
        }


        .info-box {
            border: 1px solid #ffffff;
            padding: 10px;
            background-color: #ffffff;
            margin-top: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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

        .footer-box {
            text-align: right; /* Alinea el texto a la derecha */
            margin-top: 10px;
            font-size: 13px;
            padding: 10px;
        }

        .highlight {
            font-weight: bold;
            background-color: #1e73be;
            color: white;
            padding: 5px;
            display: inline-block;
        }

        .footer-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
        }

        .footer {
            text-align: center;
            padding: 10px;
        }

        .footer img {
            width: 95%;
        }
        .terms-table {
    width: 90%;
    margin: 0 auto;
    border-collapse: collapse;
    overflow: hidden; /* Evita que el contenido se desborde */
}

    .terms-table td {
        width: 50%;
        vertical-align: top;
        border: 1px solid #ffffff;
        padding: 10px;
        background-color: #ffffff;
        font-size: 12px;
        overflow: hidden; /* Evita que el contenido se desborde */
    }

    .terms-table h3 {
        margin-top: 0;
    }
    .terms {
    vertical-align: top;
    padding: 5px; /* Ajusta el espaciado interno */
    margin-top: 20px; /* Agrega espacio arriba de los términos y condiciones */
}

.terms p {
    margin: 2px 0; /* Reduce el margen entre los párrafos */
    line-height: 1.2; /* Ajusta el interlineado */
}
    </style>
</head>
<body>
<div class="header">
    <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
    <div class="remision-info">
        <strong>REMISIÓN</strong><br>
        <span style="color: red;">No.2025-{{ $remision->id }}</span></br>
    </div>
</div>

    <div class="info-box">
        <p><strong>CLIENTE:</strong> {{ $remision->cliente->nombre }} {{ $remision->cliente->apellido ?? 'No disponible' }}</p>
        <p><strong>TELEFONO:</strong> {{ $remision->cliente->telefono ?? 'No disponible' }}
        <span style="float: right;"><strong>FECHA:</strong> {{ $remision->created_at->format('Y-m-d') }}</p>
        <p><strong>DIRECCIÓN:</strong> {{ $remision->cliente->direccion ?? 'No disponible' }}
        <span style="float: right;"><strong>VIGENCIA:</strong> 10 DÍAS</span></p>
      
    </div>
    <table class="table">
    <thead>
        <tr>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>Descripción</th>
            <th>Importe Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($remision->items as $item)
            <tr>
                <td>{{ $item->cantidad }}</td>
                <td>{{ $item->unidad }}</td>
                <td style="text-align: justify;">
                    <strong>{{ $item->nombre_item }}</strong><br>
                    {{ $item->descripcion_item ?? 'No disponible' }}
                </td>
                <td>${{ number_format($item->importe_unitario, 2) }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer-box">
@php
    $totalACuenta = $remision->items->sum('a_cuenta');
@endphp

@if($totalACuenta > 0)
    <p><strong>A cuenta:</strong> ${{ number_format($totalACuenta, 2) }}</p>
    <p><strong>Restante:</strong> ${{ number_format($remision->items->sum('restante'), 2) }}</p>
@endif

    
    @if($remision->aplicar_iva && $remision->iva > 0)
        <p><strong>Subtotal:</strong> ${{ number_format($remision->subtotal, 2) }}</p>
        <p><strong>IVA (16%):</strong> ${{ number_format($remision->iva, 2) }}</p>
    @endif

    <p><strong>Total:</strong> ${{ number_format($remision->total ?? 0, 2) }}</p>
    <p><strong>Total en letra:</strong> {{ mb_strtoupper($remision->importe_letra ?? 'No disponible', 'UTF-8') }}</p>

</div>

 


    <div class="footer-container">
    <table class="terms-table">
        <tr>
        <td class="terms">
    <h3>Términos y Condiciones</h3>
    <p>1. Garantía de 6 meses, no cubre daños por uso indebido.</p>
    <p>2. Los precios pueden cambiar sin previo aviso.</p>
    <p>3. El cliente debe proporcionar información precisa sobre el funcionamiento del equipo.</p>
    <p>4. La garantía no cubre daños por negligencia, modificación no autorizada o intervención de terceros.</p>
    <p>5. Reparaciones adicionales requerirán autorización del cliente y costos extra.</p>
    <p>6. No nos hacemos responsables por pérdida o daño de accesorios no especificados.</p>
</td>

@if($remision->aplicar_iva)
    {{-- Si aplica IVA: Mostrar datos de Anahí --}}
    <td class="terms">
        <h3>DATOS BANCARIOS</h3>
        <p><strong>BENEFICIARIO:</strong> ANAHÍ TÉLLEZ ORTIZ.</p>
        <p><strong>BANCOMER CUENTA:</strong> 29 44 26 60 64</p>
        <p><strong>CLABE INTERBANCARIA:</strong> 0121 800 2944 2660 641</p>
        <p><strong>No. DE TARJETA:</strong> 4152 3135 5179 3107</p>
    </td>
@else
    {{-- Si no aplica IVA: Mostrar ambos de Gabriela --}}
    <td class="terms">
        <h3>DATOS BANCARIOS</h3>
        <p><strong>BENEFICIARIO:</strong> GABRIELA DIAZ GARCIA.</p>
        <p><strong>SANTANDER CUENTA:</strong> 60 61 48 21 718</p>
        <p><strong>CLABE INTERBANCARIA:</strong> 014 4206 0614 8217 181</p>
        <p><strong>No. DE TARJETA:</strong> 5579 0701 2907 7528</p>
    </td>
    <td class="terms">
        <h3>DATOS BANCARIOS</h3>
        <p><strong>BENEFICIARIO:</strong> GABRIELA DIAZ GARCIA.</p>
        <p><strong>BANAMEX CUENTA:</strong> 25 84 185</p>
        <p><strong>CLABE INTERBANCARIA:</strong> 002 4209 0432 584 1851</p>
        <p><strong>No. DE TARJETA:</strong> 5256 7861 2056 8690</p>
    </td>
@endif

           
        </tr>
    </table>
        <div class="footer">
            <img src="{{ public_path('images/pie.jpeg') }}" alt="Grupo MediBuy">
        </div>
    </div>
</body>
</html>
