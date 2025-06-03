@extends('layouts.app')
@section('title', 'Historial Remisión')
@section('titulo', 'Historial Remisión')
@section('content')
<link rel="stylesheet" href="{{ asset('css/remision.css') }}?v={{ time() }}">
<!-- Tipografía moderna -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables + Export -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


<script>
    $(document).ready(function () {
        $('#ventasTable').DataTable({
              order: [[4, 'desc']], // Ordena por la columna de fecha en orden descendente
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

<!-- Contenido -->
<div class="container" style="margin-top:90px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Ventas</h2>
        <a href="{{ url('ventas/crear') }}" class="btn-add"><i class="fa-solid fa-plus"></i> Nueva Venta</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="ventasTable" class="table w-100">
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
                @forelse($ventas as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>
                            {{ isset($venta->cliente) ? mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') : 'N/A' }}
                        </td>
                        <td>
                            {{ isset($venta->usuario) ? mb_strtoupper($venta->usuario->name, 'UTF-8') : 'N/A' }}
                        </td>
                        <td>${{ number_format($venta->total, 2) }}</td>
                        <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($venta->plan) }}</td>
                        <td>
                            <a href="{{ url('ventas/'.$venta->id) }}" class="icon-btn" title="Ver"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ url('ventas/'.$venta->id.'/edit') }}" class="icon-btn" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ url('ventas/'.$venta->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar esta venta?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn text-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                            <a href="{{ url('ventas/'.$venta->id.'/pdf') }}" class="icon-btn" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No hay ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
