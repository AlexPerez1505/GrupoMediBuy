@extends('layouts.app')

@section('title', 'Viáticos')
@section('titulo', 'Viáticos')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<div class="container py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h2 class="text-slate fw-bold m-0"><i class="bi bi-wallet2 text-primary me-2"></i>Control de Viáticos</h2>
            <p class="text-muted m-0 small">Administra los gastos de transporte, gasolina y cuentas por locación.</p>
        </div>
        <!-- Bloque de Botones de Navegación y Acciones -->
        <div class="d-flex flex-wrap gap-2">
            <!-- Botón Volver Atrás (Historial del navegador) -->
            <a href="javascript:history.back()" class="btn btn-gris shadow-sm px-3 py-2 d-flex align-items-center gap-2" title="Regresar a la pantalla anterior">
                <i class="bi bi-arrow-left-short fs-5"></i> 
            </a>
            
            <!-- Botón Inicio (Home) -->
            <a href="{{ url('/home') }}" class="btn btn-gris shadow-sm px-3 py-2 d-flex align-items-center gap-2" title="Ir al Inicio">
                <i class="bi bi-house-door-fill"></i> 
            </a>

            <a href="{{ route('cuentas.create') }}" class="btn btn-azul shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> Nueva Cuenta
            </a>
            
            <a href="{{ route('cuentas.exportar.pdf') }}" class="btn btn-verde shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf-fill"></i> Exportar PDF
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-x-circle-fill fs-5"></i>
        <div>{{ session('error') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
@endif

    @if($cuentas->count() > 0)
    <div class="row g-4">
        
        <div class="col-12">
            <div class="bg-white rounded-4 shadow-sm p-4 border border-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-slate fw-bold m-0">Historial de Registros</h5>
                        <span class="badge bg-primary-soft text-primary rounded-pill">{{ $cuentas->count() }} cuentas</span>
                    </div> 
                </div>
                
                <div class="table-responsive table-scroll-container">
                    <table class="table align-middle table-hover mb-0 get-clean-table">
                        <thead>
                            <tr>
                                <th>Lugar</th>
                                <th>Camioneta</th>
                                <th class="text-end">Casetas</th>
                                <th class="text-end">Gasolina</th>
                                <th class="text-end">Viáticos</th>
                                <th class="text-end">Adicional</th>
                                <th style="min-width: 180px;">Descripción</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuentas as $cuenta)
                            <tr>
                                <td class="fw-semibold text-slate">{{ $cuenta->lugar }}</td>
                                <td><span class="badge bg-light text-secondary border px-2 py-1.5 rounded-3"><i class="bi bi-truck me-1"></i>{{ $cuenta->camioneta }}</span></td>
                                <td class="text-end">${{ number_format($cuenta->casetas, 2) }}</td>
                                <td class="text-end">${{ number_format($cuenta->gasolina, 2) }}</td>
                                <td class="text-end">${{ number_format($cuenta->viaticos, 2) }}</td>
                                <td class="text-end">${{ number_format($cuenta->adicional, 2) }}</td>
                                <td class="text-muted text-start small text-truncate-custom" style="max-width: 220px;" title="{{ $cuenta->descripcion }}">{{ $cuenta->descripcion }}</td>
                                <td class="text-end fw-bold text-primary">${{ number_format($cuenta->total, 2) }}</td>
                                <td>
    <div class="d-flex justify-content-center gap-1">
        <button 
            class="btn btn-warning-custom" 
            title="Editar cuenta"
            onclick="pedirNip('editar', {{ $cuenta->id }})">
            <i class="bi bi-pencil-square"></i>
        </button>
        <button 
            class="btn btn-rojo-custom" 
            title="Eliminar cuenta"
            onclick="pedirNip('eliminar', {{ $cuenta->id }})">
            <i class="bi bi-trash-fill"></i>
        </button>
    </div>
        {{-- Form oculto para EDITAR --}}
<form id="form-edit-{{ $cuenta->id }}" 
      action="{{ route('cuentas.edit', $cuenta->id) }}" 
      method="GET" class="d-none">
</form>

    {{-- Form oculto para ELIMINAR --}}
    <form id="form-delete-{{ $cuenta->id }}" 
          action="{{ route('cuentas.destroy', $cuenta->id) }}" 
          method="POST" class="d-none">
        @csrf
        @method('DELETE')
        <input type="hidden" name="action_pin" id="pin-delete-{{ $cuenta->id }}">
    </form>

    
</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>

        <div class="col-md-7 col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-4 border border-light h-100">
                <h5 class="text-slate fw-bold mb-3"><i class="bi bi-bar-chart-line text-primary me-2"></i>Totales acumulados por Lugar</h5>
                <div style="position: relative; height: 320px;">
                    <canvas id="graficaTotales"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-4 border border-light h-100">
                <h5 class="text-slate fw-bold mb-3 text-center"><i class="bi bi-pie-chart text-success me-2"></i>Por Camioneta</h5>
                <div style="position: relative; height: 320px;">
                    <canvas id="graficaCamionetas"></canvas>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center p-5 bg-white rounded-4 shadow-sm border border-light mt-4">
        <div class="text-muted mb-3"><i class="bi bi-folder-x fs-1 opacity-50"></i></div>
        <h5 class="text-secondary fw-bold">No hay cuentas registradas</h5>
        <p class="text-muted small">Comienza agregando un nuevo registro de viáticos usando el botón del panel superior.</p>
        <a href="{{ route('cuentas.create') }}" class="btn btn-azul px-4 py-2 mt-2"><i class="bi bi-plus-circle me-1"></i>Registrar primera cuenta</a>
    </div>
    @endif

</div>

<style>
    body { 
        background-color: #f8fafc; 
        color: #475569;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    .text-slate { color: #1e293b; }
    
    /* Limitador del scroll para mantener el control visual */
    .table-scroll-container {
        max-height: 380px; 
        overflow-y: auto;
        border-radius: 8px;
    }

    /* Hace que el encabezado se quede fijo arriba mientras scrolleas */
    .get-clean-table thead th {
        position: sticky;
        top: 0;
        background-color: #f8fafc; 
        z-index: 10;
        color: #64748b;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 2px solid #edf2f7;
    }

    /* Estilización sutil de la barra de scroll para navegadores basados en Webkit */
    .table-scroll-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .table-scroll-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Botones Pastel Premium */
    .btn-gris { 
        background-color: #f1f5f9; 
        color: #475569; 
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-gris:hover { background-color: #e2e8f0; color: #334155; transform: translateY(-1px); }

    .btn-azul { 
        background-color: #e0f2fe; 
        color: #0369a1; 
        border: 1px solid #bae6fd;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-azul:hover { background-color: #bae6fd; color: #0369a1; transform: translateY(-1px); }
    
    .btn-verde { 
        background-color: #dcfce7; 
        color: #15803d; 
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-verde:hover { background-color: #bbf7d0; color: #15803d; transform: translateY(-1px); }

    .btn-warning-custom {
        background-color: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
        height: 34px; width: 34px;
        border-radius: 10px;
        display: inline-grid;
        place-items: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-warning-custom:hover { background-color: #fef08a; color: #854d0e; transform: scale(1.05); }

    .btn-rojo-custom {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecdd3;
        height: 34px; width: 34px;
        border-radius: 10px;
        display: inline-grid;
        place-items: center;
        transition: all 0.2s;
    }
    .btn-rojo-custom:hover { background-color: #fecdd3; color: #991b1b; transform: scale(1.05); }

    .bg-primary-soft { background-color: #e0e7ff; }

    .get-clean-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .text-truncate-custom {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #modalNip .modal-content{
    border:none;
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 20px 40px rgba(0,0,0,.10);
}

#modalNip .modal-body{
    padding:35px;
}

.pin-input{
    width:60px !important;
    height:60px !important;
    border-radius:14px !important;
    border:2px solid #e2e8f0 !important;
    font-size:1.5rem !important;
}

.pin-input:focus{
    border-color:#2563eb !important;
    box-shadow:0 0 0 4px rgba(37,99,235,.15) !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function colorPastelAleatorio() {
        const r = Math.floor(160 + Math.random() * 95);
        const g = Math.floor(160 + Math.random() * 95);
        const b = Math.floor(160 + Math.random() * 95);
        return `rgba(${r}, ${g}, ${b}, 0.85)`;
    }

    // ====== Gráfica por LUGAR ======
    const coloresLugar = [
        @foreach($cuentas as $cuenta)
            colorPastelAleatorio(),
        @endforeach
    ];

    const ctxLugar = document.getElementById('graficaTotales').getContext('2d');
    new Chart(ctxLugar, {
        type: 'bar',
        data: {
            labels: [
                @foreach($cuentas as $cuenta)
                    '{{ $cuenta->lugar }} (ID {{ $cuenta->id }})',
                @endforeach
            ],
            datasets: [{
                label: 'Gastos de Viaje ($)',
                data: [
                    @foreach($cuentas as $cuenta)
                        {{ $cuenta->total }},
                    @endforeach
                ],
                backgroundColor: coloresLugar,
                borderColor: 'rgba(0,0,0,0.05)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11 } }
                }
            },
            plugins: { legend: { display: false } }
        }
    });

    // ====== Gráfica por CAMIONETA ======
    const cuentas = @json($cuentas->map->only(['camioneta','total'])->values());
    const totalesCamioneta = {};
    cuentas.forEach(c => {
        const key = c.camioneta || 'Sin especificar';
        totalesCamioneta[key] = (totalesCamioneta[key] || 0) + parseFloat(c.total || 0);
    });
    const labelsCam = Object.keys(totalesCamioneta);
    const dataCam = Object.values(totalesCamioneta);
    const coloresCam = labelsCam.map(() => colorPastelAleatorio());

    const ctxCam = document.getElementById('graficaCamionetas').getContext('2d');
    new Chart(ctxCam, {
        type: 'doughnut',
        data: {
            labels: labelsCam,
            datasets: [{
                data: dataCam,
                backgroundColor: coloresCam,
                borderColor: '#ffffff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#475569', font: { size: 12 }, padding: 15 } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const v = ctx.parsed || 0;
                            return ` ${ctx.label}: $${v.toLocaleString(undefined,{minimumFractionDigits:2})}`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
{{-- MODAL NIP --}}
<div class="modal fade" id="modalNip" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body p-4 text-center">
    <div class="mb-3">
        <i class="bi bi-shield-lock text-primary fs-2"></i>
    </div>
    <h5 class="fw-bold text-slate mb-1">Confirmación segura</h5>
    <p class="text-muted small mb-4">Escribe el PIN de 6 dígitos</p>

    <div class="bg-light p-2 rounded-3 mb-4 text-start small border">
        <div class="d-flex align-items-center">
            <input type="radio" checked class="me-2">
            <span class="text-slate">Al completar los <b>6 dígitos</b>, se validará el acceso de forma segura.</span>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-2 mb-2" id="pin-container">
        <input type="text" maxlength="1" class="pin-input form-control text-center fw-bold fs-4" style="width: 45px; height: 50px;">
        <input type="text" maxlength="1" class="pin-input form-control text-center fw-bold fs-4" style="width: 45px; height: 50px;">
        <input type="text" maxlength="1" class="pin-input form-control text-center fw-bold fs-4" style="width: 45px; height: 50px;">
        <input type="text" maxlength="1" class="pin-input form-control text-center fw-bold fs-4" style="width: 45px; height: 50px;">
        <input type="text" maxlength="1" class="pin-input form-control text-center fw-bold fs-4" style="width: 45px; height: 50px;">
        <input type="text" maxlength="1" class="pin-input form-control text-center fw-bold fs-4" style="width: 45px; height: 50px;">
    </div>
    
    <p class="text-muted small mb-4">Puedes <b>pegar</b> el PIN completo.</p>

    <div id="nipError" class="alert alert-danger py-2 small rounded-3 d-none" role="alert">
        <i class="bi bi-x-circle me-1"></i> NIP incorrecto. Intenta de nuevo.
    </div>
    <div id="nipError"
     class="alert alert-danger py-2 small rounded-3 d-none mt-3"
     role="alert">
    <i class="bi bi-x-circle-fill me-1"></i>
    El NIP es incorrecto
</div>
</div>
        </div>
    </div>
</div>


<script>
const pinInputs = document.querySelectorAll('.pin-input');
let _nipAccion = null;
let _nipId = null;

function pedirNip(accion, id) {
    _nipAccion = accion;
    _nipId = id;

    pinInputs.forEach(i => i.value = '');

    document.getElementById('nipError')
        .classList.add('d-none');

    new bootstrap.Modal(
        document.getElementById('modalNip')
    ).show();

    setTimeout(() => pinInputs[0].focus(), 300);
}

pinInputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        if (e.target.value.length === 1 && index < pinInputs.length - 1) {
            pinInputs[index + 1].focus();
        }
        
        // Verificamos si todos los campos tienen un número
        if (Array.from(pinInputs).every(i => i.value !== '')) {
            enviarFormulario();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
            pinInputs[index - 1].focus();
        }
    });
});

function enviarFormulario() {

    const fullPin = Array.from(pinInputs)
        .map(i => i.value)
        .join('');

    const pinCorrecto = "{{ env('APROBACION_PIN') }}";

    if (fullPin !== pinCorrecto) {

        const error = document.getElementById('nipError');

        error.classList.remove('d-none');

        pinInputs.forEach(i => i.value = '');

        setTimeout(() => {
            pinInputs[0].focus();
        }, 100);

        return;
    }

    if (_nipAccion === 'eliminar') {

        document.getElementById(
            'pin-delete-' + _nipId
        ).value = fullPin;

        document.getElementById(
            'form-delete-' + _nipId
        ).submit();

    } else {

    document.getElementById(
        'form-edit-' + _nipId
    ).submit();
}
}
</script>
@endsection