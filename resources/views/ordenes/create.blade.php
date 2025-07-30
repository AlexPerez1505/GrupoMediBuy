@extends('layouts.app')
@section('titulo', 'Orden Servicio')
@section('title', 'Nuevo Mantenimiento')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  :root {
    --clr-primary: #A3D5FF;
    --clr-secondary: #FFC9DE;
    --clr-accent: #FF8C94;
    --clr-bg: #F5F7FA;
    --clr-panel: #FFFFFF;
    --clr-border: #E0E0E0;
    --transition: all .4s ease;
  }

  @keyframes bgPulse {
    0%,100% { background-color: var(--clr-bg); }
    50%     { background-color: #E0F7FA; }
  }
  body {
    animation: bgPulse 20s infinite alternate;
    font-family: "Arial MT", Arial, sans-serif;
    color: #34495e;
    margin: 0;
    padding: 0;
  }

  .wizard-container {
    max-width: 800px;
    margin: 2rem auto;
    margin-top:110px !important;
    background: var(--clr-panel);
    border-radius: 19px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    overflow: hidden;
  }

  .wizard-header {
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid var(--clr-border);
  }
  .wizard-header h2 {
    margin: 0;
    font-size: 2rem;
    color: var(--clr-primary);
    letter-spacing: 1px;
  }

  .wizard-progress-info {
    text-align: center;
    padding: .75rem 1rem;
    font-size: .95rem;
    font-weight: 600;
    color: var(--clr-accent);
    border-bottom: 1px solid var(--clr-border);
  }

  .wizard-progress {
    height: 12px;
    background: var(--clr-border);
    border-radius: 6px;
    overflow: hidden;
    margin: 0 2rem 1.5rem;
  }
  .wizard-progress-bar {
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, var(--clr-primary), var(--clr-secondary));
    transition: width .5s ease;
  }

  .wizard-step {
    display: none;
    padding: 2rem;
    animation: fadeInUp .5s both;
  }
  .wizard-step.active {
    display: block;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .form-label {
    font-weight: 600;
    color: #2c3e50;
  }
  .form-control, .form-select {
    border-radius: 8px;
    border: 2px solid var(--clr-border);
    padding: .5rem .75rem;
    transition: var(--transition);
  }
  .form-control:focus, .form-select:focus {
    border-color: var(--clr-secondary);
    box-shadow: 0 0 8px rgba(255,140,148,0.3);
    outline: none;
  }

  .btn {
    border-radius: 50px;
    padding: .6rem 1.6rem;
    font-weight: 600;
    transition: var(--transition);
  }
  .btn-primary {
    background: var(--clr-primary);
    border: none;
    color: #fff;
    box-shadow: 0 6px 16px rgba(163,213,255,0.4);
  }
  .btn-primary:hover {
    background: var(--clr-accent);
    transform: translateY(-2px);
  }
  .btn-secondary {
    background: #BDC3C7;
    border: none;
    color: #fff;
  }
</style>

<div class="wizard-container">
  <div class="wizard-header">
    <h2>Orden de Servicio / Mantenimiento</h2>
  </div>

  <!-- Leyenda de progreso -->
  <div class="wizard-progress-info" id="wizard-progress-info">
    Paso 1 de 4: Cliente & Fecha
  </div>

  <!-- Barra de progreso -->
  <div class="wizard-progress">
    <div class="wizard-progress-bar" id="wizard-progress-bar"></div>
  </div>

  <div class="p-4">
    <form id="wizard-form" action="{{ route('orden.store') }}" method="POST">
      @csrf

      <!-- Step 1 -->
      <div class="wizard-step active" data-step="1">
        <div class="mb-3">
          <label for="cliente_id" class="form-label">Cliente</label>
          <select name="cliente_id" id="cliente_id" class="form-select" required>
            <option value="">— Selecciona cliente —</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}">
                {{ $c->nombre }} {{ $c->apellido }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="row gx-3">
          <div class="col-md-6 mb-3">
            <label for="fecha_entrada" class="form-label">Fecha de Entrada</label>
            <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="fecha_mantenimiento">Fecha de Mantenimiento</label>
            <input type="text" id="fecha_mantenimiento" class="form-control" readonly
                   value="{{ now()->format('Y-m-d') }}">
            <input type="hidden" name="fecha_mantenimiento" id="fecha_mantenimiento_input"
                   value="{{ now()->format('Y-m-d') }}">
          </div>
        </div>
        <button type="button" class="btn btn-primary next-step">Siguiente →</button>
      </div>

      <!-- Step 2 -->
      <div class="wizard-step d-none" data-step="2">
        <div class="mb-3">
          <label for="proximo_mantenimiento" class="form-label">Próximo Mantenimiento (meses)</label>
          <select name="proximo_mantenimiento" id="proximo_mantenimiento" class="form-select" required>
            <option value="">— Selecciona —</option>
            <option value="3">3 meses</option>
            <option value="6">6 meses</option>
            <option value="12">12 meses</option>
          </select>
        </div>
        <button type="button" class="btn btn-secondary prev-step">← Anterior</button>
        <button type="button" class="btn btn-primary next-step">Siguiente →</button>
      </div>

      <!-- Step 3 -->
      <div class="wizard-step d-none" data-step="3">
        <div class="mb-3">
          <label for="aparato_id" class="form-label">Aparato</label>
          <select name="aparato_id" id="aparato_id" class="form-select" required>
            <option value="">— Selecciona aparato —</option>
            @foreach($aparatos as $a)
              <option value="{{ $a->id }}">
                {{ $a->tipo }} ({{ $a->nombre }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Checklist Preventivo</label>
          <div id="checklist-items" class="list-group">
            {{-- Acá se inyectan los checkboxes via JS --}}
          </div>
        </div>
        <button type="button" class="btn btn-secondary prev-step">← Anterior</button>
        <button type="button" class="btn btn-primary next-step">Siguiente →</button>
      </div>

      <!-- Step 4 -->
      <div class="wizard-step d-none" data-step="4">
        <h5 class="mb-3">Revise los datos antes de enviar:</h5>
        <ul class="list-group mb-4">
          <li class="list-group-item"><strong>Cliente:</strong> <span id="confirm-cliente"></span></li>
          <li class="list-group-item"><strong>Fecha Entrada:</strong> <span id="confirm-fecha-entrada"></span></li>
          <li class="list-group-item"><strong>Fecha Mto.:</strong> <span id="confirm-fecha-mantenimiento"></span></li>
          <li class="list-group-item"><strong>Próximo Mto.:</strong> <span id="confirm-proximo-mto"></span></li>
          <li class="list-group-item"><strong>Aparato:</strong> <span id="confirm-aparato"></span></li>
          <li class="list-group-item">
            <strong>Checklist:</strong>
            <ul id="confirm-checklist" class="mb-0"></ul>
          </li>
        </ul>
        <button type="button" class="btn btn-secondary prev-step">← Anterior</button>
        <button type="submit" class="btn btn-primary">✔️ Generar PDF</button>
      </div>
    </form>
  </div>
</div>




{{-- Scripts --}}
{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const steps      = Array.from(document.querySelectorAll('.wizard-step'));
    const totalSteps = steps.length;
    let current      = 0;

    const stepLabels = [
      'Cliente & Fecha',
      'Próximo Mto.',
      'Aparato & Checklist',
      'Confirmar'
    ];

    function updateProgress() {
      const pct  = ((current + 1) / totalSteps) * 100;
      document.getElementById('wizard-progress-bar').style.width = pct + '%';
      document.getElementById('wizard-progress-info').textContent =
        `Paso ${current + 1} de ${totalSteps}: ${stepLabels[current]}`;
    }

    function showStep(idx) {
      steps.forEach((st,i) => {
        st.classList.toggle('d-none', i !== idx);
        st.classList.toggle('active', i === idx);
      });
      if (idx === 2) populateChecklist();
      if (idx === totalSteps - 1) populateConfirmation();
      updateProgress();
    }

    function validateStep(idx) {
      const inputs = steps[idx].querySelectorAll('select[required], input[required]');
      let valid = true;
      inputs.forEach(inp => {
        if (!inp.value) {
          inp.classList.add('is-invalid');
          valid = false;
        } else {
          inp.classList.remove('is-invalid');
        }
      });
      return valid;
    }

    async function populateChecklist() {
      const sel       = document.getElementById('aparato_id');
      const aparatoId = sel.value;
      const container = document.getElementById('checklist-items');
      container.innerHTML = '';

      if (!aparatoId) {
        container.textContent = 'Selecciona un aparato primero';
        return;
      }

      try {
        const res  = await fetch(`/aparatos/${aparatoId}/checklist-items`);
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();  // { conexiones: [...], botones: [...], componentes: [...] }
        const cats = ['conexiones','botones','componentes'];

        cats.forEach(cat => {
          container.insertAdjacentHTML('beforeend',
            `<div class="fw-bold text-uppercase mt-2">${cat}</div>`
          );
          const items = data[cat] || [];
          if (items.length) {
            items.forEach(item => {
              container.insertAdjacentHTML('beforeend', `
                <label class="list-group-item">
                  <input
                    class="form-check-input me-2"
                    type="checkbox"
                    name="checklist[]"
                    value="${item.id}">
                  ${item.nombre} – ${item.resultado}
                </label>
              `);
            });
          } else {
            container.insertAdjacentHTML('beforeend',
              `<div class="text-muted">— No hay ítems en ${cat} —</div>`
            );
          }
        });
      } catch (e) {
        console.error(e);
        container.textContent = 'Error cargando checklist';
      }
    }

    function populateConfirmation() {
      document.getElementById('confirm-cliente').textContent =
        document.querySelector('#cliente_id option:checked')?.text || '';
      document.getElementById('confirm-fecha-entrada').textContent =
        document.getElementById('fecha_entrada').value;
      document.getElementById('confirm-fecha-mantenimiento').textContent =
        document.getElementById('fecha_mantenimiento_input').value;
      document.getElementById('confirm-proximo-mto').textContent =
        document.querySelector('#proximo_mantenimiento option:checked')?.text || '';
      document.getElementById('confirm-aparato').textContent =
        document.querySelector('#aparato_id option:checked')?.text || '';

      const checked = Array.from(
        document.querySelectorAll('#checklist-items input:checked')
      ).map(i => i.parentElement.textContent.trim());
      const ul = document.getElementById('confirm-checklist');
      ul.innerHTML = '';
      checked.forEach(txt => ul.insertAdjacentHTML('beforeend', `<li>${txt}</li>`));
    }

    document.querySelectorAll('.next-step').forEach(btn =>
      btn.addEventListener('click', () => {
        if (!validateStep(current)) return;
        document.getElementById('fecha_mantenimiento_input').value =
          document.getElementById('fecha_mantenimiento').value;
        current = Math.min(totalSteps - 1, current + 1);
        showStep(current);
      })
    );

    document.querySelectorAll('.prev-step').forEach(btn =>
      btn.addEventListener('click', () => {
        current = Math.max(0, current - 1);
        showStep(current);
      })
    );

    // Lanza la carga de checklist al cambiar de aparato
    document.getElementById('aparato_id')
            .addEventListener('change', populateChecklist);

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
