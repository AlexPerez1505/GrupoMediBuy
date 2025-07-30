@extends('layouts.app')
@section('content')

<div class="wizard-step" style="max-width:700px;margin:30px auto;background:#fff;padding:30px 38px;border-radius:14px;box-shadow:0 8px 40px rgba(0,0,0,.08);">

    <h3 class="mb-4" style="color:#228be6">Checklist de Embalaje</h3>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success text-center mb-4" style="border-radius:1.2rem;">{{ session('success') }}</div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius:1.2rem;">
            <ul class="mb-0" style="list-style:none;">
                @foreach ($errors->all() as $error)
                    <li style="color:#cb2d6f;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Resumen detallado del checklist de ingeniería --}}
    @php
        // SEGURIDAD: Decodificar correctamente para evitar error en el foreach
        $ingenieriaComponentes = [];
        if(isset($ingenieria) && isset($ingenieria->componentes)) {
            $ingenieriaComponentes = is_array($ingenieria->componentes)
                ? $ingenieria->componentes
                : json_decode($ingenieria->componentes, true);
        }
    @endphp

    @if(!empty($ingenieriaComponentes) && is_array($ingenieriaComponentes))
        <div class="mb-4 p-3" style="background:#f7f9fb; border-radius:1rem; border-left:4px solid #228be6;">
            <div style="color:#228be6; font-weight:600; margin-bottom: 10px;">
                <i class="fa fa-cogs"></i> Resumen de Ingeniería (capturado por el ingeniero)
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-2" style="background:#f8fafc;border-radius:10px;">
                    <thead>
                        <tr>
                            <th style="font-size: 1rem;">Producto</th>
                            <th style="font-size: 1rem;">Componente</th>
                            <th style="font-size: 1rem;">Estado</th>
                            <th style="font-size: 1rem;">Incidencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingenieriaComponentes as $productoId => $compArr)
                            @foreach($compArr as $componente => $det)
                                <tr>
                                    <td>
                                        @php
                                            $prod = $productos->firstWhere('id', $productoId);
                                        @endphp
                                        {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                                    </td>
                                    <td>{{ $componente }}</td>
                                    <td>
                                        @php $estado = $det['estado'] ?? ''; @endphp
                                        <span class="badge"
                                            style="background:#ace2e1;color:#136a7c;font-weight:600;border-radius:.7rem;font-size:.98rem;">
                                            {{ ucfirst($estado) }}
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
            {{-- Firma de ingeniería (opcional) --}}
            @if(isset($ingenieria->firma_responsable))
                <div class="mt-2">
                    <b>Firma del ingeniero:</b><br>
                    <img src="{{ asset('storage/' . $ingenieria->firma_responsable) }}" style="width:180px; border:1.5px solid #bdbdbd;border-radius:1.2rem;">
                </div>
            @endif
        </div>
    @else
        <div class="alert alert-info mb-4">Aún no se ha registrado información de ingeniería.</div>
    @endif

    <form id="embalaje-form" method="POST" action="{{ route('checklists.guardarEmbalaje', $venta->id) }}" enctype="multipart/form-data">
        @csrf

        {{-- Tabla checklist embalaje --}}
        <div class="table-responsive mb-4">
            <table class="table animate-fadein" style="background: #f8fafc; border-radius: 14px;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Componente</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        @php
                            $componentes = [];
                            if(str_contains(strtolower($producto->tipo_equipo),'monitor')) $componentes=['Monitor','Eliminador','Cable de poder'];
                            elseif(str_contains(strtolower($producto->tipo_equipo),'camara')) $componentes=['Cabezal','Cable DVI','Cable de poder'];
                            else $componentes=[$producto->tipo_equipo];
                        @endphp
                        @foreach($componentes as $componente)
                        <tr>
                            <td>{{ $producto->marca }} {{ $producto->modelo }}</td>
                            <td>{{ $componente }}</td>
                            <td>
                                <select name="componentes[{{$producto->id}}][{{$componente}}][estado]" class="form-control" required>
                                    <option value="">Selecciona</option>
                                    <option value="ok">OK</option>
                                    <option value="falta">Falta</option>
                                    <option value="dañado">Dañado</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="componentes[{{$producto->id}}][{{$componente}}][observacion]" class="form-control" placeholder="Observación (opcional)">
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <label>Observaciones embalaje:</label>
        <textarea name="embalaje_observacion" class="form-control mb-3"></textarea>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Firma Responsable</label>
                <div style="border:1.5px solid #cfd8dc;border-radius:7px;position:relative;">
                    <canvas id="firmaCanvas1" width="320" height="90" style="width:100%;height:90px;touch-action:none;"></canvas>
                    <button type="button" id="limpiar1" class="btn btn-sm btn-light" style="position:absolute;top:2px;right:4px;">Limpiar</button>
                </div>
                <input type="hidden" name="firma_responsable" id="firmaInput1">
            </div>
            <div class="col-md-6">
                <label>Firma Supervisor</label>
                <div style="border:1.5px solid #cfd8dc;border-radius:7px;position:relative;">
                    <canvas id="firmaCanvas2" width="320" height="90" style="width:100%;height:90px;touch-action:none;"></canvas>
                    <button type="button" id="limpiar2" class="btn btn-sm btn-light" style="position:absolute;top:2px;right:4px;">Limpiar</button>
                </div>
                <input type="hidden" name="firma_supervisor" id="firmaInput2">
            </div>
        </div>

        @if(isset($firmaGuardada) && $firmaGuardada)
          <img src="{{ asset('storage/'.$firmaGuardada) }}" style="width:220px;">
        @endif

        <label>Evidencias (foto/archivo):</label>
        <input type="file" name="evidencias[]" class="form-control mb-3" multiple accept="image/*">

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary btn-lg">Guardar y continuar</button>
        </div>

        @if(isset($checklistEmbalaje) && $checklistEmbalaje)
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('checklists.embalaje.pdf', $venta->id) }}" target="_blank" class="btn btn-outline-success" style="border-radius:1.3rem;">
                <i class="fa fa-file-pdf"></i> Descargar PDF
            </a>
        </div>
        @endif

    </form>
</div>

<script>
function firmaCanvas(idCanvas, idInput, idBtn) {
    const canvas = document.getElementById(idCanvas);
    const ctx = canvas.getContext('2d');
    let drawing = false, lastX = 0, lastY = 0;

    function draw(x, y) {
        if(!drawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = "#111";
        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    // Mouse
    canvas.addEventListener('mousedown', e => { drawing = true; [lastX,lastY]=[e.offsetX,e.offsetY]; ctx.beginPath(); ctx.moveTo(lastX,lastY); });
    canvas.addEventListener('mouseup', () => { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mouseout', () => { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mousemove', e => { draw(e.offsetX,e.offsetY); });

    // Touch
    canvas.addEventListener('touchstart', function(e){
        e.preventDefault();
        drawing = true;
        const rect = canvas.getBoundingClientRect();
        lastX = e.touches[0].clientX - rect.left;
        lastY = e.touches[0].clientY - rect.top;
        ctx.beginPath(); ctx.moveTo(lastX,lastY);
    },{passive:false});
    canvas.addEventListener('touchmove', function(e){
        e.preventDefault();
        if(!drawing) return;
        const rect = canvas.getBoundingClientRect();
        let x = e.touches[0].clientX - rect.left;
        let y = e.touches[0].clientY - rect.top;
        draw(x,y);
    },{passive:false});
    canvas.addEventListener('touchend', function(e){ drawing = false; ctx.beginPath(); },{passive:false});

    // Limpiar firma
    document.getElementById(idBtn).onclick = function(e){
        e.preventDefault();
        ctx.clearRect(0,0,canvas.width,canvas.height);
    };
}

firmaCanvas('firmaCanvas1','firmaInput1','limpiar1');
firmaCanvas('firmaCanvas2','firmaInput2','limpiar2');

document.getElementById('embalaje-form').addEventListener('submit', function(){
    document.getElementById('firmaInput1').value = document.getElementById('firmaCanvas1').toDataURL('image/png');
    document.getElementById('firmaInput2').value = document.getElementById('firmaCanvas2').toDataURL('image/png');
});
</script>
@endsection
