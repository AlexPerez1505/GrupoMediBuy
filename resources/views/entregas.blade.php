@extends('layouts.app')
@section('title', 'Lista de Entregas de Guías')
@section('titulo', 'Lista de Entregas de Guías')
@section('content')
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
<div class="form-container-lower">
    <div class="table-responsive">
        <table id="entregasTable" class="stripe row-border order-column" style="width:100%">
            <thead>
                <tr>
                    <th>Guía</th>
                    <th>Contenido</th>
                    <th>Serie</th>
                    <th>Destinatario</th>
                    <th>Observaciones</th>
                    <th>Fecha de Entrega</th>
                    <th>Entregado Por</th>
                    <th>Imagen</th> <!-- Nueva columna para la imagen -->
                </tr>
            </thead>
            <tbody>
                @foreach($entregas as $entrega)
                    <tr>
                        <td>{{ $entrega->guia->numero_rastreo }}</td>
                        <td>{{ $entrega->contenido }}</td>
                        <td>{{ $entrega->numero_serie }}</td>
                        <td>{{ $entrega->destinatario }}</td>
                        <td>{{ $entrega->observaciones }}</td>
                        <td>{{ \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y') }}</td>
                        <td>{{ $entrega->entregado_por }}</td>
                        <td>
                            @if($entrega->imagen)
                                <a href="{{ asset('storage/' . $entrega->imagen) }}" target="_blank" class="btn btn-primary btn-sm">Ver Imagen</a>
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th><input type="text" placeholder="Buscar Guía" /></th>
                    <th><input type="text" placeholder="Buscar Contenido" /></th>
                    <th><input type="text" placeholder="Buscar Serie" /></th>
                    <th><input type="text" placeholder="Buscar Destinatario" /></th>
                    <th><input type="text" placeholder="Buscar Observaciones" /></th>
                    <th><input type="text" placeholder="Buscar Fecha de Entrega" /></th>
                    <th><input type="text" placeholder="Buscar Entregado Por" /></th>
                    <th></th> <!-- Espacio para la imagen -->
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- Cargar jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function () {
    function initDataTable() {
        $('#entregasTable tfoot th').each(function (i) {
            const title = $('#entregasTable thead th').eq($(this).index()).text();
            $(this).html(`<input type="text" placeholder="Buscar ${title}" data-index="${i}" />`);
        });

        var table = $('#entregasTable').DataTable({
            language: {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                sSearch: "Buscar:",
                oPaginate: {
                    sFirst: "Primero",
                    sLast: "Último",
                    sNext: "Siguiente",
                    sPrevious: "Anterior"
                }
            },
            dom: '<"top d-flex align-items-center"lB>frtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/excel.png') }}" alt="Excel" height="20"></span><span class="button-text">Excel</span>',
                    className: 'btn-excel',
                    title: 'Lista de Entregas de Guías',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data, row, column, node) {
                                if ($(node).find('select').length) {
                                    return $(node).find('select option:selected').text();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/csv.png') }}" alt="CSV" height="20"></span><span class="button-text">CSV</span>',
                    className: 'btn-csv',
                    title: 'Lista de Entregas de Guías',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data, row, column, node) {
                                if ($(node).find('select').length) {
                                    return $(node).find('select option:selected').text();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/pdf.png') }}" alt="PDF" height="20"></span><span class="button-text">PDF</span>',
                    className: 'btn-pdf',
                    title: 'Lista de Entregas de Guías',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data, row, column, node) {
                                if ($(node).find('select').length) {
                                    return $(node).find('select option:selected').text();
                                }
                                return data;
                            }
                        }
                    }
                }
            ]
        });

        $('#entregasTable tfoot input').on('keyup change', function () {
            var index = $(this).data('index');
            table.column(index).search(this.value).draw();
        });
    }

    initDataTable();
});
</script>
</body>
@endsection
