<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Cotizaciones')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<!-- Estilos personalizados -->
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">




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
                    <a href="{{ route('auth.change-password') }}" class="welcome-link">
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
                            <img src="{{ asset('images/inventario.png') }}" alt="Icono de Inventario" class="menu-icon-imagen">
                            Inventario
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/cotizaciones') }}">
                            <img src="{{ asset('images/cotizaciones.png') }}" alt="Icono de Cotizaciones" class="menu-icon-image">
                            Cotizaciones
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/remisiones') }}">
                            <img src="{{ asset('images/remisiones.png') }}" alt="Icono de remisiones" class="menu-icon-image">
                            Remisiones
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/agenda') }}">
                            <img src="{{ asset('images/agenda.png') }}" alt="Icono de agenda" class="menu-icon-imagen">
                            Agenda
                        </a>
                    </li>

                    @auth
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-button">
                                <img src="{{ asset('images/cerrar.png') }}" alt="Icono Registro de Inventario" class="menu-icon-image">
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

        <h1 class="titulos">Cotizaciones </h1>

        <!-- Línea animada -->
        <div class="gradient-bg-animation"></div>
    </div>

    <div class="container mt-4">
        @yield('content')
    </div>

    <footer class="text-center mt-5 py-3 bg-light">
        <p>&copy; {{ date('Y') }} - Sistema de Cotizaciones</p>
    </footer>

    
<!-- jQuery y Bootstrap JS -->
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
</body>
</html>
