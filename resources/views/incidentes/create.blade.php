@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Incidente</h1>
    <form action="{{ route('incidentes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Checklist</label>
            <select name="checklist_id" class="form-control" required>
                <option value="">Seleccione checklist</option>
                @foreach($checklists as $checklist)
                    <option value="{{ $checklist->id }}">#{{ $checklist->id }} ({{ $checklist->item->nombre }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Parte (opcional)</label>
            <select name="item_parte_id" class="form-control">
                <option value="">--</option>
                @foreach($itemPartes as $parte)
                    <option value="{{ $parte->id }}">{{ $parte->nombre_parte }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Usuario</label>
            <select name="user_id" class="form-control" required>
                <option value="">Seleccione usuario</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-control">
                <option value="daño">Daño</option>
                <option value="falta">Falta</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
