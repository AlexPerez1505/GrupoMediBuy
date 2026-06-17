@extends('layouts.app')
@section('title', 'Usuarios')
@section('titulo', 'Usuarios')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}?v={{ time() }}">

<style>
  .user-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 18px;
    gap: 10px;
  }

  .btn-delete-user {
    border: none;
    outline: none;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    padding: 12px 18px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: .3px;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(220, 38, 38, 0.25);
    transition: all .25s ease;
  }

  .btn-delete-user:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 14px 28px rgba(220, 38, 38, 0.35);
  }

  .btn-delete-user:active {
    transform: scale(.98);
  }

  .delete-form {
    margin: 0;
  }

  .profile-container .user-actions {
    display: none;
  }

  .profile-container.expanded .user-actions {
    display: flex;
  }

  .alert-success-custom {
    max-width: 1200px;
    margin: 0 auto 20px auto;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    padding: 14px 18px;
    border-radius: 14px;
    font-weight: 600;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.08);
  }
</style>

@if(session('success'))
  <div class="alert-success-custom">
    {{ session('success') }}
  </div>
@endif

<div class="users-page">
  <div class="users-container">
    @foreach ($usuarios as $user)
      <div class="profile-container" onclick="toggleUserInfo(this)">
        <div class="profile-picture">
          <img
            src="{{ $user->imagen ? asset('storage/' . $user->imagen) : asset('images/default-profile.png') }}"
            alt="Foto de perfil"
          >
        </div>

        <div class="user-name"><strong>{{ $user->name }}</strong></div>

        <div class="basic-info"><strong>Número de Usuario:</strong> {{ $user->nomina ?? 'No registrado' }}</div>
        <div class="basic-info"><strong>Teléfono:</strong> {{ $user->phone ?? 'No registrado' }}</div>
        <div class="basic-info"><strong>Puesto:</strong> {{ $user->puesto }}</div>

        <div class="extra-info">
          <div class="info"><strong>Número de Usuario:</strong><span>{{ $user->nomina ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Teléfono:</strong><span>{{ $user->phone ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Correo:</strong><span>{{ $user->email }}</span></div>
          <div class="info"><strong>Cargo:</strong><span>{{ $user->cargo ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Puesto:</strong><span>{{ $user->puesto ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Vacaciones Disponibles:</strong><span>{{ $user->vacaciones_disponibles ?? '0' }} días</span></div>
          <div class="info"><strong>Vacaciones Utilizadas:</strong><span>{{ $user->vacaciones_utilizadas ?? '0' }} días</span></div>
          <div class="info"><strong>Permisos disponibles:</strong><span>{{ $user->permisos ?? '0' }} días</span></div>
          <div class="info"><strong>Permisos Utilizados:</strong><span>{{ $user->permisos_utilizados ?? '0' }} días</span></div>
          <div class="info"><strong>Faltas:</strong><span>{{ $user->faltas ?? '0' }}</span></div>
          <div class="info"><strong>Asistencias:</strong><span>{{ $user->asistencias ?? '0' }}</span></div>
          <div class="info"><strong>Retardos:</strong><span>{{ $user->retardos ?? '0' }}</span></div>
          <div class="info"><strong>CURP:</strong><span>{{ $user->curp ?? 'No registrado' }}</span></div>
          <div class="info"><strong>INE:</strong><span>{{ $user->ine ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Licencia:</strong><span>{{ $user->licencia ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Acta de Nacimiento:</strong><span>{{ $user->acta_de_nacimiento ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Domicilio:</strong><span>{{ $user->domicilio ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Fecha de Ingreso:</strong><span>{{ $user->fecha_ingreso ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Contacto de Emergencia:</strong><span>{{ $user->nombre_contacto_emergencia ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Número:</strong><span>{{ $user->numero_contacto_emergencia ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Domicilio:</strong><span>{{ $user->domicilio_contacto_emergencia ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Contacto de Emergencia Secundario:</strong><span>{{ $user->nombre_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Número:</strong><span>{{ $user->numero_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
          <div class="info"><strong>Domicilio:</strong><span>{{ $user->domicilio_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>

          <div class="user-actions" onclick="event.stopPropagation()">
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form" onsubmit="return confirmDelete('{{ $user->name }}')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-delete-user">
                Eliminar usuario
              </button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<script>
  function toggleUserInfo(element) {
    const usersContainer = document.querySelector('.users-container');

    if (element.classList.contains('expanded')) {
      element.classList.remove('expanded');
      usersContainer.classList.remove('single-view');
      element.querySelectorAll('.basic-info').forEach(info => info.style.display = 'block');
      return;
    }

    document.querySelectorAll('.profile-container').forEach(container => {
      container.classList.remove('expanded');
      container.querySelectorAll('.basic-info').forEach(info => info.style.display = 'block');
    });

    element.classList.add('expanded');
    usersContainer.classList.add('single-view');

    element.querySelectorAll('.basic-info').forEach(info => info.style.display = 'none');
  }

  function confirmDelete(userName) {
    return confirm(`¿Estás seguro de que deseas eliminar a ${userName}? Esta acción no se puede deshacer.`);
  }
</script>
@endsection