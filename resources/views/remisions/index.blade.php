@extends('layouts.app')

@section('title', 'Historial de Mantenimiento')
@section('titulo', 'Historial de Mantenimiento')

@section('content')
<link rel="stylesheet" href="{{ asset('css/orden.css') }}?v={{ time() }}">

<div class="container-remisiones" style="margin-top:120px;">
    <h2 class="titulo">Historial de Remisiones</h2>

    <table id="tabla-remisiones">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Restante</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($remisiones as $remision)
            <tr>
                <td>{{ $remision->id }}</td>
                <td>{{ $remision->cliente->nombre }}</td>
                <td>{{ $remision->user->name }}</td>
                <td>{{ $remision->created_at->format('d/m/Y') }}</td>
                <td>${{ number_format($remision->total, 2) }}</td>
                <td>${{ number_format($remision->restante, 2) }}</td>
                <td>
                    <a href="{{ route('remisions.show', $remision) }}" class="btn-link blue">Ver</a> |
                    <a href="{{ route('remisions.descargarPdf', $remision) }}" class="btn-link green">PDF</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<!-- jQuery, DataTables y Botones -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script>
$(document).ready(function () {
    $('#tabla-remisiones').DataTable({
        dom: 'Bfrtip',
        buttons: [
    {
        extend: 'copyHtml5',
        text: '<span class="button-icon"><img src="{{ asset('images/copy.png') }}" alt="Copiar" height="20"></span><span class="button-text">Copiar</span>',
        className: 'btn-copy',
        title: 'Historial de Cotizaciones de Grupo Medibuy',
        exportOptions: { columns: ':not(:last-child)' }
    },
    {
        extend: 'csvHtml5',
        text: '<span class="button-icon"><img src="{{ asset('images/csv.png') }}" alt="CSV" height="20"></span><span class="button-text">CSV</span>',
        className: 'btn-csv',
        title: 'Historial de Cotizaciones de Grupo Medibuy',
        exportOptions: { columns: ':not(:last-child)' }
    },
    {
        extend: 'excelHtml5',
        text: '<span class="button-icon"><img src="{{ asset('images/excel.png') }}" alt="Excel" height="20"></span><span class="button-text">Excel</span>',
        className: 'btn-excel',
        title: 'Historial de Cotizaciones de Grupo Medibuy',
        exportOptions: { columns: ':not(:last-child)' }
    },
    {
        extend: 'pdfHtml5',
        text: '<span class="button-icon"><img src="{{ asset('images/pdf.png') }}" alt="PDF" height="20"></span><span class="button-text">PDF</span>',
        className: 'btn-pdf',
        title: 'Historial de Cotizaciones de Grupo Medibuy',
        exportOptions: { columns: ':not(:last-child)' }
    },
    {
        extend: 'print',
        text: '<span class="button-icon"><img src="{{ asset('images/print.png') }}" alt="Imprimir" height="20"></span><span class="button-text">Imprimir</span>',
        className: 'btn-print',
        title: 'Historial de Cotizaciones de Grupo Medibuy',
        exportOptions: { columns: ':not(:last-child)' }
    }
],

        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>


@endsection
