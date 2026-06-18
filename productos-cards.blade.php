{{-- resources/views/productos-cards.blade.php --}}
@extends('layouts.app')
@section('title', 'Productos')
@section('titulo', 'Productos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* ── Sidebar selects ── */
.sidebar-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #888;
    margin-bottom: 7px;
}

.select-filter {
    width: 100%;
    height: 42px;
    border: 1px solid #e5eaf0;
    border-radius: 12px;
    padding: 0 14px;
    font-size: 13px;
    font-weight: 600;
    color: #111;
    background: #fff;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    cursor: pointer;
    transition: border-color .15s ease, box-shadow .15s ease;
}

.select-filter:focus {
    border-color: #000;
    box-shadow: 0 0 0 3px rgba(0,0,0,.06);
}

.sidebar-block {
    margin-bottom: 18px;
}

/* Mobile: selects en fila 2x2 */
@media (max-width: 768px) {
    .catalog-sidebar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        border-bottom: 1px solid #eee;
        padding-bottom: 16px;
    }

    .sidebar-block {
        margin-bottom: 0;
    }

    .select-filter {
        height: 40px;
        font-size: 12px;
        border-radius: 10px;
    }
}
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
      background: #ffffff;
      font-family: "Open Sans", sans-serif;
      color: #333;
  }

  .catalog-wrapper {
    width: 100%;
    max-width: none;
    margin: 30px 0;
    padding: 0 20px;
}

  /* CENTRADO DE HERRAMIENTAS */
  .catalog-header {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-bottom: 20px;
      border-bottom: 1px solid #eaeaea;
      margin-bottom: 30px;
      width: 100%;
  }

  .catalog-tools {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
  }

  .clean-search-wrap {
      position: relative;
  }

  .clean-search {
      border: none;
      border-bottom: 1px solid #ccc;
      padding: 6px 10px 6px 25px;
      outline: none;
      font-size: 14px;
      width: 220px;
      transition: all 0.3s;
      background: #fff;
  }

  .clean-search:focus {
      border-bottom-color: #000;
      width: 250px;
  }

  .clean-search-wrap svg {
      position: absolute;
      left: 0;
      top: 8px;
      width: 16px;
      color: #999;
  }

  /* ESTILO PARA BOTONES (EXPORTAR Y BOTONES SOLO ICONO) */
  .btn-export,
  .btn-nav-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #eaeaea;
      background: #fff;
      color: #000;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
  }

  .btn-export {
      gap: 8px;
      padding: 8px 14px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: 600;
  }

  /* DISEÑO CUADRADO PARA SOLO ICONOS */
  .btn-nav-icon {
      width: 36px;
      height: 36px;
      border-radius: 4px;
  }

  .btn-export:hover,
  .btn-nav-icon:hover {
      border-color: #000;
      color: #000;
      background: #fafafa;
  }

  .catalog-body {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    width: 100%;
}

  .catalog-sidebar{
    width:220px;
    min-width:220px;
    flex-shrink:0;
}

  .catalog-main{
    flex:1;
    width:100%;
    min-width:0;
}

  .sidebar-block {
      margin-bottom: 30px;
  }

  .sidebar-block h3 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 15px;
      color: #000;
  }

  .sub-filter {
      font-size: 14px;
      font-weight: 700;
      margin-top: 20px;
      margin-bottom: 12px;
      color: #000;
  }

  .filter-list {
      list-style: none;
      padding: 0;
      margin: 0;
      max-height: 300px;
      overflow-y: auto;
      padding-right: 10px;
  }

  .filter-list::-webkit-scrollbar {
      width: 4px;
  }

  .filter-list::-webkit-scrollbar-thumb {
      background: #ddd;
      border-radius: 4px;
  }

  .filter-list li {
      margin-bottom: 12px;
  }

  .filter-list label {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 13px;
      color: #555;
      cursor: pointer;
      text-transform: capitalize;
      transition: color 0.2s;
      line-height: 1.4;
  }

  .filter-list label:hover {
      color: #000;
  }

  .filter-list input[type="radio"] {
      accent-color: #000;
      width: 14px;
      height: 14px;
      margin-top: 3px;
      cursor: pointer;
      flex-shrink: 0;
  }

  .catalog-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,220px));
    gap:20px;
    justify-content:flex-start;
    align-items:start;
}

  .product-card-scope {
      text-decoration: none;
      color: inherit;
      display: block;
      position: relative;
      min-width: 0;
      width:220px
  }

  .product-image-wrap{
    width:220px;
    height:220px;
    background:#f0f1f3;
    border-radius:12px;
    overflow:hidden;
    display:flex;
    justify-content:center;
    align-items:center;
}

  .product-image-wrap img{
    max-width:100%;
    max-height:100%;
    width:auto;
    height:auto;
    object-fit:contain;
    padding:15px;
}

  .product-card-scope:hover .product-image-wrap img {
      transform: scale(1.05);
  }

  .product-info {
      text-align: center;
      padding: 0 4px;
  }

  .product-title {
      font-size: 13px;
      color: #666;
      margin-bottom: 6px;
      font-weight: 400;
      text-transform: capitalize;
      line-height: 1.3;
      min-height: 38px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
  }

  .product-price {
      font-size: 18px;
      font-weight: 700;
      color: #000;
      margin-bottom: 4px;
  }

  .product-details {
      font-size: 12px;
      color: #000;
      font-weight: 700;
      margin-top: 5px;
      text-transform: uppercase;
  }

  .admin-actions {
      position: absolute;
      bottom: 12px;
      right: 12px;
      display: flex;
      gap: 8px;
      opacity: 0;
      transition: opacity 0.2s;
  }

  .product-card-scope:hover .admin-actions {
      opacity: 1;
  }

  .btn-icon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      color: #fff;
      text-decoration: none;
      border: none;
      cursor: pointer;
      background: rgba(0,0,0,0.6);
      transition: background 0.2s, transform 0.2s;
  }

  .btn-icon:hover {
      background: #000;
      transform: scale(1.1);
      color: #fff;
  }

  .btn-icon.delete {
      background: rgba(239, 83, 80, 0.9);
  }

  .btn-icon.delete:hover {
      background: #e53935;
  }

  .btn-icon svg {
      width: 16px;
      height: 16px;
  }

  .package-title-row {
      grid-column: 1 / -1;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #eaeaea;
  }

  .package-title-row h3 {
      font-size: 18px;
      font-weight: 700;
      color: #000;
      margin: 0;
  }

  .fab-add {
      position: fixed;
      right: 22px;
      bottom: 22px;
      z-index: 99;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: #000;
      color: #fff;
      display: grid;
      place-items: center;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      text-decoration: none;
      cursor: pointer;
      transition: transform 0.2s;
  }

  .fab-add:hover {
      transform: translateY(-3px);
      color: #fff;
  }

  .fab-add svg {
      width: 24px;
      height: 24px;
  }

  .modal-pro .modal-content {
      border: none;
      border-radius: 12px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }

  .modal-pro .modal-header {
      background: #fafafa;
      border-bottom: 1px solid #eee;
      padding: 20px 24px;
  }

  .modal-pro .modal-title {
      font-weight: 700;
      color: #000;
      font-size: 1.2rem;
  }

  .modal-pro .modal-body {
      padding: 24px;
  }

  .modal-pro .modal-footer {
      background: #fafafa;
      border-top: 1px solid #eee;
      padding: 16px 24px;
      gap: 10px;
  }

  .field-label {
      font-size: 12px;
      font-weight: 700;
      color: #666;
      margin-bottom: 8px;
      text-transform: uppercase;
  }

  .select-pro,
  .input-pro {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 10px 14px;
      width: 100%;
      outline: none;
  }

  .select-pro:focus,
  .input-pro:focus {
      border-color: #000;
  }

  .btn-pro {
      border: 1px solid #ddd;
      background: #fff;
      color: #000;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.2s;
  }

  .btn-pro.primary {
      background: #000;
      color: #fff;
      border-color: #000;
  }

  .btn-pro.success {
      background: #10b981;
      color: #fff;
      border-color: #10b981;
  }

  .btn-pro:hover {
      opacity: 0.8;
      color: inherit;
  }

  .btn-pro.primary:hover,
  .btn-pro.success:hover {
      color: #fff;
  }

  .check-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
      font-weight: 600;
      color: #555;
  }

  .empty-catalog-message {
      grid-column: 1 / -1;
      text-align: center;
      color: #888;
      padding: 45px 15px;
      border: 1px dashed #ddd;
      border-radius: 10px;
      background: #fafafa;
  }

  @media (max-width: 768px) {
    .catalog-body {
    flex-direction: row;
    gap: 16px;
    align-items: flex-start;
}

.catalog-sidebar {
    width: 180px;
    min-width: 180px;
    flex-shrink: 0;
    border-right: 1px solid #eaeaea;
    border-bottom: none;
    padding-right: 12px;
    padding-bottom: 0;
    position: sticky;
    top: 10px;
}

.sidebar-label {
    font-size: 10px;
}

.select-filter {
    height: 36px;
    font-size: 12px;
    border-radius: 8px;
    padding: 0 28px 0 10px;
    background-position: right 8px center;
}

.sidebar-block {
    margin-bottom: 14px;
}

      .catalog-main {
          width: 100%;
      }

      .sidebar-block {
          margin-bottom: 16px;
      }

      .sidebar-block h3 {
          font-size: 15px;
          margin-bottom: 10px;
      }

      .sub-filter {
          font-size: 12px;
          margin-top: 12px;
          margin-bottom: 8px;
          color: #555;
          text-transform: uppercase;
          letter-spacing: .3px;
      }

      .filter-list {
          max-height: none;
          display: flex;
          gap: 8px;
          overflow-x: auto;
          overflow-y: hidden;
          padding: 0 0 8px;
          margin: 0;
          scrollbar-width: none;
      }

      .filter-list::-webkit-scrollbar {
          display: none;
      }

      .filter-list li {
          margin-bottom: 0;
          flex: 0 0 auto;
      }

      .filter-list label {
          min-height: 36px;
          display: inline-flex;
          align-items: center;
          gap: 7px;
          border: 1px solid #e5e5e5;
          border-radius: 999px;
          padding: 8px 12px;
          background: #fafafa;
          color: #555;
          font-size: 12px;
          line-height: 1;
          white-space: nowrap;
          transition: .2s ease;
      }

      .filter-list label:has(input:checked) {
          background: #111;
          color: #fff;
          border-color: #111;
          }

      .filter-list input[type="radio"] {
          width: 12px;
          height: 12px;
          margin: 0;
          accent-color: #111;
      }

      .catalog-grid {
          display: grid;
          grid-template-columns: repeat(2, minmax(0, 1fr));
          gap: 20px 12px;
          width: 100%;
      }

      .product-card-scope {
          min-width: 0;
      }

      .product-image-wrap {
          aspect-ratio: 1 / 1.18;
          border-radius: 14px;
          margin-bottom: 9px;
          background: #f5f6f7;
          box-shadow: 0 6px 18px rgba(0,0,0,.04);
      }

      .product-image-wrap img {
          padding: 12px;
          object-fit: contain;
      }

      .product-card-scope:hover .product-image-wrap img {
          transform: none;
      }

      .product-info {
          padding: 0 3px;
      }

      .product-title {
          font-size: 12px;
          line-height: 1.25;
          margin-bottom: 5px;
          color: #555;
          min-height: 30px;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
      }

      .product-price {
          font-size: 17px;
          line-height: 1.1;
          margin-bottom: 3px;
      }

      .product-details {
          font-size: 10px;
          letter-spacing: .3px;
          color: #666;
          margin-top: 4px;
      }

      .admin-actions {
          opacity: 1;
          bottom: 8px;
          right: 8px;
          gap: 6px;
      }

      .btn-icon {
          width: 31px;
          height: 31px;
          background: rgba(0,0,0,.72);
          backdrop-filter: blur(4px);
      }

      .btn-icon.delete {
          background: rgba(229,57,53,.88);
      }

      .btn-icon svg {
          width: 14px;
          height: 14px;
      }

      .package-title-row {
          margin-top: 10px;
          padding-top: 18px;
          }

      .package-title-row h3 {
          font-size: 15px;
          letter-spacing: .3px;
      }

      .empty-catalog-message {
          padding: 28px 14px;
          font-size: 13px;
          border-radius: 14px;
      }

      .fab-add {
          width: 52px;
          height: 52px;
          right: 16px;
          bottom: 16px;
      }

      .modal-pro .modal-dialog {
          margin: 12px;
      }

      .modal-pro .modal-content {
          border-radius: 16px;
      }

      .modal-pro .modal-footer {
          display: grid;
          grid-template-columns: 1fr 1fr;
      }

      .modal-pro .modal-footer .btn-pro {
          text-align: center;
          justify-content: center;
      }
  }

  @media (max-width: 360px) {
      .catalog-wrapper {
          padding: 0 10px;
      }

      .catalog-grid {
          gap: 18px 10px;
      }

      .product-image-wrap {
          border-radius: 12px;
      }

      .product-image-wrap img {
          padding: 10px;
      }

      .product-title {
          font-size: 11px;
      }

      .product-price {
          font-size: 15px;
      }

      .product-details {
          font-size: 9px;
      }
  }

  /* ── Sidebar Acordeón ── */
.sidebar-toggle,
.sub-toggle {
    width: 100%;
    background: none;
    border: none;
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    text-align: left;
}

.sidebar-toggle span {
    font-size: 18px;
    font-weight: 700;
    color: #000;
}

.sub-toggle span {
    font-size: 14px;
    font-weight: 700;
    color: #000;
}

.sidebar-toggle .toggle-icon,
.sub-toggle .toggle-icon {
    width: 18px;
    height: 18px;
    color: #000;
    transition: transform 0.25s ease;
    flex-shrink: 0;
}

.sidebar-toggle.open .toggle-icon,
.sub-toggle.open .toggle-icon {
    transform: rotate(180deg);
}

.sidebar-collapsible,
.sub-collapsible {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s ease, opacity 0.3s ease;
    opacity: 0;
}

.sidebar-collapsible.open,
.sub-collapsible.open {
    max-height: 600px;
    opacity: 1;
}

.sidebar-block {
    border-bottom: 1px solid #eaeaea;
    padding: 16px 0;
    margin-bottom: 0;
}

.sidebar-block:last-child {
    border-bottom: none;
}

.sub-accordion {
    margin-top: 12px;
}

.sub-accordion + .sub-accordion {
    border-top: 1px solid #f0f0f0;
    padding-top: 10px;
}

.sidebar-collapsible .filter-list {
    margin-top: 12px;
}

.sub-collapsible .filter-list {
    margin-top: 10px;
}
.pin-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.35);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:99999;
}

.pin-overlay.show{
    display:flex;
}

.pin-modal{
    width:520px;
    max-width:95%;
    background:#fff;
    border-radius:28px;
    padding:28px;
    box-shadow:0 20px 60px rgba(0,0,0,.15);
}

.pin-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
}

.pin-title{
    font-size:34px;
    font-weight:800;
    margin:0;
}

.pin-sub{
    color:#777;
    margin-top:4px;
}

.pin-close{
    border:none;
    background:#eef2f7;
    width:38px;
    height:38px;
    border-radius:50%;
    cursor:pointer;
}

.pin-inputs{
    display:flex;
    gap:12px;
    justify-content:center;
    margin-top:30px;
}

.pin-box{
    width:58px;
    height:68px;
    border:1px solid #d7dce4;
    border-radius:14px;
    text-align:center;
    font-size:28px;
    font-weight:700;
}

.pin-box:focus{
    outline:none;
    border-color:#4f46e5;
}

.pin-help{
    text-align:center;
    color:#777;
    margin-top:18px;
}
</style>

@php
  use Illuminate\Support\Str;

  $productosCollection = collect($productos ?? []);

  $grouped = $productosCollection
      ->groupBy(function($item) {
          return $item->tipo_equipo ? strtoupper(trim($item->tipo_equipo)) : 'OTROS';
      })
      ->sortKeys();

  $tipos = $productosCollection
      ->pluck('tipo_equipo')
      ->filter()
      ->map(fn($v) => trim((string) $v))
      ->unique()
      ->sort()
      ->values();

  $subtipos = $productosCollection
      ->pluck('subtipo_equipo')
      ->filter()
      ->map(fn($v) => trim((string) $v))
      ->unique()
      ->sort()
      ->values();

  $marcas = $productosCollection
      ->pluck('marca')
      ->filter()
      ->map(fn($v) => trim((string) $v))
      ->unique()
      ->sort()
      ->values();
@endphp

<div class="catalog-wrapper">

    <div class="catalog-header">
        <div class="catalog-tools">
            
            {{-- BOTÓN REGRESAR (Solo Icono) --}}
            <button type="button" class="btn-nav-icon" onclick="history.back();" title="Regresar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </button>

            {{-- BOTÓN HOME (Solo Icono) --}}
            <a href="{{ url('/home') }}" class="btn-nav-icon" title="Inicio">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </a>

            <div class="clean-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="7"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input id="q" type="text" class="clean-search" placeholder="Buscar modelo, tipo..." autocomplete="off">
            </div>

            <button type="button" class="btn-export" data-bs-toggle="modal" data-bs-target="#modalExport">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                  <path d="M12 3v12"></path>
                  <path d="M8 11l4 4 4-4"></path>
                  <path d="M4 21h16"></path>
                </svg>
                Exportar
            </button>
        </div>
    </div>

    <div class="catalog-body">

        <aside class="catalog-sidebar">
            <div class="sidebar-block">
                <button type="button" class="sidebar-toggle open">
                    <span>Filtros</span>
                    <svg class="toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <div class="sidebar-collapsible open">
                    <div class="sub-accordion">
                        <label class="sidebar-label">Categoría</label>
                        <select id="selectTipo" class="select-filter">
                            <option value="">Todas las categorías</option>
                            @foreach($tipos as $tipoOpt)
                                <option value="{{ $tipoOpt }}">{{ strtolower($tipoOpt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sub-accordion">
                        <label class="sidebar-label">Marca</label>
                        <select id="selectMarca" class="select-filter">
                            <option value="">Todas las marcas</option>
                            @foreach($marcas as $marcaOpt)
                                <option value="{{ $marcaOpt }}">{{ strtolower($marcaOpt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sub-accordion">
                        <label class="sidebar-label">Subtipo</label>
                        <select id="selectSubtipo" class="select-filter">
                            <option value="">Todos los subtipos</option>
                            @foreach($subtipos as $subOpt)
                                <option value="{{ $subOpt }}">{{ strtolower($subOpt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sub-accordion">
                        <label class="sidebar-label">Disponibilidad</label>
                        <select id="selectStock" class="select-filter">
                            <option value="all">Todos</option>
                            <option value="with_stock">En stock</option>
                            <option value="without_stock">Sin stock</option>
                        </select>
                    </div>
                </div>
            </div>
        </aside>

        <main class="catalog-main">
            <div class="catalog-grid" id="mainGrid">

                @if(!empty($grouped) && $grouped->count() > 0)
                    @foreach($grouped as $tipo => $items)
                        @foreach($items as $p)
                            @php
                                $nombre   = $p->nombre ?? $p->tipo_equipo ?? 'PRODUCTO';
                                $tipoStr  = $p->tipo_equipo ?? $nombre ?? '';
                                $subtipo  = $p->subtipo_equipo ?? '';
                                $marca    = $p->marca ?? '';
                                $modelo   = $p->modelo ?? '';
                                $precioV  = (float)($p->precio ?? 0);
                                $precio   = number_format($precioV, 2);
                                $stock    = (int)($p->stock ?? 0);

                                $imgRaw   = $p->imagen ?? $p->imagen_url ?? $p->foto_url ?? null;
                                $img      = $imgRaw
                                            ? (Str::startsWith($imgRaw, ['http://','https://']) ? $imgRaw : asset('storage/'.$imgRaw))
                                            : 'https://via.placeholder.com/800x800.png?text=NO+IMG';

                                $searchString = $nombre.' '.$tipoStr.' '.$subtipo.' '.$marca.' '.$modelo.' '.$precioV.' '.$stock.' producto equipo';
                            @endphp

                            <div class="product-card-scope catalog-item"
                                 data-kind="producto"
                                 data-search="{{ e($searchString) }}"
                                 data-tipo="{{ e($tipoStr) }}"
                                 data-subtipo="{{ e($subtipo) }}"
                                 data-marca="{{ e($marca) }}"
                                 data-stock="{{ $stock }}">

                                <div class="product-image-wrap">
                                    <img src="{{ $img }}" alt="{{ $nombre }}">

                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="admin-actions">
                                            <button type="button" class="btn-icon btn-pin-action" data-tipo="editar"  data-url="{{ route('productos.edit', $p->id) }}"  title="Editar" onclick="event.stopPropagation();">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                                </svg>
                                            </button>

                                            <form id="delete-producto-{{ $p->id }}" class="delete-form" action="{{ route('productos.destroy', $p->id) }}"
                                                @csrf
                                                @method('DELETE')

                                                <button type="button" class="btn-icon delete btn-pin-action"  data-tipo="eliminar"  data-form="delete-producto-{{ $p->id }}" title="Eliminar">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        <path d="M10 11v6M14 11v6"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="product-info">
                                    <div class="product-title">{{ $tipoStr ?: $nombre }} {{ $modelo }}</div>
                                    <div class="product-price">${{ $precio }}</div>
                                    <div class="product-details">{{ $marca ?: 'Genérico' }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif

                @if(!empty($paquetes) && count($paquetes))
                    <div class="package-title-row package-section-title">
                        <h3>PAQUETES</h3>
                    </div>

                    @foreach($paquetes as $pkg)
                        @php
                            $pkgName   = $pkg->nombre ?? 'PAQUETE';
                            $pkgImgRaw = $pkg->imagen ?? optional(optional($pkg->productos)->first())->imagen ?? null;
                            $pkgImg    = $pkgImgRaw
                                ? (Str::startsWith($pkgImgRaw, ['http://','https://']) ? $pkgImgRaw : asset('storage/'.$pkgImgRaw))
                                : 'https://via.placeholder.com/800x800.png?text=PAQUETE';

                            $namesIn = '';
                            $pkgTipos = '';
                            $pkgSubtipos = '';
                            $pkgMarcas = '';
                            $pkgStockStatus = 'without_stock';

                            if (isset($pkg->productos)) {
                                $namesIn = collect($pkg->productos)->map(function($pp) {
                                    return ($pp->nombre ?? $pp->tipo_equipo ?? '') . ' ' .
                                           ($pp->subtipo_equipo ?? '') . ' ' .
                                           ($pp->marca ?? '') . ' ' .
                                           ($pp->modelo ?? '');
                                })->join(' ');

                                $pkgTipos = collect($pkg->productos)
                                    ->pluck('tipo_equipo')
                                    ->filter()
                                    ->map(fn($v) => trim($v))
                                    ->unique()
                                    ->join(' | ');

                                $pkgSubtipos = collect($pkg->productos)
                                    ->pluck('subtipo_equipo')
                                    ->filter()
                                    ->map(fn($v) => trim($v))
                                    ->unique()
                                    ->join(' | ');

                                $pkgMarcas = collect($pkg->productos)
                                    ->pluck('marca')
                                    ->filter()
                                    ->map(fn($v) => trim($v))
                                    ->unique()
                                    ->join(' | ');

                                $pkgStockStatus = collect($pkg->productos)->contains(fn($pp) => (int)($pp->stock ?? 0) > 0)
                                    ? 'with_stock'
                                    : 'without_stock';
                            }

                            $pkgTotal = isset($pkg->productos)
                                ? $pkg->productos->sum(function($pp) {
                                    return (float)($pp->precio ?? 0) * max(1, (int)($pp->pivot->cantidad ?? 1));
                                  })
                                : 0;

                            $pkgTotalFmt = number_format($pkgTotal, 2);
                            $searchString = $pkgName.' '.$namesIn.' '.$pkgTotal.' paquete combo';
                        @endphp

                        <div class="product-card-scope catalog-item pkg-card"
                             data-kind="paquete"
                             data-search="{{ e($searchString) }}"
                             data-tipo="{{ e($pkgTipos) }}"
                             data-subtipo="{{ e($pkgSubtipos) }}"
                             data-marca="{{ e($pkgMarcas) }}"
                             data-stock="{{ $pkgStockStatus }}">

                            <div class="product-image-wrap">
                                <img src="{{ $pkgImg }}" alt="{{ $pkgName }}">

                                @if(auth()->check() && auth()->user()->hasRole('admin'))
                                    <div class="admin-actions">
                                        <button type="button" class="btn-icon btn-pin-action" data-tipo="editar" data-url="{{ route('paquetes.edit', $pkg->id) }}"
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                            </svg>
                                        </button>
                                        <form id="delete-paquete-{{ $pkg->id }}"class="delete-form"action="{{ route('paquetes.destroy', $pkg->id) }}"
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
        class="btn-icon delete btn-pin-action"
        data-tipo="eliminar" data-form="delete-paquete-{{ $pkg->id }}" title="Eliminar">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6M14 11v6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="product-info">
                                <div class="product-title">{{ $pkgName }}</div>
                                <div class="product-price">${{ $pkgTotalFmt }}</div>
                                <div class="product-details">COMBO</div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if((empty($productos) || !count($productos)) && (empty($paquetes) || !count($paquetes)))
                    <p class="empty-catalog-message">No hay productos registrados.</p>
                @endif

            </div>
        </main>
    </div>
</div>

@if(auth()->check() && auth()->user()->hasRole('admin'))
  <a href="{{ route('productos.create') }}" class="fab-add" title="AGREGAR">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M5 12h14"/>
    </svg>
  </a>
@endif

<div class="modal fade modal-pro" id="modalExport" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="modal-title">Exportar Catálogo</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="field-label">Qué exportar</div>
            <select id="exportScope" class="select-pro">
              <option value="all">Todo (Productos + Paquetes)</option>
              <option value="productos">Solo Productos</option>
              <option value="paquetes">Solo Paquetes</option>
            </select>
          </div>

          <div class="col-12 mt-3">
            <label class="check-wrap w-100">
              <input id="exportUseAi" type="checkbox">
              Usar IA para optimizar archivo WooCommerce
            </label>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-pro" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnPdf" class="btn-pro" href="#">PDF</a>
        <a id="btnXlsx" class="btn-pro primary" href="#">Excel</a>
        <a id="btnWooXlsx" class="btn-pro success" href="#">WooCommerce</a>
      </div>
    </div>
  </div>
</div>
<div id="pinOverlay" class="pin-overlay">

    <div class="pin-modal">

        <div class="pin-header">
            <div>
                <h3 class="pin-title">Autorización</h3>
                <div class="pin-sub">
                    PIN de 6 dígitos para continuar
                </div>
            </div>

            <button type="button"
                    id="pinClose"
                    class="pin-close">
                ✕
            </button>
        </div>

        <div class="pin-inputs">
            <input class="pin-box" maxlength="1">
            <input class="pin-box" maxlength="1">
            <input class="pin-box" maxlength="1">
            <input class="pin-box" maxlength="1">
            <input class="pin-box" maxlength="1">
            <input class="pin-box" maxlength="1">
        </div>

        <div class="pin-help">
            Puedes pegar el PIN completo.
        </div>

    </div>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){

    /* ── Utilidades de filtrado ── */
    const norm = s =>
        (s||'').toString().trim().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .replace(/\s+/g,' ').trim();

    const tokens = v => norm(v).split(' ').filter(Boolean);

    const cleanBackdrop = () => {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    };

    /* ── Referencias DOM ── */
    const qEl        = document.getElementById('q');
    const selTipo    = document.getElementById('selectTipo');
    const selMarca   = document.getElementById('selectMarca');
    const selSub     = document.getElementById('selectSubtipo');
    const selStock   = document.getElementById('selectStock');
    const grid       = document.getElementById('mainGrid');
    const allCards   = () => Array.from(document.querySelectorAll('.catalog-item'));

    /* ── Lógica de control para colapsar Acordeón ── */
    document.querySelectorAll('.sidebar-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.nextElementSibling;
            this.classList.toggle('open');
            if (container) {
                container.classList.toggle('open');
            }
        });
    });

    /* ── Lee el valor actual de cada select ── */
    const getVals = () => ({
        q:       norm(qEl.value),
        tipo:    norm(selTipo.value),
        marca:   norm(selMarca.value),
        subtipo: norm(selSub.value),
        stock:   selStock.value || 'all',
    });

    /* ── Evalúa si una tarjeta pasa un conjunto de filtros ── */
    const cardPasses = (card, {q, tipo, marca, subtipo, stock}) => {
        const search  = norm(card.dataset.search  || '');
        const dTipo   = norm(card.dataset.tipo     || '');
        const dSub    = norm(card.dataset.subtipo  || '');
        const dMarca  = norm(card.dataset.marca    || '');
        const dStock  = (card.dataset.stock || '').trim();
        const kind    = card.dataset.kind || 'producto';

        const toks = tokens(q);
        if (toks.length && !toks.every(t => search.includes(t))) return false;
        if (tipo    && !dTipo.includes(tipo))   return false;
        if (subtipo && !dSub.includes(subtipo)) return false;
        if (marca   && !dMarca.includes(marca)) return false;

        if (stock !== 'all') {
            if (kind === 'paquete') {
                if (dStock !== stock) return false;
            } else {
                const n = parseInt(dStock || '0', 10);
                if (stock === 'with_stock'    && n <= 0) return false;
                if (stock === 'without_stock' && n  > 0) return false;
            }
        }
        return true;
    };

    /* ── Reconstruye las opciones dinámicas del select en cascada ── */
    const rebuildSelect = (sel, field, allLabel) => {
        const currentVal = sel.value;
        const vals = new Set();

        const partial = {...getVals(), [field]: ''};
        if (field === 'stock') partial.stock = 'all';

        allCards().forEach(card => {
            if (!cardPasses(card, partial)) return;

            if (field === 'stock') {
                const kind   = card.dataset.kind || 'producto';
                const dStock = (card.dataset.stock || '').trim();
                const n      = parseInt(dStock || '0', 10);
                if (kind === 'paquete') {
                    vals.add(dStock);
                } else {
                    vals.add(n > 0 ? 'with_stock' : 'without_stock');
                }
                return;
            }

            const raw = card.dataset[field] || '';
            raw.split('|').forEach(v => { const t = v.trim(); if (t) vals.add(t); });
        });

        if (field === 'stock') {
            Array.from(sel.options).forEach(opt => {
                if (opt.value === 'all') return;
                opt.disabled = !vals.has(opt.value);
            });
            if (!vals.has(sel.value) && sel.value !== 'all') sel.value = 'all';
            return;
        }

        const sorted = [...vals].sort((a,b) => a.localeCompare(b));

        while (sel.options.length > 1) sel.remove(1);
        sorted.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v.toLowerCase();
            if (norm(v) === norm(currentVal)) opt.selected = true;
            sel.appendChild(opt);
        });

        if (currentVal && !sorted.some(v => norm(v) === norm(currentVal))) {
            sel.value = '';
        }
    };

    /* ── Aplica filtros + reconstruye selects ── */
    const update = () => {
        const vals = getVals();
        let visible = 0;

        allCards().forEach(card => {
            const show = cardPasses(card, vals);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const pkgsVisible = Array.from(document.querySelectorAll('.pkg-card'))
            .some(c => c.style.display !== 'none');
        document.querySelectorAll('.package-section-title')
            .forEach(el => el.style.display = pkgsVisible ? '' : 'none');

        const existing = document.getElementById('emptyCatalogMessage');
        if (existing) existing.remove();
        if (visible === 0) {
            const msg = document.createElement('div');
            msg.id = 'emptyCatalogMessage';
            msg.className = 'empty-catalog-message';
            msg.textContent = 'No se encontraron coincidencias.';
            grid.appendChild(msg);
        }

        rebuildSelect(selTipo,  'tipo',    'Todas las categorías');
        rebuildSelect(selMarca, 'marca',   'Todas las marcas');
        rebuildSelect(selSub,   'subtipo', 'Todos los subtipos');
        rebuildSelect(selStock, 'stock',   'Todos');

        syncExportButtons();
    };

    /* ── URL de Exportación ── */
    const buildExportUrl = (base, forceProductos) => {
        const p = new URLSearchParams();
        const q = (qEl.value || '').trim();
        if (q) p.set('q', q);
        p.set('scope', forceProductos ? 'productos' : (document.getElementById('exportScope').value || 'all'));
        const t  = selTipo.value  || '';
        const s  = selSub.value   || '';
        const m  = selMarca.value || '';
        const st = selStock.value || '';
        if (t)               p.set('tipo',    t);
        if (s)               p.set('subtipo', s);
        if (m)               p.set('marca',   m);
        if (st && st!=='all') p.set('stock',  st);
        p.set('ai', document.getElementById('exportUseAi').checked ? '1' : '0');
        const qs = p.toString();
        return qs ? base + (base.includes('?') ? '&' : '?') + qs : base;
    };

    const syncExportButtons = () => {
        document.getElementById('btnPdf').href     = buildExportUrl(@json(route('catalogo.export.pdf')));
        document.getElementById('btnXlsx').href    = buildExportUrl(@json(route('catalogo.export.xlsx')));
        document.getElementById('btnWooXlsx').href = buildExportUrl(@json(route('productos.export.woocommerce')), true);
    };

    /* ── Eventos ── */
    qEl.addEventListener('input',      update);
    selTipo.addEventListener('change',  update);
    selMarca.addEventListener('change', update);
    selSub.addEventListener('change',   update);
    selStock.addEventListener('change', update);

    document.getElementById('modalExport')
        .addEventListener('show.bs.modal', syncExportButtons);
    document.getElementById('exportScope')
        .addEventListener('change', syncExportButtons);
    document.getElementById('exportUseAi')
        .addEventListener('change', syncExportButtons);

    /* ── Confirmación de Eliminación ── */
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            Swal.fire({
                title: '¿ELIMINAR?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53935',
                cancelButtonColor: '#000',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    /* ── Descarga PDF ── */
    document.getElementById('btnPdf').addEventListener('click', function(e){
        e.preventDefault();
        const url = this.href;
        if (!url || url === '#') return;
        bootstrap.Modal.getInstance(document.getElementById('modalExport'))?.hide();
        Swal.fire({ title: 'Generando PDF…', text: 'Esto puede tardar unos segundos.',
            allowOutsideClick: false, showConfirmButton: false,
            didOpen: () => Swal.showLoading() });
        fetch(url)
            .then(r => { if (!r.ok) throw new Error(); return r.blob(); })
            .then(blob => {
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'catalogo.pdf';
                a.click();
                URL.revokeObjectURL(a.href);
                Swal.close(); cleanBackdrop();
            })
            .catch(() => {
                Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar el PDF.' });
                cleanBackdrop();
            });
    });

    /* ── Descarga Excel / WooCommerce ── */
    ['btnXlsx','btnWooXlsx'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e){
            e.preventDefault();
            const url = this.href;
            if (!url || url === '#') return;
            bootstrap.Modal.getInstance(document.getElementById('modalExport'))?.hide();
            Swal.fire({ title: 'Generando archivo…', text: 'La descarga iniciará en un momento.',
                allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => Swal.showLoading() });
            fetch(url)
                .then(r => { if (!r.ok) throw new Error(); return r.blob(); })
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = url.includes('woocommerce') ? 'woocommerce.xlsx' : 'catalogo.xlsx';
                    a.click();
                    URL.revokeObjectURL(a.href);
                    Swal.close(); cleanBackdrop();
                })
                .catch(() => {
                    Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar el archivo.' });
                    cleanBackdrop();
                });
        });
    });
     
    const PIN_CORRECTO = '{{ env("APROBACION_PIN") }}';

const pinOverlay = document.getElementById('pinOverlay');
const pinClose = document.getElementById('pinClose');
const pinBoxes = [...document.querySelectorAll('.pin-box')];

let pinCallback = null;

function resetPin(){
    pinBoxes.forEach(i => i.value = '');
}

function abrirPin(callback){
    pinCallback = callback;
    resetPin();
    pinOverlay.classList.add('show');

    setTimeout(() => {
        pinBoxes[0].focus();
    }, 100);
}

function cerrarPin(){
    pinOverlay.classList.remove('show');
}

function verificarPin(){

    const pin = pinBoxes.map(x => x.value).join('');

    if(pin.length !== 6) return;

    if(pin !== String(PIN_CORRECTO)){

    pinBoxes.forEach(box => {
        box.style.borderColor = '#ef4444';
    });

    setTimeout(() => {
        pinBoxes.forEach(box => {
            box.style.borderColor = '#d7dce4';
        });
    }, 1200);

    resetPin();
    pinBoxes[0].focus();

    return;
}

    cerrarPin();

    if(typeof pinCallback === 'function'){
        pinCallback();
    }
}

pinClose.addEventListener('click', cerrarPin);

pinBoxes.forEach((box,index)=>{

    box.addEventListener('input',()=>{

        box.value = box.value.replace(/\D/g,'');

        if(box.value && index < pinBoxes.length-1){
            pinBoxes[index+1].focus();
        }

        verificarPin();
    });

    box.addEventListener('keydown',e=>{

        if(e.key === 'Backspace' &&
           !box.value &&
           index > 0){

            pinBoxes[index-1].focus();
        }
    });

    box.addEventListener('paste',e=>{

        e.preventDefault();

        const data = (e.clipboardData || window.clipboardData)
            .getData('text')
            .replace(/\D/g,'')
            .substring(0,6);

        data.split('').forEach((v,i)=>{
            if(pinBoxes[i]){
                pinBoxes[i].value = v;
            }
        });

        verificarPin();
    });

});

document.querySelectorAll('.btn-pin-action').forEach(btn=>{

    btn.addEventListener('click',function(){

        const tipo = this.dataset.tipo;

        abrirPin(()=>{

            if(tipo === 'editar'){
                window.location.href = this.dataset.url;
                return;
            }

            if(tipo === 'eliminar'){

                const formId = this.dataset.form;

                Swal.fire({
                    title:'¿ELIMINAR?',
                    text:'Esta acción no se puede deshacer.',
                    icon:'warning',
                    showCancelButton:true,
                    confirmButtonText:'Sí, eliminar',
                    cancelButtonText:'Cancelar'
                }).then(r=>{

                    if(r.isConfirmed){
                        document.getElementById(formId).submit();
                    }

                });
            }

        });

    });

});

    /* ── Inicialización Inicial ── */
    update();

})();
</script>

@endsection