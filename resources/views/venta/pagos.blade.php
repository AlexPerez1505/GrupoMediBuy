@extends('layouts.app')

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
    <h2 class="mb-4 text-center">Pagos de la Venta #{{ $venta->id }}</h2>

    <div class="row g-4">
        <!-- Información de la venta -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</h5>
                    <p><strong>Plan:</strong> {{ $venta->plan }}</p>
                    <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
                    <p><strong>Total pagado:</strong> ${{ number_format($venta->pagos->sum('monto'), 2) }}</p>
                    <p><strong>Saldo restante:</strong> ${{ number_format($venta->total - $venta->pagos->sum('monto'), 2) }}</p>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="mb-3">Próximos pagos</h5>
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
