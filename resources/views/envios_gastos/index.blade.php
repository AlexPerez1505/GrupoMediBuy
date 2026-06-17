@extends('layouts.app')
@section('title','Paqueterias')
@section('titulo','Paqueterias')

@section('content')
<style>
  :root{
    --bg:#f5f9ff; --card:#ffffff; --ink:#123b66; --muted:#5b728a; --line:#dbeafe;
    --brand:#cfe7ff; --brand-ink:#0b4a8f; --radius:16px; --shadow:0 12px 32px rgba(8,32,67,.08);
    --ok:#c7f9cc; --ok-ink:#166534;
  }
  body{ background:var(--bg); }
  .wrap{ max-width:1300px; margin:18px auto; padding:0 12px; }

  .toolbar{
    position:sticky; top:8px; z-index:10;
    background:linear-gradient(180deg, rgba(207,231,255,.9), rgba(255,255,255,.95));
    border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow);
    padding:12px; margin-bottom:12px;
  }
  .searchbar{ display:block; }
  .search-field{ position:relative; }
  .search-field input{
    width:100%; border:1px solid var(--line); border-radius:14px;
    padding:12px 44px 12px 40px;
    font-size:14px; color:var(--ink); background:#fff; outline:none;
  }
  .search-field .ico{
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    width:18px; height:18px; display:inline-block; pointer-events:none; opacity:.9;
  }
  .search-field .spin{
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    width:16px; height:16px; border:2px solid #bee3ff; border-top-color:#0b4a8f;
    border-radius:50%; animation: spin .8s linear infinite; display:none;
  }
  .search-field.loading .spin{ display:block; }
  @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

  .kpis{ display:flex; gap:10px; flex-wrap:wrap; margin:10px 0; }
  .kpi{
    background:#eff6ff; border:1px dashed var(--line); border-radius:12px; padding:10px 12px;
    font-size:13px; color:var(--ink);
  }
  .cta-right{ margin-left:auto; }
  .btn{
    border:1px solid var(--line); border-radius:12px; padding:10px 16px;
    font-weight:800; background:var(--brand); color:var(--brand-ink);
    text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:8px; box-shadow:var(--shadow);
  }

  .card{ background:var(--card); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); }

  .tablewrap{ overflow:visible; }
  table{ width:100%; border-collapse:collapse; border:1px solid var(--line); border-radius:12px; overflow:hidden; table-layout:fixed; }
  thead th{ background:#eff6ff; color:var(--ink); text-align:left; font-size:13px; }
  th, td{ padding:10px 10px; border-bottom:1px solid var(--line); white-space:normal; font-size:13px; word-break:break-word; }
  tbody tr:hover{ background:#f7fbff; }

  thead th:nth-child(1)  { width:90px; }
  thead th:nth-child(2)  { width:110px; }
  thead th:nth-child(3)  { width:90px; }
  thead th:nth-child(4)  { width:110px; }
  thead th:nth-child(5)  { width:90px; }
  thead th:nth-child(6)  { width:75px; }
  thead th:nth-child(7)  { width:75px; }
  thead th:nth-child(8)  { width:90px; }
  thead th:nth-child(9)  { width:90px; }
  thead th:nth-child(10) { width:auto; }
  thead th:nth-child(11) { width:160px; }

  .cards{ display:none; gap:10px; }
  .ship-card{
    background:#fff; border:1px solid var(--line); border-radius:14px; padding:12px;
    display:grid; gap:6px;
  }
  .ship-head{ display:flex; justify-content:space-between; gap:8px; align-items:center; }
  .chip{ background:#dbeafe; color:#0b4a8f; border-radius:999px; padding:4px 8px; font-size:11px; font-weight:800; }
  .ship-meta{ color:var(--muted); font-size:12px; display:flex; gap:10px; flex-wrap:wrap; }
  .ok{ background:var(--ok); color:var(--ok-ink); border:1px solid #dcfce7; }

  @media (max-width: 860px){
    .tablewrap{ display:none; }
    .cards{ display:grid; }
    .cta-right{ width:100%; margin-left:0; }
    .btn{ width:100%; }
  }

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
  .pin-box p{ color:#5e7085; font-size:14px; margin:0 0 18px; }
  .pin-input{
    width:100%; text-align:center; font-size:28px; letter-spacing:10px;
    padding:14px 12px; border:2px solid #d9e8ff; border-radius:14px;
    outline:none; color:#0f3b67; background:#f5f9ff;
    transition:border-color .2s;
  }
  .pin-input:focus{ border-color:#77b6ff; }
  .pin-error{ color:#9f1239; font-size:13px; min-height:22px; margin:8px 0; }
  .pin-btns{ display:flex; gap:8px; margin-top:4px; }
  .pin-btn{
    flex:1; padding:12px; border-radius:12px; font-weight:800;
    font-size:14px; cursor:pointer; border:1px solid #d9e8ff;
  }
  .pin-btn.cancel{ background:#fff; color:#0f3b67; }
  .pin-btn.confirm{ background:#cfe7ff; color:#0b4a8f; border-color:#cfe7ff; }
  .pin-btn.confirm:hover{ background:#b8daff; }
</style>

<div class="wrap">

  {{-- Toolbar --}}
  <div class="toolbar">
    <form class="searchbar" action="{{ route('envios-gastos.index') }}" method="GET" id="autoSearchForm">
      <div class="search-field" id="searchField">
        <svg class="ico" viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="11" cy="11" r="7" fill="none" stroke="#0b4a8f" stroke-width="2"></circle>
          <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="#0b4a8f" stroke-width="2" stroke-linecap="round"></line>
        </svg>
        <input type="text" name="q" value="{{ $q }}"
          placeholder="Buscar: referencia, destino, sucursal, transportista"
          aria-label="Buscar envíos" autocomplete="off" id="qInput">
        <span class="spin" aria-hidden="true"></span>
      </div>
    </form>

    <div class="kpis">
      <div class="kpi">Registros: <strong>{{ number_format($conteo) }}</strong></div>
      <div class="kpi">Gasto total: <strong>${{ number_format($totalGasto,2) }} MXN</strong></div>
      <div class="kpi">Promedio: <strong>${{ number_format($conteo ? ($totalGasto/$conteo) : 0,2) }} MXN</strong></div>
      <div class="cta-right">
        <a class="btn" href="{{ route('envios-gastos.create') }}">+ Registrar envío</a>
      </div>
    </div>

    @if (session('ok'))
      <div class="kpi ok">{{ session('ok') }}</div>
    @endif
  </div>

  {{-- Tabla desktop --}}
  <div class="card">
    <div class="tablewrap">
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Referencia</th>
            <th>Sucursal</th>
            <th>Destino</th>
            <th>Transp.</th>
            <th>Peso (kg)</th>
            <th>Vol (kg)</th>
            <th>Facturable (kg)</th>
            <th>Costo MXN</th>
            <th>Notas</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($envios as $e)
            <tr>
              <td>{{ optional($e->fecha_envio)->format('Y-m-d') }}</td>
              <td>{{ $e->referencia }}</td>
              <td>{{ $e->sucursal }}</td>
              <td>{{ $e->destino }}</td>
              <td>{{ $e->transportista }}</td>
              <td>{{ number_format($e->peso_kg ?? 0, 2) }}</td>
              <td>{{ number_format($e->peso_volumetrico_kg ?? 0, 2) }}</td>
              <td>{{ number_format($e->peso_facturable_kg ?? 0, 2) }}</td>
              <td><strong>${{ number_format($e->costo_mxn, 2) }}</strong></td>
              <td style="word-break:break-word">{{ $e->notas }}</td>
              <td>
                {{-- Form oculto para eliminar --}}
                <form id="del-{{ $e->id }}" method="POST"
                      action="{{ route('envios-gastos.destroy', $e) }}"
                      style="display:none">
                  @csrf
                  @method('DELETE')
                </form>

                <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap">
                  <button type="button"
                          onclick="askPin('edit', '{{ route('envios-gastos.edit', $e) }}')"
                          style="display:inline-flex; align-items:center; gap:4px; padding:6px 10px;
                                 background:#cfe7ff; color:#0b4a8f; border:1px solid #d9e8ff;
                                 border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;
                                 white-space:nowrap">
                    ✏️ Editar
                  </button>
                  <button type="button"
                          onclick="askPin('delete', '{{ route('envios-gastos.destroy', $e) }}', 'del-{{ $e->id }}')"
                          style="display:inline-flex; align-items:center; gap:4px; padding:6px 10px;
                                 background:#fff1f2; color:#9f1239; border:1px solid #fecdd3;
                                 border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;
                                 white-space:nowrap">
                    🗑️ Eliminar
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="11" style="color:var(--muted); text-align:center; padding:20px">Sin resultados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Cards móvil --}}
    <div class="cards" style="padding:12px">
      @forelse ($envios as $e)
        <div class="ship-card">
          <div class="ship-head">
            <div>
              <strong style="color:var(--ink)">{{ $e->referencia ?? 'Sin ref.' }}</strong>
              <div class="ship-meta">
                <span>{{ optional($e->fecha_envio)->format('Y-m-d') }}</span>
                <span>•</span>
                <span>{{ $e->sucursal }}</span>
              </div>
            </div>
            <span class="chip">${{ number_format($e->costo_mxn,2) }}</span>
          </div>
          <div class="ship-meta">
            <span>Destino: <strong style="color:var(--ink)">{{ $e->destino ?? '—' }}</strong></span>
            <span>Transp.: <strong style="color:var(--ink)">{{ $e->transportista ?? '—' }}</strong></span>
          </div>
          <div class="ship-meta">
            <span>Peso: <strong style="color:var(--ink)">{{ number_format($e->peso_kg ?? 0,2) }}</strong> kg</span>
            <span>Vol: <strong style="color:var(--ink)">{{ number_format($e->peso_volumetrico_kg ?? 0,2) }}</strong> kg</span>
            <span>Fact.: <strong style="color:var(--ink)">{{ number_format($e->peso_facturable_kg ?? 0,2) }}</strong> kg</span>
          </div>
          @if($e->notas)
            <div class="ship-meta" style="white-space:normal">{{ $e->notas }}</div>
          @endif

          {{-- Form oculto móvil --}}
          <form id="del-m-{{ $e->id }}" method="POST"
                action="{{ route('envios-gastos.destroy', $e) }}"
                style="display:none">
            @csrf
            @method('DELETE')
          </form>

          <div style="display:flex; gap:6px; margin-top:6px">
            <button type="button"
                    onclick="askPin('edit', '{{ route('envios-gastos.edit', $e) }}')"
                    style="flex:1; padding:10px; background:#cfe7ff; color:#0b4a8f;
                           border:1px solid #d9e8ff; border-radius:10px; font-size:13px;
                           font-weight:700; cursor:pointer">
              ✏️ Editar
            </button>
            <button type="button"
                    onclick="askPin('delete', null, 'del-m-{{ $e->id }}')"
                    style="flex:1; padding:10px; background:#fff1f2; color:#9f1239;
                           border:1px solid #fecdd3; border-radius:10px; font-size:13px;
                           font-weight:700; cursor:pointer">
              🗑️ Eliminar
            </button>
          </div>
        </div>
      @empty
        <div class="ship-card" style="color:var(--muted)">Sin resultados.</div>
      @endforelse
    </div>

    <div style="padding:12px">
      {{ $envios->links() }}
    </div>
  </div>
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
      <button class="pin-btn cancel" onclick="closePin()">Cancelar</button>
      <button class="pin-btn confirm" onclick="confirmPin()">Confirmar</button>
    </div>
  </div>
</div>
<div style="position:fixed;bottom:0;left:0;background:red;color:white;padding:4px;font-size:11px;z-index:999">
  PIN: [{{ config('app.aprobacion_pin') }}]
</div>

<script>
  // ── Buscador con debounce ──
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('qInput');
    const form  = document.getElementById('autoSearchForm');
    const field = document.getElementById('searchField');
    let composing = false, t;
    if (!input || !form) return;
    input.addEventListener('compositionstart', () => composing = true);
    input.addEventListener('compositionend',   () => { composing = false; trigger(); });
    input.addEventListener('input', () => { if (!composing) trigger(); });
    input.addEventListener('keydown', e => { if (e.key === 'Escape') { input.value = ''; trigger(); } });
    function trigger(){
      clearTimeout(t);
      field.classList.add('loading');
      t = setTimeout(() => { form.requestSubmit ? form.requestSubmit() : form.submit(); }, 500);
    }
  });

  // ── Modal PIN ──
  const PIN_CORRECTO = '{{ config("app.aprobacion_pin") }}';
  let _tipo    = null;   // 'edit' | 'delete'
  let _url     = null;   // URL destino para editar
  let _formId  = null;   // ID del form oculto para eliminar

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
      // Guardar localmente ANTES de cerrar
      const tipo   = _tipo;
      const url    = _url;
      const formId = _formId;

      closePin(); // esto limpia _tipo, _url, _formId

      if (tipo === 'edit' && url) {
        window.location.href = url;
      } else if (tipo === 'delete' && formId) {
        if (confirm('¿Seguro que deseas eliminar este registro?')) {
          document.getElementById(formId).submit();
        }
      }
    } else {
      document.getElementById('pinError').textContent = '❌ PIN incorrecto, intenta de nuevo.';
      document.getElementById('pinInput').value = '';
      document.getElementById('pinInput').focus();
    }
  }

  // Cerrar al hacer clic fuera del box
  document.getElementById('pinOverlay').addEventListener('click', function(e){
    if (e.target === this) closePin();
  });
</script>
@endsection