<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Anton&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Patrick Hand', cursive;
            background: linear-gradient(to bottom, #000000, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
            text-align: center;
        }

        .background-circle {
    position: absolute;
    bottom: 5%;
    left: 50%;
    transform: translateX(-50%);
    width: 28rem;
    height: 28rem;
    background-color: #5da0d7;
    border-radius: 50%;
    filter: blur(40px);
    opacity: 0.4;
    animation: float 6s infinite ease-in-out, bounce 1.5s infinite ease-in-out;
}

@keyframes float {
    0%, 100% {
        transform: translateX(-50%) translateY(0);
    }
    50% {
        transform: translateX(-50%) translateY(-60px); /* Ligero movimiento hacia arriba */
    }
}

@keyframes bounce {
    0%, 100% {
        transform: translateX(-50%) translateY(0);
    }
    50% {
        transform: translateX(-50%) translateY(-90px); /* Aumentar la distancia de salto */
    }
}




        .container {
            position: relative;
            max-width: 640px;
            z-index: 10;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #f3f4f6;
            line-height: 1.2;
            opacity: 0;
        }

        h1 span {
            display: block;
        }

        .highlight {
    position: relative;
    display: inline-block;  /* Cambié 'inline' a 'inline-block' para manejar mejor el pseudo-elemento */
    padding: 0 6px;
    color: black;
    background: transparent;  /* Fondo transparente para que solo se vea el triángulo */
}

.highlight::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, #facc15 50%, transparent 50%);
    clip-path: polygon(100% 50%, 0 0, 0 100%, 100% 85%); /* Triángulo acostado */
    z-index: -1; /* Asegura que el triángulo esté detrás del texto */
}

        p {
            font-size: 1.5rem;
            color: #d1d5db;
            margin-top: 1rem;
            opacity: 0;
        }

        p span {
            color: #facc15;
        }
        @import url('https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap');

.button {
    font-family: 'Patrick Hand', cursive;
}

        .button-container {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            opacity: 0;
        }

        .button {
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease-in-out;
            border: 2px solid transparent;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
            transform: scale(0.8);
        }
        .button-icon {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
    opacity: 0.5; /* Ajusta la opacidad si es necesario */
}

.button span {
    position: relative;
    z-index: 1;
}
        .button.primary {
            background-color: #60a5fa;
            color: white;
        }

        .button.primary:hover {
            background-color: #2563eb;
            color: black;
        }

        .button.secondary {
            background-color: transparent;
            color: black;
            border-color: #9ca3af;
        }

        .button.secondary:hover {
            background-color: #374151;
            color: white;
        }

        @media (max-width: 768px) {
            .button-container {
    display: flex;
    flex-wrap: wrap; /* Permite que los botones se ajusten en varias líneas */
    padding-bottom: 10px;
    padding-left: 15px;
    padding-right: 15px;
}
            h1 {
                font-size: 2rem;
                margin-top: -90px;
                margin-bottom: 30px;
            }

            p {
                font-size: 1rem;
                font-weight: bold;
                
            }

            .button-container::-webkit-scrollbar {
                display: none;
            }
            .footer {
        font-size: 1.2rem !important;  /* Usando !important para asegurar que se aplique */
        margin-top: 0.8rem !important;
        padding: 0.8rem !important;
    }
}

        .footer {
    color: Black;
    margin-top: 1.5rem;
    font-size: 1.2rem;
    opacity: 0;
    font-weight: bold;
    text-align: center;
    padding: 1rem;
}

.footer span {
    font-weight: bold;
}
.button-registro-inventario {
    background-color: #84cc16; /* Verde Lima */
    color: white;
}

.button-registro-inventario:hover {
    background-color: #65a30d; /* Verde Lima Oscuro */
}

.button-inventario {
    background-color: #facc15; /* Amarillo */
    color: black;
}

.button-inventario:hover {
    background-color: #eab308; /* Amarillo oscuro */
}

.button-cotizacion {
    background-color: #10b981; /* Verde Esmeralda */
    color: white;
}

.button-cotizacion:hover {
    background-color: #059669; /* Verde oscuro */
}

.button-remisiones {
    background-color: #f43f5e; /* Rojo Rosado */
    color: white;
}

.button-remisiones:hover {
    background-color: #e11d48; /* Rojo oscuro */
}

.button-agenda {
    background-color: #fb923c; /* Naranja */
    color: white;
}

.button-agenda:hover {
    background-color: #f97316; /* Naranja oscuro */
}

.button-perfil {
    background-color: #3b82f6; /* Azul Brillante */
    color: white;
}

.button-perfil:hover {
    background-color: #2563eb; /* Azul oscuro */
}

.button-usuarios {
    background-color: #64748b; /* Gris Azulado */
    color: white;
}

.button-usuarios:hover {
    background-color: #475569; /* Gris oscuro */
}

.button-asistencias {
    background-color: #a855f7; /* Morado Vibrante */
    color: white;
}

.button-asistencias:hover {
    background-color: #9333ea; /* Morado oscuro */
}


    </style>
</head>
<body>
    <div class="background-circle"></div>
    
    <div class="container">
        <h1>
            <span>Bienvenido al sistema de</span>
            <span class="highlight">Grupo MediBuy</span>.
        </h1>
        <p>¡Confianza y tecnología para el bienestar de tus <span>PACIENTES</span>!</p>
        
        <div class="button-container">
        <button class="button button-registro-inventario" onclick="window.location.href='{{ url('/') }}'">Registro de Inventario</button>

        <button class="button button-inventario" onclick="window.location.href='{{ url('/inventario') }}'">Inventario</button>
        <button class="button button-remisiones" onclick="window.location.href='{{ url('/remisiones') }}'">Sistema de Remisiones</button>
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor'))
            <button class="button button-cotizacion" onclick="window.location.href='{{ url('/cotizaciones') }}'">Sistema de Cotizaciones</button>
            @endif
            <button class="button button-agenda" onclick="window.location.href='{{ url('/agenda') }}'">Agenda</button>
            <button class="button secondary" onclick="window.location.href='{{ route('entrega.create') }}'">Sistema de Guía</button>
            <button class="button button-perfil" onclick="window.location.href='{{ url('/perfil') }}'">Mi Perfil</button>
            @if(Auth::user()->hasRole('admin'))
    <button class="button button-usuarios" onclick="window.location.href='{{ url('/usuarios') }}'">Lista de Usuarios</button>
    <button class="button button-asistencias" onclick="window.location.href='{{ route('asistencias.index') }}'">Registrar Asistencias</button>
@endif
        </div>
        <p class="footer">Seleccione <span>Una Opción </span>para continuar</p>
    </div>
    <script>
        gsap.to("h1", { opacity: 1, y: 0, duration: 1, ease: "power2.out" });
        gsap.from("h1 span", { opacity: 0, y: -20, duration: 1, ease: "power2.out", stagger: 0.2 });
        gsap.to("p", { opacity: 1, y: 0, duration: 1, delay: 0.5, ease: "power2.out" });
        gsap.to(".button-container", { opacity: 1, y: 0, duration: 1, delay: 1, ease: "power2.out" });
        gsap.to(".button", { opacity: 1, scale: 1, duration: 0.8, delay: 1.2, stagger: 0.2, ease: "elastic.out(1, 0.5)" });
        gsap.to(".footer", { opacity: 1, y: 0, duration: 1, delay: 1.5, ease: "power2.out" });
    </script>
</body>
</html>