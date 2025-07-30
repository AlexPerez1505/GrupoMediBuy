<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
<style>
  /* ========== Global ========== */
  @page { margin: 1.5cm; }
  body {
    margin: 0;
    font-family: "Arial MT", Arial, sans-serif;
    color: #0056b3;
    font-size: 10px;
    line-height: 1.5;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }
  th, td {
    padding: 6px 8px;
    vertical-align: top;
    word-wrap: break-word;
  }
  th {
    text-align: left;
    font-weight: 600;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #333;
    background: #ecf0f1;
    border-bottom: 1px solid #ddd;
  }
  td {
    font-size: 9px;
    color: #34495e;
    border-bottom: 1px solid #eee;
  }
  tr:last-child td {
    border-bottom: none;
  }

  /* ========== Header ========== */
  .header {
    margin-bottom: 16px;
  }
  .header td {
    border: none;
    padding: 0;
  }
  .logo-cell {
    width: 20%;
  }
  .logo-cell img {
    max-width: 120px;
    display: block;
  }
  .title-cell {
    width: 60%;
    text-align: center;
    font-size: 22px;
    font-weight: 700;
    color: #2c3e50;
    letter-spacing: 1px;
  }
  .no-cell {
    width: 20%;
    text-align: right;
    font-size: 11px;
    font-weight: 600;
    color: #2c3e50;
  }

  /* ========== Section Titles ========== */
  .section {
    margin-bottom: 14px;
  }
  .section-title {
    background: #0056b3;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 6px;
  }

  /* ========== Inspection Layout ========== */
  .insp-layout {
    width: 100%;
    margin-bottom: 12px;
  }
  .insp-layout td {
    border: none;
    padding: 0;
    vertical-align: top;
  }
  .insp-left-cell {
    width: 58%;
    padding-right: 10px;
  }
  .insp-right-cell {
    width: 40%;
  }

  .insp-table {
    width: 100%;
  }
  .insp-table + .insp-table {
    margin-top: 8px;
  }
  .insp-table th {
    background: #e9f0fb;
    color: #0056b3;
    border: 1px solid #e9f0fb;
  }
  .insp-table td {
    padding: 6px 8px;
  }

  /* ========== Photo + Label ========== */
  .foto-table {
    width: 100%;
    border-collapse: collapse;
  }
  .foto-cell {
    border: 1px solid #ddd;
    border-bottom: none;
    padding: 6px;
    text-align: center;
    background: #fff;
  }
  .foto-cell img {
    max-width: 100%;
    height: auto;
  }
  .label-cell {
    border: 1px solid #ddd;
    border-top: none;
    background: #ecf0f1;
    color: #2c3e50;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    padding: 6px 8px;
    letter-spacing: 0.5px;
  }

  /* ========== Señal de Imagen ========== */
  .senial-table th {
    background: #ecf0f1;
    color: #2c3e50;
    font-size: 9px;
  }
  .senial-table td {
    padding: 6px 8px;
  }

  /* ========== Signatures ========== */
  .signatures {
    width: 100%;
    border-collapse: collapse;
    margin-top: 24px;
  }
  .signatures td {
    border: none;
    width: 50%;
    text-align: center;
    padding: 0;
    font-size: 9px;
    color: #34495e;
  }
  .signatures .line {
    border-top: 1px solid #2c3e50;
    width: 60%;
    margin: 0 auto 6px;
  }
</style>


</head>
<body>

  <!-- CABECERA -->
  <div class="header">
    <table>
      <tr>
        <td class="logo-cell">
          @if(file_exists(public_path('images/logomedy.png')))
            <img src="{{ public_path('images/logomedy.png') }}" alt="Logo">
          @endif
        </td>
        <td class="title-cell">ORDEN DE SERVICIO</td>
        <td class="no-cell">NO. {{ $orden->id ?? 'N/A' }}</td>
      </tr>
    </table>
  </div>

  <!-- DATOS DEL CLIENTE / ORDEN -->
  <div class="section">
    <table>
      <tr>
        <th colspan="2">DATOS DEL CLIENTE</th>
        <th colspan="2">DATOS DE LA ORDEN</th>
      </tr>
      <tr>
      <tr>
  <th>Cliente</th>
  <td>
    @php
      // Construimos el nombre completo y quitamos espacios sobrantes
      $fullName = trim(
        optional($orden->cliente)->nombre . ' ' .
        optional($orden->cliente)->apellido
      );
    @endphp

    {{-- Si no hay datos, mostramos “N/A” --}}
    {{ $fullName ?: 'N/A' }}
  </td>


        <th>Fecha Entrada</th>
        <td>
          {{ optional(\Carbon\Carbon::parse($orden->fecha_entrada))->format('d/m/Y') 
             ?? 'N/A' }}
        </td>
      </tr>
      <tr>
        <th>Responsable</th>
        <td>ING. JOEL DÍAZ</td>
        <th>Fecha Mantto.</th>
        <td>
          {{ optional(\Carbon\Carbon::parse($orden->fecha_mantenimiento))->format('d/m/Y') 
             ?? 'N/A' }}
        </td>
      </tr>
      <tr>
        <th>Teléfono</th>
        <td>{{ $orden->cliente->telefono ?? 'N/A' }}</td>
        <th>Próximo Mantto.</th>
        <td>
          {{ optional(\Carbon\Carbon::parse($orden->proximo_mantenimiento))->format('d/m/Y') 
             ?? 'N/A' }}
        </td>
      </tr>
      <tr>
        <th>Dirección</th>
        <td colspan="3">{{ $orden->cliente->comentarios ?? 'N/A' }}</td>
      </tr>
    </table>
  </div>

  <!-- DESCRIPCIÓN DEL EQUIPO -->
  <div class="section">
    <table>
      <tr>
        <th colspan="4" style="text-align:center;">DESCRIPCIÓN DEL EQUIPO</th>
      </tr>
      <tr>
        <th style="width:20%;">Nombre del equipo:</th>
        <td style="width:30%;">{{ $orden->aparato->nombre ?? 'N/A' }}</td>
        <th style="width:20%;">Marca / Modelo:</th>
        <td style="width:30%;">
          {{ $orden->aparato->marca  ?? 'N/A' }}
          /
          {{ $orden->aparato->modelo ?? 'N/A' }}
        </td>
      </tr>
      <tr>
        <th>Serie:</th>
        <td colspan="3">{{ $orden->aparato->serie ?? 'N/A' }}</td>
      </tr>
    </table>
  </div>

  <!-- INSPECCIÓN PREVENTIVA -->
  {{-- Aquí dejas tu HTML tal cual, pero si quieres inyectar dinámicamente resultados desde
      tu checklist, podrías decodificarlo así:
      @php
        $insp = json_decode($orden->checklist, true) ?? [];
      @endphp
      y luego hacer @foreach sobre cada sección. --}}

  <div class="section-title"> <strong>INSPECCIÓN PREVENTIVA DE MANTENIMIENTO</strong></div>
  <table class="insp-layout">
    <tr>
      <!-- Columna IZQUIERDA -->
      <td class="insp-left-cell">
        <table class="insp-table">
          <thead>
            <tr><th>Conexiones y Estructura</th><th>Resultado</th></tr>
          </thead>
          <tbody>
            @foreach($insp['conexiones'] ?? [] as $item)
              <tr>
                <td>{{ $item['nombre'] ?? '' }}</td>
                <td>{{ $item['resultado'] ?? '' }}</td>
              </tr>
            @endforeach
            @if(empty($insp['conexiones']))
              <tr><td colspan="2">— No hay datos —</td></tr>
            @endif
          </tbody>
        </table>

        <table class="insp-table">
          <thead>
            <tr><th>Botones y Controles</th><th>Resultado</th></tr>
          </thead>
          <tbody>
            @foreach($insp['botones'] ?? [] as $item)
              <tr>
                <td>{{ $item['nombre'] ?? '' }}</td>
                <td>{{ $item['resultado'] ?? '' }}</td>
              </tr>
            @endforeach
            @if(empty($insp['botones']))
              <tr><td colspan="2">— No hay datos —</td></tr>
            @endif
          </tbody>
        </table>

        <table class="insp-table">
          <thead>
            <tr><th>Componentes Internos</th><th>Mantto. Realizado</th></tr>
          </thead>
          <tbody>
            @foreach($insp['componentes'] ?? [] as $item)
              <tr>
                <td>{{ $item['nombre'] ?? '' }}</td>
                <td>{{ $item['resultado'] ?? '' }}</td>
              </tr>
            @endforeach
            @if(empty($insp['componentes']))
              <tr><td colspan="2">— No hay datos —</td></tr>
            @endif
          </tbody>
        </table>
      </td>

      <!-- Columna DERECHA -->
      <td class="insp-right-cell">
        <table class="foto-table">
          <tr>
            <td class="foto-cell">
              @if(file_exists(public_path('images/mantenimiento.png')))
                <img src="{{ public_path('images/mantenimiento.png') }}" alt="Equipo">
              @endif
            </td>
          </tr>
          <tr>
            <td class="label-cell">Equipo</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- SEÑAL DE IMAGEN -->
  <div class="section">
    <table class="senial-table">
      <tr><th>Señal de Imagen</th><th>Resultado</th></tr>
      @foreach($insp['senial'] ?? [] as $item)
        <tr>
          <td>{{ $item['nombre'] ?? '' }}</td>
          <td>{{ $item['resultado'] ?? '' }}</td>
        </tr>
      @endforeach
      @if(empty($insp['senial']))
        <tr><td colspan="2">— No hay datos —</td></tr>
      @endif
    </table>
  </div>

  <!-- FIRMAS -->
  <table class="signatures">
    <tr>
      <td>
        <div class="line"></div>
        <div><strong>ING. JOEL DÍAZ GARCIA</strong></div>
        <div>RESPONSABLE DEL MANTENIMIENTO</div>
      </td>
        <td>
          <div class="line"></div>
          <div>
            <strong>
              @php
                // Construimos el nombre completo del cliente
                $clienteFull = trim(
                  optional($orden->cliente)->nombre . ' ' .
                  optional($orden->cliente)->apellido
                );
              @endphp

              {{ $clienteFull ?: 'N/A' }}
            </strong>
          </div>
          <div>RECEPCIÓN DE EQUIPO</div>
        </td>
    </tr>
  </table>
</body>
</html>
