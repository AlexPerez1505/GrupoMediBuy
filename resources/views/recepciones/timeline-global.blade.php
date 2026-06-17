@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/timeline.css') }}">
<style>
@media (max-width: 768px) {
  .timeline {
    position: relative;
    padding-left: 40px;
  }

  .timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 20px;
    width: 3px;
    height: 100%;
    background-color: #d0d0d0;
    z-index: 0;
  }

  .timeline__event {
    flex-direction: column;
    width: 90vw;
    margin: 30px auto;
    position: relative;
    padding-left: 40px;
    background: transparent;
  }

  .timeline__event__icon {
    position: absolute;
    left: 3px;
    top: 0;
    width: 36px;
    height: 36px;
    background: #f6a4ec;
    color: #9251ac;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    box-shadow: 0 0 0 3px #fff;
    transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
  }

  .timeline__event__icon:hover {
    transform: scale(1.2) rotate(5deg);
    background-color: #fff;
    color: #000;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  }

  .timeline__event__content {
    width: 100%;
    border-radius: 6px;
    background: #fff;
    padding: 16px;
    margin-left: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }

  .timeline__event__date {
    font-size: 1rem;
    padding: 8px;
    background: #9251ac;
    color: #f6a4ec;
    border-radius: 6px;
    margin-bottom: 10px;
  }

  .timeline__event__title {
    font-size: 1rem;
    font-weight: bold;
    color: #9251ac;
  }
}
.container{
    margin-top: 30px !important;
    padding-left: 40px !important;
    padding-right: 40px !important;
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(166, 214, 242, 0.25);
}

 body {
    background-color: #ddeef8 !important; /* azul claro como en la imagen */
  }
  h2{
    margin-top: 20px;
  }
  /* Grid de tarjetas */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
  margin-bottom: 32px;
}

/* Etiqueta de sección */
.sec-label {
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: .05em;
  margin: 24px 0 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Tarjeta base */
.tl-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  border: 1px solid #e8eef5;
  box-shadow: 0 4px 16px rgba(17,24,39,.05);
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: transform .15s ease, box-shadow .15s ease;
}
.tl-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(17,24,39,.09);
}

.tl-card__head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.tl-card__badge {
  font-size: 11px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.tl-card__badge--recepcion { background: #dbeafe; color: #1d4ed8; }
.tl-card__badge--parcial   { background: #fef9c3; color: #92400e; }
.tl-card__badge--pendiente { background: #f3e8ff; color: #6d28d9; }

.tl-card__date {
  font-size: 11px;
  color: #94a3b8;
}

.tl-card__title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.4;
}

.tl-card__sub {
  font-size: 12px;
  color: #64748b;
  margin: 0;
}

.tl-card__obs {
  font-size: 12px;
  color: #475569;
  margin: 0;
  background: #f8fafc;
  border-radius: 8px;
  padding: 8px 10px;
  border-left: 3px solid #bfdbfe;
}

.tl-card__list {
  margin: 0;
  padding-left: 16px;
  font-size: 12px;
  color: #334155;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.tl-card__note {
  color: #94a3b8;
  font-size: 11px;
}

/* Stats (parcial / pendiente) */
.tl-card__stats {
  display: flex;
  gap: 12px;
  margin-top: 4px;
}
.tl-card__stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #f8fafc;
  border-radius: 10px;
  padding: 8px 14px;
  flex: 1;
  font-size: 11px;
  color: #64748b;
  gap: 2px;
}
.tl-card__stat strong {
  font-size: 16px;
  color: #0f172a;
}

/* Borde de color lateral por tipo */
.tl-card--recepcion { border-left: 4px solid #60a5fa; }
.tl-card--parcial   { border-left: 4px solid #fbbf24; }
.tl-card--pendiente { border-left: 4px solid #a78bfa; }

/* Chips de filtro */
.chipset-filter { display: flex; gap: 6px; flex-wrap: wrap; }
.fchip {
  padding: 6px 14px;
  border: 1px solid #e8eef5;
  border-radius: 999px;
  background: #fff;
  font-size: 12px;
  color: #334155;
  cursor: pointer;
  transition: transform .12s ease, box-shadow .12s ease;
}
.fchip:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,.06); }
.fchip.is-active { background: #a6d6f2; border-color: transparent; color: #07364a; font-weight: 600; }

</style>
<div class="container" style="
  position: sticky; top: 70px; z-index: 100;
  border-bottom-left-radius: 0; border-bottom-right-radius: 0;
  border-bottom: 1px solid #e8eef5;
  box-shadow: 0 6px 16px rgba(17,24,39,0.08);
">
  <h2>Historial Global de Recepciones</h2>

  <div class="d-flex align-items-center gap-3 flex-wrap mb-4">

    {{-- Exportar PDF --}}
    <form action="{{ route('recepciones.timeline.pdf') }}" method="GET"
          class="d-flex align-items-center gap-2">
      <label for="pedido_id">Filtrar por pedido:</label>
      <select name="pedido_id" id="pedido_id" class="form-control" style="width:200px;">
        <option value="">Todos</option>
        @foreach ($pedidosDisponibles as $pedido)
          <option value="{{ $pedido->id }}">{{ $pedido->id }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Exportar PDF
      </button>
    </form>

    {{-- Separador visual --}}
    <div style="width:1px;height:32px;background:#e8eef5;"></div>

    {{-- Filtro tipo --}}
    <div class="d-flex align-items-center gap-2">
      <label class="mb-0" style="font-size:13px;color:#64748b;white-space:nowrap;">
        <i class="fas fa-filter"></i> Tipo:
      </label>
      <div class="chipset-filter" id="chipTipo">
        <button class="fchip is-active" data-v="todos">Todos</button>
        <button class="fchip" data-v="recepcion">Recepciones</button>
        <button class="fchip" data-v="parcial">Parciales</button>
        <button class="fchip" data-v="pendiente">Pendientes</button>
      </div>
    </div>

    {{-- Separador visual --}}
    <div style="width:1px;height:32px;background:#e8eef5;"></div>

    {{-- Filtro orden --}}
    <div class="d-flex align-items-center gap-2">
      <label class="mb-0" style="font-size:13px;color:#64748b;white-space:nowrap;">
        <i class="fas fa-sort"></i> Orden:
      </label>
      <select id="selOrden" class="form-control" style="width:180px;font-size:13px;">
        <option value="recientes">Más recientes primero</option>
        <option value="antiguos">Más antiguos primero</option>
      </select>
    </div>

  </div>
</div>
<div class="container" style="margin-top: 16px;">
  @if (empty($recepciones) && empty($componentesPendientes) && empty($componentesParciales))
    <p class="mensaje-vacio">No hay recepciones ni componentes registrados.</p>
  @else

    {{-- Recepciones realizadas --}}
    @if (!empty($recepciones) && count($recepciones) > 0)
    <div class="sec-recepcion"> 
      <h5 class="sec-label"><i class="fas fa-box-open"></i> Recepciones realizadas</h5>
      <div class="cards-grid">
        @foreach ($recepciones as $recepcion)
          <div class="tl-card tl-card--recepcion" data-fecha="{{ \Carbon\Carbon::parse($recepcion->fecha)->toIso8601String() }}">
            <div class="tl-card__head">
              <span class="tl-card__badge tl-card__badge--recepcion">
                <i class="fas fa-box-open"></i> Recepción
              </span>
              <span class="tl-card__date">
                {{ \Carbon\Carbon::parse($recepcion->fecha)->format('d M Y') }}
              </span>
            </div>
            <div class="tl-card__title">
              Recepción #{{ $recepcion->id }}
              @if($recepcion->pedido) — Pedido #{{ $recepcion->pedido->id }} @endif
            </div>
            <p class="tl-card__sub"><i class="fas fa-user"></i> {{ $recepcion->recibido_por }}</p>
            <p class="tl-card__obs">{{ $recepcion->observaciones ?? 'Sin observaciones.' }}</p>
            <ul class="tl-card__list">
              @foreach ($recepcion->componentes as $componente)
                <li>
                  <strong>{{ $componente->nombre_componente }}</strong> x{{ $componente->cantidad_recibida }}<br>
                  <small>Equipo: {{ $componente->nombre_equipo }}</small>
                  @if($componente->observaciones)
                    <br><em class="tl-card__note">{{ $componente->observaciones }}</em>
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Componentes parciales --}}
    @if (!empty($componentesParciales) && count($componentesParciales) > 0)
    <div class="sec-parcial">
      <h5 class="sec-label"><i class="fas fa-exclamation-triangle"></i> Recibidos parcialmente</h5>
      <div class="cards-grid">
        @foreach ($componentesParciales as $componente)
          <div class="tl-card tl-card--parcial">
            <div class="tl-card__head">
              <span class="tl-card__badge tl-card__badge--parcial">
                <i class="fas fa-exclamation-triangle"></i> Parcial
              </span>
            </div>
            <div class="tl-card__title">{{ $componente->nombre }}</div>
            @if($componente->pedido)
              <p class="tl-card__sub">Pedido #{{ $componente->pedido->id }}</p>
            @endif
            <div class="tl-card__stats">
              <div class="tl-card__stat">
                <span>Esperada</span><strong>{{ $componente->cantidad_esperada }}</strong>
              </div>
              <div class="tl-card__stat">
                <span>Recibida</span><strong>{{ $componente->cantidad_recibida }}</strong>
              </div>
              <div class="tl-card__stat">
                <span>Equipo</span><strong>{{ $componente->equipo_id }}</strong>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Componentes pendientes --}}
    @if (!empty($componentesPendientes) && count($componentesPendientes) > 0)
    <div class="sec-pendiente">
      <h5 class="sec-label"><i class="fas fa-hourglass-half"></i> Pendientes de recepción</h5>
      <div class="cards-grid">
        @foreach ($componentesPendientes as $componente)
          <div class="tl-card tl-card--pendiente">
            <div class="tl-card__head">
              <span class="tl-card__badge tl-card__badge--pendiente">
                <i class="fas fa-hourglass-half"></i> Pendiente
              </span>
            </div>
            <div class="tl-card__title">{{ $componente->nombre }}</div>
            @if($componente->pedido)
              <p class="tl-card__sub">Pedido #{{ $componente->pedido->id }}</p>
            @endif
            <div class="tl-card__stats">
              <div class="tl-card__stat">
                <span>Esperada</span><strong>{{ $componente->cantidad_esperada }}</strong>
              </div>
              <div class="tl-card__stat">
                <span>Equipo</span><strong>{{ $componente->equipo_id }}</strong>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

  @endif
</div>
<script>
(function () {
  // Referencias a las secciones
  const secRecepcion = document.querySelector('.sec-recepcion');
  const secParcial   = document.querySelector('.sec-parcial');
  const secPendiente = document.querySelector('.sec-pendiente');

  const chips   = Array.from(document.querySelectorAll('#chipTipo .fchip'));
  const selOrden = document.getElementById('selOrden');

  // Filtro por tipo
  chips.forEach(btn => {
    btn.addEventListener('click', () => {
      chips.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      const v = btn.getAttribute('data-v');

      if (secRecepcion) secRecepcion.style.display = (v === 'todos' || v === 'recepcion') ? '' : 'none';
      if (secParcial)   secParcial.style.display   = (v === 'todos' || v === 'parcial')   ? '' : 'none';
      if (secPendiente) secPendiente.style.display = (v === 'todos' || v === 'pendiente') ? '' : 'none';
    });
  });

  // Ordenar cards por fecha dentro de cada grid
  selOrden.addEventListener('change', () => {
    document.querySelectorAll('.cards-grid').forEach(grid => {
      const cards = Array.from(grid.querySelectorAll('.tl-card'));
      cards.sort((a, b) => {
        const da = Date.parse(a.getAttribute('data-fecha') || '0');
        const db = Date.parse(b.getAttribute('data-fecha') || '0');
        return selOrden.value === 'recientes' ? db - da : da - db;
      });
      cards.forEach(c => grid.appendChild(c));
    });
  });
})();
</script>
@endsection
