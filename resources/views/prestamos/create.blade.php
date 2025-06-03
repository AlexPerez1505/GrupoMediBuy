@extends('layouts.app')
@section('title', 'Préstamos')
@section('titulo', 'Préstamo')
@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Mejora de visibilidad para el input de Select2 */
.select2-container--default .select2-selection--single {
    background-color: #fff !important;
    border: 1px solid #ccc;
    height: 40px;
    display: flex;
    align-items: center;
    padding-left: 10px;
    font-size: 14px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #333;
    line-height: normal;
}

.select2-container--default .select2-selection--single .select2-selection__clear {
    cursor: pointer;
    font-weight: bold;
    color: #888;
}

</style>
<div class="form-contenedor">
 
       
        <div class="card-body">
            <form action="{{ route('prestamos.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
    <label for="search-registro" class="label_nomina">Buscar Serie</label>
    <div class="input_consulta position-relative">
        <div class="icon-container2">
            <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
        </div>
        <input type="text" id="search-registro" class="form-control" placeholder="Ingrese número de serie..." style="background-color: #ffff;" autocomplete="off">
        <input type="hidden" id="registro_id" name="registro_id" required>
        <ul class="dropdown-menu modern-dropdown w-100 position-absolute" id="registro-list" style="top: 100%; left: 0; z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></ul>
    </div>
</div>

<!-- Info extra -->
<div class="row mb-3 d-none" id="infoRegistro">
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
<div class="form-group mb-3">
    <label for="search-cliente" class="label_nomina">Buscar Cliente / Congreso</label>
    <div class="input_consulta position-relative">
        <div class="icon-container2">
            <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
        </div>
        <input type="text" id="search-cliente" class="form-control" placeholder="Ingrese nombre o apellido..." style="background-color: #ffff;" autocomplete="off">
        <input type="hidden" id="cliente_id" name="cliente_id" required>
        <ul class="dropdown-menu modern-dropdown w-100 position-absolute" id="cliente-list" style="top: 100%; left: 0; z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></ul>
    </div>
</div>

                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
                        <input type="date" name="fecha_prestamo" id="fecha_prestamo" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_devolucion_estimada" class="form-label">Fecha Estimada de Devolución</label>
                        <input type="date" name="fecha_devolucion_estimada" id="fecha_devolucion_estimada" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fecha_devolucion_real" class="form-label">Fecha Real de Devolución</label>
                    <input type="date" name="fecha_devolucion_real" id="fecha_devolucion_real" class="form-control">
                </div>

                <div class="mb-3">
                <div class="form-group">
                    <label for="estado" class="label_nomina">Estado</label>
                    <div class="form-group">
            <div class="input_consulta">
            <div class="icon-container2">
                        <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
                    </div>
                    <select name="estado" id="estado" class="form-control"  style="background-color: #ffff; display:block; width: 100%;" >
                        <option value="activo">ACTIVO</option>
                        <option value="devuelto">DEVUELTO</option>
                        <option value="retrasado">RETRASADO</option>
                        <option value="cancelado">CANCELADO</option>
                        <option value="vendido">VENDIDO</option>
                    </select>
                </div>
                </div>
                </div>

                <div class="mb-3">
    <label for="condiciones_prestamo" class="form-label">Condiciones del Préstamo</label>
    <textarea name="condiciones_prestamo" id="condiciones_prestamo" rows="4" class="form-control" placeholder="Descripción..." style="text-transform: uppercase;"></textarea>
</div>

<div class="mb-3">
    <div class="form-group">
        <label for="observaciones" class="label_nomina">Observaciones</label>
        <textarea name="observaciones" id="observaciones" rows="3" class="form-control select" placeholder="Observaciones adicionales..." style="height: 130px; text-transform: uppercase;"></textarea>
    </div>
</div>

 <!-- Registrado por (Abajo de los anteriores) -->
 <div class="col-md-12 mt-3">
        @auth
        <div class="form-group" style="display:none">
            <label for="user_name" class="label_nomina">Registrado por</label>
            <input type="text" class="form-control" id="user_name" name="user_name" value="{{ Auth::user()->name }}" readonly>
        </div>
        @endauth
    </div>
</div>
 <!-- Firma Digital -->
 <div class="mb-3">
    <label for="firmaCanvas" class="form-label">Firma Digital Del Responsable</label>
    <div class="border rounded mb-2" style="height: 150px; overflow: hidden;">
        <canvas id="firmaCanvas" class="w-100 h-100"></canvas>
    </div>
    <div class="text-end">
        <button id="limpiarFirma" class="btn btn-danger font-weight-bold" type="button">Limpiar Firma</button>
    </div>
    <input type="hidden" id="firmaInput" name="firmaDigital" value="{{ old('firmaDigital') }}" />

</div>
</div>               
                    <button type="submit" class="btn btn-primary">
                        Guardar Préstamo
                    </button>
                
            </form>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById('firmaCanvas');
    const ctx = canvas.getContext('2d');
    const limpiarFirma = document.getElementById('limpiarFirma');
    const firmaInput = document.getElementById('firmaInput');
    let dibujando = false;

    function ajustarCanvas() {
        const container = canvas.parentElement;
        const width = container.offsetWidth;
        const height = container.offsetHeight;

        const ratio = window.devicePixelRatio || 1;
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

        ctx.fillStyle = "#fff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }

    ajustarCanvas();
    window.addEventListener('resize', ajustarCanvas);

    function obtenerPosicionCanvas(event) {
        const rect = canvas.getBoundingClientRect();
        const x = (event.touches ? event.touches[0].clientX : event.clientX) - rect.left;
        const y = (event.touches ? event.touches[0].clientY : event.clientY) - rect.top;
        return { x, y };
    }

    function comenzarDibujo(event) {
        dibujando = true;
        const { x, y } = obtenerPosicionCanvas(event);
        ctx.beginPath();
        ctx.moveTo(x, y);
        event.preventDefault();
    }

    function detenerDibujo() {
        if (!dibujando) return;
        dibujando = false;
        ctx.closePath();
        firmaInput.value = canvas.toDataURL('image/png');
    }

    function dibujar(event) {
        if (!dibujando) return;
        const { x, y } = obtenerPosicionCanvas(event);
        ctx.lineTo(x, y);
        ctx.strokeStyle = '#222';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();
        event.preventDefault();
    }

    canvas.addEventListener('mousedown', comenzarDibujo);
    canvas.addEventListener('mousemove', dibujar);
    canvas.addEventListener('mouseup', detenerDibujo);
    canvas.addEventListener('mouseout', detenerDibujo);

    canvas.addEventListener('touchstart', comenzarDibujo, { passive: false });
    canvas.addEventListener('touchmove', dibujar, { passive: false });
    canvas.addEventListener('touchend', detenerDibujo);
    canvas.addEventListener('touchcancel', detenerDibujo);

    limpiarFirma.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "#fff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.beginPath();
        firmaInput.value = '';
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const registros = @json($registros);
    const clientes = @json($clientes);

    const campos = [
        {
            input: "search-registro",
            list: "registro-list",
            hidden: "registro_id",
            data: registros,
            match: item => item.numero_serie,
            display: item => item.numero_serie,
            onSelect: item => {
                document.getElementById("subtipo_equipo").value = item.subtipo_equipo;
                document.getElementById("marca").value = item.marca;
                document.getElementById("modelo").value = item.modelo;
                document.getElementById("infoRegistro").classList.remove("d-none");
            }
        },
        {
            input: "search-cliente",
            list: "cliente-list",
            hidden: "cliente_id",
            data: clientes,
            match: item => `${item.nombre} ${item.apellido}`,
            display: item => `${item.nombre} ${item.apellido}`,
            onSelect: () => {} // no hace nada adicional
        }
    ];

    campos.forEach(campo => {
        const input = document.getElementById(campo.input);
        const list = document.getElementById(campo.list);
        const hidden = document.getElementById(campo.hidden);

        input.addEventListener("input", function () {
            const query = this.value.toLowerCase().trim();
            list.innerHTML = "";

            if (query === "") {
                list.style.display = "none";
                return;
            }

            const resultados = campo.data.filter(item =>
                campo.match(item).toLowerCase().includes(query)
            );

            if (resultados.length === 0) {
                let item = document.createElement("li");
                item.innerHTML = `<button class="dropdown-item text-muted disabled">Sin resultados</button>`;
                list.appendChild(item);
                list.style.display = "block";
                return;
            }

            resultados.slice(0, 6).forEach(item => {
                let button = document.createElement("button");
                button.className = "dropdown-item";
                button.textContent = campo.display(item);
                button.type = "button"; // evita submit
                button.addEventListener("click", function () {
                    input.value = campo.display(item);
                    hidden.value = item.id;
                    campo.onSelect(item);
                    list.style.display = "none";
                });

                let li = document.createElement("li");
                li.appendChild(button);
                list.appendChild(li);
            });

            list.style.display = "block";
        });

        // Cierra la lista si se hace clic fuera
        document.addEventListener("click", function (e) {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.style.display = "none";
            }
        });
    });
});
</script>



<script>
$(document).ready(function() {
    $('#registro_id').select2({
        placeholder: 'SELECCIONA EL NÚMERO DE SERIE',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () {
                return "No se encontró ningún resultado";
            }
        }
    });

    $('#cliente_id').select2({
        placeholder: 'SELECCIONA UN CLIENTE O CONGRESO',
        allowClear: true,
        width: '100%'
    });

    // Mostrar datos al seleccionar serie
    $('#registro_id').on('change', function () {
        const selected = $(this).find('option:selected');
        const subtipo = selected.data('subtipo');
        const marca = selected.data('marca');
        const modelo = selected.data('modelo');

        if (subtipo || marca || modelo) {
            $('#infoRegistro').removeClass('d-none');
            $('#subtipo_equipo').val(subtipo);
            $('#marca').val(marca);
            $('#modelo').val(modelo);
        } else {
            $('#infoRegistro').addClass('d-none');
            $('#subtipo_equipo, #marca, #modelo').val('');
        }
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
