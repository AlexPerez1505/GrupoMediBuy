@extends('layouts.app')
@section('title', 'Cotización')
@section('titulo', 'Cotización')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="container" style="margin-top: 80px;">
    <meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .botones-compactos {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-guardar,
    .btn-regresar {
        padding: 0.4rem 1rem;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: background-color 0.2s ease-in-out;
    }

    .btn-guardar {
        background-color: #dcfce7;
        color: #15803d;
        border-color: #86efac;
    }

    .btn-guardar:hover {
        background-color: #4ade80;
        color: white;
    }

    .btn-regresar {
        background-color: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }

    .btn-regresar:hover {
        background-color: #f87171;
        color: white;
    }
</style>
    <form id="form-propuesta" method="POST" action="{{ route('propuestas.store') }}">
    @csrf
    <div class="row">
        <div class="col-md-3 mt-3">
            <!-- Tarjeta de Cliente -->
            <div class="card modern-card mb-3">
                <div class="card-header modern-heade">Cliente</div>
                <div class="card-body">
                    <div class="dropdown position-relative">
                        <input 
                            type="text" 
                            id="search-client" 
                            class="form-control modern-input dropdown-toggle" 
                            data-bs-toggle="dropdown" 
                            placeholder="Buscar cliente..."
                            autocomplete="off"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                        <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                            <li>
                                <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClient({
                                    id: 1,
                                    nombre: "Público en General",
                                    apellido: "",
                                    telefono: "",
                                    email: "",
                                    comentarios: ""
                                })'>
                                    Público en General
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                                    + Crear nuevo cliente
                                </button>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="cliente_id" id="cliente_id">
                </div>
                <div id="client-details" class="mt-3"></div>
            </div>
   
 


                <!-- Tarjeta de Lugar de la Propuesta -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Lugar de la Propuesta</div>
                    <div class="card-body">
                        <select name="lugar" id="lugarPropuesta" class="form-control modern-select" required>
                            <option value="">Selecciona un lugar...</option>
                            <option value="AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL">AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL</option>
                            <option value="AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL">AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL</option>
                            <option value="AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA">AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA</option>
                            <option value="AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA">
                                AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA
                            </option>
                            <option value="CVDL CONGRESO DE VETERINARIA">CVDL CONGRESO DE VETERINARIA</option>
                            <option value="AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA">AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA</option>
                            <option value="AMG SEMANA NACIONAL GASTRO">AMG SEMANA NACIONAL GASTRO</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <!-- Tarjeta de Nota al Cliente -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Nota al Cliente</div>
                    <div class="card-body">
                        <textarea name="nota" id="notaCliente" class="form-control modern-textarea" rows="4" placeholder="Escribe una nota..."></textarea>
                    </div>
                </div>

                <!-- Tarjeta de Registrado por -->
                <div class="card modern-card">
                    <div class="card-header modern-heade">Registrado por</div>
                    <div class="card-body">
                        @auth
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="{{ Auth::user()->name }}" readonly>
                        @else
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="Desconocido" readonly>
                        @endauth
                    </div>
                </div>
            </div>

            
<div class="col-md-9">
    <div class="card modern-card mt-3">
        <div class="card-header modern-header">Productos</div>
        <div class="card-body">

            <div class="dropdown">
                <input 
                    type="text" 
                    id="buscarProducto" 
                    class="form-control modern-input dropdown-toggle" 
                    data-bs-toggle="dropdown" 
                    placeholder="Buscar producto..." 
                    autocomplete="off"
                    onkeyup="filtrarProductos(this.value)"
                >

                <ul class="dropdown-menu modern-dropdown w-100" id="dropdownProductos">
                    <li>
                        <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                            + Crear Producto
                        </button>
                    </li>

                    @foreach($productos->sortBy('tipo_equipo') as $producto)
                        <li>
                            <button 
                                class="dropdown-item modern-dropdown-item d-flex align-items-center" 
                                onclick="agregarProductoDesdeDropdown(
                                    {{ $producto->id }},
                                    @json($producto->tipo_equipo),
                                    @json($producto->modelo),
                                    @json($producto->marca),
                                    {{ $producto->precio }},
                                    @json($producto->imagen)
                                )"
                            >
                                <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}" class="modern-product-img me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                <div class="flex-grow-1 modern-product-info">
                                    <strong>{{ strtoupper($producto->tipo_equipo) }}</strong> - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}
                                    <br>
                                    <span class="text-muted modern-product-price">${{ number_format($producto->precio, 2) }}</span>
                                </div>
                                <span class="badge bg-secondary modern-badge">{{ $producto->stock }} unidades</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="card modern-card mt-3">
        <div class="card-header modern-header">Productos Seleccionados</div>
        <div class="card-body">
            <div class="table-responsive">
                <input type="hidden" name="productos_json" id="productos_json" value="">
                
                <table id="tabla-productos" class="table modern-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Equipo</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Sobreprecio</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row">
        <div class="card modern-card mt-3 w-100 w-md-50">
            <div class="card-header modern-header">Resumen</div>
            <div class="card-body">
                <p>Subtotal: $<span id="subtotal" class="modern-value">0.00</span></p>
                <input type="hidden" name="subtotal" id="subtotal_input" value="0">

                <div class="form-group">
                    <label>Descuento</label>
                    <input type="number" name="descuento" id="descuento" value="0" class="form-control modern-input w-25 d-inline-block" onchange="actualizarTotal()">
                </div>
                <br>
                <div class="form-group">
                    <label>Envío</label>
                    <input type="number" name="envio" id="envio" value="0" class="form-control modern-input w-25 d-inline-block" onchange="actualizarTotal()">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="aplica_iva" onchange="actualizarTotal()">
                    <label class="form-check-label">Aplicar IVA (16%)</label>
                </div>
                <input type="hidden" name="iva" id="iva_input" value="0">
                <p>IVA: $<span id="iva">0.00</span></p>
                <p><strong>Total: $<span id="total">0.00</span></strong></p>
                <input type="hidden" name="total" id="total_input" value="0">

                <div class="form-group">
                    <label for="tipoPago">Selecciona Plan:</label>
                    <select id="tipoPago" name="plan" class="form-control modern-input w-50" required>
                        <option value="" selected disabled>Selecciona un plan</option>
                        <option value="contado">Pago de Contado</option>
                        <option value="personalizado">Plan Personalizado</option>
                        <option value="estatico">Plan Fijo</option>
                        <option value="dinamico">Plan Flexible</option>
                        <option value="credito">Plan a Crédito</option>
                    </select>
                </div>

                <div id="opcionesDinamicas" style="display: none; margin-top: 1rem;">
                    <label for="pagoInicial">Pago Inicial:</label>
                    <input type="number" id="pagoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                </div>

                <div id="opcionesCredito" style="display: none; margin-top: 1rem;">
                    <label for="pagoCreditoInicial">Pago Inicial:</label>
                    <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                    <label for="plazoCredito" style="margin-top: 0.5rem;">Plazo (meses):</label>
                    <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6" min="1" step="1">
                </div>

                <div id="opcionesPersonalizado" style="display: none; margin-top: 1rem;">
                    <label for="mesesPersonalizado">Selecciona el número de meses:</label>
                    <input type="number" id="mesesPersonalizado" class="form-control modern-input w-50" min="1" step="1" value="1">
                    <div id="listaPagosPersonalizados" class="mt-3"></div>
                </div>

                <input type="hidden" id="pagosJsonInput" name="pagos_json" value="">

                <br>
<div class="form-group mt-4">
    <label for="ficha_tecnica_id">Ficha Técnica a incluir en el PDF:</label>
    <select name="ficha_tecnica_id" id="ficha_tecnica_id" class="form-control modern-input w-50">
        <option value="">-- Selecciona una ficha técnica --</option>
        @foreach ($fichas as $ficha)
            <option value="{{ $ficha->id }}">{{ $ficha->nombre }}</option>
        @endforeach
    </select>
</div>

                <br>
             <div class="botones-compactos">
    <button type="submit" class="btn-guardar"> Guardar</button>
    <a href="{{ route('propuestas.index') }}" class="btn-regresar"> Regresar</a>
</div>
            </div>
        </div>

        <div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
            <div class="card-header modern-header">Detalles del Financiamiento</div>
            <div class="card-body" id="plan-pagos"></div>
        </div>
    </div>
</div>
            </div>
        </div>
    </form>
</div>

<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  <div class="modal fade" id="modal_formulario" tabindex="-1" role="dialog" aria-labelledby="FormularioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-heade">
          <h5 class="modal-title" id="createClientModalLabel">Registrar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control text-uppercase" id="nombre" name="nombre" placeholder="Ingresar nombre" required>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control text-uppercase" id="apellido" name="apellido" placeholder="Ingresar apellido" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control text-uppercase" id="telefono" name="telefono" placeholder="Ingresar teléfono" required>
              <span id="error-telefono" class="text-danger" style="display: none;">El teléfono ya está registrado.</span>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar email">
              <span id="error-email" class="text-danger" style="display: none;">El correo ya está registrado.</span>
            </div>
          </div>

          <div class="mb-3">
            <label for="comentarios" class="form-label">Dirección</label>
            <textarea id="comentarios" name="comentarios" class="form-control text-uppercase" placeholder="Agrega información de tu cliente"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  // Función para convertir texto en mayúsculas automáticamente
  document.querySelectorAll('.text-uppercase').forEach(input => {
      input.addEventListener('input', function () {
          this.value = this.value.toUpperCase();
      });
  });

  // Formateo del teléfono
  document.getElementById("telefono").addEventListener("input", function (event) {
      let valor = this.value.replace(/\D/g, ""); // Elimina caracteres no numéricos

      if (valor.length <= 10) {
          valor = valor.replace(/(\d{2})(\d{4})(\d{4})/, "$1 $2 $3");
      } else if (valor.length > 10) {
          valor = valor.replace(/(\d{2})(\d{2})(\d{4})(\d{4})/, "+$1 $2 $3 $4");
      }

      this.value = valor;
  });
</script>



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
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoPago = document.getElementById('tipoPago');
    const opcionesDinamicas = document.getElementById('opcionesDinamicas');
    const opcionesCredito = document.getElementById('opcionesCredito');
    const opcionesPersonalizado = document.getElementById('opcionesPersonalizado');
    const listaPagosPersonalizados = document.getElementById('listaPagosPersonalizados');
    const planPagosDiv = document.getElementById('plan-pagos');
    const pagoInicial = document.getElementById('pagoInicial');
    const pagoCreditoInicial = document.getElementById('pagoCreditoInicial');
    const plazoCredito = document.getElementById('plazoCredito');
    const mesesPersonalizado = document.getElementById('mesesPersonalizado');

    window.pagos = [];

    function obtenerTotal() {
        const totalTexto = document.getElementById('total')?.textContent || "0";
        return parseFloat(totalTexto.replace(/[$,]/g, '')) || 0;
    }

    function formatear(moneda) {
        return moneda.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    }

    function formatoFechaISO(date) {
        return date.toISOString().split('T')[0]; // yyyy-mm-dd
    }

    function actualizarPlanPagos(total) {
        planPagosDiv.innerHTML = '';
        window.pagos = [];

        let tipo = tipoPago.value;
        function formatoMoneda(valor) {
            return parseFloat(valor).toFixed(2);
        }

        let fechaActual = new Date();
        let fechaPago = new Date(fechaActual);
        let nombresPagos = ["Primer", "Segundo", "Tercer", "Cuarto", "Quinto", "Sexto", "Séptimo", "Octavo", "Noveno", "Décimo", "Undécimo", "Duodécimo"];

        if (total <= 0) {
            planPagosDiv.innerHTML = '<p style="color:red;">Total inválido o cero</p>';
            return;
        }

        const agregarPago = (cuota, fecha, descripcion) => {
            const fechaCopia = new Date(fecha);
            const fechaStr = fechaCopia.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });
            const fechaISO = formatoFechaISO(fechaCopia);
            window.pagos.push({ cuota: formatoMoneda(cuota), mes: fechaISO, descripcion });

            const p = document.createElement('p');
            p.innerHTML = `<strong>${descripcion} - ${fechaStr}:</strong> ${formatear(parseFloat(cuota))}`;
            planPagosDiv.appendChild(p);
        };

        if (tipo === 'estatico') {
            if (total < 500000) {
                let fechaIni = new Date(fechaPago);
                agregarPago(total * 0.5, fechaIni, 'Pago inicial');

                let fecha1 = new Date(fechaPago);
                fecha1.setMonth(fecha1.getMonth() + 1);
                agregarPago(total * 0.25, fecha1, 'Primer pago');

                let fecha2 = new Date(fechaPago);
                fecha2.setMonth(fecha2.getMonth() + 2);
                agregarPago(total * 0.25, fecha2, 'Segundo pago');
            } else {
                let fechaIni = new Date(fechaPago);
                let primerPago = total * 0.4;
                agregarPago(primerPago, fechaIni, 'Pago inicial');

                let restante = total - primerPago;
                let numPagos = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
                let cuotaRestante = restante / numPagos;

                for (let i = 0; i < numPagos; i++) {
                    let fecha = new Date(fechaPago);
                    fecha.setMonth(fecha.getMonth() + i + 1);
                    agregarPago(cuotaRestante, fecha, `${nombresPagos[i] || (i + 1)} pago`);
                }
            }
        } else if (tipo === 'dinamico') {
            let pagoIni = parseFloat(pagoInicial.value) || 0;
            if (pagoIni <= 0 || pagoIni >= total) {
                planPagosDiv.innerHTML = '<p style="color:red;">Pago inicial inválido</p>';
                return;
            }

            let restante = total - pagoIni;
            let numPagos = (total < 150000) ? 2 : (total < 400000) ? 4 : 6;
            let cuotaRestante = restante / numPagos;

            agregarPago(pagoIni, fechaPago, 'Pago inicial');

            for (let i = 0; i < numPagos; i++) {
                fechaPago.setMonth(fechaPago.getMonth() + 1);
                agregarPago(cuotaRestante, fechaPago, `${nombresPagos[i] || (i + 1)} pago`);
            }
        } else if (tipo === 'credito') {
            let pagoIni = parseFloat(pagoCreditoInicial.value) || 0;
            let plazo = parseInt(plazoCredito.value) || 6;
            if (pagoIni < 0 || pagoIni >= total) {
                planPagosDiv.innerHTML = '<p style="color:red;">Pago inicial de crédito inválido</p>';
                return;
            }
            if (plazo <= 0) {
                planPagosDiv.innerHTML = '<p style="color:red;">Plazo inválido</p>';
                return;
            }

            let tasaInteres = 0.05;
            let montoCredito = total - pagoIni;
            let totalCredito = montoCredito + (montoCredito * tasaInteres * plazo);
            let cuotaMensual = totalCredito / plazo;

            agregarPago(pagoIni, fechaPago, 'Pago inicial');

            const totalCreditoP = document.createElement('p');
            totalCreditoP.innerHTML = `<strong>Total a pagar con crédito:</strong> ${formatear(totalCredito)}`;
            planPagosDiv.appendChild(totalCreditoP);

            for (let i = 0; i < plazo; i++) {
                fechaPago.setMonth(fechaPago.getMonth() + 1);
                agregarPago(cuotaMensual, fechaPago, `${nombresPagos[i] || (i + 1)} pago`);
            }
        } else if (tipo === 'personalizado') {
            generarPagosPersonalizados(total);
        }else if (tipo === 'contado') {
    let fechaPagoUnico = new Date();
    agregarPago(total, fechaPagoUnico, 'Pago único');
}
    }

    function generarPagosPersonalizados(total) {
        let meses = parseInt(mesesPersonalizado.value) || 1;
        listaPagosPersonalizados.innerHTML = "";
        planPagosDiv.innerHTML = "";
        window.pagos = [];

        let pagoSugerido = total / (meses + 1);
        let fechaActual = new Date();
        let nombresPagos = ["Primer", "Segundo", "Tercer", "Cuarto", "Quinto", "Sexto", "Séptimo", "Octavo", "Noveno", "Décimo", "Undécimo", "Duodécimo"];

        for (let i = 0; i <= meses; i++) {
            let div = document.createElement('div');
            div.classList.add('mb-2');

            let mesPago = new Date(fechaActual);
            mesPago.setMonth(mesPago.getMonth() + i);
            let fechaStr = mesPago.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });

            let label = document.createElement('label');
            label.innerHTML = i === 0
                ? `<strong>Pago inicial - ${fechaStr}:</strong>`
                : `<strong>${nombresPagos[i - 1] || `${i + 1}°`} pago - ${fechaStr}:</strong>`;

            let inputDiv = document.createElement('div');
            inputDiv.classList.add('d-flex', 'align-items-center');

            let input = document.createElement('input');
            input.type = 'number';
            input.classList.add('form-control', 'modern-input', 'w-50');
            input.setAttribute('data-mes', i);
            input.value = pagoSugerido.toFixed(2);

            input.addEventListener('input', function () {
                input.dataset.modificado = "true";
                recalcularPagosPersonalizados();
            });

            inputDiv.appendChild(input);
            div.appendChild(label);
            div.appendChild(inputDiv);
            listaPagosPersonalizados.appendChild(div);
        }

        recalcularPagosPersonalizados();
    }

function recalcularPagosPersonalizados() {
    let total = obtenerTotal();
    let listaPagos = document.querySelectorAll('#listaPagosPersonalizados input');
    let sumaPagos = 0;
    let pagosNoEditados = [];
    let nombresPagos = ["Primer", "Segundo", "Tercer", "Cuarto", "Quinto", "Sexto", "Séptimo", "Octavo", "Noveno", "Décimo", "Undécimo", "Duodécimo"];

    listaPagos.forEach((input) => {
        if (!input.dataset.modificado) pagosNoEditados.push(input);
    });

    listaPagos.forEach(input => sumaPagos += parseFloat(input.value) || 0);

    if (Math.abs(sumaPagos - total) > 0.01 && pagosNoEditados.length > 0) {
        let diferencia = total - sumaPagos;
        let ajuste = diferencia / pagosNoEditados.length;

        pagosNoEditados.forEach(input => {
            let nuevo = (parseFloat(input.value) || 0) + ajuste;
            input.value = nuevo.toFixed(2);
        });
    }

    planPagosDiv.innerHTML = "";
    window.pagos = [];

    let fechaBase = new Date();
    listaPagos.forEach((input, index) => {
        let monto = parseFloat(input.value) || 0;
        let fechaPago = new Date(fechaBase);
        fechaPago.setMonth(fechaPago.getMonth() + index);
        const fechaStr = fechaPago.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });
        const fechaISO = formatoFechaISO(fechaPago);

        const descripcion = index === 0 ? "Pago inicial" : `${nombresPagos[index - 1] || (index + 1)} pago`;

        window.pagos.push({
            cuota: monto.toFixed(2),
            descripcion,
            mes: fechaISO
        });

        let p = document.createElement('p');
        p.innerHTML = `<strong>${descripcion} - ${fechaStr}:</strong> ${formatear(monto)}`;
        planPagosDiv.appendChild(p);
    });

    let sumaFinal = Array.from(listaPagos).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);
    let totalPagosP = document.createElement('p');
    totalPagosP.style.fontWeight = "bold";
    totalPagosP.textContent = Math.abs(sumaFinal - total) < 0.01
        ? `Total de pagos: ${formatear(sumaFinal)} ✅ (Coincide)`
        : `Total de pagos: ${formatear(sumaFinal)} ⚠️ (No coincide)`;
    planPagosDiv.appendChild(totalPagosP);
}


    tipoPago.addEventListener('change', function () {
        opcionesDinamicas.style.display = tipoPago.value === 'dinamico' ? 'block' : 'none';
        opcionesCredito.style.display = tipoPago.value === 'credito' ? 'block' : 'none';
        opcionesPersonalizado.style.display = tipoPago.value === 'personalizado' ? 'block' : 'none';
        actualizarPlanPagos(obtenerTotal());
    });

    [pagoInicial, pagoCreditoInicial, plazoCredito, mesesPersonalizado].forEach(input => {
        input?.addEventListener('input', () => actualizarPlanPagos(obtenerTotal()));
    });

    actualizarPlanPagos(obtenerTotal());
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-propuesta');
    const inputPagosJson = document.getElementById('pagosJsonInput');
    const selectPlan = document.getElementById('tipoPago');

    form.addEventListener('submit', function (e) {
        console.log('Form submit capturado');
        console.log('window.pagos:', window.pagos);
        console.log('selectPlan:', selectPlan);

        if (!selectPlan) {
            alert('Error: el select de plan no existe o tiene otro id.');
            e.preventDefault();
            return;
        }

        const planSeleccionado = selectPlan.value;
        console.log('planSeleccionado:', planSeleccionado);

        // ✅ Validación general: debe haber al menos un pago, sin importar el tipo de plan
        if (!window.pagos || window.pagos.length === 0) {
            alert('No hay pagos definidos, por favor selecciona o genera un plan de pagos.');
            e.preventDefault();
            return;
        }

        // ✅ Formatear los pagos y pasarlos al input oculto como JSON
        const pagosFormateados = window.pagos.map(pago => {
            return {
                ...pago,
                mes: pago.mes // Asegúrate que esté en formato 'YYYY-MM-DD'
            };
        });

        inputPagosJson.value = JSON.stringify(pagosFormateados);
        console.log('Pagos a enviar:', inputPagosJson.value);
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const buscarProducto = document.getElementById("buscarProducto");
    const dropdownProductos = document.getElementById("dropdownProductos");
    let controladorAbort = new AbortController();

    function realizarBusqueda(searchQuery) {
        controladorAbort.abort();
        controladorAbort = new AbortController();

        if (searchQuery.length > 1) {
            Promise.all([
                fetch("{{ route('productos.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json()),
                fetch("{{ route('paquetes.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json())
            ])
            .then(([productos, paquetes]) => {
                mostrarResultados(paquetes, productos);
            })
            .catch(error => {
                if (error.name !== "AbortError") {
                    console.error("Error en la búsqueda:", error);
                }
            });
        } else {
            restaurarListaOriginal();
        }
    }

    function mostrarResultados(paquetes, productos) {
        dropdownProductos.innerHTML = `
            <li>
                <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                    + Crear Producto
                </button>
            </li>
        `;

        if (paquetes.length > 0) {
            paquetes.forEach(paquete => {
                let li = document.createElement("li");
                li.innerHTML = `
                    <button class="dropdown-item modern-dropdown-item"
                            data-id="${paquete.id}"
                            data-productos='${JSON.stringify(paquete.productos)}'
                            onclick="agregarPaqueteDesdeData(this)">
                        📦 ${paquete.nombre.toUpperCase()} - Paquete
                    </button>
                `;
                dropdownProductos.appendChild(li);
            });
        }

        productos.sort((a, b) => a.tipo_equipo.localeCompare(b.tipo_equipo));

        if (productos.length > 0) {
            productos.forEach(producto => {
let li = document.createElement("li");
li.innerHTML = `
    <button class="dropdown-item modern-dropdown-item d-flex align-items-center"
            onclick="agregarProductoDesdeDropdown(${producto.id}, '${producto.tipo_equipo}', '${producto.modelo}', '${producto.marca}', ${producto.precio}, '${producto.imagen}')">
        <img src="/storage/${producto.imagen}" alt="${producto.tipo_equipo}" class="modern-product-img me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
        <div class="flex-grow-1 modern-product-info">
            <strong>${producto.tipo_equipo}</strong> - ${producto.modelo} ${producto.marca}
            <br>
            <span class="text-muted modern-product-price">$${producto.precio}</span>
        </div>
        <span class="badge modern-badge">${producto.stock} unidades</span>
    </button>
`;
dropdownProductos.appendChild(li);

            });
        }

        if (paquetes.length === 0 && productos.length === 0) {
            dropdownProductos.innerHTML += '<li><button class="dropdown-item text-muted">No se encontraron resultados</button></li>';
        }
    }

    function restaurarListaOriginal() {
        Promise.all([
            fetch("{{ route('productos.search') }}?search=").then(res => res.json()),
            fetch("{{ route('paquetes.search') }}?search=").then(res => res.json())
        ])
        .then(([productos, paquetes]) => {
            mostrarResultados(paquetes, productos);
        })
        .catch(error => console.error("Error al restaurar lista:", error));
    }

    buscarProducto.addEventListener("input", function () {
        realizarBusqueda(this.value.trim());
    });

    buscarProducto.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            realizarBusqueda(this.value.trim());
        }
    });

    buscarProducto.addEventListener("focus", function () {
        if (this.value.trim() === "") {
            restaurarListaOriginal();
        }
    });

    // Mostrar todos al iniciar
    restaurarListaOriginal();

    // Función global para insertar paquetes
    window.agregarPaqueteDesdeData = function (element) {
        let id = element.getAttribute("data-id");
        let productos = JSON.parse(element.getAttribute("data-productos"));
        agregarPaquete(id, productos);
    };
function agregarPaquete(paqueteId, productos) {
    productos.forEach(producto => {
        agregarProductoDesdeDropdown(
            producto.id,
            producto.tipo_equipo,
            producto.modelo,
            producto.marca,
            parseFloat(producto.precio),
            '/storage/' + producto.imagen // Ruta correcta para mostrar la imagen
        );
    });
  }
});
</script>
<script>
$(document).ready(function () {
    console.log('Documento listo.');

    // Preparar JSON al enviar formulario
    $('#form-propuesta').submit(function (e) {
        console.log('Submit detectado.');

        actualizarTotal();      // Primero recalcula los subtotales y totales
        prepararProductosJSON(); // Luego genera el JSON ya con datos correctos

        const productosJson = $('#productos_json').val();
        console.log('Valor de productos_json al enviar:', productosJson);

        if (!productosJson || productosJson === '[]') {
            e.preventDefault();
            alert('Debes agregar al menos un producto.');
            console.log('Productos JSON vacío, se detiene el submit.');
            return;
        }

        console.log('Productos JSON listo para enviar:', productosJson);
    });
});
function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
    console.log('Agregando producto:', { id, nombre, modelo, marca, precio, imagen });

    if (!id || !nombre) {
        console.log('ID o nombre vacío, no se agrega.');
        return;
    }

    if ($(`#tabla-productos tbody tr[data-id="${id}"]`).length > 0) {
        alert('Este producto ya ha sido agregado.');
        console.log('Producto duplicado, no se agrega.');
        return;
    }

    // Asegura que la ruta de la imagen esté bien
    let imagenRuta = imagen.includes('/storage') ? imagen : '/storage/' + imagen;

    const fila = `
        <tr data-id="${id}" data-precio="${precio}">
            <td>
                <img src="${imagenRuta}" alt="${nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
            </td>
            <td class="equipo">${nombre}</td>
            <td>${modelo}</td>
            <td>${marca}</td>
            <td>
                <input type="number" class="form-control cantidad" value="1" min="1" onchange="actualizarSubtotal(this)">
            </td>
            <td class="subtotal">${precio.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control sobreprecio" value="0" min="0" onchange="actualizarSubtotal(this)">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button>
            </td>
        </tr>
    `;

    $('#tabla-productos tbody').append(fila);
    console.log('Producto agregado a la tabla.');

    actualizarTotal();
    prepararProductosJSON();

    $('#buscarProducto').val('');
    $('.dropdown-menu').removeClass('show');
}

function eliminarFila(btn) {
    console.log('Eliminando fila.');
    $(btn).closest('tr').remove();
    actualizarTotal();
    prepararProductosJSON();
}

function actualizarSubtotal(input) {
    console.log('Actualizando subtotal.');
    const fila = $(input).closest('tr');
    const cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
    const sobreprecio = parseFloat(fila.find('.sobreprecio').val()) || 0;
    const precioBase = parseFloat(fila.attr('data-precio')) || 0;

    const nuevoSubtotal = cantidad * (precioBase + sobreprecio);
    fila.find('.subtotal').text(nuevoSubtotal.toFixed(2));

    actualizarTotal();
    prepararProductosJSON();
}
function actualizarTotal() {
    console.log('Calculando totales...');
    let subtotal = 0;

    $('#tabla-productos tbody tr').each(function () {
        const cantidad = parseFloat($(this).find('.cantidad').val()) || 1;
        const sobreprecio = parseFloat($(this).find('.sobreprecio').val()) || 0;
        const precioBase = parseFloat($(this).attr('data-precio')) || 0;

        const subtotalProducto = cantidad * (precioBase + sobreprecio);
        $(this).find('.subtotal').text(subtotalProducto.toFixed(2));
        subtotal += subtotalProducto;
    });

    const descuento = parseFloat($('#descuento').val()) || 0;
    const envio = parseFloat($('#envio').val()) || 0;

    // Validar descuento para que no sea mayor que subtotal
    const descuentoValidado = descuento > subtotal ? subtotal : descuento;

    // Base para IVA incluye envío
    let baseIVA = subtotal - descuentoValidado + envio;
    if (baseIVA < 0) baseIVA = 0;

    let iva = 0;
    if ($('#aplica_iva').is(':checked')) {
        iva = baseIVA * 0.16;
    }

    const total = subtotal - descuentoValidado + envio + iva;

    $('#subtotal').text(subtotal.toFixed(2));
    $('#subtotal_input').val(subtotal.toFixed(2));
    $('#iva').text(iva.toFixed(2));
    $('#iva_input').val(iva.toFixed(2));
    $('#total').text(total.toFixed(2));
    $('#total_input').val(total.toFixed(2));

    console.log('Subtotal:', subtotal.toFixed(2), 'IVA:', iva.toFixed(2), 'Total:', total.toFixed(2));
}

function prepararProductosJSON() {
    console.log('Preparando JSON de productos...');
    const productos = [];

    $('#tabla-productos tbody tr').each(function () {
        const fila = $(this);
        const producto = {
            producto_id: fila.data('id'),
            cantidad: parseFloat(fila.find('.cantidad').val()) || 1,
            precio_unitario: parseFloat(fila.attr('data-precio')) || 0,
            sobreprecio: parseFloat(fila.find('.sobreprecio').val()) || 0,
            subtotal: parseFloat(fila.find('.subtotal').text()) || 0, // Subtotal ya incluye sobreprecio
        };
        console.log('Producto encontrado:', producto);
        productos.push(producto);
    });

    $('#productos_json').val(JSON.stringify(productos));
    console.log('JSON generado:', productos);
}
</script>



<script>
    console.log("Ruta encontrar clientes:", `{{ route('clientes.encontrar') }}`);
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-client");
    const clientList = document.getElementById("client-list");
    const clienteIdInput = document.getElementById("cliente_id");
    const clientDetails = document.getElementById("client-details");
    const formVenta = document.getElementById("form-propuesta"); // Cambiado a form-propuesta

    if (!searchInput || !clientList || !clienteIdInput || !clientDetails || !formVenta) {
        console.error("Alguno de los elementos no se encontró. Revisa los IDs.");
        return;
    }

    // Validación antes de enviar el formulario
    formVenta.addEventListener("submit", function (e) {
        if (!clienteIdInput.value) {
            e.preventDefault();
            alert("Por favor selecciona un cliente antes de continuar.");
        }
    });

    // Función para cargar clientes desde backend
    function loadClients(search = "") {
        fetch(`http://192.168.1.248:8000/encontrar-clientes?search=${encodeURIComponent(search)}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(response => response.json())
        .then(clients => {
            clientList.innerHTML = `
                <li>
                    <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClientFromEncoded("${encodeURIComponent(JSON.stringify({
                        id: 1,
                        nombre: "Público en General",
                        apellido: "",
                        telefono: "",
                        email: "",
                        comentarios: ""
                    }))}")'>
                        Público en General
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                        + Crear nuevo cliente
                    </button>
                </li>
            `;

            if (clients.length === 0 && search !== "") {
                clientList.innerHTML += `
                    <li><button type="button" class="dropdown-item disabled">No se encontraron resultados</button></li>
                `;
            } else {
                clients.forEach(client => {
                    const clientFullName = `${client.nombre.toUpperCase()} ${client.apellido.toUpperCase()}`;
                    const encodedClient = encodeURIComponent(JSON.stringify(client));
                    const clientItem = document.createElement("li");
                    clientItem.innerHTML = `
                        <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClientFromEncoded("${encodedClient}")'>
                            ${clientFullName}
                        </button>
                    `;
                    clientList.appendChild(clientItem);
                });
            }
        })
        .catch(error => {
            console.error("Error al cargar clientes:", error);
        });
    }

    // Función para seleccionar cliente a partir del string codificado
    window.selectClientFromEncoded = function (encoded) {
        const client = JSON.parse(decodeURIComponent(encoded));
        selectClient(client);
    };

    // Función para actualizar UI con cliente seleccionado
    window.selectClient = function (client) {
        searchInput.value = `${client.nombre.toUpperCase()} ${client.apellido.toUpperCase()}`;
        clienteIdInput.value = client.id ?? "";

        clientDetails.innerHTML = `
            <p><strong>Nombre:</strong> ${client.nombre.toUpperCase()} ${client.apellido.toUpperCase()}</p>
            <p><strong>Teléfono:</strong> ${client.telefono || "No registrado"}</p>
            <p><strong>Email:</strong> ${client.email || "No registrado"}</p>
            <p><strong>Dirección:</strong> ${client.comentarios || "No registrado"}</p>
        `;
        clientDetails.style.padding = "15px";
        clientList.classList.remove("show");
    };

    // Abrir modal para crear nuevo cliente
    window.openCreateClientModal = function () {
        const modalFormulario = new bootstrap.Modal(document.getElementById("modal_formulario"));
        modalFormulario.show();
    };

    // Eventos para búsqueda dinámica
    searchInput.addEventListener("input", () => {
        const search = searchInput.value.trim();
        loadClients(search);
        clientList.classList.add("show");
    });

    searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            const search = searchInput.value.trim();
            loadClients(search);
            clientList.classList.add("show");
        }
    });

    // Carga inicial sin filtro
    loadClients();




    // ------------------------ LÓGICA DE CREACIÓN DE CLIENTE ------------------------
    const form = document.getElementById("form-cliente");
    const modalFormularioElement = document.getElementById("modal_formulario");
    const modalExitoElement = document.getElementById("cliente_creado");

    if (!form || !modalFormularioElement || !modalExitoElement) {
        console.error("Elementos de modal o formulario no encontrados. Revisa los IDs.");
        return;
    }

    const modalFormulario = new bootstrap.Modal(modalFormularioElement);
    const modalExito = new bootstrap.Modal(modalExitoElement);

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const errorTelefono = document.getElementById("error-telefono");
        const errorEmail = document.getElementById("error-email");
        errorTelefono.style.display = "none";
        errorEmail.style.display = "none";
        errorTelefono.textContent = "";
        errorEmail.textContent = "";

        const nombre = document.getElementById("nombre").value.trim();
        const apellido = document.getElementById("apellido").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const email = document.getElementById("email").value.trim();
        const comentarios = document.getElementById("comentarios").value.trim();

        if (!nombre || !apellido || !telefono) {
            alert("Todos los campos son obligatorios.");
            return;
        }

        fetch("{{ route('clientes.check-unique') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ telefono, email }),
        })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                fetch("{{ route('clientes.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ nombre, apellido, telefono, email, comentarios }),
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(modalFormularioElement).hide();
                        modalFormularioElement.addEventListener("hidden.bs.modal", function () {
                            modalExito.show();
                            loadClients();
                        }, { once: true });
                        form.reset();
                    } else {
                        alert(data.message || "Ocurrió un error al guardar el cliente.");
                    }
                })
                .catch((error) => {
                    console.error("Error al guardar el cliente:", error);
                    alert("Error al guardar el cliente.");
                });
            } else {
                if (data.error_telefono) {
                    errorTelefono.textContent = data.error_telefono;
                    errorTelefono.style.display = "block";
                }
                if (data.error_email) {
                    errorEmail.textContent = data.error_email;
                    errorEmail.style.display = "block";
                }
            }
        })
        .catch((error) => {
            console.error("Error al verificar duplicados:", error);
            alert("Error al verificar la existencia del teléfono o correo.");
        });
    });

    modalExitoElement.addEventListener("hidden.bs.modal", function () {
        // Opcional recarga
    });
});
</script>


