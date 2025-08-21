@extends('layouts.app')

@section('content')
<div class="container">
  <h3>Nuevo préstamo (paquete)</h3>

  <form id="prestamoForm" method="POST" action="{{ route('prestamos.store') }}">
    @csrf

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label class="form-label">Escanea número de serie</label>
        <input type="text" id="scannerInput" class="form-control" autocomplete="off" placeholder="Enfoca aquí y escanea">
        <div class="form-text">El escáner suele enviar Enter al final.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label">Cliente</label>
        <select name="cliente_id" class="form-select">
          <option value="">-- Opcional --</option>
          @foreach($clientes as $c)
            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Fecha préstamo</label>
        <input type="date" name="fecha_prestamo" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Devolución estimada</label>
        <input type="date" name="fecha_devolucion_estimada" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select" required>
          <option value="activo" selected>Activo</option>
          <option value="devuelto">Devuelto</option>
          <option value="retrasado">Retrasado</option>
          <option value="cancelado">Cancelado</option>
          <option value="vendido">Vendido</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="2"></textarea>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Equipo(s) en el paquete</strong>
        <span class="badge bg-primary" id="countBadge">0</span>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0" id="tablaScans">
          <thead>
            <tr>
              <th>#</th>
              <th>Número de serie</th>
              <th>Subtipo</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    {{-- contenedor donde enviaré el array --}}
    <div id="serialesContainer"></div>

    <button class="btn btn-success">Guardar préstamo</button>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const $input = $('#scannerInput');
  const $tbody = $('#tablaScans tbody');
  const $count = $('#countBadge');
  const $serialesContainer = $('#serialesContainer');

  const scanned = new Map(); // key: numero_serie -> registro

  // mantener foco para escanear rápido
  $(document).on('click keydown', ()=> $input.focus());
  $input.focus();

  function beep(ok=true){
    // sonidos cortos opcionales
    // new Audio(ok ? '/sounds/ok.mp3' : '/sounds/error.mp3').play();
  }

  function render(){
    $tbody.empty();
    let i=1;
    scanned.forEach((reg, serie)=>{
      const tr = $(`
        <tr>
          <td>${i++}</td>
          <td>${serie}</td>
          <td>${reg.subtipo_equipo ?? '-'}</td>
          <td>${reg.marca ?? '-'}</td>
          <td>${reg.modelo ?? '-'}</td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" data-serie="${serie}">Quitar</button>
          </td>
        </tr>
      `);
      $tbody.append(tr);
    });
    $count.text(scanned.size);

    // actualizar inputs ocultos para el submit
    $serialesContainer.empty();
    Array.from(scanned.keys()).forEach(s=>{
      $serialesContainer.append(`<input type="hidden" name="seriales[]" value="${$('<div/>').text(s).html()}">`);
    });
  }

  // quitar
  $tbody.on('click','button[data-serie]', function(){
    const serie = $(this).data('serie');
    scanned.delete(serie);
    render();
    $input.focus();
  });

  // escaneo (Enter)
  $input.on('keydown', function(e){
    if(e.key === 'Enter'){
      e.preventDefault();
      const serie = $input.val().trim();
      if(!serie) return;

      if(scanned.has(serie)){
        beep(false);
        $input.val('').focus();
        return;
      }

      $.ajax({
        method: 'POST',
        url: '{{ route('registros.lookup') }}',
        data: { numero_serie: serie, _token: '{{ csrf_token() }}' },
        success: function(res){
          scanned.set(serie, res.registro);
          render();
          beep(true);
          $input.val('').focus();
        },
        error: function(xhr){
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error';
          alert(`No agregado (${serie}): ${msg}`);
          beep(false);
          $input.val('').focus();
        }
      });
    }
  });
})();
</script>
@endpush
