{{-- resources/views/productos-cards.blade.php --}}
@extends('layouts.app')
@section('title', 'Productos')
@section('titulo', 'Productos')
@section('content')

<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#eaebec;font-family:"Open Sans",sans-serif;}
.card-scope{position:relative;width:340px;height:500px}

/* ===== Buscador minimal (empieza sólo con la lupa) ===== */
.search-wrap{display:flex;justify-content:center;margin:22px 0 8px;}
.search{
  --h:46px;
  display:flex;align-items:center;
  width:46px;height:var(--h);border-radius:999px;
  background:#fff;box-shadow:0 6px 18px rgba(18,38,63,.08);
  overflow:hidden;transition:width .25s ease, box-shadow .25s ease;
}
.search.open{width:min(560px,92vw);box-shadow:0 10px 26px rgba(18,38,63,.12);}
.search button{
  width:46px;height:var(--h);border:none;background:transparent;cursor:pointer;
  display:grid;place-items:center;color:#7a7f87;
}
.search input{
  flex:1;border:none;height:var(--h);outline:none;font-size:15px;
  width:0;opacity:0;padding:0;background:transparent;color:#2a2e35;
  transition:width .25s ease, opacity .2s ease, padding .2s ease;
}
.search.open input{width:100%;opacity:1;padding:0 16px 0 4px;}
.search .clear{width:0;overflow:hidden;transition:width .2s ease;color:#a2a7ae;background:transparent;border:none;cursor:pointer;}
.search.open .clear{width:40px;}
.search svg{width:20px;height:20px}

/* --- Product Card ---- */
#make-3D-space{position:relative;perspective:800px;width:340px;height:500px;transform-style:preserve-3d;margin:0 auto;}
#product-front,#product-back{width:335px;height:500px;background:#fff;position:absolute;left:-5px;top:-5px;transition:all 100ms ease-out;}
#product-back{display:none;transform:rotateY(180deg);}
#product-card{width:325px;height:490px;position:absolute;top:10px;left:10px;overflow:hidden;transform-style:preserve-3d;transition:100ms ease-out;background:#fff;}
#product-card.animate{top:5px;left:5px;width:335px;height:500px;box-shadow:0px 13px 21px -5px rgba(0,0,0,0.3);}
div#product-card.flip-10{transform:rotateY(-10deg);transition:50ms ease-out;}
div#product-card.flip90{transform:rotateY(90deg);transition:100ms ease-in;}
div#product-card.flip190{transform:rotateY(190deg);transition:100ms ease-out;}
div#product-card.flip180{transform:rotateY(180deg);transition:150ms ease-out;}

/* ====== MEDIA AREA (frente) ====== */
.media-area{position:relative;width:100%;height:330px;overflow:hidden;background:#e9ecef;}
.media-area img{display:block;width:100%;height:100%;object-fit:cover;}
.image_overlay{position:absolute;inset:0;background:#48daa1;opacity:0;transition:all 200ms ease-out;}
#product-card.animate .image_overlay{opacity:.7;}
#view_details{
  position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
  border:2px solid #fff;color:#fff;font-size:19px;text-align:center;text-transform:uppercase;
  font-weight:700;padding:10px 18px;min-width:172px;border-radius:2px;opacity:0;transition:all 200ms ease-out;
}
#product-card.animate #view_details{opacity:1;font-size:15px;min-width:152px;}
#view_details:hover{background:#fff;color:#48cfad;cursor:pointer;}

/* Texto / stats */
.stats-container{
  background:#fff;position:absolute;left:0;width:300px;height:450px;
  padding:27px 55px 5px;transition:all 200ms ease-out;top:330px;
}
#product-card.animate .stats-container{top:240px;}
.stats-container .product_name{font-size:22px;color:#393c45;display:block;}
.stats-container p{font-size:16px;color:#b1b1b3;padding:2px 0 20px 0;}
.stats-container .product_price{float:right;color:#48cfad;font-size:22px;font-weight:600;}
.product-options strong{font-weight:700;color:#393c45;font-size:14px;display:block;margin-top:6px;}
.product-options span{color:#969699;font-size:14px;display:block;margin-bottom:8px;}

div.shadow{width:335px;height:520px;opacity:0;position:absolute;top:0;left:0;z-index:3;display:none;background:linear-gradient(to right,rgba(0,0,0,.1),rgba(0,0,0,.2));}
#product-back div.shadow{z-index:10;opacity:1;background:linear-gradient(to right,rgba(0,0,0,.2),rgba(0,0,0,.1));}

/* Botón tache */
#flip-back{position:absolute;top:20px;right:20px;width:30px;height:30px;cursor:pointer;z-index:20;}
#cx,#cy{background:#d2d5dc;position:absolute;width:0;top:15px;right:15px;height:3px;transition:all 250ms ease-in-out;}
#flip-back:hover #cx,#flip-back:hover #cy{background:#979ca7;}
#cx.s1,#cy.s1{right:0;width:30px;}
#cy.s2{transform:rotate(50deg);}
#cy.s3{transform:rotate(45deg);}
#cx.s2{transform:rotate(140deg);}
#cx.s3{transform:rotate(135deg);}

/* ===== PARTE TRASERA: 1 sola foto, bonita ===== */
#carousel{width:335px;height:500px;overflow:hidden;position:relative;}
#carousel ul{position:absolute;inset:0;margin:0;padding:0;}
#carousel li{
  width:335px;height:500px;list-style:none;
  display:flex;align-items:center;justify-content:center;
  background:#f6f7f8;padding:16px;
}
#carousel li img{max-width:100%;max-height:100%;object-fit:contain;border-radius:6px;}
.arrows-perspective{display:none}

/* ===== Acciones (Editar + Eliminar) en el back ===== */
.card-actions{
  position:absolute;bottom:18px;right:18px;z-index:25;
  display:flex;gap:10px;align-items:center;
}
.fab{
  width:46px;height:46px;border-radius:50%;
  display:grid;place-items:center;color:#fff;text-decoration:none;border:none;cursor:pointer;
  box-shadow:0 8px 18px rgba(0,0,0,.18);
  opacity:0;transform:translateY(8px);
  transition:opacity .2s ease, transform .2s ease, background .2s ease;
}
.fab--edit{background:#48cfad;}
.fab--edit:hover{background:#34c29e;}
.fab--del{background:#ef5350;}
.fab--del:hover{background:#e53935;}
/* mostrar acciones sólo cuando está el back activo */
#product-card.on-back .fab{opacity:1;transform:none}

/* Grilla contenedora */
.cards-grid{display:flex;flex-wrap:wrap;gap:26px;justify-content:center;padding:18px 10px 30px;}
.btn-custom {
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
    font-weight: bold;
    height: 45px;
    width: 120px;
    display: inline-flex; /* Asegura que <a> y <button> se comporten igual */
    align-items: center;
    justify-content: center;
    text-align: center;
    white-space: nowrap; /* Evita que el texto se desborde */
}
.swal2-popup {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    border-radius: 15px;
}
.swal2-title {
    color: #343a40;
}
.swal2-content {
    color: #495057;
}
.btn-custom-confirm {
    background-color: #4CAF50; /* Verde suave */
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    border: none; /* Eliminar borde */
    margin-right: 10px; /* Separar botones */
    transition: background-color 0.3s ease;
}
.btn-custom-confirm:hover {
    background-color: #45a049; /* Verde un poco más oscuro */
}
.btn-custom-cancel {
    background-color: #DC3545; /* Gris suave */
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    border: none; /* Eliminar borde */
    margin-left: 10px; /* Separar botones */
    transition: background-color 0.3s ease;
}
.btn-custom-cancel:hover {
    background-color: #C82333; /* Gris un poco más oscuro */
}
/* ===== FAB Agregar (esquina inferior derecha) ===== */
.fab-add{
  position:fixed;
  right:22px;
  bottom:calc(22px + env(safe-area-inset-bottom, 0)); /* seguro en móviles con notch */
  z-index:9999;
  width:56px;height:56px;border-radius:50%;
  background:#48cfad;color:#fff;display:grid;place-items:center;
  box-shadow:0 12px 24px rgba(0,0,0,.22);
  text-decoration:none;cursor:pointer;
  transition:transform .08s ease, background .2s ease, box-shadow .2s ease, opacity .2s ease;
}
.fab-add:hover{ background:#34c29e; box-shadow:0 14px 28px rgba(0,0,0,.24); }
.fab-add:active{ transform:translateY(1px); }
.fab-add svg{ width:24px;height:24px; }
@media (max-width: 480px){
  .fab-add{ right:16px; bottom:calc(16px + env(safe-area-inset-bottom, 0)); }
}

</style>

{{-- Buscador --}}
<div class="search-wrap" style="margin-top:120px;">
  <div class="search" id="liveSearch">
    <button id="btnOpen" aria-label="Buscar">
      {{-- Ícono lupa --}}
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="7"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
    </button>
    <input id="q" type="text" placeholder="Buscar por nombre, marca o modelo…" autocomplete="off"/>
    <button class="clear" id="btnClear" aria-label="Limpiar">✕</button>
  </div>
</div>

<div class="cards-grid" id="cardsGrid">
  @forelse($productos as $p)
    @php
      $nombre = $p->tipo_equipo ?? $p->nombre ?? 'Producto';
      $precio = isset($p->precio) ? number_format($p->precio, 2) : '0.00';
      $desc   = trim(($p->marca ?? '').' · '.($p->modelo ?? ''), ' ·');
      $imgRaw = $p->imagen ?? $p->imagen_url ?? $p->foto_url ?? null;
      $img    = $imgRaw
        ? (\Illuminate\Support\Str::startsWith($imgRaw, ['http://','https://']) ? $imgRaw : asset('storage/'.$imgRaw))
        : 'https://via.placeholder.com/800x800.png?text=Producto';
      $searchText = strtolower(($nombre ?? '').' '.($p->marca ?? '').' '.($p->modelo ?? '').' '.($p->descripcion ?? ''));
    @endphp

    <div class="card-scope" data-search="{{ $searchText }}">
      <div id="make-3D-space">
        <div id="product-card">
          {{-- FRONT --}}
          <div id="product-front">
            <div class="shadow"></div>
            <div class="media-area">
              <img src="{{ $img }}" alt="{{ $nombre }}">
              <div class="image_overlay"></div>
              <div id="view_details">Ver Foto</div>
            </div>
            <div class="stats">
              <div class="stats-container">
                <span class="product_price">${{ $precio }}</span>
                <span class="product_name">{{ $nombre }}</span>
                <p>{{ \Illuminate\Support\Str::limit($desc ?: ($p->descripcion ?? ' '), 60) }}</p>
                <div class="product-options">
                  <strong>DETALLES</strong>
                  <span>Marca: {{ $p->marca ?? '—' }} | Modelo: {{ $p->modelo ?? '—' }}</span>
                </div>
              </div>
            </div>
          </div>

          {{-- BACK (1 sola imagen) --}}
          <div id="product-back">
            <div class="shadow"></div>
            <div id="carousel">
              <ul>
                <li><img src="{{ $img }}" alt="foto {{ $nombre }}"></li>
              </ul>
              <div class="arrows-perspective">
                <div class="carouselPrev"><div class="y"></div><div class="x"></div></div>
                <div class="carouselNext"><div class="y"></div><div class="x"></div></div>
              </div>
            </div>
 @if(Auth::user()->hasRole('admin'))
            {{-- Acciones: Editar + Eliminar (lado a lado) --}}
            <div class="card-actions">
              <a class="fab fab--edit" href="{{ route('productos.edit', $p->id) }}" title="Editar {{ $nombre }}">
                {{-- ícono lápiz --}}
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 20h9"/>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                </svg>
              </a>

              <form class="delete-form" action="{{ route('productos.destroy', $p->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="fab fab--del" title="Eliminar {{ $nombre }}">
                  {{-- ícono papelera --}}
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </form>
            </div>
@endif
            {{-- TACHE (volver al frente) --}}
            <div id="flip-back">
              <div id="cy"></div>
              <div id="cx"></div>
            </div>
          </div>

        </div>
      </div>
    </div>
  @empty
    <p>No hay productos.</p>
  @endforelse
   <a href="{{ route('productos.create') }}" class="fab-add" title="Agregar producto" aria-label="Agregar producto">
    {{-- ícono + --}}
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M12 5v14M5 12h14"/>
    </svg>
  </a>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  /* ===== Buscador expandible + filtrado ===== */
  const $search = $('#liveSearch');
  const $q = $('#q');

  $('#btnOpen').on('click', function(e){
    e.preventDefault();
    const open = $search.hasClass('open');
    if(!open){ $search.addClass('open'); setTimeout(()=> $q.trigger('focus'), 10); }
    else { $q.trigger('focus'); }
  });

  $('#btnClear').on('click', function(){
    $q.val('').trigger('input').focus();
  });

  $q.on('blur', function(){
    if(!this.value) $search.removeClass('open');
  });

  // Filtrado
  $q.on('input', function(){
    const val = this.value.trim().toLowerCase();
    const $cards = $('.card-scope');
    let visibles = 0;
    $cards.each(function(){
      const hay = $(this).data('search');
      const show = !val || (hay && hay.indexOf(val) !== -1);
      $(this).css('display', show ? '' : 'none');
      if(show) visibles++;
    });
    if(visibles === 0){
      if(!$('#nores').length){
        $('<p id="nores" style="color:#7a7f87;margin-top:10px;">Sin resultados…</p>').insertAfter('.search-wrap');
      }
    } else { $('#nores').remove(); }
  });

  /* ===== Tarjetas ===== */
  $('.card-scope').each(function(){
    var $scope = $(this);
    var $card  = $scope.find('#product-card');
    var $front = $scope.find('#product-front');
    var $back  = $scope.find('#product-back');
    var $view  = $scope.find('#view_details');
    var $flipB = $scope.find('#flip-back');

    // Hover: overlay + botón
    $card.hover(
      function(){ $(this).addClass('animate'); },
      function(){ $(this).removeClass('animate'); }
    );

    // Ir a la parte trasera
    $view.on('click', function(){
      $card.addClass('flip-10');
      setTimeout(function(){
        $card.removeClass('flip-10').addClass('flip90')
          .find('div.shadow').show().fadeTo(80,1,function(){
            $front.add($front.find('div.shadow')).hide();
          });
      },50);
      setTimeout(function(){
        $card.removeClass('flip90').addClass('flip190');
        $back.show().find('div.shadow').show().fadeTo(90,0);
        setTimeout(function(){
          $card.removeClass('flip190').addClass('flip180').find('div.shadow').hide();
          $card.addClass('on-back');
          $scope.find('#cx,#cy').addClass('s1');
          setTimeout(function(){ $scope.find('#cx,#cy').addClass('s2'); },100);
          setTimeout(function(){ $scope.find('#cx,#cy').addClass('s3'); },200);
        },100);
      },150);
    });

    // Volver al frente (tache)
    $flipB.on('click', function(){
      $card.removeClass('flip180').addClass('flip190');
      setTimeout(function(){
        $card.removeClass('flip190').addClass('flip90');
        $back.find('div.shadow').css('opacity',0).fadeTo(100,1,function(){
          $back.add($back.find('div.shadow')).hide();
          $front.add($front.find('div.shadow')).show();
        });
      },50);
      setTimeout(function(){
        $card.removeClass('flip90').addClass('flip-10');
        $front.find('div.shadow').show().fadeTo(100,0);
        setTimeout(function(){
          $front.find('div.shadow').hide();
          $card.removeClass('flip-10 on-back').css('transition','100ms ease-out');
          $scope.find('#cx,#cy').removeClass('s1 s2 s3');
        },100);
      },150);
    });

    // Evitar que el click en los FABs dispare el flip accidentalmente
    $scope.find('.card-actions .fab, .card-actions form').on('click', function(e){
      e.stopPropagation();
    });

    // Confirmación de borrado con SweetAlert
    $scope.find('.delete-form').on('submit', function(e){
      e.preventDefault();
      e.stopPropagation();
      const form = this;
      Swal.fire({
        title: '¿Eliminar producto?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e53935',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});

// Toast si hay mensaje de éxito
@if(session('success'))
Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: @json(session('success')),
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});
@endif
</script>

@endsection
