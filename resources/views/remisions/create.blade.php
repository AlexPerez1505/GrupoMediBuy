@extends('layouts.app')

@section('title', 'Historial de Mantenimiento')
@section('titulo', 'Historial de Mantenimiento')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ordenm.css') }}?v={{ time() }}">

<div class="container py-4" style="margin-top:80px;">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
        
        </div>
        <div class="card-body px-4 py-5">
            <form method="POST" action="{{ route('remisions.store') }}" id="form-remision">
                @csrf

                {{-- Selección de cliente --}}
                <div class="mb-4 position-relative">
    <label for="cliente_busqueda" class="form-label fw-semibold">Buscar Cliente</label>
    <input type="text" id="cliente_busqueda" class="form-control" placeholder="Escribe nombre o apellido..." autocomplete="off">
    <input type="hidden" name="cliente_id" id="cliente_id">
    <ul id="cliente_sugerencias" class="list-group position-absolute w-100 mt-1 shadow-sm" style="z-index: 1000; display: none;"></ul>
    <div class="text-end mb-3">
    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal_formulario">
        + Registrar nuevo cliente
    </button>
</div>
</div>
                {{-- Contenedor de ítems --}}
                <div id="items-container">
    <div class="item bg-light border rounded-3 p-4 mb-4 shadow-sm" data-index="0">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" name="items[0][cantidad]" class="form-control cantidad" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unidad</label>
                <input type="text" name="items[0][unidad]" class="form-control unidad" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="items[0][nombre_item]" class="form-control nombre_item" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Descripción</label>
                <textarea name="items[0][descripcion_item]" class="form-control descripcion_item" rows="1"></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Importe unitario ($)</label>
                <input type="number" step="0.01" name="items[0][importe_unitario]" class="form-control importe_unitario" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">A cuenta ($)</label>
                <input type="number" step="0.01" name="items[0][a_cuenta]" class="form-control a_cuenta">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger remove-item w-100">Eliminar</button>
            </div>
        </div>
    </div>
</div>
                <div class="mb-4 text-end">
                    <button type="button" id="add-item" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i> Añadir otro ítem
                    </button>
                </div>
                <div class="form-check form-switch mb-4">
    <input type="hidden" name="aplicar_iva" value="0">
    <input class="form-check-input" type="checkbox" id="aplicar_iva" name="aplicar_iva" value="1">
    <label class="form-check-label fw-semibold" for="aplicar_iva">Aplicar IVA (16%)</label>
</div>
                <div class="d-flex justify-content-between flex-wrap gap-3 total-info">
                <p class="fw-semibold mb-0">Subtotal: $<span id="subtotal">0.00</span></p>
    <p class="fw-semibold mb-0">IVA (16%): $<span id="iva">0.00</span></p>
    <p class="fw-semibold mb-0">Total: $<span id="total">0.00</span></p>
    <p class="fw-semibold mb-0">Restante: $<span id="restante">0.00</span></p>
                </div>
                <div class="form-actions text-end mt-4 d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-success px-5 py-2">
        <i class="bi bi-save me-1"></i> Guardar Remisión
    </button>
    <a href="/remisions" class="btn btn-secondary px-5 py-2">
        <i class="bi bi-arrow-left-circle me-1"></i> Volver
    </a>
</div>
            </form>
        </div>
    </div>
</div>
<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  @csrf
  {{-- Campo oculto para redirigir de vuelta a la vista actual --}}
  <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

  <div class="modal fade" id="modal_formulario" tabindex="-1" role="dialog" aria-labelledby="FormularioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-heade">
          <h5 class="modal-title" id="createClientModalLabel">Registrar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control text-uppercase" id="nombre" name="nombre" placeholder="Ingresar nombre" required>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control text-uppercase" id="apellido" name="apellido" placeholder="Ingresar apellido" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control text-uppercase" id="telefono" name="telefono" placeholder="Ingresar teléfono" maxlength="12" required>
              <span id="error-telefono" class="text-danger" style="display: none;">El teléfono ya está registrado o es inválido.</span>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar email" required>
              <span id="error-email" class="text-danger" style="display: none;">El correo ya está registrado o es inválido.</span>
            </div>
          </div>
          <div class="mb-3">
            <label for="comentarios" class="form-label">Dirección</label>
            <textarea id="comentarios" name="comentarios" class="form-control text-uppercase" placeholder="Agrega información de tu cliente"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
      </div>
    </div>
  </div>
</form>


<!-- Modal cliente creado -->
<div class="modal fade" id="cliente_creado" tabindex="-1" role="dialog" aria-labelledby="ClienteCreadoLabel" 
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Cliente guardado exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                    El cliente se ha registrado correctamente en el sistema.  
                    Ahora puedes seleccionarlo.
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-listo" onclick="cerrarModal()">Listo</button>
            </div>
        </div>
    </div>
</div>

{{-- Script para funcionalidad dinámica --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = 1;

    function calcularTotales() {
        let subtotal = 0, restante = 0;

        document.querySelectorAll('.item').forEach(item => {
            let cantidad = parseFloat(item.querySelector('.cantidad').value) || 0;
            let importe = parseFloat(item.querySelector('.importe_unitario').value) || 0;
            let aCuenta = parseFloat(item.querySelector('.a_cuenta').value) || 0;

            let itemSubtotal = cantidad * importe;
            subtotal += itemSubtotal;
            restante += itemSubtotal - aCuenta;
        });

        const aplicarIVA = document.getElementById('aplicar_iva')?.checked;
        const iva = aplicarIVA ? subtotal * 0.16 : 0;
        const total = subtotal + iva;
        const restanteTotal = restante + iva;

        // Asignar valores a los elementos
        if (document.getElementById('subtotal')) {
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        }
        if (document.getElementById('iva')) {
            document.getElementById('iva').textContent = iva.toFixed(2);
        }
        document.getElementById('total').textContent = total.toFixed(2);
        document.getElementById('restante').textContent = restanteTotal.toFixed(2);
    }

    document.getElementById('add-item').addEventListener('click', function () {
        const original = document.querySelector('.item');
        const clone = original.cloneNode(true);
        clone.setAttribute('data-index', index);

        // Limpiar valores
        clone.querySelectorAll('input, textarea').forEach(input => input.value = '');

        // Actualizar nombres
        clone.querySelectorAll('input, textarea').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', newName);
            }
        });

        document.getElementById('items-container').appendChild(clone);
        index++;
        calcularTotales();
    });

    document.getElementById('items-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            const items = document.querySelectorAll('.item');
            if (items.length > 1) {
                e.target.closest('.item').remove();
                calcularTotales();
            }
        }
    });

    document.getElementById('items-container').addEventListener('input', calcularTotales);

    const ivaCheckbox = document.getElementById('aplicar_iva');
    if (ivaCheckbox) {
        ivaCheckbox.addEventListener('change', calcularTotales);
    }

    calcularTotales(); // Calcular al cargar por si hay datos
});
</script>
<script>
    const clientes = @json($clientes); 

    const inputBusqueda = document.getElementById('cliente_busqueda');
    const inputHiddenId = document.getElementById('cliente_id');
    const sugerencias = document.getElementById('cliente_sugerencias');

    inputBusqueda.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        sugerencias.innerHTML = '';
        if (!query) {
            sugerencias.style.display = 'none';
            return;
        }

        const resultados = clientes.filter(cliente =>
            (`${cliente.nombre} ${cliente.apellido}`).toLowerCase().includes(query)
        );

        if (resultados.length) {
            resultados.slice(0, 5).forEach(cliente => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'list-group-item-action');
                li.textContent = `${cliente.nombre} ${cliente.apellido}`;
                li.style.cursor = 'pointer';
                li.addEventListener('click', () => {
                    inputBusqueda.value = `${cliente.nombre} ${cliente.apellido}`;
                    inputHiddenId.value = cliente.id;
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                });
                sugerencias.appendChild(li);
            });
            sugerencias.style.display = 'block';
        } else {
            sugerencias.style.display = 'none';
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.position-relative')) {
            sugerencias.style.display = 'none';
        }
    });
</script>
@if(session('cliente_creado'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('cliente_creado'));
            modal.show();
        });

        function cerrarModal() {
            const modalElement = document.getElementById('cliente_creado');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();
        }
    </script>
@endif

<script>
document.addEventListener("DOMContentLoaded", function () {
  const telefonoInput = document.getElementById("telefono");
  const emailInput = document.getElementById("email");

  // Formato tipo 72 9390 3384
  telefonoInput.addEventListener("input", function () {
    let value = this.value.replace(/\D/g, "").substring(0, 10); // Solo números y máximo 10 dígitos

    if (value.length > 6) {
      this.value = value.replace(/(\d{2})(\d{4})(\d{1,4})/, "$1 $2 $3");
    } else if (value.length > 2) {
      this.value = value.replace(/(\d{2})(\d{1,4})/, "$1 $2");
    } else {
      this.value = value;
    }
  });

  // Validación AJAX para teléfono y correo
  telefonoInput.addEventListener("blur", function () {
    validarCampo('telefono', this.value.replace(/\D/g, ""), 'error-telefono');
  });

  emailInput.addEventListener("blur", function () {
    validarCampo('email', this.value.trim(), 'error-email');
  });

  function validarCampo(tipo, valor, errorId) {
    if (!valor) return;

    fetch(`/validar-${tipo}?valor=${encodeURIComponent(valor)}`)
      .then(response => response.json())
      .then(data => {
        document.getElementById(errorId).style.display = data.existe ? 'block' : 'none';
      })
      .catch(error => {
        console.error('Error en validación:', error);
      });
  }
});
</script>

@endsection
