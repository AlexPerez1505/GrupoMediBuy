@extends('layouts.app') 

@section('title', 'Préstamos')
@section('titulo', 'Préstamos')

@section('content')
<link rel="icon" type="image/png" href="{{ asset('images/logoai.png') }}?v=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<style>
  :root{
    --bg:#f5f7fb;
    --ink:#111827;
    --muted:#4b5563;
    --line:rgba(17,24,39,.12);
    --accent1:#bfe3ff; --accent2:#c9f0dd; --accent3:#ffe1c6;
    --brand:#24843F;
    --cardRadius:22px;
  }
  *{ box-sizing:border-box }
  body{ font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,sans-serif; color:var(--ink); background:var(--bg); }

  .id-wrap{
    min-height: calc(100dvh - 140px);
    display:grid; place-items:center;
    padding: clamp(16px, 3.5vw, 28px);
    padding-top: calc(16px + env(safe-area-inset-top));
    background:
      radial-gradient(65% 52% at 18% 12%, rgba(191,227,255,.45), transparent 60%),
      radial-gradient(58% 54% at 82% 18%, rgba(201,240,221,.40), transparent 62%),
      radial-gradient(80% 64% at 50% 85%, rgba(255,225,198,.35), transparent 55%);
  }

  /* ====== CREDENCIAL ====== */
  .id-cred{
    position:relative;
    width:min(980px, 92vw);
    aspect-ratio: 16 / 10;
    perspective:1400px;
    -webkit-tap-highlight-color:transparent;
    touch-action: pan-y;
  }
  .id-cred .glow{
    position:absolute; inset:-2px; border-radius:calc(var(--cardRadius) + 2px);
    background:linear-gradient(135deg,var(--accent1),var(--accent2),var(--accent3));
    filter:blur(10px); opacity:.28; pointer-events:none; transition:.3s ease;
  }
  .id-cred:hover .glow{ opacity:.38; filter:blur(14px); }

  .stack{ position:absolute; inset:0; border-radius:var(--cardRadius); transform-style:preserve-3d; transition:transform .4s ease; will-change:transform; }

  .card-face{
    position:absolute; inset:0; border-radius:var(--cardRadius); overflow:hidden;
    border:1px solid var(--line);
    background:
      radial-gradient(140% 120% at 100% 0%, rgba(191,227,255,.55), rgba(201,240,221,.42) 48%, rgba(255,225,198,.40) 85%),
      linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.90));
    box-shadow:0 30px 70px rgba(16,24,40,.15);
    display:grid;
    grid-template-columns:minmax(260px, 320px) 1fr;
    grid-template-rows:auto 1fr auto;
    color:var(--ink);
  }
  .card-face::before{
    content:""; position:absolute; inset:0; pointer-events:none;
    background-image: radial-gradient(rgba(17,24,39,.06) 1px, transparent 1px);
    background-size: 10px 10px; opacity:.2;
  }
  .card-face::after{
    content:""; position:absolute; inset:-2px; pointer-events:none; opacity:.35;
    background:linear-gradient(120deg, transparent 0 40%, rgba(255,255,255,.55) 55%, transparent 72% 100%);
    transform:translateX(-120%) rotate(2deg);
    transition:transform 1.2s cubic-bezier(.2,.6,.2,1);
  }
  .id-cred:hover .card-face::after{ transform:translateX(0) rotate(2deg); }

  .id-head{
    grid-column:1/-1; display:flex; align-items:center; justify-content:space-between;
    padding:16px 22px; border-bottom:1px solid var(--line);
    background:linear-gradient(90deg, rgba(36,132,63,.10), transparent);
  }
  .brand{ display:flex; gap:12px; align-items:center; font-weight:700; letter-spacing:.3px; }
  .brand img{ width:34px; height:34px; border-radius:8px; object-fit:cover; }
  .tag{ font-size:.8rem; padding:6px 10px; border-radius:999px; border:1px solid var(--line); color:var(--muted); background:rgba(0,0,0,.02); letter-spacing:.12rem; text-transform:uppercase; }

  .left{
    grid-row:2/span 1; border-right:1px solid var(--line); padding:18px 20px;
    display:grid; gap:16px; align-content:start;
  }
  .avatar-wrap{ position:relative; width:200px; height:200px; margin:auto; }
  .avatar{ width:100%; height:100%; border-radius:18px; object-fit:cover; border:1px solid rgba(17,24,39,.10); cursor:pointer; }
  .avatar-tip{ position:absolute; bottom:-10px; left:50%; transform:translateX(-50%); font-size:.8rem; color:#0b5130; background:#e8f6ee; border:1px dashed #98d6b1; padding:6px 10px; border-radius:10px; }

  .u-name{ font-size:1.3rem; font-weight:800; text-align:center; }
  .u-role{ text-align:center; color:var(--muted); font-size:.92rem; }

  .chips{ display:grid; gap:10px; }
  .chip{ display:flex; justify-content:space-between; padding:10px 12px; border-radius:12px; border:1px dashed var(--line); background:#ffffffa6; backdrop-filter:saturate(1.1) blur(2px); }
  .chip b{ font-weight:800; }

  .right{ grid-row:2/span 1; padding:18px 22px; display:grid; grid-template-rows:auto 1fr auto; gap:14px; }
  .grid-info{ display:grid; grid-template-columns:1fr auto; gap:12px 14px; align-content:start; }
  .info-row{ display:flex; align-items:center; gap:10px; border:1px solid var(--line); border-radius:12px; padding:10px 12px; background:#ffffffa6; backdrop-filter:saturate(1.1) blur(2px); }
  .info-row strong{ min-width:170px; font-weight:800; }
  .info-row .value{ font-weight:700; }

  .modify-section{ transition: grid-template-rows .35s ease, opacity .35s ease; }
  .hidden{ display:none; }
  .input-container{ width:100%; }
  .input-wrapper{ display:flex; gap:8px; align-items:center; border:1px solid var(--line); border-radius:12px; padding:6px; background:#fff; }
  .input-field{ flex:1; background:transparent; border:0; outline:none; color:var(--ink); padding:10px 12px; font-size:1rem; }
  .btn-edit,.btn-save{ appearance:none; border:0; background:transparent; cursor:pointer; padding:6px; border-radius:10px; line-height:0; transition:.2s ease; }
  .btn-edit:hover,.btn-save:hover{ background:rgba(17,24,39,.06); }
  .btn-edit img,.btn-save img{ width:20px; height:20px; }

  .id-foot{ grid-column:1/-1; border-top:1px solid var(--line); padding:12px 16px; display:flex; gap:10px; justify-content:flex-end; align-items:center; background:linear-gradient(90deg, transparent, rgba(191,227,255,.22)); }
  .cred-btn{ appearance:none; border:1px solid var(--line); color:#0e1421; background:linear-gradient(180deg,#fff,#eef3ff); border-radius:12px; padding:10px 14px; font-weight:800; transition:.2s ease; cursor:pointer; }
  .cred-btn.brand{ background:linear-gradient(180deg,#2ea750,#248a4b); color:#fff; border-color:rgba(36,132,63,.35); }

  .back{ transform:rotateY(180deg); }
  .stack.flip .front{ transform:rotateY(180deg); }
  .stack.flip .back{ transform:rotateY(360deg); }
  .front,.back{ backface-visibility:hidden; transform-style:preserve-3d; transition:transform .6s cubic-bezier(.2,.7,.2,1); }

  /* ====== SOLO MÓVIL: todo en columna (NO tocar) ====== */
  @media (max-width: 768px){
    .id-cred{ aspect-ratio:auto; height:var(--cardH, auto); width:100vw; }
    .card-face{ display:flex !important; flex-direction:column !important; }
    .left{ border-right:0; border-bottom:1px solid var(--line); padding:16px; }
    .right{ padding:16px; }
    .avatar-wrap{ width:132px; height:132px; }
    .grid-info{ grid-template-columns:1fr !important; }
    .info-row strong{ min-width:auto; width:100%; }
    .id-foot{ justify-content:stretch; flex-direction:column; }
    .id-foot .cred-btn{ width:100%; }
  }
  @media (max-width: 360px){
    .avatar-wrap{ width:120px; height:120px; }
  }

  /* ====== DESKTOP: boost de contraste (solo >=992px) ====== */
  @media (min-width: 992px){
    .id-cred{ width:min(1120px, 96vw); aspect-ratio: 16 / 9; }       /* más ancho */
    .card-face{ grid-template-columns: 360px 1fr; }                   /* columna izq. más amplia */
    .left{ border-right:1px solid #e5e7eb; }
    .card-face::before{ opacity:.08; }                                /* patrón más sutil */
    .grid-info{ grid-template-columns: repeat(2, minmax(0,1fr)); }    /* dos columnas iguales */
    .info-row{
      background:#fff; 
      border:1px solid #d0d7e2;
      box-shadow:0 6px 16px rgba(16,24,40,.06);
    }
    .chip{
      background:#fff; border-color:#d0d7e2;
      box-shadow:0 4px 10px rgba(16,24,40,.05);
    }
    .u-name{ font-size:1.6rem; }
  }
</style>

<div class="id-wrap" style="margin-top:90px">
  <div class="id-cred" id="credencial" aria-live="polite">
    <div class="glow"></div>

    <div class="stack" id="stack">
      {{-- ====== FRENTE ====== --}}
      <section class="card-face front">
        <header class="id-head">
          <div class="brand">
            <img src="{{ asset('images/logoai.png') }}" alt="Logo">
            <span>Credencial de Empleado</span>
          </div>
          <span class="tag">Préstamos</span>
        </header>

        <aside class="left">
          <div class="avatar-wrap">
            <img class="avatar" id="avatarClick"
                 src="{{ Auth::user()->imagen ? asset('storage/' . Auth::user()->imagen) : asset('images/default-profile.png') }}"
                 alt="Foto de {{ Auth::user()->name }}" title="Haz clic para actualizar foto">
            <div class="avatar-tip">Clic para actualizar foto</div>

            <form action="{{ route('perfil.updatePhoto') }}" method="POST" enctype="multipart/form-data" id="photo-form">
              @csrf
              <input type="file" name="imagen" id="file-input" accept="image/*" class="d-none">
            </form>
          </div>

          <div class="u-name">{{ Auth::user()->name }}</div>
          <div class="u-role">{{ Auth::user()->puesto ?? 'Puesto no registrado' }}</div>

          <div class="chips">
            <div class="chip"><span>No. Usuario</span> <b>{{ Auth::user()->nomina ?? 'No registrado' }}</b></div>
            <div class="chip"><span>Cargo</span> <b>{{ Auth::user()->cargo ?? 'No registrado' }}</b></div>
          </div>
        </aside>

        <main class="right">
          <div class="grid-info">
            <div class="info-row" id="info-phone">
              <strong>Teléfono</strong>
              <div class="value">{{ Auth::user()->phone ?? 'No registrado' }}</div>
              <button class="btn-edit" type="button" aria-label="Editar teléfono" onclick="toggleEditPhone()">
                <img src="{{ asset('images/boligrafo.png') }}" alt="">
              </button>
            </div>

            <div id="edit-phone" class="modify-section hidden">
              <form action="{{ route('perfil.update') }}" method="POST" class="input-container">
                @csrf
                <div class="input-wrapper">
                  <input type="text" name="phone" id="new-phone" class="input-field" placeholder="Nuevo teléfono" inputmode="tel">
                  <button type="submit" class="btn-save" aria-label="Guardar teléfono">
                    <img src="{{ asset('images/usuario.png') }}" alt="">
                  </button>
                </div>
              </form>
            </div>

            <div class="info-row" id="info-email">
              <strong>Correo</strong>
              <div class="value">{{ Auth::user()->email }}</div>
              <button class="btn-edit" type="button" aria-label="Editar correo" onclick="toggleEditEmail()">
                <img src="{{ asset('images/boligrafo.png') }}" alt="">
              </button>
            </div>

            <div id="edit-email" class="modify-section hidden">
              <form action="{{ route('perfil.update') }}" method="POST" class="input-container">
                @csrf
                <div class="input-wrapper">
                  <input type="email" name="email" id="new-email" class="input-field" placeholder="Nuevo correo">
                  <button type="submit" class="btn-save" aria-label="Guardar correo">
                    <img src="{{ asset('images/usuario.png') }}" alt="">
                  </button>
                </div>
              </form>
            </div>

            <div class="info-row"><strong>Vacaciones Disp.</strong><div class="value">{{ Auth::user()->vacaciones_disponibles ?? '0' }} días</div></div>
            <div class="info-row"><strong>Vacaciones Usadas</strong><div class="value">{{ Auth::user()->vacaciones_utilizadas ?? '0' }} días</div></div>
            <div class="info-row"><strong>Permisos Disp.</strong><div class="value">{{ Auth::user()->permisos ?? '0' }}</div></div>
          </div>

          <div></div>

          <footer class="id-foot">
            <a href="{{ route('auth.change-password') }}" class="d-block">
              <button class="cred-btn brand" type="button">Cambiar contraseña</button>
            </a>
            <a href="{{ route('mi-historial') }}" class="d-block">
              <button class="cred-btn" type="button">Ver historial</button>
            </a>
          </footer>
        </main>
      </section>

      {{-- ====== REVERSO ====== --}}
      <section class="card-face back" aria-hidden="true">
        <header class="id-head">
          <div class="brand">
            <img src="{{ asset('images/logoai.png') }}" alt="Logo">
            <span>Información de contacto</span>
          </div>
          <span class="tag">Reverso</span>
        </header>

        <aside class="left">
          <div class="chips">
            <div class="chip"><span>Nombre</span> <b>{{ Auth::user()->name }}</b></div>
            <div class="chip"><span>Cargo</span> <b>{{ Auth::user()->cargo ?? 'No registrado' }}</b></div>
            <div class="chip"><span>Puesto</span> <b>{{ Auth::user()->puesto ?? 'No registrado' }}</b></div>
            <div class="chip"><span>No. Usuario</span> <b>{{ Auth::user()->nomina ?? 'No registrado' }}</b></div>
          </div>
        </aside>

        <main class="right">
          <div class="grid-info">
            <div class="info-row"><strong>Teléfono</strong><div class="value">{{ Auth::user()->phone ?? 'No registrado' }}</div></div>
            <div class="info-row"><strong>Correo</strong><div class="value">{{ Auth::user()->email }}</div></div>
            <div class="info-row"><strong>Vacaciones (Disp/Usadas)</strong><div class="value">{{ Auth::user()->vacaciones_disponibles ?? '0' }} / {{ Auth::user()->vacaciones_utilizadas ?? '0' }} días</div></div>
            <div class="info-row"><strong>Permisos</strong><div class="value">{{ Auth::user()->permisos ?? '0' }}</div></div>
          </div>

          <div></div>

          <footer class="id-foot">
            <a href="{{ route('mi-historial') }}" class="d-block">
              <button class="cred-btn brand" type="button">Ir a Historial</button>
            </a>
          </footer>
        </main>
      </section>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  /* Abrir input de foto */
  (function(){
    const file=document.getElementById('file-input');
    const form=document.getElementById('photo-form');
    const avatar=document.getElementById('avatarClick');
    if(avatar&&file&&form){ avatar.addEventListener('click',()=>file.click()); file.addEventListener('change',()=>form.submit()); }
  })();

  /* Altura dinámica en móvil para que no se recorte al voltear */
  function faceIsBack(){ return document.getElementById('stack')?.classList.contains('flip'); }
  let hRaf=null;
  function setCardHeight(){
    const cred=document.getElementById('credencial');
    if(!cred) return;
    const visible=faceIsBack()? cred.querySelector('.back') : cred.querySelector('.front');
    const h=Math.max(visible.scrollHeight,420);
    cred.style.setProperty('--cardH', h+'px');
  }
  function requestCardHeight(){ if(hRaf) cancelAnimationFrame(hRaf); hRaf=requestAnimationFrame(setCardHeight); }
  window.addEventListener('resize',requestCardHeight);
  document.addEventListener('DOMContentLoaded',requestCardHeight);

  /* Edit inline */
  function toggleEditPhone(){ document.getElementById('edit-phone')?.classList.toggle('hidden'); requestCardHeight(); }
  function toggleEditEmail(){ document.getElementById('edit-email')?.classList.toggle('hidden'); requestCardHeight(); }

  /* Tilt 3D (curva al pasar el mouse) */
  (function(){
    const cred=document.getElementById('credencial');
    const stack=document.getElementById('stack');
    if(!cred||!stack) return;
    let bounds=null; function u(){ bounds=cred.getBoundingClientRect(); }
    u(); window.addEventListener('resize',u);
    const max=8;
    cred.addEventListener('mousemove', e=>{
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      const x=((e.clientX-bounds.left)/bounds.width)-.5;
      const y=((e.clientY-bounds.top)/bounds.height)-.5;
      stack.style.transform=`rotateX(${y*-2*max}deg) rotateY(${x*2*max}deg)`;
    });
    cred.addEventListener('mouseleave', ()=>{ stack.style.transform='rotateX(0) rotateY(0)'; });
  })();

  /* Voltear con clic/tap + swipe + teclado */
  (function(){
    const cred=document.getElementById('credencial');
    const stack=document.getElementById('stack');
    if(!cred||!stack) return;

    cred.addEventListener('click',e=>{
      const tag=(e.target.tagName||'').toLowerCase();
      if(['button','a','input','label','textarea','select'].includes(tag)) return;
      stack.classList.toggle('flip'); requestCardHeight();
    });

    let sx=0,sy=0;
    cred.addEventListener('touchstart',e=>{ sx=e.touches[0].clientX; sy=e.touches[0].clientY; },{passive:true});
    cred.addEventListener('touchend',e=>{
      const dx=e.changedTouches[0].clientX-sx, dy=e.changedTouches[0].clientY-sy;
      if(Math.abs(dx)>40 && Math.abs(dy)<60){ stack.classList.toggle('flip', dx<0); requestCardHeight(); }
    },{passive:true});

    document.addEventListener('keydown',e=>{
      if(e.key===' '||e.key.toLowerCase()==='f'){ e.preventDefault(); stack.classList.toggle('flip'); requestCardHeight(); }
    });
  })();
</script>
@endsection
