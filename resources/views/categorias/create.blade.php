@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fdf2f8;
        font-family: 'Inter', sans-serif;
    }

    .container {
        max-width: 600px;
        margin: 3rem auto;
        background-color: #fff;
        padding: 2.5rem 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
    }

    h1 {
        text-align: center;
        color: #9d174d;
        font-weight: 700;
        margin-bottom: 2rem;
    }

    label {
        font-weight: 600;
        color: #7c3aed;
        margin-bottom: 0.5rem;
        display: block;
    }

    input[type="text"] {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #f3e8ff;
        background-color: #fef2f2;
        border-radius: 0.65rem;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }

    input[type="text"]:focus {
        outline: none;
        border-color: #f472b6;
        box-shadow: 0 0 0 3px rgba(252, 231, 243, 0.5);
    }

    .btn {
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        border-radius: 0.6rem;
        font-size: 0.95rem;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(90deg, #f472b6, #f9a8d4);
        color: #fff;
        box-shadow: 0 4px 14px rgba(249, 168, 212, 0.4);
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #ec4899, #f472b6);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background-color: #e5e7eb;
        color: #374151;
        margin-left: 0.75rem;
    }

    .btn-secondary:hover {
        background-color: #d1d5db;
    }

    @media (max-width: 600px) {
        .container {
            padding: 1.5rem;
        }
    }
</style>

<div class="container">
    <h1>Agregar nueva Categoría</h1>

    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de Categoría</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
