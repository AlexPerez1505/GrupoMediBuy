@extends('layouts.app')
@section('title', 'Activos')
@section('titulo', 'Gestión de Activos')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php
  $serviciosCollection = $servicios instanceof \Illuminate\Pagination\AbstractPaginator
      ? $servicios->getCollection()
      : collect($servicios);

  $resolverAmbito = function($s){
      $raw = strtolower(trim((string)(
          $s->mantenimiento_tipo
          ?? $s->ambito
          ?? $s->tipo_servicio
          ?? $s->interno_externo
          ?? $s->origen
          ?? ''
      )));

      if ($raw !== '') {
          if (str_contains($raw, 'extern') || str_contains($raw, 'fuera')) return 'externos';
          if (str_contains($raw, 'intern')) return 'internos';
      }

      return 'internos';
  };

  $internosCount = $serviciosCollection->filter(fn($s) => $resolverAmbito($s) === 'internos')->count();
  $externosCount = $serviciosCollection->filter(fn($s) => $resolverAmbito($s) === 'externos')->count();
  $totalCount = $internosCount + $externosCount;
@endphp

<style>
:root {
  /* PALETA CORPORATIVA ESTRICTA */
  --bg: #f9fafb; 
  --card: #ffffff; 
  --title: #111111;
  --ink: #333333; 
  --muted: #888888; 
  --line: #ebebeb; 
  --blue: #007aff; 
  --blue-soft: #e6f0ff; 
  --success: #15803d; 
  --success-soft: #e6ffe6;  
  --danger: #ff4a4a; 
  --danger-soft: #ffebeb;

  /* VARIABLES DE DISEÑO MÍNIMO */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-base: 0 4px 12px rgba(0,0,0,0.02);
  --shadow-hover: 0 8px 24px rgba(0,0,0,0.06);
  --shadow-focus: 0 0 0 3px var(--blue-soft);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --font-family: 'Quicksand', sans-serif;
}

/* RESET & BASE */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background-color: var(--bg);
  font-family: var(--font-family);
  color: var(--ink);
  -webkit-font-smoothing: antialiased;
}
h1, h2, h3, h4, h5, h6 { color: var(--title); font-weight: 700; margin-bottom: 0.5rem; }

/* LAYOUT */
.premium-wrapper {
  max-width: 1400px;
  margin: 0 auto;
  padding: 48px 24px;
}

/* UTILIDADES Y TEXTO */
.text-muted { color: var(--muted); }
.text-center { text-align: center; }
.font-monospace { font-family: monospace; font-size: 0.9em; letter-spacing: 0.05em; color: var(--title); }

/* ANIMACIONES */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-enter { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }

/* HEADER Y STATS */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 40px;
  flex-wrap: wrap;
  gap: 24px;
}
.header-title-block h1 { font-size: 2rem; letter-spacing: -0.02em; }
.header-title-block p { color: var(--muted); font-size: 1rem; font-weight: 500; }

.quick-stats-container {
  display: flex;
  gap: 16px;
}
.stat-card {
  background: var(--card);
  border: 1px solid var(--line);
  padding: 16px 24px;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-base);
  display: flex;
  flex-direction: column;
  min-width: 130px;
  transition: var(--transition);
}
.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}
.stat-label {
  font-size: 0.75rem;
  color: var(--muted);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--title);
  margin-top: 4px;
}

/* ALERTAS */
.alert-box {
  padding: 16px 20px;
  border-radius: var(--radius-md);
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  animation: fadeUp 0.4s ease forwards;
}
.alert-box.success { background: var(--success-soft); color: var(--success); }
.alert-box.danger { background: var(--danger-soft); color: var(--danger); }

/* TOOLBAR */
.premium-toolbar {
  background: var(--card);
  border-radius: var(--radius-lg);
  padding: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  box-shadow: var(--shadow-base);
  margin-bottom: 32px;
  border: 1px solid var(--line);
}

/* TABS ESTILO APPLE */
.custom-tabs {
  display: flex;
  background: var(--bg);
  padding: 6px;
  border-radius: var(--radius-md);
  border: 1px solid var(--line);
  position: relative;
}
.tab-btn {
  flex: 1;
  background: transparent;
  border: none;
  padding: 10px 24px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--muted);
  cursor: pointer;
  transition: var(--transition);
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.tab-btn.active { color: var(--blue); }
.tab-indicator {
  position: absolute;
  top: 6px; bottom: 6px;
  width: calc(50% - 6px);
  background: var(--card);
  border-radius: var(--radius-sm);
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  z-index: 1;
}
.indicator-right { transform: translateX(100%); }
.tab-badge {
  background: var(--line);
  color: var(--ink);
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  transition: var(--transition);
}
.tab-btn.active .tab-badge {
  background: var(--blue-soft);
  color: var(--blue);
}

/* INPUTS Y FORMULARIOS */
.toolbar-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
  justify-content: flex-end;
  flex-wrap: wrap;
}
.search-wrapper {
  position: relative;
  max-width: 350px;
  width: 100%;
}
.search-wrapper i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
}
.premium-input, .premium-select {
  width: 100%;
  padding: 12px 16px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--ink);
  transition: var(--transition);
}
.premium-input { padding-left: 44px; }
.premium-select { width: auto; min-width: 220px; cursor: pointer; }
.premium-input:focus, .premium-select:focus {
  outline: none;
  border-color: var(--blue);
  box-shadow: var(--shadow-focus);
}

/* BOTONES */
.btn-primary {
  background: var(--blue);
  color: var(--card);
  border: none;
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-weight: 600;
  font-size: 0.9rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: var(--transition);
  text-decoration: none;
  cursor: pointer;
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 122, 255, 0.2);
}
.btn-primary:active { transform: scale(0.98); }

.btn-icon-outline {
  background: var(--card);
  border: 1px solid var(--line);
  color: var(--muted);
  width: 44px; height: 44px;
  border-radius: var(--radius-sm);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  cursor: pointer;
  font-size: 1.1rem;
}
.btn-icon-outline:hover {
  color: var(--blue);
  border-color: var(--blue);
  background: var(--blue-soft);
  transform: translateY(-2px);
}

/* TOOLTIP PERSONALIZADO EN HOVER */
[data-tooltip] {
  position: relative;
}
[data-tooltip]::before,
[data-tooltip]::after {
  position: absolute;
  left: 50%;
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  transition: var(--transition);
  z-index: 9999;
}
[data-tooltip]::before {
  content: attr(data-tooltip);
  bottom: calc(100% + 12px);
  transform: translateX(-50%) translateY(6px);
  background: var(--title);
  color: #ffffff;
  padding: 8px 12px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-size: 0.78rem;
  font-weight: 600;
  white-space: nowrap;
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
[data-tooltip]::after {
  content: '';
  bottom: calc(100% + 6px);
  transform: translateX(-50%) translateY(6px);
  border: 6px solid transparent;
  border-top-color: var(--title);
}
[data-tooltip]:hover::before,
[data-tooltip]:hover::after,
[data-tooltip]:focus-visible::before,
[data-tooltip]:focus-visible::after {
  opacity: 1;
  visibility: visible;
  transform: translateX(-50%) translateY(0);
}
.action-buttons [data-tooltip]:first-child::before { left: 0; transform: translateX(0) translateY(6px); }
.action-buttons [data-tooltip]:first-child::after { left: 18px; transform: translateX(0) translateY(6px); }
.action-buttons [data-tooltip]:first-child:hover::before,
.action-buttons [data-tooltip]:first-child:focus-visible::before { transform: translateX(0) translateY(0); }
.action-buttons [data-tooltip]:first-child:hover::after,
.action-buttons [data-tooltip]:first-child:focus-visible::after { transform: translateX(0) translateY(0); }

.action-buttons [data-tooltip]:last-child::before,
.action-buttons form:last-child [data-tooltip]::before {
  left: auto;
  right: 0;
  transform: translateX(0) translateY(6px);
}
.action-buttons [data-tooltip]:last-child::after,
.action-buttons form:last-child [data-tooltip]::after {
  left: auto;
  right: 18px;
  transform: translateX(0) translateY(6px);
}
.action-buttons [data-tooltip]:last-child:hover::before,
.action-buttons [data-tooltip]:last-child:focus-visible::before,
.action-buttons form:last-child [data-tooltip]:hover::before,
.action-buttons form:last-child [data-tooltip]:focus-visible::before {
  transform: translateX(0) translateY(0);
}
.action-buttons [data-tooltip]:last-child:hover::after,
.action-buttons [data-tooltip]:last-child:focus-visible::after,
.action-buttons form:last-child [data-tooltip]:hover::after,
.action-buttons form:last-child [data-tooltip]:focus-visible::after {
  transform: translateX(0) translateY(0);
}

/* TABLA CORPORATIVA */
.table-container {
  background: var(--card);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-base);
  border: 1px solid var(--line);
  overflow: hidden;
}
.table-responsive { width: 100%; overflow-x: auto; }
.corp-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}
.corp-table th {
  background: var(--bg);
  padding: 18px 24px;
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid var(--line);
  white-space: nowrap;
}
.corp-table td {
  padding: 18px 24px;
  font-size: 0.9rem;
  font-weight: 500;
  border-bottom: 1px solid var(--line);
  vertical-align: middle;
  transition: var(--transition);
}
.corp-table tbody tr { transition: var(--transition); background: var(--card); }
.corp-table tbody tr:last-child td { border-bottom: none; }
.corp-table tbody tr:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
  position: relative;
  z-index: 10;
}

/* ELEMENTOS DE TABLA */
.item-cell { display: flex; align-items: center; gap: 16px; }
.item-img-box {
  width: 52px; height: 52px;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--bg);
  border: 1px solid var(--line);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.item-img-box img { width: 100%; height: 100%; object-fit: cover; }
.item-img-box i { font-size: 1.5rem; color: var(--muted); }

.item-info-title { font-weight: 700; color: var(--title); margin-bottom: 4px; font-size: 1rem; }
.item-info-sub { font-size: 0.8rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }

/* ETIQUETAS (BADGES) */
.corp-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
}
.corp-badge::before { content: ''; width: 8px; height: 8px; border-radius: 50%; }

.badge-success { background: var(--success-soft); color: var(--success); }
.badge-success::before { background: var(--success); }

.badge-danger { background: var(--danger-soft); color: var(--danger); }
.badge-danger::before { background: var(--danger); }

.badge-info { background: var(--blue-soft); color: var(--blue); }
.badge-info::before { background: var(--blue); }

.badge-neutral { background: var(--bg); color: var(--muted); border: 1px solid var(--line); }
.badge-neutral::before { background: var(--muted); }

/* ACCIONES DE TABLA */
.action-cell {
  opacity: 0.6;
  transition: var(--transition);
  text-align: right;
}
.corp-table tbody tr:hover .action-cell { opacity: 1; }
.action-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
.btn-ghost {
  background: transparent;
  color: var(--muted);
  border: none;
  width: 38px; height: 38px;
  border-radius: var(--radius-sm);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  text-decoration: none;
  cursor: pointer;
  font-size: 1.1rem;
}
.btn-ghost:hover {
  background: var(--bg);
  color: var(--blue);
  transform: translateY(-2px);
}
.btn-ghost:active { transform: scale(0.95); }
.btn-ghost.delete:hover { background: var(--danger-soft); color: var(--danger); }
.btn-ghost.success:hover { background: var(--success-soft); color: var(--success); }

/* ASIGNACIÓN */
.user-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: var(--bg);
  border: 1px solid var(--line);
  color: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
}
.assignment-cell {
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  color: var(--ink);
}

/* PAGINACIÓN LARAVEL SIN BOOTSTRAP */
.pagination { display: flex; gap: 8px; list-style: none; padding: 0; justify-content: center; margin-top: 32px; flex-wrap: wrap; }
.page-item .page-link {
  border: 1px solid var(--line);
  background: var(--card);
  color: var(--ink);
  padding: 10px 16px;
  border-radius: var(--radius-sm);
  text-decoration: none;
  transition: var(--transition);
  font-weight: 600;
  font-family: var(--font-family);
}
.page-item.active .page-link { background: var(--blue); color: #fff; border-color: var(--blue); }
.page-item.disabled .page-link { color: var(--muted); background: var(--bg); cursor: not-allowed; }
.page-item .page-link:hover:not(.disabled) { background: var(--blue-soft); color: var(--blue); border-color: var(--blue-soft); transform: translateY(-2px); }

/* RESPONSIVE */
@media (max-width: 992px) {
  .dashboard-header { flex-direction: column; align-items: flex-start; }
  .quick-stats-container { width: 100%; overflow-x: auto; padding-bottom: 12px; }
  .premium-toolbar { flex-direction: column; align-items: stretch; }
  .custom-tabs { width: 100%; }
  .search-wrapper { max-width: 100%; }

  .corp-table thead { display: none; }
  .corp-table, .corp-table tbody, .corp-table tr, .corp-table td { display: block; width: 100%; }
  
  .corp-table tr {
    margin-bottom: 24px;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-base);
    padding: 16px;
  }
  .corp-table td {
    padding: 12px 0;
    border-bottom: 1px solid var(--line);
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: right;
  }
  .corp-table td::before {
    content: attr(data-label);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    text-align: left;
  }
  .corp-table td:last-child { border-bottom: none; }
  .corp-table td[data-label="Equipo"] {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  .corp-table td[data-label="Equipo"]::before { display: none; }
  
  .action-cell { opacity: 1; justify-content: center; margin-top: 8px; }
  .action-buttons { width: 100%; justify-content: center; }
}
</style>

<div class="premium-wrapper" x-data="CorporateDashboard('{{ request('tab', 'internos') }}')">

  <div class="dashboard-header animate-enter">
    <div class="header-title-block">
      <h1>Inventario Corporativo</h1>
      <p>Administración, trazabilidad y control del proceso obligatorio de servicio.</p>
    </div>

    <div class="quick-stats-container">
      <div class="stat-card">
        <span class="stat-label">Total Activos</span>
        <span class="stat-value">{{ $totalCount }}</span>
      </div>
      <div class="stat-card">
        <span class="stat-label" style="color: var(--blue);">Internos</span>
        <span class="stat-value">{{ $internosCount }}</span>
      </div>
      <div class="stat-card">
        <span class="stat-label" style="color: #635bff;">Externos</span>
        <span class="stat-value">{{ $externosCount }}</span>
      </div>
    </div>
  </div>

  @if (session('ok'))
    <div class="alert-box success">
      <i class="bi bi-check-circle-fill"></i> {{ session('ok') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert-box danger">
      <i class="bi bi-x-octagon-fill"></i> {{ session('error') }}
    </div>
  @endif

  <div class="premium-toolbar animate-enter delay-1">
    <div class="custom-tabs">
      <div class="tab-indicator" :class="{ 'indicator-right': $store.dashboard.tab === 'externos' }"></div>

      <button class="tab-btn" :class="{ 'active': $store.dashboard.tab === 'internos' }" @click="setTab('internos')" type="button">
        <i class="bi bi-building"></i> Internos
        <span class="tab-badge">{{ $internosCount }}</span>
      </button>

      <button class="tab-btn" :class="{ 'active': $store.dashboard.tab === 'externos' }" @click="setTab('externos')" type="button">
        <i class="bi bi-box-arrow-up-right"></i> Externos
        <span class="tab-badge">{{ $externosCount }}</span>
      </button>
    </div>

    <div class="toolbar-actions">
      <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" class="premium-input" placeholder="Buscar por serie, modelo, doctor..." x-model="$store.dashboard.search">
      </div>

      <select class="premium-select" x-model="$store.dashboard.status">
        <option value="">Cualquier estado</option>
        <option value="pendiente_entrega">Pendiente entrega</option>
        <option value="pendiente_salida_foraneo">Pendiente salida foráneo</option>
        <option value="pendiente_regreso_foraneo">Pendiente regreso foráneo</option>
        <option value="pendiente_salida_cliente">Pendiente salida cliente</option>
        <option value="requiere_os">Requiere OS</option>
        <option value="completado">Completado</option>
        <option value="defectuoso">Defectuoso</option>
      </select>

      <button class="btn-icon-outline" onclick="location.reload()" data-tooltip="Recargar datos" aria-label="Recargar datos" type="button">
        <i class="bi bi-arrow-clockwise"></i>
      </button>

      @php
        $nuevoActivoUrl = Route::has('servicios.create') ? route('servicios.create') : url('/servicio');
      @endphp

      <a href="{{ $nuevoActivoUrl }}" class="btn-primary" data-tooltip="Registrar nuevo activo" aria-label="Registrar nuevo activo">
        <i class="bi bi-plus-lg"></i> Nuevo
      </a>
    </div>
  </div>

  <div class="table-container animate-enter delay-2">
    <div class="table-responsive">
      <table class="corp-table">
        <thead>
          <tr>
            <th>Especificaciones del Equipo</th>
            <th>Número de Serie</th>
            <th>Ámbito</th>
            <th>Estado Operativo</th>
            <th>Asignación</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($servicios as $s)
            @php
              $estado = $s->estado_proceso ?? 'registro';

              $badgeClass = match($estado){
                'requiere_os'               => 'badge-info',
                'os_validada'               => 'badge-success',
                'defectuoso'                => 'badge-danger',
                'pendiente_entrega'         => 'badge-info',
                'pendiente_salida_foraneo'  => 'badge-info',
                'pendiente_regreso_foraneo' => 'badge-info',
                'pendiente_salida_cliente'  => 'badge-info',
                'completado'                => 'badge-success',
                default                     => 'badge-neutral',
              };

              $estadoTexto = match($estado){
                'requiere_os'               => 'Requiere OS',
                'os_validada'               => 'OS validada',
                'pendiente_entrega'         => 'Pendiente entrega',
                'pendiente_salida_foraneo'  => 'Pendiente salida foráneo',
                'pendiente_regreso_foraneo' => 'Pendiente regreso foráneo',
                'pendiente_salida_cliente'  => 'Pendiente salida cliente',
                'completado'                => 'Completado',
                default                     => ucfirst(str_replace('_',' ',$estado)),
              };

              $ambito = $resolverAmbito($s);
              $ambitoTexto = $ambito === 'externos' ? 'Externo' : 'Interno';
              $ambitoIcon = $ambito === 'externos' ? 'bi-box-arrow-up-right' : 'bi-building';

              $foto = $s->evidencia1 ?? null;
              if ($foto && !\Illuminate\Support\Str::startsWith($foto, ['http://', 'https://'])) {
                  $foto = asset('storage/' . ltrim(preg_replace('#^/?storage/#', '', $foto), '/'));
              }
            @endphp

            <tr class="trow"
                x-show="filtrar(
                  @js([
                    'tipo'    => $s->tipo_equipo,
                    'subtipo' => $s->subtipo_equipo,
                    'marca'   => $s->marca,
                    'modelo'  => $s->modelo,
                    'serie'   => $s->numero_serie,
                    'estado'  => $estado,
                    'user'    => $s->user_name,
                    'doctor'  => $s->nombre_doctor,
                    'ambito'  => $ambito,
                  ])
                )"
                x-transition.opacity.duration.300ms>

              <td data-label="Equipo">
                <div class="item-cell">
                  <div class="item-img-box">
                    @if($foto)
                      <img src="{{ $foto }}" alt="Foto">
                    @else
                      <i class="bi bi-display"></i>
                    @endif
                  </div>
                  <div>
                    <div class="item-info-title">{{ $s->tipo_equipo ?? 'Equipo General' }}</div>
                    <div class="item-info-sub">
                      <i class="bi bi-tag-fill"></i>
                      {{ trim(($s->marca ?? '') . ' ' . ($s->modelo ?? '')) ?: 'Especificación pendiente' }}
                    </div>
                  </div>
                </div>
              </td>

              <td data-label="Serie">
                <span class="font-monospace">
                  <i class="bi bi-upc-scan" style="color: var(--muted); margin-right: 4px;"></i> 
                  {{ $s->numero_serie ?? 'N/A' }}
                </span>
              </td>

              <td data-label="Ámbito">
                <span style="color: var(--title); font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="bi {{ $ambitoIcon }} text-muted"></i> {{ $ambitoTexto }}
                </span>
              </td>

              <td data-label="Estado">
                <span class="corp-badge {{ $badgeClass }}">
                  {{ $estadoTexto }}
                </span>
              </td>

              <td data-label="Asignación">
                <div class="assignment-cell">
                  <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                  </div>
                  {{ $s->nombre_doctor ?? 'Sin asignación' }}
                </div>
              </td>

              <td data-label="Acciones" class="action-cell">
                <div class="action-buttons">
                  @if(Route::has('servicios.show'))
                    <a href="{{ route('servicios.show', $s->id) }}" class="btn-ghost" data-tooltip="Inspeccionar detalle" aria-label="Inspeccionar detalle">
                      <i class="bi bi-eye"></i>
                    </a>
                  @endif

                  @if(Route::has('servicio.proceso'))
                    <a href="{{ route('servicio.proceso', $s->id) }}" class="btn-ghost" data-tooltip="Abrir proceso" aria-label="Abrir proceso">
                      <i class="bi bi-diagram-3"></i>
                    </a>
                  @endif

                  @if(($s->estado_proceso ?? '') === 'requiere_os' && Route::has('servicio.os.form'))
                    <a href="{{ route('servicio.os.form', $s->id) }}" class="btn-ghost success" data-tooltip="Validar Orden de Servicio" aria-label="Validar Orden de Servicio">
                      <i class="bi bi-check2-circle"></i>
                    </a>
                  @endif

                  @if(Route::has('servicios.edit'))
                    <a href="{{ route('servicios.edit', $s->id) }}" class="btn-ghost" data-tooltip="Modificar" aria-label="Modificar">
                      <i class="bi bi-pencil"></i>
                    </a>
                  @endif

                  @if(Route::has('servicios.destroy'))
                    <form action="{{ route('servicios.destroy', $s->id) }}" method="POST" onsubmit="return confirm('¿Autoriza la eliminación definitiva de este registro corporativo?');" style="margin: 0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-ghost delete" data-tooltip="Eliminar registro" aria-label="Eliminar registro">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 64px 20px;">
                <div style="max-width: 320px; margin: 0 auto; color: var(--muted);">
                  <i class="bi bi-inboxes" style="font-size: 3rem; color: var(--line); margin-bottom: 16px; display: block;"></i>
                  <h5 style="color: var(--title); font-weight: 700; font-size: 1.25rem;">Base de datos vacía</h5>
                  <p style="font-size: 0.95rem; margin-top: 8px;">Aún no se ha registrado ningún activo en esta sección del sistema.</p>
                </div>
              </td>
            </tr>
          @endforelse

          @if($serviciosCollection->count() > 0)
            <tr x-show="sinCoincidencias()" style="display:none;">
              <td colspan="6" style="text-align: center; padding: 64px 20px;">
                <div style="max-width: 320px; margin: 0 auto; color: var(--muted);">
                  <i class="bi bi-search" style="font-size: 2.5rem; color: var(--line); margin-bottom: 16px; display: block;"></i>
                  <h5 style="color: var(--title); font-weight: 700; font-size: 1.25rem;">Sin coincidencias</h5>
                  <p style="font-size: 0.95rem; margin-top: 8px;">Intenta ajustando los filtros de búsqueda o el selector de estado.</p>
                </div>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

  @if($servicios instanceof \Illuminate\Pagination\AbstractPaginator)
    <div>
      {{ $servicios->links() }}
    </div>
  @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('dashboard', {
    search: '',
    status: '',
    tab: '{{ request('tab', 'internos') === 'externos' ? 'externos' : 'internos' }}'
  });

  Alpine.data('CorporateDashboard', function(initialTab = 'internos') {
    return {
      init() {
        this.$store.dashboard.tab = initialTab === 'externos' ? 'externos' : 'internos';
      },
      setTab(tab) {
        this.$store.dashboard.tab = tab;
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
      },
      filtrar(row) {
        const q = (this.$store.dashboard.search || '').toLowerCase().trim();
        const status = (this.$store.dashboard.status || '').toLowerCase().trim();
        const tab = (this.$store.dashboard.tab || '').toLowerCase().trim();

        const statusOk = status ? String(row.estado || '').toLowerCase().includes(status) : true;
        const tabOk = tab ? String(row.ambito || '').toLowerCase() === tab : true;

        if (!q) return statusOk && tabOk;

        const dataStr = [
          row.tipo, row.subtipo, row.marca, row.modelo,
          row.serie, row.user, row.doctor, row.ambito, row.estado
        ].join(' ').toLowerCase();

        return statusOk && tabOk && dataStr.includes(q);
      },
      sinCoincidencias() {
        const rows = document.querySelectorAll('tr.trow');
        let visibles = 0;

        rows.forEach(row => {
          const style = window.getComputedStyle(row);
          if (style.display !== 'none') visibles++;
        });

        return visibles === 0;
      }
    }
  });
});
</script>
@endsection