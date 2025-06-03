@extends('layouts.app')
@section('title', 'Edit Remisión')
@section('titulo', 'Edit Remisión')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(120deg, #f0f4ff, #e8f9f3);
    }

    h1, h3 {
        color: #3a3a3a;
        font-weight: 600;
    }

    .form-section {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
        border-left: 6px solid #a7d6ff;
    }

    label {
        font-weight: 500;
        color: #495057;
    }

    .form-control {
        border-radius: 0.6rem;
        border: 1px solid #d0dce9;
        background-color: #fefefe;
    }

    .form-control:focus {
        border-color: #a7d6ff;
        box-shadow: 0 0 0 0.2rem rgba(167, 214, 255, 0.4);
    }

    .btn-primary {
        background-color: #a7d6ff;
        border-color: #a7d6ff;
        color: #1f1f1f;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: #8ec9ff;
        border-color: #8ec9ff;
    }

    .btn-danger {
        background-color: #f8bcbc;
        border-color: #f8bcbc;
        color: #5c1a1a;
    }

    .btn-danger:hover {
        background-color: #f69c9c;
        border-color: #f69c9c;
    }

    .table thead {
        background: #e1f0ff;
    }

    .table th {
        color: #3a3a3a;
        font-weight: 500;
    }

    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }

    .table img {
        border-radius: 0.5rem;
    }

    .text-center {
        text-align: center;
    }

    select.form-control {
        background-color: #f4f8ff;
    }
</style>

<div class="container mt-4">
    <h1 class="mb-4 text-center">Editar Venta</h1>

    <form action="{{ route('ventas.update', $venta->id) }}" method="POST" id="form-venta">
        @csrf
        @method('PUT')

        <div class="row form-section">
            <div class="col-md-6 mb-3">
                <label for="cliente_id">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-control" required>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ $venta->cliente_id == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="lugar">Lugar</label>
                <input type="text" name="lugar" id="lugar" class="form-control" value="{{ $venta->lugar }}" required>
            </div>

            <div class="col-12 mb-3">
                <label for="nota">Nota</label>
                <textarea name="nota" id="nota" class="form-control" rows="2">{{ $venta->nota }}</textarea>
            </div>

            <div class="col-md-4 mb-3">
                <label for="subtotal">Subtotal</label>
                <input type="number" name="subtotal" id="subtotal" class="form-control" value="{{ $venta->subtotal }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="descuento">Descuento</label>
                <input type="number" name="descuento" id="descuento" class="form-control" value="{{ $venta->descuento }}" step="0.01">
            </div>

            <div class="col-md-4 mb-3">
                <label for="envio">Envío</label>
                <input type="number" name="envio" id="envio" class="form-control" value="{{ $venta->envio }}" step="0.01">
            </div>

            <div class="col-md-6 mb-3">
                <label for="iva">IVA</label>
                <input type="number" name="iva" id="iva" class="form-control" value="{{ $venta->iva }}" step="0.01">
            </div>

            <div class="col-md-6 mb-3">
                <label for="total">Total</label>
                <input type="number" name="total" id="total" class="form-control" value="{{ $venta->total }}" required readonly>
            </div>

            <div class="col-12 mb-3">
                <label for="plan">Plan</label>
                <input type="text" name="plan" id="plan" class="form-control" value="{{ $venta->plan }}">
            </div>
        </div>

        <div class="form-section">
            <h3 class="mb-3">Productos</h3>

            <div class="mb-3">
                <label for="producto">Seleccionar Producto</label>
                <select id="producto" class="form-control">
                    <option value="">Selecciona un producto...</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}"
                            data-nombre="{{ $producto->tipo_equipo }}"
                            data-modelo="{{ $producto->modelo }}"
                            data-marca="{{ $producto->marca }}"
                            data-precio="{{ $producto->precio }}"
                            data-imagen="{{ asset('storage/'.$producto->imagen) }}">
                            {{ $producto->tipo_equipo }} - ${{ $producto->precio }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table id="tabla-productos" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Equipo</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Sobreprecio</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->productos as $vp)
                            <tr data-id="{{ $vp->producto_id }}">
                                <td><img src="{{ asset('storage/' . $vp->producto->imagen) }}" width="50"></td>
                                <td class="equipo">{{ $vp->producto->tipo_equipo }}</td>
                                <td>{{ $vp->producto->modelo }}</td>
                                <td>{{ $vp->producto->marca }}</td>
                                <td>
                                    <input type="number" class="form-control cantidad" name="productos[{{ $vp->producto_id }}][cantidad]" value="{{ $vp->cantidad }}" onchange="actualizarSubtotal(this)">
                                </td>
                                <td class="subtotal">{{ $vp->subtotal }}</td>
                                <td>
                                    <input type="number" class="form-control sobreprecio" name="productos[{{ $vp->producto_id }}][sobreprecio]" value="{{ $vp->sobreprecio }}" onchange="actualizarSubtotal(this)">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button>
                                </td>
                                <input type="hidden" class="precio_unitario" name="productos[{{ $vp->producto_id }}][precio_unitario]" value="{{ $vp->precio_unitario }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <input type="hidden" name="productos_json" id="productos_json">

        <div class="text-center mb-5">
            <button type="submit" class="btn btn-primary px-5 py-2">Guardar Cambios</button>
        </div>
    </form>




    <script>
        $(document).ready(function () {
            // Agregar producto
            $('#producto').change(function () {
                const selected = $(this).find(':selected');
                const id = selected.val();
                const nombre = selected.data('nombre');
                const modelo = selected.data('modelo');
                const marca = selected.data('marca');
                const precio = parseFloat(selected.data('precio'));
                const imagen = selected.data('imagen');

                if (!id || !nombre) return;

                if ($(`#tabla-productos tbody tr[data-id="${id}"]`).length > 0) {
                    alert('Este producto ya ha sido agregado.');
                    return;
                }
                const fila = `
                <tr data-id="${id}" data-precio="${precio}">
                    <td><img src="${imagen}" width="50"></td>
                    <td class="equipo">${nombre}</td>
                    <td>${modelo}</td>
                    <td>${marca}</td>
                    <td><input type="number" class="form-control cantidad" name="productos[${id}][cantidad]" value="1" min="1" onchange="actualizarSubtotal(this)"></td>
                    <td class="subtotal">${precio.toFixed(2)}</td>
                    <td><input type="number" class="form-control sobreprecio" name="productos[${id}][sobreprecio]" value="0" min="0" onchange="actualizarSubtotal(this)"></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button>
                    </td>
                    <input type="hidden" class="precio_unitario" name="productos[${id}][precio_unitario]" value="${precio}">
                </tr>
                `;
                $('#tabla-productos tbody').append(fila);
                actualizarTotal();
                $(this).val('');  // Limpiar la selección
            });

            // Preparar JSON al enviar formulario
            $('#form-venta').submit(function () {
                prepararProductosJSON();
            });
        });

        function eliminarFila(btn) {
            $(btn).closest('tr').remove();
            actualizarTotal();
        }

        function actualizarSubtotal(input) {
            const tr = $(input).closest('tr');
            const cantidad = tr.find('.cantidad').val();
            const precioUnitario = tr.find('.precio_unitario').val();
            const sobreprecio = tr.find('.sobreprecio').val();
            const subtotal = (cantidad * precioUnitario) + parseFloat(sobreprecio || 0);

            tr.find('.subtotal').text(subtotal.toFixed(2));
            actualizarTotal();
        }
function actualizarSubtotal(input) {
    const tr = $(input).closest('tr');
    const cantidad = parseFloat(tr.find('.cantidad').val()) || 0;
    const precioUnitario = parseFloat(tr.find('.precio_unitario').val()) || 0;
    const sobreprecio = parseFloat(tr.find('.sobreprecio').val()) || 0;
    const subtotal = (cantidad * precioUnitario) + sobreprecio;

    tr.find('.subtotal').text(subtotal.toFixed(2));
    actualizarTotal();
}


function prepararProductosJSON() {
    const productos = [];
    $('#tabla-productos tbody tr').each(function () {
        const id = $(this).data('id'); // este es el producto_id
        const cantidad = $(this).find('.cantidad').val();
        const precioUnitario = $(this).find('.precio_unitario').val();
        const sobreprecio = $(this).find('.sobreprecio').val();
        const subtotal = $(this).find('.subtotal').text();

        productos.push({
            producto_id: id, // 👈 ESTO es lo que faltaba
            cantidad: cantidad,
            precio_unitario: precioUnitario,
            subtotal: subtotal,
            sobreprecio: sobreprecio
        });
    });

    $('#productos_json').val(JSON.stringify(productos));
}

    </script>
@endsection
