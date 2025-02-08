@extends('layouts.app')

@section('content')

    @csrf
<body>
<div class="container">
    <h2>.</h2>
    <div class="container">
    <div class="row">
        <!-- Columna de productos (lado izquierdo) -->
        <div class="col-md-9">
            <!-- Contenedor de búsqueda de registros -->
            <div class="card modern-card mt-3">
    <div class="card-header modern-header">Productos</div>
    <div class="card-body">
        <div class="dropdown">
            <input 
                type="text" 
                id="buscarRegistro" 
                class="form-control modern-input dropdown-toggle" 
                data-bs-toggle="dropdown" 
                placeholder="Buscar producto..."
            >
            <ul class="dropdown-menu modern-dropdown w-100" id="sugerencias">
                <li>
                    <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                        + Crear Producto
                    </button>
                </li>
                @foreach($productos as $producto)
                    <li>
                        <button class="dropdown-item modern-dropdown-item d-flex align-items-center" 
                                onclick="agregarProductoSeleccionado({{ $producto->id }}, '{{ $producto->tipo_equipo }}', '{{ $producto->modelo }}', '{{ $producto->marca }}', {{ $producto->precio }}, '{{ $producto->imagen }}')">
                            
                            <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}" class="modern-product-img me-2">
                            
                            <div class="flex-grow-1 modern-product-info">
                                <strong>{{ $producto->tipo_equipo }}</strong> - {{ $producto->modelo }} {{ $producto->marca }} 
                                <br>
                                <span class="text-muted modern-product-price">${{ $producto->precio }}</span>
                            </div>
                            
                            <span class="badge modern-badge">{{ $producto->stock }} unidades</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

   


<!-- Modal -->
<form id="formProducto" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-heade">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tipo de equipo -->
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="tipo_equipo" class="form-control" placeholder="Ej: Monitor" required>
                    </div>

                    <!-- Modelo y Marca -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" placeholder="Ej: Vision Pro" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ej: Stryker" required>
                        </div>
                    </div>

                    <!-- Existencias y Precio -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Existencias</label>
                            <input type="number" name="stock" class="form-control" value="1" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" name="precio" class="form-control" value="0.00" required min="0">
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">Imagen</label>
                        <div class="image-container">
                            <label for="image-upload" class="image-preview">
                                <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" 
                                     alt="Añadir imagen">
                                <span id="preview-text">Añadir imagen</span>
                            </label>
                            <input type="file" id="image-upload" name="imagen" accept="image/*" hidden>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear y Agregar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Contenedor de registros seleccionados -->
<div class="card modern-card mt-3">
    <div class="card-header modern-header">Productos Seleccionados</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="registrosSeleccionados" class="table modern-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Equipo</th>
                        <th>Modelo</th>
                        <th>Marca</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se agregarán los registros seleccionados -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex">
    <!-- Contenedor de totales -->
    <div class="card modern-card mt-3 w-50">
        <div class="card-header modern-header">Resumen</div>
        <div class="card-body">
            <p>Subtotal: <span id="subtotal" class="modern-value">0.00</span></p>
            <p>Descuento: 
                <input type="number" id="descuento" class="form-control modern-input w-25 d-inline-block" value="0">
            </p>
            <p>IVA (16%): <span id="iva" class="modern-value">0.00</span></p>
            <p>
                <label class="modern-checkbox">
                    <input type="checkbox" id="aplicarIva"> Aplicar IVA
                </label>
            </p>
            <p><strong>Total: <span id="total" class="modern-value">0.00</span></strong></p>
            
            <div>
                <label>Selecciona Plan:</label>
                <select id="tipoPago" class="form-control modern-input w-50">
                    <option value="estatico">Plan Fijo</option>
                    <option value="dinamico">Plan Flexible</option>
                    <option value="credito">Plan a Crédito</option>
                </select>
            </div>
            
            <div id="opcionesDinamicas" style="display: none;">
                <label>Pago Inicial:</label>
                <input type="number" id="pagoInicial" class="form-control modern-input w-50">
            </div>
            
            <div id="opcionesCredito" style="display: none;">
                <label>Pago Inicial:</label>
                <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50">
                <label>Plazo (meses):</label>
                <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6">
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <button id="generate-pdf" class="btn btn-primary">Generar PDF</button>
                <button class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>
    
    <!-- Contenedor de Plan de Pagos -->
    <div class="card modern-card mt-3 w-50 ms-3">
        <div class="card-header modern-header">Detalles del Financiamiento</div>
        <div class="card-body" id="plan-pagos"></div>
    </div>
</div>
</div>


<!-- Columna de cliente y detalles (lado derecho) -->
<div class="col-md-3 mt-3">
    <!-- Tarjeta de Cliente -->
    <div class="card modern-card">
        <div class="card-header modern-heade">Cliente</div>
        <div class="card-body">
            <div class="dropdown">
                <input 
                    type="text" 
                    id="search-client" 
                    class="form-control modern-input dropdown-toggle" 
                    data-bs-toggle="dropdown" 
                    placeholder="Buscar cliente..."
                >
                <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                    <li>
                        <button class="dropdown-item modern-dropdown-item" onclick="selectClient('Público en General')">
                            Público en General
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                            + Crear nuevo cliente
                        </button>
                    </li>
                    <!-- Aquí se insertarán dinámicamente los clientes -->
                </ul>
            </div>
        </div>
    </div>

    <hr>

    <!-- Tarjeta de Cotización -->
    <div class="card modern-card mb-3">
        <div class="card-header modern-heade">Cotización válida hasta</div>
        <div class="card-body">
            <input type="date" class="form-control modern-input">
        </div>
    </div>

    <hr>

    <!-- Tarjeta de Nota al Cliente -->
    <div class="card modern-card">
        <div class="card-header modern-heade">Nota al Cliente</div>
        <div class="card-body">
            <textarea class="form-control modern-textarea" rows="4" placeholder="Escribe una nota..."></textarea>
        </div>
    </div>
</div>

<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
<div class="modal fade" id="modal_formulario" tabindex="-1" role="dialog" aria-labelledby="FormularioLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-heade">
    <h5 class="modal-title" id="createClientModalLabel">Registrar Cliente</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form-cliente">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresar nombre" required>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingresar apellido" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ingresar teléfono" required>
              <span id="error-telefono" class="text-danger" style="display: none;">El teléfono ya está registrado.</span>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar email" required>
              <span id="error-email" class="text-danger" style="display: none;">El correo ya está registrado.</span>
            </div>
          </div>

          <div class="mb-3">
            <label for="comentarios" class="form-label">Comentarios</label>
            <textarea id="comentarios" name="comentarios" class="form-control" placeholder="Agrega información adicional de tu cliente"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="cliente_creado" tabindex="-1" role="dialog" aria-labelledby="ClienteCreadoLabel" 
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Cliente guardado exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                    El cliente se ha registrado correctamente en el sistema.  
                    Puedes proceder a cerrar este mensaje.  
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-listo" onclick="cerrarModal()">Listo</button>
            </div>
        </div>
    </div>
</div>


{{-- Modal de descarga PDF --}}
<div class="modal fade" id="modalPdf" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cotización Generada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>La cotización ha sido creada con éxito.</p>
                <a id="descargarPdf" class="btn btn-success" href="#">Descargar PDF</a>
            </div>
        </div>
    </div>
</div>
</body>
@endsection

@section('scripts')

<script>
function cerrarModal() {
  // Cerrar el modal
  var modalExito = new bootstrap.Modal(document.getElementById('cliente_creado'));
  modalExito.hide();

  // Asegurarte de que el formulario esté validado antes de enviarlo
  if (validarFormulario()) {
    // Aquí iría el código para enviar el formulario o hacer la solicitud Ajax.
  }
}
</script>




<script>
 document.addEventListener("DOMContentLoaded", function () {
  const createClientButton = document.querySelector('.dropdown-item[onclick="openCreateClientModal()"]');
  const modalFormulario = new bootstrap.Modal(document.getElementById("modal_formulario"));
  const modalExito = new bootstrap.Modal(document.getElementById("cliente_creado")); // Modal de éxito

  createClientButton.addEventListener("click", function () {
    modalFormulario.show();
  });

  // Manejar el envío del formulario
  const form = document.getElementById("form-cliente");

  form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevenir el envío tradicional del formulario

    // Limpiar mensajes de error previos
    const errorTelefono = document.getElementById("error-telefono");
    const errorEmail = document.getElementById("error-email");
    errorTelefono.style.display = "none";
    errorEmail.style.display = "none";
    errorTelefono.textContent = ""; // Limpiar texto previo
    errorEmail.textContent = ""; // Limpiar texto previo

    // Obtener datos del formulario
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const email = document.getElementById("email").value.trim();
    const comentarios = document.getElementById("comentarios").value.trim();

    // Validar campos requeridos
    if (!nombre || !apellido || !telefono) {
      alert("Todos los campos son obligatorios.");
      return;
    }

    // Verificar si el teléfono o el correo ya existen
    fetch("{{ route('clientes.check_unique') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        telefono: telefono,
        email: email,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Si no hay problemas de duplicado, enviar los datos al servidor
          fetch("{{ route('clientes.store') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
              nombre: nombre,
              apellido: apellido,
              telefono: telefono,
              email: email,
              comentarios: comentarios,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                // Cerrar el primer modal (modal_formulario)
                const modalFormularioElement = document.getElementById("modal_formulario");
                const bsModalFormulario = bootstrap.Modal.getInstance(modalFormularioElement);
                bsModalFormulario.hide(); // Esto debería cerrar el primer modal

                // Asegurarse de que el modal de éxito se muestre después de cerrar el primero
                modalExito.show();

                // Restablecer formulario
                form.reset();
              } else {
                alert(data.message || "Ocurrió un error al guardar el cliente.");
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              alert("Error al guardar el cliente.");
            });
        } else {
          // Mostrar mensajes de error en el formulario
          if (data.error_telefono) {
            errorTelefono.textContent = data.error_telefono;
            errorTelefono.style.display = "block";  // Asegura que se muestre
          }

          if (data.error_email) {
            errorEmail.textContent = data.error_email;
            errorEmail.style.display = "block";  // Asegura que se muestre
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error al verificar la existencia del teléfono o correo.");
      });
  });
});

// Función para cerrar el modal de éxito
function cerrarModal() {
  const modalExito = new bootstrap.Modal(document.getElementById("cliente_creado"));
  modalExito.hide(); // Cerrar el modal de éxito
}

</script>




<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-client");
        const clientList = document.getElementById("client-list");

        // Función para cargar clientes dinámicamente desde el backend
        function loadClients(search = "") {
            fetch(`/clientes?search=${search}`, {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
            })
                .then((response) => response.json())
                .then((clients) => {
                    // Limpiar la lista de clientes
                    clientList.innerHTML = `
                        <li><button class="dropdown-item" onclick="selectClient('Público en General')">Público en General</button></li>
                        <li><button class="dropdown-item" onclick="openCreateClientModal()">+ Crear nuevo cliente</button></li>
                    `;

                    // Agregar los clientes a la lista
                    clients.forEach((client) => {
                        const clientFullName = `${client.nombre} ${client.apellido}`;
                        const clientItem = document.createElement("li");
                        clientItem.innerHTML = `<button class="dropdown-item" onclick="selectClient('${clientFullName}')">${clientFullName}</button>`;
                        clientList.appendChild(clientItem);
                    });
                })
                .catch((error) => console.error("Error al cargar clientes:", error));
        }

        // Cargar todos los clientes inicialmente
        loadClients();

        // Escuchar el evento de escritura en el campo de búsqueda
        searchInput.addEventListener("input", function () {
            const search = searchInput.value.trim();
            loadClients(search); // Buscar clientes con el término ingresado
        });

        // Función para seleccionar un cliente
        window.selectClient = function (clientFullName) {
            searchInput.value = clientFullName; // Mostrar el nombre y apellido del cliente seleccionado en el campo de búsqueda
        };

        // Función para abrir el modal de creación de cliente
        window.openCreateClientModal = function () {
            const modalFormulario = new bootstrap.Modal(document.getElementById("modal_formulario"));
            modalFormulario.show();
        };
    });
</script>

  <script>
document.getElementById("formProducto").addEventListener("submit", function (event) {
    event.preventDefault(); // Evita la recarga de la página

    let formData = new FormData(this);

    fetch("{{ route('productos.store') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Accept": "application/json" // Importante para que Laravel devuelva JSON
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert("Producto creado con éxito");
            location.reload(); // Opcional: recargar para ver los cambios
        } else {
            alert("Error al crear el producto");
        }
    })
    .catch(error => console.error("Error:", error));
});

</script>




<script>
    // Lista para almacenar los productos seleccionados
    let productosSeleccionados = [];

    // Función para agregar un producto seleccionado
    function agregarProductoSeleccionado(id, tipoEquipo, modelo, marca, precio, imagen) {
        let productoExistente = productosSeleccionados.find(p => p.id === id);

        if (productoExistente) {
            // Si el producto ya está en la lista, aumentar la cantidad
            productoExistente.cantidad++;
            productoExistente.subtotal = productoExistente.cantidad * productoExistente.precio;
        } else {
            // Si no existe, agregarlo con cantidad inicial 1
            productosSeleccionados.push({
                id: id,
                tipo_equipo: tipoEquipo,
                modelo: modelo,
                marca: marca,
                precio: parseFloat(precio),
                imagen: imagen,
                cantidad: 1,
                subtotal: parseFloat(precio),
            });
        }

        // Actualizar la tabla y los totales
        actualizarTablaProductos();
        actualizarTotales();
    }

    // Función para actualizar la tabla con los productos seleccionados
    function actualizarTablaProductos() {
        let tabla = document.getElementById('registrosSeleccionados').getElementsByTagName('tbody')[0];
        tabla.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

        productosSeleccionados.forEach(producto => {
            let fila = tabla.insertRow();

            // Columna para la imagen
            let celdaImagen = fila.insertCell();
            let img = document.createElement('img');
            img.src = '/storage/' + producto.imagen;
            img.alt = 'Imagen del producto';
            img.style.width = '50px';
            celdaImagen.appendChild(img);

            // Tipo de Equipo
            let celdaTipoEquipo = fila.insertCell();
            celdaTipoEquipo.textContent = producto.tipo_equipo;

            // Modelo
            let celdaModelo = fila.insertCell();
            celdaModelo.textContent = producto.modelo;

            // Marca
            let celdaMarca = fila.insertCell();
            celdaMarca.textContent = producto.marca;

            // Cantidad con input para cambiarla
            let celdaCantidad = fila.insertCell();
            let inputCantidad = document.createElement('input');
            inputCantidad.type = 'number';
            inputCantidad.classList.add('form-control', 'cantidad-producto');
            inputCantidad.value = producto.cantidad;
            inputCantidad.min = 1;
            inputCantidad.dataset.id = producto.id; // Guardar el ID del producto para referencia
            inputCantidad.addEventListener('input', actualizarCantidad);
            celdaCantidad.appendChild(inputCantidad);

            // Subtotal
            let celdaSubtotal = fila.insertCell();
            celdaSubtotal.textContent = '$' + producto.subtotal.toFixed(2);
            celdaSubtotal.classList.add('subtotal-producto');
            celdaSubtotal.dataset.id = producto.id; // Guardar el ID del producto

            // Acción (Eliminar)
            let celdaAccion = fila.insertCell();
            let btnEliminar = document.createElement('button');
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.classList.add('btn', 'btn-danger');
            btnEliminar.dataset.id = producto.id;
            btnEliminar.onclick = function () {
                eliminarProductoSeleccionado(producto.id);
            };
            celdaAccion.appendChild(btnEliminar);
        });
    }

    // Función para actualizar la cantidad del producto seleccionado
    function actualizarCantidad(event) {
        let id = event.target.dataset.id;
        let nuevaCantidad = parseInt(event.target.value);

        if (nuevaCantidad < 1 || isNaN(nuevaCantidad)) {
            nuevaCantidad = 1; // No permitir valores menores a 1
            event.target.value = 1;
        }

        let producto = productosSeleccionados.find(p => p.id == id);
        if (producto) {
            producto.cantidad = nuevaCantidad;
            producto.subtotal = producto.cantidad * producto.precio;
        }

        // Actualizar la tabla y los totales
        actualizarTablaProductos();
        actualizarTotales();
    }

    // Función para eliminar un producto de la lista
    function eliminarProductoSeleccionado(id) {
        productosSeleccionados = productosSeleccionados.filter(p => p.id !== id);

        // Actualizar la tabla y los totales
        actualizarTablaProductos();
        actualizarTotales();
    }

    // Función para actualizar los totales (Subtotal, IVA, Total)
    function actualizarTotales() {
        let subtotal = productosSeleccionados.reduce((sum, p) => sum + p.subtotal, 0);
        let descuento = parseFloat(document.getElementById('descuento').value) || 0;
        let iva = 0;

        // Solo aplicar IVA si la casilla está marcada
        if (document.getElementById('aplicarIva').checked) {
            iva = (subtotal - descuento) * 0.16;
        }

        let total = subtotal - descuento + iva;

        
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('iva').textContent = '$' + iva.toFixed(2);
        document.getElementById('total').textContent = '$' + total.toFixed(2);
        actualizarPlanPagos(total);
    }

    
    function actualizarPlanPagos(total) {
    let planPagosDiv = document.getElementById('plan-pagos');
    planPagosDiv.innerHTML = '';
    let tipoPago = document.getElementById('tipoPago').value;
    let pagos = [];

    if (tipoPago === 'estatico') {
        if (total < 500000) {
            pagos.push({ cuota: (total * 0.5).toFixed(2), descripcion: 'Primer pago (50%)' });
            pagos.push({ cuota: (total * 0.25).toFixed(2), descripcion: 'Segundo pago (25%)' });
            pagos.push({ cuota: (total * 0.25).toFixed(2), descripcion: 'Tercer pago (25%)' });
        } else {
            let primerPago = total * 0.4;
            pagos.push({ cuota: primerPago.toFixed(2), descripcion: 'Primer pago (40%)' });
            let restante = total - primerPago;
            let numPagos = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
            let cuotaRestante = (restante / numPagos).toFixed(2);
            for (let i = 1; i <= numPagos; i++) {
                pagos.push({ cuota: cuotaRestante, descripcion: `Pago ${i + 1}` });
            }
        }
    } else if (tipoPago === 'dinamico') {
        let pagoInicial = parseFloat(document.getElementById('pagoInicial').value) || 0;
        let restante = total - pagoInicial;

        let numPagos;
        if (total < 150000) numPagos = 2;
        else if (total < 400000) numPagos = 4;
        else numPagos = 6;

        let cuotaRestante = (restante / numPagos).toFixed(2);
        pagos.push({ cuota: pagoInicial.toFixed(2), descripcion: 'Pago Inicial' });

        for (let i = 1; i <= numPagos; i++) {
            pagos.push({ cuota: cuotaRestante, descripcion: `Pago ${i} (Dinamico)` });
        }
    } else if (tipoPago === 'credito') {
        let pagoInicial = parseFloat(document.getElementById('pagoCreditoInicial').value) || 0;
        let plazo = parseInt(document.getElementById('plazoCredito').value) || 6;
        let tasaInteres = 0.05; // 5% mensual

        let montoCredito = total - pagoInicial;
        let totalCredito = montoCredito + (montoCredito * tasaInteres * plazo);
        let cuotaMensual = (totalCredito / plazo).toFixed(2);

        pagos.push({ cuota: pagoInicial.toFixed(2), descripcion: 'Pago Inicial' });
        pagos.push({ cuota: totalCredito.toFixed(2), descripcion: 'Total a pagar con crédito' });

        for (let i = 1; i <= plazo; i++) {
            pagos.push({ cuota: cuotaMensual, descripcion: `Pago ${i} (Crédito)` });
        }
    }

    pagos.forEach(pago => {
        let p = document.createElement('p');
        p.textContent = `${pago.descripcion}: $${pago.cuota}`;
        planPagosDiv.appendChild(p);
    });
}

document.getElementById('descuento').addEventListener('input', actualizarTotales);
document.getElementById('aplicarIva').addEventListener('change', actualizarTotales);
document.getElementById('tipoPago').addEventListener('change', function() {
    document.getElementById('opcionesDinamicas').style.display = this.value === 'dinamico' ? 'block' : 'none';
    document.getElementById('opcionesCredito').style.display = this.value === 'credito' ? 'block' : 'none';
    actualizarTotales();
});
document.getElementById('pagoInicial').addEventListener('input', actualizarTotales);
document.getElementById('pagoCreditoInicial').addEventListener('input', actualizarTotales);
document.getElementById('plazoCredito').addEventListener('input', actualizarTotales);

</script>


<script>
  document.getElementById('generatePdfButton').addEventListener("click", function () {
    // Asegúrate de que estos elementos existen y contienen los valores correctos
    const cliente = document.getElementById('searchInput').value;  // Aquí debes tomar el valor del input de cliente
    const telefono = '1234567890';  // Aquí deberías tomar el teléfono si está disponible
    const subtotalElement = document.getElementById('subtotalElement');  // Reemplaza con el id correcto del subtotal
    const ivaElement = document.getElementById('ivaElement');  // Reemplaza con el id correcto del IVA
    const totalElement = document.getElementById('totalElement');  // Reemplaza con el id correcto del total

    const subtotal = parseFloat(subtotalElement.textContent);
    const iva = parseFloat(ivaElement.textContent);
    const total = parseFloat(totalElement.textContent);

    // Asegúrate de que productosSeleccionados está definido y contiene los productos correctos
    const productosSeleccionados = [
        { nombre: 'Producto 1', cantidad: 2, precio: 50 },
        { nombre: 'Producto 2', cantidad: 1, precio: 30 }
    ];

    const productos = productosSeleccionados.map(producto => ({
        nombre: producto.nombre,
        cantidad: producto.cantidad,
        subtotal: producto.precio * producto.cantidad,
    }));

    // Define la variable cotizacionData correctamente
    const cotizacionData = {
        cliente: cliente,
        telefono: telefono,
        productos: productos,
        subtotal: subtotal,
        iva: iva,
        total: total
    };

    // Ahora se puede enviar cotizacionData al servidor
    fetch('/generar-cotizacion-pdf', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(cotizacionData)
    })
    .then(response => response.blob())
    .then(blob => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'cotizacion.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    })
    .catch(error => console.error('Error al generar el PDF:', error));
});

</script>
<script>
     document.getElementById('image-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewIcon = document.getElementById('preview-icon');
            const previewText = document.getElementById('preview-text');

            previewIcon.src = e.target.result;
            previewIcon.style.width = '100%';
            previewIcon.style.height = '100%';
            previewText.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

</script>

@endsection
