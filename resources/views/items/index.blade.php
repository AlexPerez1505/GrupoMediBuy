@extends('layouts.app')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-green: #d3f9d8;
        --accent-green: #38b000;
        --pastel-red: #ffd6d6;
        --accent-red: #fa5252;
        --pastel-gray: #f1f3f5;
    }
    body {
        background: var(--pastel-gray);
    }
    .main-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(100,100,100,.09);
        padding: 32px 24px 24px 24px;
        margin-bottom: 32px;
        animation: fadeIn .7s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px);}
        to { opacity: 1; transform: none;}
    }
    h1 {
        color: var(--accent-blue);
        font-weight: 700;
        font-size: 2rem;
        letter-spacing: -1px;
    }
    table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 16px;
    }
    th {
        background: var(--pastel-blue);
        color: var(--accent-blue);
        border: none;
        font-weight: 600;
    }
    td {
        vertical-align: middle !important;
    }
    /* Botones de acción solo icono, pastel, alineados horizontal */
    .btn-action {
        width: 33px;
        height: 33px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 12px;
        font-size: 1.3rem;
        margin: 0 3px;
        box-shadow: 0 2px 8px rgba(150,180,230,.06);
        transition: box-shadow .16s, transform .16s, background .16s;
        cursor: pointer;
    }
    .btn-action:focus { outline: none; box-shadow: 0 0 0 2px #b3dafe; }
    .btn-view    { background: var(--pastel-blue); }
    .btn-edit    { background: var(--pastel-green); }
    .btn-delete  { background: var(--pastel-red); }
    .btn-view i  { color: var(--accent-blue); }
    .btn-edit i  { color: var(--accent-green); }
    .btn-delete i{ color: var(--accent-red); }
    .btn-action:hover {
        box-shadow: 0 4px 18px rgba(150,180,230,.17);
        transform: scale(1.09);
        filter: brightness(1.10);
    }
    .btn-add {
        background: var(--pastel-blue);
        color: var(--accent-blue);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 8px 22px;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(150,180,230,.08);
        transition: box-shadow .2s, transform .2s, filter .2s;
    }
    .btn-add:hover {
        filter: brightness(1.07);
        box-shadow: 0 4px 16px rgba(150,180,230,.15);
    }
    @media (max-width: 650px){
        table, thead, tbody, th, td, tr { display: block; }
        th, td { border: none; }
        tr { margin-bottom: 1rem; }
    }
</style>

<div class="container mt-4" >
    <div class="main-card" style="margin-top:110px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Items</h1>
            <a href="{{ route('items.create') }}" class="btn btn-add">+ Nuevo Item</a>
        </div>
        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif
        <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th style="width:120px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><b>{{ $item->nombre }}</b></td>
                    <td>{{ $item->codigo }}</td>
                    <td>{{ $item->descripcion }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('items.show', $item) }}" 
                               class="btn-action btn-view" 
                               title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('items.edit', $item) }}" 
                               class="btn-action btn-edit" 
                               title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este item?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Sin items registrados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
