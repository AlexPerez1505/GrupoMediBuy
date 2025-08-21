@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
  :root{
    --bg:#f6f7fb; --card:#ffffff; --line:#eceff4; --text:#1f2937; --muted:#6b7280;
    --out:#22c55e; --out2:#16a34a; --in:#ffffff; --in-border:#e5e7eb;
  }
  body{font-family:'Inter',sans-serif;background:var(--bg);}
  .chat-wrap{max-width:980px;margin:16px auto;padding:0 12px;}
  .chat-convo{display:flex;flex-direction:column;height:calc(100vh - 120px);background:var(--card);
    border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.07);}
  .chat-header{display:flex;gap:12px;align-items:center;padding:14px 18px;border-bottom:1px solid var(--line);
    background:linear-gradient(180deg,#fafbfc,#fff);}
  .chat-header img{width:40px;height:40px;border-radius:50%;}
  .chat-header .title{font-weight:600;color:var(--text)}
  .chat-header .sub{font-size:.85rem;color:var(--muted)}
  .badge{margin-left:8px;font-size:.72rem;padding:4px 8px;border-radius:999px;background:#eef2ff;color:#334155;border:1px solid #dbeafe}

  .chat-body{position:relative;flex:1;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:14px;
    background:linear-gradient(180deg,#fafafa,#fff)}
  .day-sep{align-self:center;font-size:.78rem;color:#64748b;background:#eef2ff;border:1px solid #dbeafe;border-radius:999px;padding:4px 10px;margin:6px 0}
  .group{display:flex;flex-direction:column;max-width:88%;}
  .msg{position:relative;padding:12px 14px;border-radius:16px;font-size:.94rem;line-height:1.45;word-wrap:break-word;
    box-shadow:0 2px 10px rgba(0,0,0,.04);animation:fadeIn .2s ease;}
  .msg.in{background:var(--in);border:1px solid var(--in-border);align-self:flex-start;border-bottom-left-radius:6px;}
  .msg.out{align-self:flex-end;border-bottom-right-radius:6px;color:#fff;background:linear-gradient(135deg,var(--out),var(--out2));}
  .msg img{max-width:280px;border-radius:12px;display:block}
  .msg.in a{color:#1f2937;text-decoration:underline}
  .msg.out a{color:#fff;text-decoration:underline}
  .meta{display:flex;align-items:center;gap:6px;margin-top:4px;font-size:.75rem;color:var(--muted)}
  .meta.right{justify-content:flex-end}
  .ticks{font-size:.9em;opacity:.9}
  .ticks.read{color:#34b7f1}

  .attach-bar{display:none;align-items:center;gap:10px;padding:10px 14px;border-top:1px solid var(--line);
    border-bottom:1px solid var(--line);background:#fbfbff}
  .attach-chip{display:flex;align-items:center;gap:10px;background:#eef2ff;border:1px solid #dbeafe;border-radius:12px;padding:8px 10px;}
  .attach-thumb{width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;background:#fff}
  .attach-name{font-size:.9rem;color:#334155}
  .attach-size{font-size:.78rem;color:#64748b}
  .attach-remove{border:none;background:#ef4444;color:#fff;border-radius:10px;padding:8px 10px;cursor:pointer}
  .attach-remove:active{transform:scale(.98)}

  .typing{position:sticky;bottom:0;align-self:flex-start;margin-top:auto;display:none;align-items:center;gap:6px;
    background:#eef2ff;color:#3b82f6;padding:6px 10px;border-radius:12px;font-size:.85rem;border:1px solid #dbeafe}
  .typing i{width:6px;height:6px;background:#93c5fd;border-radius:50%;display:inline-block;animation:blink 1.2s infinite}
  .typing i:nth-child(2){animation-delay:.2s}.typing i:nth-child(3){animation-delay:.4s}

  .composer{display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding:12px 14px;border-top:1px solid var(--line);background:#fff}
  .field{flex:1;display:flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:14px;padding:8px 10px;background:#fff}
  .field input[type=text]{flex:1;border:none;outline:none;font-size:.95rem}
  .icon-btn{border:none;background:#eef2f7;color:#334155;border-radius:10px;padding:8px 10px;cursor:pointer;transition:transform .06s}
  .icon-btn:active{transform:scale(.98)}
  .send-btn{border:none;border-radius:12px;padding:10px 16px;cursor:pointer;color:#fff;
    background:linear-gradient(135deg,var(--out),var(--out2));box-shadow:0 6px 16px rgba(34,197,94,.25)}
  .send-btn:disabled{opacity:.5;cursor:not-allowed}

  @keyframes blink{0%,80%,100%{opacity:0}40%{opacity:1}}
  @keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}

  @media(max-width:768px){
    .chat-wrap{margin:0;padding:0}
    .chat-convo{height:100dvh;border-radius:0}
    .group{max-width:92%}
    .msg img{max-width:70vw}
  }
</style>

<div class="chat-wrap" style="margin-top:90px;">
  <div class="chat-convo" data-user="{{ $currentUser }}">
    <div class="chat-header">
      <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName ?? $currentUser) }}&background=22c55e&color=fff" alt="">
      <div>
        <div class="title">Chat con {{ $displayName ?? $currentUser }}</div>
        <div class="sub">
          Conversación activa
          @if(!empty($handover))
            <span class="badge">En atención @if(!empty($agentName)) · {{ $agentName }} @endif</span>
          @else
            <span class="badge">Automático</span>
          @endif
        </div>
      </div>
    </div>

    <div id="chat-body" class="chat-body">
      @php
        $lastDay = null;
        $tz = 'America/Mexico_City';
      @endphp

      @foreach($messages as $m)
        @php
          $isoUtc = \Carbon\Carbon::parse($m->wa_timestamp)->utc()->toIso8601String();
          $dayKey = \Carbon\Carbon::parse($m->wa_timestamp)->setTimezone($tz)->toDateString();
        @endphp

        @if($dayKey !== $lastDay)
          @php
            $label = \Carbon\Carbon::parse($m->wa_timestamp)->setTimezone($tz)->isToday() ? 'Hoy'
                   : (\Carbon\Carbon::parse($m->wa_timestamp)->setTimezone($tz)->isYesterday() ? 'Ayer'
                   : \Carbon\Carbon::parse($m->wa_timestamp)->setTimezone($tz)->isoFormat('D MMM YYYY'));
            $lastDay = $dayKey;
          @endphp
          <div class="day-sep" data-day="{{ $dayKey }}">{{ $label }}</div>
        @endif

        <div class="group" data-id="{{ $m->id }}" data-ts="{{ $isoUtc }}">
          <div class="msg {{ $m->direction === 'in' ? 'in' : 'out' }}">
            @if($m->type === 'text')
              {!! nl2br(e($m->text)) !!}
            @elseif($m->type === 'image')
              @php
                $imgUrl = $m->media_id
                  ? ( \Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : ($m->media_link ?? '#') )
                  : ($m->media_link ?? '#');
              @endphp
              <img src="{{ $imgUrl }}" alt="Imagen">
            @elseif($m->type === 'document')
              @php
                $docUrl = $m->media_id
                  ? ( \Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : ($m->media_link ?? '#') )
                  : ($m->media_link ?? '#');
              @endphp
              📎 <a href="{{ $docUrl }}" target="_blank">{{ $m->media_filename ?? 'Documento' }}</a>
            @else
              {!! nl2br(e($m->text ?? '')) !!}
            @endif
          </div>
          <div class="meta {{ $m->direction === 'out' ? 'right' : '' }}">
            <span class="time" data-ts="{{ $isoUtc }}"></span>
            @if($m->direction === 'out')
              @php $st = $m->status ?? ''; @endphp
              <span class="ticks {{ $st === 'read' ? 'read' : '' }}" data-status="{{ $st }}">
                @if($st === 'read') ✓✓ @elseif($st === 'delivered') ✓✓ @elseif($st === 'sent') ✓ @else · @endif
              </span>
            @endif
          </div>
        </div>
      @endforeach

      <div id="typing" class="typing"><i></i><i></i><i></i></div>
    </div>

    <div id="attach-bar" class="attach-bar">
      <div class="attach-chip" id="attach-chip"></div>
      <button id="attach-remove" type="button" class="attach-remove">Quitar</button>
    </div>

    <form id="chat-form" class="composer" action="{{ route('wa.chat.send',$currentUser) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="field">
        <button class="icon-btn" type="button" id="btn-attach" title="Adjuntar">📎</button>
        <input id="chat-input" type="text" name="text" placeholder="Escribe un mensaje…">
        <input id="file-input" type="file" name="file" style="display:none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
      </div>
      <button id="send-btn" class="send-btn" type="submit" disabled>Enviar</button>
    </form>
  </div>
</div>

@php
  $waMediaUrlTpl = \Illuminate\Support\Facades\Route::has('wa.media')
      ? route('wa.media', ['mediaId' => 'MEDIA_ID_PLACEHOLDER'])
      : null;
@endphp

<script>
  const chatBody   = document.getElementById('chat-body');
  const chatForm   = document.getElementById('chat-form');
  const chatInput  = document.getElementById('chat-input');
  let   fileInput  = document.getElementById('file-input');
  const btnAttach  = document.getElementById('btn-attach');
  const sendBtn    = document.getElementById('send-btn');
  const typing     = document.getElementById('typing');
  const user       = document.querySelector('.chat-convo').dataset.user;
  const attachBar  = document.getElementById('attach-bar');
  const attachChip = document.getElementById('attach-chip');
  const attachRmBtn= document.getElementById('attach-remove');

  /* === Forzar MX siempre === */
  const MX_TZ = 'America/Mexico_City';
  const timeFmt = new Intl.DateTimeFormat('es-MX', { timeZone: MX_TZ, hour:'2-digit', minute:'2-digit', hour12:false });

  /* Ruta media */
  const MEDIA_URL_TPL = @json($waMediaUrlTpl);
  const mediaUrl = (id) => MEDIA_URL_TPL ? MEDIA_URL_TPL.replace('MEDIA_ID_PLACEHOLDER', encodeURIComponent(id)) : null;

  /* ===== Autoscroll con respeto al scroll del usuario ===== */
  let pinnedToBottom = true;
  const isNearBottom=(t=80)=> (chatBody.scrollHeight - chatBody.clientHeight - chatBody.scrollTop) <= t;
  const scrollToBottom=()=> chatBody.scrollTop = chatBody.scrollHeight;
  const preserveScrollWhile=(fn)=>{
    if (pinnedToBottom){ fn(); scrollToBottom(); return; }
    const oldBottom = chatBody.scrollHeight - chatBody.scrollTop;
    fn(); requestAnimationFrame(()=> chatBody.scrollTop = chatBody.scrollHeight - oldBottom);
  };
  chatBody.addEventListener('scroll', ()=> pinnedToBottom = isNearBottom());
  scrollToBottom();

  /* ======== PARSEO DE TIEMPOS – FIX: asumir UTC si no hay zona ======== */
  const RE_NO_TZ = /^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}:\d{2})$/; // 'YYYY-MM-DD HH:MM:SS' o 'YYYY-MM-DDTHH:MM:SS'

  function toDate(ts){
    if (!ts) return null;
    if (typeof ts === 'number') return (ts > 1e12) ? new Date(ts) : new Date(ts*1000);
    if (/^\d{10}$/.test(ts)) return new Date(Number(ts)*1000);
    if (/^\d{13}$/.test(ts)) return new Date(Number(ts));
    if (typeof ts === 'string' && RE_NO_TZ.test(ts)) {
      // ⬅️ El backend a veces manda sin zona: interprétalo como UTC
      return new Date(ts.replace(' ', 'T') + 'Z');
    }
    const d = new Date(ts);
    return isNaN(d.getTime()) ? null : d;
  }

  function normalizeTsIso(ts){
    if (!ts) return '';
    if (typeof ts === 'string' && RE_NO_TZ.test(ts)) {
      // ⬅️ Fuerza ISO-UTC si venía sin zona
      return new Date(ts.replace(' ', 'T') + 'Z').toISOString();
    }
    if (typeof ts === 'number') {
      const ms = ts > 1e12 ? ts : ts*1000;
      return new Date(ms).toISOString();
    }
    if (/^\d{10}$/.test(ts)) return new Date(Number(ts)*1000).toISOString();
    if (/^\d{13}$/.test(ts)) return new Date(Number(ts)).toISOString();
    const d = new Date(ts);
    return isNaN(d.getTime()) ? '' : d.toISOString();
  }

  function tsToSec(ts){
    const d = toDate(ts);
    return d ? Math.floor(d.getTime()/1000) : 0;
  }

  function renderTimes(){
    document.querySelectorAll('.time[data-ts]').forEach(el=>{
      const d = toDate(el.dataset.ts);
      if (!d) return;
      el.textContent = timeFmt.format(d);
    });
  }
  renderTimes();

  /* ===== Separadores por día ===== */
  const dayFmtKey = new Intl.DateTimeFormat('en-CA', { timeZone: MX_TZ, year:'numeric', month:'2-digit', day:'2-digit' });
  function dayKeyFor(d){
    const parts = dayFmtKey.formatToParts(d);
    const y = parts.find(p=>p.type==='year').value;
    const m = parts.find(p=>p.type==='month').value;
    const da= parts.find(p=>p.type==='day').value;
    return `${y}-${m}-${da}`;
  }
  function ensureDaySeparator(d){
    const key = dayKeyFor(d);
    if (chatBody.querySelector(`.day-sep[data-day="${key}"]`)) return;
    const label = (()=> {
      const today = dayKeyFor(new Date());
      const yd    = dayKeyFor(new Date(Date.now()-86400000));
      if (key===today) return 'Hoy';
      if (key===yd)    return 'Ayer';
      return new Intl.DateTimeFormat('es-MX',{timeZone:MX_TZ, day:'2-digit', month:'short', year:'numeric'}).format(d);
    })();
    const sep = `<div class="day-sep" data-day="${key}">${label}</div>`;
    const nodes = [...chatBody.querySelectorAll('.group')];
    let inserted = false;
    const startOfDay = new Date(d); startOfDay.setHours(0,0,0,0);
    const sSec = Math.floor(startOfDay.getTime()/1000);
    for (const node of nodes){
      const nSec = tsToSec(node.dataset.ts);
      if (nSec >= sSec){ node.insertAdjacentHTML('beforebegin', sep); inserted = true; break; }
    }
    if (!inserted) chatBody.insertAdjacentHTML('beforeend', sep);
  }

  /* ===== UX envío ===== */
  function updateSendEnabled(){
    const hasText = chatInput.value.trim().length>0;
    const hasFile = fileInput.files.length>0;
    sendBtn.disabled = !(hasText || hasFile);
  }
  chatInput.addEventListener('input', ()=>{
    typing.style.display = chatInput.value.trim().length ? 'flex' : 'none';
    updateSendEnabled();
  });

  let previewUrl=null;
  const formatBytes=b=>{ if(!b&&b!==0)return''; const u=['B','KB','MB','GB']; let i=0; while(b>=1024&&i<u.length-1){b/=1024;i++} return `${b.toFixed(1)} ${u[i]}`; };
  function showAttachmentPreview(file){
    if (previewUrl){ URL.revokeObjectURL(previewUrl); previewUrl=null; }
    attachChip.innerHTML='';
    const size=formatBytes(file.size);
    let inner='';
    if ((file.type||'').startsWith('image/')){
      previewUrl = URL.createObjectURL(file);
      inner = `<img src="${previewUrl}" class="attach-thumb" alt="Imagen">
               <div><div class="attach-name">${file.name}</div><div class="attach-size">${size} • Imagen</div></div>`;
    } else {
      inner = `<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='44' height='44'%3E%3Crect width='44' height='44' rx='8' fill='%23fff' stroke='%23e5e7eb'/%3E%3Ctext x='22' y='27' text-anchor='middle' font-size='18' fill='%239ca3af'%3E📄%3C/text%3E%3C/svg%3E" class="attach-thumb" alt="Documento">
               <div><div class="attach-name">${file.name}</div><div class="attach-size">${size} • Documento</div></div>`;
    }
    attachBar.style.display='flex';
    attachChip.insertAdjacentHTML('beforeend', inner);
  }
  function bindFileListener(){
    fileInput.addEventListener('change', ()=>{
      if (fileInput.files.length) showAttachmentPreview(fileInput.files[0]);
      else hideAttachmentPreview();
      updateSendEnabled();
    });
  }
  bindFileListener();
  function hideAttachmentPreview(){ if(previewUrl){URL.revokeObjectURL(previewUrl); previewUrl=null;} attachChip.innerHTML=''; attachBar.style.display='none'; }
  function clearFileInputHard(){
    const clone=fileInput.cloneNode(); clone.id=fileInput.id; clone.name=fileInput.name; clone.style.display=fileInput.style.display;
    fileInput.replaceWith(clone); fileInput=clone; bindFileListener();
  }
  function clearAttachmentAll(){ hideAttachmentPreview(); clearFileInputHard(); updateSendEnabled(); }
  attachRmBtn.addEventListener('click', clearAttachmentAll);

  chatInput.addEventListener('keydown', e=>{
    if (e.key==='Enter' && !e.shiftKey){ e.preventDefault(); if(!sendBtn.disabled) chatForm.requestSubmit(); }
  });

  function detectType(){ const f=fileInput.files[0]; if(!f) return 'text'; return (f.type||'').startsWith('image/') ? 'image' : 'document'; }

  /* ===== Envío AJAX ===== */
  let sending=false;
  chatForm.addEventListener('submit', async e=>{
    e.preventDefault(); if(sending) return;
    const hasFile=fileInput.files.length>0, typed=chatInput.value.trim();
    if(!hasFile && !typed) return;

    sending=true; sendBtn.disabled=true;
    const fd=new FormData();
    fd.append('_token', document.querySelector('input[name=_token]').value);
    const type=hasFile ? detectType() : 'text';
    fd.append('type', type);
    if (hasFile) fd.append('file', fileInput.files[0]); else fd.append('text', typed);

    chatInput.value=''; typing.style.display='none'; clearAttachmentAll();

    try{
      const res = await fetch(chatForm.action, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, cache:'no-store' });
      const msg = await res.json();
      preserveScrollWhile(()=> upsertMessage(msg));
      renderTimes();
    }catch(err){ console.error('send error', err); }
    finally{ sending=false; updateSendEnabled(); }
  });

  /* ===== Helpers de render ===== */
  const escapeHtml=s=>(s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const escapeAttr=s=>escapeHtml(s).replace(/"/g,'&quot;');
  const statusIcon=s=> s==='read'?{text:'✓✓',cls:'ticks read'} : s==='delivered'?{text:'✓✓',cls:'ticks'} : s==='sent'?{text:'✓',cls:'ticks'} : {text:'·',cls:'ticks'};

  function messageHtml(m){
    const dir = m.direction==='in' ? 'in':'out';
    let content='';
    if (m.type==='text'){
      content = (m.text?escapeHtml(m.text).replace(/\n/g,'<br>'):'');
    } else if (m.type==='image'){
      const u = m.media_id ? (mediaUrl(m.media_id) || m.media_link || '#') : (m.media_link || '#');
      content = `<img src="${escapeAttr(u)}" alt="Imagen">`;
    } else if (m.type==='document'){
      const u = m.media_id ? (mediaUrl(m.media_id) || m.media_link || '#') : (m.media_link || '#');
      const name = m.media_filename || 'Documento';
      content = `📎 <a href="${escapeAttr(u)}" target="_blank">${escapeHtml(name)}</a>`;
    } else {
      content = escapeHtml(m.text||'');
    }
    const tsIso = normalizeTsIso(m.wa_timestamp);
    const st = m.status||'';
    const ic = statusIcon(st);
    const metaRight = dir==='out' ? ' right' : '';

    const d = toDate(tsIso); if (d) ensureDaySeparator(d);

    return `
      <div class="group" data-id="${m.id}" data-ts="${escapeAttr(tsIso)}">
        <div class="msg ${dir}">${content}</div>
        <div class="meta${metaRight}">
          <span class="time" data-ts="${escapeAttr(tsIso)}"></span>
          ${dir==='out' ? `<span class="${ic.cls}" data-status="${escapeAttr(st)}">${ic.text}</span>` : ''}
        </div>
      </div>
    `;
  }

  function insertSorted(html, tsIso){
    const tsSec = tsToSec(tsIso);
    const nodes = chatBody.querySelectorAll('.group');
    for (const node of nodes){
      const nSec = tsToSec(node.dataset.ts);
      if (nSec > tsSec){ node.insertAdjacentHTML('beforebegin', html); return; }
    }
    chatBody.insertAdjacentHTML('beforeend', html);
  }

  function upsertMessage(m){
    const node = chatBody.querySelector(`.group[data-id="${m.id}"]`);
    if (!node){
      const html  = messageHtml(m);
      const tsIso = normalizeTsIso(m.wa_timestamp);
      insertSorted(html, tsIso);
      const just = chatBody.querySelector(`.group[data-id="${m.id}"] img`);
      if (just) just.addEventListener('load', ()=>{ if (pinnedToBottom) scrollToBottom(); }, {once:true});
    } else {
      if (m.direction==='out'){
        const ticksEl = node.querySelector('.ticks');
        if (ticksEl){
          const ic = statusIcon(m.status||'');
          ticksEl.textContent = ic.text; ticksEl.className = ic.cls; ticksEl.dataset.status = m.status||'';
        }
      }
      const tEl = node.querySelector('.time');
      const tsIso = normalizeTsIso(m.wa_timestamp);
      if (tEl && tsIso){ tEl.dataset.ts = tsIso; node.dataset.ts = tsIso; }
    }
  }

  /* ===== Polling ===== */
  async function tick(){
    try{
      const res = await fetch(`/whatsapp/chat/${encodeURIComponent(user)}/fetch`, { headers:{'Accept':'application/json'}, cache:'no-store' });
      const msgs = await res.json();
      preserveScrollWhile(()=>{ msgs.forEach(m=>upsertMessage(m)); });
      if (pinnedToBottom) scrollToBottom();
      renderTimes();
    }catch(e){ console.error('poll error', e); }
  }
  setInterval(tick, 2500);
</script>
@endsection
