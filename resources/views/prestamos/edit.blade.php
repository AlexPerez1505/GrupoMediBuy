@extends('layouts.app')
@section('title', 'Editar Préstamo')
@section('titulo', 'Editar Préstamo')
@section('content')

<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="form-contenedor">
    <div class="card-body">
        <form action="{{ route('prestamos.update', $prestamo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Registro -->
            {{-- REGISTRO --}}
<div class="form-group mb-3">
    <label for="search-registro" class="label_nomina">Buscar Serie</label>
    <div class="input_consulta position-relative">
        <div class="icon-container2">
            <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
        </div>
        <input type="text" id="search-registro" class="form-control" placeholder="Ingrese número de serie..." autocomplete="off" style="background-color: #ffff;">
        <input type="hidden" name="registro_id" id="registro_id" value="{{ $prestamo->registro_id }}">
        <ul class="dropdown-menu modern-dropdown w-100 position-absolute" id="registro-list" style="top: 100%; left: 0; z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></ul>
    </div>
</div>

{{-- INFO REGISTRO --}}
<div class="row mb-3" id="infoRegistro">
    <div class="col-md-4">
        <label for="subtipo_equipo" class="form-label">Subtipo</label>
        <input type="text" id="subtipo_equipo" class="form-control" readonly>
    </div>
    <div class="col-md-4">
        <label for="marca" class="form-label">Marca</label>
        <input type="text" id="marca" class="form-control" readonly>
    </div>
    <div class="col-md-4">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" id="modelo" class="form-control" readonly>
    </div>
</div>

{{-- CLIENTE --}}
<div class="form-group mb-3">
    <label for="search-cliente" class="label_nomina">Buscar Cliente</label>
    <div class="input_consulta position-relative">
        <div class="icon-container2">
            <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
        </div>
        <input type="text" id="search-cliente" class="form-control" placeholder="Ingrese nombre o apellido..." autocomplete="off" style="background-color: #ffff;">
        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $prestamo->cliente_id }}">
        <ul class="dropdown-menu modern-dropdown w-100 position-absolute" id="cliente-list" style="top: 100%; left: 0; z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></ul>
    </div>
</div>


            <!-- Fechas -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
                    <input type="date" name="fecha_prestamo" id="fecha_prestamo" class="form-control" value="{{ $prestamo->fecha_prestamo }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_devolucion_estimada" class="form-label">Fecha Estimada de Devolución</label>
                    <input type="date" name="fecha_devolucion_estimada" id="fecha_devolucion_estimada" class="form-control" value="{{ $prestamo->fecha_devolucion_estimada }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="fecha_devolucion_real" class="form-label">Fecha Real de Devolución</label>
                <input type="date" name="fecha_devolucion_real" id="fecha_devolucion_real" class="form-control" value="{{ $prestamo->fecha_devolucion_real }}">
            </div>

            <!-- Estado -->
            <div class="mb-3">
                <div class="form-group">
                    <label for="estado" class="label_nomina">Estado</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select name="estado" id="estado" class="form-control" style="background-color: #ffff;">
                            <option value="activo" {{ $prestamo->estado == 'activo' ? 'selected' : '' }}>ACTIVO</option>
                            <option value="devuelto" {{ $prestamo->estado == 'devuelto' ? 'selected' : '' }}>DEVUELTO</option>
                            <option value="retrasado" {{ $prestamo->estado == 'retrasado' ? 'selected' : '' }}>RETRASADO</option>
                            <option value="cancelado" {{ $prestamo->estado == 'cancelado' ? 'selected' : '' }}>CANCELADO</option>
                            <option value="vendido" {{ $prestamo->estado == 'vendido' ? 'selected' : '' }}>VENDIDO</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Condiciones -->
            <div class="mb-3">
                <label for="condiciones_prestamo" class="form-label">Condiciones del Préstamo</label>
                <textarea name="condiciones_prestamo" id="condiciones_prestamo" rows="4" class="form-control">{{ $prestamo->condiciones_prestamo }}</textarea>
            </div>

            <!-- Observaciones -->
            <div class="mb-3">
                <label for="observaciones" class="label_nomina">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="3" class="form-control" style="height: 130px;">{{ $prestamo->observaciones }}</textarea>
            </div>

            <!-- Registrado por -->
            <div class="col-md-12 mt-3">
                @auth
                <div class="form-group" style="display:none">
                    <label for="user_name" class="label_nomina">Registrado por</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" value="{{ Auth::user()->name }}" readonly>
                </div>
                @endauth
            </div>

         <br>

            <button type="submit" class="btn btn-primary">
                Actualizar Préstamo
            </button>
        </form>
    </div>
</div>
@endsection
<script>
document.addEventListener("DOMContentLoaded", function () {
    const registros = @json($registros);
    const clientes = @json($clientes);

    const setupAutocomplete = (inputId, listId, hiddenId, dataset, getText, onSelect) => {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const hidden = document.getElementById(hiddenId);

        input.addEventListener("input", function () {
            const query = this.value.toLowerCase().trim();
            list.innerHTML = "";
            if (!query) {
                list.style.display = "none";
                return;
            }

            const resultados = dataset.filter(item => getText(item).toLowerCase().includes(query));
            if (resultados.length === 0) {
                list.innerHTML = `<li><button type="button" class="dropdown-item text-muted disabled">Sin resultados</button></li>`;
                list.style.display = "block";
                return;
            }

            resultados.slice(0, 6).forEach(item => {
                const li = document.createElement("li");
                li.innerHTML = `<button type="button" class="dropdown-item">${getText(item)}</button>`;
                li.querySelector("button").addEventListener("click", (e) => {
                    e.preventDefault(); // Evita comportamiento por defecto como enviar formularios
                    input.value = getText(item);
                    hidden.value = item.id;
                    list.style.display = "none";
                    if (onSelect) onSelect(item);
                });
                list.appendChild(li);
            });

            list.style.display = "block";
        });

        document.addEventListener("click", function (e) {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.style.display = "none";
            }
        });
    };

    setupAutocomplete(
        "search-registro",
        "registro-list",
        "registro_id",
        registros,
        (r) => r.numero_serie,
        (r) => {
            document.getElementById("subtipo_equipo").value = r.subtipo_equipo;
            document.getElementById("marca").value = r.marca;
            document.getElementById("modelo").value = r.modelo;
            document.getElementById("infoRegistro").classList.remove("d-none");
        }
    );

    setupAutocomplete(
        "search-cliente",
        "cliente-list",
        "cliente_id",
        clientes,
        (c) => `${c.nombre} ${c.apellido}`,
        null
    );

    // Precarga si ya hay valores
    const selectedRegistro = registros.find(r => r.id == document.getElementById("registro_id").value);
    if (selectedRegistro) {
        document.getElementById("search-registro").value = selectedRegistro.numero_serie;
        document.getElementById("subtipo_equipo").value = selectedRegistro.subtipo_equipo;
        document.getElementById("marca").value = selectedRegistro.marca;
        document.getElementById("modelo").value = selectedRegistro.modelo;
        document.getElementById("infoRegistro").classList.remove("d-none");
    }

    const selectedCliente = clientes.find(c => c.id == document.getElementById("cliente_id").value);
    if (selectedCliente) {
        document.getElementById("search-cliente").value = `${selectedCliente.nombre} ${selectedCliente.apellido}`;
    }
});
</script>


<script>
    $(document).ready(function() {
        $('#registro_id').select2({
            placeholder: 'Buscar registro...',
            allowClear: true
        });

        $('#cliente_id').select2({
            placeholder: 'Buscar cliente o seleccionar "Congresos"',
            allowClear: true
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const registroSelect = document.getElementById('registro_id');
        const subtipoInput = document.getElementById('subtipo_equipo');
        const marcaInput = document.getElementById('marca');
        const modeloInput = document.getElementById('modelo');
        const infoBox = document.getElementById('infoRegistro');

        registroSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];

            if (selected && selected.value) {
                subtipoInput.value = selected.getAttribute('data-subtipo') || '';
                marcaInput.value = selected.getAttribute('data-marca') || '';
                modeloInput.value = selected.getAttribute('data-modelo') || '';
                infoBox.classList.remove('d-none'); // Muestra correctamente
            } else {
                infoBox.classList.add('d-none'); // Oculta correctamente
                subtipoInput.value = '';
                marcaInput.value = '';
                modeloInput.value = '';
            }
        });
    });
</script>


<!-- jQuery y Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
