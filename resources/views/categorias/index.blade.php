@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fdf2f8;
        font-family: 'Inter', sans-serif;
    }

    .container {
        max-width: 720px;
        margin: 3rem auto;
        background-color: #fff;
        padding: 2.5rem 2rem;
        border-radius: 1rem;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
    }

    h1, h3 {
        text-align: center;
        font-weight: 700;
        color: #9d174d;
        margin-bottom: 1.5rem;
    }

    .btn-success {
        background: linear-gradient(90deg, #34d399, #10b981);
        border: none;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 0.6rem;
        box-shadow: 0 4px 12px rgba(52, 211, 153, 0.3);
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #059669, #34d399);
        transform: translateY(-1px);
    }

    .btn-danger {
        background-color: #ef4444;
        border: none;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
    }

    .btn-danger:hover {
        background-color: #dc2626;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    ul li {
        background-color: #fef2f2;
        margin-bottom: 1rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 5px solid #7c3aed;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
        font-weight: 500;
    }

    ul li span {
        flex-grow: 1;
        color: #374151;
    }
</style>

<div class="container">
    <h1>Categorías</h1>

    <div class="text-center mb-4">
        <a href="{{ route('categorias.create') }}" class="btn btn-success">+ Agregar Categoría</a>
    </div>

    <hr>
    <h3>Lista de Categorías</h3>

    <ul>
        @forelse($categorias as $categoria)
            <li>
                <span>{{ $categoria->nombre }}</span>
                <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </li>
        @empty
            <li>No hay categorías registradas.</li>
        @endforelse
    </ul>
</div>
@endsection
