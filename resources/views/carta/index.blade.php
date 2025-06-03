@extends('layouts.app')

@section('title', 'Cartas de Garantía')
@section('titulo', 'Cartas de Garantía')

@section('content')
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            margin: 0;
            background: linear-gradient(-45deg, #ffe5ec, #e0f7fa, #e2ece9, #fff1e6);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .carta-container {
            max-width: 960px;
            margin: 90px auto;
            padding: 2rem 1.5rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .top-bar h3 {
            margin: 0;
            color: #023047;
            font-size: 1.6rem;
        }

        .btn-primary,
        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s ease-in-out, background-color 0.2s ease;
        }

        .btn-primary {
            background-color: #219ebc;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #126782;
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #ef476f;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c43052;
            transform: scale(1.05);
        }

        .table-card {
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            padding: 1rem;
            text-align: left;
        }

        th {
            background-color: #e0f7fa;
            color: #0077b6;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.2s ease;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .alert {
            background-color: #d8f3dc;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            color: #2d6a4f;
            text-align: center;
        }

        .empty-message {
            text-align: center;
            padding: 2rem;
            color: #888;
        }
        @media (max-width: 600px) {
        .top-bar h3 {
    margin-left: 1rem;
}
}
.swal2-popup {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    border-radius: 15px;
}
.swal2-title {
    color: #343a40;
}
.swal2-content {
    color: #495057;
}
.btn-custom-confirm {
    background-color: #4CAF50; /* Verde suave */
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    border: none; /* Eliminar borde */
    margin-right: 10px; /* Separar botones */
    transition: background-color 0.3s ease;
}
.btn-custom-confirm:hover {
    background-color: #45a049; /* Verde un poco más oscuro */
}
.btn-custom-cancel {
    background-color: #DC3545; /* Gris suave */
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    border: none; /* Eliminar borde */
    margin-left: 10px; /* Separar botones */
    transition: background-color 0.3s ease;
}
.btn-custom-cancel:hover {
    background-color: #C82333; /* Gris un poco más oscuro */
}
    </style>

    <div class="carta-container">
        <div class="top-bar">
            <h3>Cartas de Garantía</h3>
            <a href="{{ route('carta.create') }}" class="btn-primary">
                <i class="fa-solid fa-upload"></i> Agregar
            </a>
        </div>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Nombre del documento</th>
                        <th style="width: 170px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cartas as $carta)
                        <tr>
                            <td>{{ $carta->nombre }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('carta.descargar', $carta->id) }}" class="btn-primary" title="Descargar">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <form action="{{ route('carta.destroy', $carta->id) }}" method="POST" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty-message">No hay cartas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
