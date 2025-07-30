@extends('layouts.app')
@section('content')
<div class="wizard-step" style="max-width:750px;margin:30px auto;background:#fff;padding:30px 38px;border-radius:14px;box-shadow:0 8px 40px rgba(0,0,0,.08);">

    <h3 class="mb-4" style="color:#228be6">Entrega</h3>

    {{-- Resumen de ingeniería --}}
    @php
        $ingenieriaComponentes = [];
        if(isset($ingenieria) && isset($ingenieria->componentes)) {
            $ingenieriaComponentes = is_array($ingenieria->componentes)
                ? $ingenieria->componentes
                : json_decode($ingenieria->componentes, true);
        }
    @endphp
    @if(!empty($ingenieriaComponentes) && is_array($ingenieriaComponentes))
        <div class="mb-4 p-3" style="background:#e3f0fd; border-radius:1rem; border-left:4px solid #228be6;">
            <div style="color:#228be6;font-weight:600;margin-bottom:10px;"><i class="fa fa-cogs"></i> Ingeniería</div>
            <div class="table-responsive">
                <table class="table table-sm" style="background:#f8fafc;border-radius:10px;">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Componente</th>
                            <th>Estado</th>
                            <th>Incidencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingenieriaComponentes as $productoId => $compArr)
                            @foreach($compArr as $componente => $det)
                                <tr>
                                    <td>
                                        @php $prod = $productos->firstWhere('id', $productoId); @endphp
                                        {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                                    </td>
                                    <td>{{ $componente }}</td>
                                    <td>
                                        <span class="badge" style="background:#ace2e1;color:#136a7c;font-weight:600;border-radius:.7rem;font-size:.98rem;">
                                            {{ ucfirst($det['estado'] ?? '') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($det['incidencia']))
                                            <span style="color:#d85b66;">{{ $det['incidencia'] }}</span>
                                        @else
                                            <span style="color:#bdbdbd;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!empty($ingenieria->incidente))
                <div class="mt-2" style="color:#d85b66;">
                    <b>Incidente general:</b> {{ $ingenieria->incidente }}
                </div>
            @endif
        </div>
    @endif

    {{-- Resumen de embalaje --}}
    @php
        $embalajeComponentes = [];
        if(isset($embalaje) && isset($embalaje->componentes)) {
            $embalajeComponentes = is_array($embalaje->componentes)
                ? $embalaje->componentes
                : json_decode($embalaje->componentes, true);
        }
    @endphp
    @if(!empty($embalajeComponentes) && is_array($embalajeComponentes))
        <div class="mb-4 p-3" style="background:#fdf8e3; border-radius:1rem; border-left:4px solid #f6c453;">
            <div style="color:#f6c453;font-weight:600;margin-bottom:10px;"><i class="fa fa-box"></i> Embalaje</div>
            <div class="table-responsive">
                <table class="table table-sm" style="background:#f8fafc;border-radius:10px;">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Componente</th>
                            <th>Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($embalajeComponentes as $productoId => $compArr)
                            @foreach($compArr as $componente => $det)
                                <tr>
                                    <td>
                                        @php $prod = $productos->firstWhere('id', $productoId); @endphp
                                        {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                                    </td>
                                    <td>{{ $componente }}</td>
                                    <td>
                                        <span class="badge" style="background:#f6c453;color:#975e06;font-weight:600;border-radius:.7rem;font-size:.98rem;">
                                            {{ ucfirst($det['estado'] ?? '') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($det['observacion']))
                                            <span style="color:#c7a54a;">{{ $det['observacion'] }}</span>
                                        @else
                                            <span style="color:#bdbdbd;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!empty($embalaje->observaciones))
                <div class="mt-2" style="color:#975e06;">
                    <b>Observación general:</b> {{ $embalaje->observaciones }}
                </div>
            @endif
        </div>
    @endif

   <form id="entrega-form" method="POST" action="{{ route('checklists.guardarEntrega', $venta->id) }}">
    @csrf
    <label>Tipo de entrega</label>
    <select name="tipo_entrega" id="tipo_entrega" class="form-control mb-2" required>
        <option value="">Selecciona</option>
        <option value="paqueteria">Paquetería</option>
        <option value="hospital">Hospital</option>
    </select>

    <label>Comentario final</label>
    <textarea name="comentario_final" class="form-control mb-3"></textarea>

    {{-- QR dinámico desde backend --}}
    <div id="qr-hospital" style="display:none;text-align:center;margin-bottom:22px;">
        <div style="color:#228be6;font-weight:500;margin-bottom:7px;">
            <i class="fa fa-qrcode"></i> Escanea este código para recepción digital
        </div>
        @if(!empty($qrHtml))
            <div>{!! $qrHtml !!}</div>
            <small style="color:#868e96;">
                El personal del hospital debe escanear este QR, hacer checklist de recibido, firmar y registrar nombre.
            </small>
        @endif
    </div>

    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-success btn-lg">Finalizar Checklist</button>
    </div>
</form>

{{-- Mostrar QR solo si se elige hospital --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoEntrega = document.getElementById('tipo_entrega');
        const qrDiv = document.getElementById('qr-hospital');

        function toggleQR() {
            if (tipoEntrega.value === 'hospital') {
                qrDiv.style.display = '';
            } else {
                qrDiv.style.display = 'none';
            }
        }

        tipoEntrega.addEventListener('change', toggleQR);
        toggleQR(); // Ejecutar al cargar
    });
</script>

@endsection
