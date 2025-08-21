
@extends('layouts.app')

@section('title', 'Inventario')
@section('titulo', 'Inventario')

@section('content')
<link rel="stylesheet" href="{{ asset('css/inventario.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>


    <!-- Font Awesome (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
        <!-- Contenido Principal -->
        <div class="content">
            <!-- Formulario -->
            <div class="form-container-queja">
            <form action="{{ route('inventario') }}" method="get">
                    @csrf
                    <div class="titulo-seccion">Informacion del Equipo</div>
                    <div class="form-fila">
    <!-- Primera fila con cuatro campos -->
    <div class="row">
    <div class="col-md-3 mb-3">
        <label for="Tipo_de_equipo">Tipo de Equipo</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/tipo.png') }}" alt="Tipo de Equipo icon" class="icon">
            </div>
            <input type="text" id="Tipo_de_equipo" name="Tipo_de_equipo"
                   class="form-controln form-controlqueja-la iconized w-100" readonly>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="Subtipo">Subtipo de Equipo</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/producto.png') }}" alt="Subtipo de Equipo icon" class="icon">
            </div>
            <input type="text" id="Subtipo" name="Subtipo"
                   class="form-controln form-controlqueja-lb iconized w-100" readonly>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="Serie">Serie</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/serie.png') }}" alt="Número de serie icon" class="icon">
            </div>
            <input type="text" id="Serie" name="Serie"
                   class="form-controln form-controlqueja-lc iconized w-100" readonly>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="Marca">Marca</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/marca.png') }}" alt="Marca icon" class="icon">
            </div>
            <!-- SOLO MUESTRA, NO ENVÍA NADA -->
<input type="text" id="Marca" class="form-controln form-controlqueja-ld iconized w-100" readonly>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label for="Modelo">Modelo</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/modelo.png') }}" alt="Modelo icon" class="icon">
            </div>
            <input type="text" id="Modelo" class="form-controln form-controlqueja-le iconized w-100" readonly>

        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="Fecha_adquisicion">Fecha de Adquisición</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/modelo.png') }}" alt="Fecha de adquisición icon" class="icon">
            </div>
            <input type="text" id="Fecha_adquisicion" name="Fecha_adquisicion"
                   class="form-controln form-controlqueja-le iconized w-100" readonly>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="Año">Año</label>
        <div class="w-100">
            <input type="text" id="Año" name="Año"
                   class="form-controln form-controlqueja-lf w-100" readonly>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="photo">Evidencia</label>
        <div class="d-flex align-items-center w-100">
            <div class="icon-container">
                <img src="{{ asset('images/imagen.jpeg') }}" alt="Foto del equipo" class="icon-pin">
            </div>
            <button id="viewFilesButton" type="button"
                    class="form-controln form-controlqueja-li iconized w-100">
                Ver archivos
            </button>
        </div>
    </div>
</div>
</div>
<!-- Contenedor de imágenes -->
<div id="fileContainer" style="display: none;">
    @if(!empty($photos) && is_array($photos))
        @foreach($photos as $photo)
            <img class="evidence-image" src="{{ asset($photo) }}" alt="Evidencia" style="max-width: 1%; margin: -10px;">
        @endforeach
    @else
        <p>No hay evidencia disponible</p>
    @endif
</div>
<div class="row">
    <!-- Mensaje -->
    <div class="form-grupo col-12 col-md-6 px-3">
        <div class="label-container">
            <label for="message">Descripción</label>
            <div class="d-flex align-items-center">
                <textarea id="message" name="message" rows="4" class="form-controln form-controlqueja-lj w-100" readonly></textarea>
            </div>
        </div>
    </div>
    <!-- Observaciones -->
    <div class="form-grupo col-12 col-md-6 px-3">
        <div class="label-container">
            <label for="observaciones">Observaciones</label>
            <div class="d-flex align-items-center">
                <textarea id="observaciones" name="observaciones" rows="4" class="form-controln form-controlqueja-lj w-100" readonly></textarea>
            </div>
        </div>
    </div>
    <!-- Botón para imprimir el ticket/etiqueta con código de barras -->
<div id="barcodePrintContainer" style="margin-top:18px;display:none;">
    <button id="btnImprimirTicket" class="btn btn-pastel-barcode" style="
        background: #f0f4ff;
        color: #228be6;
        border: 1.5px solid #cfd8dc;
        border-radius: 1.2rem;
        font-weight: 600;
        padding: 6px 22px;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(34,139,230,0.04);
        transition: background .15s;
    ">
        <i class="fa fa-barcode"></i> Imprimir etiqueta
    </button>
</div>

    <div class="container">
    <div class="row align-items-center">
        <!-- Columna del video -->
        <div class="col-md-6">
            <div id="videoContainer" class="video-preview-container">
                <!-- Aquí se inyectará el video con JavaScript -->
            </div>
        </div>

        <!-- Columna de la firma digital -->
        <div class="col-md-6 text-center">
            <label for="firma" class="form-label">Firma Digital</label><br>
            <img id="firmaDigitalImagen" src="" alt="Firma Digital" style="max-width: 100%; height: auto; display: none;" class="img-fluid border border-dark rounded shadow">

            <!-- Nombre del firmante -->
            <div id="firmaUsuarioNombre" style="display: none; margin-top: 10px;">
                <strong>Firmado por:</strong> <br> <span id="nombreUsuarioTexto" class="text-primary fst-italic" style="font-style: italic; color: #0d6efd;"></span>
            </div>
        </div>
    </div>
</div>


<div class="division"></div>
<div class="titulo-seccion">Reporte Hojalatería</div>
<div id="procesosContainer"></div>

                    </div>

                </form>
            </div>
        </div>
      
        <div class="form-container-lower">
    <div class="titulo-seccion">Lista de productos</div>
    <table id="example" class="stripe row-border order-column" style="width:100%">
        <thead>
            <tr>
                <th>Estado terminado</th>
                <th>Tipo de Producto</th>
                <th>Subtipo de Producto</th>
                <th>Número de serie</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Fecha de creación</th> <!-- Nueva columna -->
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
                <tr>
                    <td>
                        @if($isPdfExport ?? false)
                            {{-- Texto plano para PDF --}}
                            @if($producto->estado_proceso)
                                {{ ucfirst($producto->estado_proceso) }}
                            @else
                                No disponible
                            @endif
                        @else
                            {{-- Etiquetas HTML para la vista web --}}
                            @if($producto->estado_proceso)
                                <span class="badge 
                                    @switch($producto->estado_proceso)
                                        @case('registro') badge-info @break
                                        @case('hojalateria') badge-primary @break
                                        @case('mantenimiento') badge-warning @break
                                        @case('stock') badge-success @break
                                        @case('defectuoso') badge-secondary @break
                                        @default badge-danger @break
                                    @endswitch
                                ">
                                    {{ ucfirst($producto->estado_proceso) }}
                                </span>
                            @else
                                <span class="badge badge-danger">No disponible</span>
                            @endif
                        @endif
                    </td>
                    <td>{{ $producto->tipo_equipo }}</td>
                    <td>{{ $producto->subtipo_equipo ?? 'N/A' }}</td>
                    <td>{{ $producto->numero_serie }}</td>
                    <td>{{ $producto->marca }}</td>
                    <td>{{ $producto->modelo }}</td>
                    <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td> <!-- Mostrar la fecha de creación -->
                    <td>
    <button class="btn btn-info btn-detalles" data-id="{{ $producto->id }}" data-estado="{{ $producto->estado_proceso }}">
        <i class="fa fa-eye"></i>
    </button>

    <button class="btn btn-info btn-hojalateria" id="btn-hojalateria" data-id="{{ $producto->id }}" data-estado="{{ $producto->estado_proceso }}">
        <i class="fa fa-tools"></i> 
    </button>

    <button class="btn btn-info btn-mantenimiento" id="btn-mantenimiento" data-id="{{ $producto->id }}" data-estado="{{ $producto->estado_proceso }}">
        <i class="fa fa-wrench"></i>
    </button>

    <button class="btn btn-info btn-stock" id="btn-stock" data-id="{{ $producto->id }}" data-estado="{{ $producto->estado_proceso }}">
        <i class="fa fa-box"></i> 
    </button>

    <button class="btn btn-info btn-vendido" id="btn-vendido" data-id="{{ $producto->id }}" data-estado="{{ $producto->estado_proceso }}">
        <i class="fa fa-check-circle"></i> 
    </button>

    <button class="btn btn-info btn-defectuoso" id="btn-defectuoso" data-id="{{ $producto->id }}" data-estado="{{ $producto->estado_proceso }}">
        <i class="fa fa-exclamation-triangle"></i>
    </button>
    <button class="btn btn-info btn-editar" id="btn-editar" data-id="{{ $producto->id }}">
    <i class="fa fa-edit"></i>
</button>


<button class="btn btn-info btn-eliminar" id="btn-borrar" data-id="{{ $producto->id }}">
    <i class="fa fa-trash"></i>
</button>

</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Estado terminado</th>
                <th>Tipo de Producto</th>
                <th>Subtipo de Producto</th>
                <th>Número de serie</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Fecha de creación</th> <!-- Nueva columna -->
                <th>Detalles</th>
            </tr>
        </tfoot>
    </table>
</div>
<!-- Modal de edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditar">
                @csrf
                <input type="hidden" id="registro_id">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Equipo</label>
                        <input type="text" name="tipo_equipo" id="Tipo_de_Equipo" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Subtipo de Equipo</label>
                        <input type="text" name="subtipo_equipo" id="Subtipo_de_Equipo" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Número de Serie</label>
                        <input type="text" name="numero_serie" id="Numero_de_Serie" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Marca</label>
                        <input type="text" name="marca" id="marca" class="form-control">
                    </div>
                    <div class="col-md-6">
    <label class="form-label">Modelo</label>
    <input type="text" name="modelo" id="modelo" class="form-control">
</div>
                    <div class="col-md-3">
                        <label class="form-label">Año</label>
                        <input type="text" name="anio" id="Año" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Fecha Inicial</label>
                        <input type="date" name="fecha_adquisicion" id="fecha_inicial" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.btn-eliminar').on('click', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'swal2-popup',
                title: 'swal2-title',
                htmlContainer: 'swal2-content',
                confirmButton: 'btn-custom-confirm',
                cancelButton: 'btn-custom-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/registro/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message,
                        customClass: {
                            popup: 'swal2-popup',
                            title: 'swal2-title',
                            htmlContainer: 'swal2-content',
                        },
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                          confirmButton: 'btn-custom-confirm'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    console.error('Error al eliminar:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el registro.',
                        customClass: {
                            popup: 'swal2-popup',
                            title: 'swal2-title',
                            htmlContainer: 'swal2-content',
                        },
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        confirmButtonClass: 'btn-custom-cancel'
                    });
                });
            }
        });
    });
});
</script>
<script>
$(document).ready(function () {
    $('.btn-editar').on('click', function () {
        const id = $(this).data('id');

        $.get(`/registro/${id}`, function (data) {
            console.log("Datos recibidos:", data);

            $('#registro_id').val(data.id);
            $('#Tipo_de_Equipo').val(data.tipo_equipo);
            $('#Subtipo_de_Equipo').val(data.subtipo_equipo);
            $('#Numero_de_Serie').val(data.numero_serie);
            $('#marca').val(data.marca);
            $('#modelo').val(data.modelo);
            $('#Año').val(data.anio);
            $('#descripcion').val(data.descripcion);
            $('#fecha_inicial').val(data.fecha_adquisicion ? data.fecha_adquisicion.substring(0, 10) : '');
            $('#observaciones').val(data.observaciones);

            const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();
        });
    });

    $('#formEditar').on('submit', function (e) {
        e.preventDefault();
        $('.text-danger').remove(); // Borra errores anteriores

        const id = $('#registro_id').val();
        const formData = $(this).serialize();

        $.ajax({
            url: `/registro/${id}`,
            type: 'PUT',
            data: formData,
            success: function (response) {
                if (response.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
                    modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: response.message,
                        customClass: {
                              confirmButton: 'btn-custom-confirm'
                        },
                        confirmButtonText: 'OK',
                        buttonsStyling: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errores = xhr.responseJSON.errors;
                    mostrarErrores(errores);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error inesperado al actualizar el registro.',
                        customClass: {
                              confirmButton: 'btn-custom-confirm'
                        },
                        confirmButtonText: 'OK',
                        buttonsStyling: false
                    });
                }
            }
        });
    });

    function mostrarErrores(errores) {
        for (const campo in errores) {
            const mensajes = errores[campo];
            const input = $(`[name="${campo}"]`);
            if (input.length) {
                input.after(`<small class="text-danger">${mensajes[0]}</small>`);
            }
        }
    }
});
</script>
<script>
function actualizarProcesosPendientes(id) {
    $.ajax({
        url: `/procesos/${id}/pendientes`,
        type: 'GET',
        success: function(procesosPendientes) {
            console.log(procesosPendientes);

            // Ocultar todos los botones primero
            $(".btn-proceso").hide();

            // Mostrar solo los botones correspondientes a los procesos pendientes
            procesosPendientes.forEach(proceso => {
                $(`#btn-${proceso}`).show();
            });
        },
        error: function() {
            console.error("Error al obtener los procesos pendientes.");
        }
    });
}

// Hojalatería
$(document).on('click', '.btn-hojalateria', function () {
    var id = $(this).data('id');
    window.location.href = "/procesos/" + id + "/hojalateria";
});

// Mantenimiento
$(document).on('click', '.btn-mantenimiento', function () {
    var id = $(this).data('id');
    window.location.href = "/procesos/" + id + "/mantenimiento";
});

// Stock
$(document).on('click', '.btn-stock', function () {
    var id = $(this).data('id');
    window.location.href = "/procesos/" + id + "/stock";
});

// Vendido
$(document).on('click', '.btn-vendido', function () {
    var id = $(this).data('id');
    window.location.href = "/procesos/" + id + "/vendido";
});

// NUEVO: Defectuoso
$(document).on('click', '.btn-defectuoso', function () {
    var id = $(this).data('id');
    window.location.href = "/procesos/" + id + "/defectuoso";
});

// Ejecutar la función de actualización al cargar
$(document).ready(function() {
    var id = $('#registro-id').val();
    if (id) {
        actualizarProcesosPendientes(id);
    } else {
        console.error("No se encontró el ID del registro.");
    }
});
</script>



<script>
$(document).ready(function () {
    $('.btn-hojalateria, .btn-mantenimiento, .btn-stock, .btn-vendido').on('click', function (e) {
        const estadoActual = $(this).data('estado');
        const boton = $(this);

        // Orden de los procesos
        const ordenProcesos = ['hojalateria', 'mantenimiento', 'stock', 'vendido'];

        // Si está defectuoso, bloquear todo
        if (estadoActual === 'defectuoso') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Equipo marcado como defectuoso',
                text: 'No se puede realizar ningún proceso adicional.',
                confirmButtonColor: '#d33',
            });
            return false;
        }

        // Detectar el tipo de botón que se presionó
        const tipoProceso = boton.hasClass('btn-hojalateria') ? 'hojalateria'
                          : boton.hasClass('btn-mantenimiento') ? 'mantenimiento'
                          : boton.hasClass('btn-stock') ? 'stock'
                          : boton.hasClass('btn-vendido') ? 'vendido'
                          : null;

        const indiceEstado = ordenProcesos.indexOf(estadoActual);
        const indiceBoton = ordenProcesos.indexOf(tipoProceso);

        // Saltarse un paso
        if (indiceBoton > indiceEstado + 1) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Proceso incompleto',
                text: `Primero debes completar el proceso de "${ordenProcesos[indiceEstado + 1]}".`,
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        // Intentando regresar a un proceso anterior
        if (indiceBoton < indiceEstado) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Proceso ya avanzado',
                text: `Este equipo ya está en el proceso de "${estadoActual}", no puedes regresar a "${tipoProceso}".`,
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        // Si todo bien, deja pasar
    });
});
</script>

<script>
$(document).ready(function () {
    // Detectamos todos los botones de procesos
    $('.btn-hojalateria, .btn-mantenimiento, .btn-stock, .btn-vendido, .btn-defectuoso').on('click', function (e) {
        let estado = $(this).data('estado');

        if (estado === 'defectuoso') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Equipo marcado como defectuoso',
                text: 'No se puede realizar ningún proceso adicional.',
                confirmButtonColor: '#d33',
            });
            return false;
        }

        // Validaciones secuenciales normales
        const boton = $(this);

        if (boton.hasClass('btn-mantenimiento') && estado !== 'hojalateria') {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Proceso incompleto',
                text: 'Primero debes completar el proceso de hojalatería.',
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        if (boton.hasClass('btn-stock') && estado !== 'mantenimiento') {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Proceso incompleto',
                text: 'Primero debes completar el proceso de mantenimiento.',
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        if (boton.hasClass('btn-vendido') && estado !== 'stock') {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Proceso incompleto',
                text: 'Primero debes pasar el producto a stock.',
                confirmButtonColor: '#3085d6',
            });
            return false;
        }
    });
});
</script>
<script>
$(document).ready(function () {
    // Inicializar contenedores y eventos
    function initFormContainers() {
        $('.form-container-queja').hide();

        if (!$('.form-container-queja .close-btn').length) {
            $('.form-container-queja').prepend('<button class="close-btn">&times;</button>');
        }

        $('.form-container-queja').on('click', '.close-btn', function () {
            $('.form-container-queja').slideUp();
        });
    }

    function handleViewDetailsClick() {
        $('#example tbody').on('click', 'button', function () {
            const productId = $(this).data('id');
            $('.form-container-queja').slideDown().attr('data-product-id', productId);
            $('.form-container-lower').slideDown();

            $('html, body').animate({
                scrollTop: $('.form-container-lower').offset().top
            }, 1000);
        });
    }

    function initDataTable() {
        $('#example tfoot th').each(function (i) {
            const title = $('#example thead th').eq($(this).index()).text();
            $(this).html(`<input type="text" placeholder="${title}" data-index="${i}" />`);
        });

        const table = $('#example').DataTable({
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
                },
                oAria: {
                    sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sSortDescending: ": Activar para ordenar la columna de manera descendente"
                }
            },
            dom: '<"length-and-buttons"lB>frtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<span class="button-icon"><img src="images/excel.png" alt="Excel" height="20"></span><span class="button-text">Excel</span>',
                    className: 'btn-excel',
                    title: 'Inventario Grupo MediBuy',
                    exportOptions: {
                        columns: ':not(:last-child)',
                        format: {
                            body: function (data) {
                                return data.toString().toUpperCase();
                            }
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<span class="button-icon"><img src="images/csv.png" alt="CSV" height="20"></span><span class="button-text">CSV</span>',
                    className: 'btn-csv',
                    title: 'Inventario Grupo MediBuy'
                },
                {
                    extend: 'pdfHtml5',
text: '<span class="button-icon"><img src="images/pdf.png" alt="PDF" height="20"></span><span class="button-text">PDF</span>',
className: 'btn-pdf',
title: 'Inventario Grupo MediBuy',
exportOptions: {
    columns: ':not(:last-child)', // Excluye la última columna (botón de detalles)
    format: {
        body: function (data, row, column, node) {
            // Convierte el contenido HTML a texto plano, pero deja las etiquetas intactas
            let plainText = $('<div>').html(data).text().trim(); // Obtén solo texto
            return plainText.toUpperCase(); // Devuelve en mayúsculas
        }
    }
},
customize: function (doc) {
    // Opcional: Personaliza el diseño del PDF
    doc.styles.tableBodyEven.alignment = 'center';
    doc.styles.tableBodyOdd.alignment = 'center';
    doc.styles.tableHeader.alignment = 'center';
}
}
            ],
            columnDefs: [
                {
                    targets: 0, // Define la columna con las etiquetas HTML
                    render: function (data, type, row) {
                        // Renderizar las etiquetas como HTML, sin convertirlas a texto plano
                        return $('<div>').html(data).text() === data ? data : data;
                    }
                }
            ]
        });

       // Aplicar estilos a las filas después de cada redibujo de la tabla (filtro, orden, etc.)
       table.on('draw', function () {
            applyRowStyling();
        });

        $(table.table().container()).on('keyup', 'tfoot input', function () {
            table
                .column($(this).data('index'))
                .search(this.value)
                .draw();
        });
    }
    function applyRowStyling() { 
    $('#example tbody tr').each(function () {
        const status = $(this).find('td').eq(0).find('span').text().trim();

        if (status === 'Stock') {
            $(this).addClass('row-success'); // Verde
        } else if (status === 'Vendido') {
            $(this).addClass('row-danger'); // Rojo
        } else if (status === 'Mantenimiento') {
            $(this).addClass('row-warning'); // Amarillo
        } else if (status === 'Registro') {
            $(this).addClass('row-info'); // Azul
        } else if (status === 'Hojalateria') {
            $(this).addClass('row-info'); // Azul más fuerte
        } else if (status === 'Defectuoso') {
            $(this).addClass('row-defectuoso'); // Gris
        }
    });
}
    initFormContainers();
    handleViewDetailsClick();
    initDataTable();
    applyRowStyling();
});
</script>
<script> 
$(document).ready(function () {
    $(document).on('click', '.btn-info', function () {
        var id = $(this).data('id');

        if (!id) {
            alert('No se encontró un ID válido.');
            return;
        }

        $('#loadingIndicator').show();

        $.ajax({
            url: '/obtener-detalles/' + id,
            method: 'GET',
            success: function (response) {
                $('#loadingIndicator').hide();

                if (response.error) {
                    alert(response.error);
                    return;
                }

                // Información general
                $('#Tipo_de_equipo').val(response.tipo_equipo);
                $('#Subtipo').val(response.subtipo_equipo);
                $('#Serie').val(response.numero_serie);
                $('#Marca').val(response.marca);
                $('#Modelo').val(response.modelo);
                $('#Año').val(response.anio);
                $('#EstadoActual').val(response.estado_actual);
                $('#Fecha_adquisicion').val(response.fecha_adquisicion);
                $('#Ultimo_Mantenimiento').val(response.ultimo_mantenimiento);
                $('#Proximo_Mantenimiento').val(response.proximo_mantenimiento);
                $('#message').val(response.descripcion);
                $('#observaciones').val(response.observaciones);

                $('#documentoPDF').next('iframe').remove();
                if (response.firma_digital) {
    $('#firmaDigitalImagen').attr('src', response.firma_digital).show();

    // Mostrar nombre del usuario si existe
    if (response.user_name) {
        $('#nombreUsuarioTexto').text(response.user_name);
        $('#firmaUsuarioNombre').show();
    } else {
        $('#firmaUsuarioNombre').hide();
    }
} else {
    $('#firmaDigitalImagen').hide();
    $('#firmaUsuarioNombre').hide();
}
                if (response.documentoPDF) {
                    const pdfPath = response.documentoPDF.startsWith('/storage/') || response.documentoPDF.startsWith('http')
                        ? response.documentoPDF
                        : '/storage/' + response.documentoPDF;

                    const isIOS = /iPhone|iPad|iPod/.test(navigator.userAgent);
                    const isAndroid = /Android/.test(navigator.userAgent);

                    if (isIOS) {
                        $('#documentoPDF').hide(); 
                        window.open(pdfPath, '_blank'); 
                    } else if (isAndroid) {
                        $('#documentoPDF').attr('src', 'https://docs.google.com/gview?embedded=true&url=' + encodeURIComponent(pdfPath)).show();
                    } else {
                        $('#documentoPDF').attr('src', pdfPath).show();
                    }

                    $('#downloadPDF').attr('href', pdfPath).show();
                } else {
                    $('#documentoPDF').attr('src', '').hide();
                }

                // Evidencias generales
                $('#fileContainer').empty();
                let evidencias = [response.evidencia1, response.evidencia2, response.evidencia3].filter(Boolean);

                if (evidencias.length > 0) {
                    evidencias.forEach(function (photo) {
                        const photoUrl = photo.startsWith('/storage/') || photo.startsWith('http') ? photo : `/storage/${photo}`;
                        $('#fileContainer').append(`<img class="evidence-image" src="${photoUrl}" alt="Evidencia de queja" style="max-width: 100%; display: none;">`);
                    });
                    $('#fileContainer').show();
                } else {
                    $('#fileContainer').hide();
                }

                // Video general
                if (response.video) {
                    const videoUrl = response.video.startsWith('/storage/') || response.video.startsWith('http') ? response.video : `/storage/${response.video}`;
                    $('#videoContainer').html(`
                        <h5 class="preview-title">Previsualización del Video:</h5>
                        <video controls class="video-preview" style="max-width: 100%; margin: 10px;">
                            <source src="${videoUrl}" type="video/mp4">
                            Tu navegador no soporta la reproducción de videos.
                        </video>
                    `).show();
                } else {
                    $('#videoContainer').html('<p>No hay video disponible</p>').show();
                }
                // ⬇️ BLOQUE NUEVO para mostrar el botón de imprimir ticket/etiqueta
                if (response.imprimir_barcode_url) {
                    $('#barcodePrintContainer').show();
                    $('#btnImprimirTicket').off('click').on('click', function() {
                        window.open(response.imprimir_barcode_url, '_blank');
                    });
                } else {
                    $('#barcodePrintContainer').hide();
                }

// Procesos
$('#procesosContainer').empty();

if (response.procesos && response.procesos.length > 0) {
    response.procesos.forEach(function (proceso) {
        let tipoBadge = `
            <div class="reporte-separador bg-secondary-subtle text-dark border-start border-4 border-dark mb-4 p-3 rounded">
                ⚙️ <strong>Proceso Técnico</strong>
            </div>`;

        // Fecha formateada
let fechaCreacion = 'Fecha no disponible';

if (proceso.created_at && typeof proceso.created_at === 'string') {
    const raw = proceso.created_at.replace(' ', 'T');
    const fecha = new Date(raw);

    if (!isNaN(fecha)) {
        fechaCreacion = fecha.toLocaleString('es-MX', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
    } else {
        console.warn(`❌ Fecha inválida para proceso ${proceso.id}:`, proceso.created_at);
    }
} else {
    console.warn(`❌ created_at no existe o no es string para proceso ${proceso.id}`);
}


        let procesoHTML = `
            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="proceso-wrapper text-center">
                    ${tipoBadge}
                    <label for="descripcion_proceso_${proceso.id}" class="video-label">
                        Descripción del Proceso (ID: ${proceso.id}):
                    </label>
                    <p class="text-muted mb-2">
                        🕒 Creado el: ${fechaCreacion}
                    </p>
                    <div class="d-flex justify-content-center">
                        <textarea id="descripcion_proceso_${proceso.id}" rows="4" class="form-controln form-controlqueja-lj w-100 text-center" readonly>${proceso.descripcion_proceso}</textarea>
                    </div>
                </div>
            </div>
        `;

        // Defectos
        if (proceso.defectos && proceso.defectos.trim() !== '') {
            let defectosList = proceso.defectos.split(',').map(defecto => {
                defecto = defecto.replace(/\\u[\dA-F]{4}/gi, match => decodeURIComponent('%' + match.replace(/\\u/g, '')));
                return `
                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="check-icon bg-success-subtle text-success-emphasis rounded-circle d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-lg"></i>
                        </span>
                        <span class="text-body">${defecto.trim()}</span>
                    </li>
                `;
            }).join('');

            procesoHTML += `
                <div class="col-12 d-flex justify-content-center align-items-center mt-4">
                    <div class="w-100" style="max-width: 600px;">
                        <label class="form-label fw-semibold text-dark fs-5 mb-3">
                            Defectos del Proceso (ID: ${proceso.id})
                        </label>
                        <ul class="list-group list-group-flush shadow-sm rounded-3 border">
                            ${defectosList}
                        </ul>
                    </div>
                </div>
            `;
        }

        // Ficha técnica
        if (proceso.ficha_tecnica_archivo && proceso.ficha_tecnica_archivo !== 'null') {
            procesoHTML += `
                <div class="col-12 d-flex justify-content-center align-items-center mt-3">
                    <div class="proceso-wrapper text-center">
                        <label class="video-label">Ficha Técnica (ID: ${proceso.id}):</label>
                        <iframe src="/storage/${proceso.ficha_tecnica_archivo}" class="ficha-tecnica d-none d-md-block" frameborder="0"></iframe>
                        <a href="/storage/${proceso.ficha_tecnica_archivo}" target="_blank" class="btn btn-primary d-block d-md-none mt-2">
                            📄 Ver Ficha Técnica
                        </a>
                    </div>
                </div>
            `;
        }

        // Evidencias
        let evidenciasProceso = [proceso.evidencia1, proceso.evidencia2, proceso.evidencia3].filter(Boolean);
        if (evidenciasProceso.length > 0) {
            procesoHTML += `<div class="evidencias-container mt-3">`;
            evidenciasProceso.forEach(ev => {
                const evUrl = ev.startsWith('http') ? ev : '/storage/' + ev;
                procesoHTML += evUrl.match(/\.(jpg|jpeg|png|gif)$/i)
                    ? `<img src="${evUrl}" alt="Evidencia" class="evidencia-img">`
                    : `<iframe src="${evUrl}" class="evidencia-pdf"></iframe>`;
            });
            procesoHTML += `</div>`;
        }

        // Video
        if (proceso.video) {
            const videoUrl = proceso.video.startsWith('http') ? proceso.video : '/storage/' + proceso.video;
            const shareUrl = encodeURIComponent(window.location.origin + videoUrl);
            procesoHTML += `
                <div class="col-12 d-flex justify-content-center align-items-center mt-3">
                    <div class="video-wrapper" style="max-width: 640px; width: 100%;">
                        <label class="video-label">Video del Proceso (ID: ${proceso.id}):</label>
                        <div class="video-container position-relative video-menu-wrapper">
                            <video controls class="mov-video" style="width: 100%; border-radius: 8px;">
                                <source src="${videoUrl}" type="video/mp4">
                                Tu navegador no soporta el formato de video.
                            </video>
                            <div class="menu-toggle position-absolute top-0 end-0 m-2" style="z-index: 10; cursor: pointer;">
                                <img src="https://img.icons8.com/material-outlined/24/000000/more.png" alt="Más opciones" style="width: 30px; height: 30px;">
                            </div>
                            <div class="menu-options position-absolute top-0 end-0 mt-5 me-2 p-2 bg-white shadow rounded border"
                                style="display: none; z-index: 20; min-width: 160px;">
                                <a href="https://wa.me/?text=${shareUrl}" target="_blank" 
                                class="d-block mb-1 text-decoration-none text-dark">
                                    📤 Compartir por WhatsApp
                                </a>
                                <a href="${videoUrl}" download 
                                class="d-block text-decoration-none text-dark">
                                    ⬇️ Descargar video
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        $('#procesosContainer').append(procesoHTML);
    });

    $('#procesosContainer').show();
} else {
    $('#procesosContainer').hide();
}


            },
            error: function (xhr, status, error) {
                $('#loadingIndicator').hide();
                console.error('Error fetching details:', error);
                alert('Ocurrió un error al obtener los detalles del producto.');
            }
        });
    });
});
</script>  
<script>
document.getElementById('viewFilesButton').addEventListener('click', function () {
    // Verificar si el contenedor tiene imágenes cargadas
    setTimeout(() => {
        const imageElements = document.querySelectorAll('#fileContainer img');
        const images = Array.from(imageElements).map(img => img.getAttribute('src'));

        if (images.length > 0) {
            let currentIndex = 0;
            let touchStartX = 0;
            let touchEndX = 0;

            function showImage(index) {
                Swal.fire({
                    html: `
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <img src="${images[index]}" 
                                style="width: 400px; height: 300px; object-fit: contain; border-radius: 8px; transition: transform 0.3s;" id="swalImage">
                            <div style="display: flex; gap: 5px; margin-top: 10px;">
                                ${images.map((_, i) => `
                                    <span style="width: 8px; height: 8px; background-color: ${i === index ? '#333' : '#ddd'}; border-radius: 50%;"></span>
                                `).join('')}
                            </div>
                        </div>
                    `,
                    width: 'auto',
                    padding: '15px',
                    background: '#f9f9f9',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'compact-popup',
                        closeButton: 'custom-close-button'
                    },
                    backdrop: 'rgba(0, 0, 0, 0.5)',
                    willOpen: (popup) => {
                        const image = popup.querySelector('img');
                        image.addEventListener('click', nextImage);

                        popup.addEventListener('keydown', handleKeyboard);
                        popup.addEventListener('touchstart', handleTouchStart);
                        popup.addEventListener('touchmove', handleTouchMove);
                        popup.addEventListener('touchend', handleTouchEnd);
                    },
                    willClose: (popup) => {
                        const image = popup.querySelector('img');
                        image.removeEventListener('click', nextImage);

                        popup.removeEventListener('keydown', handleKeyboard);
                        popup.removeEventListener('touchstart', handleTouchStart);
                        popup.removeEventListener('touchmove', handleTouchMove);
                        popup.removeEventListener('touchend', handleTouchEnd);
                    },
                });
            }

            function nextImage() {
                currentIndex = (currentIndex + 1) % images.length;
                showImage(currentIndex);
            }

            function prevImage() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                showImage(currentIndex);
            }

            function handleKeyboard(evt) {
                if (evt.key === 'ArrowRight') {
                    nextImage();
                } else if (evt.key === 'ArrowLeft') {
                    prevImage();
                }
            }

            function handleTouchStart(evt) {
                touchStartX = evt.touches[0].clientX;
            }

            function handleTouchMove(evt) {
                touchEndX = evt.touches[0].clientX;
            }

            function handleTouchEnd() {
                const diffX = touchEndX - touchStartX;
                if (diffX > 50) {
                    prevImage();
                } else if (diffX < -50) {
                    nextImage();
                }
            }

            showImage(currentIndex);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Sin imágenes',
                text: 'No hay evidencia disponible.',
                background: '#f9f9f9',
                confirmButtonColor: '#333'
            });
        }
    }, 200); // Agregar pequeño retraso para asegurarse de que las imágenes ya fueron agregadas
});



</script>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    const viewVideoButton = document.getElementById('viewVideoButton');
    const videoContainer = document.getElementById('videoContainer');
    const videoPreview = document.getElementById('videoPreview');
    const videoSource = document.getElementById('videoSource');

    viewVideoButton.addEventListener('click', function () {
        // Obtener la URL del video desde el atributo data-video-url
        const videoURL = viewVideoButton.getAttribute('data-video-url');

        // Verificar si la URL es válida
        if (videoURL) {
            videoSource.src = videoURL; // Asignar la URL al source del video
            videoPreview.load(); // Recargar el video para mostrarlo
            videoContainer.style.display = 'block'; // Mostrar el contenedor del video
        } else {
            alert('No se encontró un archivo de video.');
        }
    });
});
</script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

@endsection






