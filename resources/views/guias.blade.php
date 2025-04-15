@extends('layouts.app')
@section('title', 'Guias')
@section('titulo', 'Registro Guias')
@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
<div class="form-container">
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }} <br>
        @endforeach
    </div>
@endif


    <form action="{{ route('guias.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="numero_rastreo" class="label_nomina">Número de Rastreo</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/fedex.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" class="form-control" id="numero_rastreo" name="numero_rastreo" 
                    placeholder="Ej. 8181 6757 8340" maxlength="14" 
                    style="background-color: #ffff; display:block; width: 100%;" required>
            </div>
        </div>

        <div class="form-group">
            <label for="peso" class="label_nomina">Peso Total (kg)</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/peso.png') }}" alt="Acceso" class="icon2">
                </div>
                <select class="form-control" id="peso" name="peso" style="background-color: #ffff; display:block; width: 100%;" required>
                    <option value="">Seleccione</option>
                    <option value="1">1 kg</option>
                    <option value="5">5 kg</option>
                    <option value="10">10 kg</option>
                    <option value="15">15 kg</option>
                    <option value="20">20 kg</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
    <label for="fecha_recepcion" class="label_nomina">Fecha de Recepción</label>
    <input type="date" class="form-control select" id="fecha_recepcion" name="fecha_recepcion" required 
        value="{{ \Carbon\Carbon::today()->toDateString() }}">
</div>

<button type="submit" class="btn btn-primary">Registrar</button>

</div>

<script>
    document.getElementById('numero_rastreo').addEventListener('input', function (e) {
        let valor = e.target.value.replace(/\D/g, ''); // Solo números
        valor = valor.substring(0, 14); // Máximo 12 dígitos
        let formato = valor.match(/.{1,4}/g)?.join(' ') || ''; 
        e.target.value = formato;
    });
</script>
<script>
document.getElementById('numero_rastreo').addEventListener('blur', function () {
    let numeroRastreo = this.value.replace(/\s+/g, ''); // Elimina espacios
    if (numeroRastreo.length === 14) {
        fetch(`/verificar-guia/${numeroRastreo}`)
            .then(response => response.json())
            .then(data => {
                if (data.existe) {
                    alert('Esta guía ya ha sido registrada.');
                    this.value = ''; // Borra el campo
                }
            });
    }
});
</script>

</body>
@endsection
