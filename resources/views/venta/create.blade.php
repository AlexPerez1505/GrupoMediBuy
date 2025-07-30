@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h1>Ventas Pendientes a Armar</h1>
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Venta (Folio)</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            <tr>
                <td><b>{{ $venta->folio }}</b></td>
                <td>{{ $venta->fecha }}</td>
                <td>{{ $venta->cliente }}</td>
                <td>
                    <a href="{{ route('ventas.productos', $venta->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-list-check"></i> Checklist
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Sin ventas pendientes.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
