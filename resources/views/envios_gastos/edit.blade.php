@extends('layouts.app')
@section('title','Editar Envío')
@section('titulo','Editar Envío')

@section('content')
<style>
  :root{
    --bg:#f3f8ff;
    --surface:#ffffff;
    --ink:#0f3b67;
    --muted:#5e7085;
    --stroke:#d9e8ff;
    --ring:#77b6ff;
    --brand:#cfe7ff;
    --brand-ink:#0b4a8f;
    --radius:16px;
    --shadow:0 10px 28px rgba(12, 44, 94, .09);
    --control-h: 64px;
    --control-fs: 18px;
  }

  body{ background:var(--bg); }
  .page{ max-width:1100px; margin:20px auto; padding:0 14px; }

  .hero{
    background:linear-gradient(135deg, rgba(207,231,255,.85), rgba(183,218,255,.65));
    border:1px solid var(--stroke); border-radius:var(--radius); box-shadow:var(--shadow);
    padding:18px; margin:14px 0 12px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap;
  }
  .hero h2{ margin:0; color:var(--ink); font-weight:800; font-size:clamp(18px,2.2vw,22px); }
  .hero p{ margin:0; color:var(--muted); font-size:13px; }

  .btnx{
    appearance:none; border:1px solid var(--stroke); border-radius:12px; padding:12px 16px;
    background:var(--brand); color:var(--brand-ink); font-weight:800; cursor:pointer;
    box-shadow:var(--shadow); text-decoration:none; display:inline-flex; align-items:center; gap:8px;
    transition:transform .06s ease, filter .2s ease;
  }
  .btnx:active{ transform:translateY(1px); }
  .btnx.ghost{ background:#fff; color:var(--ink); }
  .btnx.danger{ background:#fff1f2; color:#9f1239; border-color:#fecdd3; }

  .grid{ display:grid; gap:14px; grid-template-columns: 2fr 1fr; }
  @media (max-width: 980px){ .grid{ grid-template-columns: 1fr; } }

  .cardx{ background:var(--surface); border:1px solid var(--stroke); border-radius:var(--radius); box-shadow:var(--shadow); }
  .section{ padding:16px 16px 12px; border-bottom:1px solid var(--stroke); }
  .section:last-child{ border-bottom:0; }
  .section h3{ margin:0 0 6px; color:var(--ink); font-size:16px; font-weight:800; }
  .section p{ margin:0 0 12px; color:var(--muted); font-size:13px; }

  .fields{ display:grid; gap:14px; grid-template-columns: repeat(12,1fr); }
  .col-12{ grid-column: span 12; }
  .col-6{ grid-column: span 6; }
  .col-3{ grid-column: span 3; }

  @media (max-width: 1024px){ .col-6{ grid-column:span 12; } }
  @media (max-width: 860px){ .col-3{ grid-column:span 6; } }
  @media (max-width: 520px){ .col-3{ grid-column:span 12; } }

  .form-label{ display:block; font-size:14px; color:var(--ink); font-weight:800; margin:0 0 6px; }
  .control{
    width:100%; background:#fff; color:var(--ink) !important;
    border:1px solid var(--stroke); border-radius:14px;
    height:var(--control-h); padding:16px 16px;
    font-size:var(--control-fs); line-height:1.25; outline:none;
    -webkit-appearance:none; appearance:none;
    transition: box-shadow .2s, border-color .2s, background .2s;
    font-variant-numeric: tabular-nums;
  }
  .control::placeholder{ color:#7b93ac; }
  textarea.control{ height:auto; min-height:140px; resize:vertical; font-size:var(--control-fs); }

  select.control{
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24'%3E%3Cpath fill='%230b4a8f' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 12px center; background-size:18px;
    padding-right:44px;
  }
  .control:focus{ border-color:var(--ring); box-shadow:0 0 0 4px rgba(119,182,255,.20); }

  .aside{ position:sticky; top:10px; height:fit-content; }
  .kpis{ display:grid; gap:10px; grid-template-columns: 1fr 1fr; }
  .kpi{ background:#eef6ff; border:1px dashed var(--stroke); border-radius:12px; padding:12px; text-align:center; color:var(--ink); }
  .kpi small{ display:block; color:var(--muted); margin-bottom:4px; }

  .savebar{ display:none; position:fixed; left:0; right:0; bottom:10px; z-index:50; padding:8px 12px; }
  .savebar .inner{ max-width:640px; margin:0 auto; background:#fff; border:1px solid var(--stroke);
                   border-radius:14px; box-shadow:var(--shadow); padding:8px; display:flex; align-items:center; justify-content:space-between; gap:8px; }
  @media (max-width: 980px){ .savebar{ display:block; } }

  .alertx{ background:#fff1f2; color:#9f1239; border:1px solid #fecdd3; padding:10px 12px; border-radius:12px; font-size:14px; }
  .badge-id{ background:#eef6ff; color:var(--muted); border:1px solid var(--stroke); border-radius:8px; padding:4px 10px; font-size:13px; }

  .control, .control option{ color:var(--ink) !important; background-color:#fff !important; }
  input[type="date"].control{ color-scheme:light; color:var(--ink) !important; }
  input[type="date"].control::-webkit-datetime-edit{ color:var(--ink) !important; }
  .control:-webkit-autofill,
  .control:-webkit-autofill:hover,
  .control:-webkit-autofill:focus{
    -webkit-text-fill-color:var(--ink) !important;
    box-shadow:0 0 0px 1000px #fff inset !important;
    transition:background-color 5000s ease-in-out 0s !important;
  }

  .dims-block .dims-grid{
    display:grid !important;
    gap:16px !important;
    grid-template-columns: repeat(4, minmax(200px, 1fr)) !important;
    align-items:end;
  }
  .dims-block .dims-grid .control{
    width:100% !important; display:block !important;
    height:84px !important; padding:18px 18px !important;
    font-size:22px !important; border-radius:18px !important;
  }
  .dims-block .dims-grid .form-label{ font-size:15px !important; }

  @media (max-width: 980px){
    .dims-block .dims-grid{ grid-template-columns: repeat(2, minmax(200px, 1fr)) !important; }
  }
  @media (max-width: 520px){
    .dims-block .dims-grid{ grid-template-columns: 1fr !important; }
    .dims-block .dims-grid .control{ height:88px !important; font-size:23px !important; }
  }
</style>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="page" x-data="editEnvio()">

  {{-- HERO --}}
  <div class="hero">
    <div>
      <h2>Editar gasto de envío</h2>
      <p>
        Modifica los datos del registro.
        <span class="badge-id">ID #{{ $envio->id }}</span>
      </p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap">
      <a href="{{ route('envios-gastos.index') }}" class="btnx ghost">Regresar</a>

      {{-- Botón eliminar con confirmación --}}
      <form method="POST" action="{{ route('envios-gastos.destroy', $envio->id) }}"
            onsubmit="return confirm('¿Eliminar este registro? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btnx danger">Eliminar</button>
      </form>
    </div>
  </div>

  <form id="mainForm" class="grid" method="POST"
        action="{{ route('envios-gastos.update', $envio->id) }}"
        @submit="onSubmit">
    @csrf
    @method('PUT')

    {{-- MAIN --}}
    <div class="cardx">

      {{-- Datos generales --}}
      <div class="section">
        <h3>Datos generales</h3>
        <p>Información base del envío.</p>

        <div class="fields">
          <div class="col-12">
            <label class="form-label">Referencia</label>
            <input type="text" name="referencia" x-model="f.referencia"
                   class="control" placeholder="ERBE / descripción corta"
                   autocomplete="off" spellcheck="false">
          </div>

          <div class="col-6">
            <label class="form-label">Sucursal</label>
            <select name="sucursal" x-model="f.sucursal" class="control" required>
              <option value="Reynosa">Reynosa</option>
              <option value="CDMX">CDMX</option>
              <option value="Toluca">Toluca</option>
              <option value="Monterrey">Monterrey</option>
              <option value="Guadalajara">Guadalajara</option>
              <option value="Mérida">Mérida</option>
            </select>
          </div>

          <div class="col-6">
            <label class="form-label">Fecha de envío</label>
            <input type="date" name="fecha_envio" x-model="f.fecha_envio"
                   class="control" required>
          </div>

          <div class="col-12">
            <label class="form-label">Destino (opcional)</label>
            <input type="text" name="destino" x-model="f.destino"
                   class="control" placeholder="Ciudad, Estado"
                   autocomplete="off" spellcheck="false">
          </div>

          <div class="col-12">
            <label class="form-label">Transportista (opcional)</label>
            <input type="text" name="transportista" x-model="f.transportista"
                   class="control" placeholder="DHL / Estafeta / FedEx"
                   autocomplete="off" spellcheck="false">
          </div>
        </div>
      </div>

      {{-- Dimensiones y peso --}}
      <div class="section dims-block">
        <h3>Dimensiones y peso (opcionales)</h3>
        <p>Si no capturas dimensiones, se usará solo el peso real. Divisor volumétrico: <strong>5000</strong> cm³/kg.</p>

        <div class="dims-grid">
          <div>
            <label class="form-label">Alto (cm)</label>
            <input type="number" step="0.1" min="0" name="alto_cm"
                   x-model.number="f.alto_cm" @input="recalc()"
                   class="control" inputmode="decimal" placeholder="0.0">
          </div>
          <div>
            <label class="form-label">Largo (cm)</label>
            <input type="number" step="0.1" min="0" name="largo_cm"
                   x-model.number="f.largo_cm" @input="recalc()"
                   class="control" inputmode="decimal" placeholder="0.0">
          </div>
          <div>
            <label class="form-label">Ancho (cm)</label>
            <input type="number" step="0.1" min="0" name="ancho_cm"
                   x-model.number="f.ancho_cm" @input="recalc()"
                   class="control" inputmode="decimal" placeholder="0.0">
          </div>
          <div>
            <label class="form-label">Peso real (kg)</label>
            <input type="number" step="0.01" min="0" name="peso_kg"
                   x-model.number="f.peso_kg" @input="recalc()"
                   class="control" inputmode="decimal" placeholder="0.00">
          </div>
        </div>
      </div>

      {{-- Costo --}}
      <div class="section">
        <h3>Costo</h3>
        <p>Registra el costo total del envío (lo que pagaste).</p>

        <div class="fields">
          <div class="col-6">
            <label class="form-label">Costo MXN</label>
            <input type="number" step="0.01" min="0" name="costo_mxn"
                   x-model.number="f.costo_mxn" class="control" required inputmode="decimal">
          </div>
          <div class="col-12">
            <label class="form-label">Notas</label>
            <textarea name="notas" x-model="f.notas" class="control"
                      placeholder="Observaciones, guía, incidencias, etc."></textarea>
          </div>
        </div>
      </div>

      {{-- Hidden calc --}}
      <input type="hidden" name="peso_volumetrico_kg" :value="f.peso_vol">
      <input type="hidden" name="peso_facturable_kg"  :value="f.peso_fact">

      @if ($errors->any())
        <div class="section">
          <div class="alertx"><strong>Corrige:</strong> {{ implode(', ', $errors->all()) }}</div>
        </div>
      @endif

      @if (session('ok'))
        <div class="section">
          <div class="alertx" style="background:#c7f9cc; color:#166534; border-color:#dcfce7">
            {{ session('ok') }}
          </div>
        </div>
      @endif

      {{-- Acciones --}}
      <div class="section" style="display:flex; gap:8px; justify-content:flex-end">
        <a href="{{ route('envios-gastos.index') }}" class="btnx ghost">Cancelar</a>
        <button class="btnx" type="submit">Guardar cambios</button>
      </div>
    </div>

    {{-- ASIDE --}}
    <div class="cardx aside" style="padding:16px">
      <h3 style="margin:0 0 10px; color:var(--ink)">Resumen</h3>
      <div class="kpis">
        <div class="kpi"><small>Volumétrico</small><strong x-text="fmtKg(f.peso_vol)"></strong></div>
        <div class="kpi"><small>Facturable</small><strong x-text="fmtKg(f.peso_fact)"></strong></div>
        <div class="kpi"><small>Dimensiones</small><strong x-text="dimsOk ? 'Usadas' : '—'"></strong></div>
        <div class="kpi"><small>Costo</small><strong x-text="money(f.costo_mxn)"></strong></div>
      </div>

      {{-- Meta del registro --}}
      <div style="margin-top:14px; display:grid; gap:8px">
        <div class="kpi" style="text-align:left">
          <small>Creado</small>
          <strong>{{ optional($envio->created_at)->format('d/m/Y H:i') ?? '—' }}</strong>
        </div>
        <div class="kpi" style="text-align:left">
          <small>Última edición</small>
          <strong>{{ optional($envio->updated_at)->format('d/m/Y H:i') ?? '—' }}</strong>
        </div>
      </div>

      <div style="margin-top:10px; color:var(--muted); font-size:13px">
        Tip: los campos de dimensiones son opcionales. Si los dejas vacíos, el peso facturable será igual al peso real.
      </div>
    </div>
  </form>
</div>

{{-- Savebar móvil --}}
<div class="savebar" aria-hidden="true">
  <div class="inner">
    <div style="font-size:13px; color:var(--muted)">
      <strong x-text="money(f.costo_mxn)"></strong> • Facturable <strong x-text="fmtKg(f.peso_fact)"></strong>
    </div>
    <div style="display:flex; gap:8px">
      <a href="{{ route('envios-gastos.index') }}" class="btnx ghost">Cancelar</a>
      <button class="btnx" type="button"
              onclick="document.getElementById('mainForm').requestSubmit()">Guardar</button>
    </div>
  </div>
</div>

<script>
function editEnvio(){
  return {
    divisor: 5000,
    f: {
      referencia:    '{{ old('referencia',   $envio->referencia) }}',
      sucursal:      '{{ old('sucursal',     $envio->sucursal) }}',
      fecha_envio:   '{{ old('fecha_envio',  optional($envio->fecha_envio)->format('Y-m-d')) }}',
      destino:       '{{ old('destino',      $envio->destino) }}',
      transportista: '{{ old('transportista',$envio->transportista) }}',
      alto_cm:       {{ old('alto_cm',   $envio->alto_cm   ?? 'null') }},
      largo_cm:      {{ old('largo_cm',  $envio->largo_cm  ?? 'null') }},
      ancho_cm:      {{ old('ancho_cm',  $envio->ancho_cm  ?? 'null') }},
      peso_kg:       {{ old('peso_kg',   $envio->peso_kg   ?? 'null') }},
      peso_vol:      {{ $envio->peso_volumetrico_kg ?? 'null' }},
      peso_fact:     {{ $envio->peso_facturable_kg  ?? 'null' }},
      costo_mxn:     {{ old('costo_mxn', $envio->costo_mxn ?? 0) }},
      notas:         '{{ old('notas', addslashes($envio->notas ?? '')) }}',
    },

    get dimsOk(){
      return [this.f.alto_cm, this.f.largo_cm, this.f.ancho_cm].every(v => v && Number(v) > 0);
    },
    fmtKg(v){ return (v ? Number(v).toFixed(2) : '0.00') + ' kg'; },
    money(v){ return Number(v||0).toLocaleString('es-MX',{style:'currency',currency:'MXN'}); },

    recalc(){
      const a=Number(this.f.alto_cm||0), l=Number(this.f.largo_cm||0),
            an=Number(this.f.ancho_cm||0), r=Number(this.f.peso_kg||0);
      const vol = (a>0 && l>0 && an>0) ? (a*l*an)/this.divisor : 0;
      this.f.peso_vol  = (a>0 && l>0 && an>0) ? Number(vol.toFixed(2)) : null;
      this.f.peso_fact = (r>0 || vol>0) ? Number(Math.max(r, vol).toFixed(2)) : null;
    },

    onSubmit(){ }
  }
}
</script>
@endsection