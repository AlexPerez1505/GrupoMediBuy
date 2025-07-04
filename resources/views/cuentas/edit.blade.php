@extends('layouts.app')

@section('content')

<style>
    body {
        background-color: #f4f6f9;
    }
    .form-container {
        max-width: 500px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.6s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
        font-weight: 600;
        animation: slideDown 0.6s ease;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .form-label {
        font-weight: 500;
        color: #555;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #339af0;
        box-shadow: 0 0 5px rgba(51, 154, 240, 0.5);
        transform: scale(1.02);
    }
    .btn-guardar {
        background-color: #339af0;
        color: #fff;
        border: none;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-guardar:hover {
        background-color: #1c7ed6;
        transform: scale(1.02);
    }
    .error-text {
        color: #e03131;
        font-size: 0.9rem;
        margin-top: 5px;
    }
</style>

<div class="form-container">

    <h2>Editar Cuenta</h2>

    <form id="cuentaForm" action="{{ route('cuentas.update', $cuenta->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="lugar" class="form-label">Lugar</label>
            <input type="text" class="form-control" id="lugar" name="lugar" value="{{ old('lugar', $cuenta->lugar) }}" required>
            <div id="lugarError" class="error-text"></div>
        </div>

        <div class="mb-3">
            <label for="casetas" class="form-label">Casetas</label>
            <input type="number" step="0.01" class="form-control" id="casetas" name="casetas" value="{{ old('casetas', $cuenta->casetas) }}" required>
            <div id="casetasError" class="error-text"></div>
        </div>

        <div class="mb-3">
            <label for="gasolina" class="form-label">Gasolina</label>
            <input type="number" step="0.01" class="form-control" id="gasolina" name="gasolina" value="{{ old('gasolina', $cuenta->gasolina) }}" required>
            <div id="gasolinaError" class="error-text"></div>
        </div>

        <div class="mb-3">
            <label for="viaticos" class="form-label">Viáticos</label>
            <input type="number" step="0.01" class="form-control" id="viaticos" name="viaticos" value="{{ old('viaticos', $cuenta->viaticos) }}" required>
            <div id="viaticosError" class="error-text"></div>
        </div>

        <div class="mb-3">
            <label for="adicional" class="form-label">Adicional</label>
            <input type="number" step="0.01" class="form-control" id="adicional" name="adicional" value="{{ old('adicional', $cuenta->adicional) }}" min="0">
            <div id="adicionalError" class="error-text"></div>
        </div>

        <div class="mb-3" id="descripcionGroup" style="display: none;">
            <label for="descripcion" class="form-label">Descripción del Adicional</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $cuenta->descripcion) }}</textarea>
            <div id="descripcionError" class="error-text"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Total (con $500 extra)</label>
            <input type="text" class="form-control bg-light" id="total" name="total" readonly>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-guardar">Actualizar</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lugar = document.getElementById('lugar');
    const casetas = document.getElementById('casetas');
    const gasolina = document.getElementById('gasolina');
    const viaticos = document.getElementById('viaticos');
    const adicional = document.getElementById('adicional');
    const descripcion = document.getElementById('descripcion');
    const descripcionGroup = document.getElementById('descripcionGroup');
    const total = document.getElementById('total');
    const form = document.getElementById('cuentaForm');

    function calcularTotal() {
        const suma = (parseFloat(casetas.value) || 0) + 
                     (parseFloat(gasolina.value) || 0) + 
                     (parseFloat(viaticos.value) || 0) + 
                     (parseFloat(adicional.value) || 0) + 500;
        total.value = suma.toFixed(2);
    }

    function mostrarDescripcion() {
        if (parseFloat(adicional.value) > 0) {
            descripcionGroup.style.display = 'block';
        } else {
            descripcionGroup.style.display = 'none';
            descripcion.value = '';
            document.getElementById('descripcionError').textContent = '';
        }
    }

    function validarInput(input, errorId, mensaje) {
        const errorDiv = document.getElementById(errorId);
        if (input.value.trim() === '' || (input.type === 'number' && (isNaN(parseFloat(input.value)) || parseFloat(input.value) < 0))) {
            errorDiv.textContent = mensaje;
            return false;
        } else {
            errorDiv.textContent = '';
            return true;
        }
    }

    function validarDescripcion() {
        const errorDiv = document.getElementById('descripcionError');
        if (descripcionGroup.style.display !== 'none' && descripcion.value.trim().length < 3) {
            errorDiv.textContent = 'Describe brevemente el adicional.';
            return false;
        } else {
            errorDiv.textContent = '';
            return true;
        }
    }

    function validarLugar() {
        const errorDiv = document.getElementById('lugarError');
        if (lugar.value.trim().length < 2) {
            errorDiv.textContent = 'Especifica un lugar válido.';
            return false;
        } else {
            errorDiv.textContent = '';
            return true;
        }
    }

    casetas.addEventListener('input', () => { calcularTotal(); validarInput(casetas, 'casetasError', 'Ingrese un valor válido.'); });
    gasolina.addEventListener('input', () => { calcularTotal(); validarInput(gasolina, 'gasolinaError', 'Ingrese un valor válido.'); });
    viaticos.addEventListener('input', () => { calcularTotal(); validarInput(viaticos, 'viaticosError', 'Ingrese un valor válido.'); });
    adicional.addEventListener('input', () => {
        calcularTotal();
        validarInput(adicional, 'adicionalError', 'Ingrese un valor válido.');
        mostrarDescripcion();
    });
    descripcion.addEventListener('input', validarDescripcion);
    lugar.addEventListener('input', validarLugar);

    // Inicialización
    calcularTotal();
    mostrarDescripcion();

    form.addEventListener('submit', function(e) {
        let valid = true;
        valid &= validarLugar();
        valid &= validarInput(casetas, 'casetasError', 'Ingrese un valor válido.');
        valid &= validarInput(gasolina, 'gasolinaError', 'Ingrese un valor válido.');
        valid &= validarInput(viaticos, 'viaticosError', 'Ingrese un valor válido.');
        valid &= validarInput(adicional, 'adicionalError', 'Ingrese un valor válido.');
        valid &= validarDescripcion();

        if (!valid) {
            e.preventDefault();
        }
    });
});
</script>

@endsection
