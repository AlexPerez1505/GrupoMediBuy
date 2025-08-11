@extends('layouts.app')
@section('title', 'Remisión')
@section('titulo', 'Remisión No' . $venta->id)

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pagos.css') }}?v={{ time() }}">

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="container my-5">
 

    <div class="row g-4" style="margin-top:90px;">
        <!-- Información de la venta -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</h5>
                @php
    $pagosAprobados = $pagos->filter(function($p) {
        return $p->aprobado;
    });

    $totalPagado = $pagosAprobados->sum('monto');
    $saldoRestante = $venta->total - $totalPagado;
    $porcentajePagado = min(100, round(($totalPagado / $venta->total) * 100, 2));
    $tooltipText = "Has pagado $" . number_format($totalPagado, 2) . " de $" . number_format($venta->total, 2);
@endphp

<p><strong>Plan:</strong> {{ $venta->plan }}</p>
<p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
<p><strong>Total pagado:</strong> ${{ number_format($totalPagado, 2) }}</p>
<p><strong>Saldo restante:</strong> ${{ number_format($saldoRestante, 2) }}</p>

<div class="mt-4">
    <label class="form-label fw-semibold">Progreso del pago: {{ $porcentajePagado }}%</label>
    <div class="progress position-relative" style="height: 20px; border-radius: 12px;">
        <div class="progress-bar 
                    @if($porcentajePagado < 50) bg-danger 
                    @elseif($porcentajePagado < 100) bg-warning 
                    @else bg-success @endif"
             role="progressbar"
             style="width: {{ $porcentajePagado }}%; transition: width 0.6s ease;"
             data-bs-toggle="tooltip"
             data-bs-placement="top"
             title="{{ $tooltipText }}"
             aria-valuenow="{{ $porcentajePagado }}"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
    </div>
</div>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="mb-3">Próximos pagos <button class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalEditarPagos">
    Editar pagos de financiamiento
</button>
</h5>
                @php
                    $pagosPendientes = $venta->pagosFinanciamiento->where('pagado', false)->sortBy('fecha_pago');
                @endphp

                @if($pagosPendientes->isEmpty())
                    <div class="alert alert-secondary">No hay pagos pendientes.</div>
                @else
                    <ul class="list-group small">
                        @foreach($pagosPendientes as $pago)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</strong><br>
                                    <small>{{ $pago->descripcion }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold d-block">${{ number_format($pago->monto, 2) }}</span>
                                    <button class="btn btn-sm btn-outline-primary mt-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#pagoModal" 
                                            data-venta-id="{{ $pago->venta_id }}" 
                                            data-fecha="{{ $pago->fecha_pago }}" 
                                            data-monto="{{ $pago->monto }}"
                                            data-descripcion="{{ $pago->descripcion }}">
                                        Registrar
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Pagos realizados -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Pagos realizados</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Recibo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pagos as $pago)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                        <td>${{ number_format($pago->monto, 2) }}</td>
                                        <td>{{ $pago->metodo_pago }}</td>
                                        <td>
                                            @if($pago->aprobado)
                                                <a href="{{ route('pagos.recibo', $pago->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">Ver</a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pago->aprobado)
                                                <span class="badge bg-success">Pagado</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$pago->aprobado)
                                                @if($pago->financiamiento_id)
                                                    <button 
                                                        class="btn btn-sm btn-success aprobar-pago-btn" 
                                                        data-id="{{ $pago->financiamiento_id }}" 
                                                        data-form-id="form-{{ $pago->id }}">
                                                        Aprobar
                                                    </button>

                                                    <form id="form-{{ $pago->id }}" 
                                                          action="{{ route('pagos.marcarPagado', $pago->financiamiento_id) }}" 
                                                          method="POST" 
                                                          class="d-none">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="pin" class="pin-input">
                                                    </form>
                                                @else
                                                    <span class="text-muted small">Sin financiamiento</span>
                                                @endif
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled>Pagado</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Editar Pagos -->
<div class="modal fade" id="modalEditarPagos" tabindex="-1" aria-labelledby="modalEditarPagosLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form 
      action="{{ route('ventas.pagosFinanciamiento.update', $venta->id) }}" 
      method="POST" 
      onsubmit="return validarTotalPagos(event)">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarPagosLabel">Editar Pagos de Financiamiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3 text-end">
            <span class="fw-bold">Total venta:</span> ${{ number_format($venta->total, 2) }}
          </div>
          <div class="alert alert-danger d-none" id="errorTotalPagos"></div>
          <div class="table-responsive">
            <table class="table align-middle table-bordered" style="min-width:450px;">
              <thead class="table-light">
                <tr>
                  <th>Descripción</th>
                  <th>Fecha</th>
                  <th>Monto</th>
                  <th>Eliminar</th>
                </tr>
              </thead>
             <tbody id="pagosTableBody">
@foreach($venta->pagosFinanciamiento as $pago)
<tr>
  <td data-label="Descripción">
    <input type="text" name="pagos_financiamiento[{{ $pago->id }}][descripcion]" class="form-control" value="{{ $pago->descripcion }}" required>
  </td>
  <td data-label="Fecha">
    <input type="date" name="pagos_financiamiento[{{ $pago->id }}][fecha_pago]" class="form-control" value="{{ $pago->fecha_pago }}" required>
  </td>
  <td data-label="Monto">
    <input type="number" name="pagos_financiamiento[{{ $pago->id }}][monto]" class="form-control monto-pago" value="{{ $pago->monto }}" min="0" step="0.01" required onchange="actualizarTotalPagos()">
  </td>
  <td data-label="Eliminar" class="text-center">
    <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar pago"
      onclick="eliminarPagoExistente(this, '{{ $pago->id }}')">
      <i class="bi bi-trash"></i>
    </button>
    <input type="hidden" name="pagos_financiamiento[{{ $pago->id }}][eliminar]" value="0" class="campo-eliminar">
  </td>
</tr>
@endforeach
</tbody>

            </table>
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="agregarFilaPago()">Agregar pago</button>
          <div class="mt-3 text-end">
            <span class="fw-bold">Total de pagos:</span> $<span id="totalPagosSpan">0.00</span>
          </div>
        </div>
        <div class="modal-footer flex-wrap">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnGuardarPagos" disabled>Guardar cambios</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Incluye moment.js y bootstrap-icons si aún no los tienes -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/es.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<script>
let contadorNuevoPago = 1;
const totalVenta = {{ $venta->total }};

// Elimina fila de un pago NUEVO (borrado físico)
function eliminarPagoNuevo(boton) {
  const fila = boton.closest('tr');
  fila.remove();
  renombrarPagos();
  actualizarTotalPagos();
}

// Elimina visualmente pago existente y marca el campo hidden
function eliminarPagoExistente(boton, idPago) {
  const fila = boton.closest('tr');
  // Marca el campo hidden "eliminar" en 1
  fila.querySelector('.campo-eliminar').value = '1';
  // Oculta la fila visualmente
  fila.style.display = 'none';
  renombrarPagos();
  actualizarTotalPagos();
}

// Regresa la descripción que toca según el número de pago visible (0-index)
function obtenerDescripcionPago(n) {
  const nombres = [
    'Pago inicial', 'Primer pago', 'Segundo pago', 'Tercer pago', 'Cuarto pago',
    'Quinto pago', 'Sexto pago', 'Séptimo pago', 'Octavo pago', 'Noveno pago', 'Décimo pago'
  ];
  return nombres[n] || `${n}° Pago`;
}

// Busca la fecha del último pago visible
function obtenerFechaUltimoPago() {
  let fechaUltima = null;
  document.querySelectorAll('#pagosTableBody tr').forEach(fila => {
    if (fila.style.display !== 'none') {
      const fechaInput = fila.querySelector('input[type="date"]');
      if (fechaInput && fechaInput.value) {
        fechaUltima = fechaInput.value;
      }
    }
  });
  return fechaUltima;
}

// Agrega nueva fila de pago con descripción y fecha automática
function agregarFilaPago() {
  const tbody = document.getElementById('pagosTableBody');
  const numPagosVisibles = Array.from(tbody.querySelectorAll('tr')).filter(fila => fila.style.display !== 'none').length;
  const descripcion = obtenerDescripcionPago(numPagosVisibles);
  const idx = 'nuevo_' + contadorNuevoPago++;

  let fechaBase = obtenerFechaUltimoPago();
  let fechaNueva = '';
  if (fechaBase) {
    fechaNueva = moment(fechaBase).add(1, 'months').format('YYYY-MM-DD');
  } else {
    fechaNueva = moment().format('YYYY-MM-DD');
  }

  const row = document.createElement('tr');
  row.innerHTML = `
    <td>
      <input type="text" name="pagos_financiamiento[${idx}][descripcion]" class="form-control" value="${descripcion}" required>
    </td>
    <td>
      <input type="date" name="pagos_financiamiento[${idx}][fecha_pago]" class="form-control" value="${fechaNueva}" required>
    </td>
    <td>
      <input type="number" name="pagos_financiamiento[${idx}][monto]" class="form-control monto-pago" min="0" step="0.01" placeholder="Monto" required onchange="actualizarTotalPagos()">
    </td>
    <td class="text-center">
      <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar pago"
        onclick="eliminarPagoNuevo(this)">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  `;
  tbody.appendChild(row);
  renombrarPagos();
  actualizarTotalPagos();
}

// Renombra automáticamente todos los pagos al agregar/eliminar
function renombrarPagos() {
  const filas = Array.from(document.querySelectorAll('#pagosTableBody tr')).filter(fila => fila.style.display !== 'none');
  filas.forEach((fila, i) => {
    const inputDesc = fila.querySelector('input[type="text"]');
    if (inputDesc) {
      inputDesc.value = obtenerDescripcionPago(i);
    }
  });
}

// Valida la suma de los pagos y muestra errores
function actualizarTotalPagos() {
  const filas = document.querySelectorAll('#pagosTableBody tr');
  let suma = 0;
  filas.forEach((fila) => {
    if (fila.style.display === 'none') return;
    const montoInput = fila.querySelector('.monto-pago');
    if (montoInput) {
      const monto = parseFloat(montoInput.value) || 0;
      suma += monto;
    }
  });
  document.getElementById('totalPagosSpan').textContent = suma.toFixed(2);

  const errorDiv = document.getElementById('errorTotalPagos');
  const btnGuardar = document.getElementById('btnGuardarPagos');
  if (suma < totalVenta) {
    errorDiv.textContent = 'El total de los pagos es menor al total de la venta.';
    errorDiv.classList.remove('d-none');
    btnGuardar.disabled = true;
  } else if (suma > totalVenta) {
    errorDiv.textContent = 'El total de los pagos es mayor al total de la venta.';
    errorDiv.classList.remove('d-none');
    btnGuardar.disabled = true;
  } else {
    errorDiv.textContent = '';
    errorDiv.classList.add('d-none');
    btnGuardar.disabled = false;
  }
}

// Validación final al enviar el formulario
function validarTotalPagos(event) {
  actualizarTotalPagos();
  const totalPagos = parseFloat(document.getElementById('totalPagosSpan').textContent);
  if (totalPagos !== totalVenta) {
    event.preventDefault();
    return false;
  }
  return true;
}

// Recalcula automáticamente al cargar el modal
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(() => {
    renombrarPagos();
    actualizarTotalPagos();
  }, 300);
});

// Recalcula cada vez que abras el modal
document.getElementById('modalEditarPagos').addEventListener('show.bs.modal', function () {
  setTimeout(() => {
    renombrarPagos();
    actualizarTotalPagos();
  }, 100);
});
</script>
<style>
/* Responsive modal y tabla de pagos */
@media (max-width: 991.98px) {
  #modalEditarPagos .modal-dialog {
    max-width: 98vw !important;
    width: 98vw !important;
    margin: 1.2rem auto;
  }
  #modalEditarPagos .modal-content {
    border-radius: 1.2rem;
    padding: 0 0.5rem;
  }
  #modalEditarPagos .table-responsive {
    overflow-x: visible !important;
  }
  #modalEditarPagos .table {
    min-width: unset !important;
    width: 100% !important;
  }
  #modalEditarPagos .table thead {
    display: none;
  }
  #modalEditarPagos .table tbody tr {
    display: flex;
    flex-wrap: wrap;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 1rem;
    background: #f7fafc;
    border-radius: 1rem;
    box-shadow: 0 3px 10px 0 rgba(80,120,170,0.07);
    padding: 0.7rem 0.3rem;
  }
  #modalEditarPagos .table td {
    flex: 1 1 100%;
    border: none;
    padding: 0.4rem 0;
    display: flex;
    align-items: center;
    word-break: break-word;
    /* Evita desbordamientos */
  }
  #modalEditarPagos .table td:not(:last-child) {
    margin-bottom: 0.35rem;
  }
  #modalEditarPagos .table td:before {
    content: attr(data-label);
    flex: 0 0 110px;
    font-weight: 600;
    color: #7a869a;
    margin-right: 0.7rem;
    font-size: 0.97em;
    min-width: 85px;
  }
}

@media (max-width: 575.98px) {
  #modalEditarPagos .modal-dialog {
    max-width: 98vw !important;
    width: 98vw !important;
    margin: 0.7rem auto;
  }
  #modalEditarPagos .modal-content {
    padding: 0.3rem !important;
    border-radius: 0.7rem;
  }
  #modalEditarPagos .table-responsive {
    overflow-x: visible !important;
  }
  #modalEditarPagos .table {
    min-width: unset !important;
    width: 100% !important;
  }
}

/* Siempre asegura que el body no tenga scroll lateral cuando el modal está abierto */
body.modal-open {
  overflow-x: hidden;
}
</style>



<!-- Inicializa tooltips -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@include('partials.pago-modal')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.aprobar-pago-btn').forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            const formId = this.dataset.formId;
            const form = document.getElementById(formId);

            const { value: pin, isConfirmed } = await Swal.fire({
                title: 'Aprobar pago',
                input: 'password',
                inputLabel: 'Ingresa el PIN de aprobación',
                inputPlaceholder: 'PIN...',
                inputAttributes: {
                    maxlength: 6,
                    autocapitalize: 'off',
                    autocorrect: 'off',
                    inputmode: 'numeric'
                },
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                customClass: {
                    popup: 'swal2-popup-minimal',
                    confirmButton: 'btn-pastel',
                    cancelButton: 'btn-pastel-outline',
                    input: 'swal2-input-minimal'
                },
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage('El PIN es obligatorio');
                    }
                }
            });

            if (isConfirmed && pin) {
                form.querySelector('.pin-input').value = pin;
                form.submit();
            }
        });
    });

    // Mostrar mensajes del backend en SweetAlert
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#a8dadc',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'swal2-popup-minimal',
            confirmButton: 'btn-pastel',
        }
    });
    @endif

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '{{ session('success') }}',
        confirmButtonColor: '#a8dadc',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'swal2-popup-minimal',
            confirmButton: 'btn-pastel',
        }
    });
    @endif
});
</script>
@endsection
