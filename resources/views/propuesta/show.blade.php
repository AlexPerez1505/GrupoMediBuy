<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cotización</title>

  <!-- Bootstrap + Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <!-- Tu hoja de estilos -->
  <link rel="stylesheet" href="{{ asset('css/remisiones.css') }}?v={{ time() }}">

  <style>
    /* ====== estilos de la página (resumidos) ====== */
    .pastel-progress-bar{height:100%;border-radius:18px;font-weight:bold;color:#555;display:flex;align-items:center;justify-content:center;transition:width .8s ease-in-out,background .4s ease-in-out}
    .table th,.table td{vertical-align:middle;padding:.75rem}
    .table-borderless th,.table-borderless td{border:none}
    .table thead{border-bottom:2px solid #eee}
    .card{background-color:#fefefe;border-radius:1rem}
    .card-header{background-color:transparent;border-bottom:none}

    /* ====== barra de acciones: chips minimalistas unificados ====== */
    :root{
      --line:#e6ecf4;
      --text:#334155;
      --bg:#ffffff;

      --brand:#2f5fb1;     /* azul */
      --brand-50:#eef5ff;
      --brand-100:#cfe2ff;

      --success:#1ECD97;   /* verde WA */
      --success-50:#effaf6;

      --danger:#dc2626;    /* rojo PDF */
      --danger-50:#fee2e2;
      --danger-100:#fecaca;
    }

    .page-header{display:flex;gap:16px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    .action-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:center}

    .btn-chip{
      --bd: var(--line); --bg: var(--bg); --fg: var(--text);
      display:inline-flex; align-items:center; gap:.55rem;
      padding:.6rem 1rem; border-radius:999px;
      border:1px solid var(--bd); background:var(--bg); color:var(--fg);
      font-weight:700; line-height:1; text-decoration:none; cursor:pointer;
      transition: background .2s ease, color .2s ease, border-color .2s ease, box-shadow .2s ease, transform .02s ease;
      height:44px; min-width:220px; justify-content:center;
    }
    .btn-chip i{font-size:14px}
    .btn-chip:hover{ box-shadow:0 6px 16px rgba(20,40,80,.08) }
    .btn-chip:active{ transform:translateY(1px) }
    .btn-chip:disabled{ opacity:.7; cursor:not-allowed }

    /* Variantes */
    .btn-chip.-danger { --bd: var(--danger-100); --bg: var(--danger-50); --fg:#991b1b; }
    .btn-chip.-danger:hover{ background:#fecaca }
    .btn-chip.-success{ --bd: var(--success); --bg: var(--success-50); --fg: var(--success); }
    .btn-chip.-success:hover{ background: var(--success); color:#fff }
    .btn-chip.-primary{ --bd: var(--brand-100); --bg: var(--brand-50); --fg: var(--brand); }
    .btn-chip.-ghost  { --bd: var(--line); --bg:#fff; --fg: var(--text); }

    /* spinner + estados */
    .btn-chip .spinner{
      width:16px;height:16px;border:2px solid currentColor;border-right-color:transparent;border-radius:50%;
      display:none; animation:spin .6s linear infinite;
    }
    .btn-chip.is-loading .spinner{ display:inline-block }
    .btn-chip .label{white-space:nowrap}
    @keyframes spin{ to{ transform:rotate(360deg) } }

    /* Responsivo */
    @media (max-width:576px){
      .action-bar{ width:100% }
      .btn-chip{ flex:1 1 100%; min-width:unset }
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
    <h1 class="titulos">Cotización</h1>
    <div class="gradient-bg-animation"></div>
  </div>

  <div class="container mt-5">
    {{-- Ocultamos banners y usamos el texto en el propio botón --}}
    @php
      $wa_ok   = session('wa_success');
      $wa_info = session('wa_info'); // por si quieres manejar error/advertencia
    @endphp

    <div class="page-header mb-4">
      <h2 class="mb-0">Resumen de Cotización N.2025{{ $propuesta->id }}</h2>

      <!-- Acciones -->
      <div class="action-bar" role="toolbar" aria-label="Acciones">
        <!-- PDF -->
        <a href="{{ route('propuestas.pdf', $propuesta->id) }}"
           class="btn-chip -danger js-async-btn"
           target="_blank" rel="noopener"
           data-default-label="Descargar PDF"
           data-busy-label="Abriendo…"
           data-done-label="Listo ✓">
          <i class="fa-solid fa-file-pdf"></i>
          <span class="label">Descargar PDF</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>

        <!-- WhatsApp (tpl fija doc_pdf_utility_v1 / es_MX) -->
        <form method="POST" action="{{ route('propuestas.whatsapp.plantilla', $propuesta) }}" id="form-wa" class="m-0">
          @csrf
          <input type="hidden" name="template_name" value="doc_pdf_utility_v1">
          <input type="hidden" name="template_lang"  value="es_MX">
          <button type="submit"
                  id="btn-wa"
                  class="btn-chip -success js-async-submit"
                  data-default-label="Enviar por WhatsApp"
                  data-busy-label="Enviando…"
                  data-done-label="Enviado ✓">
            <i class="fa-brands fa-whatsapp"></i>
            <span class="label">
              {{-- Si vienes de éxito, mostramos “Enviado ✓” de una vez --}}
              @if($wa_ok) Enviado ✓ @else Enviar por WhatsApp @endif
            </span>
            <span class="spinner" aria-hidden="true"></span>
          </button>
        </form>

        {{-- EJEMPLOS (si los necesitas en esta vista)
        <a href="{{ route('ventas.etiqueta', $propuesta->id) }}"
           class="btn-chip -ghost js-async-btn" target="_blank" rel="noopener"
           data-default-label="Etiqueta 4×8 (QR)"
           data-busy-label="Abriendo…"
           data-done-label="Listo ✓">
          <i class="fa-solid fa-tag"></i><span class="label">Etiqueta 4×8 (QR)</span><span class="spinner"></span>
        </a>

        <a href="{{ route('checklists.wizard', $propuesta->id) }}"
           class="btn-chip -primary js-async-btn" target="_blank" rel="noopener"
           data-default-label="Abrir checklist"
           data-busy-label="Abriendo…"
           data-done-label="Listo ✓">
          <i class="fa-solid fa-clipboard-check"></i><span class="label">Abrir checklist</span><span class="spinner"></span>
        </a>
        --}}
      </div>
    </div>

    {{-- ====== CONTENIDO (tu contenido original) ====== --}}
    <div class="row g-4">
      <div class="col-md-6 d-flex">
        <div class="card shadow-sm w-100 border-0 rounded-4">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h6 class="mb-0 text-primary-emphasis fw-semibold">📋 Información General</h6>
          </div>
          <div class="card-body">
            <p><strong>Cliente:</strong> {{ $propuesta->cliente->nombre }} {{ $propuesta->cliente->apellido }}</p>
            <p><strong>Teléfono:</strong> {{ $propuesta->cliente->telefono ?? 'No proporcionado' }}</p>
            <p><strong>Correo:</strong> {{ $propuesta->cliente->email ?? 'No proporcionado' }}</p>
            <p><strong>Dirección / Comentarios:</strong> {{ $propuesta->cliente->comentarios ?? 'Sin comentarios' }}</p>
            <p><strong>Lugar de Cotización:</strong> {{ $propuesta->lugar }}</p>
            <p><strong>Nota:</strong> {{ $propuesta->nota ?? 'Sin nota' }}</p>
            <p><strong>Realizada por:</strong> {{ $propuesta->usuario->name }}</p>
            <p><strong>Plan:</strong> {{ ucfirst($propuesta->plan) }}</p>
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
                  @foreach ($propuesta->productos as $item)
                    <tr class="border-bottom">
                      <td>
                        @php
                          $img = $item->producto?->imagen ? asset('storage/'.$item->producto->imagen) : asset('images/imagen-no-disponible.png');
                        @endphp
                        <img src="{{ $img }}" alt="{{ $item->producto->nombre ?? 'Producto eliminado' }}"
                             class="rounded shadow-sm" style="width:48px;height:48px;object-fit:cover;">
                      </td>
                      <td>
                        @if ($item->producto)
                          <span class="fw-semibold d-block">{{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}</span>
                          <small class="text-muted">
                            {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                            {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
                          </small>
                        @else
                          <span class="text-danger fst-italic">Producto eliminado</span>
                        @endif
                      </td>
                      <td class="text-center">{{ $item->cantidad }}</td>
                      <td class="text-end">${{ number_format((float)$item->subtotal, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              <hr class="my-3">
              <div class="d-flex flex-column gap-1 text-end small">
                <div><strong>Subtotal:</strong> ${{ number_format((float)$propuesta->subtotal, 2) }}</div>
                @if(($propuesta->descuento ?? 0) > 0)
                  <div><strong>Descuento:</strong> <span class="text-warning">${{ number_format((float)$propuesta->descuento, 2) }}</span></div>
                @endif
                @if(($propuesta->envio ?? 0) > 0)
                  <div><strong>Envío:</strong> ${{ number_format((float)$propuesta->envio, 2) }}</div>
                @endif
                <div><strong>IVA:</strong> ${{ number_format((float)$propuesta->iva, 2) }}</div>
                <div class="fw-bold fs-5 mt-2 text-success-emphasis"><strong>Total:</strong> ${{ number_format((float)$propuesta->total, 2) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== gráfico (igual que tenías) ===== --}}
    @php $pagosCol = $propuesta->pagosFinanciamiento ?? collect(); @endphp
    @if($pagosCol->count() > 0)
      <div class="row mt-4">
        <div class="col-md-6 d-flex">
          <div class="card shadow-sm w-100 border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 py-3">
              <h6 class="mb-0 text-primary-emphasis fw-semibold">💳 Plan de Pagos (Presupuesto)</h6>
            </div>
            <div class="card-body d-flex gap-4 flex-wrap">
              @php
                $pagoInicial = $pagosCol->first(fn($p) => strtolower(trim($p->descripcion))==='pago inicial');
                $montoInicial = $pagoInicial->monto ?? 0;
                $pagosMensuales = $pagosCol->filter(fn($p)=> strtolower(trim($p->descripcion))!=='pago inicial');
                $plazoMeses = $pagosMensuales->count();
                $total = (float)($propuesta->total ?? 0);
                $montoFinanciadoBase = max(0, $total - (float)$montoInicial);
                $tasaInteresMensual = 0.05;
                $montoConIntereses = (float)$pagosMensuales->sum('monto');
                $cuotaMensual = $plazoMeses>0 ? $montoConIntereses/$plazoMeses : 0;
                $pluralMes = \Illuminate\Support\Str::plural('mes', $plazoMeses);
                $mon = fn($n)=>'$'.number_format((float)$n,2,'.',',');
              @endphp

              <div style="flex:1 1 35%;min-width:280px;max-width:320px">
                <h3 style="color:#1e73be;font-weight:bold;border-bottom:2px solid #1e73be;padding-bottom:4px;margin-bottom:12px">
                  Resumen del Financiamiento
                </h3>
                <table class="table table-sm mb-0">
                  <tr><td><strong>Total de la Cotización:</strong></td><td>{{ $mon($total) }}</td></tr>
                  @if(($propuesta->plan ?? '') !== 'contado')
                    <tr><td><strong>Pago inicial estimado:</strong></td><td>{{ $mon($montoInicial) }}</td></tr>
                    <tr><td><strong>Monto financiado (sin intereses):</strong></td><td>{{ $mon($montoFinanciadoBase) }}</td></tr>
                    <tr><td><strong>Plazo estimado:</strong></td><td>{{ $plazoMeses }} {{ $pluralMes }}</td></tr>
                  @endif
                  @if(($propuesta->plan ?? '') === 'credito' && $plazoMeses > 0)
                    <tr><td><strong>Tasa de interés mensual:</strong></td><td>{{ $tasaInteresMensual*100 }}%</td></tr>
                    <tr><td><strong>Total con intereses:</strong></td><td class="text-primary fw-bold">{{ $mon($montoConIntereses) }}</td></tr>
                    <tr><td><strong>Cuota mensual:</strong></td><td>{{ $mon($cuotaMensual) }}</td></tr>
                  @endif
                </table>
              </div>

              <div style="flex:1 1 60%;min-width:320px">
                <table class="table align-middle table-borderless mb-0">
                  <thead class="border-bottom text-muted small text-uppercase">
                    <tr><th>Descripción</th><th>Fecha Estimada</th><th>Monto</th></tr>
                  </thead>
                  <tbody>
                    @foreach($pagosCol as $pago)
                      <tr class="border-bottom">
                        <td>{{ mb_strtoupper($pago->descripcion,'UTF-8') }}</td>
                        <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                        <td>${{ number_format((float)$pago->monto, 2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

        <div class="col-md-6 d-flex">
          <div class="card shadow-sm w-100 border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 py-3">
              <h6 class="mb-0 text-primary-emphasis fw-semibold">🍩 Distribución por Subtotal</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
              <canvas id="graficoSubtotales" height="220" style="max-width:100%"></canvas>
            </div>
          </div>
        </div>
      </div>
    @endif

  </div> <!-- /.container -->

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const ctx = document.getElementById('graficoSubtotales');
      if(!ctx) return;
      const dataSubtotales = {
        labels: @json($labels),
        datasets:[{ data:@json($valores),
          backgroundColor:['#AEC6CF','#FFDAB9','#CBAACB','#B5EAD7','#FFDAC1','#FFB7B2','#E2F0CB','#FDCBFF','#B0E0E6','#D8BFD8'],
          borderColor:'#fff', borderWidth:2 }]
      };
      new Chart(ctx, {
        type:'doughnut', data:dataSubtotales,
        options:{ responsive:true, cutout:'60%',
          plugins:{ legend:{ position:'bottom', labels:{ color:'#6c757d', font:{ size:12 } }},
            tooltip:{ callbacks:{ label:(c)=>`${c.label||''}: $${Number(c.raw??0).toLocaleString()}` }}}}
      });
    });
  </script>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- UX: estados en el propio botón -->
  <script>
    // anchors (PDF, etc.)
    document.querySelectorAll('.js-async-btn').forEach(btn=>{
      const label = btn.querySelector('.label');
      const def   = btn.dataset.defaultLabel || label?.textContent || '';
      const busy  = btn.dataset.busyLabel || 'Abriendo…';
      const done  = btn.dataset.doneLabel || 'Listo ✓';

      btn.addEventListener('click', ()=>{
        // no evitamos navegación (target="_blank")
        if(label){ label.textContent = busy; }
        btn.classList.add('is-loading');
        setTimeout(()=>{ // feedback “Listo ✓”
          btn.classList.remove('is-loading');
          if(label){ label.textContent = done; }
          // volver al texto normal después
          setTimeout(()=>{ if(label){ label.textContent = def; } }, 2000);
        }, 900);
      }, {once:false});
    });

    // submit (WhatsApp)
    (function(){
      const form  = document.getElementById('form-wa');
      const btn   = document.getElementById('btn-wa');
      if(!form || !btn) return;

      const label = btn.querySelector('.label');
      const def   = btn.dataset.defaultLabel || label?.textContent || '';
      const busy  = btn.dataset.busyLabel || 'Enviando…';
      const done  = btn.dataset.doneLabel || 'Enviado ✓';

      // Si venimos de una respuesta OK del servidor, ya mostramos "Enviado ✓"
      @if($wa_ok)
        btn.classList.remove('is-loading');
        if(label){ label.textContent = done; }
      @endif

      form.addEventListener('submit', ()=>{
        btn.disabled = true;
        btn.classList.add('is-loading');
        if(label){ label.textContent = busy; }
      });
    })();
  </script>
</body>
</html>
