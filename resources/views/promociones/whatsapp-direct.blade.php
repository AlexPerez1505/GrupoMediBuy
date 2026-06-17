{{-- resources/views/promociones/whatsapp-direct.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Promocionales')
@section('content')

<style>
  :root{
    --bg:#f7f9fb; --card:#fff; --line:#e6ebf1;
    --brand:#bfe3ff; --brand-ink:#0a0f1f;
    --ink:#0f172a; --muted:#64748b;
    --ok:#16a34a; --warn:#b45309; --err:#dc2626;
  }
  .wrap{max-width:1200px;margin:24px auto;padding:0 16px}
  .card{background:var(--card);border:1px solid var(--line);border-radius:18px;box-shadow:0 12px 32px rgba(2,8,23,.06);overflow:hidden}
  .card-head{padding:18px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px}
  .dot{width:10px;height:10px;border-radius:999px;background:var(--brand);animation:pulse 1.6s infinite}
  @keyframes pulse{0%{transform:scale(.9);opacity:.8}50%{transform:scale(1);opacity:1}100%{transform:scale(.9);opacity:.8}}
  h3{margin:0;font-size:20px;letter-spacing:-.3px;color:var(--ink)}
  .tag{background:#eff7ff;border:1px solid #dbeafe;color:#1e3a8a;padding:6px 10px;border-radius:10px;font-weight:700;font-size:12px}
  .muted{color:var(--muted);font-size:13px}
  label{font-size:13px;color:var(--muted);margin-bottom:6px;display:block}
  .grid{display:grid;gap:16px}
  @media (min-width:1000px){ .grid-2{grid-template-columns:1.1fr .9fr} }

  .hero{
    background:
      radial-gradient(1200px 160px at 10% -20%, #e8f3ff 0%, transparent 60%),
      radial-gradient(1200px 160px at 90% -20%, #e6f5ff 0%, transparent 60%),
      linear-gradient(180deg, #ffffff 0%, #ffffff 100%);
  }

  .inp, textarea, input[type=file], select{
    width:100%;border:1px solid var(--line);background:#fff;border-radius:12px;
    padding:12px 14px;font-size:14px;outline:none; transition:.15s border, .15s box-shadow;
  }
  .inp:focus, textarea:focus, select:focus{box-shadow:0 0 0 4px rgba(191,227,255,.35);border-color:#cfe9ff}
  .btn{
    display:inline-flex;align-items:center;gap:10px;border:0;border-radius:14px;
    padding:12px 18px;font-weight:700;cursor:pointer;background:var(--brand);color:var(--brand-ink);
    transition:transform .05s ease, box-shadow .15s ease;
  }
  .btn:hover{transform:translateY(-1px); box-shadow:0 6px 14px rgba(2,8,23,.08)}
  .btn-ghost{background:#fff;border:1px solid var(--line);color:var(--ink);border-radius:14px;padding:10px 14px;font-weight:600}
  .btn-lite{background:#eff7ff;border:1px solid #dbeafe;color:#1e3a8a;border-radius:12px;padding:8px 12px;font-weight:600;font-size:13px}
  .alert{margin:14px 0;padding:12px 14px;border-radius:12px;border:1px solid}
  .alert.ok{background:#f7fffb;border-color:#d1fae5;color:#065f46}
  .alert.warn{background:#fff7ed;border-color:#fed7aa;color:#9a3412}
  .alert.err{background:#fef2f2;border-color:#fecaca;color:#991b1b}

  /* Dropzone SOLO imagen */
  .dropzone{
    position:relative;border:2px dashed #dbeafe;border-radius:14px;background:linear-gradient(180deg,#fcfeff, #f7fbff);
    padding:14px; display:grid; grid-template-columns:120px 1fr; gap:14px; align-items:center;
    transition:border-color .15s ease, transform .15s ease, box-shadow .2s ease;
  }
  .dropzone.dragover{ border-color:#93c5fd; transform:scale(1.01); box-shadow:0 8px 28px rgba(2,8,23,.06) }
  .dz-thumb{
    width:120px;height:120px;border-radius:12px;border:1px solid var(--line);background:#f1f5f9;overflow:hidden;
    display:flex;align-items:center;justify-content:center; position:relative;
    cursor:pointer;
  }
  .dz-thumb img{width:100%;height:100%;object-fit:cover; animation:fadein .2s ease}
  @keyframes fadein{from{opacity:.6;transform:scale(.98)}to{opacity:1;transform:scale(1)}}
  .dz-actions{display:flex;gap:8px;margin-top:8px}
  .dz-remove{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:6px 10px;cursor:pointer}

  /* Tabla */
  .table-wrap{overflow:auto;border:1px solid var(--line);border-radius:14px}
  table{width:100%;border-collapse:separate;border-spacing:0}
  th,td{padding:10px 12px;border-bottom:1px solid var(--line);font-size:14px}
  th{font-weight:700;color:var(--ink);text-align:left;background:#fbfcff}
  tr:hover td{background:#fafcff}
  tr.picked td{background:#f4f9ff}
  .rowcheck{transform:scale(1.1);cursor:pointer}

  /* Chips */
  .chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
  .chip{display:inline-flex;align-items:center;gap:8px;background:#eef6ff;border:1px solid #dbeafe;color:#1e3a8a;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
  .chip .x{cursor:pointer;opacity:.7}

  /* Side */
  .side-card{border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:0 12px 32px rgba(2,8,23,.06)}
  .side-head{padding:16px 18px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center}
  .side-body{padding:16px 18px}
  @media (min-width:1000px){ .side-sticky{position:sticky; top:18px} }

  /* Drawer */
  .drawer{position:fixed; right:16px; bottom:16px; width:clamp(280px, 90vw, 420px);
    background:var(--card); border:1px solid var(--line); border-radius:18px; box-shadow:0 20px 60px rgba(2,8,23,.16);
    transform:translateY(calc(100% + 16px)); transition:transform .25s ease; z-index:1200}
  .drawer.open{ transform:translateY(0) }
  .drawer-head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--line)}
  .drawer-body{padding:12px 14px; max-height:40vh; overflow:auto}
  .drawer-foot{padding:12px 14px;border-top:1px solid var(--line);display:flex;gap:8px;justify-content:flex-end}

  .sticky-actions{position:sticky;bottom:-1px;background:linear-gradient(180deg, transparent, #fff 20%);padding-top:8px;margin-top:8px;display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap}

  .mini{font-size:12px;color:var(--muted)}
  .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;}
</style>

<div class="wrap" style="margin-top:110px;">
  <div class="card">
    <div class="card-head hero">
      <div style="display:flex;align-items:center;gap:12px">
        <span class="dot"></span>
        <h3>Promocionales WhatsApp <span class="tag">Imagen + variables</span></h3>
      </div>
    </div>

    <div class="card-body grid grid-2">
      {{-- IZQUIERDA --}}
      <div class="grid">

        {{-- Mensaje OK --}}
        @if(session('wa_success'))
          <div class="alert ok">{{ session('wa_success') }}</div>
        @endif

        {{-- Mensaje info --}}
        @if(session('wa_info'))
          <div class="alert warn">{{ session('wa_info') }}</div>
          @if(session('wa_fail'))
            <details style="margin-top:-6px">
              <summary class="mini">Ver detalles</summary>
              <pre class="mono" style="white-space:pre-wrap;background:#fff;border:1px dashed var(--line);border-radius:12px;padding:12px;font-size:12px">{{ json_encode(session('wa_fail'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          @endif
        @endif

        {{-- Errores Laravel --}}
        @if ($errors->any())
          <div class="alert err">
            <div style="font-weight:700;margin-bottom:6px">Errores del servidor</div>
            <ul style="margin:0;padding-left:18px">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- ✅ Errores en vivo (frontend) --}}
        <div id="liveError" class="alert err" style="display:none"></div>

        {{-- Buscar --}}
        <div>
          <label>Buscar (rápido)</label>
          <input class="inp" id="quickSearch" placeholder="Filtra por nombre o teléfono sin recargar">
          <div class="muted">Tip: Ctrl/Cmd + K para enfocar.</div>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('promos.whatsapp.direct.send') }}" enctype="multipart/form-data" id="sendForm" class="grid">
          @csrf

          {{-- IMAGEN --}}
          <div>
            <label>Imagen de encabezado</label>

            <div class="dropzone" id="dropzone">
              <div class="dz-thumb" id="dzThumb" title="Click para seleccionar imagen">
                <span class="muted" id="dzPlaceholder">IMG</span>
              </div>

              <div>
                <div style="font-weight:700;color:#0b1220;margin-bottom:4px">Sube una imagen</div>
                <div class="muted" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                  <span>Arrastra una imagen o</span>
                  <button type="button" class="btn-lite" id="btnBrowse">búscala</button>
                  <span>(se acepta cualquier imagen).</span>
                </div>

                {{-- ✅ OJO: este nombre debe coincidir con tu validate (imagen_file) --}}
                <input type="file" name="imagen_file" id="headerInput" accept="image/*" style="display:none" required>

                @error('imagen_file')<div class="mini" style="color:var(--err);margin-top:6px">{{ $message }}</div>@enderror

                <div class="dz-actions">
                  <button class="dz-remove" type="button" id="btnRemoveImg" style="display:none">Quitar imagen</button>
                </div>

                <div class="mini" id="fileMeta" style="margin-top:8px"></div>
              </div>
            </div>
          </div>

          {{-- FRASE --}}
          <div>
            <label>Texto ({{2}}) (frase)</label>
            <input class="inp" name="producto" id="phrase" maxlength="2000" placeholder="En promoción videocolonoscopio fujinon" required>
            @error('producto')<div class="mini" style="color:var(--err)">{{ $message }}</div>@enderror
            <div class="muted"><span id="fraseCounter">0</span>/2000 • <span id="phrasePreview">Tu frase aparecerá aquí…</span></div>
          </div>

          {{-- ✅ DESCUENTO + VIGENCIA (NO se quitaron) --}}
          <div class="grid" style="grid-template-columns:1fr 1fr;gap:12px">
            <div>
              <label>Descuento ({{3}})</label>
              <input class="inp" name="descuento" id="descuento" placeholder="Ej. 25%" required>
              @error('descuento')<div class="mini" style="color:var(--err)">{{ $message }}</div>@enderror
            </div>
            <div>
              <label>Vigencia ({{4}})</label>
              <input class="inp" name="vigencia" id="vigencia" placeholder="Ej. Vigente hasta 30/09" required>
              @error('vigencia')<div class="mini" style="color:var(--err)">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Tools --}}
          <div class="grid" style="grid-template-columns:1fr auto auto auto;align-items:center">
            <div class="muted">Selecciona destinatarios (se guarda en tu navegador).</div>
            <button class="btn-lite" type="button" id="checkPage">Seleccionar visibles</button>
            <button class="btn-lite" type="button" id="uncheckPage">Quitar visibles</button>
            <button class="btn-lite" type="button" id="invertPage">Invertir visibles</button>
          </div>

          {{-- Tabla --}}
          <div class="table-wrap">
            <table id="clientsTable">
              <thead>
                <tr>
                  <th style="width:42px"><input type="checkbox" id="checkAllPage"></th>
                  <th>Nombre</th>
                  <th>Teléfono</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($clientes as $c)
                  @php $nombre = trim(($c->nombre ?? '').' '.($c->apellido ?? '')); @endphp
                  <tr data-name="{{ $nombre }}" data-phone="{{ $c->telefono ?? '' }}">
                    <td><input type="checkbox" value="{{ $c->id }}" class="rowcheck"></td>
                    <td>{{ $nombre ?: '—' }}</td>
                    <td>{{ $c->telefono ?? '—' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="muted">Sin resultados</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="chips" id="chipsInline"></div>

          {{-- Acciones --}}
          <div class="sticky-actions">
            <button class="btn-ghost" type="button" id="toggleDrawer">Mostrar panel</button>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <span class="tag" id="selCounterBadge">0</span>
              <input type="hidden" name="mode" id="mode" value="selected">
              <button class="btn" type="submit" id="btnSelected">Enviar a seleccionados (<span id="selCountBtn">0</span>)</button>
            </div>
          </div>
        </form>
      </div>

      {{-- DERECHA --}}
      <div class="side-card side-sticky">
        <div class="side-head">
          <div style="display:flex;gap:8px;align-items:center">
            <strong>Seleccionados</strong>
            <span class="tag" id="selCounterBadgeSide">0</span>
          </div>
          <button class="btn-ghost" type="button" id="clearAll">Limpiar</button>
        </div>
        <div class="side-body">
          <div class="muted" style="margin-bottom:8px">Acciones rápidas sobre los visibles:</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
            <button class="btn-lite" type="button" id="sideCheckPage">Seleccionar</button>
            <button class="btn-lite" type="button" id="sideUncheckPage">Quitar</button>
            <button class="btn-lite" type="button" id="sideInvertPage">Invertir</button>
          </div>
          <div class="chips" id="chipsSide"></div>
          <div style="display:flex;justify-content:flex-end;margin-top:12px">
            <button class="btn" type="button" id="sideSend">Enviar a seleccionados</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Drawer --}}
<div class="drawer" id="drawer">
  <div class="drawer-head">
    <strong>Seleccionados (<span id="selCounterDrawer">0</span>)</strong>
    <button class="btn-ghost" id="closeDrawer" type="button">Cerrar</button>
  </div>
  <div class="drawer-body"><div class="chips" id="chipsDrawer"></div></div>
  <div class="drawer-foot">
    <button class="btn-ghost" id="drawerClear" type="button">Limpiar</button>
    <button class="btn" id="drawerSend" type="button">Enviar a seleccionados</button>
  </div>
</div>

<script>
(() => {
  /* ====== Helpers UI de error ====== */
  const liveError = document.getElementById('liveError');
  const showLiveError = (html) => {
    liveError.innerHTML = html;
    liveError.style.display = 'block';
    liveError.scrollIntoView({behavior:'smooth', block:'center'});
  };
  const clearLiveError = () => {
    liveError.innerHTML = '';
    liveError.style.display = 'none';
  };

  /* ========= Selección persistente ========= */
  const STORAGE_KEY = 'promoSelectedIds_v4';
  let selected = new Map();
  const saveSel = () => localStorage.setItem(STORAGE_KEY, JSON.stringify([...selected.values()]));
  const loadSel = () => {
    try { selected = new Map((JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]')).map(o=>[String(o.id), o])); }
    catch { selected = new Map(); }
  };

  /* Refs */
  const table = document.getElementById('clientsTable');
  const quickSearch = document.getElementById('quickSearch');
  const checkAll = document.getElementById('checkAllPage');

  const chipsInline = document.getElementById('chipsInline');
  const chipsSide   = document.getElementById('chipsSide');
  const chipsDrawer = document.getElementById('chipsDrawer');

  const selCountBtn = document.getElementById('selCountBtn');
  const selBadge    = document.getElementById('selCounterBadge');
  const selBadgeSide= document.getElementById('selCounterBadgeSide');
  const selDrawer   = document.getElementById('selCounterDrawer');

  const btnSelected = document.getElementById('btnSelected');
  const sideSend = document.getElementById('sideSend');
  const form = document.getElementById('sendForm');
  const mode = document.getElementById('mode');

  const checkPage = document.getElementById('checkPage');
  const uncheckPage = document.getElementById('uncheckPage');
  const invertPage = document.getElementById('invertPage');

  const sideCheckPage = document.getElementById('sideCheckPage');
  const sideUncheckPage = document.getElementById('sideUncheckPage');
  const sideInvertPage = document.getElementById('sideInvertPage');

  const drawer = document.getElementById('drawer');
  const toggleDrawer = document.getElementById('toggleDrawer');
  const closeDrawer  = document.getElementById('closeDrawer');
  const drawerClear  = document.getElementById('drawerClear');
  const drawerSend   = document.getElementById('drawerSend');
  const clearAllBtn  = document.getElementById('clearAll');

  /* Inputs */
  const phrase = document.getElementById('phrase');
  const descuento = document.getElementById('descuento');
  const vigencia = document.getElementById('vigencia');

  /* Utils */
  const checks = () => [...table.querySelectorAll('.rowcheck')];
  const rowInfo = (tr) => ({ id:String(tr.querySelector('.rowcheck').value), name:tr.dataset.name||'Cliente', phone:tr.dataset.phone||'' });

  function renderChips(container, max){
    container.innerHTML = '';
    const arr = [...selected.values()];
    arr.slice(0, max).forEach(o=>{
      const el = document.createElement('span');
      el.className='chip';
      el.innerHTML=`${o.name} <span class="muted">• ${o.phone || 's/tel'}</span> <span class="x" data-id="${o.id}">x</span>`;
      el.querySelector('.x').onclick = () => { selected.delete(String(o.id)); saveSel(); refresh(); }
      container.appendChild(el);
    });
    if(arr.length>max){
      const more=document.createElement('span');
      more.className='chip';
      more.textContent = `+${arr.length-max} más`;
      container.appendChild(more);
    }
  }

  const visibleRows = () => {
    const q = (quickSearch.value||'').trim().toLowerCase();
    const rows = [...table.querySelectorAll('tbody tr')];
    if(!q) return rows;
    return rows.filter(tr => ((tr.dataset.name||'')+' '+(tr.dataset.phone||'')).toLowerCase().includes(q));
  };

  function refresh(){
    checks().forEach(cb=>{
      const on = selected.has(String(cb.value));
      cb.checked = on;
      cb.closest('tr').classList.toggle('picked', on);
    });
    const n = selected.size;
    [selCountBtn, selBadge, selBadgeSide, selDrawer].forEach(el=> el && (el.textContent = n));
    renderChips(chipsInline, 20);
    renderChips(chipsSide, 40);
    renderChips(chipsDrawer, 999);
    const vis = visibleRows();
    checkAll.checked = vis.length && vis.every(tr => selected.has(rowInfo(tr).id));
    if(n>0 && !drawer.classList.contains('open')) drawer.classList.add('open');
  }

  function selectVis(){ visibleRows().forEach(tr => selected.set(rowInfo(tr).id, rowInfo(tr))); saveSel(); refresh(); }
  function unselectVis(){ visibleRows().forEach(tr => selected.delete(rowInfo(tr).id)); saveSel(); refresh(); }
  function invertVis(){ visibleRows().forEach(tr => { const {id} = rowInfo(tr); selected.has(id)? selected.delete(id): selected.set(id,rowInfo(tr)); }); saveSel(); refresh(); }

  /* Eventos selección */
  loadSel(); refresh();

  table.addEventListener('change', e=>{
    const cb = e.target.closest('.rowcheck'); if(!cb) return;
    const info = rowInfo(cb.closest('tr'));
    cb.checked ? selected.set(info.id, info) : selected.delete(info.id);
    saveSel(); refresh();
  });

  checkAll.addEventListener('change', e=>{ e.target.checked? selectVis() : unselectVis(); });

  checkPage.addEventListener('click', selectVis);
  uncheckPage.addEventListener('click', unselectVis);
  invertPage.addEventListener('click', invertVis);

  sideCheckPage.addEventListener('click', selectVis);
  sideUncheckPage.addEventListener('click', unselectVis);
  sideInvertPage.addEventListener('click', invertVis);

  quickSearch.addEventListener('input', ()=>{
    const q = quickSearch.value.trim().toLowerCase();
    [...table.querySelectorAll('tbody tr')].forEach(tr=>{
      const txt = ((tr.dataset.name||'')+' '+(tr.dataset.phone||'')).toLowerCase();
      tr.style.display = !q || txt.includes(q) ? '' : 'none';
    });
    refresh();
  });

  window.addEventListener('keydown', e=>{
    if((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='k'){
      e.preventDefault(); quickSearch.focus();
    }
  });

  /* -------- Dropzone SOLO imagen -------- */
  const dz = document.getElementById('dropzone');
  const dzThumb = document.getElementById('dzThumb');
  const inputFile = document.getElementById('headerInput');
  const btnRemoveImg = document.getElementById('btnRemoveImg');
  const btnBrowse = document.getElementById('btnBrowse');
  const fileMeta = document.getElementById('fileMeta');

  const MAX_HINT_MB = 24; // solo para avisar (no bloquea). si pasa de esto, casi seguro PHP/WA rechaza

  function bytesToMB(b){ return (b/1024/1024).toFixed(2); }

  function setThumb(file){
    const reader = new FileReader();
    reader.onload = e => {
      dzThumb.innerHTML = `<img src="${e.target.result}" alt="preview">`;
      btnRemoveImg.style.display='inline-block';
    };
    reader.readAsDataURL(file);

    const mb = bytesToMB(file.size);
    fileMeta.textContent = `Archivo: ${file.name} • ${mb} MB • ${file.type || 'tipo desconocido'}`;

    clearLiveError();

    if(!String(file.type||'').startsWith('image/')){
      showLiveError('El archivo seleccionado no es una imagen.');
    }

    if(parseFloat(mb) > MAX_HINT_MB){
      showLiveError(`La imagen pesa ${mb} MB. Si no se envía, es por límite del servidor (upload_max_filesize/post_max_size) o por límite de WhatsApp. Reduce el tamaño.`);
    }
  }

  function clearThumb(){
    dzThumb.innerHTML = `<span class="muted" id="dzPlaceholder">IMG</span>`;
    inputFile.value='';
    btnRemoveImg.style.display='none';
    fileMeta.textContent = '';
  }

  dzThumb.addEventListener('click', (e)=>{ e.preventDefault(); inputFile.click(); });
  btnBrowse.addEventListener('click', (e)=>{ e.preventDefault(); inputFile.click(); });

  dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
  dz.addEventListener('dragleave', ()=> dz.classList.remove('dragover'));
  dz.addEventListener('drop', e => {
    e.preventDefault(); dz.classList.remove('dragover');
    const f = e.dataTransfer.files?.[0]; if(!f) return;
    if(!String(f.type||'').startsWith('image/')){
      showLiveError('Solo archivos de imagen.');
      return;
    }
    inputFile.files = e.dataTransfer.files;
    setThumb(f);
  });

  inputFile.addEventListener('change', ()=>{
    const f = inputFile.files?.[0];
    if(f) setThumb(f);
  });

  btnRemoveImg.addEventListener('click', (e)=>{ e.preventDefault(); clearThumb(); });

  /* Frase: contador + preview */
  const fraseCounter = document.getElementById('fraseCounter');
  const phrasePreview = document.getElementById('phrasePreview');
  phrase.addEventListener('input', ()=>{
    fraseCounter.textContent = phrase.value.length;
    phrasePreview.textContent = phrase.value || 'Tu frase aparecerá aquí…';
  });

  /* ====== Submit: validación previa + inject ids ====== */
  function injectIds(){
    form.querySelectorAll('input[name="clientes_ids[]"]').forEach(el=>el.remove());
    for(const {id} of selected.values()){
      const h=document.createElement('input');
      h.type='hidden'; h.name='clientes_ids[]'; h.value=id; form.appendChild(h);
    }
  }

  function validateBeforeSubmit(){
    const errs = [];

    const f = inputFile.files?.[0];
    if(!f) errs.push('Falta seleccionar la imagen.');

    if(f && !String(f.type||'').startsWith('image/')) errs.push('El archivo no es una imagen.');

    if(!phrase.value.trim()) errs.push('Falta la frase.');
    if(!descuento.value.trim()) errs.push('Falta el descuento.');
    if(!vigencia.value.trim()) errs.push('Falta la vigencia.');

    if(selected.size===0) errs.push('Selecciona al menos un cliente.');

    if(errs.length){
      showLiveError('<div style="font-weight:700;margin-bottom:6px">No se puede enviar</div><ul style="margin:0;padding-left:18px">' + errs.map(e=>`<li>${e}</li>`).join('') + '</ul>');
      return false;
    }

    clearLiveError();
    return true;
  }

  function submitSelected(e){
    e.preventDefault();
    mode.value = 'selected';
    injectIds();
    if(!validateBeforeSubmit()) return;
    form.submit();
  }

  btnSelected.addEventListener('click', submitSelected);
  sideSend.addEventListener('click', submitSelected);
  drawerSend.addEventListener('click', submitSelected);

  toggleDrawer.addEventListener('click', ()=> drawer.classList.toggle('open'));
  closeDrawer.addEventListener('click', ()=> drawer.classList.remove('open'));
  drawerClear.addEventListener('click', ()=> { selected.clear(); saveSel(); refresh(); });
  clearAllBtn.addEventListener('click', ()=> { selected.clear(); saveSel(); refresh(); });

})();
</script>
@endsection
