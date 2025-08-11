<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    @import url("https://fonts.googleapis.com/css?family=Poppins:100,300,400,500,600,700,800,900&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background-color: transparent;
      margin: 0;
      padding: 0;
    }

    .animated-background {
      position: fixed;
      width: 100%;
      height: 100vh;
      top: 0;
      left: 0;
      overflow: hidden;
      z-index: 0;
      pointer-events: none;
    }

    .wave {
      position: absolute;
      left: 0;
      width: 100%;
      height: 100%;
      background:rgb(183, 194, 230);
      box-shadow: inset 0 0 50px rgba(90, 250, 250, 0.5);
    }

    .wave span {
      position: absolute;
      width: 325vh;
      height: 325vh;
      top: 0;
      left: 50%;
      transform: translate(-50%, -75%);
      background: #000;
    }

    .wave span:nth-child(1) {
      border-radius: 45%;
      background: rgb(255, 255, 255);
      animation: animate 5s linear infinite;
    }

    .wave span:nth-child(2) {
      border-radius: 40%;
      background: rgba(20, 20, 20, 0.5);
      animation: animate 10s linear infinite;
    }

    .wave span:nth-child(3) {
      border-radius: 42.5%;
      background: rgba(20, 20, 20, 0.3);
      animation: animate 15s linear infinite;
    }

    @keyframes animate {
      0% { transform: translate(-50%, -75%) rotate(0deg); }
      100% { transform: translate(-50%, -75%) rotate(360deg); }
    }

    body > *:not(.animated-background) {
      position: relative;
      z-index: 1;
    }

    header {
      position: fixed;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      z-index: 1000 !important; /* Asegura que esté encima de otros elementos */
      background-color: #f2f2f2;
      padding: 16px 20px !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      display: flex !important;
      justify-content: space-between !important;
      align-items: center;
    }


    header img {
      height: 40px;
    }

    .buscador {
      max-width: 500px;
      margin: 20px auto;
      padding: 0 20px;
    }

    .buscador input {
      width: 100%;
      padding: 10px 14px;
      border-radius: 12px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    h3 {
      margin: 20px 20px 10px;
      color: #fff;
      text-shadow: 1px 1px 2px #000;
    }

      .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 16px;
      padding: 0 20px 40px;
      max-width: 1000px;
      margin: 0 auto;
    }

    .menu-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      padding: 20px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      text-decoration: none;
      color: #333;
      position: relative;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.5s ease forwards;
    }
    .menu-card:hover {
      transform: translateY(-8px) scale(1.04);
      box-shadow:
        0 8px 20px rgba(0, 0, 0, 0.15),
        0 0 12px rgba(0, 123, 255, 0.3); /* Glow azul */
      border: 1px solid rgba(0, 123, 255, 0.2);
      background-color: #f9fbff;
    }
    .menu-card:hover i {
      color: #007bff;
      transform: scale(1.1);
      transition: color 0.3s ease, transform 0.3s ease;
    }
    .menu-card i {
      font-size: 28px;
      margin-bottom: 10px;
      color: #2a2a2a;
    }

    .menu-card p {
      margin: 0;
      font-size: 14px;
      font-weight: 600;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Animaciones escalonadas hasta 24 elementos */
    .menu-card:nth-child(1) { animation-delay: 0s; }
    .menu-card:nth-child(2) { animation-delay: 0.1s; }
    .menu-card:nth-child(3) { animation-delay: 0.2s; }
    .menu-card:nth-child(4) { animation-delay: 0.3s; }
    .menu-card:nth-child(5) { animation-delay: 0.4s; }
    .menu-card:nth-child(6) { animation-delay: 0.5s; }
    .menu-card:nth-child(7) { animation-delay: 0.6s; }
    .menu-card:nth-child(8) { animation-delay: 0.7s; }
    .menu-card:nth-child(9) { animation-delay: 0.8s; }
    .menu-card:nth-child(10) { animation-delay: 0.9s; }
    .menu-card:nth-child(11) { animation-delay: 1.0s; }
    .menu-card:nth-child(12) { animation-delay: 1.1s; }
    .menu-card:nth-child(13) { animation-delay: 1.2s; }
    .menu-card:nth-child(14) { animation-delay: 1.3s; }
    .menu-card:nth-child(15) { animation-delay: 1.4s; }
    .menu-card:nth-child(16) { animation-delay: 1.5s; }
    .menu-card:nth-child(17) { animation-delay: 1.6s; }
    .menu-card:nth-child(18) { animation-delay: 1.7s; }
    .menu-card:nth-child(19) { animation-delay: 1.8s; }
    .menu-card:nth-child(20) { animation-delay: 1.9s; }
    .menu-card:nth-child(21) { animation-delay: 2.0s; }
    .menu-card:nth-child(22) { animation-delay: 2.1s; }
    .menu-card:nth-child(23) { animation-delay: 2.2s; }
    .menu-card:nth-child(24) { animation-delay: 2.3s; }

    .badge {
      background-color: #e53935;
      color: white;
      font-size: 10px;
      padding: 4px 6px;
      border-radius: 8px;
      position: absolute;
      top: 10px;
      right: 10px;
    }


    @media (max-width: 600px) {
      .logo-responsive {
        display: none;
      }

      .menu-grid {
        padding: 16px;
      }
    }
     .welcome-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 20px; /* Puedes ajustar a 100% si no quieres que ocupe toda la ventana */  
        overflow: hidden;
    }

    .masked-text {
        font-size: 1rem;
        font-weight: bold;
        color: transparent;
        background-image: url('https://images.unsplash.com/photo-1732535725600-f805d8b33c9c?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); 
        background-size: 200%;
        background-position: 0 50%;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: animate-background 5s infinite alternate linear;
    }

    @keyframes animate-background {
        0% {
            background-position: 0 50%;
        }
        100% {
            background-position: 100% 50%;
        }
    }
/* Estilos generales del boton flotante tipo WhatsApp */
.registro-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #007bff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  animation: breathe 2s ease-in-out infinite;
  transition: background 0.2s;
  text-decoration: none;
}

.registro-btn:hover {
  background-color: #0056b3;
}

.registro-btn i {
  color: #fff;
  font-size: 28px;
  animation: beat 2s ease-in-out infinite;
  text-decoration: none;
}

/* Animación contorno respirando */
@keyframes breathe {
  0% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.5);
  }
  70% {
    box-shadow: 0 0 0 15px rgba(0, 123, 255, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
  }
}

/* Animación de latido del icono */
@keyframes beat {
  0% { transform: scale(1);}
  50% { transform: scale(1.2);}
  100% { transform: scale(1);}
}

@media (max-width: 600px) {
  .registro-btn {
    width: 48px;
    height: 48px;
    right: 14px;
    bottom: 14px;
  }
  .registro-btn i {
    font-size: 22px;
  }
}
  </style>
</head>
<body>

  <section class="animated-background">
    <div class="wave">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </section>

<header>
    <img src="{{ asset('images/logomedy.png') }}" alt="Logo" class="logo-responsive">
    <div class="welcome-wrapper">
      <div class="masked-text">
          <strong>Bienvenido, {{ Auth::user()->name }}</strong>
      </div>
    </div>
</header>

  <div class="buscador">
    <input type="text" id="buscador" placeholder="Buscar..." oninput="filtrarMenu()">
  </div>
@php
  $rutasRecientes = $modulosRecientes->map(fn($m) => url($m->ruta))->toArray();
@endphp

@if($modulosRecientes->isNotEmpty())
  <h3>Recientes</h3>
  <div class="menu-grid">
    @foreach($modulosRecientes as $modulo)
      <a href="{{ url($modulo->ruta) }}" class="menu-card">
        <i class="{{ $modulo->icono }}"></i>
        <p>{{ $modulo->nombre }}</p>
      </a>
    @endforeach
  </div>
@endif

<h3>Tus accesos</h3>
<div class="menu-grid" id="menuGrid">
  @if(!in_array(url('/publicaciones'), $rutasRecientes))
  <a href="{{ url('/publicaciones') }}" class="menu-card">
    <i class="fas fa-newspaper"></i>
    <p>Noticias</p>
  </a>
  @endif

  @if(!in_array(url('/inventario'), $rutasRecientes))
  <a href="{{ url('/inventario') }}" class="menu-card">
    <i class="fas fa-box"></i>
    <p>Inventario</p>
  </a>
  @endif

  @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor'))
    @if(!in_array(url('/ventas'), $rutasRecientes))
    <a href="{{ url('/ventas') }}" class="menu-card">
      <i class="fas fa-receipt"></i>
      <p>Remisiones</p>
    </a>
    @endif

    @if(!in_array(url('/propuestas'), $rutasRecientes))
    <a href="{{ url('/propuestas') }}" class="menu-card">
      <i class="fas fa-file-contract"></i>
      <p>Cotizaciones</p>
    </a>
    @endif

    @if(!in_array(url('/remisions'), $rutasRecientes))
    <a href="{{ url('/remisions') }}" class="menu-card">
      <i class="fas fa-tools"></i>
      <p>Mantenimiento</p>
    </a>
    @endif

    @if(!in_array(url('/ventas/deudores'), $rutasRecientes))
    <a href="{{ url('/ventas/deudores') }}" class="menu-card">
      <i class="fas fa-hand-holding-usd"></i>
      <p>Financiamientos</p>
    </a>
    @endif

    @if(!in_array(url('/clientes'), $rutasRecientes))
    <a href="{{ url('/clientes') }}" class="menu-card">
      <i class="fas fa-address-book"></i>
      <p>Clientes</p>
    </a>
    @endif
  @endif

  @if(!in_array(url('/agenda'), $rutasRecientes))
  <a href="{{ url('/agenda') }}" class="menu-card">
    <i class="fas fa-calendar-alt"></i>
    <p>Agenda</p>
  </a>
  @endif

  @if(!in_array(url('/camionetas'), $rutasRecientes))
  <a href="{{ url('/camionetas') }}" class="menu-card">
    <i class="fas fa-truck"></i>
    <p>Camionetas</p>
  </a>
  @endif

  @if(!in_array(url('/perfil'), $rutasRecientes))
  <a href="{{ url('/perfil') }}" class="menu-card">
    <i class="fas fa-user-circle"></i>
    <p>Perfil</p>
  </a>
  @endif

  @if(!in_array(url('/fichas'), $rutasRecientes))
  <a href="{{ url('/fichas') }}" class="menu-card">
    <i class="fas fa-file-alt"></i>
    <p>Fichas Técnicas</p>
  </a>
  @endif

  @if(!in_array(url('/carta-garantia'), $rutasRecientes))
  <a href="{{ url('/carta-garantia') }}" class="menu-card">
    <i class="fas fa-shield-alt"></i>
    <p>Cartas de Garantía</p>
  </a>
  @endif

  @if(!in_array(url('/solicitudes/crear'), $rutasRecientes))
  <a href="{{ url('/solicitudes/crear') }}" class="menu-card">
    <i class="fas fa-box-open"></i>
    <p>Solicitar Material</p>
  </a>
  @endif

  @if(!in_array(url('/cuentas'), $rutasRecientes))
  <a href="{{ url('/cuentas') }}" class="menu-card">
    <i class="fas fa-wallet"></i>
    <p>Viáticos</p>
  </a>
  @endif

  @if(!in_array(url('/prestamos'), $rutasRecientes))
  <a href="{{ url('/prestamos') }}" class="menu-card">
    <i class="fas fa-money-check-alt"></i>
    <p>Préstamos</p>
  </a>
  @endif

  @if(Auth::user()->hasRole('admin'))
    @if(!in_array(url('/usuarios'), $rutasRecientes))
    <a href="{{ url('/usuarios') }}" class="menu-card">
      <i class="fas fa-users-cog"></i>
      <p>Usuarios</p>
    </a>
    @endif

    @if(!in_array(url('/pedidos'), $rutasRecientes))
    <a href="{{ url('/pedidos') }}" class="menu-card">
      <i class="fas fa-shopping-cart"></i>
      <p>Pedidos</p>
    </a>
    @endif

    @if(!in_array(url(route('asistencias.index')), $rutasRecientes))
    <a href="{{ route('asistencias.index') }}" class="menu-card">
      <i class="fas fa-user-check"></i>
      <p>Asistencias</p>
    </a>
    <!-- Botón flotante -->
<a href="/inventario/buscar" target="_self" class="registro-btn" title="Ir a registro">
  <i class="bi bi-search"></i>
</a>
    @endif
  @endif
</div>

  <script>
    function filtrarMenu() {
      const filtro = document.getElementById("buscador").value.toLowerCase();
      const cards = document.querySelectorAll(".menu-card");

      cards.forEach(card => {
        const texto = card.innerText.toLowerCase();
        card.style.display = texto.includes(filtro) ? "block" : "none";
      });
    }
  </script>

</body>
</html>
