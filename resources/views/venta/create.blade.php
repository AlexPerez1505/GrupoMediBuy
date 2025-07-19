@extends('layouts.app')
@section('title', 'Remisión')
@section('titulo', 'Remisión')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="container"  style="margin-top: 80px;">
    <form id="form-venta" method="POST" action="{{ route('ventas.store') }}">
        <style>
            .swal2-popup.custom-swal {
    border-radius: 16px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 15px;
    color: #444;
    background-color: #fdfcff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    padding: 2rem;
}

.swal2-title.custom-title {
    font-size: 22px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: center;
}

.swal2-html-container.custom-html {
    text-align: left;
    line-height: 1.8;
    color: #555;
    padding: 0.5rem 1rem;
}

.swal2-confirm.custom-btn {
    background-color: #a78bfa;
    color: white !important;
    font-weight: 600;
    padding: 0.5rem 1.2rem;
    border-radius: 12px;
    font-size: 15px;
    box-shadow: 0 4px 10px rgba(167, 139, 250, 0.3);
    transition: all 0.2s ease-in-out;
}

.swal2-confirm.custom-btn:hover {
    background-color: #8b5cf6;
}

.swal-img-evidencia {
    width: 100%;
    max-height: 260px;
    object-fit: contain;
    border-radius: 12px;
    margin-top: 1rem;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
}

        </style>
        @csrf
        <div class="row">
            <div class="col-md-3 mt-3">
                <!-- Tarjeta de Cliente -->
 <div class="card modern-card mb-3">
    <div class="card-header modern-heade">Cliente</div>
    <div class="card-body">
        <div class="dropdown">
            <input 
                type="text" 
                id="search-client" 
                class="form-control modern-input dropdown-toggle" 
                data-bs-toggle="dropdown" 
                placeholder="Buscar cliente..."
                autocomplete="off"
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
                <!-- Aquí se insertarán dinámicamente los clientes -->
            </ul>
        </div>

        <!-- Campo oculto para enviar ID del cliente seleccionado -->
        <input type="hidden" name="cliente_id" id="cliente_id">

    </div>

    <!-- Detalles del cliente -->
    <div id="client-details" class="mt-3"></div>
</div>


                <!-- Tarjeta de Lugar de la Cotización -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Lugar de la Cotización</div>
                    <div class="card-body">
                        <select name="lugar" id="lugarCotizacion" class="form-control modern-select" required>
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
            <!-- Opción para crear producto -->
            <li>
                <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                    + Crear Producto
                </button>
            </li>

            <!-- Productos existentes -->
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

                        <!-- Imagen -->
                        <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}" class="modern-product-img me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">

                        <!-- Información del producto -->
                        <div class="flex-grow-1 modern-product-info">
                            <strong>{{ strtoupper($producto->tipo_equipo) }}</strong> - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}
                            <br>
                            <span class="text-muted modern-product-price">${{ number_format($producto->precio, 2) }}</span>
                        </div>

                        <!-- Stock -->
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
            <input type="hidden" name="productos_json" id="productos_json">
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
        <th>Número de Serie</th> {{-- NUEVO --}}
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
    <label for="carta_garantia_id">Carta de Garantía a incluir en el PDF:</label>
    <select name="carta_garantia_id" id="carta_garantia_id" class="form-control modern-input w-50" required>
        <option value="">-- Selecciona una carta --</option>
        @foreach ($cartas as $carta)
            <option value="{{ $carta->id }}">{{ $carta->nombre }}</option>
        @endforeach
    </select>
</div>
<br>

                                <input type="hidden" name="productos" id="productos_input">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                
                            </div>
                        </div>
                  

                    {{-- Detalles del financiamiento --}}
                  
                          <div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
    <div class="card-header modern-header">Detalles del Financiamiento</div>
    <div class="card-body" id="plan-pagos"></div>
</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
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
    const form = document.getElementById('form-venta');
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
    // Al enviar, prevenimos, validamos y preparamos JSON
    $('#form-venta').on('submit', function (e) {
        e.preventDefault();
        prepararProductosJSON();

        const productos = JSON.parse($('#productos_json').val());
        console.log('🧪 Enviando productos_json:', productos);

        // Validación: cada unidad debe tener un registro_id
        const algunoSinSerie = productos.some(p =>
            !Array.isArray(p.registro_id) ||
            p.registro_id.length !== p.cantidad ||
            p.registro_id.includes(null)
        );
        if (algunoSinSerie) {
            alert('Selecciona un número de serie para cada unidad.');
            return;
        }

        // Enviamos después de un breve retraso
        setTimeout(() => e.target.submit(), 100);
    });
});

// Carga registros en stock al enfocar cualquier select.numero-serie
$(document).on('focus', '.numero-serie', function () {
    const sel = $(this);
    if (sel.children().length > 1) return;
    $.get('/registros-disponibles', registros => {
        registros.forEach(r => {
            sel.append(`<option value="${r.id}">${r.numero_serie}</option>`);
        });
    });
});

// Evitar duplicados dentro de la misma fila
$(document).on('change', '.numero-serie', function () {
    const val = $(this).val(), row = $(this).closest('tr');
    let dup = false;
    row.find('.numero-serie').not(this).each(function () {
        if ($(this).val() === val && val !== '') dup = true;
    });
    if (dup) {
        alert('Número de serie duplicado en esta fila.');
        $(this).val('');
    }
});

// SweetAlert para detalles
$(document).on('click', '.ver-registro', function () {
    const sel = $(this).siblings('.numero-serie');
    const id = sel.val();
    if (!id) {
        Swal.fire('Atención', 'Primero elige un número de serie.', 'warning');
        return;
    }
    $.get(`/registro-info/${id}`, data => {
        Swal.fire({
            title: '🔍 Detalles del Registro',
            html: `
              <p><strong>Equipo:</strong> ${data.tipo_equipo || '—'}</p>
              <p><strong>Subtipo:</strong> ${data.subtipo_equipo || '—'}</p>
              <p><strong>Modelo:</strong> ${data.modelo || '—'}</p>
              <p><strong>Marca:</strong> ${data.marca || '—'}</p>
              <p><strong>Serie:</strong> ${data.numero_serie}</p>
              <p><strong>Estado:</strong> ${data.estado_proceso}</p>
              ${data.evidencia1
                ? `<img src="/storage/${data.evidencia1}" style="width:100%;max-height:200px;object-fit:contain;border-radius:8px;margin-top:8px;">`
                : `<em style="color:#999;">Sin evidencia</em>`}
            `,
            confirmButtonText: 'Cerrar',
            customClass: {
                popup: 'rounded-4 shadow',
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
            width: 500
        });
    }).fail(() => {
        Swal.fire('Error', 'No se pudo cargar la información.', 'error');
    });
});

// Agregar producto a la tabla
function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
    if (!id || !nombre) return;
    if ($(`#tabla-productos tbody tr[data-id="${id}"]`).length) {
        alert('Este producto ya ha sido agregado.');
        return;
    }
    const fila = `
      <tr data-id="${id}" data-precio="${precio}">
        <td><img src="/storage/${imagen}" width="50"></td>
        <td class="equipo">${nombre}</td>
        <td>${modelo}</td>
        <td>${marca}</td>
        <td><input type="number" class="form-control cantidad" value="1" min="1" onchange="actualizarSubtotal(this)"></td>
        <td class="subtotal">${precio.toFixed(2)}</td>
        <td><input type="number" class="form-control sobreprecio" value="0" min="0" onchange="actualizarSubtotal(this)"></td>
        <td><div class="serie-container"></div></td>
        <td><button type="button" class="btn btn-sm btn-danger eliminar-fila">Eliminar</button></td>
      </tr>`;
    $('#tabla-productos tbody').append(fila);
    const nueva = $('#tabla-productos tbody tr').last();
    generarSelects(nueva, 1);
    actualizarTotal();
}

// Eliminar fila
$(document).on('click', '.eliminar-fila', function () {
    $(this).closest('tr').remove();
    actualizarTotal();
});

// Actualizar subtotal + selects cuando cambia cantidad o sobreprecio
$(document).on('input change', '.cantidad, .sobreprecio', function () {
    actualizarSubtotal(this);
});

function actualizarSubtotal(el) {
    const tr = $(el).closest('tr');
    const qty = parseInt(tr.find('.cantidad').val()) || 1;
    const base = parseFloat(tr.data('precio'));
    const extra = parseFloat(tr.find('.sobreprecio').val()) || 0;
    const sub = (base + extra) * qty;
    tr.find('.subtotal').text(sub.toFixed(2));
    generarSelects(tr, qty);
    actualizarTotal();
}

// Recalcula totales
function actualizarTotal() {
    let subtotal = 0;
    $('#tabla-productos tbody tr').each(function () {
        subtotal += parseFloat($(this).find('.subtotal').text()) || 0;
    });
    const desc = parseFloat($('#descuento').val()) || 0;
    const envio = parseFloat($('#envio').val()) || 0;
    const iva = $('#aplica_iva').is(':checked') ? (subtotal - desc) * 0.16 : 0;
    const total = subtotal - desc + envio + iva;
    $('#subtotal').text(subtotal.toFixed(2));
    $('#subtotal_input').val(subtotal.toFixed(2));
    $('#iva').text(iva.toFixed(2));
    $('#iva_input').val(iva.toFixed(2));
    $('#total').text(total.toFixed(2));
    $('#total_input').val(total.toFixed(2));
}

// Genera tantos <select> + botón "ver" como unidades
function generarSelects(tr, n) {
    const cont = tr.find('.serie-container').empty();
    for (let i = 0; i < n; i++) {
        cont.append(`
          <div class="d-flex align-items-center gap-1 mb-1">
            <select class="form-control numero-serie">
              <option value="">Selecciona...</option>
            </select>
            <button type="button" class="btn btn-outline-info btn-sm ver-registro">
              <i class="bi bi-eye"></i>
            </button>
          </div>`);
    }
}

// Prepara JSON final
function prepararProductosJSON() {
    const arr = [];
    $('#tabla-productos tbody tr').each(function (i) {
        const tr = $(this);
        const pid = tr.data('id');
        const qty = parseInt(tr.find('.cantidad').val()) || 1;
        const pu = parseFloat(tr.data('precio'));
        const sp = parseFloat(tr.find('.sobreprecio').val()) || 0;
        const st = parseFloat(tr.find('.subtotal').text()) || 0;
        // Recolectar todos los registros
        const regs = tr.find('.numero-serie').map(function () {
            return $(this).val() || null;
        }).get();
        arr.push({ producto_id: pid, cantidad: qty, precio_unitario: pu, sobreprecio: sp, subtotal: st, registro_id: regs });
        console.log(`🧾 Producto ${i+1}:`, arr[i]);
    });
    $('#productos_json').val(JSON.stringify(arr));
    console.log('🧪 JSON generado:', arr);
}
</script>



<!-- Incluye esto en tu layout Blade o justo antes de cerrar </body> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-client");
    const clientList = document.getElementById("client-list");
    const clienteIdInput = document.getElementById("cliente_id");
    const clientDetails = document.getElementById("client-details");
    const formVenta = document.getElementById("form-venta");

    // Validación antes de enviar el formulario
    formVenta.addEventListener("submit", function (e) {
        console.log("cliente_id al enviar:", clienteIdInput.value); // Debug
        if (!clienteIdInput.value) {
            e.preventDefault();
            alert("Por favor selecciona un cliente antes de continuar.");
        }
    });

    // Función para cargar clientes dinámicamente desde el backend
    function loadClients(search = "") {
        fetch(`/buscar-clientes?search=${search}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then((response) => response.json())
        .then((clients) => {
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
                clients.forEach((client) => {
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
        .catch((error) => console.error("Error al cargar clientes:", error));
    }

    // Función para decodificar el cliente
    window.selectClientFromEncoded = function (encoded) {
        const client = JSON.parse(decodeURIComponent(encoded));
        selectClient(client);
    };

    // Función para seleccionar cliente
   window.selectClient = function (client) {
    console.log("Seleccionado:", client);
    console.log("ID del cliente:", client.id); // <-- Asegúrate de que esto NO sea undefined

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
    // Función para mostrar el modal
    window.openCreateClientModal = function () {
        const modalFormulario = new bootstrap.Modal(document.getElementById("modal_formulario"));
        modalFormulario.show();
    };

    // Eventos de búsqueda
    searchInput.addEventListener("input", () => {
        const search = searchInput.value.trim();
        loadClients(search);
        clientList.classList.add("show");
    });

    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            const search = searchInput.value.trim();
            loadClients(search);
            clientList.classList.add("show");
        }
    });

    loadClients(); // Carga inicial




    // ------------------------ LÓGICA DE CREACIÓN DE CLIENTE ------------------------
    const form = document.getElementById("form-cliente");
    const modalFormularioElement = document.getElementById("modal_formulario");
    const modalExitoElement = document.getElementById("cliente_creado");
    const modalFormulario = new bootstrap.Modal(modalFormularioElement);
    const modalExito = new bootstrap.Modal(modalExitoElement);

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        // Limpiar errores
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

        // Validar duplicados
        fetch("{{ route('clientes.check_unique') }}", {
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
                // Guardar nuevo cliente
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
                            loadClients(); // Recargar lista sin recargar página
                        }, { once: true });

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
            console.error("Error:", error);
            alert("Error al verificar la existencia del teléfono o correo.");
        });
    });

    modalExitoElement.addEventListener("hidden.bs.modal", function () {
        // Opcional: mantener sin recargar, ya se actualizó
        // location.reload();
    });
});
</script>

@endsection