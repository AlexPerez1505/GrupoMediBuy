@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Nuevo Aparato</h1>
  <form action="{{ route('aparatos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input name="nombre" placeholder="Nombre" class="form-control mb-2" required>
    <input name="modelo" placeholder="Modelo" class="form-control mb-2">
    <input name="marca"  placeholder="Marca"  class="form-control mb-2">
    <input name="stock"  type="number" min="1" value="1" class="form-control mb-2" required>
    <input name="precio" type="number" step="0.01" value="0" class="form-control mb-2" required>
    <input name="imagen" type="file" class="form-control mb-2">
    <button class="btn btn-primary">Guardar</button>
  </form>
</div>
@endsection
