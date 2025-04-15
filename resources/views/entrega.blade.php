@extends('layouts.app')
@section('title', 'Guias')
@section('titulo', 'Entrega de Guia')
@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
    

<div class="form-contenedor">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('entrega.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
    <label for="search-guia" class="label_nomina">Buscar Guía</label>
    <div class="input_consulta position-relative">
        <div class="icon-container2">
            <img src="{{ asset('images/fedex.png') }}" alt="Acceso" class="icon2">
        </div>
        <input type="text" id="search-guia" style="background-color: #ffff;" class="form-control" placeholder="Ingrese número de rastreo..." autocomplete="off">
        <input type="hidden" id="guia_id" name="guia_id" required>
        <ul class="dropdown-menu modern-dropdown w-100 position-absolute" id="guia-list" style="top: 100%; left: 0; z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></ul>
    </div>
</div>



        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="peso" class="label_nomina">Peso Total (kg)</label>
                    <div class="form-group">
            <div class="input_consulta">
            <div class="icon-container2">
                    
                        <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
                    </div>
                    <input type="text" class="form-control" id="peso" name="peso" readonly style="background-color: #ffff; display:block; width: 100%;">
                </div>
            </div>
            </div>
            </div>
           
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required value="{{ \Carbon\Carbon::today()->toDateString() }}">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="contenido" class="label_nomina">Contenido del Paquete</label>
                    <div class="form-group">
            <div class="input_consulta">
            <div class="icon-container2">
                    
                        <img src="{{ asset('images/paquete.png') }}" alt="Acceso" class="icon2">
                    </div>
                    <input type="text" class="form-control" id="contenido" name="contenido" required style="background-color: #ffff; display:block; width: 100%;">
                </div>
            </div>
            </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="numero_serie" class="label_nomina">Número de Serie</label>
                    <div class="form-group">
            <div class="input_consulta">
            <div class="icon-container2">
                    
                        <img src="{{ asset('images/serie.png') }}" alt="Acceso" class="icon2">
                    </div>
                    <input type="text" class="form-control" id="numero_serie" name="numero_serie" required style="background-color: #ffff; display:block; width: 100%;">
                </div>
            </div>
            </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="destinatario" class="label_nomina">Destinatario</label>
                    <div class="form-group">
            <div class="input_consulta">
            <div class="icon-container2">
                    
                        <img src="{{ asset('images/destinatario.png') }}" alt="Acceso" class="icon2">
                    </div>
                    <input type="text" class="form-control" id="destinatario" name="destinatario" required style="background-color: #ffff; display:block; width: 100%;">
                </div>
            </div>
        </div>
        </div>
        </div>
        
        <div class="row">
    <!-- Imagen (Izquierda) -->
    <div class="col-md-6 col-lg-4 text-center mt-3">
        <label class="form-label">Imagen</label>
        <div class="image-container">
            <label for="image-upload" class="image-preview">
                <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                <span id="preview-text">Añadir imagen</span>
            </label>
            <input type="file" id="image-upload" name="imagen" accept="image/*" hidden>
        </div>
    </div>

    <!-- Observaciones (Derecha) -->
    <div class="col-md-6 col-lg-8">
        <div class="form-group">
            <label for="observaciones" class="label_nomina">Observaciones</label>
            <textarea class="form-control select" id="observaciones" name="observaciones" placeholder="Escribe aquí una descripción detallada del equipo" style="height: 130px;"></textarea>
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



        <div class="form-group w-100">
            <label for="firmaCanvas" class="form-label">Firma Digital</label>
            <div class="border rounded mb-2" style="height: 150px; overflow: hidden; width: 100%;">
                <canvas id="firmaCanvas" class="w-100 h-100"></canvas>
            </div>
            <div class="text-end">
            <button id="limpiarFirma" class="btn btn-danger font-weight-bold" type="button">Limpiar Firma</button>

            </div>
            <input type="hidden" id="firmaInput" name="firmaDigital">
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectGuia = document.getElementById("guia_id");
    let inputPeso = document.getElementById("peso");

    if (selectGuia) {
        selectGuia.addEventListener("change", function () {
            let selectedOption = selectGuia.options[selectGuia.selectedIndex];  
            let peso = selectedOption.getAttribute("data-peso"); 

            inputPeso.value = peso ? Math.round(parseFloat(peso)) + " kg" : "";
        });
    }

    // Convertir a mayúsculas en tiempo real
    function convertirAMayusculas(event) {
        event.target.value = event.target.value.toUpperCase();
    }

    let inputs = ["contenido", "numero_serie", "destinatario", "observaciones"];
    inputs.forEach(function (id) {
        let input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", convertirAMayusculas);
        }
    });

    // Configuración del canvas para firma digital
    const canvas = document.getElementById('firmaCanvas');
    const ctx = canvas.getContext('2d');
    const limpiarFirma = document.getElementById('limpiarFirma');
    const firmaInput = document.getElementById('firmaInput');
    let dibujando = false;

    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

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
        dibujando = false;
        ctx.beginPath();
        firmaInput.value = canvas.toDataURL();
    }

    function dibujar(event) {
        if (!dibujando) return;
        const { x, y } = obtenerPosicionCanvas(event);
        ctx.lineTo(x, y);
        ctx.strokeStyle = '#333';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
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
        firmaInput.value = '';
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let guias = @json($guias); // Obtener guías desde Laravel
    let searchInput = document.getElementById("search-guia");
    let guiaList = document.getElementById("guia-list");
    let inputGuiaId = document.getElementById("guia_id");
    let inputPeso = document.getElementById("peso");

    searchInput.addEventListener("input", function () {
        let query = this.value.replace(/\D/g, ""); // Solo números
        query = query.replace(/(.{4})/g, "$1 ").trim(); // Espacios cada 4 dígitos
        this.value = query;

        let cleanQuery = query.replace(/\s+/g, ""); // Sin espacios para búsqueda
        guiaList.innerHTML = ""; // Limpiar lista

        if (cleanQuery === "") {
            guiaList.style.display = "none";
            return;
        }

        let resultados = guias.filter(guia => guia.numero_rastreo.replace(/\s+/g, "").includes(cleanQuery));

        if (resultados.length === 0) {
            let item = document.createElement("li");
            item.innerHTML = `<button class="dropdown-item text-muted disabled">Sin registros disponibles</button>`;
            guiaList.appendChild(item);
            guiaList.style.display = "block";
            return;
        }

        resultados.slice(0, 6).forEach(guia => {
            let item = document.createElement("li");
            item.innerHTML = `<button class="dropdown-item" data-id="${guia.id}" data-peso="${guia.peso}">${guia.numero_rastreo}</button>`;
            item.addEventListener("click", function () {
                searchInput.value = guia.numero_rastreo;
                inputGuiaId.value = guia.id;
                inputPeso.value = guia.peso ? Math.round(parseFloat(guia.peso)) + " kg" : ""; // Asignar peso
                guiaList.style.display = "none";
            });
            guiaList.appendChild(item);
        });

        guiaList.style.display = "block";
    });

    // Cerrar lista si se hace clic fuera
    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !guiaList.contains(event.target)) {
            guiaList.style.display = "none";
        }
    });

    // Manejo de teclas arriba/abajo en la lista
    searchInput.addEventListener("keydown", function (event) {
        let items = guiaList.querySelectorAll(".dropdown-item:not(.disabled)");
        if (items.length === 0) return;

        let activeIndex = Array.from(items).findIndex(item => item.classList.contains("active"));

        if (event.key === "ArrowDown") {
            event.preventDefault();
            activeIndex = activeIndex < items.length - 1 ? activeIndex + 1 : 0;
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            activeIndex = activeIndex > 0 ? activeIndex - 1 : items.length - 1;
        } else if (event.key === "Enter" && activeIndex !== -1) {
            event.preventDefault();
            items[activeIndex].click();
            return;
        }

        items.forEach(item => item.classList.remove("active"));
        if (activeIndex !== -1) {
            items[activeIndex].classList.add("active");
        }
    });
});


</script>

<script>
document.getElementById('image-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewIcon = document.getElementById('preview-icon');
            const previewText = document.getElementById('preview-text');
            const previewContainer = previewIcon.parentElement; // Asegurar que está dentro del contenedor
            
            previewIcon.src = e.target.result;
            previewIcon.style.width = '100%';
            previewIcon.style.height = 'auto';
            previewIcon.style.maxWidth = '100%';
            previewIcon.style.maxHeight = '300px';
            previewIcon.style.objectFit = 'contain';
            previewIcon.style.borderRadius = '10px';
            previewIcon.style.display = 'block'; // Evita problemas con elementos inline
            previewIcon.style.margin = '0 auto'; // Centrar dentro del contenedor
            
            previewContainer.style.overflow = 'hidden'; // Evita que se salga
            previewContainer.style.textAlign = 'center'; // Centrar si es necesario
            
            previewText.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>
</body>
@endsection
