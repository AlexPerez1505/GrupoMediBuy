
@extends('layouts.app')

@section('title', 'Préstamos')
@section('titulo', 'Préstamos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}?v={{ time() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logoai.png') }}?v=1">
<body>

    <div class="container">
 <!-- Contenedor de la foto y el nombre -->
<div class="profile-container" style="display: flex; align-items: center; gap: 15px;">
    <!-- Foto de perfil -->
    <div class="profile-picture" style="position: relative; display: inline-block; cursor: pointer;">
        <img src="{{ Auth::user()->imagen ? asset('storage/' . Auth::user()->imagen) : asset('images/default-profile.png') }}" 
             alt="Foto de perfil" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
        
        <!-- Input de archivo oculto -->
        <form action="{{ route('perfil.updatePhoto') }}" method="POST" enctype="multipart/form-data" id="photo-form">
            @csrf
            <input type="file" name="imagen" id="file-input" style="display: none;" accept="image/*" onchange="document.getElementById('photo-form').submit();">
        </form>
    </div>

    <!-- Nombre del usuario -->
    <div class="user-name" style="font-weight: bold; font-size: 18px; display: flex; flex-direction: column;">
        <span>{{ Auth::user()->name }}</span>
    </div>
</div>
<div class="info"><strong>Número de usuario:</strong> {{ Auth::user()->nomina ?? 'No registrado' }}</div>

    <!-- Teléfono -->
<div id="info-phone" class="info">
    <strong>Teléfono:</strong> {{ Auth::user()->phone ?? 'No registrado' }} 
    <button class="btn-edit" onclick="toggleEditPhone()">
        <img src="/images/boligrafo.png" alt="Editar">
    </button>
</div>
<div id="edit-phone" class="modify-section hidden">
    <form action="{{ route('perfil.update') }}" method="POST" class="input-container">
        @csrf
        <div class="input-wrapper">
            <input type="text" name="phone" id="new-phone" class="input-field" placeholder="Introduce el nuevo teléfono">
            <button type="submit" class="btn-save">
                <img src="/images/usuario.png" alt="Guardar">
            </button>
        </div>
    </form>
</div>

<!-- Correo -->
<div id="info-email" class="info">
    <strong>Correo:</strong> {{ Auth::user()->email }} 
    <button class="btn-edit" onclick="toggleEditEmail()">
        <img src="/images/boligrafo.png" alt="Editar">
    </button>
</div>
<div id="edit-email" class="modify-section hidden">
    <form action="{{ route('perfil.update') }}" method="POST" class="input-container">
        @csrf
        <div class="input-wrapper">
            <input type="email" name="email" id="new-email" class="input-field" placeholder="Introduce el nuevo correo">
            <button type="submit" class="btn-save">
                <img src="/images/usuario.png" alt="Guardar">
            </button>
        </div>
    </form>
</div>

    <div class="info"><strong>Cargo:</strong> {{ Auth::user()->cargo ?? 'No registrado' }}</div>
    <div class="info"><strong>Puesto:</strong> {{ Auth::user()->puesto ?? 'No registrado' }}</div>
    <div class="info"><strong>Vacaciones Disponibles:</strong> {{ Auth::user()->vacaciones_disponibles ?? '0' }} días</div>
    <div class="info"><strong>Vacaciones Utilizadas:</strong> {{ Auth::user()->vacaciones_utilizadas ?? '0' }} días</div>
    <div class="info"><strong>Permisos Disponibles:</strong> {{ Auth::user()->permisos ?? '0' }}</div>

<!-- Contenedor para centrar los botones uno al lado del otro -->
<div class="password-container" style="display: flex; gap: 1rem; justify-content: center;">
    <a href="{{ route('auth.change-password') }}">
        <button class="btn-modify">Cambiar Contraseña</button>
    </a>
    <a href="{{ route('mi-historial') }}">
        <button class="btn-modify">Ver Historial</button>
    </a>
</div>
    </div>
    <script>
        function toggleEditPhone() {
            const phoneSection = document.getElementById('edit-phone');
            phoneSection.classList.toggle('hidden');
        }

        function toggleEditEmail() {
            const emailSection = document.getElementById('edit-email');
            emailSection.classList.toggle('hidden');
        }

        function savePhone() {
            const newPhone = document.getElementById('new-phone').value;
            if (newPhone) {
                document.getElementById('info-phone').innerHTML = `<strong>Teléfono:</strong> ${newPhone} <button class="btn-edit" onclick="toggleEditPhone()"><img src="/images/boligrafo.png" alt="Editar"></button>`;
                document.getElementById('edit-phone').classList.add('hidden');
            }
        }

        function saveEmail() {
            const newEmail = document.getElementById('new-email').value;
            if (newEmail) {
                document.getElementById('info-email').innerHTML = `<strong>Correo:</strong> ${newEmail} <button class="btn-edit" onclick="toggleEditEmail()"><img src="/images/boligrafo.png" alt="Editar"></button>`;
                document.getElementById('edit-email').classList.add('hidden');
            }
        }
    </script>
</body>
<script>
    document.querySelector('.profile-picture').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });
</script>
@endsection
