@extends('layouts.app')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-green: #d3f9d8;
        --accent-green: #38b000;
        --pastel-pink: #ffe0e9;
        --accent-pink: #d6336c;
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
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px);}
        to { opacity: 1; transform: none;}
    }
    h1 {
        color: var(--accent-blue);
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 1.5rem;
        letter-spacing: -1px;
    }
    label {
        color: var(--accent-blue);
        font-weight: 500;
        margin-bottom: 6px;
    }
    .form-control {
        border-radius: 12px;
        border: 1.5px solid var(--pastel-blue);
        background: #fff;
        color: var(--accent-blue);
        font-weight: 500;
        font-size: 1.09rem;
        margin-bottom: 18px;
        box-shadow: 0 2px 8px rgba(150,180,230,0.04);
        transition: border-color .18s, box-shadow .18s, transform .18s;
    }
    .form-control:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 2px #b3dafe30;
        background: #f8fbfe;
        color: var(--accent-blue);
        transform: scale(1.04);
        z-index: 1;
        position: relative;
    }
    .btn-pastel-save {
        background: var(--pastel-green);
        color: var(--accent-green);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 28px;
        font-size: 1.12rem;
        margin-right: 6px;
        box-shadow: 0 2px 8px rgba(56,176,0,0.06);
        transition: box-shadow .17s, filter .17s, transform .17s;
    }
    .btn-pastel-save:focus,
    .btn-pastel-save:hover {
        filter: brightness(1.06);
        box-shadow: 0 4px 16px rgba(56,176,0,0.16);
        transform: scale(1.06);
    }
    .btn-pastel-back {
        background: var(--pastel-pink);
        color: var(--accent-pink);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 24px;
        font-size: 1.12rem;
        transition: box-shadow .17s, filter .17s, transform .17s;
    }
    .btn-pastel-back:focus,
    .btn-pastel-back:hover {
        filter: brightness(1.06);
        box-shadow: 0 4px 14px rgba(214,51,108,0.15);
        transform: scale(1.06);
    }
</style>

<div class="container mt-4">
    <div class="main-card" style="margin-top:90px;">
        <h1>Editar Item</h1>
        <form action="{{ route('items.update', $item) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required autocomplete="off" value="{{ old('nombre', $item->nombre) }}">
            </div>
            <div class="mb-3">
                <label for="codigo">Código</label>
