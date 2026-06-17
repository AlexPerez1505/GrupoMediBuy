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
    display: block;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    height: 42px;
    border: 1px solid #e5eaf0;
    border-radius: 12px;
    padding: 0 36px 0 14px;
    font-size: 13px;
    font-weight: 600;
    color: #111;
    background: #fff;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: border-color .15s ease, box-shadow .15s ease;
}

.select-filter:focus {
    border-color: #000;
    box-shadow: 0 0 0 3px rgba(0,0,0,.06);
}

.sidebar-block {
    margin-bottom: 18px;
    width: 100%;
    overflow: hidden;
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
      max-width: 1300px;
      width: 100%;
      margin: 30px auto;
      padding: 0 20px;
      box-sizing: border-box;
  }

  .catalog-header {
      display: flex;
      align-items: flex-end;
      padding-bottom: 20px;
      border-bottom: 1px solid #eaeaea;
      margin-bottom: 30px;
      gap: 20px;
      flex-wrap: wrap;
      width: 100%;
  }

  .top-categories {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      white-space: nowrap;
      padding-bottom: 10px;
      flex: 1;
      min-width: 300px;
  }

  .top-categories::-webkit-scrollbar {
      height: 4px;
  }

  .top-categories::-webkit-scrollbar-thumb {
      background: #ddd;
      border-radius: 4px;
  }

  .top-categories a {
      text-decoration: none;
      color: #888;
      font-size: 14px;
      font-weight: 600;
      text-transform: capitalize;
      transition: color 0.2s;
  }

  .top-categories a:hover,
  .top-categories a.active {
      color: #000;
  }

  .catalog-tools {
      display: flex;
      gap: 15px;
      align-items: center;
      flex-shrink: 0;
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

  .btn-export {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid #eaeaea;
      background: #fff;
      color: #000;
      padding: 8px 14px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
  }

  .btn-export:hover {
      border-color: #000;
      color: #000;
  }

  .catalog-body {
      display: flex;
      flex-direction: row;
      gap: 40px;
      align-items: flex-start;
      width: 100%;
  }

  .catalog-sidebar {
      width: 250px;
      min-width: 250px;
      flex-shrink: 0;
      border-right: 1px solid #eaeaea;
      padding-right: 20px;
  }

  .catalog-main {
      flex-grow: 1;
      min-width: 0;
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

  .catalog-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 30px 20px;
      align-content: start;
  }

  .product-card-scope {
      text-decoration: none;
      color: inherit;
      display: block;
      position: relative;
      min-width: 0;
  }

  .product-image-wrap {
      background: #f0f1f3;    
      aspect-ratio: 4 / 5;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      margin-bottom: 15px;
      position: relative;
      border-radius: 8px;
  }

  .product-image-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 32px;
    transition: transform 0.4s ease;
}

  .product-card-scope:hover .product-image-wrap img {
      transform: scale(1.05);
  }

  .product-info {
      text-align: center;
      padding: 0 4px;
  }

  .product-title {
      font-size: 14px;
      color: #666;
      margin-bottom: 6px;
      font-weight: 400;
      text-transform: capitalize;
      line-height: 1.3;
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
</style>

@php
  use Illuminate\Support\Str;
  $productosCollection = collect($productos ?? []);
  $grouped = $productosCollection
      ->groupBy(function($item) {
          return $item->tipo_equipo ? strtoupper(trim($item->tipo_equipo)) : 'OTROS';
      })
      ->sortKeys();
@endphp

<div class="catalog-wrapper">

    <div class="catalog-header">

        <div class="catalog-tools">
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

    {{-- CATEGORÍA (tipo_equipo principal: Endoscopio, Laparoscopio…) --}}
    <div class="sidebar-block">
        <label class="sidebar-label">Categoría</label>
        <select id="selectCategoria" class="select-filter">
            <option value="">Todas las categorías</option>
        </select>
    </div>

    {{-- SUBTIPO (subtipo_equipo: depende de categoría seleccionada) --}}
    <div class="sidebar-block">
        <label class="sidebar-label">Subtipo</label>
        <select id="selectSubtipo" class="select-filter" disabled>
            <option value="">Todos los subtipos</option>
        </select>
    </div>

    {{-- MARCA (depende de categoría + subtipo seleccionados) --}}
    <div class="sidebar-block">
        <label class="sidebar-label">Marca</label>
        <select id="selectMarca" class="select-filter" disabled>
            <option value="">Todas las marcas</option>
        </select>
    </div>

    {{-- DISPONIBILIDAD — sin cambios --}}
    <div class="sidebar-block">
        <label class="sidebar-label">Disponibilidad</label>
        <select id="selectStock" class="select-filter">
            <option value="all">Todos</option>
            <option value="with_stock">En stock</option>
            <option value="without_stock">Sin stock</option>
        </select>
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
                            data-tipo="{{ e($tipoStr ?: 'OTROS') }}" {{-- <-- Si viene vacío, le inyectamos 'OTROS' --}}
                            data-subtipo="{{ e($subtipo) }}" 
                            data-marca="{{ e($marca) }}" 
                            data-stock="{{ $stock }}">

                                <div class="product-image-wrap">
                                    <img src="{{ $img }}" alt="{{ $nombre }}">

                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="admin-actions">
                                            <a class="btn-icon" href="{{ route('productos.edit', $p->id) }}" title="Editar" onclick="event.stopPropagation();">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                                </svg>
                                            </a>

                                            <form class="delete-form" action="{{ route('productos.destroy', $p->id) }}" method="POST" style="margin:0" onclick="event.stopPropagation();">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn-icon delete" title="Eliminar">
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
                                        <a class="btn-icon" href="{{ route('paquetes.edit', $pkg->id) }}" title="Editar" onclick="event.stopPropagation();">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                            </svg>
                                        </a>

                                        <form class="delete-form" action="{{ route('paquetes.destroy', $pkg->id) }}" method="POST" style="margin:0" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn-icon delete" title="Eliminar">
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

          <div class="col-12">
            <div class="field-label" style="margin-bottom:8px;">Columnas a incluir en Excel</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px 16px;">
              <label class="check-wrap">
                <input class="col-check" id="col_categoria" type="checkbox" value="1" checked>
                Categoría
              </label>
              <label class="check-wrap">
                <input class="col-check" id="col_nombre" type="checkbox" value="1" checked>
                Nombre del equipo
              </label>
              <label class="check-wrap">
                <input class="col-check" id="col_marca" type="checkbox" value="1" checked>
                Marca
              </label>
              <label class="check-wrap">
                <input class="col-check" id="col_modelo" type="checkbox" value="1" checked>
                Modelo
              </label>
              <label class="check-wrap">
                <input class="col-check" id="col_beneficios" type="checkbox" value="1" checked>
                Beneficios
              </label>
              <label class="check-wrap">
                <input class="col-check" id="col_precio" type="checkbox" value="1">
                Precio
              </label>
              <label class="check-wrap">
                <input class="col-check" id="col_stock" type="checkbox" value="1">
                Stock
              </label>
            </div>
          </div>

          <div class="col-12" style="border-top:1px solid #eee;padding-top:12px;">
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
    // --- utilidades (normalización y tokens)
    const norm = s =>
        (s || '').toString().trim().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ').trim();
    const tokens = v => norm(v).split(' ').filter(Boolean);

    // --- referencias DOM
    const qEl        = document.getElementById('q');
    const selCat     = document.getElementById('selectCategoria');
    const selSub     = document.getElementById('selectSubtipo');
    const selMarca   = document.getElementById('selectMarca');
    const selStock   = document.getElementById('selectStock');
    const grid       = document.getElementById('mainGrid');
    const allCards   = () => Array.from(document.querySelectorAll('.catalog-item'));

    // --- datos de catálogo (inyectados por partial)
    @include('partials.catalogo-equipos-data')
    // `tiposEquipos` y `marcasModelosPorSubtipo` ya deben existir

    // --- función para llenar cualquier select
    function cascFill(selectElem, arrayData, defaultText) {
        selectElem.innerHTML = '';
        const optDefault = document.createElement('option');
        optDefault.value = '';
        optDefault.textContent = defaultText;
        selectElem.appendChild(optDefault);

        arrayData.forEach(val => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.textContent = val.charAt(0).toUpperCase() + val.slice(1).toLowerCase();
            selectElem.appendChild(opt);
        });
    }

    // --- obtener marcas disponibles según categoría y subtipo
    function cascMarcas(categoria, subtipo) {
        const catKey = norm(categoria);
        const subKey = norm(subtipo);
        const catNode = marcasModelosPorSubtipo[catKey];
        if (!catNode) return [];
        let node = null;
        for (let k in catNode) {
            if (norm(k) === subKey) { node = catNode[k]; break; }
        }
        return node ? Object.keys(node) : [];
    }

    // --- poblar categorías (agregamos "Otros" manualmente)
    const categorias = Object.keys(tiposEquipos);
    cascFill(selCat, categorias, 'Todas las categorías');
    // Añadir opción "Otros" (valor 'OTROS') al final
    const optOtros = document.createElement('option');
    optOtros.value = 'OTROS';
    optOtros.textContent = 'Otros';
    selCat.appendChild(optOtros);

    // --- estado inicial: deshabilitar subtipo y marca
    selSub.disabled   = true;
    selMarca.disabled = true;

    // --- evento cambio de categoría
    selCat.addEventListener('change', () => {
        const catVal = selCat.value;
        if (catVal === '') {
            // No hay categoría seleccionada -> deshabilitar subtipo y marca
            cascFill(selSub, [], 'Todos los subtipos');
            cascFill(selMarca, [], 'Todas las marcas');
            selSub.disabled   = true;
            selMarca.disabled = true;
            selSub.value = '';
            selMarca.value = '';
        } else {
            // Cargar subtipos correspondientes (incluye 'OTROS' si existe en tiposEquipos)
            const subtipos = tiposEquipos[catVal] || [];
            cascFill(selSub, subtipos, 'Todos los subtipos');
            selSub.disabled = false;
            // Resetear marca
            cascFill(selMarca, [], 'Todas las marcas');
            selMarca.disabled = true;
            selMarca.value = '';
        }
        update(); // Refrescar catálogo
    });

    // --- evento cambio de subtipo
    selSub.addEventListener('change', () => {
        const catVal = selCat.value;
        const subVal = selSub.value;
        if (catVal === '' || subVal === '') {
            // No hay categoría o subtipo -> sin marcas
            cascFill(selMarca, [], 'Todas las marcas');
            selMarca.disabled = true;
            selMarca.value = '';
        } else {
            const marcas = cascMarcas(catVal, subVal);
            cascFill(selMarca, marcas, 'Todas las marcas');
            selMarca.disabled = false;
        }
        update();
    });

    // --- resto de filtros
    selMarca.addEventListener('change', update);
    selStock.addEventListener('change', update);
    qEl.addEventListener('input', update);

    // --- función principal de filtrado
    const getVals = () => ({
        q:         norm(qEl.value),
        categoria: norm(selCat.value),
        subtipo:   norm(selSub.value),
        marca:     norm(selMarca.value),
        stock:     selStock.value || 'all',
    });

    const cardPasses = (card, {q, categoria, subtipo, marca, stock}) => {
        const search   = norm(card.dataset.search || '');
        const dTipo    = norm(card.dataset.tipo   || '');
        const dSub     = norm(card.dataset.subtipo|| '');
        const dMarca   = norm(card.dataset.marca  || '');
        const dStock   = (card.dataset.stock || '').trim();
        const kind     = card.dataset.kind || 'producto';

        const toks = tokens(q);
        if (toks.length && !toks.every(t => search.includes(t))) return false;

        if (categoria && !dTipo.includes(categoria)) return false;
        if (subtipo   && !dSub.includes(subtipo))   return false;
        if (marca     && !dMarca.includes(marca))   return false;

        if (stock !== 'all') {
            if (kind === 'paquete') {
                if (dStock !== stock) return false;
            } else {
                const n = parseInt(dStock, 10);
                if (stock === 'with_stock'    && n <= 0) return false;
                if (stock === 'without_stock' && n  > 0) return false;
            }
        }
        return true;
    };

    const update = () => {
        const vals = getVals();
        let visible = 0;
        allCards().forEach(card => {
            const show = cardPasses(card, vals);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Mostrar/ocultar título de paquetes
        const pkgsVisible = Array.from(document.querySelectorAll('.pkg-card')).some(c => c.style.display !== 'none');
        document.querySelectorAll('.package-section-title').forEach(el => el.style.display = pkgsVisible ? '' : 'none');

        // Mensaje de vacío
        const oldMsg = document.getElementById('emptyCatalogMessage');
        if (oldMsg) oldMsg.remove();
        if (visible === 0) {
            const msg = document.createElement('div');
            msg.id = 'emptyCatalogMessage';
            msg.className = 'empty-catalog-message';
            msg.textContent = 'No se encontraron coincidencias.';
            grid.appendChild(msg);
        }

        syncExportButtons();
    };

    // --- exportación (sin cambios relevantes)
    const buildExportUrl = (base, forceProductos) => {
        const p = new URLSearchParams();
        const q = (qEl.value || '').trim();
        if (q) p.set('q', q);
        p.set('scope', forceProductos ? 'productos' : (document.getElementById('exportScope').value || 'all'));
        const t  = selCat.value   || '';
        const s  = selSub.value   || '';
        const m  = selMarca.value || '';
        const st = selStock.value || '';
        if (t) p.set('tipo',    t);
        if (s) p.set('subtipo', s);
        if (m) p.set('marca',   m);
        if (st && st !== 'all') p.set('stock', st);
        p.set('ai', document.getElementById('exportUseAi').checked ? '1' : '0');
        // Columnas seleccionadas (solo para Excel)
        const cols = ['categoria','nombre','marca','modelo','beneficios','precio','stock_col'];
        const colIds = {
            categoria:   'col_categoria',
            nombre:      'col_nombre',
            marca:       'col_marca',
            modelo:      'col_modelo',
            beneficios:  'col_beneficios',
            precio:      'col_precio',
            stock_col:   'col_stock',
        };
        cols.forEach(c => {
            const el = document.getElementById(colIds[c]);
            if (el) p.set('col_' + c, el.checked ? '1' : '0');
        });
        const qs = p.toString();
        return qs ? base + (base.includes('?') ? '&' : '?') + qs : base;
    };

    const syncExportButtons = () => {
        document.getElementById('btnPdf').href     = buildExportUrl(@json(route('catalogo.export.pdf')));
        document.getElementById('btnXlsx').href    = buildExportUrl(@json(route('catalogo.export.xlsx')));
        document.getElementById('btnWooXlsx').href = buildExportUrl(@json(route('productos.export.woocommerce')), true);
    };

    const modalExport = document.getElementById('modalExport');
    modalExport.addEventListener('show.bs.modal', syncExportButtons);
    document.getElementById('exportScope').addEventListener('change', syncExportButtons);
    document.getElementById('exportUseAi').addEventListener('change', syncExportButtons);
    // Actualizar URL al cambiar cualquier columna
    document.querySelectorAll('.col-check').forEach(cb => cb.addEventListener('change', syncExportButtons));

    // --- confirmación de eliminación
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

    // --- descarga PDF
    document.getElementById('btnPdf').addEventListener('click', function(e){
        e.preventDefault();
        const url = this.href;
        if (!url || url === '#') return;
        bootstrap.Modal.getInstance(modalExport)?.hide();
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
                Swal.close();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
            })
            .catch(() => {
                Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar el PDF.' });
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
    });

    // --- descarga Excel / WooCommerce
    ['btnXlsx','btnWooXlsx'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e){
            e.preventDefault();
            const url = this.href;
            if (!url || url === '#') return;
            bootstrap.Modal.getInstance(modalExport)?.hide();
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
                    Swal.close();
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                })
                .catch(() => {
                    Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar el archivo.' });
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                });
        });
    });

    // --- inicializar filtros
    update();
})();
</script>


@endsection