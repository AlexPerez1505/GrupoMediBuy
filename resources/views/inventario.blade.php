@extends('layouts.app')
@section('title','Inventario')
@section('titulo','Inventario')

@section('content')
<div style="margin-top:1%; margin-left:15%; padding:0 16px;">
    <a href="{{ url('/home') }}"
       style="display:inline-flex; align-items:center; gap:8px;
              background:#dbeafe; color:#1e3a5f;
              padding:8px 22px; border-radius:14px;
              font-weight:700; font-size:15px;
              border:1px solid #bfdbfe;
              text-decoration:none;
              cursor:pointer;
              transition:background .2s ease;"
       onmouseover="this.style.background='#bfdbfe'"
       onmouseout="this.style.background='#dbeafe'">
        <i class="bi bi-arrow-left"></i> Ir a Inicio
    </a>
</div>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root{
  --bg:#eaebec; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
  --pblue:#dbeafe; --pblue-700:#1d4ed8; --pgreen:#dcfce7; --pgreen-700:#046c4e;
  --shadow:0 10px 30px rgba(2,6,23,.06); --radius:22px;
  --m-line2:#dde6f6; --m-shadow:0 26px 70px rgba(15,23,42,.18);
  --m-glow:0 0 0 10px rgba(31,75,184,.07); --m-blue:#1f4bb8;
}
.bank-mask{ position:fixed; inset:0; background:rgba(15,23,42,.22); opacity:0; pointer-events:none; transition:.18s ease; z-index:5000; backdrop-filter:blur(10px); }
.bank-mask.open{ opacity:1; pointer-events:auto; }
.bank-modal{ position:fixed; left:50%; top:50%; transform:translate(-50%,-46%) scale(.985); width:min(520px,calc(100% - 28px)); opacity:0; pointer-events:none; transition:.18s ease; z-index:5001; }
.bank-modal.open{ opacity:1; pointer-events:auto; transform:translate(-50%,-50%) scale(1); }
.bank-card{ background:rgba(255,255,255,.92); border:1px solid var(--m-line2); border-radius:18px; box-shadow:var(--m-shadow),var(--m-glow); overflow:hidden; }
.bank-top{ padding:14px 16px; display:flex; align-items:flex-start; justify-content:space-between; gap:10px; border-bottom:1px solid #e7ebf2; background:radial-gradient(120% 120% at 0% 0%,rgba(31,75,184,.08) 0%,rgba(255,255,255,.0) 55%),linear-gradient(180deg,rgba(255,255,255,.75) 0%,rgba(255,255,255,.35) 100%); }
.bank-brand{ display:flex; gap:12px; align-items:center; }
.bank-badge{ width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:rgba(31,75,184,.08); border:1px solid rgba(31,75,184,.14); }
.bank-badge i{ color:var(--m-blue); font-size:1.15rem; }
.bank-title{ font-weight:900; color:#0f172a; letter-spacing:.2px; line-height:1.1; }
.bank-sub{ margin-top:2px; font-size:.88rem; color:#667085; }
.bank-close{ border:1px solid #e7ebf2; background:#fff; color:#475569; width:36px; height:36px; border-radius:12px; display:grid; place-items:center; transition:.12s ease; }
.bank-close:hover{ background:#f8fafc; }
.bank-body{ padding:14px 16px 16px; }
.bank-alert{ display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border-radius:14px; border:1px solid #e7ebf2; background:rgba(31,75,184,.045); color:#334155; font-size:.92rem; line-height:1.35; }
.bank-alert .dot{ width:10px; height:10px; border-radius:999px; background:rgba(31,75,184,.75); box-shadow:0 0 0 6px rgba(31,75,184,.10); margin-top:4px; flex:0 0 auto; }
.otp-row{ margin-top:12px; display:flex; gap:10px; justify-content:center; }
.otp{ width:54px; height:58px; text-align:center; font-weight:900; font-size:1.15rem; color:#0f172a; background:#fff; border:1px solid var(--m-line2); border-radius:14px; outline:0; transition:.12s ease; }
.otp:focus{ border-color:rgba(31,75,184,.35); box-shadow:0 0 0 6px rgba(31,75,184,.12); }
.otp.error{ border-color:rgba(220,38,38,.40); box-shadow:0 0 0 6px rgba(220,38,38,.12); }
.bank-note{ margin-top:10px; text-align:center; font-size:.85rem; color:#64748b; }
.loading-dots{ margin-top:10px; display:none; justify-content:center; gap:6px; }
.loading-dots span{ width:7px; height:7px; border-radius:999px; background:rgba(31,75,184,.55); opacity:.6; animation:dotPulse .9s infinite ease-in-out; }
.loading-dots span:nth-child(2){ animation-delay:.12s; }
.loading-dots span:nth-child(3){ animation-delay:.24s; }
@keyframes dotPulse{ 0%,100%{transform:translateY(0);opacity:.45} 50%{transform:translateY(-4px);opacity:1} }
@keyframes shake{ 0%,100%{transform:translateX(0)} 20%{transform:translateX(-5px)} 40%{transform:translateX(5px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)} }
.shake{ animation:shake .28s ease; }

*,*::before,*::after{ box-sizing:border-box; }
body{ background:var(--bg); color:var(--text); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
.page-wrap{ max-width:1160px; margin:0 auto; padding:0 16px; overflow-x:hidden; }

/* HERO */
.hero{
  background:
    radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
    radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
    #fff;
  border:1px solid var(--border); border-radius:18px; padding:16px 18px;
  box-shadow:var(--shadow);
  display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
  margin:18px 0; overflow:hidden;
}
.hero .chip{ width:56px; height:56px; border-radius:16px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #dce7ff; }
.hero h1{ margin:0; font-weight:800; letter-spacing:-.02em; }
.subtle{ color:var(--muted); }
.hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; width:100%; }
.search{ position:relative; flex:1 1 420px; min-width:0; background:#fff; border:1px solid var(--border); border-radius:14px; padding-left:42px; }
.search input{ border:none; outline:none; background:transparent; padding:12px 14px; width:100%; color:#111827; }
.search .ico{ position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:18px; color:#1d4ed8; opacity:.9; }

.hero-select{ border:1px solid var(--border); border-radius:14px; padding:10px 12px; background:#fff; min-width:220px; }

/* BOTONES */
.btn{
  display:inline-flex; align-items:center; gap:.45rem;
  padding:12px 14px; border-radius:14px;
  border:1px solid var(--border); background:#fff; color:#334155;
  font-weight:800; text-decoration:none; cursor:pointer;
  transition:transform .04s ease, box-shadow .2s ease, background .2s ease;
}
.btn:active{ transform:translateY(1px) }
.btn-utility{ box-shadow:0 4px 10px rgba(2,6,23,.04); }
.btn-blue{ background:var(--pblue); color:#0b2a4a; border-color:rgba(96,165,250,.45); }
.btn-green{ background:var(--pgreen); color:#064e3b; border-color:rgba(52,211,153,.45); }

/* TABLA */
.table-wrap{ background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.table-scroll{ overflow:auto; max-width:100%; }
.inv-table{ width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; }
.inv-table .th,.inv-table .td{ padding:16px 18px; text-align:left; font-size:14px; }
.inv-table .th{ color:#6b7280; font-weight:700; background:#eef3ff; border-bottom:1px solid var(--border); white-space:nowrap; }
.inv-table tr.trow{ display:table-row; border-bottom:1px solid var(--border); background:#fff; }
.inv-table tr.trow:hover{ background:#fcfcff; }
.inv-table td.td{ display:table-cell; vertical-align:middle; }
.cell-actions{ display:flex; gap:8px; justify-content:flex-end; }
.no-results{ color:#667085; }

.tile-mini{ width:52px; height:52px; border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#f8fafc; display:grid; place-items:center }
.tile-mini img{ width:100%; height:100%; object-fit:cover }

/* Layout info equipo */
.equip-main{ display:flex; align-items:center; gap:12px; }
.equip-main-text-title{ margin-bottom:2px; display:flex; flex-wrap:wrap; column-gap:4px; }
.equip-tipo{ font-weight:700; }
.equip-subtipo{ font-weight:700; }
.equip-dot{ color:#9ca3af; }
.equip-model{ line-height:1.3; }

/* BADGES */
.badge-state{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:4px 10px; border-radius:999px;
  font-weight:800; font-size:12px;
  border:1px solid transparent; width:fit-content;
}
.badge-registro{ background:#f1f5f9; color:#334155; border-color:#e2e8f0; }
.badge-hojalateria{ background:#e0f2fe; color:#1d4ed8; border-color:#bfdbfe; }
.badge-mantenimiento{ background:#fef9c3; color:#a16207; border-color:#fde68a; }
.badge-stock{ background:#dcfce7; color:#065f46; border-color:#bbf7d0; }
.badge-vendido{ background:#ffe4e6; color:#9f1239; border-color:#fecdd3; }
.badge-defectuoso{ background:#ffedd5; color:#c2410c; border-color:#fdba74; }
.state-meta{ color:#667085; font-size:12px; margin-top:6px; }

.inv-table col:nth-child(1){ width:27%; }
.inv-table col:nth-child(2){ width:12%; }
.inv-table col:nth-child(3){ width:18%; }
.inv-table col:nth-child(4){ width:12%; }
.inv-table col:nth-child(5){ width:13%; }
.inv-table col:nth-child(6){ width:18%; }
/* MÓVIL */
@media (max-width:576px){
  .hero-actions .btn-utility.export-desktop{ display:none; }
  .hero-actions .hero-select{ display:none; }

  .equip-main{ align-items:flex-start; }
  .equip-main-text{ max-width:210px; }
  .equip-main-text-title{ margin-bottom:4px; flex-direction:column; row-gap:2px; }
  .equip-dot{ display:none; }

  .inv-table.is-stacked thead{ display:none; }
  .inv-table.is-stacked, .inv-table.is-stacked tbody, .inv-table.is-stacked tr.trow, .inv-table.is-stacked td.td{ display:block; width:100%; }
  .inv-table.is-stacked tr.trow{ padding:12px 14px; }
  .inv-table.is-stacked tr.trow + tr.trow{ border-top:1px solid var(--border); }
  .inv-table.is-stacked td.td{
    border:none; padding:10px 0;
    display:grid; grid-template-columns:minmax(96px,40%) 1fr; gap:8px; align-items:flex-start;
    word-wrap:break-word;
  }
  .inv-table.is-stacked td.td::before{ content:attr(data-label); font-weight:700; color:#6b7280; }
  .inv-table.is-stacked td.td[data-label="Acciones"]{ grid-template-columns:1fr; }
  .inv-table.is-stacked .cell-actions{ justify-content:flex-start; }
}

/* FAB + Bottom sheet */
.inv-fab{ position:fixed; right:16px; bottom:18px; z-index:60; display:none; }
@media (max-width:576px){ .inv-fab{ display:block; } }
.inv-fab-btn{
  width:56px; height:56px; border-radius:999px; border:1px solid #dce7ff; background:#ffffff;
  display:grid; place-items:center; box-shadow:0 14px 28px rgba(2,6,23,.12);
  transition:transform .06s ease, box-shadow .2s ease, background .2s ease;
}
.inv-fab-btn:hover{ background:#f4fbff; box-shadow:0 18px 36px rgba(2,6,23,.16) }
.inv-fab-btn i{ font-size:22px; color:#2563eb; }

.inv-sheet-backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.35); backdrop-filter: blur(8px) saturate(1.05); opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:70; }
.inv-sheet-backdrop.show{ opacity:1; pointer-events:auto; }
.inv-sheet{
  position:fixed; left:0; right:0; bottom:-100%; z-index:80; background:#fff;
  border-radius:18px 18px 0 0; box-shadow:0 -20px 40px rgba(2,6,23,.16);
  padding:14px 14px 18px; transition:bottom .28s ease; will-change:bottom;
}
.inv-sheet.show{ bottom:0; }
.inv-sheet .grab{ width:60px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 12px; }
.inv-sheet .title{ font-weight:800; color:#0f172a; margin:4px 0 12px; }
.inv-sheet .group{ display:grid; gap:14px; }
.inv-sheet .btn-row{ display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.inv-sheet .inv-btn-sheet{
  display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
  height:48px; width:100%;
  border-radius:12px; font-weight:800; border:1px solid var(--border);
  background:#fff; color:#334155;
  box-shadow:0 6px 16px rgba(2,6,23,.05);
  transition:transform .04s ease, box-shadow .2s ease, background .2s ease;
}
.inv-sheet .inv-btn-sheet:active{ transform:translateY(1px) }
.inv-sheet .inv-btn-sheet.primary{ background:linear-gradient(180deg,#eef4ff,#e3ecff); color:#0b2a4a; border-color:#cfe0ff; }
.inv-sheet .inv-btn-sheet.secondary{ background:#ffffff; color:#334155; border-color:var(--border); }

.inv-sheet .add-equipo{
  display:flex; align-items:center; gap:12px; padding:14px;
  border-radius:14px; border:1px solid #b8f3df; background:#eafff6;
  text-decoration:none; color:#0b6b53; font-weight:800;
  box-shadow:0 6px 16px rgba(11,107,83,.08);
}
.inv-sheet .add-equipo .ico{
  width:40px; height:40px; border-radius:12px; display:grid; place-items:center;
  background:#dff9ef; color:#0b6b53; font-size:1.1rem;
}
.inv-sheet .add-equipo .sub{
  font-weight:600; color:#0b6b53; opacity:.9; font-size:.875rem; margin-top:2px;
}

/* MODAL EXPORTAR */
.modal-content{ border-radius:18px; border:1px solid var(--border); }
.modal-header{ border-bottom:1px solid var(--border); }
.modal-footer{ border-top:1px solid var(--border); }
.option-card{
  border:1px solid var(--border);
  border-radius:14px;
  padding:12px;
  background:#fff;
}
.option-card small{ color:#667085; }
/* Agrega esto al <style> */
.cell-actions .btn{
  padding: 7px 9px;
  border-radius: 10px;
}
.cell-actions .btn i{
  font-size: 15px;
}
</style>

<div class="page-wrap" x-data="InventarioUI()">
  {{-- HERO --}}
  <div class="hero">
    <div class="d-flex align-items-center gap-3">
      <div class="chip"><i class="bi bi-clipboard-check" style="font-size:1.25rem;color:#1d4ed8"></i></div>
      <div>
        <h1 class="h4 mb-0">Inventario</h1>
        <div class="small subtle">Consulta y gestiona tus equipos en tiempo real.</div>
      </div>
    </div>

    <div class="hero-actions">
      <div class="search">
        <i class="ico bi bi-search"></i>
        <input type="search" placeholder="Buscar" x-model="$store.inv.q">
      </div>

      {{-- DESKTOP: filtro estado --}}
      <select class="hero-select btn-utility" x-model="$store.inv.filtroEstado">
        <option value="">Todos los estados</option>
        <option value="registro">Registro</option>
        <option value="hojalateria">Hojalatería</option>
        <option value="mantenimiento">Mantenimiento</option>
        <option value="stock">Stock</option>
        <option value="vendido">Vendido</option>
        <option value="defectuoso">Defectuoso</option>
      </select>

      {{-- NUEVO BOTÓN EXPORTAR (abre modal) --}}
      <button
        type="button"
        class="btn btn-blue btn-utility export-desktop"
        data-bs-toggle="modal"
        data-bs-target="#exportModal"
        title="Exportar"
      >
        <i class="bi bi-download"></i> Exportar
      </button>

      <button class="btn btn-utility" onclick="location.reload()">Actualizar</button>

      <a href="{{ route('registros.create') }}" class="btn btn-green btn-utility">
        <i class="bi bi-plus-circle"></i> Agregar equipo
      </a>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

<div id="bankMask" class="bank-mask"></div>
<div id="bankModal" class="bank-modal" role="dialog" aria-modal="true">
  <div class="bank-card">
    <div class="bank-top">
      <div class="bank-brand">
        <div class="bank-badge"><i class="bi bi-shield-lock"></i></div>
        <div>
          <div id="bankTitle" class="bank-title">Confirmación segura</div>
          <div id="bankSub" class="bank-sub">Escribe el PIN de 6 dígitos</div>
        </div>
      </div>
      <button type="button" id="bankClose" class="bank-close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="bank-body">
      <div class="bank-alert">
        <div class="dot"></div>
        <div id="bankAlertText">Al completar los <b>6 dígitos</b>, se confirma automáticamente.</div>
      </div>
      <div class="otp-row">
        <input class="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="1">
        <input class="otp" inputmode="numeric" maxlength="1">
        <input class="otp" inputmode="numeric" maxlength="1">
        <input class="otp" inputmode="numeric" maxlength="1">
        <input class="otp" inputmode="numeric" maxlength="1">
        <input class="otp" inputmode="numeric" maxlength="1">
      </div>
      <div class="bank-note">Puedes <b>pegar</b> el PIN completo.</div>
      <div id="otpLoading" class="loading-dots"><span></span><span></span><span></span></div>
    </div>
  </div>
</div>
  </div>

  {{-- TABLA --}}
  <div class="table-wrap">
    <div class="table-scroll">
      <table class="inv-table" :class="{'is-stacked': isMobile}">
        <colgroup><col><col><col><col><col><col></colgroup>
        <thead>
          <tr>
            <th class="th">Equipo</th>
            <th class="th">Serie</th>
            <th class="th">Estado</th>
            <th class="th">Fecha adquisición</th>
            <th class="th">Registrado por</th>
            <th class="th text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach(($productos ?? []) as $r)
            @php
              /*
               * ✅ ESTADO ACTUAL (FIX):
               * 1) Tomamos SIEMPRE el estado oficial del registro: estado_proceso
               * 2) La fecha del "último cambio" se toma del proceso más reciente de ESE estado (si existe)
               *    y si no existe, usamos updated_at/created_at como fallback.
               */

              $estado = $r->estado_proceso
                ?: ([1 => 'stock', 2 => 'vendido', 3 => 'mantenimiento', 4 => 'defectuoso'][$r->estado_actual] ?? 'registro');

              $fechaUltimoEstado = null;

              if (method_exists($r, 'procesos')) {
                  $procesoEstado = $r->relationLoaded('procesos')
                      ? $r->procesos->where('tipo_proceso', $estado)->sortByDesc('created_at')->first()
                      : $r->procesos()->where('tipo_proceso', $estado)->orderByDesc('created_at')->first();

                  if ($procesoEstado) {
                      $fechaUltimoEstado = $procesoEstado->created_at;
                  }
              }

              if (!$fechaUltimoEstado) {
                  $fechaUltimoEstado = $r->updated_at ?: $r->created_at;
              }

              $detalleUrl = Route::has('inventario.detalle') ? route('inventario.detalle', $r->id) : url('/inventario/detalle/'.$r->id);
              $barcodeUrl = Route::has('registros.imprimir-barcode') ? route('registros.imprimir-barcode', $r->id) : '#';

              $badgeClass = match($estado){
                'hojalateria'   => 'badge-hojalateria',
                'mantenimiento' => 'badge-mantenimiento',
                'stock'         => 'badge-stock',
                'vendido'       => 'badge-vendido',
                'defectuoso'    => 'badge-defectuoso',
                default         => 'badge-registro'
              };
            @endphp

            <tr class="trow"
                x-show="filtra(
                  {{ json_encode([
                    'tipo'=>$r->tipo_equipo,
                    'subtipo'=>$r->subtipo_equipo,
                    'marca'=>$r->marca,
                    'modelo'=>$r->modelo,
                    'serie'=>$r->numero_serie,
                    'estado'=>$estado,
                    'user'=>$r->user_name
                  ]) }},
                  $store.inv.q,
                  $store.inv.filtroEstado
                )"
                :style="filtra(
                  {{ json_encode([
                    'tipo'=>$r->tipo_equipo,
                    'subtipo'=>$r->subtipo_equipo,
                    'marca'=>$r->marca,
                    'modelo'=>$r->modelo,
                    'serie'=>$r->numero_serie,
                    'estado'=>$estado,
                    'user'=>$r->user_name
                  ]) }},
                  $store.inv.q,
                  $store.inv.filtroEstado
                ) ? '' : 'display:none !important'">

              {{-- EQUIPO --}}
              <td class="td" data-label="Equipo">
                <div class="equip-main">
                  <div class="tile-mini">
                    @if($r->evidencia1)
                      <img src="{{ Str::startsWith($r->evidencia1, ['http://','https://']) ? $r->evidencia1 : asset('storage/'.ltrim($r->evidencia1,'/')) }}" alt="prev">
                    @else
                      <i class="bi bi-box text-muted"></i>
                    @endif
                  </div>
                  <div class="equip-main-text">
                    <div class="equip-main-text-title">
                      <span class="equip-tipo">{{ $r->tipo_equipo ?? 'Equipo' }}</span>
                      <span class="equip-dot">•</span>
                      <span class="equip-subtipo">{{ $r->subtipo_equipo ?? '—' }}</span>
                    </div>
                    <div class="equip-model text-muted small">
                      {{ $r->marca }} {{ $r->modelo }}
                    </div>
                  </div>
                </div>
              </td>

              <td class="td" data-label="Serie"><span class="fw-semibold">{{ $r->numero_serie }}</span></td>

              <td class="td" data-label="Estado">
                <span class="badge-state {{ $badgeClass }}">
                  <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
                  <span class="text-capitalize">{{ $estado }}</span>
                </span>
                <div class="state-meta">
                  @if($fechaUltimoEstado)
                    Último cambio: {{ $fechaUltimoEstado->format('Y-m-d H:i') }}
                  @else
                    Último cambio: —
                  @endif
                </div>
              </td>

              <td class="td" data-label="Fecha adquisición">{{ optional($r->fecha_adquisicion)->format('Y-m-d') ?? '—' }}</td>
              <td class="td" data-label="Registrado por">{{ $r->user_name ?? '—' }}</td>

              <td class="td" data-label="Acciones">
                <div class="cell-actions">
                    {{-- Ver --}}
                    <a class="btn btn-blue btn-utility" href="{{ $detalleUrl }}" title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </a>
                    
                    {{-- Editar --}}
                   <button type="button"
                   class="btn btn-utility js-inv-edit"
                   data-edit-url="{{ route('registros.edit', $r->id) }}"
                   data-pin-url="{{ route('registros.validar-pin-edicion', $r->id) }}"
                   title="Editar">
                       <i class="bi bi-pencil-square"></i>
                    </button>
                    
                    {{-- Eliminar --}}
                    <button type="button"
                    class="btn btn-utility js-inv-delete"
                    data-delete-url="{{ route('registros.destroy', $r->id) }}"
                    style="color:#dc2626;"
                    title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>

                    {{-- Barcode --}}
                    <a class="btn btn-utility" target="_blank" href="{{ $barcodeUrl }}" title="Imprimir etiqueta">
                    <i class="bi bi-upc-scan"></i>
                    </a>
                </div>
              </td>
            </tr>
          @endforeach

          @if(empty($productos) || count($productos)===0)
            <tr class="trow"><td class="td no-results" colspan="6">No hay registros aún.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

  {{-- MODAL EXPORTAR (PDF / EXCEL) --}}
  <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <div class="fw-bold" id="exportModalLabel" style="letter-spacing:.2px">Exportar inventario</div>
            <div class="small subtle">
              El PDF se agrupa por <b>categoría / tipo de equipo</b>. Se respeta el estado seleccionado.
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="option-card mb-3">
            <div class="fw-semibold mb-1">Estado a exportar</div>
            <small>
              Se usará el mismo estado que tienes filtrado en la vista.
              Si en la parte superior tienes "Todos los estados", aquí también se exportan todos.
            </small>
          </div>

          <div class="option-card mb-3">
            <div class="fw-semibold mb-2">Formato</div>
            <div class="d-flex gap-2">
              <input class="btn-check" type="radio" name="fmtExport" id="fmtPdf" value="pdf" checked>
              <label class="btn btn-outline-primary w-100" for="fmtPdf">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF (agrupado por categoría)
              </label>

              <input class="btn-check" type="radio" name="fmtExport" id="fmtExcel" value="excel">
              <label class="btn btn-outline-success w-100" for="fmtExcel">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel (.xlsx)
              </label>
            </div>

            <div class="mt-3">
              <small class="text-muted d-block" id="pdfHint">
                El PDF se abrirá en otra pestaña listo para imprimir o guardar.
              </small>
              <small class="text-muted d-none" id="excelHint">
                Se descargará un archivo <b>.xlsx</b> de Excel.
              </small>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-utility w-100" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="button" class="btn btn-blue w-100" id="btnConfirmExport">
              <i class="bi bi-download"></i> Exportar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- FAB + Bottom sheet (móvil) --}}
  <div class="inv-fab">
    <button type="button" id="openInvSheet" class="inv-fab-btn" aria-label="Filtros">
      <i class="bi bi-sliders"></i>
    </button>
  </div>
  <div class="inv-sheet-backdrop" id="invSheetBackdrop"></div>

  <div class="inv-sheet" id="invSheetPanel" x-data>
    <div class="grab"></div>
    <div class="title">Filtros</div>

    <div class="group">
      <div>
        <label class="form-label fw-semibold mb-1">Estado</label>
        <select class="form-select" x-model="$store.inv.filtroEstado">
          <option value="">Todos los estados</option>
          <option value="registro">Registro</option>
          <option value="hojalateria">Hojalatería</option>
          <option value="mantenimiento">Mantenimiento</option>
          <option value="stock">Stock</option>
          <option value="vendido">Vendido</option>
          <option value="defectuoso">Defectuoso</option>
        </select>
      </div>

      {{-- Exportar (móvil) abre el mismo modal --}}
      <button
        type="button"
        class="inv-btn-sheet primary"
        @click="document.getElementById('closeInvSheet')?.click(); setTimeout(()=>document.querySelector('[data-bs-target=\"#exportModal\"]')?.click(), 80);"
      >
        <i class="bi bi-download"></i> Exportar
      </button>

      <a href="{{ route('registros.create') }}" class="add-equipo">
        <div class="ico"><i class="bi bi-plus-lg"></i></div>
        <div>
          <div class="title">Agregar nuevo equipo</div>
          <div class="sub">Regístralo y adjunta evidencias desde tu móvil.</div>
        </div>
      </a>

      <div class="btn-row">
        <button class="inv-btn-sheet primary" id="applyInvSheet">
          <i class="bi bi-check2-circle"></i> Aplicar filtros
        </button>
        <button class="inv-btn-sheet secondary" id="closeInvSheet">
          <i class="bi bi-x-lg"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('inv', {
    q: '',
    filtroEstado: '',
  });

  Alpine.data('InventarioUI', InventarioUI);
});

function InventarioUI(){
  return {
    isMobile: window.matchMedia('(max-width: 576px)').matches,

    filtra(row, q, estado){
      q = (q || '').toLowerCase().trim();
      estado = (estado || '').toLowerCase().trim();

      const estadoOk = estado ? (String(row.estado||'').toLowerCase().trim() === estado) : true;
      if(!q) return estadoOk;

      const blob = [row.tipo,row.subtipo,row.marca,row.modelo,row.serie,row.user]
        .join(' ').toLowerCase();
      return estadoOk && blob.includes(q);
    },
  };
}

/* Helper global para desbloquear la UI */
function unlockUi(){
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('overflow');
  document.body.style.removeProperty('padding-right');
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  const bsBackdrop = document.getElementById('invSheetBackdrop');
  const bsPanel = document.getElementById('invSheetPanel');
  bsBackdrop?.classList.remove('show');
  bsPanel?.classList.remove('show');
}

/* Bottom sheet + export */
document.addEventListener('DOMContentLoaded', () => {
  const openBtn   = document.getElementById('openInvSheet');
  const closeBtn  = document.getElementById('closeInvSheet');
  const applyBtn  = document.getElementById('applyInvSheet');
  const backdrop  = document.getElementById('invSheetBackdrop');
  const panel     = document.getElementById('invSheetPanel');
  const exportModalEl = document.getElementById('exportModal');

  const open = () => {
    if (window.matchMedia('(max-width: 576px)').matches) {
      panel.classList.add('show');
      backdrop.classList.add('show');
    }
  };
  const hide = () => {
    panel.classList.remove('show');
    backdrop.classList.remove('show');
  };

  openBtn?.addEventListener('click', open);
  closeBtn?.addEventListener('click', () => { hide(); unlockUi(); });
  applyBtn?.addEventListener('click', () => { hide(); unlockUi(); });
  backdrop?.addEventListener('click', () => { hide(); unlockUi(); });

  if (exportModalEl) {
    exportModalEl.addEventListener('hidden.bs.modal', unlockUi);
  }

  const fmtPdf   = document.getElementById('fmtPdf');
  const fmtExcel = document.getElementById('fmtExcel');
  const pdfHint  = document.getElementById('pdfHint');
  const excelHint= document.getElementById('excelHint');

  function refreshHints(){
    const isPdf = fmtPdf?.checked;
    pdfHint?.classList.toggle('d-none', !isPdf);
    excelHint?.classList.toggle('d-none', isPdf);
  }
  fmtPdf?.addEventListener('change', refreshHints);
  fmtExcel?.addEventListener('change', refreshHints);
  refreshHints();

  const btnExport = document.getElementById('btnConfirmExport');
  btnExport?.addEventListener('click', () => {
    const fmt = document.querySelector('input[name="fmtExport"]:checked')?.value || 'pdf';
    const estado = (Alpine.store('inv')?.filtroEstado || '').toLowerCase().trim();

    const modalEl = document.getElementById('exportModal');
    let modalInstance = null;
    if (modalEl && window.bootstrap && bootstrap.Modal) {
      modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    }

    const hardCloseModal = () => {
      if (modalInstance) modalInstance.hide();
      unlockUi();
    };

    if (fmt === 'excel') {
      const base = @json(route('registros.exportExcel'));
      const url = new URL(base, window.location.origin);
      if (estado) url.searchParams.set('estado_proceso', estado);

      hardCloseModal();
      setTimeout(() => {
        window.open(url.toString(), '_blank', 'noopener');
      }, 80);
    } else {
      const base = @json(route('registros.export.pdf'));
      const url = new URL(base, window.location.origin);
      if (estado) url.searchParams.set('estado_proceso', estado);

      hardCloseModal();
      setTimeout(() => {
        window.open(url.toString(), '_blank', 'noopener');
      }, 80);
    }
  });

  unlockUi();
});
(function(){
  const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const bankMask   = document.getElementById('bankMask');
  const bankModal  = document.getElementById('bankModal');
  const bankClose  = document.getElementById('bankClose');
  const bankTitle  = document.getElementById('bankTitle');
  const bankSub    = document.getElementById('bankSub');
  const bankAlertText = document.getElementById('bankAlertText');
  const otpInputs  = Array.from(document.querySelectorAll('.otp'));
  const bankCard   = document.querySelector('.bank-card');
  const loading    = document.getElementById('otpLoading');

  let activeMode = null, activeEditUrl = null, activePinUrl = null, activeDeleteUrl = null;
  let submitting = false;

  const onlyDigits = s => (s||'').toString().replace(/\D+/g,'');
  const getOTP     = () => otpInputs.map(i=>i.value||'').join('');

  function clearOTP(){
    otpInputs.forEach(i=>{ i.value=''; i.classList.remove('error'); i.disabled=false; });
    submitting=false;
    if(loading) loading.style.display='none';
  }

  function focusFirst(){ setTimeout(()=>otpInputs[0]?.focus(), 60); }

  function openOTP(mode, urls={}){
    activeMode      = mode;
    activeEditUrl   = urls.edit   || null;
    activePinUrl    = urls.pin    || null;
    activeDeleteUrl = urls.delete || null;

    if(mode==='editar'){
      if(bankTitle)     bankTitle.textContent = 'Autorización de edición';
      if(bankSub)       bankSub.textContent   = 'PIN de 6 dígitos para continuar';
      if(bankAlertText) bankAlertText.innerHTML= 'Al completar los <b>6 dígitos</b>, se abrirá la edición.';
    } else {
      if(bankTitle)     bankTitle.textContent = 'Confirmar eliminación';
      if(bankSub)       bankSub.textContent   = 'PIN de 6 dígitos para eliminar';
      if(bankAlertText) bankAlertText.innerHTML= 'Al completar los <b>6 dígitos</b>, se eliminará el registro.';
    }

    clearOTP();
    bankMask?.classList.add('open');
    bankModal?.classList.add('open');
    focusFirst();
  }

  function closeOTP(){
    bankMask?.classList.remove('open');
    bankModal?.classList.remove('open');
    activeMode=activeEditUrl=activePinUrl=activeDeleteUrl=null;
  }

  function shake(){ if(!bankCard) return; bankCard.classList.remove('shake'); void bankCard.offsetWidth; bankCard.classList.add('shake'); }

  function flashError(){
    otpInputs.forEach(i=>i.classList.add('error'));
    setTimeout(()=>otpInputs.forEach(i=>i.classList.remove('error')),420);
    shake();
  }

  function resetForRetry(msg){
    submitting=false;
    if(loading) loading.style.display='none';
    otpInputs.forEach(i=>{ i.value=''; i.disabled=false; i.classList.remove('error'); });
    flashError();
    alert(msg||'PIN incorrecto, intenta de nuevo.');
    focusFirst();
  }

  async function runAction(pin){
    if(activeMode==='editar'){
      try{
        if(activePinUrl){
          const r = await fetch(activePinUrl,{
            method:'POST',
            headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({aprobacion_pin:pin})
          });
          const d = await r.json().catch(()=>({}));
          if(!r.ok) throw new Error(d?.message||d?.error||'PIN incorrecto.');
        }
        closeOTP();
        window.location.href = activeEditUrl;
      }catch(e){ resetForRetry(e?.message); }
      return;
    }

    if(activeMode==='eliminar'){
      try{
        const r = await fetch(activeDeleteUrl,{
          method:'DELETE',
          headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
          body: JSON.stringify({aprobacion_pin:pin})
        });
        const d = await r.json().catch(()=>({}));
        if(!r.ok) throw new Error(d?.message||d?.error||'No se pudo eliminar.');
        closeOTP();
        window.location.reload();
      }catch(e){ resetForRetry(e?.message); }
    }
  }

  function autoSubmit(){
    if(submitting) return;
    const pin = getOTP();
    if(pin.length===6 && !otpInputs.some(i=>!i.value)){
      submitting=true;
      otpInputs.forEach(i=>i.disabled=true);
      if(loading) loading.style.display='flex';
      runAction(pin);
    }
  }

  otpInputs.forEach((input,idx)=>{
    input.addEventListener('input',()=>{
      input.value = onlyDigits(input.value).slice(0,1);
      if(input.value && otpInputs[idx+1]) otpInputs[idx+1].focus();
      autoSubmit();
    });
    input.addEventListener('keydown',(e)=>{
      if(e.key==='Backspace' && !input.value && otpInputs[idx-1]){
        otpInputs[idx-1].focus(); otpInputs[idx-1].value='';
      }
    });
    input.addEventListener('paste',(e)=>{
      e.preventDefault();
      const paste = onlyDigits((e.clipboardData||window.clipboardData).getData('text')).slice(0,6);
      if(!paste) return;
      clearOTP();
      paste.split('').forEach((ch,i)=>{ if(otpInputs[i]) otpInputs[i].value=ch; });
      otpInputs[Math.min(paste.length,6)-1]?.focus();
      autoSubmit();
    });
  });

  bankMask?.addEventListener('click', closeOTP);
  bankClose?.addEventListener('click', closeOTP);
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeOTP(); });

  // Delegar clicks en la tabla
  document.addEventListener('click', e=>{
    const editBtn = e.target.closest('.js-inv-edit');
    if(editBtn){
      openOTP('editar',{
        edit: editBtn.dataset.editUrl,
        pin:  editBtn.dataset.pinUrl
      });
      return;
    }
    const delBtn = e.target.closest('.js-inv-delete');
    if(delBtn){
      openOTP('eliminar',{
        delete: delBtn.dataset.deleteUrl
      });
    }
  });
})();
</script>

@endsection