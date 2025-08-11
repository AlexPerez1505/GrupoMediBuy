@extends('layouts.app')
@section('title', 'Ficha de equipo')
@section('title', 'Ficha de equipo')
@section('content')
<link rel="stylesheet" href="{{ asset('css/detalle.css') }}?v={{ time() }}">
<div class="container py-4 animated-entry" style="margin-top:150px;">
    <div class="row justify-content-center gx-5 gy-4">
        <!-- Ficha técnica (columna izquierda en escritorio) -->
      <div class="col-12 col-lg-5">
    <div class="card shadow-sm rounded-4 p-4 mb-3 mb-lg-0" style="background: #eef1fb; border: none;">
        <div class="d-flex align-items-center mb-3" style="gap:20px;">
            <div>
                <i class="fa fa-cogs" style="font-size:2.2rem; color: #7c3aed;"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-primary" style="font-size: 1.5rem;">Ficha técnica del equipo</h4>
                <div class="text-secondary" style="font-size: 1rem;">
                    {{ $registro->tipo_equipo ?? '-' }}
                    @if($registro->subtipo_equipo) &middot; {{ $registro->subtipo_equipo }} @endif
                </div>
            </div>
        </div>
        {{-- Galería de imágenes y video --}}
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @foreach(['foto1','foto2','foto3'] as $foto)
                    @if(!empty($registro->$foto))
                        <a href="{{ asset('storage/'.$registro->$foto) }}" target="_blank" class="preview-img-link">
                            <img src="{{ asset('storage/'.$registro->$foto) }}"
                                 alt="Foto equipo"
                                 class="preview-img shadow"
                                 loading="lazy">
                        </a>
                    @endif
                @endforeach
                @if(!empty($registro->video_equipo))
                    <video controls class="preview-video shadow" style="max-width:60px;max-height:56px;border-radius:11px;background:#e6ecf5;">
                        <source src="{{ asset('storage/'.$registro->video_equipo) }}" type="video/mp4">
                        Tu navegador no soporta video.
                    </video>
                @endif
            </div>
        </div>
        <div class="row mb-2 gy-2 gx-4">
            <div class="col-6 small"><b>Marca:</b> <span class="text-secondary">{{ $registro->marca ?? '-' }}</span></div>
            <div class="col-6 small"><b>Modelo:</b> <span class="text-secondary">{{ $registro->modelo ?? '-' }}</span></div>
            <div class="col-6 small"><b>Serie:</b> <span class="text-secondary">{{ $registro->numero_serie ?? '-' }}</span></div>
            <div class="col-6 small"><b>Año:</b> <span class="text-secondary">{{ $registro->anio ?? '-' }}</span></div>
            <div class="col-6 small"><b>Estado:</b>
                <span class="badge rounded-pill px-3 py-1" style="background: #d1d5fa; color: #4f46e5;">
                    {{ ucfirst($registro->estado_proceso) }}
                </span>
            </div>
            <div class="col-6 small"><b>Fecha adquisición:</b> <span class="text-secondary">{{ $registro->fecha_adquisicion ?? '-' }}</span></div>
        </div>
        <div class="small text-muted mt-2" style="font-size:13px;">
            <b>Descripción:</b> {{ $registro->descripcion ?? '-' }}
        </div>
        @if($registro->observaciones)
            <div class="small text-gray-700 mt-2" style="font-size:13px;">
                <b>Observaciones:</b> {{ $registro->observaciones }}
            </div>
        @endif
    </div>
</div>
    <div class="col-12 col-lg-7">
        <div class="card shadow rounded-4 p-4 mb-4" style="background:#f8faff; border:none;">
            <h2 class="mb-4 text-primary" style="font-weight:700;font-size:2rem;letter-spacing:-1px;">Procesos del equipo</h2>
            <div id="wizard-steps">
                @foreach($pasos as $i => $paso)
                    @php
                        $proceso = $procesos[$paso];
                        $nombreBonito = [
                            'hojalateria' => 'Hojalatería',
                            'mantenimiento' => 'Mantenimiento',
                            'stock' => 'Stock',
                            'vendido' => 'Vendido',
                        ][$paso] ?? ucfirst($paso);
                        $routeName = 'proceso.' . $paso;

                        // Detectar si está vacío o es un objeto vacío
                        $procesoVacio = empty($proceso) || (is_object($proceso) && count((array)$proceso) == 0);
                    @endphp

                    <div class="wizard-step fade-step" style="{{ $i !== 0 ? 'display:none;' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-pill px-3 py-2"
                                style="background:#ede7f6;color:#6d4caf;font-size:15px;">
                                {{ $nombreBonito }}
                            </span>
                            @if(!$procesoVacio)
                                <span class="badge bg-success ms-2" style="font-size:12px;">✔ Completado</span>
                            @else
                                <span class="badge bg-warning text-dark ms-2" style="font-size:12px;">Pendiente</span>
                            @endif
                        </div>

                        @if($procesoVacio)
                            <div class="my-3">
                                <span class="text-danger" style="font-size:14px;">Este proceso aún no ha sido registrado.</span>
                            </div>
                            @if(Route::has($routeName))
                            <a href="{{ route($routeName, $registro->id) }}" class="btn btn-outline-primary rounded-pill mt-1 fw-bold">
                                <i class="fa fa-plus"></i> Completar {{ $nombreBonito }}
                            </a>
                            @endif
                        @else
                            <div style="font-size:15px;">
                                <b>Descripción:</b>
                                <span class="text-muted">{{ $proceso->descripcion_proceso }}</span><br>
                                <b>Defectos:</b>
                                <span class="text-muted">{{ $proceso->defectos ? implode(', ', (array) json_decode($proceso->defectos)) : 'Ninguno' }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @for($e=1;$e<=3;$e++)
                                    @php $ev = "evidencia$e"; @endphp
                                    @if($proceso->$ev)
                                        <img src="{{ asset('storage/'.$proceso->$ev) }}" alt="Evidencia {{$e}}" class="rounded shadow-sm border" style="width:48px;height:48px;object-fit:cover;">
                                    @endif
                                @endfor
                            </div>
                            @if($proceso->video)
                                <div class="mt-2">
                                    <video controls class="rounded shadow-sm border" style="max-width:120px;">
                                        <source src="{{ asset('storage/'.$proceso->video) }}" type="video/mp4">
                                    </video>
                                </div>
                            @endif
                            @if($proceso->documento_pdf)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/'.$proceso->documento_pdf) }}" class="text-secondary" target="_blank">
                                        <i class="fa fa-file-pdf-o"></i> Ver PDF adjunto
                                    </a>
                                </div>
                            @endif
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            @if($i > 0)
                                <button class="btn btn-link text-secondary prev-step px-0">← Anterior</button>
                            @else
                                <span></span>
                            @endif

                            @if($i < count($pasos) - 1)
                                <button class="btn btn-link text-primary next-step px-0">Siguiente →</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let steps = document.querySelectorAll('.wizard-step');
    let current = 0;
    function showStep(idx) {
        steps.forEach((step,i) => step.style.display = (i === idx ? '' : 'none'));
        current = idx;
    }
    document.querySelectorAll('.next-step').forEach(btn => {
        btn.onclick = () => showStep(current+1);
    });
    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.onclick = () => showStep(current-1);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let steps = document.querySelectorAll('.wizard-step');
    let pendingStep = 0;

    // Buscar el índice del primer proceso pendiente
    steps.forEach(function(step, idx) {
        if (pendingStep === 0 && step.querySelector('.badge.bg-warning')) {
            pendingStep = idx;
        }
    });

    let current = pendingStep;

    function showStep(idx) {
        if (idx < 0 || idx >= steps.length) return; // Evita salir de rango
        steps.forEach((step, i) => step.style.display = (i === idx ? '' : 'none'));
        current = idx; // Actualiza current correctamente
    }

    showStep(current);

    document.querySelectorAll('.next-step').forEach(btn => {
        btn.onclick = () => showStep(current + 1);
    });
    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.onclick = () => showStep(current - 1);
    });
});
</script>

@endsection
