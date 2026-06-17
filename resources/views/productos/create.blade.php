@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad;
  --mint-dark:#34c29e;
  --ink:#2a2e35;
  --muted:#7a7f87;
  --line:#e9ecef;
  --card:#ffffff;
}
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}

/* Page */
.edit-wrap{
  max-width:980px;margin:110px auto 40px;padding:0 16px;
}
.panel{
  background:var(--card);
  border-radius:16px;
  box-shadow:0 16px 40px rgba(18,38,63,.12);
  overflow:hidden;
}
.panel-head{
  padding:22px 26px;
  border-bottom:1px solid var(--line);
  display:flex;align-items:center;gap:14px;justify-content:space-between;
}
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.back-link{
  display:inline-flex;align-items:center;gap:8px;
  color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px;
  border:1px solid var(--line);background:#fff;
}
.back-link:hover{color:var(--ink);border-color:#dfe3e8}

/* Form */
.form{ padding:26px; }
.grid{ display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px; }
@media (max-width: 800px){ .grid{grid-template-columns:1fr} }

.field{
  position:relative;background:#fff;border:1px solid var(--line);
  border-radius:12px;padding:16px 14px 10px;transition:box-shadow .2s,border-color .2s;
}
.field:focus-within{border-color:#d8dee6;box-shadow:0 8px 24px rgba(18,38,63,.08)}

.field input,
.field select{
  width:100%;border:0;outline:0;background:transparent;
  font-size:15px;color:var(--ink);padding-top:10px;
  /* CORRECCIÓN DOBLE FLECHA: */
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  cursor: pointer;
  z-index: 2; /* Para que quede por encima de la flecha SVG */
  position: relative;
}

.field label{
  position:absolute;left:14px;top:14px;color:var(--muted);font-size:13px;
  transition:transform .15s ease, color .15s ease, font-size .15s ease, top .15s ease;
  pointer-events:none;
  z-index: 1;
}
.field input::placeholder{color:transparent;}
.field input:focus + label,
.field input:not(:placeholder-shown) + label{
  top:8px;transform:translateY(-10px);font-size:11px;color:var(--mint-dark);
}

/* Label flotante para select usando .has-value */
.field.has-value label,
.field:focus-within label{
  top:8px;transform:translateY(-10px);font-size:11px;color:var(--mint-dark);
}

/* caret */
.field .caret{
  position:absolute;right:14px;top:50%;transform:translateY(-10%);
  color:#a2a7ae;pointer-events:none;
  z-index: 0;
}

/* Price adornment */
.field .prefix{
  position:absolute;right:14px;top:50%;transform:translateY(-10%);
  color:#a2a7ae;font-size:13px;
}

/* Dropzone / Image */
.block{
  border:1px dashed #dfe3e8;border-radius:14px;padding:16px;background:#fafbfc;
}
.uploader{
  display:grid;grid-template-columns:140px 1fr;gap:16px;align-items:center;
}
@media (max-width: 600px){ .uploader{grid-template-columns:1fr} }
.thumb{
  width:140px;height:140px;border-radius:12px;overflow:hidden;background:#f0f2f5;
  display:grid;place-items:center;border:1px solid #edf0f3;
}
.thumb img{width:100%;height:100%;object-fit:cover}
.drop{ display:flex;align-items:center;gap:14px;flex-wrap:wrap; }
.input-file{display:none}
.drop .btn{
  background:var(--mint);color:#fff;border:none;border-radius:999px;
  padding:10px 16px;cursor:pointer;box-shadow:0 10px 20px rgba(72,207,173,.25);
}
.drop .btn:hover{background:var(--mint-dark)}
.small{color:var(--muted);font-size:12px}

/* Actions */
.actions{
  display:flex;gap:12px;justify-content:flex-end;margin-top:10px;padding:0 26px 26px;
}
.btn{
  border:0;border-radius:12px;padding:12px 18px;font-weight:700;cursor:pointer;
  transition:transform .05s ease, box-shadow .2s ease, background .2s ease,color .2s ease;
}
.btn:active{transform:translateY(1px)}
.btn-primary{
  background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26);
}
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{
  background:#fff;color:var(--ink);border:1px solid var(--line);
}
.btn-ghost:hover{border-color:#dfe3e8}

/* Error styles (Laravel) */
.is-invalid{border-color:#f9c0c0 !important}
.error{color:#cc4b4b;font-size:12px;margin-top:6px}
</style>

@php
  // Pre-llenado de datos si existen (Old inputs o Edición)
  $oldTipo    = old('tipo_equipo',    $producto->tipo_equipo    ?? '');
  $oldSubtipo = old('subtipo_equipo', $producto->subtipo_equipo ?? '');
  $oldMarca   = old('marca',          $producto->marca          ?? '');
  $oldModelo  = old('modelo',         $producto->modelo         ?? '');
@endphp

<div class="edit-wrap">
  <div class="panel">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Agregar producto</h2>
        <p>Crea un nuevo producto y sube su imagen.</p>
      </div>
      <a href="{{ route('productos.cards') }}" class="back-link" title="Volver">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Volver
      </a>
    </div>

    <form class="form" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="grid">
        {{-- Tipo de equipo --}}
        <div>
          <div class="field @error('tipo_equipo') is-invalid @enderror" id="wrap-tipo">
            <select name="tipo_equipo" id="f-tipo" required>
              <option value="" selected disabled hidden></option>
            </select>
            <label for="f-tipo">Categoria</label>
            <svg class="caret" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          @error('tipo_equipo')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Subtipo --}}
        <div>
          <div class="field @error('subtipo_equipo') is-invalid @enderror" id="wrap-subtipo">
            <select name="subtipo_equipo" id="f-subtipo" required disabled>
              <option value="" selected disabled hidden></option>
            </select>
            <label for="f-subtipo">Subtipo</label>
            <svg class="caret" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          @error('subtipo_equipo')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Marca --}}
        <div>
          <div class="field @error('marca') is-invalid @enderror" id="wrap-marca">
            <select name="marca" id="f-marca" required disabled>
              <option value="" selected disabled hidden></option>
            </select>
            <label for="f-marca">Marca</label>
            <svg class="caret" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          @error('marca')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Modelo --}}
        <div>
          <div class="field @error('modelo') is-invalid @enderror" id="wrap-modelo">
            <select name="modelo" id="f-modelo" required disabled>
              <option value="" selected disabled hidden></option>
            </select>
            <label for="f-modelo">Modelo</label>
            <svg class="caret" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          @error('modelo')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Precio --}}
        <div>
          <div class="field @error('precio') is-invalid @enderror">
            <input type="number" step="0.01" name="precio" id="f-precio" value="{{ old('precio') }}" placeholder=" " required>
            <label for="f-precio">Precio</label>
            <span class="prefix">$ MXN</span>
          </div>
          @error('precio')<div class="error">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Imagen --}}
      <div class="block" style="margin-top:22px;">
        <div class="uploader">
          <div class="thumb">
            <img id="preview" src="https://via.placeholder.com/280x280.png?text=Sin+imagen" alt="Vista previa">
          </div>
          <div class="drop">
            <label class="btn" for="imagen">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;">
                <path d="M12 5v14M5 12h14"/>
              </svg>
              Subir imagen
            </label>
            <input id="imagen" class="input-file" type="file" name="imagen" accept="image/*">
            <span class="small">Formatos: JPG/PNG. Máx 2MB.</span>
          </div>
        </div>
        @error('imagen')<div class="error" style="margin-top:8px;">{{ $message }}</div>@enderror
      </div>

      <div class="actions">
        <a href="{{ route('productos.cards') }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
/* ==================== Preview imagen dinámica ==================== */
document.getElementById('imagen')?.addEventListener('change', function(e){
  const file = e.target.files && e.target.files[0];
  if(!file) return;
  const ok = /^image\//.test(file.type);
  if(!ok) { alert('Selecciona una imagen válida.'); this.value=''; return; }
  const max = 2 * 1024 * 1024;
  if(file.size > max){ alert('La imagen supera 2MB.'); this.value=''; return; }
  const reader = new FileReader();
  reader.onload = ev => document.getElementById('preview').src = ev.target.result;
  reader.readAsDataURL(file);
});

/* ==================== Formatear precio a 2 decimales ==================== */
const precio = document.getElementById('f-precio');
if(precio){
  precio.addEventListener('blur', ()=> {
    if(precio.value !== '') {
      const n = Number(precio.value);
      if(!isNaN(n)) precio.value = n.toFixed(2);
    }
  });
}

/* ==================== LOGICA: Tipo → Subtipo → Marca → Modelo ==================== */
const OLD = {
  tipo:    @json($oldTipo),
  subtipo: @json($oldSubtipo),
  marca:   @json($oldMarca),
  modelo:  @json($oldModelo),
};

@include('partials.catalogo-equipos-data')

const $tipo    = document.getElementById('f-tipo');
const $subtipo = document.getElementById('f-subtipo');
const $marca   = document.getElementById('f-marca');
const $modelo  = document.getElementById('f-modelo');

const wrapTipo    = document.getElementById('wrap-tipo');
const wrapSubtipo = document.getElementById('wrap-subtipo');
const wrapMarca   = document.getElementById('wrap-marca');
const wrapModelo  = document.getElementById('wrap-modelo');

function norm(s){
  return (s||'').toString().trim().toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .replace(/\s+/g,' ');
}

function setHasValue(wrap, el){
  wrap?.classList.toggle('has-value', !!(el && el.value));
}

function clearSelect(sel){
  while(sel.options.length > 1) sel.remove(1);
  sel.value = '';
}

function fillSelect(sel, arr){
  (arr||[]).forEach(v => {
    const opt = document.createElement('option');
    opt.value = v;
    opt.textContent = v;
    sel.appendChild(opt);
  });
}

function pickExact(arr, wanted){
  const w = norm(wanted);
  if(!w) return '';
  const found = (arr||[]).find(x => norm(x) === w);
  return found || '';
}

function getMarcas(tipo, subtipo){
  const tipoSlug = norm(tipo);
  const subSlug  = norm(subtipo);

  const tipoNode = marcasModelosPorSubtipo[tipoSlug];
  if(!tipoNode) return [];

  let node = null;
  for (const k in tipoNode){
    if (norm(k) === subSlug){ node = tipoNode[k]; break; }
  }
  if(!node) return [];
  return Object.keys(node);
}

function getModelos(tipo, subtipo, marca){
  const tipoSlug = norm(tipo);
  const subSlug  = norm(subtipo);

  const tipoNode = marcasModelosPorSubtipo[tipoSlug];
  if(!tipoNode) return [];

  let node = null;
  for (const k in tipoNode){
    if (norm(k) === subSlug){ node = tipoNode[k]; break; }
  }
  if(!node) return [];
  const arr = node[marca] || [];
  return Array.isArray(arr) ? arr : [];
}

function onTipoChange(){
  clearSelect($subtipo);
  clearSelect($marca);
  clearSelect($modelo);

  const tipo = $tipo.value;
  const subs = tipo ? (tiposEquipos[tipo] || []) : [];

  fillSelect($subtipo, subs);
  $subtipo.disabled = !tipo;

  $marca.disabled  = true;
  $modelo.disabled = true;

  setHasValue(wrapTipo, $tipo);
  setHasValue(wrapSubtipo, $subtipo);
  setHasValue(wrapMarca, $marca);
  setHasValue(wrapModelo, $modelo);
}

function onSubtipoChange(){
  clearSelect($marca);
  clearSelect($modelo);

  const tipo = $tipo.value;
  const subtipo = $subtipo.value;

  const marcas = (tipo && subtipo) ? getMarcas(tipo, subtipo) : [];
  fillSelect($marca, marcas);

  $marca.disabled  = !(tipo && subtipo && marcas.length);
  $modelo.disabled = true;

  setHasValue(wrapSubtipo, $subtipo);
  setHasValue(wrapMarca, $marca);
  setHasValue(wrapModelo, $modelo);
}

function onMarcaChange(){
  clearSelect($modelo);

  const tipo = $tipo.value;
  const subtipo = $subtipo.value;
  const marca = $marca.value;

  const modelos = (tipo && subtipo && marca) ? getModelos(tipo, subtipo, marca) : [];
  fillSelect($modelo, modelos);

  $modelo.disabled = !(marca && modelos.length);

  setHasValue(wrapMarca, $marca);
  setHasValue(wrapModelo, $modelo);
}

document.addEventListener('DOMContentLoaded', () => {
  // llenar TIPOS
  fillSelect($tipo, Object.keys(tiposEquipos));

  // bind
  $tipo.addEventListener('change', onTipoChange);
  $subtipo.addEventListener('change', onSubtipoChange);
  $marca.addEventListener('change', onMarcaChange);
  $modelo.addEventListener('change', ()=> setHasValue(wrapModelo, $modelo));

  // precargar EDIT/OLD
  const tipoPick = pickExact(Object.keys(tiposEquipos), OLD.tipo);
  if(tipoPick){
    $tipo.value = tipoPick;
    onTipoChange();

    const subPick = pickExact(tiposEquipos[tipoPick] || [], OLD.subtipo);
    if(subPick){
      $subtipo.value = subPick;
      onSubtipoChange();

      const marcas = getMarcas(tipoPick, subPick);
      const marcaPick = pickExact(marcas, OLD.marca);
      if(marcaPick){
        $marca.value = marcaPick;
        onMarcaChange();

        const modelos = getModelos(tipoPick, subPick, marcaPick);
        const modeloPick = pickExact(modelos, OLD.modelo);
        if(modeloPick){
          $modelo.value = modeloPick;
        }
      }
    }
  }

  // aplicar has-value inicial
  setHasValue(wrapTipo, $tipo);
  setHasValue(wrapSubtipo, $subtipo);
  setHasValue(wrapMarca, $marca);
  setHasValue(wrapModelo, $modelo);
});
</script>
@endsection