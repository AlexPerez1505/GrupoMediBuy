@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nueva Firma</h1>
    <form action="{{ route('checklist-firmas.store') }}" method="POST">
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
            <label>Usuario</label>
            <select name="user_id" class="form-control" required>
                <option value="">Seleccione usuario</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Rol</label>
            <select name="rol" class="form-control" required>
                <option value="responsable">Responsable</option>
                <option value="supervisor">Supervisor</option>
                <option value="entregador">Entregador</option>
                <option value="receptor">Receptor</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Firma (puede ser texto o base64 de imagen)</label>
            <input type="text" name="firma" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Fecha de Firma</label>
            <input type="datetime-local" name="fecha_firma" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('checklist-firmas.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
