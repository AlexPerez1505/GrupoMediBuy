@extends('layouts.app')

@section('title', 'Nuevo Mantenimiento')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <h2 class="mb-4 text-primary">📝 Orden de Servicio / Mantenimiento</h2>
    <form id="wizard-form" action="{{ route('orden.store') }}" method="POST">
        @csrf

        <!-- 👉 Step indicators -->
        <ul class="nav nav-tabs mb-4" id="wizard-tabs">
            <li class="nav-item"><a class="nav-link active" data-step="1" href="#">1. Cliente & Fecha</a></li>
            <li class="nav-item"><a class="nav-link disabled" data-step="2" href="#">2. Próximo Mto.</a></li>
            <li class="nav-item"><a class="nav-link disabled" data-step="3" href="#">3. Aparato & Checklist</a></li>
            <li class="nav-item"><a class="nav-link disabled" data-step="4" href="#">4. Confirmar</a></li>
        </ul>

        <!-- 👉 Step 1: Cliente & Fecha -->
        <div class="wizard-step" data-step="1">
            <div class="mb-3">
                <label for="cliente_id" class="form-label">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-select" required>
                    <option value="">-- Selecciona un cliente --</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }} {{ $c->apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fecha_entrada" class="form-label">Fecha de Entrada</label>
                    <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_mantenimiento" class="form-label">Fecha de Mantenimiento</label>
                    <input type="text"
                           id="fecha_mantenimiento"
                           class="form-control"
                           readonly
                           value="{{ now()->format('Y-m-d') }}">
                    <!-- Campo oculto para enviar al backend -->
                    <input type="hidden"
                           name="fecha_mantenimiento"
                           id="fecha_mantenimiento_input"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            <button type="button" class="btn btn-primary next-step">Siguiente &raquo;</button>
        </div>

        <!-- 👉 Step 2: Próximo Mantenimiento -->
        <div class="wizard-step d-none" data-step="2">
            <div class="mb-3">
                <label for="proximo_mantenimiento" class="form-label">Próximo Mantenimiento (meses)</label>
                <select name="proximo_mantenimiento" id="proximo_mantenimiento" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <option value="3">3 meses</option>
                    <option value="6">6 meses</option>
                    <option value="12">12 meses</option>
                </select>
            </div>
            <button type="button" class="btn btn-secondary prev-step">&laquo; Anterior</button>
            <button type="button" class="btn btn-primary next-step">Siguiente &raquo;</button>
        </div>

        <!-- 👉 Step 3: Aparato & Checklist -->
        <div class="wizard-step d-none" data-step="3">
            <div class="mb-3">
                <label for="aparato_id" class="form-label">Aparato</label>
                <select name="aparato_id" id="aparato_id" class="form-select" required>
                    <option value="">-- Selecciona un aparato --</option>
                    @foreach($aparatos as $a)
                        <option value="{{ $a->id }}" data-type="{{ strtoupper($a->tipo) }}">
                            {{ $a->tipo }} ({{ $a->nombre }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="checklist-container" class="mb-3">
                <label class="form-label">Checklist Preventivo</label>
                <div id="checklist-items" class="list-group"></div>
            </div>

            <button type="button" class="btn btn-secondary prev-step">&laquo; Anterior</button>
            <button type="button" class="btn btn-primary next-step">Siguiente &raquo;</button>
        </div>

        <!-- 👉 Step 4: Confirmar & Enviar -->
        <div class="wizard-step d-none" data-step="4">
            <h5>Revise los datos antes de enviar:</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Cliente:</strong> <span id="confirm-cliente"></span></li>
                <li class="list-group-item"><strong>Fecha Entrada:</strong> <span id="confirm-fecha-entrada"></span></li>
                <li class="list-group-item"><strong>Fecha Mto.:</strong> <span id="confirm-fecha-mantenimiento"></span></li>
                <li class="list-group-item"><strong>Próximo Mto.:</strong> <span id="confirm-proximo-mto"></span></li>
                <li class="list-group-item"><strong>Aparato:</strong> <span id="confirm-aparato"></span></li>
                <li class="list-group-item"><strong>Checklist:</strong>
                    <ul id="confirm-checklist" class="mb-0"></ul>
                </li>
            </ul>

            <button type="button" class="btn btn-secondary prev-step">&laquo; Anterior</button>
            <button type="submit" class="btn btn-success">✔️ Generar PDF</button>
        </div>
    </form>
</div>
{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const steps = Array.from(document.querySelectorAll('.wizard-step'));
    let current = 0;

    // === 1) Mapa de checklist según el tipo de aparato (en MAYÚSCULAS) ===
    const checklistMap = {
        'COLONOSCOPIO': [
            'Conector de luz - Bueno y Funcional',
            'Cubierta distal - Bueno y Funcional',
            'Tubo de inserción - Funcional',
            'Puerto de biopsia - Limpio y Funcional',
            'Botón de succión - Funcional',
            'Perrilla de control - Bueno y Fluida'
        ],
        'FUENTE DE LUZ L9000': [
            'Cable de alimentación - Bueno',
            'Fibra de luz blanca - Intacta'
        ],
        'FUENTE DE LUZ L10': [
            'Cable de alimentación - Bueno',
            'Fibra de luz verde - Intacta',
            'Interfaz (USB-USB) - Funcional'
        ],
        'INSUFLADOR 40 LTS': [
            'Manguera - Sin fugas',
            'Yugo - Ajustado',
            'Adaptador trasero de CO₂ - Correcto'
        ],
        // Puedes añadir más aquí
    };

    // === 2) Mostrar el paso activo del wizard ===
    function showStep(idx) {
        steps.forEach((st, i) => {
            st.classList.toggle('d-none', i !== idx);
            const tab = document.querySelector(`#wizard-tabs a[data-step="${i + 1}"]`);
            if (tab) {
                tab.classList.toggle('active', i === idx);
                tab.classList.toggle('disabled', i < current);
            }
        });

        // Al entrar al paso 3, asegúrate de renderizar el checklist
        if (idx === 2) {
            populateChecklist();
        }
    }

    // === 3) Validación de campos obligatorios en cada paso ===
    function validateStep(idx) {
        const inputs = steps[idx].querySelectorAll('select[required], input[required]');
        for (let inp of inputs) {
            if (!inp.value) {
                inp.classList.add('is-invalid');
                return false;
            }
            inp.classList.remove('is-invalid');
        }
        return true;
    }

    // === 4) Generar dinámicamente el checklist según el aparato seleccionado ===
    function populateChecklist() {
        const sel = document.getElementById('aparato_id');
        const rawType = sel.options[sel.selectedIndex]?.dataset?.type || '';
        const tipo = rawType.trim().toUpperCase();
        console.log('[DEBUG] populateChecklist() con tipo:', tipo);

        const items = checklistMap[tipo] || ['Sin checklist definido'];
        console.log('[DEBUG] items a renderizar:', items);

        const container = document.getElementById('checklist-items');
        container.innerHTML = ''; // Limpiar checklist

        items.forEach((txt, i) => {
            const id = `chk_${i}`;
            container.insertAdjacentHTML('beforeend', `
                <label class="list-group-item">
                    <input 
                        class="form-check-input me-2" 
                        type="checkbox" 
                        id="${id}"
                        name="checklist[]"
                        value="${txt}"
                    >
                    ${txt}
                </label>
            `);
        });

        console.log(`[DEBUG] Checklist renderizado con ${items.length} item(s)`);
    }

    // === 5) Mostrar la vista de confirmación con los datos capturados ===
    function populateConfirmation() {
        document.getElementById('confirm-cliente').textContent =
            document.querySelector('#cliente_id option:checked')?.text || '';
        document.getElementById('confirm-fecha-entrada').textContent =
            document.getElementById('fecha_entrada').value;
        document.getElementById('confirm-fecha-mantenimiento').textContent =
            document.getElementById('fecha_mantenimiento').value;
        document.getElementById('confirm-proximo-mto').textContent =
            document.querySelector('#proximo_mantenimiento option:checked')?.text || '';
        document.getElementById('confirm-aparato').textContent =
            document.querySelector('#aparato_id option:checked')?.text || '';

        const checked = Array.from(document.querySelectorAll('#checklist-items input:checked'))
            .map(i => i.value);

        const ul = document.getElementById('confirm-checklist');
        ul.innerHTML = '';
        checked.forEach(txt => {
            ul.insertAdjacentHTML('beforeend', `<li>${txt}</li>`);
        });
    }

    // === 6) Navegación - Siguiente paso ===
    document.querySelectorAll('.next-step').forEach(btn => {
        btn.addEventListener('click', () => {
            console.log(`[DEBUG] Intentando avanzar desde paso ${current + 1}`);
            if (!validateStep(current)) return;

            // Actualizar fecha de mantenimiento oculta
            document.getElementById('fecha_mantenimiento_input').value =
                document.getElementById('fecha_mantenimiento').value;
            console.log('[DEBUG] fecha_mantenimiento enviada:', document.getElementById('fecha_mantenimiento_input').value);

            if (current === 3) populateConfirmation();

            current++;
            showStep(current);
        });
    });

    // === 7) Navegación - Paso anterior ===
    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', () => {
            current = Math.max(0, current - 1);
            showStep(current);
        });
    });

    // === 8) Cambio de aparato (para regenerar checklist) ===
    document.getElementById('aparato_id')
        .addEventListener('change', () => {
            console.log('[DEBUG] Aparato cambiado:', document.getElementById('aparato_id').value);
            populateChecklist();
        });

    // === 9) Iniciar wizard en paso 0 ===
    showStep(0);
});
</script>


<script>
  // --- Depuración en cliente ---
  document.getElementById('wizard-form').addEventListener('submit', function(e) {
    console.log('🛠️ [DEBUG] Form submit disparado');
    // Capturamos todos los pares clave-valor
    const data = new FormData(this);
    for (let [k,v] of data.entries()) {
      console.log(`  • ${k} =`, v);
    }
    // Permitimos que continúe el envío
  });
</script>

@endsection
