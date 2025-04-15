<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoai.png') }}?v=1">

    <title>@yield('title', 'Sistema de Cotizaciones')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}"> 
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <!-- Estilos personalizados -->
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/es.js"></script> <!-- Español -->
    @yield('styles')
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
                <li>
    <a href="{{ route('fichas.index') }}">
        <img src="{{ asset('images/documento.png') }}" alt="Icono de fichas técnicas" class="menu-icon-image">
        Fichas Técnicas
    </a>
</li>


                <!-- Nueva opción: Guias y Entregas, visible para todos los usuarios autenticados -->
                @auth
                <li class="menu-items">
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-guias')">
                        <img src="{{ asset('images/fedex.png') }}" alt="Icono de Guías" class="menu-icon-image">
                        Guias
                    </a>
                    <ul id="submenu-guias" class="submenu">
                        <li><a href="{{ route('guias.create') }}">+ Crear Guía</a></li>
                        <li><a href="{{ route('entrega.create') }}">Entregar Guía</a></li>
                        <li><a href="{{ route('entregas.index') }}">Ver Entregas</a></li>
                    </ul>
                </li>
                @endauth
                @auth
                <li class="menu-items">
    <a href="#" onclick="toggleSubmenu(event, 'submenu-camionetas')">
        <img src="{{ asset('images/cady.png') }}" alt="Icono de Camionetas" class="menu-icon-image">
        Camionetas
    </a>
    <ul id="submenu-camionetas" class="submenu">
        <li><a href="{{ route('camionetas.create') }}">+ Agregar Camioneta</a></li>
        <li><a href="{{ route('camionetas.index') }}">Lista de Camionetas</a></li>
    </ul>
</li>
@endauth

                <!-- Nueva opción: Usuarios, visible solo para el Admin con Submenú -->
                @if(Auth::user()->hasRole('admin'))
                <li class="menu-items">
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-usuarios')">
                        <img src="{{ asset('images/empleado.png') }}" alt="Icono de Usuarios" class="menu-icon-image">
                        Usuarios
                    </a>
                    <ul id="submenu-usuarios" class="submenu">
                        <li><a href="{{ route('users.create') }}">+ Agregar Usuario</a></li>
                        <li><a href="{{ url('/usuarios') }}">Lista de Usuarios</a></li>
                        <li><a href="{{ route('asistencias.index') }}"> Registrar Asistencias</a></li>
                    </ul> 
                </li>
                @endif

                <!-- Nueva opción para todas las cuentas: Vacaciones -->
                @auth
                <li class="menu-items">
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-vacaciones')">
                        <img src="{{ asset('images/vacaciones.png') }}" alt="Icono de Vacaciones" class="menu-icon-image">
                        Vacaciones
                    </a>
                    <ul id="submenu-vacaciones" class="submenu">
                        <li><a href="{{ route('vacaciones.index') }}">Solicitar Vacaciones</a></li>
                        <li><a href="{{ route('vacaciones.listar') }}">Ver Solicitudes</a></li>
                    </ul>
                </li>
                @endauth

                <!-- Nueva opción: Solicitudes de Vacaciones (solo para Admin) -->
                @if(Auth::user()->hasRole('admin'))
                <li class="menu-items">
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-admin-vacaciones')">
                        <img src="{{ asset('images/vacaciones-admin.png') }}" alt="Icono de Vacaciones Admin" class="menu-icon-image">
                        Solicitudes Vacaciones
                    </a>
                    <ul id="submenu-admin-vacaciones" class="submenu">
                        <li><a href="{{ route('vacaciones.listar') }}">Ver Solicitudes Pendientes</a></li>
                        <li><a href="{{ route('vacaciones.ver', ['id' => 1]) }}">Ver Detalles Solicitud</a></li>
                        <li><a href="{{ route('vacaciones.aprobar', ['id' => 1]) }}">Aprobar Solicitud</a></li>
                        <li><a href="{{ route('vacaciones.rechazar', ['id' => 1]) }}">Rechazar Solicitud</a></li>
                    </ul>
                </li>
                @endif
<!-- Nueva opción: Solicitudes de Material (visible para todos los autenticados) -->
@auth
<li class="menu-items">
    <a href="#" onclick="toggleSubmenu(event, 'submenu-solicitudes-material')">
        <img src="{{ asset('images/material.png') }}" alt="Icono de Solicitudes de Material" class="menu-icon-image">
        Solicitudes de Material
    </a>
    <ul id="submenu-solicitudes-material" class="submenu">
        <!-- Solo visible para admin -->
        @if(Auth::user()->hasRole('admin'))
            <li><a href="{{ route('solicitudes.admin') }}">Solicitudes Pendientes</a></li>
        @endif
        <!-- Visible para todos los autenticados -->
        <li><a href="{{ route('solicitudes.index') }}">Ver Mis Solicitudes</a></li>
        <li><a href="{{ route('solicitudes.create') }}">+ Crear Solicitud</a></li>
    </ul>
</li>
@endauth

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

        <h1 class="titulos">@yield('titulo', 'Cotizaciones')</h1>


        <!-- Línea animada -->
        <div class="gradient-bg-animation"></div>
    </div>


    @yield('content')
    @php
    // Lista de rutas donde el footer debe mostrarse
    $footerPages = [
        'cotizaciones',
        'clientes/vista',
        'historial-cotizaciones'
    ];
@endphp

@if(Request::is($footerPages))
    <footer class="text-center mt-5 py-3 bg-light">
        <p>&copy; {{ date('Y') }} - Sistema de Cotizaciones</p>
    </footer>
@endif


    
<!-- jQuery y Bootstrap JS -->
 <!-- Cargar jQuery -->


<!-- Cargar Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
   
   
    <script>
        function toggleMenu() {
            document.getElementById('menu-sidebar').classList.toggle('active');
            document.getElementById('menu-overlay').classList.toggle('active');
        }
        function closeMenu() {
            document.getElementById('menu-sidebar').classList.remove('active');
            document.getElementById('menu-overlay').classList.remove('active');
        }
    </script>
    @yield('scripts')
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
    document.addEventListener("DOMContentLoaded", function () {
    const clientList = [
        { name: "jani ivonne" },
        { name: "Jose Alex Esquivel Pérez" },
    ];

    // Rellena la lista de clientes
    const clientListElement = document.getElementById("client-list");
    clientList.forEach(client => {
        const li = document.createElement("li");
        li.innerHTML = `<button class="dropdown-item" onclick="selectClient('${client.name}')">${client.name}</button>`;
        clientListElement.appendChild(li);
    });
});

function openCreateClientModal() {
    const modal = new bootstrap.Modal(document.getElementById("createClientModal"));
    modal.show();
}

function saveClient() {
    const name = document.getElementById("client-name").value;
    const lastname = document.getElementById("client-lastname").value;
    const fullName = `${name} ${lastname}`;

    if (name && lastname) {
        const clientListElement = document.getElementById("client-list");
        const li = document.createElement("li");
        li.innerHTML = `<button class="dropdown-item" onclick="selectClient('${fullName}')">${fullName}</button>`;
        clientListElement.appendChild(li);

        // Cierra el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById("createClientModal"));
        modal.hide();
    }
}

function selectClient(clientName) {
    document.getElementById("search-client").value = clientName;
}

</script>
<script>
    let timeout;
    const INACTIVITY_LIMIT = 120 * 60 * 1000; // 10 minutos en milisegundos
    let lastActivityTime = Date.now(); // Establece el tiempo inicial de actividad

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logoutUser, INACTIVITY_LIMIT); // Redirige después de 10 minutos
        lastActivityTime = Date.now(); // Resetea el tiempo de la última actividad
    }

    function logoutUser() {
        // Redirige al usuario a la ruta de logout
        window.location.href = "{{ route('logout') }}"; // Asegúrate de que la ruta 'logout' esté definida en tus rutas
    }

    // Detectar cualquier actividad del usuario (movimiento del ratón, teclas presionadas, etc.)
    document.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onscroll = resetTimer;
    document.onclick = resetTimer;

    // Verificar automáticamente la sesión cada vez que el usuario realice una acción
    setInterval(function() {
        // Si han pasado más de 10 minutos desde la última actividad, cerramos la sesión
        if (Date.now() - lastActivityTime > INACTIVITY_LIMIT) {
            logoutUser();
        }
    }, 10000); // Verificar cada 10 segundos si ha pasado el tiempo de inactividad
</script>

<script>
function toggleSubmenu(event, submenuId) {
    event.preventDefault(); // Evita que el enlace recargue la página
    const submenu = document.getElementById(submenuId);
    submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
}
</script>
</body>
</html>
