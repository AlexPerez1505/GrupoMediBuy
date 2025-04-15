@extends('layouts.app')
@section('title', 'Vacaciones')
@section('titulo', 'Solicitud de Vacaciones')
@section('content')
<head>
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
</head>
<div class="form-contenedor">
    
<!-- Información del usuario -->
<div class="info-box">
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Vacaciones Disponibles</strong>
                    <span>{{ $user->vacaciones_disponibles ?? '0' }} días</span>
                </div>
            </div>

            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Vacaciones Utilizadas</strong>
                    <span>{{ $user->vacaciones_utilizadas ?? '0' }} días</span>
                </div>
            </div>

            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Permisos Disponibles</strong>
                    <span>{{ $user->permisos ?? '0' }} días</span>
                </div>
            </div>

            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Permisos Utilizados</strong>
                    <span>{{ $user->permisos_utilizados ?? '0' }} días</span>
                </div>
            </div>

            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Faltas</strong>
                    <span>{{ $user->faltas ?? '0' }}</span>
                </div>
            </div>

            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Asistencias</strong>
                    <span>{{ $user->asistencias ?? '0' }}</span>
                </div>
            </div>

            <div class="col-6 col-md-4 mb-3">
                <div class="info-card">
                    <strong>Retardos</strong>
                    <span>{{ $user->retardos ?? '0' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Mensajes de éxito o error -->
 @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
<!-- Formulario de solicitud -->
<form action="{{ route('vacaciones.solicitar') }}" method="POST">
    @csrf

    <!-- Fechas en la misma fila en pantallas grandes -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                <input type="date" class="form-control" name="fecha_inicio" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="fecha_fin" class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control" required>
            </div>
        </div>
    </div>

    <!-- Tipo de Permiso y Justificación en la misma fila en pantallas grandes -->
    <div class="row">
        <div class="col-md-6">
        <div class="form-group">
        <label for="role" class="label_nomina">Tipo de Permiso:</label>
        <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/visa.png') }}" alt="Acceso" class="icon2">
                            </div>
            <select  class="form-control" name="tipo_permiso" style="background-color: #ffff; display:block; width: 100%;" >
            <option value="">Selecciona</option>
                <option value="Normal">Normal</option>
                <option value="Especial">Especial</option>
            </select>
                </div>
            </div>
        </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="justificacion" class="form-label">Justificación (Opcional):</label>
                <textarea class="form-control" name="justificacion" placeholder="Escribe aquí una descripción detallada" style="height: 130px;"></textarea>
            </div>
        </div>
    </div>

   

    <button type="submit" class="btn btn-primary btn-custom mt-3">Enviar Solicitud</button>
</form>

@endsection
