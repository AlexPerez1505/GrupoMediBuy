@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

@php
  use Illuminate\Support\Str;
  use App\Models\Cliente;

  /* ===== Helpers del lado servidor (para el render inicial) ===== */

  $getDisplayName = function (?string $msisdn) {
      if (!$msisdn) return null;
      static $cache = [];
      $digits = preg_replace('/\D+/', '', $msisdn);
      $key = substr($digits, -10);
      if (isset($cache[$key])) return $cache[$key];

      $cli = Cliente::where('telefono', 'like', "%{$key}%")->first();
      $name = null;
      if ($cli) {
          $name = trim(implode(' ', array_filter([$cli->nombre ?? null, $cli->apellido ?? null]))) ?: null;
      }
      return $cache[$key] = $name;
  };

  $formatMsisdn = function (?string $msisdn) {
      if (!$msisdn) return '';
      $d = preg_replace('/\D+/', '', $msisdn);
      if (Str::startsWith($d, '521'))      $d = '+52 ' . substr($d, 3);
      elseif (Str::startsWith($d, '52'))   $d = '+52 ' . substr($d, 2);
      return trim($d);
  };

  $getPeer = function ($row) {
      foreach (['from', 'msisdn', 'peer', 'telefono', 'phone'] as $field) {
          if (isset($row->$field) && $row->$field) return $row->$field;
      }
      return null;
  };

  $getLastAt = function ($row) {
      foreach (['last_at', 'last_ts', 'wa_timestamp'] as $field) {
          if (!empty($row->$field)) return \Carbon\Carbon::parse($row->$field);
      }
      return null;
  };

  // URL para polling JSON
  $inboxPollUrl = \Illuminate\Support\Facades\Route::has('wa.inbox.fetch')
      ? route('wa.inbox.fetch')
      : url('/whatsapp/inbox/fetch');
@endphp

<style>
  :root{
    --bg:#f5f7fb; --card:#ffffff; --line:#e9edf3; --text:#0f172a; --muted:#64748b;
    --brand:#16a34a; --brand2:#22c55e; --hover:#f8fafc;
    --chip:#eef2ff; --chip-b:#dbeafe; --badge:#ecfdf5; --badge-b:#a7f3d0; --badge-t:#065f46;
  }
  body{font-family:'Inter',sans-serif;background:var(--bg);}
  .inbox-shell{max-width:980px;margin:24px auto;padding:0 12px;}
  .inbox{background:var(--card);border-radius:20px;overflow:hidden;box-shadow:0 14px 40px rgba(2,6,23,.08);}
  .inbox-top{display:flex;gap:12px;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fbfcff,#fff);}
  .inbox-title{font-weight:700;color:var(--text)}
  .top-right{display:flex;gap:10px;align-items:center}
  .chip{display:inline-flex;align-items:center;gap:6px;background:var(--chip);border:1px solid var(--chip-b);border-radius:999px;padding:6px 10px;color:#374151;font-weight:600;font-size:.85rem;cursor:default;}
  .chip .dot{width:8px;height:8px;border-radius:50%;background:#a1a1aa}
  .chip.ok .dot{background:#22c55e}
  .chip.warn .dot{background:#f59e0b}
  .chip.action{cursor:pointer;}
  .search{display:flex;align-items:center;gap:8px;border:1px solid var(--line);background:#fff;border-radius:12px;padding:8px 10px;min-width:240px}
  .search input{border:none;outline:none;width:100%;font-size:.95rem}
  .list{display:block}
  .row{
    /* Quitamos animaciones por defecto para evitar parpadeo */
    display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid var(--line);text-decoration:none;color:inherit;background:#fff;transition:background .12s ease;
  }
  .row:hover{background:var(--hover);}
  .row.unread .name{font-weight:800}
  .row.unread .preview{color:#0f172a;font-weight:600}
  /* Sólo animamos la ENTRADA de filas nuevas (un solo pulso suave) */
  .row--new{animation:fadeIn .18s ease both;}
  @keyframes fadeIn{from{opacity:.0;transform:translateY(2px)}to{opacity:1;transform:none}}

  .list.no-anim .row{transition:none !important; animation:none !important;} /* desactiva animación durante reordenamientos */

  .avatar{width:52px;height:52px;border-radius:14px;flex-shrink:0;overflow:hidden;background:linear-gradient(135deg,var(--brand),var(--brand2));box-shadow:0 4px 14px rgba(34,197,94,.25);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;}
  .avatar img{width:100%;height:100%;object-fit:cover}
  .info{flex:1;min-width:0}
  .name-line{display:flex;align-items:center;gap:8px}
  .name{font-weight:700;color:#0b1220;max-width:75%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .badge{font-size:.72rem;background:var(--badge);color:var(--badge-t);border:1px solid var(--badge-b);border-radius:999px;padding:2px 8px}
  .preview{font-size:.9rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
  .meta{margin-left:auto;text-align:right}
  .time{font-size:.82rem;color:#94a3b8}
  .agent{margin-top:4px;font-size:.8rem;color:#94a3b8}
  .empty{padding:28px;color:var(--muted);text-align:center}

  /* Responsive */
  @media (max-width:768px){
    .inbox-shell{margin:0;padding:0}
    .inbox{border-radius:0}
    .search{min-width:0;flex:1}
    .name{max-width:60%}
  }

  /* Chip "N nuevos" flotante */
  .new-indicator{
    position:sticky; top:0; z-index:5; display:none; justify-content:center;
    padding:6px 0; background:linear-gradient(180deg,#ffffff,rgba(255,255,255,.6));
    border-bottom:1px solid var(--line);
  }
</style>

<div class="inbox-shell" style="margin-top:86px;">
  <div class="inbox">
    <div class="inbox-top">
      <div class="inbox-title">📨 Bandeja de entrada</div>
      <div class="top-right">
        @isset($inboxAvailability)
          <span class="chip {{ $inboxAvailability ? 'ok' : 'warn' }}"><span class="dot"></span>{{ $inboxAvailability ? 'En atención' : 'Fuera de línea' }}</span>
        @endisset
        <div class="search">🔎 <input id="search" type="text" placeholder="Buscar por nombre, número o mensaje…"></div>
      </div>
    </div>

    <!-- Chip de “N nuevos” (aparece sólo cuando difiramos inserciones) -->
    <div id="new-indicator" class="new-indicator">
      <span id="new-chip" class="chip action">⬆️ Ver <b id="new-count">0</b> nuevo(s)</span>
    </div>

    <div id="list" class="list">
      @forelse($threads as $i => $t)
        @php
          $peer   = $getPeer($t);
          if (!$peer) { continue; }

          $display   = $getDisplayName($peer) ?? $formatMsisdn($peer);
          $avatarUrl = "https://ui-avatars.com/api/?name=".urlencode($display)."&background=16a34a&color=fff&rounded=true&size=64";

          $lastAt = $getLastAt($t);
          $iso    = $lastAt ? $lastAt->utc()->toIso8601String() : null;

          $lastText = $t->last_in_text ?? $t->last_text ?? $t->preview ?? '—';
          $unread   = (int) ($t->unread_count ?? 0);
          $agent    = $t->agent_name ?? null;
          $handover = !empty($t->handover);

          $peerKey = preg_replace('/\D+/', '', $peer);
        @endphp

        <a class="row {{ $unread > 0 ? 'unread' : '' }}"
           href="{{ route('wa.chat', $peer) }}"
           data-peer="{{ $peerKey }}"
           data-ts="{{ $iso }}"
           data-display="{{ e($display) }}"
           data-name="{{ Str::lower($display) }}"
           data-number="{{ Str::lower($formatMsisdn($peer)) }}"
           data-preview="{{ Str::lower($lastText) }}">
          <div class="avatar">
            <img src="{{ $avatarUrl }}" alt="{{ $display }}">
          </div>

          <div class="info">
            <div class="name-line">
              <div class="name">{{ $display }}</div>
              @if($unread > 0)
                <span class="badge" data-badge="unread">{{ $unread }} nuevo{{ $unread > 1 ? 's' : '' }}</span>
              @endif
              @if($handover)
                <span class="badge" data-badge="handover">En atención</span>
              @endif
            </div>
            <div class="preview" data-role="preview">{{ \Illuminate\Support\Str::limit($lastText, 110) }}</div>
          </div>

          <div class="meta">
            <div class="time" data-ts="{{ $iso }}"></div>
            @if($agent)
              <div class="agent" data-role="agent">👤 {{ $agent }}</div>
            @else
              <div class="agent" data-role="agent" style="display:none;"></div>
            @endif
          </div>
        </a>
      @empty
        <div class="empty">Aún no hay conversaciones.</div>
      @endforelse
    </div>
  </div>
</div>

@php
  $POLL_URL = $inboxPollUrl ?: null;
@endphp

<script>
  /* =================== Búsqueda local =================== */
  const q = document.getElementById('search');
  const list = document.getElementById('list');
  q?.addEventListener('input', () => {
    const v = q.value.trim().toLowerCase();
    list.querySelectorAll('.row').forEach(row => {
      const hay = row.dataset.name.includes(v) ||
                  row.dataset.number.includes(v) ||
                  row.dataset.preview.includes(v);
      row.style.display = hay ? 'flex' : 'none';
    });
  });

  /* =================== Tiempo MX =================== */
  const MX_TZ  = 'America/Mexico_City';
  const LOCALE = 'es-MX';

  function sameDayMX(d1, d2){
    const fmt = (d) => new Intl.DateTimeFormat('en-CA', { timeZone: MX_TZ, year:'numeric', month:'2-digit', day:'2-digit' }).format(d);
    return fmt(d1) === fmt(d2);
  }

  function renderTimes(){
    const now = new Date();
    document.querySelectorAll('.time[data-ts]').forEach(el => {
      const iso = el.dataset.ts;
      if (!iso) { el.textContent=''; return; }
      const d = new Date(iso);
      if (Number.isNaN(d.getTime())) { el.textContent = ''; return; }
      if (sameDayMX(d, now)){
        el.textContent = new Intl.DateTimeFormat(LOCALE,{
          hour:'2-digit', minute:'2-digit', hour12:false, timeZone: MX_TZ
        }).format(d);
      } else {
        el.textContent = new Intl.DateTimeFormat(LOCALE,{
          day:'2-digit', month:'2-digit', year:'2-digit', timeZone: MX_TZ
        }).format(d);
      }
    });
  }
  renderTimes();
  setInterval(renderTimes, 60000);

  /* =================== Polling en vivo sin parpadeos =================== */
  const POLL_URL = @json($POLL_URL);
  const RELOAD_FALLBACK = !POLL_URL;

  // Indicador “N nuevos”
  const newIndicator = document.getElementById('new-indicator');
  const newChip = document.getElementById('new-chip');
  const newCountEl = document.getElementById('new-count');
  let pending = new Map(); // peerDigits -> payload

  // Cache de nombres del primer render
  const nameCache = {};
  document.querySelectorAll('.row[data-peer][data-display]').forEach(r=>{
    nameCache[r.dataset.peer] = r.dataset.display;
  });

  // Detectar si estás “arriba”
  let pinnedToTop = true;
  function isNearTop(t=60){
    return (window.scrollY || document.documentElement.scrollTop || 0) <= t;
  }
  function onScroll(){
    const nowTop = isNearTop();
    if (nowTop && pending.size){
      flushPending(); // si subiste arriba, insertamos lo pendiente
    }
    pinnedToTop = nowTop;
  }
  window.addEventListener('scroll', onScroll, {passive:true});
  pinnedToTop = isNearTop();

  // Construcción de filas
  function formatMsisdnClient(msisdn){
    const d = (msisdn||'').replace(/\D+/g,'');
    if (!d) return '';
    if (d.startsWith('521')) return '+52 ' + d.slice(3);
    if (d.startsWith('52'))  return '+52 ' + d.slice(2);
    return '+' + d;
  }
  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
  function previewLimit(s, n=110){ s = s || '—'; return s.length>n ? s.slice(0,n-1)+'…' : s; }
  function avatarUrlByName(name){ return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=16a34a&color=fff&rounded=true&size=64`; }
  function rowSelector(peerDigits){ return `.row[data-peer="${peerDigits}"]`; }

  const chatUrlTpl = @json(route('wa.chat', 'PEER_PLACEHOLDER'));

  function buildRowHtml(t, withIntroAnim=false){
    const peerDigits = (t.peer||'').replace(/\D+/g,'');
    const display    = t.display_name || nameCache[peerDigits] || formatMsisdnClient(t.peer);
    const iso        = t.last_at || '';
    const unread     = Number(t.unread_count || 0);
    const handover   = !!t.handover;
    const agent      = t.agent_name || '';
    const lastText   = t.last_text || '—';
    const preview    = previewLimit(lastText);

    nameCache[peerDigits] = display;

    const href = chatUrlTpl.replace('PEER_PLACEHOLDER', encodeURIComponent(t.peer));

    return `
      <a class="row ${withIntroAnim?'row--new':''} ${unread>0?'unread':''}"
         href="${href}"
         data-peer="${peerDigits}"
         data-ts="${iso}"
         data-display="${escapeHtml(display)}"
         data-name="${escapeHtml(display.toLowerCase())}"
         data-number="${escapeHtml(formatMsisdnClient(t.peer).toLowerCase())}"
         data-preview="${escapeHtml((lastText||'').toLowerCase())}">
        <div class="avatar">
          <img src="${avatarUrlByName(display)}" alt="${escapeHtml(display)}">
        </div>

        <div class="info">
          <div class="name-line">
            <div class="name">${escapeHtml(display)}</div>
            ${unread>0?`<span class="badge" data-badge="unread">${unread} nuevo${unread>1?'s':''}</span>`:''}
            ${handover?`<span class="badge" data-badge="handover">En atención</span>`:''}
          </div>
          <div class="preview" data-role="preview">${escapeHtml(preview)}</div>
        </div>

        <div class="meta">
          <div class="time" data-ts="${iso}"></div>
          <div class="agent" data-role="agent" ${agent?'':'style="display:none;"'}>${agent?('👤 '+escapeHtml(agent)) : ''}</div>
        </div>
      </a>
    `;
  }

  function updateRowContent(row, t){
    const peerDigits = (t.peer||'').replace(/\D+/g,'');
    const display    = t.display_name || nameCache[peerDigits] || formatMsisdnClient(t.peer);
    const iso        = t.last_at || '';
    const unread     = Number(t.unread_count || 0);
    const handover   = !!t.handover;
    const agent      = t.agent_name || '';
    const lastText   = t.last_text || '—';
    const preview    = previewLimit(lastText);

    nameCache[peerDigits] = display;

    row.dataset.ts = iso;
    row.dataset.display = display;
    row.dataset.name = display.toLowerCase();
    row.dataset.number = formatMsisdnClient(t.peer).toLowerCase();
    row.dataset.preview = (lastText||'').toLowerCase();
    row.classList.toggle('unread', unread>0);

    // badges
    const bUnread = row.querySelector('.badge[data-badge="unread"]');
    if (unread>0){
      if (bUnread) bUnread.textContent = `${unread} nuevo${unread>1?'s':''}`;
      else row.querySelector('.name-line').insertAdjacentHTML('beforeend', `<span class="badge" data-badge="unread">${unread} nuevo${unread>1?'s':''}</span>`);
    } else if (bUnread){ bUnread.remove(); }

    const bHandover = row.querySelector('.badge[data-badge="handover"]');
    if (handover){
      if (!bHandover) row.querySelector('.name-line').insertAdjacentHTML('beforeend', `<span class="badge" data-badge="handover">En atención</span>`);
    } else if (bHandover){ bHandover.remove(); }

    const prevEl = row.querySelector('[data-role="preview"]');
    if (prevEl) prevEl.textContent = preview;

    const agentEl = row.querySelector('[data-role="agent"]');
    if (agentEl){
      if (agent){ agentEl.style.display=''; agentEl.textContent = `👤 ${agent}`; }
      else { agentEl.style.display='none'; agentEl.textContent=''; }
    }

    const timeEl = row.querySelector('.time');
    if (timeEl) timeEl.dataset.ts = iso;
  }

  function upsertThread(t){
    const peerDigits = (t.peer||'').replace(/\D+/g,'');
    if (!peerDigits) return;

    const selector = rowSelector(peerDigits);
    let row = document.querySelector(selector);

    if (!row){
      // Si no estás arriba, NO reordenamos: diferimos la inserción y mostramos chip
      if (!pinnedToTop){
        pending.set(peerDigits, t);
        newCountEl.textContent = pending.size;
        newIndicator.style.display = 'flex';
        return;
      }
      // Estás arriba: insertamos al principio con animación suave sólo una vez
      const html = buildRowHtml(t, true);
      list.insertAdjacentHTML('afterbegin', html);
      requestAnimationFrame(()=>{
        const just = list.querySelector(selector);
        if (just) just.classList.remove('row--new'); // evitas re-animación en futuros updates
      });
      return;
    }

    // Actualizamos contenido sin recrear ni mover
    updateRowContent(row, t);

    // Sólo si estás arriba reordenamos la lista
    if (pinnedToTop){
      reorderListByTs();
    }
  }

  function flushPending(){
    if (!pending.size) return;
    // Evita parpadeos durante inserciones múltiples
    list.classList.add('no-anim');

    // Insertamos al principio en orden por ts desc
    const items = Array.from(pending.values()).sort((a,b)=>{
      return new Date(b.last_at||0) - new Date(a.last_at||0);
    });
    pending.clear();
    newIndicator.style.display = 'none';
    newCountEl.textContent = '0';

    // Guardamos offset actual respecto al top de la lista
    const prevY = window.scrollY || document.documentElement.scrollTop || 0;

    items.forEach(t=>{
      const selector = rowSelector((t.peer||'').replace(/\D+/g,''));
      if (!document.querySelector(selector)){
        const html = buildRowHtml(t, true);
        list.insertAdjacentHTML('afterbegin', html);
      } else {
        // si mientras tanto apareció, sólo actualiza
        updateRowContent(document.querySelector(selector), t);
      }
    });

    reorderListByTs();

    // Restauramos scroll para que no “salte”
    window.scrollTo({top: prevY});
    requestAnimationFrame(()=>{
      list.classList.remove('no-anim');
      // quitar clase de animación a los recién insertados
      list.querySelectorAll('.row--new').forEach(r=> r.classList.remove('row--new'));
      renderTimes();
    });
  }

  newChip.addEventListener('click', flushPending);

  function reorderListByTs(){
    // Reordenamos sólo si estás arriba (para no moverle el piso al usuario)
    if (!pinnedToTop) return;

    const rows = Array.from(list.querySelectorAll('.row[data-ts]'));
    rows.sort((a,b)=>{
      const ta = new Date(a.dataset.ts || 0).getTime() || 0;
      const tb = new Date(b.dataset.ts || 0).getTime() || 0;
      return tb - ta;
    });

    list.classList.add('no-anim');
    const prevY = window.scrollY || document.documentElement.scrollTop || 0;
    rows.forEach(r => list.appendChild(r));
    window.scrollTo({top: prevY});
    requestAnimationFrame(()=> list.classList.remove('no-anim'));
  }

  function maxIsoFromDom(){
    let max = 0;
    list.querySelectorAll('.row[data-ts]').forEach(r=>{
      const t = new Date(r.dataset.ts || 0).getTime();
      if (!isNaN(t) && t>max) max = t;
    });
    return max ? new Date(max).toISOString() : '';
  }

  // ============ Polling ============
  let etag = null;
  let pollingTimer = null;

  async function poll(){
    if (RELOAD_FALLBACK){
      // fallback súper simple si no hay endpoint JSON
      return; // evita recarga para no “saltar”
    }
    try{
      const since = maxIsoFromDom();
      const headers = {'Accept':'application/json'};
      if (etag) headers['If-None-Match'] = etag;

      const res = await fetch(`${POLL_URL}?since=${encodeURIComponent(since)}`, {
        headers,
        cache: 'no-store',
        credentials: 'same-origin'
      });

      if (res.status === 304) return;
      if (!res.ok){
        console.warn('Polling inbox error', res.status);
        return;
      }

      etag = res.headers.get('ETag') || etag;
      const data = await res.json();
      if (Array.isArray(data)){
        // Preservar postura de scroll global
        const prevY = window.scrollY || document.documentElement.scrollTop || 0;

        data.forEach(upsertThread);

        // Si estás arriba, refresca hora; si no, lo hacemos al flush
        if (pinnedToTop) renderTimes();

        // Restaura scroll si hicimos cambios menores
        window.scrollTo({top: prevY});
      }
    }catch(err){
      console.error('poll exception', err);
    }
  }

  function startPolling(){
    if (pollingTimer) clearInterval(pollingTimer);
    poll(); // primer tiro
    pollingTimer = setInterval(()=>{
      if (document.hidden) return;
      poll();
    }, 2500);
  }

  document.addEventListener('visibilitychange', ()=>{ if (!document.hidden) poll(); });

  startPolling();
</script>
@endsection
