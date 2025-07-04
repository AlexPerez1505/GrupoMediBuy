@extends('layouts.app')
@section('title', 'Edit Remisión')
@section('titulo', 'Edit Remisión')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(120deg, #f0f4ff, #e8f9f3);
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.95rem;
    }

    h1, h3, h4 {
        color: #333;
        font-weight: 600;
    }

    .form-section {
        background: #ffffff;
        border-radius: 0.8rem;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #8ec9ff;
    }

    @media (min-width: 768px) {
        .form-section.double {
            display: flex;
            gap: 1.5rem;
        }

        .form-section.double > .form-section-inner {
            flex: 1;
        }
    }

    label {
        font-weight: 500;
        color: #444;
        margin-bottom: 0.4rem;
    }

    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #d0dce9;
        background-color: #fff;
        font-size: 0.92rem;
    }

    .form-control:focus {
        border-color: #8ec9ff;
        box-shadow: 0 0 0 0.15rem rgba(167, 214, 255, 0.35);
    }

    .btn {
        font-weight: 500;
        border-radius: 0.5rem;
    }

    .btn-primary {
        background-color: #8ec9ff;
        border-color: #8ec9ff;
        color: #1f1f1f;
    }

    .btn-primary:hover {
        background-color: #73bdf8;
        border-color: #73bdf8;
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

    .btn-success {
        background-color: #c1f3d3;
        border-color: #c1f3d3;
        color: #215c3b;
    }

    .btn-success:hover {
        background-color: #a8e9bf;
    }

    .table thead {
        background: #e7f3ff;
    }

    .table th {
        color: #333;
        font-weight: 500;
        font-size: 0.92rem;
    }

    .table td {
        font-size: 0.9rem;
    }

    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }

    .table img {
        border-radius: 0.4rem;
    }

    .alert-danger {
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
    }
</style>

<div class="container mt-4">
    <h1 class="mb-4 text-center">Editar Venta</h1>

    <form action="{{ route('ventas.update', $venta->id) }}" method="POST" id="form-venta" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <!-- Productos -->
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

        <!-- Datos Cliente y Totales -->
        <div class="form-section double">
            <div class="form-section-inner">
                <div class="mb-3">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control" required>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $venta->cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="lugar">Lugar</label>
                    <input type="text" name="lugar" id="lugar" class="form-control" value="{{ $venta->lugar }}" required>
                </div>
                <div class="mb-3">
                    <label for="nota">Nota</label>
                    <textarea name="nota" id="nota" class="form-control" rows="2">{{ $venta->nota }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="plan">Plan</label>
                    <input type="text" name="plan" id="plan" class="form-control" value="{{ $venta->plan }}">
                </div>
            </div>

            <div class="form-section-inner">
                <div class="mb-3">
                    <label for="subtotal">Subtotal</label>
                    <input type="number" name="subtotal" id="subtotal" class="form-control" value="{{ $venta->subtotal }}" required>
                </div>
                <div class="mb-3">
                    <label for="descuento">Descuento</label>
                    <input type="number" name="descuento" id="descuento" class="form-control" value="{{ $venta->descuento }}" step="0.01" oninput="actualizarTotal()">
                </div>
                <div class="mb-3">
                    <label for="envio">Envío</label>
                    <input type="number" name="envio" id="envio" class="form-control" value="{{ $venta->envio }}" step="0.01" oninput="actualizarTotal()">
                </div>
                <div class="mb-3">
                    <label for="iva">IVA</label>
                    <input type="number" name="iva" id="iva" class="form-control" readonly>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" id="toggleIva">
                        <label class="form-check-label" for="toggleIva">Aplicar IVA (16%)</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total">Total</label>
                    <input type="number" name="total" id="total" class="form-control" value="{{ $venta->total }}" required readonly>
                </div>
            </div>
        </div>
<!-- Pagos Planeados -->
<div class="form-section">
    <h4 class="mb-3">Pagos Planeados (Financiamiento)</h4>
    <table id="tabla-pagos" class="table table-bordered">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Documento (PDF)</th>
                <th>¿Eliminar?</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->pagoFinanciamiento as $pago)
            <tr>
                <td>
                    <input type="text" name="pagos_financiamiento[{{ $pago->id }}][descripcion]" value="{{ $pago->descripcion }}" class="form-control" required>
                </td>
                <td>
                    <input type="date" name="pagos_financiamiento[{{ $pago->id }}][fecha_pago]" value="{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d') }}" class="form-control" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="pagos_financiamiento[{{ $pago->id }}][monto]" value="{{ $pago->monto }}" class="form-control" required>
                </td>
                <td>
                    <input type="file" name="pagos_financiamiento[{{ $pago->id }}][documento]" accept="application/pdf" class="form-control">
                    @if($pago->documentos && $pago->documentos->count() > 0)
                        <a href="{{ Storage::url($pago->documentos->first()->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-info mt-1">Ver PDF</a>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger eliminar-fila">Eliminar</button>
                    <input type="hidden" name="pagos_financiamiento[{{ $pago->id }}][eliminar]" value="0" class="eliminar-hidden">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" id="agregarPago" class="btn btn-success btn-sm mb-3">Agregar Pago</button>

    <div id="error-total-pagos" class="alert alert-danger d-none">
        La suma de los pagos planeados debe ser igual al total de la venta.
    </div>
</div>


        <input type="hidden" name="productos_json" id="productos_json">

        <div class="text-center mb-5">
            <button type="submit" class="btn btn-primary px-5 py-2">Guardar Cambios</button>
        </div>
    </form>
</div>



<script>
document.getElementById('form-venta').addEventListener('submit', function(event) {
    const totalVenta = parseFloat(document.getElementById('total').value) || 0;
    let sumaPagos = 0;

    const filas = document.querySelectorAll('input[name^="pagos_financiamiento"]');
    filas.forEach(input => {
        if (input.name.includes('[monto]')) {
            const monto = parseFloat(input.value) || 0;
            const tr = input.closest('tr');
            const eliminar = tr.querySelector('input[type="checkbox"]');
            if (!eliminar || !eliminar.checked) {
                sumaPagos += monto;
            }
        }
    });

    const errorDiv = document.getElementById('error-total-pagos');

    if (Math.abs(sumaPagos - totalVenta) > 0.01) {
        event.preventDefault();
        errorDiv.classList.remove('d-none');
        errorDiv.scrollIntoView({ behavior: 'smooth' });
    } else {
        errorDiv.classList.add('d-none');
    }
});
</script>
<script>
$(document).ready(function () {
    actualizarTotal();

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
                <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
                <input type="hidden" class="precio_unitario" name="productos[${id}][precio_unitario]" value="${precio}">
            </tr>`;
        $('#tabla-productos tbody').append(fila);
        actualizarTotal();
        $(this).val('');
    });

    $('#descuento, #envio').on('input', actualizarTotal);
    $('#toggleIva').on('change', function () {
        if (navigator.vibrate) navigator.vibrate(80);
        $('#iva').prop('readonly', true);
        actualizarTotal();
    });

    $(document).on('input', 'input[name^="pagos_financiamiento"][name$="[monto]"]', function () {
        ajustarPagos($(this));
    });

    // Eliminar fila al hacer clic en el botón "Eliminar"
    $(document).on('click', '.eliminar-fila', function () {
        $(this).closest('tr').remove();
        actualizarPagos();
    });

    let contadorNuevoPago = 0;
    $('#agregarPago').on('click', function () {
        const nuevoId = 'nuevo_' + contadorNuevoPago++;

        // Filtrar pagos que no están marcados para eliminar
        const filasPagos = $('#tabla-pagos tbody tr');

        // Ignorar el primero (ej. "Pago inicial")
        const filasSinInicial = filasPagos.slice(1);

        const descripcion = `${numeroEnLetras(filasSinInicial.length + 1)} pago`;

        // Obtener última fecha del resto (sin contar el primero)
        let ultimaFecha = null;
        filasSinInicial.each(function () {
            const inputFecha = $(this).find('input[name$="[fecha_pago]"]');
            const fecha = inputFecha.val();
            if (fecha && (!ultimaFecha || new Date(fecha) > new Date(ultimaFecha))) {
                ultimaFecha = fecha;
            }
        });

        let nuevaFecha = '';
        if (ultimaFecha) {
            const fecha = new Date(ultimaFecha);
            fecha.setMonth(fecha.getMonth() + 1);
            nuevaFecha = fecha.toISOString().split('T')[0];
        }

        const fila = `
            <tr>
                <td>
                    <input type="hidden" name="pagos_financiamiento[${nuevoId}][descripcion]" value="${descripcion}">
                    ${descripcion}
                </td>
                <td><input type="date" name="pagos_financiamiento[${nuevoId}][fecha_pago]" class="form-control" value="${nuevaFecha}" required></td>
                <td><input type="number" step="0.01" name="pagos_financiamiento[${nuevoId}][monto]" class="form-control" required></td>
                <td>
                    <input type="file" name="pagos_financiamiento[${nuevoId}][documento]" accept="application/pdf" class="form-control">
                </td>
                <td><button type="button" class="btn btn-sm btn-danger eliminar-fila">Eliminar</button></td>
            </tr>`;

        $('#tabla-pagos tbody').append(fila);
        actualizarPagos();
    });

    $('#form-venta').submit(function (e) {
        if (!validarTotalPagos()) {
            e.preventDefault();
        }
        prepararProductosJSON();
    });
});

function eliminarFila(btn) {
    $(btn).closest('tr').remove();
    actualizarTotal();
}

function actualizarSubtotal(input) {
    const tr = $(input).closest('tr');
    const cantidad = parseFloat(tr.find('.cantidad').val()) || 0;
    const precio = parseFloat(tr.find('.precio_unitario').val()) || 0;
    const sobreprecio = parseFloat(tr.find('.sobreprecio').val()) || 0;
    const subtotal = (cantidad * precio) + sobreprecio;
    tr.find('.subtotal').text(subtotal.toFixed(2));
    actualizarTotal();
}

function actualizarTotal() {
    let subtotal = 0;
    $('#tabla-productos tbody tr').each(function () {
        const cantidad = parseFloat($(this).find('.cantidad').val()) || 0;
        const precio = parseFloat($(this).find('.precio_unitario').val()) || 0;
        const sobreprecio = parseFloat($(this).find('.sobreprecio').val()) || 0;
        const filaSubtotal = (cantidad * precio) + sobreprecio;
        subtotal += filaSubtotal;
        $(this).find('.subtotal').text(filaSubtotal.toFixed(2));
    });

    $('#subtotal').val(subtotal.toFixed(2));
    const descuento = parseFloat($('#descuento').val()) || 0;
    const envio = parseFloat($('#envio').val()) || 0;
    const base = subtotal - descuento + envio;
    let iva = $('#toggleIva').is(':checked') ? base * 0.16 : 0;
    $('#iva').val(iva.toFixed(2));
    const total = base + iva;
    $('#total').val(total.toFixed(2));
    actualizarPagos();
}

function actualizarPagos() {
    const total = parseFloat($('#total').val()) || 0;
    const filas = $('input[name^="pagos_financiamiento"][name$="[monto]"]').closest('tr');

    const num = filas.length;
    if (num === 0) return;

    const monto = (total / num).toFixed(2);
    filas.each(function () {
        $(this).find('input[name$="[monto]"]').val(monto);
    });

    validarTotalPagos();
}

function ajustarPagos(inputModificado) {
    const total = parseFloat($('#total').val()) || 0;
    const filas = $('input[name^="pagos_financiamiento"][name$="[monto]"]').closest('tr');

    const pagos = [];
    let totalManual = 0;

    filas.each(function (i, tr) {
        const inputMonto = $(tr).find('input[name$="[monto]"]');
        const monto = parseFloat(inputMonto.val()) || 0;
        pagos.push({ input: inputMonto, monto, modificado: false });
    });

    const modIndex = filas.index(inputModificado.closest('tr'));
    if (modIndex !== -1) {
        pagos[modIndex].modificado = true;
        totalManual += pagos[modIndex].monto;
    }

    const restantes = pagos.filter((_, i) => i !== modIndex);
    const montoRestante = (total - totalManual) / restantes.length;

    restantes.forEach(pago => {
        pago.input.val(montoRestante.toFixed(2));
    });

    validarTotalPagos();
}

function validarTotalPagos() {
    const total = parseFloat($('#total').val()) || 0;
    let suma = 0;

    $('input[name^="pagos_financiamiento"][name$="[monto]"]').each(function () {
        suma += parseFloat($(this).val()) || 0;
    });

    const errorDiv = $('#error-total-pagos');
    if (Math.abs(suma - total) > 0.01) {
        errorDiv.removeClass('d-none');
        return false;
    } else {
        errorDiv.addClass('d-none');
        return true;
    }
}

function prepararProductosJSON() {
    const productos = [];
    $('#tabla-productos tbody tr').each(function () {
        productos.push({
            producto_id: $(this).data('id'),
            cantidad: $(this).find('.cantidad').val(),
            precio_unitario: $(this).find('.precio_unitario').val(),
            sobreprecio: $(this).find('.sobreprecio').val(),
            subtotal: $(this).find('.subtotal').text()
        });
    });
    $('#productos_json').val(JSON.stringify(productos));
}

// Ordinal en español
function numeroEnLetras(num) {
    const lista = ['Primer', 'Segundo', 'Tercer', 'Cuarto', 'Quinto', 'Sexto', 'Séptimo', 'Octavo', 'Noveno', 'Décimo',
                   'Undécimo', 'Duodécimo', 'Décimotercer', 'Décimocuarto', 'Décimoquinto', 'Décimosexto'];
    return lista[num - 1] || `${num}°`;
}
</script>



@endsection
