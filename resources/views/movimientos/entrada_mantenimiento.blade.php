@extends('layouts.app')
@section('title', 'Movimientos')
@section('titulo', 'Regreso')
@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<style>
      body{
        background: #F5FAFF;
    }
    .formulario-contenedor {
    background: #ffffff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
    text-align: center;
    animation: fade-in 0.5s ease-in-out;
    margin: auto; /* Centra el formulario */
    margin-top:110px;
    margin-bottom:50px;

}
.image-container {
    position: relative;
    width: 300px;  /* Ajusta el ancho como prefieras */
    height: 250px; /* Ajusta la altura como prefieras */
    overflow: hidden;
    border: 1px solid #ccc;
    margin: 10px;
}

.image-preview {
    display: block;
    width: 100%;
    height: 100%;
    position: relative;
    background-color: #f2f2f2;
    text-align: center;
    padding: 10px;
}

#preview-icon-1,
#preview-icon-2,
#preview-icon-3 {
    width: 100%;
    height: 100%;
    object-fit: cover;  /* Asegura que la imagen cubra todo el contenedor */
}

#preview-text-1,
#preview-text-2,
#preview-text-3 {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #888;
    font-size: 16px;
}

/* Alineación horizontal de los contenedores de imagen */
.image-container-wrapper {
    display: flex;
    justify-content: space-around; /* Espacio entre las imágenes */
    gap: 10px; /* Espacio entre los contenedores de imagen */
}

/* Media query para pantallas más pequeñas (por ejemplo, dispositivos móviles) */
@media (max-width: 768px) {
    .image-container-wrapper {
        flex-direction: column;  /* Cambia de fila a columna */
        align-items: center;  /* Alinea los elementos al centro */
    }

    .image-container {
        width: 100%;  /* Los contenedores ocupan todo el ancho disponible */
        margin: 5px 0; /* Espaciado vertical entre contenedores */
    }
}
/* Estilo para la lista de checkboxes */
.checkbox-list {
    margin: 15px 0;
    padding: 0;
    list-style: none;
}

.checkbox-list input[type="checkbox"] {
    margin-right: 10px;
    vertical-align: middle;
}

.checkbox-list label {
    font-size: 14px;
    color: #333;
    display: inline-block;
    margin-bottom: 10px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.checkbox-list label:hover {
    color: #0056b3; /* Color de texto al pasar el ratón */
}
/* Diseño del checkbox */
.checkbox-list input[type="checkbox"]:focus {
    outline: none;
    border-color: #0056b3;
}

.checkbox-list input[type="checkbox"]:checked {
    background-color: #0056b3;
    border-color: #0056b3;
}

/* Espaciado entre las casillas */
.checkbox-list input[type="checkbox"] + label {
    margin-right: 20px;
}

.checkbox-list input[type="checkbox"]:last-of-type {
    margin-bottom: 0;
}
.defecto-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .defecto-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1rem;
  }

  .checkbox-card {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background-color: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 60px;
  }

  .checkbox-card:hover {
    border-color: #2563eb;
    background-color: #eef2ff;
  }

  .checkbox-card input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    accent-color: #2563eb;
    margin-top: 0.2rem;
    flex-shrink: 0;
  }

  .checkbox-card label {
    font-size: 0.95rem;
    color: #374151;
    line-height: 1.3;
    cursor: pointer;
  }

  .titulo-defectos {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
  }

  /* Responsive: una columna en pantallas pequeñas */
  @media (max-width: 640px) {
    .defecto-container {
      grid-template-columns: 1fr;
    }
  }
  .btn-secondary, .btn-primary {
    display: flex;
    align-items: center;   /* Centra verticalmente */
    justify-content: center; /* Centra horizontalmente */
    border: none;
    border-radius: 8px;
    font-weight: bold;
    width: 130px;
    height: 45px;
    transition: background-color 0.3s;
}

.btn-secondary {
    background-color: #dc3545;
}

.btn-secondary:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.btn-primary {
    background-color: #1E6BB8;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.btn-secondary img, .btn-primary img {
    margin-right: 5px;  /* Espacio entre la imagen y el texto */
}

</style>
<div class="formulario-contenedor">
    <h5 class="card-title">Equipo ID: {{ $id }}</h5>
    <p class="card-text">Registro de <strong>entrada de mantenimiento</strong> del equipo.</p>

    <form action="{{ route('movimientos.guardar', $id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
        <input type="hidden" name="tipo_movimiento" value="entrada_mantenimiento">

 <!-- Checklist -->
<div class="form-group">
    <label class="titulo-defectos">Checklist al ingresar de nuevo a la planta</label>
    <div class="defecto-container">
        <div class="checkbox-card">
            <input type="checkbox" id="check-limpieza" name="checklist[]" value="Limpieza interna">
            <label for="check-limpieza">Limpieza interna</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-imagen" name="checklist[]" value="Prueba de imagen">
            <label for="check-imagen">Prueba de imagen</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-fugas" name="checklist[]" value="Prueba de fugas">
            <label for="check-fugas">Prueba de fugas</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-angulacion" name="checklist[]" value="Prueba de angulación">
            <label for="check-angulacion">Prueba de angulación</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-botones" name="checklist[]" value="Prueba de botones">
            <label for="check-botones">Prueba de botones</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-tubo" name="checklist[]" value="Inspección de tubo de inserción universal">
            <label for="check-tubo">Inspección de tubo de inserción universal</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-lente" name="checklist[]" value="Inspección de lente de objetivo">
            <label for="check-lente">Inspección de lente de objetivo</label>
        </div>

        <div class="checkbox-card">
            <input type="checkbox" id="check-ruber" name="checklist[]" value="Inspección de ruber">
            <label for="check-ruber">Inspección de ruber</label>
        </div>
    </div>
</div>

           

        <!-- Descripción -->
        <div class="form-group">
            <label for="descripcion" class="label_nomina">Descripción del proceso</label>
            <textarea class="form-control select" name="descripcion" placeholder="Describe el estado y acciones realizadas." style="height: 130px;" required></textarea>
        </div>

      
        <!-- Evidencias fotográficas -->
        <div class="form-group">
        <label  class="label_nomina">Fotos de evidencia
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 3 (una por campo).</div>
            </div>
            </div>
        </label>
        <div class="image-container-wrapper">
    <div class="image-container">
        <label for="image-upload-1" class="image-preview">
            <img id="preview-icon-1"   src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
            <span id="preview-text-1">Añadir imagen</span>
        </label>
        <input type="file" id="image-upload-1" name="evidencia1" accept="image/*" hidden>
    </div>

    <div class="image-container">
        <label for="image-upload-2" class="image-preview">
            <img id="preview-icon-2"  src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
            <span id="preview-text-2">Añadir imagen</span>
        </label>
        <input type="file" id="image-upload-2" name="evidencia2" accept="image/*" hidden>
    </div>

    <div class="image-container">
        <label for="image-upload-3" class="image-preview">
            <img id="preview-icon-3"  src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
            <span id="preview-text-3">Añadir imagen</span>
        </label>
        <input type="file" id="image-upload-3" name="evidencia3" accept="image/*" hidden>
    </div>
</div>
<br>
<div class="form-group">
     
<label for="video" class="label_nomina">Video de Evidencia
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 1.</div>
            </div>
            </div>
        </label>
        <div class="d-flex align-items-center input-containerr">
            <label for="video" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
                </div>
                <span>Seleccione archivos</span>
            </label>
            <input type="file" id="video" name="video" class="file-input"
                onchange="updateVideoPreview(this, 'file-input-text-video')" 
                accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime">
            <div id="file-input-text-video" class="file-input-text">Sin selección</div>
        </div>
        <!-- Área de previsualización -->
        <div id="video-preview-container" class="preview-containerrr mt-3" style="display: none;">
            <h5 class="preview-title">Previsualización del Video:</h5>
            <div id="video-preview" class="video-preview"></div>
            <div id="video-message" class="upload-message"></div>
        </div>

        <br>

        <!-- Botones -->
        <div style="display: flex;">
            <a href="/inventario/servicio" class="btn btn-secondary btn-custom" style="margin-right: 10px;">
                <img src="/images/regresa.png" alt="Icono Volver" style="width: 20px; height: 20px;">
                Volver
            </a>
            <button type="submit" class="btn btn-primary btn-custom" style="margin-left: 10px;">
                <img src="/images/like.png" alt="Icono Finalizar" style="width: 20px; height: 20px;">
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection


@section('scripts')
    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espera mientras se guarda el proceso.',
                imageWidth: 100,
                imageHeight: 100,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => {
                        Swal.fire({
                            title: '¡Proceso guardado!',
                            text: '{{ session('success') }}',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('inventarioservicio') }}";
                        });
                    }, 2000);
                }
            });
        @elseif(session('error'))
            Swal.fire({
                title: '¡Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('inventarioservicio') }}";
            });
        @endif
    });
    </script>
    <script>
document.getElementById("image-upload-1").addEventListener("change", function(event) {
    previewImage(event, "1");
});

document.getElementById("image-upload-2").addEventListener("change", function(event) {
    previewImage(event, "2");
});

document.getElementById("image-upload-3").addEventListener("change", function(event) {
    previewImage(event, "3");
});

function previewImage(event, index) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Cambia la imagen y el texto del contenedor correspondiente
            document.getElementById("preview-icon-" + index).src = e.target.result;
            document.getElementById("preview-text-" + index).style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
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
            // Mostrar mensaje minimalista en lugar del nombre del archivo
            fileInputText.textContent = 'Video seleccionado';

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
@endsection
