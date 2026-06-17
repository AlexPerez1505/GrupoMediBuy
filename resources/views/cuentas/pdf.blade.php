<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cuentas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 4px;
            color: #339af0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px 6px;
            text-align: center;
        }
        th {
            background-color: #e3f2fd;
            font-size: 11px
        }
        tr:nth-child(even) td { background-color: #f9f9f9; }
        .total-strong {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 30px;
        }
        /* Sección gráficas */
        .graficas { width: 100%; margin-top: 10px; }
        .grafica-bloque { margin-bottom: 28px; }
        .grafica-titulo { font-size: 12px; font-weight: bold; color: #339af0;
                      margin-bottom: 10px; border-bottom: 1px solid #d0ebff; padding-bottom: 4px; }

        /* Barras horizontales */
        .barra-fila { margin-bottom: 6px; }
        .barra-label { font-size: 9px; color: #555; margin-bottom: 2px; white-space: nowrap;
                   overflow: hidden; text-overflow: ellipsis; max-width: 300px; }
        .barra-wrap { background: #f0f0f0; border-radius: 4px; height: 14px; width: 100%; }
        .barra-fill { height: 14px; border-radius: 4px; background: #74c0fc; display: inline-block; }
        .barra-valor { font-size: 9px; color: #333; margin-left: 4px; }

        /* Tabla camionetas */
        .cam-table { width: 60%; margin: 0 auto; }
        .cam-table th { background: #d3f9d8; }
    </style>
</head>
<body>

    <h2>Reporte de Cuentas</h2>
    <p class="subtitulo">Generado el {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Lugar</th>
                <th>Camioneta</th>
                <th>Casetas</th>
                <th>Gasolina</th>
                <th>Viáticos</th>
                <th>Adicional</th>
                <th>Descripción</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cuentas as $cuenta)
            <tr>
                
                <td>{{ $cuenta->lugar }}</td>
                <td>{{ $cuenta->camioneta }}</td>
                <td>${{ number_format($cuenta->casetas, 2) }}</td>
                <td>${{ number_format($cuenta->gasolina, 2) }}</td>
                <td>${{ number_format($cuenta->viaticos, 2) }}</td>
                <td>${{ number_format($cuenta->adicional, 2) }}</td>
                <td>{{ $cuenta->descripcion }}</td>
                <td class="total-strong">${{ number_format($cuenta->total, 2) }}</td>
                <td>{{ $cuenta->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{-- ── GRÁFICAS ── --}}
<div style="width:100%; margin-top: 20px;">
  <table style="width:100%; border:none;">
    <tr>
      <td style="width:50%; padding-right:10px; border:none;">
        <div style="text-align:center; font-size:12px; font-weight:bold;
                    color:#339af0; margin-bottom:6px;">
          Totales por Lugar
        </div>
        @if($chartLugar)
          <img src="{{ $chartLugar }}" style="width:100%; border-radius:8px;">
        @endif
      </td>
      <td style="width:50%; padding-left:10px; border:none;">
        <div style="text-align:center; font-size:12px; font-weight:bold;
                    color:#339af0; margin-bottom:6px;">
          Totales por Camioneta
        </div>
        @if($chartCamioneta)
          <img src="{{ $chartCamioneta }}" style="width:100%; border-radius:8px;">
        @endif
      </td>
    </tr>
  </table>
</div>
<div class="footer">
    Reporte generado automáticamente por el sistema - {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
