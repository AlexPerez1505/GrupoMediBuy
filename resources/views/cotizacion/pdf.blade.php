<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #{{ $cotizacion->id }}</title>
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
    align-items: center; /* Centra verticalmente */
    position: absolute;
    top: 20px; /* Ajusta la distancia desde arriba */
   
    width: 90%;
    padding: 0;
}

.logo {
    width: 180px;
    height: auto;
    
}

.cotizacion-info {
    font-size: 14px;
   margin-right:43px;
    text-align: right;
    margin-buttom: 50px;
    padding: 0;
    display: flex;
    align-items: center; /* Centra el texto con el logo */
    height: 100%;
    line-height: 1; /* Evita que el texto tenga separación extra */
}






        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.2;
            z-index: -1;
        }

        .info-box {
            border: 1px solid #ffffff;
            padding: 10px;
            margin-top: 35px;
            margin-buttom: 50px;
            font-size: 12px;
            background-color: #ffffff;
        }

        .table-container {
            position: relative;
        }

        .table-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            z-index: -1;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .terms-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .terms-table td {
        width: 50%;
        vertical-align: top;
        border: 1px solid #ffffff;
        padding: 10px;
        background-color: #ffffff;
        font-size: 12px;
    }

    .terms-table h3 {
        margin-top: 0;
    }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 95%;
            text-align: center;
            padding: 10px;
        }

        .footer img {
            width: 95%;
        }
/* Ajusta la tabla principal */
.layout-tabla {
    width: 100%;
    border-collapse: collapse;
}

/* Columnas del Plan de Pagos y Total Box */
.layout-tabla td {
    width: 50%;
    vertical-align: top; /* Alinea el contenido arriba */
}

/* Asegurar que la tabla del plan de pagos tenga el ancho correcto */
.plan-pagos-tabla {
    width: 100%;
    font-size: 12px; /* Establece el tamaño de la fuente a 12px */
    border-collapse: collapse;
}

/* Estilos para mover el total-box más a la derecha */
.total-box {
    text-align: right; /* Alinea el texto a la derecha */
    padding-left: 50px; /* Ajusta este valor para mover más a la derecha */
    font-size: 13px;
    
    padding: 10px;
}
/* Estilo para la celda del plan de pagos */
.plan-pagos {
    font-size: 12px; /* Establece el tamaño de la fuente a 12px */
}








    </style>
</head>
<body>

<!-- Encabezado con imagen -->
<div class="header">
    <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
    <div class="cotizacion-info">
        <span><strong>Cotización:</strong> <br>No.2025-{{ $cotizacion->id }}</br></span>
    </div>
</div>





   
    <div class="info-box">
    <p>
        <span style="float: right;"><strong>VIGENCIA:</strong> {{ $diasRestantes }} DÍAS</span>
    </p>
    <p><strong>CLIENTE:</strong> {{ $cotizacion['cliente'] ?? 'Desconocido' }}</p>
    <p><strong>FECHA:</strong> {{ \Carbon\Carbon::parse($cotizacion['created_at'])->format('Y-m-d') ?? 'Desconocido' }}</p>

     <!--<p><strong>Teléfono:</strong> {{ $cliente['telefono'] ?? 'No disponible' }}</p>
    <p><strong>Email:</strong> {{ $cliente['email'] ?? 'No disponible' }}</p>
    <p><strong>Ubicación:</strong> {{ $cliente['comentarios'] ?? 'No disponible' }}</p>-->
    <p><strong>LLUGAR DE COTIZACIÓN:</strong> {{ $cotizacion->lugar_cotizacion }}</p>
</div>
<!-- Tabla de Productos -->
<div class="table-container">
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
            @foreach($productos as $producto)
                <tr>
                    <td>
                        <img src="{{ public_path('storage/' . $producto['imagen']) }}" width="50" alt="Imagen del producto">
                    </td>
                    <td>
                    {{ $producto['tipo_equipo'] }}
                       {{ $producto['modelo'] }}<br>
                       {{ $producto['marca'] }}
                    </td>
                    <td>{{ $producto['cantidad'] }}</td>
                    <td>${{ number_format($producto['subtotal'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<table class="layout-tabla">
    <tr>
        <td class="plan-pagos">
            @if (!empty($cotizacion->plan_pagos))
                <h3 class="plan-pagos-titulo">Plan de Pagos</h3>
                <table class="plan-pagos-tabla">
                    @foreach(json_decode($cotizacion->plan_pagos, true) as $index => $pago)
                    <tr>
                        <td class="plan-pagos-celda">{{ $pago }}</td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </td>
        <td class="total-box">
            <p><strong>Subtotal:</strong> ${{ number_format($cotizacion->subtotal, 2) }}</p>
            <p><strong>Descuento:</strong> ${{ number_format($cotizacion->descuento, 2) }}</p>
            <p><strong>IVA:</strong> ${{ number_format($cotizacion->iva, 2) }}</p>
            <p class="highlight"><strong>Total:</strong> ${{ number_format($cotizacion->total, 2) }}</p>
        </td>
    </tr>
</table>


 <!-- Nota -->
 <h3>Nota</h3>
    <p>{{ $cotizacion->nota }}</p>

<!-- Sección de Términos y Condiciones en dos columnas -->
<table class="terms-table">
    <tr>
        <td class="terms">
            <h3>Términos y Condiciones</h3>
            <p>1. Garantía de 6 meses.</p>
            <p>2. Los precios están sujetos a cambios sin previo aviso.</p>
            <p>3. Los productos están sujetos a disponibilidad.</p>
            <p>4. Si requiere factura, se agregará el 16% de IVA al total del pedido.</p>
            <p>5. Bajo financiamiento, el equipo seguirá siendo propiedad de Grupo Medibuy hasta la liquidación total del pago.</p>
        </td>
        <td class="terms">
            <h3>DATOS BANCARIOS</h3>
            <p><strong>BENEFICIARIO:</strong> GABRIELA DIAZ GARCIA.</p>
            <p><strong>SANTANDER CUENTA:</strong> 60614821718</p>
            <p><strong>CLABE INTERBANCARIA:</strong> 014420606148217181</p>
        </td>
    </tr>
</table>

    <!-- Pie de Página con Imagen -->
    <div class="footer">
    <img src="{{ public_path('images/pie.jpeg') }}" alt="Grupo MediBuy" class="logo">
    </div>

</body>
</html>
