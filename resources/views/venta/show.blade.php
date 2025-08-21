<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Remisión</title>

  <!-- Bootstrap + Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <link rel="stylesheet" href="{{ asset('css/remisiones.css') }}?v={{ time() }}">

  <style>
    /* ====== estilos existentes ====== */
    .pastel-progress-bar{height:100%;border-radius:18px;font-weight:bold;color:#555;display:flex;align-items:center;justify-content:center;transition:width .8s ease-in-out,background .4s ease-in-out}
    .table th,.table td{vertical-align:middle;padding:.75rem}
    .table-borderless th,.table-borderless td{border:none}
    .table thead{border-bottom:2px solid #eee}
    .card{background-color:#fefefe;border-radius:1rem}
    .card-header{background-color:transparent;border-bottom:none}

    /* ====== chips minimalistas unificados ====== */
    :root{
      --line:#e6ecf4;
      --text:#334155;
      --bg:#ffffff;
      --muted:#64748b;

      --brand:#2f5fb1;     /* azul suave */
      --brand-50:#eef5ff;
      --brand-100:#cfe2ff;

      --success:#1ECD97;   /* verde WA */
      --success-50:#effaf6;

      --danger:#dc2626;    /* rojo PDF */
      --danger-50:#fee2e2;
      --danger-100:#fecaca;
    }

    .page-header{
      display:flex; gap:16px; align-items:center; justify-content:space-between; flex-wrap:wrap;
    }

    .action-bar{
      display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    }

    .btn-chip{
      --bd: var(--line);
      --bg: var(--bg);
      --fg: var(--text);

      display:inline-flex; align-items:center; gap:.55rem;
      padding:.6rem 1rem; border-radius:999px;
      border:1px solid var(--bd); background:var(--bg); color:var(--fg);
      font-weight:600; line-height:1; text-decoration:none; cursor:pointer;
      transition: background .2s ease, color .2s ease, border-color .2s ease, box-shadow .2s ease, transform .02s ease;
      height:40px;
    }
    .btn-chip i{font-size:14px}
    .btn-chip:hover{ box-shadow:0 6px 16px rgba(20,40,80,.08) }
    .btn-chip:active{ transform:translateY(1px) }
    .btn-chip:focus-visible{ outline:2px solid var(--brand-100); outline-offset:2px }

    .btn-chip.-primary  { --bd: var(--brand-100);  --bg: var(--brand-50);  --fg: var(--brand); }
    .btn-chip.-success  { --bd: var(--success);    --bg: var(--success-50);--fg: var(--success); }
    .btn-chip.-danger   { --bd: var(--danger-100); --bg: var(--danger-50); --fg: #991b1b; }
    .btn-chip.-ghost    { --bd: var(--line);       --bg: #fff;             --fg: var(--text); }

    .btn-chip.-primary:hover { background:#e7f0ff }
    .btn-chip.-success:hover { background:#1ECD97; color:#fff }
    .btn-chip.-danger:hover  { background:#fecaca }

    .btn-chip .spinner{
      width:16px; height:16px; border:2px solid currentColor; border-right-color:transparent;
      border-radius:50%; display:none; animation: spin .6s linear infinite;
    }
    .btn-chip.is-loading .label{ visibility:hidden }
    .btn-chip.is-loading .spinner{ display:inline-block }

    @keyframes spin{ to{ transform:rotate(360deg) } }

    @media (max-width: 576px){
      .action-bar{ width:100% }
      .btn-chip{ flex:1 1 220px; justify-content:center }
    }
  </style>
</head>
<body>
  <div class="header-container">
    @auth
      <div class="back-button">
        <button onclick="window.history.back()" class="menu-icon" aria-label="Regresar">
          <img src="{{ asset('images/atras.png') }}" alt="Regresar">
        </button>
      </div>
    @endauth
    <h1 class="titulos">Remisión</h1>
    <div class="gradient-bg-animation"></div>
  </div>

  <div class="container mt-5">
    {{-- Mensajes de resultado WhatsApp --}}
    @if(session('wa_success'))
      <div class="alert alert-success border-0" style="border-radius:12px">{{ session('wa_success') }}</div>
    @endif
    @if(session('wa_info'))
      <div class="alert alert-warning border-0" style="border-radius:12px">
        {{ session('wa_info') }}
        @if(session('wa_fail'))
          <details class="mt-2">
            <summary>Ver detalles</summary>
            <pre style="white-space:pre-wrap;background:#f8fafc;border:1px dashed #e6ecf4;border-radius:10px;padding:10px;font-size:12px">{{ json_encode(session('wa_fail'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
          </details>
        @endif
      </div>
    @endif

    <!-- ====== Header con acciones ====== -->
    <div class="page-header mb-4">
      <h2 class="mb-0">Resumen de Venta N.2025-{{ $venta->id }}</h2>

      <div class="action-bar" role="toolbar" aria-label="Acciones de la remisión">
        <!-- PDF -->
        <a href="{{ route('ventas.pdf', $venta->id) }}"
           class="btn-chip -danger"
           target="_blank" rel="noopener" title="Descargar PDF">
          <i class="fa-solid fa-file-pdf"></i>
          <span class="label">Descargar PDF</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>

        <!-- WhatsApp por defecto: doc_pdf_utility_v2 · es_MX -->
        <form method="POST" action="{{ route('ventas.whatsapp.plantilla', $venta) }}" id="form-wa" class="m-0 d-inline">
          @csrf
          <input type="hidden" name="template_name" value="doc_pdf_utility_v2">
          <input type="hidden" name="template_lang"  value="es_MX">
          <button type="submit" id="btn-wa" class="btn-chip -success" title="Enviar por WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
            <span class="label">Enviar por WhatsApp</span>
            <span class="spinner" aria-hidden="true"></span>
          </button>
        </form>

        <!-- Etiqueta 4×8 -->
        <a href="{{ route('ventas.etiqueta', $venta->id) }}"
           class="btn-chip -ghost"
           target="_blank" rel="noopener" title="Imprimir etiqueta 4×8 con QR">
          <i class="fa-solid fa-tag"></i>
          <span class="label">Etiqueta 4×8 (QR)</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>

        <!-- Checklist -->
        <a href="{{ route('checklists.wizard', $venta->id) }}"
           class="btn-chip -primary"
           target="_blank" rel="noopener" title="Abrir checklist">
          <i class="fa-solid fa-clipboard-check"></i>
          <span class="label">Abrir checklist</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>
      </div>
    </div>

    {{-- ====== CONTENIDO ====== --}}
    <div class="row g-4">
      {{-- Datos de la venta --}}
      <div class="col-md-6 d-flex">
        <div class="card shadow-sm w-100 border-0 rounded-4">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">Datos de la Venta</h6>
          </div>
          <div class="card-body">
            <p><strong>Cliente:</strong> {{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</p>
            <p><strong>Teléfono:</strong> {{ mb_strtoupper($venta->cliente->telefono, 'UTF-8') }}</p>
            <p><strong>Dirección:</strong> {{ mb_strtoupper($venta->cliente->comentarios, 'UTF-8') }}</p>
            <p><strong>Asesor de venta:</strong> {{ mb_strtoupper($venta->usuario->name, 'UTF-8') }}</p>
            <p><strong>Teléfono:</strong> {{ $venta->usuario->phone }}</p>
            <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Plan:</strong> {{ mb_strtoupper($venta->plan, 'UTF-8') }}</p>
            <p><strong>Meses de Garantía:</strong> {{ $venta->meses_garantia ? $venta->meses_garantia . ' meses' : 'N/A' }}</p>
            <p><strong>Nota:</strong> {{ $venta->nota ? mb_strtoupper($venta->nota, 'UTF-8') : 'N/A' }}</p>
          </div>
        </div>
      </div>

      {{-- Productos --}}
      <div class="col-md-6 d-flex">
        <div class="card shadow-sm w-100 border-0 rounded-4">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">🛒 Productos Seleccionados</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-middle table-borderless mb-0">
                <thead class="border-bottom">
                  <tr class="text-muted small text-uppercase">
                    <th>Equipo</th>
                    <th>Descripción</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($venta->productos as $item)
                    <tr class="border-bottom">
                      <td>
                        <img src="{{ $item->producto ? asset('storage/' . $item->producto->imagen) : asset('images/imagen-no-disponible.png') }}"
                             alt="{{ $item->producto->nombre ?? 'Producto eliminado' }}"
                             class="rounded shadow-sm"
                             style="width:48px;height:48px;object-fit:cover;">
                      </td>
                      <td>
                        @if ($item->producto)
                          <span class="fw-semibold d-block">{{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}</span>
                          <small class="text-muted d-block">
                            {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                            {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
                          </small>
                          @if ($item->registro)
                            <small class="text-info-emphasis d-block mt-1"></i> Serie: {{ $item->registro->numero_serie }}</small>
                          @else
                            <small class="text-warning-emphasis d-block mt-1"><i class="fa-solid fa-circle-exclamation"></i> Sin número de serie</small>
                          @endif
                        @else
                          <span class="text-danger fst-italic">Producto eliminado</span>
                        @endif
                      </td>
                      <td class="text-center">{{ $item->cantidad }}</td>
                      <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              <hr class="my-3">
              <div class="d-flex flex-column gap-1 text-end small">
                <div><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</div>
                @if($venta->descuento > 0)
                  <div><strong>Descuento:</strong> <span class="text-warning">${{ number_format($venta->descuento, 2) }}</span></div>
                @endif
                @if($venta->envio > 0)
                  <div><strong>Envío:</strong> ${{ number_format($venta->envio, 2) }}</div>
                @endif
                <div><strong>IVA:</strong> ${{ number_format($venta->iva, 2) }}</div>
                <div class="fw-bold fs-5 mt-2 text-success-emphasis"><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <br>

    <div class="row g-4">
      {{-- Resumen financiero --}}
      <div class="col-md-6 d-flex">
        <div class="card shadow-sm w-100 border-0 rounded-4">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">Resumen financiero</h6>
          </div>
          <div class="card-body">
            <h5 class="card-title mb-3">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</h5>
            @php
              $pagosAprobados = $venta->pagosReales->filter(fn($p)=>$p->aprobado);
              $totalPagado = $pagosAprobados->sum('monto');
              $saldoRestante = $venta->total - $totalPagado;
              $porcentajePagado = min(100, round(($totalPagado / max(1,$venta->total)) * 100, 2));
              if ($porcentajePagado < 25)   $frase="¡Empezar es ganar, aunque sea poco!";
              elseif ($porcentajePagado < 50) $frase="Ya calentaste motores, sigue rodando.";
              elseif ($porcentajePagado < 75) $frase="Casi en la cima, no mires para atrás.";
              elseif ($porcentajePagado < 100) $frase="¡La meta se asoma, dale con todo!";
              else $frase="¡Listo! Has desbloqueado el modo campeón.";
              $tooltipText = "Has pagado $".number_format($totalPagado,2)." de $".number_format($venta->total,2).". ".$frase;
            @endphp

            <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
            <p><strong>Total pagado:</strong> ${{ number_format($totalPagado, 2) }}</p>
            <p><strong>Saldo restante:</strong> ${{ number_format($saldoRestante, 2) }}</p>

            <div class="mt-4">
              <label class="form-label fw-semibold mb-2 text-secondary-emphasis">
                Progreso del pago: <span id="porcentajeContador" class="fw-bold">{{ $porcentajePagado }}%</span>
              </label>
              <div class="progress position-relative shadow" style="height:26px;border-radius:18px;background:#f9f9f9;">
                <div class="progress-bar pastel-progress-bar" role="progressbar" id="barraProgreso"
                    data-porcentaje="{{ $porcentajePagado }}"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltipText }}"
                    aria-valuenow="{{ $porcentajePagado }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>

            <br>
            @if($pagos->every(fn($p)=>$p->pagado))
              <a href="{{ route('venta.recibo.final', $venta->id) }}" class="btn btn-success" target="_blank">
                Descargar Recibo Final
              </a>
            @endif
          </div>
        </div>
      </div>

      {{-- Plan de pagos --}}
      <div class="col-md-6 d-flex">
        <div class="card shadow-sm w-100 border-0 rounded-4">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">Plan de pagos</h6>
          </div>
          <div class="card-body">
            @if ($venta->plan === 'contado')
              <div class="col-12 mt-4">
                <div class="card border-start border-success border-4 shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <div class="me-3"><i class="fas fa-money-check-alt fa-2x text-success"></i></div>
                    <div>
                      <h5 class="mb-1 text-success fw-bold">Pago registrado correctamente</h5>
                      <p class="mb-0 text-muted">Gracias por su compra. El pago se realizó en una sola exhibición.</p>
                    </div>
                  </div>
                </div>
              </div>
            @else
              @php
                $pagoInicial = $pagos->first(fn($p)=>strtolower(trim($p->descripcion))==='pago inicial');
                $montoInicial = $pagoInicial->monto ?? 0;
                $pagosMensuales = $pagos->filter(fn($p)=>strtolower(trim($p->descripcion))!=='pago inicial');
                $plazoMeses = $pagosMensuales->count();
                $total = $venta->total ?? 0;
                $montoFinanciadoBase = $total - $montoInicial;
                $montoConIntereses = $pagosMensuales->sum('monto');
                function moneda($n){return '$'.number_format($n,2,'.',',');}
              @endphp

              <div class="mb-4">
                <h3 style="color:#1e73be;font-weight:bold;border-bottom:2px solid #1e73be;padding-bottom:4px">Resumen del Financiamiento</h3>
                <table class="table table-sm mt-3">
                  <tr><td><strong>Total de la venta:</strong></td><td>{{ moneda($total) }}</td></tr>
                  <tr><td><strong>Pago inicial:</strong></td><td>{{ moneda($montoInicial) }}</td></tr>
                  <tr><td><strong>Monto financiado (sin intereses):</strong></td><td>{{ moneda($montoFinanciadoBase) }}</td></tr>
                  <tr><td><strong>Plazo:</strong></td><td>{{ $plazoMeses }} {{ Str::plural('meses',$plazoMeses) }}</td></tr>
                  @if($venta->plan === 'credito')
                    <tr><td><strong>Tasa de interés mensual:</strong></td><td>5%</td></tr>
                    <tr><td><strong>Total a pagar con intereses:</strong></td><td class="text-primary fw-bold">{{ moneda($montoConIntereses) }}</td></tr>
                  @endif
                </table>
              </div>

              @php
                $mostrarColumnaDocumento = $pagos->contains(fn($p)=>$p->documentos && $p->documentos->isNotEmpty());
              @endphp

              <h5 class="card-title mb-3">Pagos</h5>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Descripción</th>
                      <th>Fecha</th>
                      <th>Monto</th>
                      <th>Recibo</th>
                      @if($mostrarColumnaDocumento)<th>Factura</th>@endif
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($pagos as $pagoFin)
                      <tr>
                        <td>{{ $pagoFin->descripcion ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($pagoFin->fecha_pago)->format('d/m/Y') }}</td>
                        <td>${{ number_format($pagoFin->monto, 2) }}</td>
                        <td>
                          @if($pagoFin->pago && $pagoFin->pago->aprobado)
                            <a href="{{ route('pagos.recibo', $pagoFin->pago->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">Ver</a>
                          @else
                            <span class="text-muted small">—</span>
                          @endif
                        </td>
                        @if($mostrarColumnaDocumento)
                          <td>
                            @if($pagoFin->documentos && $pagoFin->documentos->isNotEmpty())
                              <a href="{{ Storage::url($pagoFin->documentos->first()->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-regular fa-file-pdf"></i> Abrir
                              </a>
                            @else
                              <span class="text-muted small">—</span>
                            @endif
                          </td>
                        @endif
                        <td>
                          @if($pagoFin->pago && $pagoFin->pago->aprobado)
                            <span class="badge bg-success">Pagado</span>
                          @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="{{ $mostrarColumnaDocumento ? 6 : 5 }}" class="text-center">No hay pagos programados</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <hr>
    <p class="fw-semibold small text-center text-secondary-emphasis mt-3">
      Si tienes duda, queja o aclaración, manda mensaje al
      <a href="tel:+522724485191" class="fw-bold text-decoration-none">+52 722 448 5191</a>
      o al correo
      <a href="mailto:compras@grupomedibuy.com" class="fw-bold text-decoration-none">compras@grupomedibuy.com</a>.
    </p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Tooltips + Progreso -->
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const tt = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tt.map(el => new bootstrap.Tooltip(el));

      const barra = document.getElementById('barraProgreso');
      if (!barra) return;
      const porcentaje = parseInt(barra.dataset.porcentaje || 0);
      const contador = document.getElementById('porcentajeContador');
      let actual = 0; let color;
      if (porcentaje < 50) color='linear-gradient(90deg,#f7b3b3,#f48f8f)';
      else if (porcentaje < 100) color='linear-gradient(90deg,#fff3b0,#ffdb58)';
      else color='linear-gradient(90deg,#b0f2b6,#7ed6a3)';
      barra.style.background = color;

      const anim = setInterval(()=>{
        if (actual >= porcentaje){ clearInterval(anim); return; }
        actual++; barra.style.width = actual+'%'; contador.textContent = actual+'%';
      }, 15);
    });
  </script>

  <!-- UX botones: spinner al enviar / abrir -->
  <script>
    // Botón WA: loading al enviar
    const formWa = document.getElementById('form-wa');
    const btnWa  = document.getElementById('btn-wa');
    formWa?.addEventListener('submit', function(){
      if (!btnWa) return;
      btnWa.classList.add('is-loading');
      btnWa.setAttribute('aria-busy','true');
      btnWa.disabled = true;
    });

    // Spinner visual al abrir links (PDF / etiqueta / checklist)
    document.querySelectorAll('.action-bar a.btn-chip').forEach(link=>{
      link.addEventListener('click', ()=> link.classList.add('is-loading'), { once:true });
    });
  </script>
</body>
</html>
