@extends('layouts.app')

@include('components.registro-uso')
@section('title', 'FichaTecnica')
@section('titulo', 'Ficha Tecnica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/fichas.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="center-container">
<a href="{{ route('fichas.create') }}" class="btn btn-custom-confirm">+ Agregar</a>
</div>
<div class="container">


    <table class="w-full mt-4 border-collapse">
        <thead>
        <tr class="bg-gray-200">
                <th class="p-2 border">Nombre</th>
                <th class="p-2 border">Archivo</th>
                <th class="p-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fichas as $ficha)
                <tr>
                <td class="p-2 border">{{ $ficha->nombre }}</td>
                <td class="p-2 border">
    <a href="{{ route('fichas.download', $ficha) }}" target="_blank">
        <img src="{{ asset('images/descarga.png') }}" alt="Descargar Ficha" class="img-ficha">
    </a>
</td>



                <td class="p-2 border">
                        <form action="{{ route('fichas.destroy', $ficha) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-custom-cancel">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                const formElement = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'border-radius-15',
                        title: 'font-weight-bold text-dark',
                        content: 'font-size-16',
                        confirmButton: 'btn-custom-confirm',
                        cancelButton: 'btn-custom-cancel',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        formElement.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
