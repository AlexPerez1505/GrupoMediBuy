@extends('layouts.app')
@section('title', 'Clientes')
@section('titulo', 'Clientes')

@section('content')
@include('partials.submenu-cotizaciones')
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root{
    --bg:#f9fafb; --card:#ffffff; --ink:#111111; --text:#333333;
    --muted:#888888; --line:#ebebeb;
    --blue:#007aff; --blue-soft:#e6f0ff;
    --success:#15803d; --success-soft:#e6ffe6;
    --danger:#ff4a4a; --danger-soft:#ffebeb;
    --warning:#d97706; --warning-soft:#fef3c7;
    --purple:#7c3aed; --purple-soft:#ede9fe;
    --radius:16px; --shadow:0 4px 12px rgba(0,0,0,0.02);
}

*{box-sizing:border-box;}
body{margin:0;padding:0;background:var(--bg);color:var(--text);font-family:'Quicksand',sans-serif;overflow-x:hidden;}

.page{width:100%;max-width:1400px;margin:auto;padding:24px;}

/* ===== TOPBAR ===== */
.topbar{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;}
.title{margin:0;font-size:28px;font-weight:700;color:var(--ink);letter-spacing:-0.5px;}
.sub{margin:4px 0 0;color:var(--muted);font-size:15px;font-weight:500;}

/* ===== STATS ===== */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;}
.stat{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;transition:.2s ease;}
.stat:hover{transform:translateY(-2px);box-shadow:0 8px 16px rgba(0,0,0,.03);}
.stat-ic{width:46px;height:46px;border-radius:12px;display:grid;place-items:center;font-size:18px;}
.stat-ic.blue{background:var(--blue-soft);color:var(--blue);}
.stat-ic.green{background:var(--success-soft);color:var(--success);}
.stat-ic.purple{background:var(--purple-soft);color:var(--purple);}
.stat-ic.warn{background:var(--warning-soft);color:var(--warning);}
.stat-info .lbl{font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.stat-info .val{font-size:22px;font-weight:700;color:var(--ink);}

/* ===== BAR ===== */
.bar{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:20px;}
.search{flex:1;min-width:260px;display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:1px solid var(--line);background:var(--card);transition:.2s ease;position:relative;}
.search .ic{color:var(--muted);font-size:16px;}
.search input{width:100%;border:0;outline:0;background:transparent;font-family:'Quicksand',sans-serif;font-size:15px;font-weight:600;color:#111;}
.search input::placeholder{color:var(--muted);}
.search:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-soft);}
.search .kbd{font-size:11px;font-weight:700;color:var(--muted);padding:2px 6px;border:1px solid var(--line);border-radius:5px;background:var(--bg);}
.search .clear-btn{background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;border-radius:6px;display:none;}
.search .clear-btn:hover{background:var(--bg);color:var(--danger);}
.search.has-text .clear-btn{display:inline-flex;}
.search.has-text .kbd{display:none;}

.select-filter{padding:12px 16px;border-radius:10px;border:1px solid var(--line);background:var(--card);font-family:'Quicksand',sans-serif;font-size:14px;font-weight:600;color:var(--text);outline:none;cursor:pointer;min-width:160px;}
.select-filter:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-soft);}

.view-toggle{display:inline-flex;background:var(--card);border:1px solid var(--line);border-radius:10px;padding:4px;gap:4px;}
.view-toggle button{background:none;border:none;cursor:pointer;padding:8px 14px;border-radius:7px;font-weight:700;font-size:13px;color:var(--muted);display:inline-flex;align-items:center;gap:6px;transition:.18s ease;}
.view-toggle button.active{background:var(--blue);color:#fff;}
.view-toggle button:not(.active):hover{background:var(--bg);color:var(--text);}

.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:48px;padding:12px 22px;border-radius:10px;background:var(--blue);color:#fff;font-size:15px;font-weight:700;text-decoration:none;border:none;cursor:pointer;transition:.2s ease;white-space:nowrap;}
.btn-primary:hover{filter:brightness(1.05);transform:translateY(-1px);}

.results-info{margin:0 0 16px;font-size:13px;color:var(--muted);font-weight:600;}
.results-info b{color:var(--ink);}

/* ===== NOTICE ===== */
.notice{background:var(--danger-soft);border-radius:16px;padding:20px;margin-bottom:24px;}
.notice-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;flex-wrap:wrap;}
.notice-title{display:flex;align-items:center;gap:12px;font-weight:700;color:var(--danger);font-size:15px;}
.notice-count{color:var(--danger);font-size:13px;font-weight:700;}
.alertas{list-style:none;padding:0;margin:0;display:grid;gap:10px;}
.alertas li{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;}
.alertas .txt{font-size:14px;font-weight:600;color:var(--text);}
.btn-outline{background:var(--card);border:1px solid var(--blue);color:var(--blue);font-family:'Quicksand',sans-serif;font-weight:700;font-size:13px;border-radius:8px;padding:9px 14px;cursor:pointer;}

/* ===== BADGES ===== */
.badge-nuevo{display:inline-flex;align-items:center;gap:4px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:10px;font-weight:800;padding:3px 8px;border-radius:999px;text-transform:uppercase;letter-spacing:.5px;animation:pulse 2s ease-in-out infinite;}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.5);}50%{box-shadow:0 0 0 5px rgba(16,185,129,0);}}

.pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pill-si{background:var(--success-soft);color:var(--success);}
.pill-no{background:#f1f5f9;color:var(--muted);}
.pill-cat{background:#f1f5f9;color:#475569;border-radius:8px;font-size:12px;font-weight:700;padding:4px 10px;}

/* ===== CARDS GRID ===== */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;}
.card-c{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);padding:22px;display:flex;flex-direction:column;gap:18px;transition:.2s ease;animation:fadeIn .25s ease;}
.card-c:hover{transform:translateY(-3px);box-shadow:0 14px 26px rgba(0,0,0,.05);border-color:#d1d5db;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

.c-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;}
.who{display:flex;align-items:center;gap:14px;flex:1;min-width:0;}
.avatar{width:46px;height:46px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,var(--blue-soft),#dbeafe);color:var(--blue);font-weight:700;font-size:16px;flex-shrink:0;}
.info-who{flex:1;min-width:0;}
.name{margin:0;font-size:17px;font-weight:700;color:var(--ink);display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.meta{margin:3px 0 0;font-size:13px;color:var(--muted);}

.rows{display:grid;gap:12px;}
.rowx{display:flex;align-items:flex-start;gap:12px;}
.rowx .ic{width:22px;display:grid;place-items:center;color:var(--muted);flex-shrink:0;}
.rowx .k{font-size:11px;color:var(--muted);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;}
.rowx .v{font-size:14px;font-weight:600;color:var(--text);line-height:1.5;overflow-wrap:anywhere;}

.acciones{margin-top:auto;display:flex;justify-content:space-between;align-items:center;gap:10px;padding-top:14px;border-top:1px solid var(--line);flex-wrap:wrap;}
.acciones-btns{display:flex;gap:6px;}
.icon-btn{width:38px;height:38px;border-radius:9px;display:grid;place-items:center;background:transparent;color:var(--muted);text-decoration:none;border:none;cursor:pointer;transition:.15s ease;}
.icon-btn:hover{background:var(--bg);color:var(--text);}
.icon-btn.danger:hover{background:var(--danger-soft);color:var(--danger);}

/* ===== TABLA ===== */
.tabla-wrap{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.tabla-scroll{overflow-x:auto;max-height:70vh;}
.tabla{width:100%;border-collapse:collapse;font-size:14px;}
.tabla thead th{background:#f1f5fb;color:var(--muted);font-weight:700;padding:14px 16px;text-align:left;border-bottom:1px solid var(--line);white-space:nowrap;position:sticky;top:0;z-index:5;}
.tabla tbody tr{border-bottom:1px solid var(--line);transition:background .15s ease;}
.tabla tbody tr:hover{background:#f8faff;}
.tabla tbody td{padding:14px 16px;vertical-align:middle;color:var(--text);font-weight:600;}
.tabla .td-id{color:var(--muted);font-weight:700;font-size:13px;width:60px;}
.tabla .td-nombre .nombre-principal{font-weight:700;color:var(--ink);display:inline-flex;align-items:center;gap:8px;}
.tabla .td-nombre .nombre-sub{font-size:12px;color:var(--muted);margin-top:2px;}
.tabla .td-acciones{white-space:nowrap;text-align:right;}
.tabla .icon-btn{width:34px;height:34px;display:inline-grid;}

mark.hl{background:#fef08a;color:var(--ink);padding:0 2px;border-radius:3px;font-weight:700;}

/* ===== EMPTY ===== */
.empty-state{grid-column:1/-1;text-align:center;padding:64px 24px;background:var(--card);border:1px dashed var(--line);border-radius:var(--radius);}
.empty-state .ic{font-size:48px;color:var(--line);}
.empty-state h3{margin:14px 0 6px;color:var(--ink);}
.empty-state p{margin:0;color:var(--muted);}

/* ===== SUBLAYOUT ===== */
@media (min-width:768px){
    .page{
        width:auto !important;
        margin-left:calc(88px + clamp(16px,2vw,32px)) !important;
        margin-right:clamp(16px,2vw,32px) !important;
        max-width:calc(100% - 88px - clamp(32px,4vw,64px)) !important;
    }
}
@media (max-width:768px){
    .topbar{flex-direction:column;align-items:flex-start;}
    .bar{flex-direction:column;align-items:stretch;}
    .search,.select-filter,.btn-primary,.view-toggle{width:100%;}
    .view-toggle{justify-content:center;}
}
@media (max-width:480px){
    .grid{grid-template-columns:1fr;}
}
</style>

@php
  // Lista FIJA de asesores (igual a la del formulario de crear cliente)
  // Estos siempre aparecerán en el filtro, tengan o no clientes asignados.
  $asesoresFijos = [
    'Jesús Tellez',
    'Gabriela Diaz',
    'Joel Diaz',
    'Anahí Tellez',
    'Jose Alex',
    'Megan Diaz',
    'Victor Guerrero',
  ];

  // Stats
  $totalClientes  = count($clientes);
  $clientesNuevos = collect($clientes)->filter(fn($c) => isset($c->created_at) && \Carbon\Carbon::parse($c->created_at)->gte(now()->subDays(7)))->count();
  $conPromocion   = collect($clientes)->where('recibe_promocion', 1)->count();
  $pendientes     = $alertasGenerales->count() ?? 0;

  // Ordenar clientes por fecha de creación descendente (nuevos arriba)
  $clientesOrdenados = collect($clientes)->sortByDesc(function($c){
    return isset($c->created_at) ? \Carbon\Carbon::parse($c->created_at)->timestamp : 0;
  });
@endphp

<div class="page">

  {{-- TOPBAR --}}
  <div class="topbar">
    <div>
      <h1 class="title">Clientes</h1>
      <p class="sub">Administra y encuentra clientes rápidamente.</p>
    </div>
    <a href="{{ route('clientes.create') }}" class="btn-primary">
      <i class="fa-solid fa-plus"></i> Nuevo cliente
    </a>
  </div>

  {{-- STATS --}}
  <div class="stats">
    <div class="stat">
      <div class="stat-ic blue"><i class="fa-solid fa-users"></i></div>
      <div class="stat-info"><div class="lbl">Total</div><div class="val" id="contador-clientes">{{ $totalClientes }}</div></div>
    </div>
    <div class="stat">
      <div class="stat-ic green"><i class="fa-solid fa-user-plus"></i></div>
      <div class="stat-info"><div class="lbl">Nuevos (7d)</div><div class="val">{{ $clientesNuevos }}</div></div>
    </div>
    <div class="stat">
      <div class="stat-ic purple"><i class="fa-solid fa-gift"></i></div>
      <div class="stat-info"><div class="lbl">Con promoción</div><div class="val">{{ $conPromocion }}</div></div>
    </div>
    <div class="stat">
      <div class="stat-ic warn"><i class="fa-solid fa-bell"></i></div>
      <div class="stat-info"><div class="lbl">Pendientes</div><div class="val">{{ $pendientes }}</div></div>
    </div>
  </div>

  {{-- BAR DE FILTROS --}}
  <div class="bar">
    <div class="search" role="search" id="searchBox">
      <div class="ic"><i class="fa-solid fa-magnifying-glass"></i></div>
      <input type="search" id="searchCliente" placeholder="Buscar por nombre, teléfono, correo, asesor...">
      <button class="clear-btn" id="clearSearch" type="button" title="Limpiar"><i class="fa-solid fa-xmark"></i></button>
      <span class="kbd">Ctrl K</span>
    </div>

    {{-- ✅ Asesores: lista FIJA renderizada desde Blade --}}
    <select id="filterAsesor" class="select-filter" aria-label="Filtrar por asesor">
      <option value="">Todos los asesores</option>
      @foreach($asesoresFijos as $asesor)
        <option value="{{ strtolower(\Illuminate\Support\Str::ascii($asesor)) }}">{{ $asesor }}</option>
      @endforeach
    </select>

    <select id="filterPromo" class="select-filter" aria-label="Filtrar por promoción">
      <option value="">Promoción: todos</option>
      <option value="1">Con promoción</option>
      <option value="0">Sin promoción</option>
    </select>

    <select id="orderBy" class="select-filter" aria-label="Ordenar">
      <option value="nuevo" selected>Más recientes</option>
      <option value="nombre">Nombre A–Z</option>
      <option value="asesor">Asesor A–Z</option>
      <option value="default">Orden original</option>
    </select>

    <div class="view-toggle" role="tablist" aria-label="Cambiar vista">
      <button id="btnViewCards" type="button"><i class="fa-solid fa-grip"></i> Cards</button>
      <button id="btnViewTable" class="active" type="button"><i class="fa-solid fa-table-list"></i> Tabla</button>
    </div>
  </div>

  <p class="results-info">
    Mostrando <b id="resCount">{{ $totalClientes }}</b> de <b>{{ $totalClientes }}</b> clientes
  </p>

  {{-- ALERTAS --}}
  @if($alertasGenerales->isNotEmpty())
    <div class="notice">
      <div class="notice-head">
        <div class="notice-title"><i class="fa-solid fa-bell"></i> Atenciones pendientes</div>
        <div class="notice-count">{{ $alertasGenerales->count() }} pendiente(s)</div>
      </div>
      <ul class="alertas">
        @foreach($alertasGenerales as $alerta)
          <li>
            <div class="txt">
              @if(isset($alerta['cliente']))
                {{ $alerta['cliente']->nombre }} {{ $alerta['cliente']->apellido }} –
              @else
                Cliente no disponible –
              @endif
              @if($alerta['dias'] < 0)
                seguimiento vencido ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
              @elseif($alerta['dias'] === 0)
                seguimiento para hoy ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
              @else
                seguimiento en {{ $alerta['dias'] }} días ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
              @endif
            </div>
            <form method="POST" action="{{ route('seguimientos.completar', $alerta['seguimiento_id']) }}" class="m-0">
              @csrf @method('PATCH')
              <button type="submit" class="btn-outline">Completado</button>
            </form>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- VISTA CARDS --}}
  <div class="grid" id="view-cards" style="display:none;">
    @forelse($clientesOrdenados as $cliente)
      @php
        $esNuevo    = isset($cliente->created_at) && \Carbon\Carbon::parse($cliente->created_at)->gte(now()->subDays(7));
        $catName    = $cliente->categoria->nombre ?? 'Sin categoría';
        $asesorName = $cliente->asesor ?? '';
        $iniciales  = strtoupper(mb_substr($cliente->nombre ?? '?',0,1).mb_substr($cliente->apellido ?? '',0,1));
        $createdTs  = isset($cliente->created_at) ? \Carbon\Carbon::parse($cliente->created_at)->timestamp : 0;
      @endphp
      <div class="card-c"
           data-asesor="{{ $asesorName }}"
           data-promo="{{ $cliente->recibe_promocion ? '1' : '0' }}"
           data-nuevo="{{ $esNuevo ? '1' : '0' }}"
           data-created="{{ $createdTs }}"
           data-nombre="{{ strtolower($cliente->nombre.' '.$cliente->apellido) }}"
           data-search="{{ strtolower($cliente->nombre.' '.$cliente->apellido.' '.$cliente->telefono.' '.($cliente->email ?? '').' '.$asesorName.' '.($cliente->congreso_conocido ?? '')) }}">
        <div class="c-head">
          <div class="who">
            <div class="avatar">{{ $iniciales }}</div>
            <div class="info-who">
              <h3 class="name">
                <span class="name-text">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                @if($esNuevo)<span class="badge-nuevo"><i class="fa-solid fa-sparkles"></i> Nuevo</span>@endif
              </h3>
              <p class="meta">#{{ $cliente->id }} · {{ $asesorName ?: 'Sin asesor' }}</p>
            </div>
          </div>
        </div>

        <div class="rows">
          @if($cliente->telefono)
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-phone"></i></div>
            <div><div class="k">Teléfono</div><div class="v">{{ $cliente->telefono }}</div></div>
          </div>
          @endif
          @if($cliente->email)
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-envelope"></i></div>
            <div><div class="k">Correo</div><div class="v">{{ $cliente->email }}</div></div>
          </div>
          @endif
          @if($cliente->congreso_conocido)
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-building-columns"></i></div>
            <div><div class="k">Congreso</div><div class="v">{{ $cliente->congreso_conocido }}</div></div>
          </div>
          @endif
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-gift"></i></div>
            <div>
              <div class="k">Promoción</div>
              <div class="v">
                @if($cliente->recibe_promocion)<span class="pill pill-si">SÍ</span>@else<span class="pill pill-no">NO</span>@endif
              </div>
            </div>
          </div>
        </div>

        <div class="acciones">
          <span class="pill-cat">{{ $catName }}</span>
          <div class="acciones-btns">
            <a class="icon-btn" href="{{ route('seguimientos.index', $cliente->id) }}" title="Seguimientos"><i class="fa-solid fa-file-lines"></i></a>
            <a class="icon-btn" href="{{ route('clientes.edit', $cliente->id) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            <form method="POST" action="{{ route('clientes.destroy', $cliente->id) }}" class="form-eliminar-cliente d-inline m-0" data-nombre="{{ $cliente->nombre }} {{ $cliente->apellido }}">
              @csrf @method('DELETE')
              <button type="submit" class="icon-btn danger btn-delete" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <div class="ic"><i class="fa-solid fa-folder-open"></i></div>
        <h3>Sin clientes</h3>
        <p>Aún no hay clientes registrados.</p>
      </div>
    @endforelse
  </div>

  {{-- VISTA TABLA (DEFAULT) --}}
  <div class="tabla-wrap" id="view-table">
    <div class="tabla-scroll">
      <table class="tabla">
        <thead>
          <tr>
            <th>ID</th><th>Cliente</th><th>Asesor</th><th>Promoción</th>
            <th>Congreso</th><th>Categoría</th><th style="text-align:right;">Acciones</th>
          </tr>
        </thead>
        <tbody id="lista-clientes">
          @forelse($clientesOrdenados as $cliente)
            @php
              $esNuevo    = isset($cliente->created_at) && \Carbon\Carbon::parse($cliente->created_at)->gte(now()->subDays(7));
              $catName    = $cliente->categoria->nombre ?? 'Sin categoría';
              $asesorName = $cliente->asesor ?? '';
              $createdTs  = isset($cliente->created_at) ? \Carbon\Carbon::parse($cliente->created_at)->timestamp : 0;
            @endphp
            <tr
              data-asesor="{{ $asesorName }}"
              data-promo="{{ $cliente->recibe_promocion ? '1' : '0' }}"
              data-nuevo="{{ $esNuevo ? '1' : '0' }}"
              data-created="{{ $createdTs }}"
              data-nombre="{{ strtolower($cliente->nombre.' '.$cliente->apellido) }}"
              data-search="{{ strtolower($cliente->nombre.' '.$cliente->apellido.' '.$cliente->telefono.' '.($cliente->email ?? '').' '.$asesorName.' '.($cliente->congreso_conocido ?? '')) }}">
              <td class="td-id">#{{ $cliente->id }}</td>
              <td class="td-nombre">
                <div class="nombre-principal">
                  <span class="name-text">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                  @if($esNuevo)<span class="badge-nuevo"><i class="fa-solid fa-sparkles"></i> Nuevo</span>@endif
                </div>
                @if($cliente->telefono)<div class="nombre-sub">{{ $cliente->telefono }}</div>@endif
              </td>
              <td>{{ $asesorName ?: '—' }}</td>
              <td>
                @if($cliente->recibe_promocion)<span class="pill pill-si">SÍ</span>@else<span class="pill pill-no">NO</span>@endif
              </td>
              <td>{{ $cliente->congreso_conocido ?: '—' }}</td>
              <td><span class="pill-cat">{{ $catName }}</span></td>
              <td class="td-acciones">
                <a class="icon-btn" href="{{ route('seguimientos.index', $cliente->id) }}" title="Seguimientos"><i class="fa-solid fa-file-lines"></i></a>
                <a class="icon-btn" href="{{ route('clientes.edit', $cliente->id) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
                <form method="POST" action="{{ route('clientes.destroy', $cliente->id) }}" class="form-eliminar-cliente d-inline m-0" data-nombre="{{ $cliente->nombre }} {{ $cliente->apellido }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="icon-btn danger btn-delete" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">No hay clientes registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div id="empty-state" class="empty-state" style="display:none;margin-top:16px;">
    <div class="ic"><i class="fa-solid fa-folder-open"></i></div>
    <h3>No encontramos resultados</h3>
    <p>Intenta con otras palabras o limpia los filtros.</p>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const input        = document.getElementById('searchCliente');
  const searchBox    = document.getElementById('searchBox');
  const clearBtn     = document.getElementById('clearSearch');
  const selAsesor    = document.getElementById('filterAsesor');
  const selPromo     = document.getElementById('filterPromo');
  const orderBy      = document.getElementById('orderBy');
  const btnCards     = document.getElementById('btnViewCards');
  const btnTable     = document.getElementById('btnViewTable');
  const viewCards    = document.getElementById('view-cards');
  const viewTable    = document.getElementById('view-table');
  const emptyState   = document.getElementById('empty-state');
  const resCount     = document.getElementById('resCount');
  const contadorTop  = document.getElementById('contador-clientes');

  // Normaliza quitando acentos y minúsculas, también colapsa espacios
  const norm = s => (s||'').toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase().replace(/\s+/g,' ').trim();

  // ===== Toggle Vista (con localStorage) — DEFAULT: tabla =====
  function setView(mode){
    const isCards = mode === 'cards';
    viewCards.style.display = isCards ? '' : 'none';
    viewTable.style.display = isCards ? 'none' : '';
    btnCards.classList.toggle('active', isCards);
    btnTable.classList.toggle('active', !isCards);
    localStorage.setItem('clientes_view', mode);
  }
  btnCards.addEventListener('click', () => setView('cards'));
  btnTable.addEventListener('click', () => setView('table'));
  setView(localStorage.getItem('clientes_view') || 'table'); // <- tabla por defecto

  // ===== Cache filas/cards =====
  const cards = Array.from(viewCards.querySelectorAll('.card-c'));
  const trs   = Array.from(viewTable.querySelectorAll('#lista-clientes tr[data-search]'));

  // ===== Highlight helpers =====
  function clearHighlights(){
    document.querySelectorAll('.name-text').forEach(el => { el.innerHTML = el.textContent; });
  }
  function highlight(term){
    if(!term) return;
    const re = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')','gi');
    document.querySelectorAll('.name-text').forEach(el => {
      el.innerHTML = el.textContent.replace(re, '<mark class="hl">$1</mark>');
    });
  }

  // ===== Aplicar filtros =====
  function applyFilters(){
    const q       = norm(input.value);
    const fAse    = (selAsesor.value || '').trim().toLowerCase();
    const fPromo  = selPromo.value;
    let visibleC = 0, visibleT = 0;

    clearHighlights();

    const passes = (el) => {
      const matchText   = !q || norm(el.dataset.search).includes(q);
      const matchAsesor = !fAse || norm(el.dataset.asesor) === fAse;
      const matchPromo  = !fPromo || el.dataset.promo === fPromo;
      return matchText && matchAsesor && matchPromo;
    };

    cards.forEach(el => {
      const show = passes(el);
      el.style.display = show ? '' : 'none';
      if(show) visibleC++;
    });
    trs.forEach(el => {
      const show = passes(el);
      el.style.display = show ? '' : 'none';
      if(show) visibleT++;
    });

    if(q) highlight(input.value.trim());

    const visible = Math.max(visibleC, visibleT);
    resCount.textContent = visible;
    if(contadorTop) contadorTop.textContent = visible;
    emptyState.style.display = visible === 0 ? 'block' : 'none';

    searchBox.classList.toggle('has-text', !!input.value);
  }

  // ===== Ordenar =====
  function sortItems(mode){
    const sorter = (a,b) => {
      if(mode === 'nombre')  return (a.dataset.nombre||'').localeCompare(b.dataset.nombre||'','es');
      if(mode === 'asesor')  return (a.dataset.asesor||'').localeCompare(b.dataset.asesor||'','es');
      if(mode === 'nuevo')   return (parseInt(b.dataset.created)||0) - (parseInt(a.dataset.created)||0);
      return 0;
    };
    if(mode !== 'default'){
      cards.sort(sorter).forEach(el => viewCards.appendChild(el));
      const tbody = document.getElementById('lista-clientes');
      trs.sort(sorter).forEach(el => tbody.appendChild(el));
    }
  }
  orderBy.addEventListener('change', () => sortItems(orderBy.value));

  // ===== Debounce =====
  const debounce = (fn,w) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), w); }; };
  input.addEventListener('input', debounce(applyFilters, 200));
  input.addEventListener('search', applyFilters);
  selAsesor.addEventListener('change', applyFilters);
  selPromo.addEventListener('change', applyFilters);

  // ===== Clear =====
  clearBtn.addEventListener('click', () => { input.value = ''; applyFilters(); input.focus(); });

  // ===== Atajo Ctrl/Cmd + K =====
  document.addEventListener('keydown', e => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k'){
      e.preventDefault(); input.focus(); input.select();
    }
  });

  // ===== Aplicar orden inicial (nuevos arriba) =====
  sortItems('nuevo');

  // ===== Toast & Confirm =====
  const Toast = Swal.mixin({
    toast:true, position:'top-end', showConfirmButton:false,
    timer:3400, timerProgressBar:true
  });
  @if(session('success'))
    Toast.fire({ icon:'success', title:'Éxito', text:@json(session('success')) });
  @endif
  @if(session('error'))
    Toast.fire({ icon:'error', title:'Ocurrió un problema', text:@json(session('error')) });
  @endif

  document.querySelectorAll('.form-eliminar-cliente').forEach(form => {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      const nombre = this.dataset.nombre || 'este cliente';
      Swal.fire({
        icon:'warning', title:'Eliminar cliente',
        html:`Vas a eliminar a <b>${nombre}</b>.<br>Esta acción no se puede deshacer.`,
        showCancelButton:true, confirmButtonText:'Sí, eliminar',
        cancelButtonText:'Cancelar', reverseButtons:true, focusCancel:true
      }).then(r => { if(r.isConfirmed) form.submit(); });
    });
  });
});
</script>
@endsection