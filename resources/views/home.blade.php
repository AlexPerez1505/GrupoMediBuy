<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio | Grupo MediBuy</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      font-family: 'Inter', sans-serif;
      height: 100%;
      overflow: hidden;
      position: relative;
    }

    video.background-video {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
    }

    .overlay {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
    }

    .main-container {
      position: relative;
      z-index: 2;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      padding: 1rem;
    }

    h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    h1 span {
      color: #facc15;
    }

    .subtext {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      opacity: 0.85;
    }

    .button-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1rem;
      width: 100%;
      max-width: 800px;
      margin-bottom: 2rem;
    }

    .button-grid button {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid #facc15;
      padding: 0.8rem;
      color: #fff;
      font-weight: 600;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .button-grid button:hover {
      background-color: #facc15;
      color: #000;
    }

    .stats-container {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background-color: rgba(255, 255, 255, 0.1);
      padding: 1rem;
      border-radius: 12px;
      text-align: left;
      min-width: 180px;
      color: white;
      border-left: 4px solid #facc15;
    }

    .notifications {
      background-color: rgba(255, 255, 255, 0.08);
      padding: 1rem;
      border-radius: 12px;
      width: 100%;
      max-width: 800px;
      color: white;
      font-size: 0.95rem;
      border-left: 4px solid #3b82f6;
    }

    @media (max-width: 768px) {
      h1 {
        font-size: 2rem;
      }

      .subtext {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <video class="background-video" autoplay muted loop>
    <source src="{{ asset('video/fondo.mp4') }}" type="video/mp4">
    Tu navegador no soporta videos HTML5.
  </video>

  <div class="overlay"></div>

  <div class="main-container">
    <h1>Bienvenido a <span>Grupo MediBuy</span></h1>
    <p class="subtext">Innovación para el cuidado de tus pacientes</p>

    <div class="button-grid">
      <button onclick="window.location.href='{{ url('/inventario') }}'"><i class="fas fa-box"></i> Inventario</button>
      <button onclick="window.location.href='{{ url('/remisiones') }}'"><i class="fas fa-file-invoice"></i> Remisiones</button>
      <button onclick="window.location.href='{{ url('/agenda') }}'"><i class="fas fa-calendar-alt"></i> Agenda</button>
      <button onclick="window.location.href='{{ url('/perfil') }}'"><i class="fas fa-user-circle"></i> Perfil</button>
      @if(Auth::user()->hasRole('admin'))
      <button onclick="window.location.href='{{ url('/usuarios') }}'"><i class="fas fa-users"></i> Usuarios</button>
      <button onclick="window.location.href='{{ route('asistencias.index') }}'"><i class="fas fa-clipboard-check"></i> Asistencias</button>
      @endif
    </div>

    <div class="stats-container">
      <div class="stat-card">
        <strong>Inventario</strong><br>
        1,250 productos
      </div>
      <div class="stat-card">
        <strong>Remisiones activas</strong><br>
        47 en proceso
      </div>
      <div class="stat-card">
        <strong>Agenda</strong><br>
        12 citas hoy
      </div>
    </div>

    <div class="notifications">
      <strong>Notificación:</strong> Se ha actualizado el sistema de inventario. Revisa los nuevos filtros avanzados.
    </div>
  </div>
</body>
</html>
