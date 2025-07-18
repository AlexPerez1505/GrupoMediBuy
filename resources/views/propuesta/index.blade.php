@extends('layouts.app')
@section('title', 'Cotizaciones ')
@section('titulo', 'Cotizaciones ')
@section('content')

<link rel="stylesheet" href="{{ asset('css/remision.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        $('#propuestasTable').DataTable({
            order: [[4, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            buttons: [
                { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel"></i> Excel', titleAttr: 'Exportar a Excel' },
                { extend: 'pdfHtml5', text: '<i class="fa-solid fa-file-pdf"></i> PDF', titleAttr: 'Exportar a PDF', orientation: 'landscape', pageSize: 'A4' },
                { extend: 'print', text: '<i class="fa-solid fa-print"></i> Imprimir', titleAttr: 'Imprimir tabla' }
            ]
        });
    });
</script>

<div class="container" style="margin-top: 90px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Historial de Cotizaciones</h2>
        <a href="{{ route('propuestas.create') }}" class="btn-add"><i class="fa-solid fa-plus"></i> Nueva Cotización</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="propuestasTable" class="table w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Usuario</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Plan</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propuestas as $propuesta)
                    <tr>
                        <td>{{ $propuesta->id }}</td>
                        <td>
                            {{ isset($propuesta->cliente) ? mb_strtoupper($propuesta->cliente->nombre . ' ' . $propuesta->cliente->apellido, 'UTF-8') : 'N/A' }}
                        </td>
                        <td>
                            {{ isset($propuesta->usuario) ? mb_strtoupper($propuesta->usuario->name, 'UTF-8') : 'N/A' }}
                        </td>
                        <td>${{ number_format($propuesta->total, 2) }}</td>
                        <td>{{ $propuesta->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($propuesta->plan) }}</td>
                        <td>
    <a href="{{ route('propuestas.show', $propuesta->id) }}" class="icon-btn" title="Ver">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('propuestas.pdf', $propuesta->id) }}" class="icon-btn" title="Descargar PDF">
        <i class="fa-solid fa-file-pdf"></i>
    </a>
    <a href="{{ route('propuestas.edit', $propuesta->id) }}" class="icon-btn" title="Editar">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
</td>

                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No hay propuestas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
