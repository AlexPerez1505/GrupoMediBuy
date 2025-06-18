@extends('layouts.app')
@section('title', 'Seguimiento')
@section('titulo', 'Seguimiento')
@section('content')
<style>
    body {
        background-color: #f0f4f8;
        font-family: 'Inter', sans-serif;
    }

    .container {
        max-width: 880px;
        margin: 3rem auto;
        background-color: #fff;
        padding: 2.5rem 2rem;
        border-radius: 1rem;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
    }

    h1, h3 {
        color: #9d174d;
        font-weight: 700;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    h1{
        font-size: 30px;
    }

    .btn-success {
        background: linear-gradient(90deg, #34d399, #10b981);
        border: none;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 0.6rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(52, 211, 153, 0.3);
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #059669, #34d399);
        transform: translateY(-1px);
    }

    .btn-primary {
        background-color: #389989;
        border: none;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #6d28d9;
    }

    .btn-danger {
        background-color: #ef4444;
        border: none;
        font-weight: 600;
    }

    .btn-danger:hover {
        background-color: #dc2626;
    }

    .list-group-item {
        background-color: #e8f5e9;
        border: none;
        border-left: 5px solid #389989;
        margin-bottom: 1rem;
        border-radius: 0.6rem;
        padding: 1rem 1.25rem;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
    }

    .list-group-item strong {
        color: #389989;
    }

    .list-group-item p {
        margin: 0.5rem 0 0;
        color: #374151;
    }

    .modal-content {
        border-radius: 1rem;
    }

    .modal-title {
        font-weight: 600;
        color: #389989;
    }

    label {
        font-weight: 600;
        color: #6b7280;
    }

    input.form-control, textarea.form-control {
        border-radius: 0.6rem;
        background-color: #f0f4f8;
        border: 1px solid #f3e8ff;
    }

    input.form-control:focus, textarea.form-control:focus {
        border-color: #f472b6;
        box-shadow: 0 0 0 0.2rem rgba(252, 231, 243, 0.6);
    }
</style>
<div class="container" style="margin-top:110px;">
    <h1>Seguimientos para {{ $cliente->nombre }} {{ $cliente->apellido }}</h1>

    <button type="button" class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#modalSeguimiento">
        + Agregar Seguimiento
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-labelledby="modalSeguimientoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('seguimientos.store', $cliente->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSeguimientoLabel">Nuevo Seguimiento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <hr>
    <h3>Historial de Seguimientos</h3>
    <ul class="list-group">
        @forelse($seguimientos as $seguimiento)
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($seguimiento->fecha)->translatedFormat('d \d\e F \d\e Y') }}
                        </small><br>
                        <strong>{{ $seguimiento->titulo }}</strong>
                        <p>{{ $seguimiento->descripcion }}</p>
                        @if($seguimiento->completado)
                            <span class="badge bg-success">Completado</span>
                        @endif
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <!-- Botón Completar (si aún no está completado) -->
                        @if(is_null($seguimiento->completado))
                            <form action="{{ route('seguimientos.completar', $seguimiento->id) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary" onclick="return confirm('¿Marcar como completado?')">Completar</button>
                            </form>
                        @endif

                        <!-- Botón Eliminar -->
                        <form action="{{ route('seguimientos.destroy', $seguimiento->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este seguimiento?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </li>
        @empty
            <p class="text-muted">No hay seguimientos registrados.</p>
        @endforelse
    </ul>
</div>

@endsection
