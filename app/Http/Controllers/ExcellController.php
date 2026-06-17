<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request; 

class ExcellController extends Controller
{
    public function exportarExcel(Request $request)
{
    $query = Producto::query();
    $catalogo = $request->input('export_catalogo') ?: $request->input('tipo');
    $marca    = $request->input('export_marca')    ?: $request->input('marca');
    $modelo   = $request->input('export_modelo')   ?: $request->input('modelo');
    if ($catalogo) $query->where('tipo_equipo', $catalogo);
    if ($marca)    $query->where('marca', $marca);
    if ($modelo)   $query->where('modelo', $modelo);
    $datos = $query->get();
    $filename = 'Productos_' . now()->format('Y-m-d') . '.xls';
    return response()->streamDownload(function () use ($datos) {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                      xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';

        echo '<Styles>
            <Style ss:ID="encabezado">
                <Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="12"/>
                <Interior ss:Color="#1565C0" ss:Pattern="Solid"/>
                <Alignment ss:Horizontal="Center"/>
            </Style>
            <Style ss:ID="fila_par">
                <Interior ss:Color="#EEF5FF" ss:Pattern="Solid"/>
            </Style>
            <Style ss:ID="moneda">
                <NumberFormat ss:Format="$#,##0.00"/>
            </Style>
        </Styles>';

        echo '<Worksheet ss:Name="Reporte">';
        echo '<Table>';

        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="150"/>';

        // Encabezados
        echo '<Row>';
        $encabezados = ['Categoría', 'Nombre', 'Modelo', 'Marca', 'Beneficio'];
        foreach ($encabezados as $enc) {
            echo "<Cell ss:StyleID=\"encabezado\"><Data ss:Type=\"String\">{$enc}</Data></Cell>";
        }
        echo '</Row>';

        // Datos
        $fila = 2;
        foreach ($datos as $item) {
            $estilo = ($fila % 2 === 0) ? 'ss:StyleID="fila_par"' : '';
            echo "<Row>";
            echo "<Cell {$estilo}><Data ss:Type=\"String\">" . htmlspecialchars($item->subtipo_equipo ?? '—') . "</Data></Cell>";
            echo "<Cell {$estilo}><Data ss:Type=\"String\">" . htmlspecialchars($item->tipo_equipo ?? '—') . "</Data></Cell>";
            echo "<Cell {$estilo}><Data ss:Type=\"String\">" . htmlspecialchars($item->modelo ?? '—') . "</Data></Cell>";
            echo "<Cell {$estilo}><Data ss:Type=\"String\">" . htmlspecialchars($item->marca ?? '—') . "</Data></Cell>";
            echo "<Cell {$estilo}><Data ss:Type=\"Number\">" . ($item->precio ?? 0) . "</Data></Cell>";
            echo "</Row>";
            $fila++;
        }

        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        exit;
        }, $filename, [
        'Content-Type' => 'application/vnd.ms-excel',
    ]);
}
    }