@extends('layouts.app')

@section('title', 'Cartas de Garantía')
@section('titulo', 'Cartas de Garantía')

@section('content')

    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/cartas.css') }}?v={{ time() }}">

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
                            <td style="text-transform: uppercase;">{{ $carta->nombre }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('carta.descargar', $carta->id) }}" class="btn-primary" title="Descargar">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <form action="{{ route('carta.destroy', $carta->id) }}" method="POST">
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
