@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/ingenieria.css') }}?v={{ time() }}">
<div class="wizard-step">
    <h3 class="mb-4">Ingeniería: Verifica Productos y Componentes</h3>
    <form method="POST" action="{{ route('checklists.guardarIngenieria', $venta->id) }}" enctype="multipart/form-data" id="ingenieria-form">
        @csrf
        <div class="table-responsive">
        <table class="table mb-4 animate-fadein">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Componente</th>
                    <th>Estado</th>
                    <th>Incidencia</th>
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
                    <tr class="table-row-animate">
                        <td>{{ $producto->marca }} {{ $producto->modelo }}</td>
                        <td>{{ $componente }}</td>
                        <td>
                            <select name="componentes[{{$producto->id}}][{{$componente}}][estado]" class="form-control animate-shadow" required>
                                <option value="">Selecciona</option>
                                <option value="bueno">Bueno</option>
                                <option value="funcional">Funcional</option>
                                <option value="defectuoso">Defectuoso</option>
                                <option value="no_viene">No viene</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="componentes[{{$producto->id}}][{{$componente}}][incidencia]" class="form-control animate-shadow" placeholder="Describe incidencia si aplica">
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        </div>
        
        <label class="mb-1">¿Incidente general?</label>
        <textarea name="ingenieria_incidente" class="form-control mb-3 animate-shadow"></textarea>
        
        <div class="row mb-3 firmas-row">
            <div class="col-md-6 firma-box">
                <label>Firma Responsable</label>
                <div class="firma-area">
                    <canvas id="firmaCanvas1" width="320" height="90"></canvas>
                    <button type="button" id="limpiar1" class="btn btn-sm btn-light firma-clear">Limpiar</button>
                </div>
                <input type="hidden" name="firma_responsable" id="firmaInput1">
            </div>
            <div class="col-md-6 firma-box">
                <label>Firma Supervisor</label>
                <div class="firma-area">
                    <canvas id="firmaCanvas2" width="320" height="90"></canvas>
                    <button type="button" id="limpiar2" class="btn btn-sm btn-light firma-clear">Limpiar</button>
                </div>
                <input type="hidden" name="firma_supervisor" id="firmaInput2">
            </div>
        </div>

        <label>Evidencias (foto o archivo)</label>
        <input type="file" name="evidencias[]" class="form-control mb-3 animate-shadow" multiple accept="image/*">

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary btn-lg animate-pop">Guardar y continuar</button>
        </div>
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

    canvas.addEventListener('mousedown', e => { drawing = true; [lastX,lastY]=[e.offsetX,e.offsetY]; ctx.beginPath(); ctx.moveTo(lastX,lastY); });
    canvas.addEventListener('mouseup', () => { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mouseout', () => { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mousemove', e => { draw(e.offsetX,e.offsetY); });

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

    document.getElementById(idBtn).onclick = function(e){
        e.preventDefault();
        ctx.clearRect(0,0,canvas.width,canvas.height);
    };

    document.getElementById('ingenieria-form').addEventListener('submit', function(){
        document.getElementById(idInput).value = canvas.toDataURL('image/png');
    });
}
firmaCanvas('firmaCanvas1','firmaInput1','limpiar1');
firmaCanvas('firmaCanvas2','firmaInput2','limpiar2');
</script>
@endsection
