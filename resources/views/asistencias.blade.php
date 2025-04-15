@extends('layouts.app')
@section('title', 'Asistencias')
@section('titulo', 'Asistencia')
@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
    <!-- Agregar Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Cargar jQuery primero si es necesario -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Cargar SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
    

    <!-- Mostrar errores específicos -->
    @if (session('error_asistencia'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error_asistencia') }}',
            });
        </script>
    @endif

    @if (session('error_permiso'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Permiso insuficiente',
                text: '{{ session('error_permiso') }}',
            });
        </script>
    @endif

    @if (session('error_vacaciones'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Vacaciones agotadas',
                text: '{{ session('error_vacaciones') }}',
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
            });
        </script>
    @endif


    <div class="form-container">
        <form action="{{ route('asistencias.store') }}" method="POST" class="asistencias-form">
            @csrf

            <div class="form-group">
                <label for="user_id" class="label_nomina">Empleado:</label>
                <div class="form-group">
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/nombre.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select class="form-control" name="user_id" style="background-color: #ffff; display:block; width: 100%;" required>
                            <option value="">Selecciona</option>
                            @foreach ($usuarios as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
    <label for="fecha" class="label_nomina">Fecha:</label>
    <input type="date" class="form-control select" name="fecha" required value="{{ \Carbon\Carbon::today()->toDateString() }}">
</div>

<div class="form-group">
    <label for="hora" class="label_nomina">Hora:</label>
    <input type="time" class="form-control select" name="hora" id="hora" required>
</div>
            <div class="form-group">
                <label for="estado" class="label_nomina">Estado:</label>
                <div class="form-group">
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/asistencia.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select class="form-control" name="estado" style="background-color: #ffff; display:block; width: 100%;" required>
                            <option value="">Selecciona</option>
                            <option value="asistencia">Asistencia</option>
                            <option value="falta">Falta</option>
                            <option value="permiso">Permiso</option>
                            <option value="vacaciones">Vacaciones</option>
                            <option value="retardo">Retardo</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-custom">Registrar</button>
        </form>
    </div>

    <!-- Incluir el JS de SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script>
    // Establecer la hora actual en el campo de hora al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        let now = new Date();
        let hours = now.getHours().toString().padStart(2, '0');
        let minutes = now.getMinutes().toString().padStart(2, '0');
        document.getElementById("hora").value = `${hours}:${minutes}`;
    });
</script>
</body>
@endsection
