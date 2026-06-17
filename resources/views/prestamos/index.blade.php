@extends('layouts.app')

@section('title', 'Préstamos')
@section('titulo', 'Préstamos')

@section('content')

<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad; --mint-dark:#34c29e;
  --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff;
}
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}
body {
  overflow-y: scroll;
  scrollbar-width: none; /* Firefox */
}
body::-webkit-scrollbar {
  display: none; /* Chrome, Edge, Safari */
}
*, *::before, *::after {
  scrollbar-width: none;
}
*::-webkit-scrollbar {
  display: none;
}
.edit-wrap {
  width: calc(100% - 120px);
  max-width: none;
  margin: 10px auto 40px;
  padding: 0;
}
.panel{background:var(--card);border-radius:16px;box-shadow:0 16px 40px rgba(18,38,63,.12);overflow:hidden;}
.panel-head{padding:22px 26px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:14px;justify-content:space-between;}
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.actions-top{display:flex;gap:10px;flex-wrap:wrap}

.toolbar{padding:18px 26px;display:flex;gap:12px;align-items:center;justify-content:space-between}
.toolbar-form{width:100%;display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap}

.searchbar{display:flex;align-items:center;gap:10px;position:relative}
.icon-btn{
  width:42px;height:42px;border-radius:999px;border:1px solid var(--line);
  background:#fff;display:grid;place-items:center;cursor:pointer;
  transition:transform .1s ease, border-color .2s ease, box-shadow .2s ease;
}
.icon-btn:hover{border-color:#dfe3e8;box-shadow:0 6px 16px rgba(18,38,63,.08)}
.icon-btn:active{transform:scale(.98)}
.input{
  border:1px solid var(--line);border-radius:12px;padding:10px 12px;background:#fff;
  width:0;opacity:0;pointer-events:none;transition:width .25s ease,opacity .2s ease;
}
.searchbar.open .input{width:240px;opacity:1;pointer-events:auto}
@media (min-width: 900px){
  .icon-btn{display:none}
  .input{width:260px;opacity:1;pointer-events:auto}
}

.filterbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.select{
  height:42px;border:1px solid var(--line);border-radius:12px;padding:0 12px;
  background:#fff;font-weight:700;color:var(--ink);outline:none;
}
.select:focus{border-color:#dfe3e8;box-shadow:0 6px 16px rgba(18,38,63,.08)}
@media (max-width: 760px){
  .filterbar{width:100%}
  .select{flex:1;min-width:220px}
}

.btn{border:0;border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;transition:transform .05s,box-shadow .2s,background .2s,color .2s;}
.btn:active{transform:translateY(1px)}
.btn-primary{background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26);}
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{background:#fff;color:var(--ink);border:1px solid var(--line);}
.btn-ghost:hover{border-color:#dfe3e8}
.btn-icon{border:1px solid var(--line);background:#fff;border-radius:10px;padding:8px;display:inline-grid;place-items:center;transition:transform .08s ease,border-color .2s ease;cursor:pointer;}
.btn-icon:hover{border-color:#dfe3e8;transform:translateY(-1px)}

.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:12px 14px;border-bottom:1px solid #edf0f3;text-align:left;vertical-align:middle}
.table thead th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em}
.row-link{color:inherit;text-decoration:none}
tbody tr{transition:background .2s ease, transform .08s ease}
tbody tr:hover{background:#fafbfc}

@media (max-width: 760px){
  .table thead{display:none}
  .table, .table tbody, .table tr, .table td{display:block;width:100%}
  .table tr{
    background:#fff;border:1px solid var(--line);border-radius:12px;
    padding:10px;margin:10px 0;box-shadow:0 10px 22px rgba(18,38,63,.06);
  }
  .table td{border-bottom:none;padding:8px 6px;}
  .table td::before{
    content: attr(data-label);
    display:block;font-size:11px;color:var(--muted);
    text-transform:uppercase;letter-spacing:.03em;margin-bottom:2px;
  }
  .td-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:8px}
}

.pill{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
.pill--activo{background:#e7fdf6;color:#127c64}
.pill--retrasado{background:#fff4e5;color:#9a5800}
.pill--devuelto{background:#eef7ff;color:#0b4c8c}
.pill--cancelado{background:#f7f7f8;color:#6a6e76}
.pill--vendido{background:#ffeef0;color:#b6232a}

.empty{padding:36px;text-align:center;color:var(--muted)}

/* ── Modal PIN ── */
.pin-overlay{
  display:none; position:fixed; inset:0; z-index:200;
  background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
  align-items:center; justify-content:center;
}
.pin-overlay.active{ display:flex; }
.pin-box{
  background:#fff; border-radius:20px; padding:32px 28px; width:320px;
  box-shadow:0 24px 64px rgba(0,0,0,.22); text-align:center;
  animation: popIn .18s ease;
}
@keyframes popIn{ from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.pin-box h3{ margin:8px 0 4px; color:#0f3b67; font-size:18px; }
.pin-box p { color:#5e7085; font-size:14px; margin:0 0 18px; }
.pin-input{
  width:100%; text-align:center; font-size:28px; letter-spacing:10px;
  padding:14px 12px; border:2px solid #d9e8ff; border-radius:14px;
  outline:none; color:#0f3b67; background:#f5f9ff;
  transition:border-color .2s;
}
.pin-input:focus{ border-color:#77b6ff; }
.pin-error{ color:#9f1239; font-size:13px; min-height:22px; margin:8px 0; }
.pin-btns { display:flex; gap:8px; margin-top:4px; }
.pin-btn  { flex:1; padding:12px; border-radius:12px; font-weight:800; font-size:14px; cursor:pointer; border:1px solid #d9e8ff; }
.pin-btn.cancel { background:#fff; color:#0f3b67; }
.pin-btn.confirm{ background:#cfe7ff; color:#0b4a8f; border-color:#cfe7ff; }
.pin-btn.confirm:hover{ background:#b8daff; }
/* ── Paginación ── */
.pag-wrap{
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 20px; border-top:1px solid var(--line); flex-wrap:wrap; gap:10px;
}
.pag-info{ font-size:13px; color:var(--muted); }
.pag-btns{ display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
.pag-btn{
  min-width:36px; height:36px; border-radius:10px; border:1px solid var(--line);
  background:#fff; color:var(--ink); font-weight:700; font-size:14px;
  display:grid; place-items:center; text-decoration:none; padding:0 8px;
  transition:border-color .2s, background .2s, color .2s;
}
.pag-btn:hover{ border-color:#dfe3e8; background:#f5f7fa; }
.pag-btn--active{ background:var(--mint); color:#fff; border-color:var(--mint); }
.pag-btn--disabled{ color:#c5c9d0; cursor:default; pointer-events:none; }
.pag-ellipsis{ color:var(--muted); font-size:14px; padding:0 4px; }
</style>

<div class="edit-wrap">
 {{-- Panel 1: Encabezado + Buscador + Filtros --}}
  <div class="panel" style="position:sticky; top:0; z-index:10;">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Préstamos (paquetes)</h2>
        <p>Administra tus paquetes y consulta sus equipos.</p>
      </div>
      <div class="actions-top">
        <a href="{{ route('prestamos.create') }}" class="btn btn-primary">+ Nuevo paquete</a>
      </div>
    </div>

    <div class="toolbar">
      <form method="GET" class="toolbar-form" action="{{ route('prestamos.index') }}">
        <div class="searchbar" id="searchbar">
          <button type="button" class="icon-btn" id="searchToggle" aria-label="Buscar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="7"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </button>
          <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por cliente o folio...">
        </div>

        <div class="filterbar">
          <select class="select" name="estado" onchange="this.form.submit()">
            <option value=""          {{ request('estado')===''          ? 'selected' : '' }}>Todos los estados</option>
            <option value="activo"    {{ request('estado')==='activo'    ? 'selected' : '' }}>Activo</option>
            <option value="retrasado" {{ request('estado')==='retrasado' ? 'selected' : '' }}>Retrasado</option>
            <option value="devuelto"  {{ request('estado')==='devuelto'  ? 'selected' : '' }}>Devuelto</option>
            <option value="cancelado" {{ request('estado')==='cancelado' ? 'selected' : '' }}>Cancelado</option>
            <option value="vendido"   {{ request('estado')==='vendido'   ? 'selected' : '' }}>Vendido</option>
          </select>

          @if(request('q') || request('estado'))
            <a class="btn btn-ghost" href="{{ route('prestamos.index') }}">Limpiar</a>
          @endif
        </div>
      </form>
    </div>
  </div>

 {{-- Panel 2: Tabla --}}
<div class="panel" style="margin-top:8px;">
  @if($prestamos->isEmpty())
    <div class="empty">No hay préstamos que mostrar.</div>
  @else

    {{-- ↓ max-height + overflow-y para scroll vertical --}}
    <div style="overflow-x:auto; overflow-y:auto; max-height:60vh;">
      <table class="table">
        <thead>
          <tr>
            <th>Folio</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Salida</th>
            <th>Regreso est.</th>
            <th>Equipos</th>
            <th>Usuario</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($prestamos as $p)
            @php
              $pill = match($p->estado){
                'retrasado' => 'pill--retrasado',
                'devuelto'  => 'pill--devuelto',
                'cancelado' => 'pill--cancelado',
                'vendido'   => 'pill--vendido',
                default     => 'pill--activo'
              };
            @endphp
            <tr>
              <td data-label="Folio">
                <a class="row-link" href="{{ route('prestamos.show',$p->id) }}"><strong>#{{ $p->id }}</strong></a>
              </td>
              <td data-label="Cliente">{{ optional($p->cliente)->nombre ?? '—' }}</td>
              <td data-label="Estado"><span class="pill {{ $pill }}">{{ ucfirst($p->estado) }}</span></td>
              <td data-label="Salida">{{ optional($p->fecha_prestamo)->format('Y-m-d') }}</td>
              <td data-label="Regreso est.">{{ optional($p->fecha_devolucion_estimada)->format('Y-m-d') }}</td>
              <td data-label="Equipos">{{ $p->relationLoaded('registros') ? $p->registros->count() : $p->registros()->count() }}</td>
              <td data-label="Usuario">{{ $p->user_name }}</td>
              <td data-label="Acciones" style="text-align:right">
                <form id="del-prestamo-{{ $p->id }}" method="POST"
                      action="{{ route('prestamos.destroy', $p->id) }}"
                      style="display:none">
                  @csrf
                  @method('DELETE')
                </form>
                <div class="td-actions">
                  <a class="btn-icon" title="Ver" href="{{ route('prestamos.show',$p->id) }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </a>
                  <button type="button" class="btn-icon" title="Editar"
                          onclick="askPin('edit', '{{ route('prestamos.edit',$p->id) }}')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 20h9"/>
                      <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                    </svg>
                  </button>
                  <button type="button" class="btn-icon" title="Eliminar"
                          onclick="askPin('delete', null, 'del-prestamo-{{ $p->id }}')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                      <line x1="10" y1="11" x2="10" y2="17"/>
                      <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- ↓ Paginación --}}
    @if($prestamos->lastPage() > 1)
      <div class="pag-wrap">
        <span class="pag-info">
          Mostrando {{ $prestamos->firstItem() }}–{{ $prestamos->lastItem() }}
          de {{ $prestamos->total() }} registros
        </span>
        <div class="pag-btns">
          {{-- Anterior --}}
          @if($prestamos->onFirstPage())
            <span class="pag-btn pag-btn--disabled">‹</span>
          @else
            <a class="pag-btn" href="{{ $prestamos->appends(request()->query())->previousPageUrl() }}">‹</a>
          @endif

          {{-- Números --}}
          @php
            $current = $prestamos->currentPage();
            $last    = $prestamos->lastPage();
            $start   = max(1, $current - 2);
            $end     = min($last, $current + 2);
          @endphp

          @if($start > 1)
            <a class="pag-btn" href="{{ $prestamos->appends(request()->query())->url(1) }}">1</a>
            @if($start > 2)<span class="pag-ellipsis">…</span>@endif
          @endif

          @for($i = $start; $i <= $end; $i++)
            @if($i === $current)
              <span class="pag-btn pag-btn--active">{{ $i }}</span>
            @else
              <a class="pag-btn" href="{{ $prestamos->appends(request()->query())->url($i) }}">{{ $i }}</a>
            @endif
          @endfor

          @if($end < $last)
            @if($end < $last - 1)<span class="pag-ellipsis">…</span>@endif
            <a class="pag-btn" href="{{ $prestamos->appends(request()->query())->url($last) }}">{{ $last }}</a>
          @endif

          {{-- Siguiente --}}
          @if($prestamos->hasMorePages())
            <a class="pag-btn" href="{{ $prestamos->appends(request()->query())->nextPageUrl() }}">›</a>
          @else
            <span class="pag-btn pag-btn--disabled">›</span>
          @endif
        </div>
      </div>
    @endif

  @endif
</div>

{{-- ── Modal PIN ── --}}
<div class="pin-overlay" id="pinOverlay">
  <div class="pin-box">
    <div style="font-size:36px">🔐</div>
    <h3>Acción protegida</h3>
    <p>Ingresa el PIN de aprobación para continuar.</p>
    <input class="pin-input" id="pinInput" type="password"
           inputmode="numeric" maxlength="10" placeholder="• • • • • •"
           onkeydown="if(event.key==='Enter') confirmPin()">
    <div class="pin-error" id="pinError"></div>
    <div class="pin-btns">
      <button class="pin-btn cancel"  onclick="closePin()">Cancelar</button>
      <button class="pin-btn confirm" onclick="confirmPin()">Confirmar</button>
    </div>
  </div>
</div>

<script>
  // Lupa móvil
  const bar = document.getElementById('searchbar');
  const tog = document.getElementById('searchToggle');
  tog?.addEventListener('click', () => {
    bar.classList.toggle('open');
    if (bar.classList.contains('open')) bar.querySelector('input')?.focus();
  });
  document.addEventListener('click', (e) => {
    if (!bar.contains(e.target) && window.innerWidth < 900) bar.classList.remove('open');
  });

  // ── Modal PIN ──
  const PIN_CORRECTO = '{{ config("app.aprobacion_pin") }}';
  let _tipo = null, _url = null, _formId = null;

  function askPin(tipo, url, formId) {
    _tipo   = tipo;
    _url    = url    || null;
    _formId = formId || null;
    document.getElementById('pinInput').value = '';
    document.getElementById('pinError').textContent = '';
    document.getElementById('pinOverlay').classList.add('active');
    setTimeout(() => document.getElementById('pinInput').focus(), 120);
  }

  function closePin() {
    document.getElementById('pinOverlay').classList.remove('active');
    _tipo = _url = _formId = null;
  }

  function confirmPin() {
    const val = document.getElementById('pinInput').value.trim();
    if (val === PIN_CORRECTO) {
      const tipo   = _tipo;
      const url    = _url;
      const formId = _formId;
      closePin();

      if (tipo === 'edit' && url) {
        window.location.href = url;
      } else if (tipo === 'delete' && formId) {
        if (confirm('¿Seguro que deseas eliminar este préstamo? Esta acción no se puede deshacer.')) {
          document.getElementById(formId).submit();
        }
      }
    } else {
      document.getElementById('pinError').textContent = '❌ PIN incorrecto, intenta de nuevo.';
      document.getElementById('pinInput').value = '';
      document.getElementById('pinInput').focus();
    }
  }

  // Cerrar al hacer clic fuera
  document.getElementById('pinOverlay').addEventListener('click', function(e) {
    if (e.target === this) closePin();
  });
</script>

@endsection