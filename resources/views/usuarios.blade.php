<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <style>
        body {
        font-family: Arial, sans-serif;
        background-color: #f5faff;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        overflow: auto;
    }

    .container {
        margin-top:70px;
        padding: 30px;
        border-radius: 10px;
       
        width: 90%;
        max-width: 1200px;
        text-align: left;
        opacity: 0;
        transform: translateY(-20px);
        animation: fadeIn 1s forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    /* Diseño de usuarios en filas de tres */
    .users-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
    }

    /* Tarjeta de perfil */
.profile-container {
    flex: 1 1 calc(33.33% - 20px);
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}


/* Animación al pasar el mouse */
.profile-container:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* 📌 Ajuste para cada fila de información */
.info {
    font-size: 14px;
    color: #555;
    display: flex;
    flex-direction: column; /* Coloca el título arriba y el dato abajo */
    align-items: flex-start; /* Alinea todo a la izquierda */
    gap: 3px; /* Espacio entre el título y el dato */
    opacity: 0;
    transform: translateX(-10px);
    animation: slideIn 0.5s forwards;
}
/* 🖥️ SOLO en pantallas grandes (mínimo 768px) → Dos columnas */
@media (min-width: 768px) {
    .profile-container.expanded .extra-info {
        grid-template-columns: 1fr 1fr; /* Divide en dos columnas */
    }
}

/* 📱 Para pantallas pequeñas (menos de 768px) → Una columna */
@media (max-width: 767px) {
    .profile-container.expanded .extra-info {
        grid-template-columns: 1fr; /* Solo una columna en móviles */
        text-align: left; /* Mantiene la alineación izquierda */
        display: flex;
        flex-direction: column;
        align-items: flex-start; /* Alinea todo a la izquierda */
    }

    /* Asegurar que los textos estén bien alineados */
    .info {
        align-items: flex-start; /* Evita que los textos se desordenen */
    }
}

/* Negritas bien alineadas */
.info strong {
    font-weight: 600;
    white-space: nowrap; /* Evita que el texto fuerte se divida en dos líneas */
    color: #333;
    font-size: 13px;
    text-transform: uppercase;
}

/* Dato del usuario con mayor contraste */
.info span {
    font-size: 15px;
    font-weight: 500;
    color: #000;
}


    hr {
        width: 100%;
        border: 1px solid #ddd;
        margin-top: 20px;
    }

    /* Responsividad */
    @media (max-width: 1024px) {
        .profile-container {
            flex: 1 1 calc(50% - 20px);
        }
    }

    @media (max-width: 768px) {
        .profile-container {
            flex: 1 1 100%;
        }
    }
        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
            text-align: center;
        }
 /* 📌 Ajuste para cada fila de información */
/* 📌 Ajuste para cada fila de información */
.info {
    font-size: 14px;
    color: #555;
    display: flex;
    flex-direction: column; /* Coloca el título arriba y el dato abajo */
    align-items: flex-start; /* Alinea todo a la izquierda */
    gap: 3px; /* Espacio entre el título y el dato */
    opacity: 0;
    transform: translateX(-10px);
    animation: slideIn 0.5s forwards;
}


        .info:nth-child(even) {
            animation-delay: 0.3s;
        }
        .info:nth-child(odd) {
            animation-delay: 0.6s;
        }
        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
      
        /* Asegurar que el texto fluya correctamente */
.info span {
    flex: 1;
    text-align: left;
}
        .btn-edit {
        background: none;
        border: none;
        cursor: pointer;
    }

    .btn-edit img {
        width: 20px;
        height: 20px;
    }
    .btn-save {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        cursor: pointer;
    }

    .btn-save img {
        width: 24px;
        height: 24px;
    }
    .hidden {
        display: none;
    }
       
        .input-field {
        width: 100%;
        padding: 10px 40px 10px 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }
    .input-container {
        display: flex;
        align-items: center;
    }
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
        .input-field:focus {
            border-color: #007BFF;
            outline: none;
        }
        .modify-section {
    margin-top: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

        .modify-section label {
            font-size: 16px;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }
        .hidden {
            display: none;
        }
                 /* Estilos para el menú hamburguesa */
.menu-hamburguesa {
    position: relative;
    z-index: 10000;
    grid-column: 1; /* Coloca el menú en la primera columna */
}

.menu-hamburguesa button {
    background-color: #ffffff;

    border: none;
    padding: 10px 15px;
    cursor: pointer;
    z-index: 10001;
}

.menu-sidebar {
    position: fixed;
    top: 0;
    left: -250px;
    width: 250px;
    height: 100%;
    background: #F4F4F4;
    color: #fff;

    transition: left 0.3s ease;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
    z-index: 10001;
    display: flex;
    flex-direction: column;
}

.menu-sidebar.open {
    left: 0;
}
/* Overlay (fondo oscuro al abrir menú) */
.menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 10000;
}

.menu-overlay.active {
    display: block;
}
/* Estilos de los ítems del menú */
.menu-items {
    max-height: calc(100vh - 50px); /* Ajustar según el espacio del logo y bienvenida */
    overflow-y: auto; /* Permite el desplazamiento solo para el contenido debajo */
    padding: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
}
/* Ocultar la barra de desplazamiento en navegadores basados en WebKit (Chrome, Safari) */
.menu-items::-webkit-scrollbar {
    display: none;
}

/* En Firefox, usar -moz para ocultar la barra de desplazamiento */
.menu-items {
    scrollbar-width: none; /* Ocultar barra en Firefox */
}

.menu-items li {
    padding: 15px 20px;
    border-bottom: 1px solid #CCCCCC;
    transition: background 0.3s ease, transform 0.3s ease;
    position: relative;
}

.menu-items li a {
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
    width: 210px;
    height: 35px;
    transition: color 0.3s ease;
    position: relative;
    overflow: hidden;
}

.menu-items li i {
    margin-right: 10px;
    font-size: 18px;
    transition: transform 0.3s ease, color 0.3s ease;
}

/* Subrayado animado */
.menu-items li a::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #007bff;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease-in-out;
}

.menu-items li a:hover::after {
    transform: scaleX(1);
}

.menu-items li:hover {
    background: rgba(0, 123, 255, 0.1);
    transform: scale(1.02);
    border-radius: 5px;
}

.menu-items li a:hover {
    color: #01579b;
}

.menu-items li a:hover i {
    color: #007bff;
    transform: rotate(10deg);
}



/* Imagen del icono */
.menu-icon-image {
    width: 34px; /* Tamaño del icono */
    height: 34px;
    margin-right: 5px; /* Espacio entre el icono y el texto */
    object-fit: contain;
}
.menu-icon-imagen {
    width: 25px; /* Tamaño del icono */
    height: 25px;
    margin-right: 11px; /* Espacio entre el icono y el texto */
    margin-left: 3px;
    object-fit: contain;
}

.logout-button {
    border: none;
    color: black;
    display: flex;
    align-items: center;
    cursor: pointer;
    width: 100%;
    text-align: left;
    padding: 0;
}

.logout-button i {
    margin-right: 10px;
}
.menu-logo {
    width: 220px;
    height: auto;
}
/* Logo del menú */
.menu-header {
    padding: 20px;
    background: #F4F4F4;
    text-align: center;
}
.menu-hamburguesa .menu-icon img {
    width: 34px;
    height: 34px;
}

/* Sección de bienvenida */
.welcome-section {
    padding: 15px 20px;
    background: #7cb8eb;
    border-bottom: 1px solid #444;
    color: black;
    font-size: 14px;
}

.welcome-section .user-name {
    font-weight: bold;
    color: #33373b;
}

/* Estilo para ocultar el contenido al moverlo */
        /* Estilos proporcionados */
        .titulos {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 900;
            color: #333333;
            font-size: 30px;
            line-height: 24px;
            text-align: center; /* Centra el texto */
            grid-column: 2; /* Coloca el título en la columna central del grid */
            margin: 0 auto; /* Centra el texto horizontalmente dentro de la columna */
            transform: translateX(-10%); /* Mueve el título ligeramente hacia la izquierda */
        }

        .titulos_encabezado {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 400;
            font-size: 20px;
            line-height: 24px;
            margin-left: 25px;
        }
        /* Linea de encabezado*/
        .gradient-bg-animation {
            width: 100%;
            bottom: 0; /* Posiciona la línea en la parte inferior del encabezado */
            position: absolute;
            height: 4px;
            border-radius: 2.5px;
            background: linear-gradient(26deg, #01448F 20%, #1E6BB8 40%, #3498DB 60%, #87CEFA 80%, #B0E0E6 100%);
            background-size: 300% 100%;
            animation: gradient 3s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
            margin-top: 10px;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .welcome-link {
            display: block;
            text-decoration: none;
            color: #333;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            
            border: 1px solid transparent;
            border-radius: 5px;
            background-color: #7CB8EB;
            transition: all 0.3s ease, transform 0.3s ease; /* Agregamos transición para transform */
        }
        
        .welcome-link:hover {
            color: black;
            background-color: #7CB8EB;
            border: 1px solid #7CB8EB;
            text-decoration: none;
            transform: scale(0.9); /* Aumenta el tamaño en un 10% */
        }
@media (max-width: 375px) {
    .titulos {
        font-size: 20px;
        transform: translateX(-30%); /* Elimina el desplazamiento lateral en pantallas pequeñas */
    }
}
.header-container {
            position: fixed; /* Hace que el contenedor sea fijo */
            top: 0; /* Lo fija en la parte superior de la pantalla */
            left: 0; /* Lo fija al inicio del viewport horizontal */
            width: 100%; /* Asegura que el encabezado abarque todo el ancho de la pantalla */
            z-index: 1000; /* Coloca el encabezado por encima de otros elementos */
            background-color: #ffffff; /* Añade un fondo para que no se superponga con otros elementos */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Añade una ligera sombra para destacarlo */
            display: grid;
            grid-template-columns: auto 1fr auto; /* Tres columnas: menú, título, espacio */
            align-items: center;
            padding: 10px;
            box-sizing: border-box;
        }
        body {
    padding-top: 10px; /* Ajusta este valor al alto del encabezado */
}
       /* Logo de encabezado */
       .logo {
            width: 230px; /* Tamaño del logo en pantallas grandes */
            height: auto;
        }
          /* Media query para pantallas pequeñas */
          @media (max-width: 768px) {
            body.menu-open .contenedor_general {
        transform: translateX(250px);
    }
    body {
    padding-top: -100% !important; /* Ajusta este valor al alto del encabezado */
}
    .header-container {
        grid-template-columns: 1fr; /* Una sola columna en pantallas pequeñas */
        text-align: center; /* Centra todo en el eje horizontal */
    }
    .menu-hamburguesa {
        justify-self: start; /* Alinea el menú al inicio en pantallas pequeñas */
    }
            .logo {
                width: 180px;
                order: -1;
            }
            .titulos {
        font-size: 20px;
        transform: translateX(-45%); /* Elimina el desplazamiento lateral en pantallas pequeñas */
    }
            .custom-margin {
                margin-left: 0;
            }
            .customer-margin {
            margin-left: 0 !important;
            }
            .contenedor {
                    margin-left: 14px; !important; /* Forzamos a sobrescribir otros estilos */
                    margin-right: 30px;
            }
            .contenedor_observaciones {
                    margin-left: 14px !important;
                    margin-right: 14px !important;
            }
            .image-preview {
                display: flex;
                flex-wrap: wrap;
                justify-content: center; /* Centrar las imágenes */
            }
            .image-preview-container {
                width: calc(50% - 10px); /* Dos imágenes en una fila */
                margin-right: 5px;
                margin-bottom: 10px;
            }
            .preview-contenedor {
                max-width: 100%; /* No limitar el ancho en pantallas pequeñas */
                padding: 10px;
                box-shadow: none;
            }
            .file-input-text {
                font-size: 14px; /* Reducir el tamaño del texto para pantallas más pequeñas */
                height: 40px; /* Reducir la altura para ajustarse mejor en pantallas pequeñas */
            }
            .preview-container {
            padding: 10px; /* Reducir el padding en pantallas pequeñas */
            border-radius: 5px; /* Redondear más los bordes en pantallas pequeñas */
            max-width: 100%; /* Asegurar que el contenedor ocupe todo el ancho */
            }
            .video-preview video {
                max-width: 100%; /* Asegurar que el video no se desborde */
                height: 280px; /* Mantener la proporción */
                border-radius: 5px; /* Redondear los bordes del video */
            }
            .cuadro {
                margin-left: -15px !important;
                margin-right: 5px !important;
                width: 100%;
                padding: -200px;
            }
        }
        .profile-picture {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-picture {
        display: flex;
        justify-content: center; /* Centrar horizontalmente */
        align-items: center; /* Centrar verticalmente si es necesario */
        flex-direction: column; /* Para que los elementos dentro se apilen verticalmente */
        margin-bottom: 20px;
    }

    .profile-picture img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ddd; /* Opcional: agregar borde */
    }
    .password-container {
    display: flex;
    justify-content: center; /* Centrar horizontalmente */
    margin-top: 20px; /* Espacio superior */
}

.btn-modify { 
    background-color: #1E6BB8;
    gap: 8px;
    width: 140px;
    height: 45px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    border: none;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: flex;
    align-items: center; 
    justify-content: center; /* Centrar el texto dentro del botón */
}

.btn-modify:hover {
    background-color: #155a91;
    transform: scale(1.05);
}
.submenu {
    display: none;
    list-style: none;
    padding-left: 20px;
}
.submenu li {
    padding: 5px 0;
}

.extra-info { 
    display: none; /* Ocultamos por defecto */
    margin-top: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    width: 100%;
    max-height: 250px; /* Evita que se expanda demasiado */
    overflow-y: auto; /* Activa el scroll si hay demasiada información */
}



/* Usuario seleccionado: agrandamos y mostramos */
.profile-container.expanded {
    max-width: 600px;
    background: #f9f9f9;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Ocultar a los demás usuarios cuando uno esté expandido */
.users-container.single-view .profile-container:not(.expanded) {
    display: none;
}

/* Cuando el perfil está expandido, se muestra la info */
.profile-container.expanded .extra-info {
    display: grid;
    gap: 10px; /* Espaciado entre elementos */
}
/* Información básica */
.basic-info {
    font-size: 14px;
    color: #555;
    margin-top:10px;
}

    </style>
</head>
<body>
<div class="header-container"> 
    <div class="menu-hamburguesa">
        <!-- Botón de menú con icono personalizado -->
        <button onclick="toggleMenu()" class="menu-icon">
            <img src="{{ asset('images/menu.png') }}" alt="Menu Icon">
        </button>

        <!-- Menú lateral -->
        <nav id="menu-sidebar" class="menu-sidebar">
            <!-- Logo del proyecto -->
            <div class="menu-header">
                <img src="{{ asset('images/logomedy.png') }}" alt="Logo" class="menu-logo">
            </div>

            <!-- Bienvenida personalizada -->
            @auth
                <div class="welcome-section">
                    <a href="{{ route('perfil') }}" class="welcome-link">
                        <p>Bienvenido,</p>
                        <p class="user-name">{{ Auth::user()->name }}</p>
                    </a>
                </div>
            @endauth

            <!-- Ítems del menú -->
            <ul class="menu-items">
                <li>
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/registro.png') }}" alt="Icono Registro de Inventario" class="menu-icon-image">
                        Registro de Inventario
                    </a>
                </li>
                <li>
                    <a href="{{ url('/inventario') }}">
                        <img src="{{ asset('images/inventario.png') }}" alt="Icono de Inventario" class="menu-icon-image">
                        Inventario
                    </a>
                </li>

                <!-- Opción de Cotizaciones con Submenú -->
                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor'))
                <li class="menu-items">
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-cotizaciones')">
                        <img src="{{ asset('images/cotizaciones.png') }}" alt="Icono de Cotizaciones" class="menu-icon-image">
                        Cotizaciones
                    </a>
                    <ul id="submenu-cotizaciones" class="submenu">
                        <li><a href="{{ url('/cotizaciones') }}">+ Crear Cotización</a></li>
                        <li><a href="{{ url('/clientes/vista') }}">Clientes</a></li>
                        <li><a href="{{ route('historial-cotizaciones') }}">Historial de Cotizaciones</a></li>
                    </ul>
                </li>
                @endif

                <li>
                    <a href="{{ url('/remisiones') }}">
                        <img src="{{ asset('images/remisiones.png') }}" alt="Icono de remisiones" class="menu-icon-image">
                        Remisiones
                    </a>
                </li>
                <li>
                    <a href="{{ url('/agenda') }}">
                        <img src="{{ asset('images/agenda.png') }}" alt="Icono de agenda" class="menu-icon-image">
                        Agenda
                    </a>
                </li>

                <!-- Nueva opción: Visible solo para el Admin con Submenú -->
                @if(Auth::user()->hasRole('admin'))
                <li class="menu-items">
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-usuarios')">
                        <img src="{{ asset('images/empleado.png') }}" alt="Icono de Usuarios" class="menu-icon-image">
                        Usuarios
                    </a>
                    <ul id="submenu-usuarios" class="submenu">
                        <li><a href="{{ route('users.create') }}">+ Agregar Usuario</a></li>
                        <li><a href="{{ url('/usuarios') }}">Lista de Usuarios</a></li>
                        <li> <a href="{{ route('asistencias.index') }}"> Registrar Asistencias</a></li>
                    </ul> 
                </li>
                @endif

                @auth
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-button">
                                <img src="{{ asset('images/cerrar.png') }}" alt="Icono de Cerrar Sesión" class="menu-icon-image">
                                Cerrar Sesión
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="icon-class icon-login"></i> Iniciar Sesión
                        </a>
                    </li>
                @endauth
            </ul>
        </nav>
  
        <!-- Overlay para cerrar el menú -->
        <div id="menu-overlay" class="menu-overlay" onclick="closeMenu()"></div>
    </div>
    @if (Auth::user()->role === 'admin')
    <h1 class="titulos">Lista de Usuarios</h1>

    <!-- Línea animada -->
    <div class="gradient-bg-animation"></div>
    </div>

 
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


@else
    <h1 class="titulos">Acceso Restringido</h1>
    <p>No tienes permisos para ver esta página.</p>
@endif


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
<script>
   let startX;

function toggleMenu() {
    const menu = document.getElementById('menu-sidebar');
    const overlay = document.getElementById('menu-overlay');
    const body = document.body;

    if (menu.classList.contains('open')) {
        closeMenu();
    } else {
        menu.classList.add('open');
        overlay.classList.add('active');
        body.classList.add('menu-open');
    }
}

function closeMenu() {
    const menu = document.getElementById('menu-sidebar');
    const overlay = document.getElementById('menu-overlay');
    const body = document.body;

    menu.classList.remove('open');
    overlay.classList.remove('active');
    body.classList.remove('menu-open');
}

// Manejo de gestos en pantallas táctiles
document.addEventListener('touchstart', function (e) {
    startX = e.touches[0].clientX;
});

document.addEventListener('touchmove', function (e) {
    const currentX = e.touches[0].clientX;
    const menu = document.getElementById('menu-sidebar');

    if (startX > currentX && startX - currentX > 50 && menu.classList.contains('open')) {
        closeMenu();
    }
});
</script>
<script>
function toggleSubmenu(event, submenuId) {
    event.preventDefault(); // Evita que el enlace recargue la página
    const submenu = document.getElementById(submenuId);
    submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
}
</script>
    </html>