@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #e6f0fa;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #1e293b;
        font-size: 16px;
        line-height: 1.6;
    }

    h1 {
        font-size: 2.2rem;
        color: #1e40af;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .card {
        background: #ffffff;
        border: none;
        border-radius: 1.2rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        padding: 2rem;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0ea5e9;
        margin-top: 1rem;
        margin-bottom: 0.3rem;
    }

    .card-text {
        font-size: 1rem;
        color: #475569;
        margin-bottom: 0.75rem;
    }

    .item-box {
        background-color: #f9fbfd;
        border: 1px solid #e2e8f0;
        border-radius: 0.8rem;
        padding: 1.2rem;
        margin-bottom: 1rem;
        transition: background 0.3s ease;
    }

    .item-box:hover {
        background-color: #f0f9ff;
    }

    .item-box p {
        margin-bottom: 0.4rem;
        font-size: 0.95rem;
    }

    .item-box strong {
        color: #0f172a;
    }



    @media (max-width: 600px) {
        .card, .item-box {
            padding: 1rem;
        }

        h1 {
            font-size: 1.6rem;
        }
    }
</style>

<div class="container" style="margin-top:80px;">

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Cliente</h5>
            <p class="card-text">{{ $remision->cliente->nombre}}  {{ $remision->cliente->apellido ?? 'No disponible' }}</p>
            <h5 class="card-title">Celular</h5>
            <p class="card-text">{{ $remision->cliente->telefono ?? 'No disponible'  }}</p>
          

            <h5 class="card-title">Asesor de venta</h5>
            <p class="card-text">{{ mb_strtoupper($remision->usuario->name ?? 'No disponible', 'UTF-8') }}</p>

            <h5 class="card-title">Ítems</h5>
@foreach($remision->items as $item)
    <div class="border rounded p-3 mb-3 bg-light">
        <p><strong>Unidad:</strong> {{ $item->unidad }}</p>
        <p><strong>Cantidad:</strong> {{ $item->cantidad }}</p>
        <p><strong>Artículo:</strong> {{ $item->nombre_item }}</p>
        <p><strong>Descripción:</strong> {{ $item->descripcion_item }}</p>
        <p><strong>Importe Unitario:</strong> ${{ number_format($item->importe_unitario, 2) }}</p>
        <p><strong>A cuenta:</strong> ${{ number_format($item->a_cuenta, 2) }}</p>
        <p><strong>Subtotal:</strong> ${{ number_format($item->subtotal, 2) }}</p>
        <p><strong>Restante:</strong> ${{ number_format($item->restante, 2) }}</p>
    </div>
@endforeach


            <h5 class="card-title">Subtotal</h5>
            <p class="card-text"> ${{ number_format($remision->subtotal, 2) }}</p>
            <h5 class="card-title">IVA</h5>
            <p class="card-text"> ${{ number_format($remision->iva, 2) }}</p>
            <h5 class="card-title">Total</h5>
            <p class="card-text"> ${{ number_format($remision->total, 2) }}</p>
            <h5 class="card-title">Importe con letra</h5>
            <p class="card-text">{{ mb_strtoupper($remision->importe_letra, 'UTF-8') }}</p>

        </div>
    </div>

  <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
    <a href="{{ route('remisions.descargarPdf', $remision->id) }}" class="btn btn-primary">Descargar PDF</a>
</div>
@endsection
