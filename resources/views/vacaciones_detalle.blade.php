@extends('layouts.app')
@section('title', 'Vacaciones')
@section('titulo', 'Detalles de Solicitud')
@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
    <style>
   
        h2 {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .info p {
            font-size: 16px;
            margin: 5px 0;
        }
        .status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 5px;
    color: white;
}

.status.aprobado {
    background: green;
}

.status.pendiente {
    background: orange;
}

.status.rechazado {
    background: red;
}

  
 
        .approve {
            background: #28a745;
        }
        .reject {
            background: #dc3545;
        }
        .reject-form {
            display: none;
            margin-top: 15px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
    </style>
</head>
<div class="form-contenedor">

    
    <div class="info">
        <p><strong>Empleado:</strong> {{ $solicitud->user->name }}</p>
        <p><strong>Puesto:</strong> {{ $solicitud->user->puesto ?? 'No registrado' }}</p>
        <p><strong>Fecha de Inicio:</strong> {{ $solicitud->fecha_inicio }}</p>
        <p><strong>Fecha de Fin:</strong> {{ $solicitud->fecha_fin }}</p>
        <p><strong>Estatus:</strong> <span class="status {{ $solicitud->estatus == 'Aprobado' ? 'aprobado' : 'pendiente' }}">{{ $solicitud->estatus }}</span></p>
        <p><strong>Vacaciones Disponibles:</strong> {{ $solicitud->user->vacaciones_disponibles }} días</p>
        <p><strong>Vacaciones Utilizadas:</strong> {{ $solicitud->user->vacaciones_utilizadas }} días</p>
    </div>
    <div class="buttons d-flex justify-content-center align-items-center"  style=" margin-top: 30px;">
    <form action="{{ route('vacaciones.aprobar', $solicitud->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary approve">Aprobar</button>
    </form>
    <button onclick="toggleRejectionForm()" class="btn btn-primary reject ms-2">Rechazar</button>
</div>

    <form id="rejectionForm" action="{{ route('vacaciones.rechazar', $solicitud->id) }}" method="POST" class="reject-form">
        @csrf
        <label><strong>Razón de Rechazo:</strong></label>
        <textarea name="comentario" rows="3" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
        <button type="submit" class="btn btn-primary reject" style="width: 25%; margin-top: 10px;">Confirmar Rechazo</button>
    </form>
</div>

<script>
    function toggleRejectionForm() {
        var form = document.getElementById('rejectionForm');
        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    }
</script>
@endsection
