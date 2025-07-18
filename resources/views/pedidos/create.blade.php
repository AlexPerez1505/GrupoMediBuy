@extends('layouts.app')

@section('content')
<style>
    .card-section {
        background-color: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 500;
        color: #333;
    }

    .form-control {
        border-radius: 0.5rem;
    }

    h2, h4 {
        font-weight: 600;
        color: #1f2937;
    }

    .btn-primary {
        border-radius: 0.5rem;
    }

    .btn-secondary {
        border-radius: 0.5rem;
        background-color: #e5e7eb;
        color: #374151;
        border: none;
    }

    .btn-danger {
        border-radius: 0.5rem;
    }
  .btn-pastel {
        background-color: #f3e8ff;
        color: #6b21a8;
        font-weight: 600;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        transition: background-color 0.2s ease-in-out;
    }

    .btn-pastel:hover {
        background-color: #e9d5ff;
        color: #581c87;
    }

    .btn-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        font-weight: 600;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.9rem;
    }

    .btn-danger:hover {
        background-color: #fecaca;
        color: #991b1b;
    }

    .equipo, .componente {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #c4b5fd;
    }
    .equipo-card, .componente-card {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
    }

    .alert-success {
        border-radius: 0.5rem;
    }
    body{
       background: #f4f7fb; 
    }
    .container{
        margin-top:90px !important;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4">📦 Crear Nuevo Pedido</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('pedidos.store') }}" method="POST" id="form-pedido">
        @csrf

        <div class="row">
            <!-- IZQUIERDA -->
            <div class="col-md-6">
                <div class="card-section">
                    <div class="mb-3">
                        <label for="fecha_programada" class="form-label">📅 Fecha Programada de Llegada</label>
                        <input type="date" name="fecha_programada" id="fecha_programada" class="form-control" required>
                    </div>

                   <div class="mb-3">
    <label for="creado_por" class="form-label">👤 Creado por (Jefe)</label>
    <input type="text" name="creado_por" id="creado_por" class="form-control" 
           value="{{ Auth::user()->name }}" readonly>
</div>


                    <div class="mb-3">
                        <label for="observaciones" class="form-label">📝 Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>

            <!-- DERECHA -->
            <div class="col-md-6">
                <div class="card-section">
                    <h4>🖥 Equipos que llegarán</h4>
                    <div id="equipos-container"></div>
                    <button type="button" class="btn btn-pastel mt-2" id="btn-add-equipo">+ Agregar Equipo</button>
                </div>
            </div>
        </div>

        <!-- COMPONENTES -->
        <div class="card-section">
            <h4>🔧 Componentes esperados (manual)</h4>
            <div id="componentes-container"></div>
            <button type="button" class="btn btn-pastel mt-2 mb-3" id="btn-add-componente">+ Agregar Componente</button>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">✅ Crear Pedido</button>
        </div>
    </form>
</div>
<script>
    const equiposContainer = document.getElementById('equipos-container');
    const componentesContainer = document.getElementById('componentes-container');
    const btnAddEquipo = document.getElementById('btn-add-equipo');
    const btnAddComponente = document.getElementById('btn-add-componente');

    btnAddEquipo.addEventListener('click', agregarEquipo);
    btnAddComponente.addEventListener('click', agregarComponente);

    function agregarEquipo() {
        const idx = equiposContainer.children.length;
        const div = document.createElement('div');
        div.classList.add('equipo-card');
        // Añadimos un data-id para identificar cada equipo (puede ser el índice)
        div.dataset.id = idx; 
        div.innerHTML = `
            <label>Nombre del Equipo:</label>
            <input type="text" name="equipos[${idx}][nombre]" class="form-control equipo-nombre" required placeholder="Ejemplo: Torre laparoscopía...">

            <label class="mt-2">Cantidad:</label>
            <input type="number" name="equipos[${idx}][cantidad]" class="form-control" min="1" value="1" required>

            <button type="button" class="btn btn-danger btn-sm mt-3 btn-remove">Eliminar</button>
        `;
        equiposContainer.appendChild(div);

        div.querySelector('.btn-remove').addEventListener('click', () => {
            div.remove();
            actualizarSelectsEquipos();
        });

        div.querySelector('input.equipo-nombre').addEventListener('input', actualizarSelectsEquipos);

        actualizarSelectsEquipos();
    }

    function agregarComponente() {
        const idx = componentesContainer.children.length;
        const div = document.createElement('div');
        div.classList.add('componente-card');
        div.innerHTML = `
            <label>Nombre del Componente:</label>
            <input type="text" name="componentes[${idx}][nombre]" class="form-control" required placeholder="Ejemplo: Cable, Batería, Broca...">

            <label class="mt-2">Equipo Relacionado:</label>
            <select name="componentes[${idx}][equipo_id]" class="form-control componente-equipo">
                <option value="">-- Selecciona un equipo --</option>
            </select>

            <label class="mt-2">Cantidad Esperada:</label>
            <input type="number" name="componentes[${idx}][cantidad_esperada]" class="form-control" min="0" value="1" required>

            <button type="button" class="btn btn-danger btn-sm mt-3 btn-remove">Eliminar</button>
        `;
        componentesContainer.appendChild(div);

        div.querySelector('.btn-remove').addEventListener('click', () => div.remove());

        actualizarSelectsEquipos();
    }

    function actualizarSelectsEquipos() {
        // Obtenemos equipos con id (dataset.id) y nombre
        const equipos = Array.from(equiposContainer.querySelectorAll('.equipo-card'))
            .map(div => {
                const nombreInput = div.querySelector('input.equipo-nombre');
                return {
                    id: div.dataset.id,
                    nombre: nombreInput ? nombreInput.value.trim() : ''
                };
            })
            .filter(equipo => equipo.nombre.length > 0);

        // Actualizamos selects de componentes con id y nombre
        const selects = document.querySelectorAll('.componente-equipo');
        selects.forEach(select => {
            const selected = select.value;
            select.innerHTML = `<option value="">-- Selecciona un equipo --</option>`;
            equipos.forEach(eq => {
                const option = document.createElement('option');
                option.value = eq.id;  // ID para enviar al backend
                option.textContent = eq.nombre;
                if (eq.id === selected) option.selected = true;
                select.appendChild(option);
            });
        });
    }

    // Inicial
    agregarEquipo();
    agregarComponente();
</script>

@endsection
