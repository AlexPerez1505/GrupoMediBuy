@extends('layouts.app')
@section('title', 'Préstamos')
@section('titulo', 'Préstamos')
@section('content')
@php
    registrarModuloUso('Prestamos', '/prestamos', 'fas fa-user-circle');
@endphp
<!-- Estilos personalizados -->
<link rel="stylesheet" href="{{ asset('css/prestamos.css') }}?v={{ time() }}">
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="container card-table">


    <!-- Encabezado y botón -->
    <div class="card-table-header">
        <h2>Lista de Préstamos</h2>
        <a href="{{ route('prestamos.create') }}" class="btn-crear">
        <i class="fa fa-plus"></i> Crear Nuevo
        </a>
    </div>

    <!-- Filtros -->
    <div class="filtro-busqueda">
    <select id="filtro-tipo" class="form-select">
        <option value="">Todos los tipos</option>
        <option value="activo">Activo</option>
        <option value="devuelto">Devuelto</option>
        <option value="retrasado">Retrasado</option>
        <option value="vendido">Vendido</option>
    </select>

    <input type="text" id="buscador" class="form-control" placeholder="Buscar...">
</div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabla -->
 <!-- Tabla -->
<div class="table-responsive">
    <table id="tablaPrestamos" class="table table-hover nowrap display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estado</th>
                <th>Recibió</th>
                <th>Serie</th>
                <th>F. Préstamo</th>
                <th>F. Estimada</th>
                <th>Responsable</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($prestamos as $prestamo)
    <tr class="estado-{{ $prestamo->estado }}">
        <td>{{ $prestamo->id }}</td>
        <td><span class="badge estado-{{ $prestamo->estado }}">{{ ucfirst($prestamo->estado) }}</span></td>
        <td>
    {{ $prestamo->cliente ? $prestamo->cliente->nombre . ' ' . $prestamo->cliente->apellido : '—' }}
</td>

        <td>{{ $prestamo->registro->numero_serie ?? '—' }}</td>
        <td>{{ $prestamo->fecha_prestamo }}</td>
        <td>{{ $prestamo->fecha_devolucion_estimada }}</td>
        <td>{{ $prestamo->user_name }}</td>
        <td>
    <!-- Botón VER (azul) -->
    <button class="btn btn-info btn-ver-detalles" data-prestamo='@json($prestamo)'>
        <i class="fa fa-eye icono-pequeno"></i>
    </button>

    <!-- Botón EDITAR (amarillo) -->
    <a href="{{ route('prestamos.edit', $prestamo->id) }}" class="btn btn-editar">
        <i class="fa fa-edit icono-pequeno"></i>
    </a>

    <!-- Botón ELIMINAR (rojo) -->
    <form action="{{ route('prestamos.destroy', $prestamo->id) }}" method="POST" class="d-inline delete-form">
    @csrf @method('DELETE')
    <button class="btn btn-eliminar">
        <i class="fa fa-trash-alt icono-pequeno"></i>
    </button>
</form>

</td>


    </tr>
@endforeach

        </tbody>
    </table>
</div>
</div>
<!-- DataTables + Export buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Botones exportar -->



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script>
    $(document).ready(function () {
        const table = $('#tablaPrestamos').DataTable({
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando del 0 al 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros)",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            dom: '<"top d-flex align-items-center justify-content-between gap-3 mb-3"lfB>rtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/excel.png') }}" alt="Excel" height="20"></span><span class="button-text">Excel</span>',
                    className: 'btn-excel',
                    title: 'Lista de Préstamos',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'csvHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/csv.png') }}" alt="CSV" height="20"></span><span class="button-text">CSV</span>',
                    className: 'btn-csv',
                    title: 'Lista de Préstamos',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/pdf.png') }}" alt="PDF" height="20"></span><span class="button-text">PDF</span>',
                    className: 'btn-pdf',
                    title: 'Lista de Préstamos',
                    exportOptions: { columns: ':not(:last-child)' }
                }
            ]
        });

       // Filtro por estado (columna 2 -> Estado)
$('#filtro-tipo').on('change', function () {
    const valor = this.value;
    if (valor) {
        const valorCapitalizado = valor.charAt(0).toUpperCase() + valor.slice(1);
        table.column(1).search('^' + valorCapitalizado + '$', true, false).draw(); // Cambié de 5 a 1
    } else {
        table.column(1).search('').draw(); // Cambié de 5 a 1
    }
});


        // Filtro general por texto
        $('#buscador').on('keyup', function () {
            table.search(this.value).draw();
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
$(document).ready(function () {
    $('#tablaPrestamos').on('click', '.btn-ver-detalles', function () {
        const p = $(this).data('prestamo');

        const firma = p.firmaDigital 
    ? `<img src="${p.firmaDigital}" alt="Firma Digital" class="firma-img">`
    : '<span class="placeholder">No disponible</span>';




        const htmlContent = `
        <div class="swal-prestamo-container">
           <div class="swal-section"> 
    <h3><i class="fas fa-user-circle"></i> Cliente</h3>
    <p>${p.cliente ? (p.cliente.nombre + ' ' + p.cliente.apellido) : '—'}</p>
</div>

            <div class="swal-section">
                <h3><i class="fas fa-box"></i> Número de Serie del Equipo</h3>
                <p>${p.registro?.numero_serie ?? '—'}</p>
            </div>
            <div class="swal-grid">
                <div>
                    <h4><i class="fas fa-calendar-plus"></i> F. Préstamo</h4>
                    <p>${p.fecha_prestamo}</p>
                </div>
                <div>
                    <h4><i class="fas fa-calendar-check"></i> F. Estimada</h4>
                    <p>${p.fecha_devolucion_estimada}</p>
                </div>
                <div>
                    <h4><i class="fas fa-calendar-times"></i> F. Real</h4>
                    <p>${p.fecha_devolucion_real ?? '—'}</p>
                </div>
            </div>
            <div class="swal-section">
                <h3><i class="fas fa-info-circle"></i> Condiciones</h3>
                <p>${p.condiciones_prestamo ?? '<span class="placeholder">—</span>'}</p>
            </div>
            <div class="swal-section">
                <h3><i class="fas fa-comment-alt"></i> Observaciones</h3>
                <p>${p.observaciones ?? '<span class="placeholder">—</span>'}</p>
            </div>
            <div class="swal-section">
                <h3><i class="fas fa-user-shield"></i> Usuario Responsable</h3>
                <p>${p.user_name}</p>
            </div>
            <div class="swal-section">
                <h3><i class="fas fa-signature"></i> Firma</h3>
                ${firma}
            </div>
        </div>
        `;

        Swal.fire({
            title: `<strong style="font-size: 20px;">Préstamo #${p.id}</strong>`,
            html: htmlContent,
            width: 700,
            padding: '2rem',
            showCloseButton: true,
            confirmButtonText: 'Cerrar',
            customClass: {
                popup: 'swal-prestamo-popup',
                confirmButton: 'swal-btn-confirm'
            }
        });
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                const formElement = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar!',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'border-radius-15',
                        title: 'font-weight-bold text-dark',
                        content: 'font-size-16',
                        confirmButton: 'btn-custom-confirm',
                        cancelButton: 'btn-custom-cancel',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        formElement.submit();
                    }
                });
            });
        });
    });
</script>



@endsection
