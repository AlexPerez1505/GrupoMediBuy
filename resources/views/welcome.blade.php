<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


    <title>Registro</title>
    <style>
        /* Estilos proporcionados */
        .titulos {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 900;
            color: #333333;
            font-size: 30px;
            line-height: 24px;
            margin-left: 20px;
            margin-top: 15px;
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

        /* Contenedores */
        .contenedor-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .contenedor_general {
            background-color: white;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: -4px 1px 16px rgba(0, 0, 0, 0.1);
            width: 85%;
            height: auto;
            margin-top: 25px;
        }
         /* Botones */
        .form-grupo  {
            margin-bottom: 35px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .btn {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 1.6rem;
        }

        .btn-guardar {
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
        }

        .btn-guardar:hover {
            background-color: #155a91;
            transform: scale(1.05);
        }

        .btn-borrar {
            background-color: #E43E3D;
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
        }

        .btn-borrar:hover {
            background-color: #bf2f2e;
            transform: scale(1.05);
        }

        .icono  {
            width: 23px;
            height: 23px;
        }
        .icono-borrar  {
            width: 33px;
            height: 21px;
        }
        /* Contenedores para los campos de texto */
        .label_nomina{
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: bold;
            color: #333333;
            font-size: 15px;
        }
        .form-group {
            margin-bottom: 0.75rem;
            text-align: left;
            position: relative;
            display: flex;
            align-items: center;
        }
        .input_consulta {
            display: flex;
            align-items: center;
            padding-bottom: 0px;
            border-bottom: 2px solid #1E6BB8;
            border-radius: 10px;
            width: 100%;
        }
        .form-group .icon-container2 {
            width: 40px;
            height: 40px;
            background-color: #c0cab452;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom-left-radius: 10px;
            border-top-left-radius: 10px;
        }
        .form-group .icon2 {
            width: 23px;
            height: auto;
        }
        .header-container {
            display: flex;
            align-items: center; /* Alinea verticalmente */
            justify-content: space-between; /* Título y logo en lados opuestos */
            flex-direction: row; /* Por defecto en horizontal */
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        /* Logo de encabezado */
        .logo {
            width: 230px; /* Tamaño del logo en pantallas grandes */
            height: auto;
        }
        /* Media query para pantallas pequeñas */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            .logo {
                width: 180px;
                order: -1;
            }
            .titulos {
                font-size: 20px;
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
        /* Media query para pantallas grandes */
        @media (min-width: 768px) {
            .custom-margin {
                margin-left: 40px;
            }
            .customer-margin {
                margin-left: 50px;
            }
        }
        .contenedor_observaciones{
            width: 100%;
            max-width: 1150px; /* Ancho máximo en pantallas grandes */


            margin-left: 40px;
        }
        /* Línea de separación */
        .division {
            width: 95%;
            margin: 0 auto;
            margin-top: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
        }
        .form-control {
            padding: 0.5rem; /* Ajustado el padding */
            border-radius: 0 10px 10px 0;
            border: none; /* Quitado el borde */
            width: calc(100% - 36px);
            box-sizing: border-box;
            background-color: #F4F4F4; /* Fondo ajustado */
            color: #555555; /* Color del texto */
            height: 40px; /* Altura ajustada para que coincida con el icono */
            outline: none; /* Eliminar el resaltado al seleccionar */
        }

        .select {
            appearance: auto;
            border-radius: 10px 10px 10px 10px;
            width: 100%;
        }

        textarea::placeholder {
            color: #6c757d; /* Color gris oscuro */
            font-style: italic;
        }
        /* Unique Container Styles */
        .custom-container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        /* Unique Legend Styles */
        .custom-legend {
            font-size: 1.6rem;
            font-weight: 500;
            color: #444;
            margin-bottom: 15px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 5px;
        }

        /* Unique Form Group Styles */
        .custom-form-group {
            margin-bottom: 20px;
        }


        /* Unique Textarea Styles */
        .custom-textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f4f4f4;
            padding: 12px;
            font-size: 1rem;
            color: #333;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .custom-textarea:focus {
            border-color: #0066cc;
        }

        /* Unique Canvas Styles */
        .custom-canvas {
            width: 100%;
            height: 180px;
            border: 1px solid #bbb;
            border-radius: 6px;
            background-color: #fcfcfc;
            cursor: crosshair;
        }

        /* Unique Button Styles */
        .custom-button {
            background-color: #0066cc;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            margin-top: 10px;
        }

        .custom-button:hover {
            background-color: #004a99;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 102, 204, 0.3);
        }

        /* Hidden Input */
        .custom-hidden-input {
            display: none;
        }
        .info-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
            margin-left: 5px;
            width: 20px;
            height: 20px;
            background-color: #D9D9D9;
            border-radius: 50%;
            text-align: center;
        }

        .info-icon:hover .info-tooltip {
            display: block;
        }

        .info-tooltip {
            display: none;
            position: absolute;
            bottom: 120%;
            background-color: #D1ECF1;
            color: #000000;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 1;
            font-weight: normal;
            padding: 15px;
            border: 1px solid #94ABAF;
            max-width: 500px;
            font-size: 12px;
        }

        .info-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 2%;
            border-width: 5px;
            border-style: solid;
            border-color: #94ABAF transparent transparent transparent;
        }
        .icon-container {
            width: 40px;
            height: 40px;
            background-color: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #F4F4F4;
            border-right: 1px solid #D1D1D1;
            border-radius: 10px 0 0 10px;

        }

        /* Elimina la flecha del navegador */
        .select.no-arrow {
            -webkit-appearance: none; /* Para navegadores WebKit (Chrome, Safari) */
            -moz-appearance: none;    /* Para Firefox */
            appearance: none;         /* Estándar */

        }
        /* estilos imagenes */
        .input-containerr {
            display: flex;
            align-items: center;
            height: 45px;
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
            border: 1px solid #F4F4F4;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            padding: 0 15px; /* Espaciado interno uniforme */
            cursor: pointer;
            font-size: 13px;
            font-weight: normal;
            height: 100%; /* Alineación completa con el contenedor */
            gap: 15px; /* Espacio entre el ícono y el texto */
            background-color: transparent;
            align-items: center; /* Alinea verticalmente ícono y texto */
            justify-content: center; /* Centra contenido horizontalmente */
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .icon-containerr {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-pin {
            width: 20px; /* Ajustar según tus necesidades */
            height: 25px;
            color: #888;
        }


        .file-input {
            display: none;
        }
        .file-input-text {
            padding: 0.5rem; /* Ajustado el padding */
            border-radius: 0 10px 10px 0;
            border: none;
            width: 100%; /* Aseguramos que ocupe todo el espacio disponible */
            box-sizing: border-box;
            background-color: #F4F4F4; /* Fondo ajustado */
            color: #555555; /* Color del texto */
            height: 45px; /* Altura ajustada para que coincida con el icono */
            outline: none; /* Eliminar el resaltado al seleccionar */
            overflow: hidden; /* Evita que el texto sobresalga */
            text-overflow: ellipsis; /* Texto largo se mostrará con '...' */
            display: flex; /* Usar flexbox */
            align-items: center; /* Centrar verticalmente */
            justify-content: center; /* Centrar horizontalmente */
        }
        .preview-container {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #F9F9F9;
            max-width: 320px; /* Máximo ancho para una apariencia más ordenada */
            margin: 0 auto; /* Centrar el contenedor */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .video-preview video {
            border: 2px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
            max-width: 100%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);

            transition: transform 0.3s ease;
        }
        .video-preview video:hover {
            transform: scale(1.05); /* Efecto de hover para un diseño más moderno */
        }
        .image-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; /* Alinea las imágenes de forma ordenada */
        }

        .preview-contenedor {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #F9F9F9;
            max-width: 400px; /* Máximo ancho para una apariencia más ordenada */
            margin: 0 auto; /* Centrar el contenedor */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 5px;
        }

        /* Estilo de la imagen */
        .image-preview-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        /* Efecto hover aplicado individualmente a cada contenedor de imagen */
        .image-preview-container:hover img {
            transform: scale(1.1); /* Escalar la imagen individual al pasar el ratón */
        }

        .image-preview-container:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Agregar sombra al contenedor de la imagen */
        }


        .preview-title {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-weight: 500;
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            text-align: center;
        }
                .upload-message {
                    margin-top: 10px;
                    color: green;
                    color: #5cb85c; /* Verde para mensajes normales */
                    font-weight: bold;
                    /* Color de éxito */
                }

                .upload-message.error {
                    color: #d9534f; /* Rojo para errores */
                    font-weight: bold;
                    /* Color de error */
                }


        .remove-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.7);
            border: none;
            color: white;
            border-radius: 50%;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            font-weight: bold;
            line-height: 20px;
            text-align: center;
        }
        .cuadro {
            display: block;
            text-align: left; /* Asegura que los textos estén alineados a la izquierda */
            margin: 0; /* Elimina cualquier margen que pueda centrar el contenido */
            padding-left: 35px; /* Asegura que no haya padding a la izquierda */
            width: 90%; /* Asegura que los elementos ocupen todo el ancho disponible */
        }
        /* Estilos básicos */
        .menu-hamburguesa {
                    position: relative;
        }

        .menu-hamburguesa button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 24px;
        }

        .menu-hamburguesa .menu-items {
            display: none;
            position: absolute;
            top: 40px;
            left: 0;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            list-style: none;
            padding: 0;
            margin: 0;
            width: 150px;
        }

        .menu-hamburguesa .menu-items li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .menu-hamburguesa .menu-items li:last-child {
            border-bottom: none;
        }

        .menu-hamburguesa .menu-items li a {
            text-decoration: none;
            color: #333;
            display: block;
        }

        .menu-hamburguesa .menu-items li a:hover {
            background-color: #f0f0f0;
        }

        .menu-hamburguesa .menu-items.active {
            display: block;
        }
        /* General Modal Styles */
        .modal-content {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: none;
        }

        .modal-header {
                background-color: #1e6bb8;
                color: white;
                padding: 20px;
                text-align: center;
                font-size: 1.5rem;
                font-weight: bold;
        }

.modal-title {
    margin: 0;
}

.boton_cerrar {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: white;
    cursor: pointer;
}

.modal-body {
    padding: 20px;
    font-size: 1rem;
    color: #333;
}

.text-center {
    text-align: center;
}

.logo-modal {
    width: 80px;
    height: 80px;
    margin: 10px auto;
    border-radius: 50%;
    background-color: #f1f1f1;
    display: block;
    object-fit: cover;
}

.modal-footer {
    padding: 15px;
    background-color: #f9f9f9;
    border-top: 1px solid #e5e5e5;
    display: flex;
    justify-content: center;
    gap: 15px;
}

.boton_actualizar {
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.boton_actualizar img {
    width: 20px;
    height: 20px;
}

.boton_actualizar:hover {
    background-color: #164d84;
    transform: translateY(-3px);
}

.btn-primary {
    background-color: #1e6bb8;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-weight: bold;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary:hover {
    background-color: #164d84;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
    background-color: #f1f1f1;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px 20px;
    font-weight: bold;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-secondary:hover {
    background-color: #ddd;
    color: #000;
}

/* Responsive Adjustments */
@media (max-width: 576px) {
    .modal-content {
        padding: 15px;
    }

    .modal-header {
        font-size: 1.2rem;
        padding: 15px;
    }

    .modal-footer {
        flex-direction: column;
        gap: 10px;
    }

    .boton_actualizar,
    .btn-primary,
    .btn-secondary {
        width: 100%;
    }
}


    </style>
</head>
<body>
<div class="header-container">
        <div class="menu-hamburguesa">
            <button onclick="toggleMenu()">☰</button>
            <ul class="menu-items">
                <li><a href="{{ url('/') }}">Registro de Inventario</a></li>
                <li><a href="{{ url('/inventario') }}">Inventario</a></li>
            </ul>
        </div>
        <h1 class="titulos">Registro de Inventario</h1>
        <img src="{{ asset('images/Medibuy.png') }}" alt="Logo" class="logo">
    </div>
    <div class="gradient-bg-animation"></div>

    <!-- Contenedor para centrar -->
    <div class="contenedor-wrapper">
        <div class="contenedor_general">
        <form id="registroForm" action="{{ route('registro.guardar') }}" method="POST" enctype="multipart/form-data">

    @csrf


                <!-- Información del Equipo -->

                <div class="row text-start">
                                <div class="col-10" style="margin-top: 25px;">
                                    <h5 class="titulos_encabezado">Información del Equipo</h5>
                                </div>
                                <div class="row" style="margin: 0 auto; gap: 10px;">
   <div class="container">
    <!-- Primera fila -->
    <div class="row">
        <div class="col-md-4 col-12 mb-3">
            <label for="Tipo de Equipo" class="label_nomina d-block mb-1">Tipo de Equipo</label>
            <div class="form-group w-100">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/tipo.png') }}" alt="Tipo de Equipo" class="icon2">
                    </div>
                    <select class="form-control" id="tipoEquipo" name="Tipo de Equipo" style="background-color: #ffff; display:block;" value="{{ old('Tipo_de_Equipo') }}"  required>
                        <option value="">Selecciona un tipo de equipo</option>
                        <option value="endoscopia">Endoscopia</option>
                        <option value="laparoscopia">Laparoscopia</option>
                        <option value="quirofano">Quirofano</option>
                        <option value="hospitalizacion">Hospitalización</option>
                        <option value="cirujia">Cirujia</option>
                        <option value="artroscopia">Artroscopia</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12 mb-3" id="subtipoEquipoContainer" style="display: none;">
    <label for="Subtipo de Equipo" class="label_nomina align-self-start mb-1">Subtipo de Equipo</label>
    <div class="form-group">
        <div class="input_consulta" style="width: 100%;">
            <div class="icon-container2" style="border: none;">
                <img src="{{ asset('images/producto.png') }}" alt="Subtipo de Equipo" class="icon2">
            </div>
            <select class="form-control" id="subtipoEquipo" name="Subtipo de Equipo" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('Subtipo_de_Equipo') }}"required>
                <!-- Las opciones se cargarán dinámicamente -->
            </select>
        </div>
    </div>
</div>

<!-- Contenedor para especificar subtipo -->
<div class="col-md-4 col-12 mb-3" id="subtipoEquipoOtroContainer" style="display: none;">
    <label for="Subtipo de Equipo" class="label_nomina align-self-start mb-1">Especifica el Subtipo de Equipo</label>
    <div class="form-group">
        <div class="input_consulta" style="width: 100%;">
            <div class="icon-container2" style="border: none;">
                <img src="{{ asset('images/producto.png') }}" alt="Subtipo de Equipo" class="icon2">
            </div>
            <input type="text" class="form-control" id="subtipoEquipoOtro" name="Subtipo de Equipo Otro" placeholder="Especifica el subtipo" value="{{ old('Subtipo_de_Equipo_Otro') }}"style="background-color: #ffff; display:block; width: 100%;" />
        </div>
    </div>
</div>

        <div class="col-md-4 col-12 mb-3">
            <label for="Numero de Serie" class="label_nomina align-self-start mb-1">Número de Serie</label>
            <div class="form-group">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/serie.png') }}" alt="Número de Serie" class="icon2">
                    </div>
                    <input
                    type="text"
                    class="form-control"
                    style="background-color: #ffff; display:block; "
                    name="Numero de Serie"
                    placeholder="Número de Serie"
                    value="{{ old('Numero_de_Serie') }}"
                    required>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila -->
    <div class="row">
        <div class="col-md-3 col-12 mb-3">
            <label for="Marca" class="label_nomina align-self-start mb-1">Marca</label>
            <div class="form-group">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/marca.png') }}" alt="Marca" class="icon2">
                    </div>
                    <input
                    type="text"
                    class="form-control"
                    style="background-color: #ffff; display:block;"
                    name="Marca"
                    placeholder="Ej. Olympus, Storz"
                     value="{{ old('Marca') }}"
                    required>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-12 mb-3">
            <label for="Modelo" class="label_nomina align-self-start mb-1">Modelo</label>
            <div class="form-group">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/modelo.png') }}" alt="Modelo" class="icon2">
                    </div>
                    <input
                    type="text"
                    class="form-control"
                    style="background-color: #ffff; display:block;"
                    name="Modelo"
                    placeholder="Ej. CF-HQ190L"
                    value="{{ old('Modelo') }}"
                    required>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-12 mb-3">
    <label for="Año" class="label_nomina d-block mb-1">Año</label>
    <div class="form-group">
        <div class="input_consulta" style="width: 100%;">
            <div class="icon-container2" style="border: none;">
                <img src="{{ asset('images/Año.png') }}" alt="Año" class="icon2">
            </div>
            <input
                type="text"
                class="form-control"
                style="background-color: #ffff; display:block;"
                name="Año"
                placeholder="Ejemplo: 2023"
                maxlength="4"
                pattern="\d{4}"
                title="El año debe ser un número de 4 dígitos"
                value="{{ old('Año') }}"
            >
        </div>
    </div>
</div>


        <div class="col-md-4 col-12">
            <label for="descripcion" class="label_nomina d-block mb-1">Descripción</label>
            <div class="form-group">
                <textarea class="form-control select" name="descripcion" placeholder="Escribe aquí una descripción detallada del equipo" rows="6" required>{{ old('descripcion') }}</textarea>
            </div>
        </div>
    </div>
</div>


<div class="division"></div>
<!-- Estado y Uso -->
<div class="row text-start">
    <div class="col-10" style="margin-top: 17px;">
 <h5 class="titulos_encabezado">Evidencia</h5>
 </div>
<div class="row d-flex justify-content-between align-items-start" style="margin: 15px;">
    <!-- Estado Actual -->
    <div class="col-md-3 col-12 mb-3">
    <label for="estado_actual" class="label_nomina mb-1">Estado Actual</label>
    <div class="form-group">
        <select class="form-control custom-select select no-arrow" name="estado_actual" id="estado_actual" required>
            <option value="" selected disabled hidden>Seleccione una opción</option>
            <option value="1" {{ old('estado_actual') == 1 ? 'selected' : '' }}>En Stock</option>
            <option value="2" {{ old('estado_actual') == 2 ? 'selected' : '' }}>Vendido</option>
            <option value="3" {{ old('estado_actual') == 3 ? 'selected' : '' }}>En Mantenimiento</option>
        </select>
    </div>
</div>



    <!-- Fecha de Adquisición -->
    <div class="col-md-3 col-12 mb-3">
        <label for="fecha_inicial" class="label_nomina mb-1">Fecha de Adquisición</label>
        <div class="form-group">
            <input type="date" class="form-control select" id="fecha_inicial" name="fecha_inicial" value="{{ old('fecha_inicial') }}"required>
        </div>
    </div>

    <!-- Último Mantenimiento -->
    <div class="col-md-3 col-12 mb-3">
        <label for="fecha_mantenimiento" class="label_nomina mb-1">Último Mantenimiento</label>
        <div class="form-group">
            <input type="date" class="form-control select" id="fecha_mantenimiento" name="fecha_mantenimiento" value="{{ old('fecha_mantenimiento') }}" required>
        </div>
    </div>

    <!-- Próximo Mantenimiento -->
    <div class="col-md-3 col-12 mb-3">
        <label for="proximo_mantenimiento" class="label_nomina mb-1">Próximo Mantenimiento</label>
        <div class="form-group">
            <input type="date" class="form-control select" id="proximo_mantenimiento" name="proximo_mantenimiento" value="{{ old('proximo_mantenimiento') }}">
        </div>
    </div>



    <div class="form-grupo col-md-6">
        <div class="d-block w-100">
            <label for="evidencia" class="label_nomina mb-1">Fotos del equipo
                <div class="info-icon">?
                    <div class="info-tooltip text-center">Máximo 3.</div>
                </div>
            </label>
            <div class="d-flex align-items-center input-containerr">
                <label for="evidencia" class="file-input-label d-flex align-items-center">
                    <div class="icon-containerr">
                        <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                    </div>
                    <span>Seleccione archivos</span>
                </label>
              
                <input type="file" id="evidencia" name="evidencia[]" class="file-input" onchange="updateFileNames(this, 'file-input-text-images')" multiple accept="image/*">
                <div id="file-input-text-images" class="file-input-text">Sin selección</div>
            </div>

            <!-- Área de previsualización -->
            <div id="preview-container" class="preview-contenedor mt-3" style="display: none;">
                <h5 class="preview-title">Previsualización de las Imágenes:</h5>
                <div id="image-preview" class="image-preview"></div>
                <div id="message" class="upload-message"></div>
            </div>
        </div>
    </div>


<div class="form-grupo col-md-6">
    <div class="d-block w-100">
        <label for="video-evidencia" class="label_nomina mb-1">Video del equipo<div class="info-icon">?
                        <div class="info-tooltip text-center">Máximo 1.</div>
                    </div></label>
        <div class="d-flex align-items-center input-containerr">
            <label for="video-evidencia" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
                </div>
                <span>Seleccione archivos</span>
            </label>
            <input type="file" id="video-evidencia" name="video-evidencia" class="file-input"
                onchange="updateVideoPreview(this, 'file-input-text-video')" accept="video/*">
            <div id="file-input-text-video" class="file-input-text">Sin selección</div>
        </div>
        <!-- Área de previsualización -->
        <div id="video-preview-container" class="preview-container mt-3" style="display: none;">
            <h5 class="preview-title">Previsualización del Video:</h5>
            <div id="video-preview" class="video-preview"></div>
            <div id="video-message" class="upload-message"></div>
        </div>
    </div>
</div>

<div class="division"></div>

                <!-- Observaciones -->
                <h5 class="titulos_encabezado">Notas Adicionales</h5>
                 <!-- Campo para subir PDF -->
                 <div class="cuadro mt-4">
                 <div class="mb-4" style="max-width: 500px;">
    <label for="documentoPDF" class="label_nomina mb-1">
        Ficha Técnica
        <div class="info-icon">
            ?
            <div class="info-tooltip text-center">Solo archivos en formato PDF</div>
        </div>
    </label>
    <div class="d-flex align-items-start input-containerr"> <!-- Cambiado a align-items-start -->
        <label for="documentoPDF" class="file-input-label d-flex align-items-center">
            <div class="icon-containerr me-2">
                <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
            </div>
            <span id="file-label-text">Seleccione archivos</span>
        </label>
        <input 
            type="file" 
            id="documentoPDF" 
            name="documentoPDF" 
            class="file-input" 
            accept=".pdf" 
            onchange="mostrarNombreArchivo(this)"
            class="ms-2" 
        >
        <div id="file-input-text" class="file-input-text ms-2">Sin selección</div>
    </div>
</div>






    <!-- Observaciones -->
    <div class="mb-4">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea
            id="observaciones"
            name="observaciones"
            class="form-control select"
            placeholder="Espacio para comentarios adicionales"
            rows="4"
            required>{{ old('observaciones') }}</textarea>
    </div>

    <!-- Firma Digital -->
    <div class="mb-3">
        <label for="firmaCanvas" class="form-label">Firma Digital</label>
        <div class="border rounded mb-2" style="height: 150px; overflow: hidden;">
            <canvas id="firmaCanvas" class="w-100 h-100"></canvas>
        </div>
        <div class="text-end">
            <button id="limpiarFirma" class="btn btn-primary" type="button">Limpiar Firma</button>
        </div>
        <input type="hidden" id="firmaInput" name="firmaDigital"value="{{ old('firmaDigital') }}" />
    </div>

</div>
</div>

<body>



<script>
    const canvas = document.getElementById('firmaCanvas');
    const ctx = canvas.getContext('2d');
    const limpiarFirma = document.getElementById('limpiarFirma');
    const firmaInput = document.getElementById('firmaInput');
    let dibujando = false;

    function obtenerPosicionCanvas(event) {
        const rect = canvas.getBoundingClientRect();
        const x = (event.touches ? event.touches[0].clientX : event.clientX) - rect.left;
        const y = (event.touches ? event.touches[0].clientY : event.clientY) - rect.top;
        return { x, y };
    }

    function comenzarDibujo(event) {
        dibujando = true;
        const { x, y } = obtenerPosicionCanvas(event);
        ctx.beginPath();
        ctx.moveTo(x, y);
        event.preventDefault();
    }

    function detenerDibujo() {
        dibujando = false;
        ctx.beginPath();
        firmaInput.value = canvas.toDataURL();
    }

    function dibujar(event) {
        if (!dibujando) return;
        const { x, y } = obtenerPosicionCanvas(event);
        ctx.lineTo(x, y);
        ctx.strokeStyle = '#333';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.stroke();
        event.preventDefault();
    }

    canvas.addEventListener('mousedown', comenzarDibujo);
    canvas.addEventListener('mousemove', dibujar);
    canvas.addEventListener('mouseup', detenerDibujo);
    canvas.addEventListener('mouseout', detenerDibujo);
    canvas.addEventListener('touchstart', comenzarDibujo);
    canvas.addEventListener('touchmove', dibujar);
    canvas.addEventListener('touchend', detenerDibujo);
    canvas.addEventListener('touchcancel', detenerDibujo);

    limpiarFirma.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        firmaInput.value = '';
    });
</script>
<script>
function updateFileNames(input, textElementId) {
    const files = input.files;
    const imagePreview = document.getElementById('image-preview');
    const message = document.getElementById('message');
    const maxFiles = 3;

    const currentImagesCount = imagePreview.children.length;
    const selectedFilesCount = files.length;

    // Verificar límite de imágenes
    if (currentImagesCount + selectedFilesCount > maxFiles) {
        message.textContent = `Solo se permiten hasta ${maxFiles} imágenes.`;
        message.className = 'upload-message error';
        input.value = ''; // Limpia la selección
        return;
    }

    // Crear vista previa
    for (let i = 0; i < selectedFilesCount; i++) {
        const file = files[i];
        if (file && file.type.startsWith('image/')) {
            const imgContainer = document.createElement('div');
            imgContainer.classList.add('image-preview-container');
            imgContainer.style.position = 'relative';

            const img = document.createElement('img');
            const fileURL = URL.createObjectURL(file);
            img.src = fileURL;
            img.style.maxWidth = '100px';
            img.style.margin = '5px';
            img.onload = () => URL.revokeObjectURL(fileURL);

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '&times;';
            removeBtn.className = 'remove-btn';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '0';
            removeBtn.style.right = '0';
            removeBtn.style.background = 'red';
            removeBtn.style.color = 'white';
            removeBtn.style.border = 'none';
            removeBtn.style.borderRadius = '50%';
            removeBtn.style.cursor = 'pointer';

            removeBtn.onclick = function () {
                imgContainer.remove();
                updateFileCount();
                input.value = ''; // Restablecer el input
            };

            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            imagePreview.appendChild(imgContainer);
        }
    }

    // Actualizar texto y mostrar previsualización
    updateFileCount();
}

function updateFileCount() {
    const imagePreview = document.getElementById('image-preview');
    const textElement = document.getElementById('file-input-text-images');
    const totalFilesCount = imagePreview.children.length;

    textElement.textContent = totalFilesCount > 0
        ? `${totalFilesCount} archivo(s) seleccionado(s)`
        : 'Sin selección';

    const message = document.getElementById('message');
    message.textContent = totalFilesCount > 0
        ? 'Se han seleccionado imágenes.'
        : '';
    document.getElementById('preview-container').style.display = totalFilesCount > 0 ? 'block' : 'none';
}

</script>




<script>
function updateVideoPreview(input) {
    const file = input.files[0]; // Solo se permite un archivo
    const videoPreview = document.getElementById('video-preview');
    const message = document.getElementById('video-message');
    const previewContainer = document.getElementById('video-preview-container');
    const fileInputText = document.getElementById('file-input-text-video'); // Elemento para mostrar el nombre del archivo

    // Reiniciar vista previa
    videoPreview.innerHTML = '';
    message.textContent = '';

    // Validar si se seleccionó un archivo
    if (file) {
        // Comprobar si el archivo es un video
        if (file.type.startsWith('video/')) {
            // Mostrar el nombre del archivo seleccionado
            fileInputText.textContent = file.name;

            // Crear el elemento <video>
            const video = document.createElement('video');
            video.controls = true; // Habilitar controles del video
            video.width = 300; // Ancho del video
            video.src = URL.createObjectURL(file); // Crear URL para el video

            // Liberar memoria una vez que se haya cargado el video completamente
            video.onloadeddata = () => {
                video.onended = () => {
                    URL.revokeObjectURL(video.src);
                };
            };

            // Crear botón de eliminar
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '&times;'; // Tache (X)
            removeBtn.onclick = function () {
                // Eliminar el video y el botón de eliminar
                videoPreview.innerHTML = '';
                input.value = ''; // Limpiar el input
                previewContainer.style.display = 'none'; // Ocultar el contenedor de previsualización
                fileInputText.textContent = 'Sin selección';
            };

            // Crear contenedor para el video y el botón de eliminar
            const videoContainer = document.createElement('div');
            videoContainer.className = 'video-container';
            videoContainer.style.position = 'relative'; // Asegura que el botón se posicione sobre el video
            videoContainer.appendChild(video);
            videoContainer.appendChild(removeBtn);

            // Agregar el video y el botón de eliminar al contenedor de previsualización
            videoPreview.appendChild(videoContainer);

            message.textContent = 'Se ha seleccionado un video.';
            message.className = 'upload-message';

            // Mostrar el contenedor de previsualización
            previewContainer.style.display = 'block';
        } else {
            // Mostrar mensaje de error si el archivo no es un video
            message.textContent = 'El archivo seleccionado no es un video válido.';
            message.className = 'upload-message error';
            previewContainer.style.display = 'none';
            input.value = ''; // Limpiar el input
            fileInputText.textContent = 'Sin selección';
        }
    } else {
        // Restablecer si no hay archivo seleccionado
        previewContainer.style.display = 'none';
        fileInputText.textContent = 'Sin selección';
    }
}

</script>

</div>
</div>
</div>

<!-- Botones -->
<div class="form-grupo btn-container">
    <!-- Botón Guardar -->
    <button type="submit" class="btn btn-guardar enviar" id="submitBtn">
        <img src="{{ asset('images/guardar.png') }}" alt="Enviar icono" class="icono">
        Guardar
    </button>

    <!-- Botón Borrar -->
    <button type="reset" class="btn btn-borrar borrar" id="resetBtn">
        <img src="{{ asset('images/erase.png') }}" alt="Borrar icon" class="icono-borrar">
        Borrar
    </button>
</div>
<!-- Modal -->
<div class="modal fade" id="NutrimoilEnviado" tabindex="-1" role="dialog" aria-labelledby="NutrimoilEnviadoLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal">
                <h5 class="modal-title titulo_modal" id="addNoteModalLabel">Tu registro se ha guardado exitosamente.</h5>
                <button type="button" class="close boton_cerrar" onclick="cerrarModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.png') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center">El equipo se ha guardado. Puedes proceder a agregar otro o cerrar este mensaje <b>Grupo MediBuy</b>.</p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="boton_actualizar boton1" id="newNutrimailBtn">
                    <img class="imagen_boton_modal" src="{{ asset('images/agregar.png') }}" alt="Nuevo Nutrimail">
                    <span class="texto_boton_modal">Agregar Otro</span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Verifica si la sesión contiene el mensaje de éxito
        @if(session('success'))
            const modal = new bootstrap.Modal(document.getElementById('NutrimoilEnviado'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        @endif

        // Cierra el modal
        document.querySelector('.boton_cerrar').addEventListener('click', function () {
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('NutrimoilEnviado'));
            modalInstance.hide();
        });

        // Maneja el botón "Agregar Otro"
        document.getElementById('newNutrimailBtn').addEventListener('click', function () {
            // Refresca la página para borrar todo
            location.reload();
        });
    });
</script>

<script>
    // Cargar los archivos de sonido desde la ruta en public
    const clickSoundGuardar = new Audio('/assets/sounds/borrar.mp3');
    const clickSoundBorrar = new Audio('/assets/sounds/subir.mp3');

    // Agregar evento click al botón Guardar
    document.getElementById('submitBtn').addEventListener('click', () => {
        clickSoundGuardar.currentTime = 0; // Reinicia el sonido para reproducirlo desde el inicio
        clickSoundGuardar.play(); // Reproduce el sonido
    });

    // Agregar evento click al botón Borrar
    document.getElementById('resetBtn').addEventListener('click', () => {
        clickSoundBorrar.currentTime = 0; // Reinicia el sonido para reproducirlo desde el inicio
        clickSoundBorrar.play(); // Reproduce el sonido
    });
</script>
<script>
    // Seleccionar el botón reset
    document.getElementById('resetBtn').addEventListener('click', () => {
        // Limpiar el formulario
        const form = document.getElementById('registroForm');
        form.reset(); // Resetea los valores de todos los campos de entrada

        // Limpiar previsualización de video
        const videoPreview = document.getElementById('video-preview');
        const fileInputTextVideo = document.getElementById('file-input-text-video');
        const videoPreviewContainer = document.getElementById('video-preview-container');

        videoPreview.innerHTML = '';
        fileInputTextVideo.textContent = 'Sin selección';
        videoPreviewContainer.style.display = 'none';

        // Limpiar previsualización de imágenes
        const imagePreview = document.getElementById('image-preview');
        const fileInputTextImages = document.getElementById('file-input-text-images');
        const previewContainer = document.getElementById('preview-container');
        const message = document.getElementById('message');

        imagePreview.innerHTML = ''; // Eliminar todas las imágenes
        fileInputTextImages.textContent = 'Sin selección';
        message.textContent = ''; // Limpiar mensaje
        previewContainer.style.display = 'none'; // Ocultar contenedor de imágenes

        // Limpiar canvas de firma
        const canvas = document.getElementById('firmaCanvas');
        const ctx = canvas.getContext('2d');
        const firmaInput = document.getElementById('firmaInput');

        ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpiar el contenido del canvas
        firmaInput.value = ''; // Reiniciar el valor del input oculto

        // Opcional: Limpiar cualquier otro campo o acción relacionada
        // Si necesitas borrar alguna otra cosa que no se ha considerado, puedes agregarla aquí
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const yearInput = document.querySelector('input[name="Año"]');
    
    yearInput.addEventListener('input', () => {
        const value = yearInput.value;
        if (!/^\d{0,4}$/.test(value)) {
            yearInput.value = value.slice(0, -1); // Elimina el último carácter si no es válido
        }
    });
});

</script>

<script>
    // Definir los subtipo de equipo por categorías
        const tiposEquipos = {
        endoscopia: [
            "Adaptador USB", "Adaptador para Sonda", "Bomba de Irrigación", "Bomba de Secreción", "Boquillas", "Broncoscopio", 
            "Cable", "Cable Bipolar", "Cable USB", "Cámara con Cabezal", "Carrito", "Cepillo de Limpieza", "Colonoscopio", 
            "Contenedor de Liquidos", "Duodenoscopio", "Eliminador", "Fuente de Luz", "Gastroscopio", "Kit de Limpieza", 
            "Lineas de Irrigación", "Mause", "Pigtail", "Pigtel", "Pinza de Biopsia", "Pinza de Biopsia Hot", 
            "Pinza de Extracción", "Pinza de Polipectomia", "Probador", "Probador de Fuga", "Procesador", 
            "Proctector", "Sistema", "Sistema Endoscopia", "Tapon-ETO", "Teclado", "Video Carro"
        ],
        laparoscopia: [
            "Adaptador", "Adaptador Para Ligasure", "Armonico", "Cabezal", "Cable USB", "Camilla", "Charolas de Esterilización", 
            "Clips para Monitor", "Eliminador", "Fibra de Luz", "Forcetriad", "Fuente de Luz", "Insuflador", "Lampara XENON", 
            "Lente", "Maletin/Case", "Manguera de Insuflación", "Manguera para Bomba de Agua", "Manguera y Yugo", 
            "Monitor", "Pedestal", "Pieza de Mano", "Pinza", "Rasurador y Radio Frecuencia", "Set de Artroscopia", 
            "Trasmisor", "Trocar", "Video Carro", "Video Grabador", "Yugo"
        ],

        quirofano: [
            "Desfibrador", "Electrocauterio", "Eliminador", "Lámpara de Cirugía", "Lámpara de Quirofano", 
            "Máquina de Anestesia", "Mesa de Cirugía", "Monitor Signos Vitales"
        ],

        hospitalizacion: [
             "Aspirador", "Cama Hospitalaria Eléctrica", "Camilla", "Incubadora", "Mesa de Exploración"
        ],
        cirujia: [
            "Lapíz para Electrocauterio", "Placa para Electrocauterio", "Brazalete"
        ],
        artroscopia: [
            "Set de Taladros de Artroscopia"
        ],
        otros: [] // Dejar vacío si no tiene subtipo específico
    };

    // Función para actualizar los subtipo de equipo según la categoría seleccionada
    document.getElementById("tipoEquipo").addEventListener("change", function() {
    const tipoSeleccionado = this.value;
    const subtipoSelect = document.getElementById("subtipoEquipo");
    const subtipoContainer = document.getElementById("subtipoEquipoContainer");
    const subtipoOtroContainer = document.getElementById("subtipoEquipoOtroContainer");

    // Limpiar las opciones anteriores
    subtipoSelect.innerHTML = '<option value="">Selecciona un subtipo</option>';

    if (tipoSeleccionado) {
        if (tipoSeleccionado === "otros") {
            // Mostrar el campo de texto para "Otros"
            subtipoContainer.style.display = "none";
            subtipoOtroContainer.style.display = "block";
        } else {
            // Mostrar el select para subtipos específicos
            subtipoOtroContainer.style.display = "none";
            subtipoContainer.style.display = "block";

            // Agregar las opciones correspondientes
            const opciones = tiposEquipos[tipoSeleccionado];
            opciones.forEach(subtipo => {
                const option = document.createElement("option");
                option.value = subtipo.toLowerCase().replace(/\s+/g, '_'); // Formateo limpio
                option.textContent = subtipo;
                subtipoSelect.appendChild(option);
            });
        }
    } else {
        // Ocultar ambos contenedores si no se selecciona ningún tipo
        subtipoContainer.style.display = "none";
        subtipoOtroContainer.style.display = "none";
    }
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
        function toggleMenu() {
            const menu = document.querySelector('.menu-hamburguesa .menu-items');
            menu.classList.toggle('active');
        }
    </script>
    <script>
    function mostrarNombreArchivo(input) {
        const fileName = input.files.length > 0 ? input.files[0].name : 'Sin selección';
        document.getElementById('file-input-text').innerText = fileName;
    }
</script>


            </form>
        </div>
    </div>
</body>
</html>
