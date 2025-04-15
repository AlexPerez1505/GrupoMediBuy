@extends('layouts.app')
@section('title', 'Vacaciones')
@section('titulo', 'Solicitudes')
@section('content')
<head>
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
</head>
<div class="form-contenedor">
<style>
    /* Estilos generales */


/* Contenedor principal */


/* Mensajes de éxito y error */
.bg-green-200, .bg-red-200 {
    padding: 12px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
    animation: fadeIn 0.5s ease-in-out;
}
.bg-green-200 {
    background: #d1e7dd;
    color: #155724;
}
.bg-red-200 {
    background: #f8d7da;
    color: #721c24;
}

/* Tabla de solicitudes */
/* Tabla responsiva */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

thead {
    background: #7cb8eb;
    color: white;
    text-transform: uppercase;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

tr:last-child td {
    border-bottom: none;
}
tbody tr:hover {
    background: #f1f1f1;
    transition: background 0.3s ease-in-out;
}

/* Botón de acción */
a.px-4.py-2 {
    display: inline-block;
    padding: 8px 16px;
    background: #007bff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease-in-out;
}
a.px-4.py-2:hover {
    background: #0056b3;
    transform: translateY(-2px);
}
/* Permite desplazamiento en dispositivos pequeños */
@media screen and (max-width: 768px) {
    .form-contenedor {
        max-width: 100%;
        padding: 10px;
    }

    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    th, td {
        padding: 8px;
        font-size: 14px;
    }
}
/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
/* Botón animado */
.btn-animated {
    display: inline-block;
    padding: 10px 20px;
    background-color: rgb(187, 190, 196);
    color: white;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
}

.btn-animated::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300%;
    height: 300%;
    background: rgba(255, 255, 255, 0.2);
    transition: all 0.5s ease-in-out;
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
}

.btn-animated:hover::before {
    transform: translate(-50%, -50%) scale(1);
}

.btn-animated:hover {
    background-color: #2563EB;
    transform: translateY(-3px);
    box-shadow: 0px 10px 20px rgba(59, 130, 246, 0.3);
}

/* Ajuste del botón en pantallas pequeñas */
@media screen and (max-width: 768px) {
    .btn-animated {
        padding: 8px 12px;
        font-size: 14px;
    }
}

</style>


    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-2 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-200 text-red-800 p-2 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <table class="w-full mt-4 border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Empleado</th>
                <th class="p-2 border">Fecha Inicio</th>
                <th class="p-2 border">Fecha Fin</th>
                <th class="p-2 border">Estatus</th>
                <th class="p-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitudes as $solicitud)
                <tr class="text-center">
                    <td class="p-2 border">{{ $solicitud->user->name }}</td>
                    <td class="p-2 border">{{ $solicitud->fecha_inicio }}</td>
                    <td class="p-2 border">{{ $solicitud->fecha_fin }}</td>
                    <td class="p-2 border">{{ $solicitud->estatus }}</td>
                    <td class="p-2 border">
                    <a href="{{ route('vacaciones.ver', $solicitud->id) }}" class="btn-animated">
    Ver
</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
