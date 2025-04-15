@extends('layouts.app')
@section('title', 'Requisiciones')
@section('titulo', 'Solicitudes')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">

<div class="formulario-contenedor">
    <h2>Solicitudes Pendientes / En Planta</h2>

    {{-- Contenedor donde se cargarán las solicitudes dinámicamente --}}
    <div id="contenedor-solicitudes">
        @include('partials.listado', ['solicitudes' => $solicitudes])
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function cargarSolicitudes() {
        $.ajax({
            url: '{{ route('solicitudes.listado') }}',
            type: 'GET',
            success: function(data) {
                $('#contenedor-solicitudes').html(data);
            },
            error: function() {
                console.log('Error al cargar las solicitudes');
            }
        });
    }

    // Cargar solicitudes al inicio
    $(document).ready(function() {
        // Actualiza cada 1 segundo
        setInterval(cargarSolicitudes, 20000); // 1000 ms = 1 segundo
    });
</script>

@endsection
