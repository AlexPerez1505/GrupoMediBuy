@extends('layouts.app')
@section('title', 'Mis Solicitudes')
@section('titulo', 'Mis Solicitudes')
@section('content')
@php
    use Carbon\Carbon;
@endphp

<head>
    <link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
</head>
<div class="form-contenedor">
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
            <th class="p-2 border">Fecha Inicio</th>
            <th class="p-2 border">Fecha Fin</th>
            <th class="p-2 border">Días Solicitados</th> <!-- Nueva columna -->
            <th class="p-2 border">Estatus</th>
            <th class="p-2 border">Comentarios</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($solicitudes as $solicitud)
            <tr class="text-center">
                <td class="p-2 border">{{ $solicitud->fecha_inicio }}</td>
                <td class="p-2 border">{{ $solicitud->fecha_fin }}</td>
                <td class="p-2 border">
                    @php
                        $inicio = Carbon::parse($solicitud->fecha_inicio);
                        $fin = Carbon::parse($solicitud->fecha_fin);
                        $dias = $inicio->diffInDays($fin) + 1; // Se suma 1 para incluir el día de inicio
                    @endphp
                    {{ $dias }}
                </td>
                <td class="p-2 border">{{ $solicitud->estatus }}</td>
                <td class="p-2 border">
                    @if($solicitud->comentario)
                        {{ $solicitud->comentario }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection
