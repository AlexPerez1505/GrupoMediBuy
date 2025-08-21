{{-- resources/views/promociones/whatsapp-direct.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Promocionales')
@section('content')
<style>
:root{ --p:#cfe2ff; --p-text:#2f5fb1; --bg:#f7f8fb; --card:#fff; --line:#e6ecf4; --ok:#1ECD97; }
.page{max-width:1100px;margin:22px auto;padding:0 16px}
.card{background:var(--card);border:1px solid var(--line);border-radius:16px;box-shadow:0 10px 30px rgba(20,40,80,.07);overflow:hidden}
.head{padding:18px 22px;border-bottom:1px solid var(--line);display:flex;gap:10px;align-items:center}
.head h3{margin:0;color:var(--p-text);letter-spacing:-.3px}
.body{padding:20px}
.grid{display:grid;gap:16px}
.grid-2{grid-template-columns:1fr 1fr}
@media (max-width: 900px){ .grid-2{grid-template-columns:1fr} }
label{font-size:14px;color:#374151;font-weight:600}
input,textarea,select{width:100%;padding:12px 14px;border:1px solid var(--line);border-radius:12px;background:#fff;outline:none;transition:border .2s}
input:focus,textarea:focus,select:focus{border-color:var(--p)}
.small{font-size:12px;color:#6b7280}
.badge{display:inline-block;background:#eef5ff;color:var(--p-text);border:1px solid var(--p);padding:6px 10px;border-radius:999px;font-size:12px}
.btn{border:0;border-radius:12px;padding:12px 16px;cursor:pointer}
.btn-primary{background:var(--p);color:var(--p-text);font-weight:700}
.btn-ghost{background:#fff;border:1px solid var(--line);color:#374151}
.alert{border-radius:12px;padding:12px 14px;margin-bottom:12px}
.alert-ok{background:#ecfdf5;border:1px solid #bbf7d0;color:#065f46}
.alert-warn{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412}
.tools{display:flex;gap:10px;align-items:center;margin-bottom:8px;flex-wrap:wrap}
.clients-wrap{border:1px solid var(--line);border-radius:12px;padding:10px;max-height:360px;overflow:auto;background:#fff}
.row{display:flex;gap:10px;align-items:center;border-bottom:1px dashed var(--line);padding:8px 4px}
.row:last-child{border-bottom:0}
.name{font-weight:600}

.preview{display:flex;gap:12px;align-items:flex-start;border:1px dashed var(--line);border-radius:12px;padding:12px;background:#fbfcff}
.preview .ph{width:56px;height:56px;border-radius:12px;background:#e9eefb;display:flex;align-items:center;justify-content:center;font-weight:700;color:#4763c4}
.preview .bubble{flex:1;background:#fff;border:1px solid #e9eefb;border-radius:12px;padding:12px}
.preview h4{margin:0 0 6px 0;font-size:15px;color:#0b1f66}
.preview pre{margin:0;white-space:pre-wrap;font-size:14px;color:#222;line-height:1.45}
.preview .foot{margin-top:8px;font-size:12px;color:#64748b;border-top:1px dashed #e6ecf4;padding-top:6px}

.switcher{display:flex;gap:8px;margin-top:12px}
.switcher .tab{padding:8px 12px;border:1px solid var(--line);border-radius:999px;background:#fff;cursor:pointer}
.switcher .tab.active{background:#eef5ff;border-color:var(--p);color:var(--p-text)}

.counter{font-size:12px;color:#475569}
.hint{font-size:12px;color:#6b7280}

.inline{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.kpi{display:inline-flex;gap:6px;align-items:center;background:#f7fafc;border:1px solid #e6ecf4;border-radius:999px;padding:6px 10px;font-size:12px;color:#334155}
.kpi i{width:8px;height:8px;background:var(--ok);border-radius:50%}
</style>

<div class="page">
  <div class="card">
    <div class="head">
      <span class="badge">WhatsApp Marketing</span>
      <h3>Enviar promoción SIN plantilla (imagen desde archivo)</h3>
    </div>

    <div class="body">

      @if(session('wa_success'))
        <div class="alert alert-ok">{{ session('wa_success') }}</div>
      @endif
      @if(session('wa_info'))
        <div class="alert alert-warn">{{ session('wa_info') }}</div>
        @if(session('wa_fail'))
          <details style="margin-bottom:12px">
            <summary>Ver detalles de fallos</summary>
            <pre style="white-space:pre-wrap;background:#f8fafc;border:1px dashed var(--line);border-radius:10px;padding:10px;font-size:12px">{{ json_encode(session('wa_fail'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
          </details>
        @endif
      @endif

      {{-- Filtros de clientes --}}
      <form method="GET" action="{{ route('promos.whatsapp.direct.create') }}" style="margin-bottom:8px">
        <div class="tools">
          <div>
            <label class="small">Buscar</label>
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nombre, teléfono, email, asesor">
          </div>
          @if($categorias->count())
          <div>
            <label class="small">Categoría</label>
            <select name="categoria">
              <option value="">Todas</option>
              @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" {{ ($categoria ?? null)==$cat->id?'selected':'' }}>{{ $cat->nombre }}</option>
              @endforeach
            </select>
          </div>
          @endif
          <button class="btn btn-primary" type="submit">Filtrar</button>
          <span class="counter">{{ $clientes->count() }} cliente(s) listados</span>
        </div>
      </form>

      {{-- Form principal --}}
      <form method="POST" action="{{ route('promos.whatsapp.direct.send') }}" enctype="multipart/form-data" id="formPromo">
        @csrf

        <div class="grid grid-2">
          <div>
            <label>Título</label>
            <input type="text" name="titulo" maxlength="60" value="{{ old('titulo','¡Promoción especial de Grupo MediBuy! 🎉') }}" placeholder="Ej. ¡Promoción de fin de semana!">
            @error('titulo')<div class="small">{{ $message }}</div>@enderror>

            <div style="margin-top:12px">
              <div class="inline">
                <span class="kpi"><i></i> Mensaje rápido (auto)</span>
                <span class="hint">Completa los campos y generamos el texto por ti.</span>
              </div>

              <div style="margin-top:8px">
                <label>Producto / Oferta ({{ '{' }}{2}})</label>
                <input type="text" id="f_producto" placeholder="Ej. Colonoscopio FUJINON" value="">
              </div>
              <div class="grid" style="grid-template-columns:1fr 1fr">
                <div>
                  <label>Descuento ({{ '{' }}{3}})</label>
                  <input type="text" id="f_descuento" placeholder="Ej. 25%">
                </div>
                <div>
                  <label>Vigencia ({{ '{' }}{4}})</label>
                  <input type="text" id="f_vigencia" placeholder="Ej. Solo este fin de semana">
                </div>
              </div>
              <div style="margin-top:8px">
                <label>Pie de página (opcional)</label>
                <input type="text" id="f_footer" placeholder="Ej. ⚡ Oferta válida hasta agotar existencias.">
                <div class="small">Se agrega al final si lo completas.</div>
              </div>

              <div class="switcher">
                <div class="tab active" id="tab-auto">Usar mensaje automático</div>
                <div class="tab" id="tab-libre">Escribir texto libre</div>
              </div>

              {{-- Preview tipo WhatsApp --}}
              <div class="preview" style="margin-top:10px">
                <div class="ph">WA</div>
                <div class="bubble">
                  <h4 id="pv_titulo">¡Promoción especial de Grupo MediBuy! 🎉</h4>
                  <pre id="pv_texto">Hola 👋,
Tenemos para ti una gran oportunidad en equipo médico 🏥.

👉 …
📉 Descuento: …
⏳ Vigencia: …

No dejes pasar esta promoción exclusiva 💙.
En Grupo MediBuy estamos para apoyarte con la mejor calidad y servicio.</pre>
                  <div class="foot" id="pv_footer" style="display:none"></div>
                </div>
              </div>
            </div>

            {{-- Campo oculto que siempre viaja al servidor --}}
            <input type="hidden" name="descripcion" id="descripcion_hidden" value="">
            {{-- Textarea libre (solo si el usuario elige modo libre) --}}
            <div id="box_libre" style="display:none;margin-top:8px">
              <label>Descripción (texto libre)</label>
              <textarea rows="6" id="descripcion_libre" placeholder="Escribe tu texto…"></textarea>
              <div class="small">Este texto sustituirá al mensaje automático.</div>
            </div>
          </div>

          <div>
            <label>Imagen (JPG/PNG, máx. 5 MB)</label>
            <input type="file" name="imagen_file" accept=".jpg,.jpeg,.png" id="imagenInput">
            @error('imagen_file')<div class="small">{{ $message }}</div>@enderror
            <div class="preview" id="previewBox" style="display:none;margin-top:8px">
              <div class="ph">IMG</div>
              <img id="previewImg" alt="Preview" style="max-width:180px;border-radius:12px;display:block">
            </div>
          </div>
        </div>

        <div class="tabbar" role="tablist" aria-label="Origen de destinatarios" style="margin-top:8px">
          <div class="tab active" data-tab="bd">Seleccionar desde clientes</div>
          <div class="tab" data-tab="manual">Pegar números manualmente</div>
        </div>

        {{-- Tab: desde BD --}}
        <div id="tab-bd">
          <div class="tools" style="margin-top:8px">
            <button type="button" class="btn btn-ghost" id="btnSelTodos">Seleccionar visibles</button>
            <button type="button" class="btn btn-ghost" id="btnQuitarTodos">Quitar selección</button>
            <span class="counter" id="countSel">0 seleccionados</span>
          </div>
          <div class="clients-wrap" id="clientsList">
            @forelse($clientes as $c)
              @php $nombre = trim(($c->nombre ?? '').' '.($c->apellido ?? '')); @endphp
              <label class="row">
                <input type="checkbox" name="clientes_ids[]" value="{{ $c->id }}">
                <div>
                  <div class="name">{{ $nombre ?: 'Sin nombre' }}</div>
                  <div class="small">{{ $c->telefono ?: '—' }} · {{ $c->email ?: '—' }} @if($c->asesor) · Asesor: {{ $c->asesor }} @endif</div>
                </div>
              </label>
            @empty
              <div class="small">No hay clientes que coincidan con el filtro.</div>
            @endforelse
          </div>
        </div>

        {{-- Tab: manual --}}
        <div id="tab-manual" style="display:none">
          <label style="margin-top:10px">Números (uno por línea o separados por coma)</label>
          <textarea name="clientes_manual" rows="4" placeholder="52155xxxxxxx&#10;52156xxxxxxx">{{ old('clientes_manual') }}</textarea>
          <div class="small">Para MX también acepto 10 dígitos; convierto a <b>521</b> automáticamente.</div>
        </div>

        <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:10px">
          <button class="btn btn-primary" type="submit" id="btnSend">Enviar promoción</button>
        </div>
      </form>

      <div class="small" style="margin-top:12px">
        Nota: los mensajes sin plantilla solo se entregan dentro de la ventana de 24 h desde el último mensaje del cliente.
        Fuera de ese periodo, WhatsApp devuelve error de “re-engagement” (cód. <code>131047</code>).
      </div>

    </div>
  </div>
</div>

<script>
// ---------- Preview de imagen ----------
const input = document.getElementById('imagenInput');
const box   = document.getElementById('previewBox');
const img   = document.getElementById('previewImg');
input?.addEventListener('change', ()=>{
  const f = input.files?.[0];
  if (!f) { box.style.display='none'; return; }
  img.src = URL.createObjectURL(f);
  box.style.display = 'flex';
});

// ---------- Generador de mensaje automático ----------
const titulo       = document.querySelector('input[name="titulo"]');
const pvTitulo     = document.getElementById('pv_titulo');
const pvTexto      = document.getElementById('pv_texto');
const pvFooter     = document.getElementById('pv_footer');
const fProd        = document.getElementById('f_producto');
const fDesc        = document.getElementById('f_descuento');
const fVig         = document.getElementById('f_vigencia');
const fFooter      = document.getElementById('f_footer');

const hiddenDesc   = document.getElementById('descripcion_hidden');
const boxLibre     = document.getElementById('box_libre');
const txtLibre     = document.getElementById('descripcion_libre');
const tabAuto      = document.getElementById('tab-auto');
const tabLibre     = document.getElementById('tab-libre');

let modo = 'auto'; // 'auto' | 'libre'

function buildAutoMessage(){
  const p  = fProd.value?.trim() || '…';
  const d  = fDesc.value?.trim() || '…';
  const v  = fVig.value?.trim()  || '…';
  const body =
`Hola 👋,
Tenemos para ti una gran oportunidad en equipo médico 🏥.

👉 ${p}
📉 Descuento: ${d}
⏳ Vigencia: ${v}

No dejes pasar esta promoción exclusiva 💙.
En Grupo MediBuy estamos para apoyarte con la mejor calidad y servicio.`;
  pvTexto.textContent = body;
  pvTitulo.textContent = titulo.value || '¡Promoción especial de Grupo MediBuy! 🎉';

  const foot = fFooter.value.trim();
  if (foot) { pvFooter.style.display='block'; pvFooter.textContent = foot; }
  else { pvFooter.style.display='none'; pvFooter.textContent=''; }

  // Llevar al input oculto que leerá el servidor:
  hiddenDesc.value = foot ? (body + '\n\n' + foot) : body;
}

// inputs que refrescan el preview
['input','change'].forEach(evt=>{
  titulo.addEventListener(evt, buildAutoMessage);
  fProd.addEventListener(evt, buildAutoMessage);
  fDesc.addEventListener(evt, buildAutoMessage);
  fVig.addEventListener(evt, buildAutoMessage);
  fFooter.addEventListener(evt, buildAutoMessage);
});

// modo auto / libre
tabAuto.addEventListener('click', ()=>{
  modo='auto';
  tabAuto.classList.add('active'); tabLibre.classList.remove('active');
  boxLibre.style.display='none';
  buildAutoMessage();
});
tabLibre.addEventListener('click', ()=>{
  modo='libre';
  tabLibre.classList.add('active'); tabAuto.classList.remove('active');
  boxLibre.style.display='block';
});

// al escribir libre, copiar a hidden
txtLibre.addEventListener('input', ()=>{
  if (modo==='libre') hiddenDesc.value = txtLibre.value;
});

// inicial
buildAutoMessage();

// ---------- Tabs (destinatarios) ----------
document.querySelectorAll('.tabbar .tab').forEach(t=>{
  t.addEventListener('click', ()=>{
    document.querySelectorAll('.tabbar .tab').forEach(x=>x.classList.remove('active'));
    t.classList.add('active');
    const tab = t.dataset.tab;
    document.getElementById('tab-bd').style.display     = (tab==='bd')?'block':'none';
    document.getElementById('tab-manual').style.display = (tab==='manual')?'block':'none';
  });
});

// ---------- Selección y contador ----------
const list = document.getElementById('clientsList');
const countSel = document.getElementById('countSel');
function updateCount(){
  const n = list?.querySelectorAll('input[type=checkbox]:checked').length || 0;
  countSel.textContent = n + ' seleccionados';
}
list?.addEventListener('change', (e)=>{ if(e.target.type==='checkbox') updateCount(); });
document.getElementById('btnSelTodos')?.addEventListener('click', ()=>{
  list?.querySelectorAll('input[type=checkbox]').forEach(cb=>cb.checked = true);
  updateCount();
});
document.getElementById('btnQuitarTodos')?.addEventListener('click', ()=>{
  list?.querySelectorAll('input[type=checkbox]').forEach(cb=>cb.checked = false);
  updateCount();
});
updateCount();

// ---------- Envío: asegurar que descripción viaja correcta ----------
document.getElementById('formPromo')?.addEventListener('submit', ()=>{
  if (modo==='auto') buildAutoMessage();
});
</script>
@endsection
