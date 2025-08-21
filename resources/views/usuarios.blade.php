@extends('layouts.app')
@section('title', 'Usuarios')
@section('titulo', 'Usuarios')
@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}?v={{ time() }}">
<body>
    <div class="container">
    <div class="users-container">
        @foreach ($usuarios as $user)
        <div class="profile-container" onclick="toggleUserInfo(this)">
    <div class="profile-picture">
        <img src="{{ $user->imagen ? asset('storage/' . $user->imagen) : asset('images/default-profile.png') }}" 
            alt="Foto de perfil">
    </div>
    <div class="user-name"><strong>{{ $user->name }}</strong></div>

    <!-- Información visible inicialmente -->
    <div class="basic-info"><strong>Número de Usuario:</strong> {{ $user->nomina ?? 'No registrado' }}</div>
    <div class="basic-info"><strong>Teléfono:</strong> {{ $user->phone ?? 'No registrado' }}</div>
    <div class="basic-info"><strong>Puesto:</strong> {{ $user->puesto }}</div>

    <!-- Información oculta por defecto -->
    <div class="extra-info">
        <div class="info"><strong>Número de Usuario:</strong><span> {{ $user->nomina ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Teléfono:</strong> <span>{{ $user->phone ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Correo:</strong> <span>{{ $user->email }}</span></div>
        <div class="info"><strong>Cargo:</strong> <span>{{ $user->cargo ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Puesto:</strong> <span>{{ $user->puesto ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Vacaciones Disponibles:</strong> <span>{{ $user->vacaciones_disponibles ?? '0' }} días</span></div>
        <div class="info"><strong>Vacaciones Utilizadas:</strong> <span>{{ $user->vacaciones_utilizadas ?? '0' }} días</span></div>
        <div class="info"><strong>Permisos disponibles:</strong> <span>{{ $user->permisos ?? '0' }} días</span></div>
        <div class="info"><strong>Permisos Utilizados:</strong> <span>{{ $user->permisos_utilizados ?? '0' }} días</span></div>
        <div class="info"><strong>Faltas:</strong> <span>{{ $user->faltas ?? '0' }}</span></div>
        <div class="info"><strong>Asistencias:</strong> <span>{{ $user->asistencias ?? '0' }}</span></div>
        <div class="info"><strong>Retardos:</strong> <span>{{ $user->retardos ?? '0' }}</span></div>
        <div class="info"><strong>CURP:</strong> <span>{{ $user->curp ?? 'No registrado' }}</span></div>
        <div class="info"><strong>INE:</strong> <span>{{ $user->ine ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Licencia:</strong> <span>{{ $user->licencia ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Acta de Nacimiento:</strong> <span>{{ $user->acta_de_nacimiento ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Domicilio:</strong> <span>{{ $user->domicilio ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Fecha de Ingreso:</strong> <span>{{ $user->fecha_ingreso ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Contacto de Emergencia:</strong> <span>{{ $user->nombre_contacto_emergencia ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Número:</strong> <span>{{ $user->numero_contacto_emergencia ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Domicilio:</strong> <span>{{ $user->domicilio_contacto_emergencia ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Contacto de Emergencia Secundario:</strong> <span>{{ $user->nombre_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Número:</strong> <span>{{ $user->numero_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
        <div class="info"><strong>Domicilio:</strong> <span>{{ $user->domicilio_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
    </div>
</div>        
        @endforeach
    </div>
</div>
</body>
<script>
function toggleUserInfo(element) {
    const usersContainer = document.querySelector('.users-container');

    // Si ya está expandido, lo cerramos
    if (element.classList.contains('expanded')) {
        element.classList.remove('expanded');
        usersContainer.classList.remove('single-view');

        // Mostrar de nuevo la información básica
        element.querySelectorAll('.basic-info').forEach(info => {
            info.style.display = 'block';
        });

    } else {
        // Cerrar todos los usuarios antes de abrir el seleccionado
        document.querySelectorAll('.profile-container').forEach(container => {
            container.classList.remove('expanded');
            container.querySelectorAll('.basic-info').forEach(info => {
                info.style.display = 'block'; // Mostrar la info básica de los demás
            });
        });

        // Expandir el usuario seleccionado
        element.classList.add('expanded');
        usersContainer.classList.add('single-view');

        // Ocultar su información básica
        element.querySelectorAll('.basic-info').forEach(info => {
            info.style.display = 'none';
        });
    }
}
</script>
@endsection