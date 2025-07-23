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
    color: #0056b3;
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
    background: #2c3e50;
    color: #fff;
    font-size: 11px;
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
     <img src="{{ public_path('images/logomedy.png') }}" alt="Logo">
        </td>
        <td class="title-cell">ORDEN DE SERVICIO</td>
        <td class="no-cell">NO. 123</td>
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
        <th>Cliente</th>
        <td>JUAN PÉREZ GÓMEZ</td>
        <th>Fecha Entrada</th>
        <td>15/08/2025</td>
      </tr>
      <tr>
        <th>Representante</th>
        <td>Ing. Laura Martínez</td>
        <th>Fecha Mantto.</th>
        <td>15/08/2025</td>
      </tr>
      <tr>
        <th>Teléfono</th>
        <td>55 1234 5678</td>
        <th>Próximo Mantto.</th>
        <td>15/12/2025</td>
      </tr>
      <tr>
        <th>Dirección</th>
        <td colspan="3">Calle Falsa 123, Ciudad</td>
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
        <td style="width:30%;">ENDOSCOPIO</td>
        <th style="width:20%;">Marca / Modelo:</th>
        <td style="width:30%;">Olympus / E12345</td>
      </tr>
      <tr>
        <th>Serie:</th>
        <td colspan="3">AB98765</td>
      </tr>
    </table>
  </div>

  <!-- INSPECCIÓN PREVENTIVA -->
  <div class="section-title">INSPECCIÓN PREVENTIVA DE MANTENIMIENTO</div>
  <table class="insp-layout">
    <tr>
      <!-- Columna IZQUIERDA -->
      <td class="insp-left-cell">
        <table class="insp-table">
          <thead>
            <tr><th>Conexiones y Estructura</th><th>Resultado</th></tr>
          </thead>
          <tbody>
            <tr><td>Conector de luz</td><td>Bueno y Funcional</td></tr>
            <tr><td>Conector universal</td><td>Funcional</td></tr>
            <tr><td>Cubierta distal</td><td>Reemplazada</td></tr>
            <tr><td>Tubo de succión</td><td>Limpio y Funcional</td></tr>
            <tr><td>Puerto de biopsia</td><td>Limpio y Funcional</td></tr>
          </tbody>
        </table>
        <table class="insp-table">
          <thead>
            <tr><th>Botones y Controles</th><th>Resultado</th></tr>
          </thead>
          <tbody>
            <tr><td>Botón de succión</td><td>Funcional</td></tr>
            <tr><td>Botón de aire/agua</td><td>Funcional</td></tr>
            <tr><td>Botón de irrigación</td><td>Funcional</td></tr>
            <tr><td>Perrilla de control</td><td>Bueno y Fluido</td></tr>
            <tr><td>Traba de flexión</td><td>Bueno y Funcional</td></tr>
          </tbody>
        </table>
        <table class="insp-table">
          <thead>
            <tr><th>Componentes Internos</th><th>Mantto. Realizado</th></tr>
          </thead>
          <tbody>
            <tr><td>Canal de trabajo</td><td>Limpio y Funcional</td></tr>
            <tr><td>Sistema interno sellado</td><td>Verificado sin fugas</td></tr>
            <tr><td>Estructura interna</td><td>Revisada y sin anomalías</td></tr>
            <tr><td>Sistema de estanqueidad</td><td>Prueba de fuga OK</td></tr>
          </tbody>
        </table>
      </td>

      <!-- Columna DERECHA -->
      <td class="insp-right-cell">
        <table class="foto-table">
          <tr>
            <td class="foto-cell">
              <img src="{{ public_path('images/mantenimiento.png') }}" alt="Equipo">
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
      <tr><td>Color</td><td>Bueno</td></tr>
      <tr><td>Interferencia</td><td>Ninguna</td></tr>
      <tr><td>Puntos Muertos</td><td>Ninguno</td></tr>
    </table>
  </div>

  <!-- FIRMAS -->

<table class="signatures">
  <tr>
    <td>
      <div class="line"></div>
      <div>RESPONSABLE DEL MANTENIMIENTO</div>
      <div>Ing. Pedro Gómez</div>
    </td>
    <td>
      <div class="line"></div>
      <div>RECEPCIÓN DE EQUIPO</div>
      <div>Dra. María López</div>
    </td>
  </tr>
</table>


</body>
</html>
