@extends('layouts.app')
@section('title', 'Compras')
@section('titulo', 'Pedidos Pendientes')
@section('content')

<style>
  ul { padding-left: 1rem; }
  th, td { vertical-align: middle; }
  table thead th {
    font-size: 0.95rem; font-weight: 600;
    text-transform: uppercase; border-bottom: none;
  }
  table tbody td { font-size: 0.9rem; color: #374151; }
  .table tbody tr { transition: box-shadow 0.2s ease; }
  .table tbody tr:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
  body { background: #f4f7fb; }

  .mt-5 { margin-top: 1.5rem !important; }
  .container { margin-top: 1.5rem !important; }

  table { table-layout: fixed; width: 100%; }
  table th:nth-child(1) { width: 8%; }
  table th:nth-child(2) { width: 12%; }
  table th:nth-child(3) { width: 14%; }
  table th:nth-child(4) { width: 24%; }
  table th:nth-child(5) { width: 28%; }
  table th:nth-child(6) { width: 18%; }

  .table td ul { max-height: 100px; overflow-y: auto; margin: 0; padding-left: 1.2rem; }

  @media (max-width: 768px) {
    .table thead { display: none; }
    .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
    .table tr {
      margin-bottom: 1rem; border-radius: 0.5rem;
      box-shadow: 0 1px 6px rgba(0,0,0,0.05);
      padding: 0.75rem; background-color: #ffffff;
    }
    .table td { padding: 0.5rem 0; text-align: right; position: relative; }
    .table td::before {
      content: attr(data-label); position: absolute; left: 0;
      width: 50%; padding-left: 1rem; font-weight: bold;
      text-align: left; color: #6b7280;
    }
  }

  /* ── Modal PIN ── */
  .pin-overlay {
    display: none; position: fixed; inset: 0; z-index: 200;
    background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
  }
  .pin-overlay.active { display: flex; }
  .pin-box {
    background: #fff; border-radius: 20px; padding: 32px 28px; width: 320px;
    box-shadow: 0 24px 64px rgba(0,0,0,.22); text-align: center;
    animation: popIn .18s ease;
  }
  @keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
  .pin-box h3 { margin: 8px 0 4px; color: #0f3b67; font-size: 18px; }
  .pin-box p  { color: #5e7085; font-size: 14px; margin: 0 0 18px; }
  .pin-input {
    width: 100%; text-align: center; font-size: 28px; letter-spacing: 10px;
    padding: 14px 12px; border: 2px solid #d9e8ff; border-radius: 14px;
    outline: none; color: #0f3b67; background: #f5f9ff;
    transition: border-color .2s;
  }
  .pin-input:focus { border-color: #77b6ff; }
  .pin-error { color: #9f1239; font-size: 13px; min-height: 22px; margin: 8px 0; }
  .pin-btns  { display: flex; gap: 8px; margin-top: 4px; }
  .pin-btn   { flex: 1; padding: 12px; border-radius: 12px; font-weight: 800; font-size: 14px; cursor: pointer; border: 1px solid #d9e8ff; }
  .pin-btn.cancel  { background: #fff; color: #0f3b67; }
  .pin-btn.confirm { background: #cfe7ff; color: #0b4a8f; border-color: #cfe7ff; }
  .pin-btn.confirm:hover { background: #b8daff; }
</style>

<div class="container mt-5">
  <div class="card shadow p-4 border-0 rounded-4" style="background-color:#ffffff;">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
      <h2 class="m-0" style="color:#1e293b; font-weight:600;">📦 Pedidos Pendientes</h2>
      <a href="{{ route('pedidos.create') }}" class="btn"
         style="background-color:#dcfce7; color:#15803d; border:1px solid #86efac;
                padding:0.45rem 1rem; font-weight:500; border-radius:0.5rem;">
        + Nuevo Pedido
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($pedidos->isEmpty())
      <p class="text-muted">No hay pedidos pendientes.</p>
    @else
      <div class="table-responsive">
        <table class="table align-middle" style="border-collapse:separate; border-spacing:0 0.75rem;">
          <thead style="background-color:#f3f4f6; color:#374151;">
            <tr>
              <th>ID Pedido</th>
              <th>Fecha Programada</th>
              <th>Creado por</th>
              <th>Observaciones</th>
              <th>Equipos</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pedidos as $pedido)
              <tr style="background-color:#f9fafb; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <td style="font-weight:500;">#{{ $pedido->id }}</td>
                <td>{{ \Carbon\Carbon::parse($pedido->fecha_programada)->format('Y-m-d') }}</td>
                <td>{{ $pedido->creado_por }}</td>
                <td class="text-muted">{{ $pedido->observaciones }}</td>
                <td>
                  <ul class="mb-0 ps-3">
                    @foreach($pedido->equipos as $pequipo)
                      <li>{{ $pequipo->cantidad }} × {{ $pequipo->nombre }}</li>
                    @endforeach
                  </ul>
                </td>
                <td class="text-center">

                  {{-- Form oculto eliminar --}}
                  <form id="del-pedido-{{ $pedido->id }}" method="POST"
                        action="{{ route('pedidos.destroy', $pedido->id) }}"
                        style="display:none">
                    @csrf
                    @method('DELETE')
                  </form>

                  <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">

                    {{-- Registrar recepción: también protegido por PIN --}}
                    <button type="button"
                            onclick="askPin('edit', '{{ route('recepciones.createDesdePedido', $pedido->id) }}')"
                            class="btn btn-sm"
                            style="border:1px solid #fdba74; color:#b45309; background-color:#ffedd5;
                                   padding:0.4rem 0.8rem; border-radius:0.5rem; font-weight:500;">
                      + Registrar Recepción
                    </button>

                    {{-- Eliminar: protegido por PIN --}}
                    <button type="button"
                            onclick="askPin('delete', null, 'del-pedido-{{ $pedido->id }}')"
                            class="btn btn-sm"
                            style="border:1px solid #fca5a5; color:#b91c1c; background-color:#fee2e2;
                                   padding:0.4rem 0.8rem; border-radius:0.5rem; font-weight:500;">
                      🗑 Eliminar
                    </button>

                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

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
      <button class="pin-btn cancel"  onclick="closePin()">Cancelar</button>
      <button class="pin-btn confirm" onclick="confirmPin()">Confirmar</button>
    </div>
  </div>
</div>

<script>
  // Responsive labels
  document.addEventListener('DOMContentLoaded', () => {
    const headers = Array.from(document.querySelectorAll('table thead th')).map(th => th.innerText.trim());
    document.querySelectorAll('table tbody tr').forEach(tr => {
      tr.querySelectorAll('td').forEach((td, i) => td.setAttribute('data-label', headers[i] || ''));
    });
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
      // Guardar locales ANTES de cerrar
      const tipo   = _tipo;
      const url    = _url;
      const formId = _formId;
      closePin();

      if (tipo === 'edit' && url) {
        window.location.href = url;
      } else if (tipo === 'delete' && formId) {
        if (confirm('¿Seguro que deseas eliminar este pedido? Esta acción no se puede deshacer.')) {
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
  document.getElementById('pinOverlay').addEventListener('click', function(e){
    if (e.target === this) closePin();
  });
</script>

@endsection