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
.edit-wrap{ max-width:980px;margin:110px auto 40px;padding:0 16px; }
.panel{ background:var(--card); border-radius:16px; box-shadow:0 16px 40px rgba(18,38,63,.12); overflow:hidden; }
.panel-head{ padding:22px 26px; border-bottom:1px solid var(--line); display:flex;align-items:center;gap:14px;justify-content:space-between; }
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.back-link{ display:inline-flex;align-items:center;gap:8px; color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px; border:1px solid var(--line);background:#fff; }
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
  /* CORRECCIÓN DOBLE FLECHA */
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  cursor: pointer;
  z-index: 2;
  position: relative;
}

.field label{
  position:absolute;left:14px;top:14px;color:var(--muted);font-size:13px;
  transition:transform .15s ease, color .15s ease, font-size .15s ease, top .15s ease;
  pointer-events:none;
  z-index: 1;
}

/* Label flotante */
.field input::placeholder{color:transparent;}
.field input:focus + label,
.field input:not(:placeholder-shown) + label,
.field.has-value label,
.field:focus-within label{
  top:8px;transform:translateY(-10px);font-size:11px;color:var(--mint-dark);
}

/* Caret personalizado */
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

/* Select familias (Tu estilo original) */
.field-select{ border:1px solid var(--line);border-radius:12px;padding:12px 14px;background:#fff; }
.field-select label{ display:block;color:var(--muted);font-size:12px;margin-bottom:6px;font-weight:600; }
.select-multi{
  width:100%;border:1px solid #e6e9ee;border-radius:10px;padding:8px 10px;min-height:44px;
  outline:none;background:#fafbfc;font-size:14px;
}
.hint{color:var(--muted);font-size:12px;margin-top:6px}
.chips{ display:flex;gap:6px;flex-wrap:wrap;margin-top:8px; }
.chip{
  display:inline-flex;align-items:center;gap:6px;
  background:#eef2ff;border:1px solid #e5e7eb;color:#374151;
  padding:4px 8px;border-radius:999px;font-size:12px;
}
.chip button{
  border:none;background:transparent;color:#6b7280;cursor:pointer;font-size:13px;line-height:1;
}
.chip button:hover{ color:#111827 }

/* Dropzone / Image */
.block{ border:1px dashed #dfe3e8;border-radius:14px;padding:16px;background:#fafbfc; }
.uploader{ display:grid;grid-template-columns:140px 1fr;gap:16px;align-items:center; }
@media (max-width: 600px){ .uploader{grid-template-columns:1fr} }
.thumb{ width:140px;height:140px;border-radius:12px;overflow:hidden;background:#f0f2f5; display:grid;place-items:center;border:1px solid #edf0f3; }
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
.actions{ display:flex;gap:12px;justify-content:flex-end;margin-top:10px;padding:0 26px 26px; }
.btn{ border:0;border-radius:12px;padding:12px 18px;font-weight:700;cursor:pointer; transition:transform .05s ease, box-shadow .2s ease, background .2s ease,color .2s ease; }
.btn:active{transform:translateY(1px)}
.btn-primary{ background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26); }
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{ background:#fff;color:var(--ink);border:1px solid var(--line); }
.btn-ghost:hover{border-color:#dfe3e8}

/* Error styles */
.is-invalid{border-color:#f9c0c0 !important}
.error{color:#cc4b4b;font-size:12px;margin-top:6px}
</style>

@php
  // Variables para la cascada de selects
  $oldTipo    = old('tipo_equipo',    $producto->tipo_equipo    ?? '');
  $oldSubtipo = old('subtipo_equipo', $producto->subtipo_equipo ?? '');
  $oldMarca   = old('marca',          $producto->marca          ?? '');
  $oldModelo  = old('modelo',         $producto->modelo         ?? '');

  // Variables para familias (Tu lógica original)
  $familias = $familias ?? \App\Models\Familia::orderBy('nombre')->get();
  $familiasSeleccionadas = old('familias', isset($producto->familias) ? $producto->familias->pluck('id')->toArray() : []);
@endphp

<div class="edit-wrap">
  <div class="panel">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Editar producto</h2>
        <p>Actualiza la información y la imagen del producto.</p>
      </div>
      <a href="{{ url()->previous() }}" class="back-link" title="Volver">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Volver
      </a>
    </div>

    <form class="form" action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="grid">
        {{-- Tipo de equipo --}}
        <div>
          <div class="field @error('tipo_equipo') is-invalid @enderror" id="wrap-tipo">
            <select name="tipo_equipo" id="f-tipo" required>
              <option value="" selected disabled hidden></option>
            </select>
            <label for="f-tipo">Tipo de equipo</label>
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
            <input type="number" step="0.01" name="precio" id="f-precio" value="{{ old('precio', $producto->precio) }}" placeholder=" " required>
            <label for="f-precio">Precio</label>
            <span class="prefix">$ MXN</span>
          </div>
          @error('precio')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Familias (multiselección) --}}
        <div style="grid-column:1/-1;">
          <div class="field-select @error('familias') is-invalid @enderror">
            <label for="familias">Familias (opcional)</label>
            <select id="familias" name="familias[]" class="select-multi" multiple size="6">
              @foreach($familias as $fam)
                <option value="{{ $fam->id }}" {{ in_array($fam->id, $familiasSeleccionadas) ? 'selected' : '' }}>
                  {{ $fam->nombre }}
                </option>
              @endforeach
            </select>
            <div class="hint">Mantén presionadas Ctrl / Cmd para seleccionar varias.</div>
            <div id="chips" class="chips"></div>
          </div>
          @error('familias')<div class="error">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Imagen --}}
      <div class="block" style="margin-top:22px;">
        <div class="uploader">
          <div class="thumb">
            <img id="preview" src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : 'https://via.placeholder.com/280x280.png?text=Sin+imagen' }}" alt="Vista previa">
          </div>
          <div class="drop">
            <label class="btn" for="imagen">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;">
                <path d="M12 5v14M5 12h14"/>
              </svg>
              Cambiar imagen
            </label>
            <input id="imagen" class="input-file" type="file" name="imagen" accept="image/*">
            <span class="small">Formatos: JPG/PNG. Máx 2MB.</span>
          </div>
        </div>
        @error('imagen')<div class="error" style="margin-top:8px;">{{ $message }}</div>@enderror
      </div>

      <div class="actions">
        <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
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

/* ==================== Logic Familias / Chips ==================== */
const sel = document.getElementById('familias');
const chips = document.getElementById('chips');

function renderChips(){
  if(!sel || !chips) return;
  chips.innerHTML = '';
  Array.from(sel.selectedOptions).forEach(opt => {
    const span = document.createElement('span');
    span.className = 'chip';
    span.innerHTML = `${opt.text} <button type="button" aria-label="Quitar">&times;</button>`;
    span.querySelector('button').addEventListener('click', () => {
      opt.selected = false;
      renderChips();
    });
    chips.appendChild(span);
  });
}
sel?.addEventListener('change', renderChips);
renderChips(); // Ejecutar al inicio

/* ==================== DATOS COMPLETOS (Endoscopia, Laparoscopia, etc.) ==================== */
const OLD = {
  tipo:    @json($oldTipo),
  subtipo: @json($oldSubtipo),
  marca:   @json($oldMarca),
  modelo:  @json($oldModelo),
};

const tiposEquipos = {
  ENDOSCOPIA: [
    "Adaptador","Argón Plasma","Bomba de Irrigación","Bomba de Secreción","Bomba de CO2","Broncoscopio","Cable","Cable Bipolar","Cable Monopolar","Capturador de Video","Capuchón Distal","Carro","Cepillo de Limpieza","Colonoscopio","Conjunto de Irrigación","Contenedor de líquidos","Convertidor de Video","Consumibles","Colgador de pared para Endoscopios","Duodenoscopio","Eliminador","Focos excelitas","Fuente de Luz","Gastroscopio","Grabador","Interfaz Monopolar para Erbe","Kit de Limpieza","Línea de Irrigación","Monitor","Mouse","Multicontacto","PC SIIMED Análogo","PC SIIMED HD","Pedal","Pigtail","Pinzas de Endoscopia","Probador de Fuga","Procesador","Protector Bucal de Endoscopio","Protector de Punta de Endoscopio","Regulador de Argón de Endoscopia","Sistema Endoscopia","Tapon de Biopsia","Tapon-ETO","Tanque de Argón","Teclado","Valvúlas desechables","Valvúlas Reusables","Yugo Para Argón"
  ],
  LAPAROSCOPIA: [
    "Adaptador","Cabezal","Cable Interfaz 1688","Cable Interfaz USB 1588","Cable Bipolar","Cámara","Case de Transporte","Charola de Esterilización","Clarity","Clips para Monitor","Fibra de Luz","Fuente de Luz","Grabador","Instrumental de Laparoscopia","Insuflador","Lente","Manguera de Insuflación","Manguera para Bomba de Agua","Monitor Grado Médico","Parche para Electrocauterio","Pedestal","Pieza de Mano","Pinza","Porta tanque","Transmisor","Trocar","Receptor","Video Carro","Video Grabador"
  ],
  QUIRÓFANO: [
    "Adaptador para Ligasure","Armónico Gen11","Bipap","Brazalete Pani","Bomba de Infusion","Cable Para Pinza Bipolar","Cable Trocal ECG","Carro para Electrocauterio","Carro Rojo Emergencias","Desfribilador","Electrocauterio","Eliminador","Fuente de Poder para Desfribilador","Lámpara de Quirófano","Lapíz para Electrocauterio","Ligasure LS8","Línea de Muestreo de CO2","Máquina de Anestesia","Mesa de Cirugía","Monitor Signos Vitales","Pedal Bipolar","Pedal Ligasure","Pedal Monopolar","Pieza de Mano Para Gen11","Placa para Electrocauterio","Sensor de ECG","Sensor de SPO2","Sensor PANI","Sensor de Temperatura","Vaporizador"
  ],
  HOSPITALIZACIÓN: [
    "Aspirador","Cama Hospitalaria Eléctrica","Camilla","Cuna Térmica","Incubadora","Mesa de Exploración","Ventilador"
  ],
  MATERIAL: [
    "Limpiador y Desengrasante"
  ],
  RADIOLOGÍA: ["Arco en C","Batería","Chasis","Flat Panel","Rayos X Rodable","Rayos X Portatil"],
  UROLOGÍA: ["Cistoscopio","Histeroscopio","Resectoscopio","Ureteroscopio Flexible", "Ureteroscopio Rigido"],
  ARTROSCOPIA: [
    "Batería","Cargador de Baterias","Camisa con Opturador","Cable para pedal","Cable para pieza de mano","Charola de Esterilización","Puntas de Radio Frecuencia","Endogia","Bomba de Irrigación","Pedal",
    "Lente",
    "Serfas de radiofrecuencia","Serfas Energy","Shaver","Rasurador", "Radio Frecuencia",
    "Set de Taladros de Artroscopia",
    "Transmisores",
    "Set de Cirugia Para Tobillo y Muñeca", "Set de Cirugía de Rodilla",
    "Meditronic","linea de irrigacion"
  ],
  MANTENIMIENTO: ["Servicio Preventivo","Servicio Preventivo y Correctivo"],
  CEYE: ["Autoclave de cámara 95 L ","Monitor"],
  GINECOLOGÍA: ["Camilla Ginecologíca","Mesa de Exploración","Ultrasonido","Impresora"]
};

const marcasModelosPorSubtipo = {
   laparoscopia: {
    'camara': {
      'Stryker': ['1188','1288','Precision','1488','1588','1688','1788'],
      'Karl Storz': ['IMAGE1 S', 'IMAGE1 HUB', 'Spies']
    },
    'insuflador': {
      'Stryker': ['High Flow 40L','PneumoSure 45L','PneumoClear 50L'],
      'Karl Storz': ['Endoflator 50', 'Endoflator 264320 20'],
    },
    'fuente de luz': {
      'Stryker': ['X8000', 'L9000', 'L10', 'L11'],
      'Karl Storz': ['Xenon 300', 'Power LED 300']
    },
    'monitor grado medico': {
      'Stryker': ['Vision Elect HDTV', 'VisionPro LED 26"', 'VisionPro SYNK LED 26"', '4K LED 32"', '4K 32" OLED', 'Wise HD 26"'],
    },
    'cabezal': {
      'Stryker': ['1188', '1288', 'Precision', '1488', '1588','1688', '1788']
    },
    'cable bipolar': {
          'Olympus': ['WA00014A para ESG-400']
    },
    'clarity': { 'Stryker': ['clarity'] },
    'grabador': { 'Stryker': ['SDC Ultra','SDC3','Connected OR HUB'] },
    'lente': {
      'Stryker': ['30-5mm Azul','30-5mm AIM','30-5mm Precision','30-10mm Precision','30-10mm AIM','30-10mm Azul']
    },
    'fibra de luz': {
      'Stryker' : ['X8000 Gris','L9000 Blanca','L10 y L11 Verde','Kit Ureteral IRIS']
    },
    'video carro': {
      'Stryker': ['Standar','Connected OR'],
    },
    'transmisor': {
      'Stryker': ['4K SYNK Wireless','4K SYNK Wireless Receiver','VisionPro SYNK Wireless','Wise HDTV Wireless']
    },
    'trocar': {
      'Ethicon': ['11mm X 100mm','12mm X 100mm 2D12-T'],
      'GM': ['KIT Trocares GYTR L KIT A','KIT TROCARES GYTR-LLL KIT A']
    },
    'receptor':{
      'stryker': ['4K']  
    },
    'pedestal': { 'Stryker': ['Pedestal']
    },
     'Porta tanque': {'GM':['Porta tanque']
    },
    'instrumental de laparoscopia': { 
      'Ethicon': ['100mm x 12mm'],
      'GM': ['Aguja de Veress','Baja Nudos','Cable Bipolar','Cable monopolar','Clips Hemolok Dorado','Clips Hemolok Morado','Clips Hemolok Verde','Clips Titanio OC300','Clips Titanio OC400','Conjunto de Irrigacion y Succion desechable','Engrapadora Articulada','Engrapadora Hemolok Amarillo','Engrapadora Hemolok Dorado','Engrapadora Hemolok Morado','Engrapadora Hemolok Verde','Engrapadora Titanio LT300','Engrapadora Titanio LT400','Espatula','Gancho En L','Pinza Alligator','Pinza Babcock','Pinza Babcock Grasper 5mm 330mm','Pinza Babcock Grasper 10mm 330mm','Pinza Cobra','Pinza Colecistectomia','Pinza De Curva','Pinza De Disección','Pinza De Tijera Recta','Pinza Disectora','Pinza Extractora De Litos','Pinza Fenestrada','Pinza Grasper','Pinza Har23','Pinza Har26','Pinza Maryland Curva','Porta agujas 5mm 300mm','Retractor','Tijera Metzenbaum Doble Acción Curva 5mm* 330mm','Tubo de Irrigacion y Succion Reusable' ],
      'Covidien': ['Engrapadora Endogia Articulada 45mm Morado','Engrapadora Endogia Articulada 60mm Morado','Engrapadora Endogia Articulada 45mm Vascular Dorado','Engrapadora Endogia Articulada 60mm Vascular Dorado','Engrapadora Endogia ultra 12mm','Engrapadora Endoclip 10mm M/L','Engrapadora Tri-Staple Extra 60mm Negro'],
      'Storz': ['Pinza Grasper'],
    },
    'manguera de insuflacion': { 
      'stryker': ['manguera','yugo CO2']
    },
    'pinza': {
      'Covidien': ['Blunt Tip 5mm-37cm','Maryland 5mm-37cm','Maryland 5mm-23cm','Small Jam 16.5mm-19cm','Exact Dissector 20.6mm-21cm']
    },
    'adaptador': { 
      'stryker': ['Adaptador cople de lente','Adaptador frontal de Insuflador','Adaptador Trasero de Insuflador'],
    },
    'case de transporte': {
      'GM': [ 'Camara y Fuente L9000','Camara 1688 y Fuente L11','Grabador e Insuflador','Monitor Vision Pro led','Monitor 4K Stryker','Monitor 4K SONY']
    },
    'charola de esterilizacion': {
      'Stryker': ['Camara IAM','Lente de Laparoscopia'],
      'Storz': ['Lente de Laparoscopia'],
      'Artrhex': ['Lente de Laparoscopia'],
      'Olympus': ['Lente de Laparoscopia'],
    },
    'clips para monitor': {
      'GM':[ 'Porta Monitor']
    },
  },

  endoscopia: {
    'procesador': {
      'Olympus': ['CV-160','CV-170','CV-180','CV-190','EVIS X1'],
      'Fujifilm': ['VP-4400','VP-4440HD','EP-6000','EP-7000'],
      'Pentax': ['EPK-i','EPK-i7010'],
    },
    'fuente de luz': {
      'Olympus': ['CLV-160','CLV-180','CLV-190'],
      'Fujifilm': ['XL-4400','XL-4450','BL-7000'],
      'Pentax': ['Prueba']
    },
  'broncoscopio': { 
  'Olympus': ['BF-XP160F','BF-1T190']
},
    'colonoscopio': {
      'Olympus': ['CF-Q160L','CF-H180AL','CF-HQ190L', 'CF-EZ1500DL'],
      'Fujinon': ['EC-250HL5','EC-600HL','EC-760R-V/L'],
      'Pentax': ['EC-3890LI'],
    },
    'duodenoscopio': { 
      'Olympus': ['JF-140F','TJF-160F','TJF-160VF','TJF-Q180V','TJF-Q180','TJF-Q90V'],
      'Fujinon': ['ED-530XT'],
      'Pentax': ['ED-34-I10T2'],
    },
    'gastroscopio': {
      'Olympus': ['GIF-Q160','GIF-XP160','GIF-1TQ160','GIF-2T160','GIF-180','GIF-H180','GIF-H180J','GIF-HQ190', 'GIF-EZ1500', 'CF-EZ1500DL'],
      'Fujinon': ['EG-530N','EG-530WR','EG-600WR','EG-6400N','EG-760R'],
      'Pentax': ['EG-2990i'],
    },
    'argon plasma': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300', 'APC'] },
    'bomba de co2': { 'Fujinon': ['GW-100'] },
    'bomba de irrigacion': {
      'Olympus': ['UCR','OFP','OFP2'],
      'Medivators': ['Endogator EGP-100','Stratus EGA-500'],
      'Erbe': ['EIP 2'],
    },
    'bomba de secrecion': { 'Infusomat': ['Braun Sumalfit'] },
    'capturador de video': { 'Ugreen': ['HDMI'] },
    'convertidor de video': { 'GM': ['X003'] },
    'consumibles': {
        'MicroTech':['Balon multietapa para dilatacion de vias digestivas 10-11-12 mm Longitud del balon 55 mm Canal de trabajo 2,8 mm Longitud del cateter 230 cm','Balón de dilatación multietapa, inflado del balon de 10-11-12 mm, longitud total de 180cm','Aguja de inyección de 19G 230 cm longitud','Aguja de inyección de 19G, Longitud de 180 cm','Aguja de inyección de 22G, longitud de 200 cm','Set de ligaduras para varices esofagicas, Longitud 145x30mm, cantidad de bandas 7 piezas','Clip para hemostasia, reposicionable y rotable., ancho de apertura de 11 mm, diámetro de la camisa 2.6 mm, longitud de 235 cm.','MIC PEG-SET Sonda de alimentación por gastrostomia endoscópica percutánea, tipo Pull, diámetro externo 20 Fr, incluye: jeringa, campo, sutura, pinza extractora y aguja filtro','MIC PEG-SET Sonda de alimentación por gastrostomia endoscópica percutánea, tipo Pull, diámetro externo 24 Fr, incluye: jeringa, campo, sutura, pinza extractora y aguja filtro','Asa de polipectomia calientede 15 mm de diámetro, longitud de 230 cms','Asa fría de 10 y 15 mm de diámetro, longitu de 230 cms','Asa con red de 230 cms de longitud', 'BBP-50 Trampa para polipos']
    },
    'Colgador de pared para Endoscopios': {
           'GM': ['Colgador de Metal con protector de Goma'],
    },
    'monitor': {
      'Olympus': ['OEV 262H','OEV 191H'],
      'Storz': ['4k 32"','Led 26"'],
      'Sony': ['HD 19"','4k 55"'],
    },
    'Adaptador': {
      'Valleylab': ['Adapatador Bipolar Azul Active Only'],
      'Erbe': ['Adaptador Bipolar ICC 200 ','Adaptador para Sonda ICC200 ICC300 VIO 300D','Sonda Circular'],
      'Generico': ['Adaptador para el canal de Biopsia']
    },
    'grabador': { 'KingMa': ['KM-YK980'] },
    'interfaz monopolar para erbe': { 'Erbe': ['Cable interfaz'] },
    'eliminador': { 
      'Storz': ['4k 32"','Led 26"'],
      'Sony': ['HD 19"','4k 55"'],
    },
    'focos excelitas': {
      'PE300BFA': ['180-160-4400-4450-Xenon300'],
      'PE150AF': ['Fujinon-2200'],
      'Y1911': ['EPK-5010','EPKI-7010'],
      'Y1882': ['EPK-i'],
      'Y1964': ['EPK-5010','EPKI-7010'],
    },
    'Carro': { 
      'Olympus': ['Para sistema 160 o 180','Para sistema 190'],
      'Fujinon': ['Carro Original'],
      'GM':['Carro GM'],
    },
    'kit de limpieza': { 
      'Olympus': ['MH-946 para 160 180 y 190'],
      'Fujinon': ['WA-007 para 760'],
    },
    'linea de irrigacion': {
      'GM': ['Genericas'],
      'Medivators': ['OFP','OFP 2','Stratus'],
    },
    'contenedor de liquidos': {
      'Olympus': ['Serie 100','160','180','190'],
      'Fujinon': [ 'Serie 500 y 600','760','760 para Insuflador'],
      'Pentax': ['Serie 7010'],
    },
    'Pedal':{
        'Medivators':['Endogator'],
        'Olympus':['OFP'],
        'Erbe':['OFP2'],
    },
    'Pinzas de Endoscopia': {
      'Olympus': ['pinza de biopsia','pinza de biopsia hot','pinza de canasta','pinza de 4 hilos','pinza de extraccion','pinza de polipectomia'],
      'MicroTech': ['pinza de biopsia Ovaladas Diam. 2.3mm Long. 160cm ','pinza de biopsia Ovaladas Diam. 2.3mm Long. 230cm','Pinza de recuperación de cuerpos extraños Griffi, longitud de 230 cms']
    },
    'probador de fuga': { 
      'Olympus': ['Serie 160 180 190'],
      'Fujinon': [ 'Serie 500 y 600','Serie 760'],
      'Pentax': ['Serie 90i'],
    },
    'protector bucal de endoscopio': { 
        'Olympus': ['MB-142 Boquilla'],
        'MicroTech': ['AC01-103.A Boquilla adulto']
    },
    'protector de punta de endoscopio': { 'GM': ['Protector Azul'] },
    'tapon de biopsia': { 'GM': ['GM'] },
    'tapon-eto': { 'Olympus': ['MH-553'] },
    'Tanque de Argón': { 'GM' : ['Tanque de Argón'] },
    'valvulas desechables': { },
    'valvulas reusables': { 'Fujinon': [ 'Serie 760'] },
    'yugo para argon': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300', 'APC'] },
    'teclado': { 
      'Olympus': ['Serie 100','160','180','190'],
      'Fujinon': [ 'Serie 500 y 600','760'],
      'Pentax': ['Serie 7010'],
    },
    'mouse': { 'GM': ['GM'] },
    'multicontacto': { },
    'pc siimed analogo': { },
    'pc siimed hd': { },
    'pigtail': { 'Olympus': ['Maj-1430'] },
    'cable': { },
    'cable bipolar': { },
    'cable monopolar': { },
    'cepillo de limpieza': {
        'MicroTech': ['CB18-T5050/23-B Set cepillos de limpieza'],
       'Olympus': ['Prueba']
        },
    'capuchon distal': { },
  },

  quirofano: {
    'adaptador para ligasure': {
      'Cad':['LS8','Force FX','Force 2','Adaptador Bipolar LS8']
    },
    'ligasure ls8': { 'Medtronic': ['LS8'] },
    'electrocauterio': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Erbe': ['ICC 200','ICC 300','VIO 300D'],
      'Olympus': ['ESG-400',],
      'GM': ['CITADEL 300'],
      'Conmed': ['Sabre Genesis'],
    },
    'brazalete pani': { 
      'Datex-Ohmeda': ['Cardiocap5'],
      'Drager': ['Delta Infinity'],
      'Phillips': ['MP50 Intellivue','MP70 Intellivue'],
      'Mindray': ['V12'],
    },
    'Bomba de Infusion': {
      'Dre Med':[ 'NTx3 Plus'],
    },
    'maquina de anestesia': {
      'Datex-Ohmeda': ['Aestiva','Avance','Aisys','Aespire'],
      'Dräger':['Fabius MRI'],
    },
    'mesa de cirugia': {
      'Amsco': ['2080 Semielectrica y SemiTraslucida' ,'3080 Electrica y Traslucida'],
      'Maquet':['AlphaStart']
    },
    'lampara de quirofano': {
      'Stryker': ['Vision 2'],
      'Skytron': ['Aurora'],
    },
    'monitor signos vitales': {
      'Datex-Ohmeda': ['Cardiocap5'],
      'Drager': ['Delta Infinity'],
      'Phillips': ['MP50 Intellivue sin capnografia','MP50 Intellivue con capnografia','MP70 Intellivue'],
      'Mindray': ['V12'],
    },
    'desfribilador': {
      'Phillips': ['Heartstart MRX'],
      'Zoll': ['AED plus'],
    },
    'bipap': {
      'Phillips':['Ventilador Respironics Nuevo']
    },
    'vaporizador': { 
      'Datex-Ohmeda': ['Tec 7 Aestiva-Aespire','Casette Aisys'],
    },
    'sensor de ecg': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor de spo2': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor pani': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor de temperatura': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'pedal bipolar': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Conmed': ['Sabre Genesis'],
    },
    'pedal monopolar': { 
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Conmed': ['Sabre Genesis'],
      'Olympus': ['ESG-400']
    },
    'pedal ligasure': { 
      'Covidien':[ 'Pedal Bipolar Morado','Pedal Bipolar Anaranjado']
    },
    'placa para electrocauterio': {
      'OBS':['Placa desechable']
    },
    'lapiz para electrocauterio': {
      'Avante':['Placa desechable'],
      'OBS':['Placa desechable'],
      'Covidien': [ 'Placa desechable'],
      'Conmed':['Placa desechable'],
      'Smith&Nephew':['Placa desechable']
    },
    'Línea de Muestreo de CO2': {
      'Datex Ohmeda':['Aisys','Avance' ,'Cardiocap5'],
      'Phillips': ['Heartstart MRX'],
    },
    'cable para pinza bipolar': {
      'Covidien': [ 'Pinza Bipolar']
    },
    'cable trocal ecg': { 
      'Drager': ['Delta Infinity'],
    },
    'carro para electrocauterio': { 
      'Erbe': [ 'Para ERBE'],
      'Covidien': ['Force 2','Force FX','ForceTriad','FT10'],
    },
    'carro rojo Emergencias': {
      'Lifeline': [ 'Carro de Emergencias'],
      'GM': [ 'Carro de Emergencias NUEVO'],
    },
    'eliminador': {
      'Phillips': ['Fuente de poder Desfibrilador MRX']
    },
    'pieza de mano para gen11': {
      'Ethicon':[ 'Pieza con 4 usos','Pieza con 70 usos','Pieza con 87 usos']
    },
    'armonico gen11': { 
      'Ethicon':[ 'Armonico GEN11']
    },
  },

   hospitalizacion: {
    'aspirador': {
      'Hergon': ['7E-A NUEVO']
    },
    'cama hospitalaria electrica': {
      'Hill Roon':['Versacare',],
      'stryker':['MPS Secure II','S3']
    },
    'camilla': { 
      'Hill Roon':['P8000'],
      'Stryker':['Prime Series','1015 Stretcher'],
    },
    'cuna termica': {
      'GE Healthcare':[' Panda Warmer']
    },
    'incubadora': {
      'GE': [' Giraffe'],
    },
    'mesa de exploracion': { },
    'ventilador': {
      'Nellcor': ['Puritan Benett 840']
    },
  },

  radiologia: {
    'arco en c': { },
    'bateria': { },
    'chasis': { },
    'flat panel': { },
    'rayos x rodable': { },
    'rayos x portatil': { },
  },

  urologia: {
    'cistoscopio': { },
    'histeroscopio': { },
    'resectoscopio': { },
    'ureteroscopio flexible': { },
    'ureteroscopio rigido': { },
  },

  artroscopia: {
    'shaver': { },
    'rasurador': { },
    'radio frecuencia': { },
    'puntas de radio frecuencia': {
      'Stryker': ['Cortadora Agresiva Plus 3.5mm x 80mm Amarillo','Cortadora Agresiva Plus 5.0mm x 125mm Azul','Cortadora Angular 4.0mm x 125mm Rojo','Cortadora Angular 5.0mm x 125mm Azul','Cortadora Resector 3.5mm x 125mm Amarillo','Cortadora XL Agresiva 4.0mm x 180mm Rojo','Fresa 5mm x 125mm Azul','Fresa de Abrasion 2.0mm x 80mm Morado','Fresa Redonda de 12 filos 5.5mm x 125mm Café','Fresa de Barril de 12 hilos 5.5mm x 125mm Cafe'],
    },
    'serfas de radiofrecuencia': { },
    'serfas energy': { },
    'bomba de irrigacion': { },
    'lente': { 'Stryker': ['30-4mm'] },
    'transmisores': { },
    'pedal': {
      'Arthocare': ['Coblator II']
    },
    'set de taladros de artroscopia': {
      'Stryker': [ 'System 7 Mandril llave']
    },
    'camisa con opturador': { },
    'cable para pedal': { },
    'cable para pieza de mano': { },
    'charola de esterilizacion': {
           'Stryker': [ 'Set de Taladro System 4','Set de Taladro System 7','Set de Taladro System 8','Set de Taladros Electrico Core Azul','Set de Taladros Electrico Core Negro','Set de cirugia para hombro y tobillo','Set de cirugia de rodilla'],
           'Arthrex': [ 'AR-3100'],
            'GM': [ 'GM'],
    },
    'bateria': { },
    'cargador de baterias': { 
      'Stryker': ['Taladros'],
    },
    'meditronic': { },
    'set de cirugia para tobillo y muñeca': { 
            'Stryker': ['Nariz Roma hacia Arriba de 2.7mm 242-100-013','Nariz Recta 242-100-012','Pinza Grasper hacia Arriba 2.1mm 242-100-006','Pinza Grasper de 2.5mm 242-100-008','Nariz Recta Punzon de Articulacion Pequeña 242-100-002','Grasper Nariz hacia Abajo 242-100-005','Nariz derecha Punzon en Articulacion Pequeña 242-100-003','Nariz Izquierda Pequeño Golpe en la Articulacion 2.1mm 242-100-004','Tijeras para Juntas Pequeñas 242-100-007','Sonda de Articulacion Pequeña Recta 242-100-014','Sonda para Articulaciones Pequeñas de 90° 242-100-015','Sonda para Articulaciones pequeñas de 30° 242-100-016']
        },
        'set de cirugia de rodilla': {
            'Stryker': [ 'Pinza de Mordida Grande hacia Arriba 3.4mm 15°','Morida Grande del Eje Ascendente Recto','Punzon de Mordida Grande de 3.4mm Recto','Pinzas para Tejidos Blandos de 3.4mm X 120mm','Micro Punzon de Tijera Recto 3.4mm','Mordedor de Punta Derecha 3.4mm x 45°','Mordedor de Punta Izquierda de 3.4mm 45°','Punzon Rotatorio de 3.4 mm y 90° a la Derecha','Sonda con Mango en Forma de Anillo','Opturador','Sonda','Aguja de Negra','Opturador de Punta Roma para Canula de Entrada / Salida 5.8mm','Palpador Switching Stick Pequeño', ' Canula de 100mm','Mango de Bisturi'],
            'V.MUELLER': [ 'Tijeras de Diseccion Inoxidables','Porta agujas','Pinzas para la Arteria de Kelly','Pinza ADSON 1X2 Dientes'],
            'KONIG': ['Pinza Quirurgica/Tolla','Pinza para Arteria de Crile- 1/2" de Largo'],
      },
  },
  mantenimiento: {
     'Servicio Preventivo':{
      'Olympus': ['GIF-Q160','GIF-H180','GIF-HQ190','GIF-HQ170','CF-Q160','CF-H180','CF-HQ190'],
      'Fujinon': [ 'EG-530WR','EG-600WR','EG-760','EC-530WR','EC-600WR','EC-760LR'],
      'Pentax': ['EG-2990i','EC-2990Li'],
     },
      'Servicio Preventivo y Correctivo':{
      'Olympus': ['GIF-Q160','GIF-H180','GIF-HQ190','GIF-HQ170','CF-Q160','CF-H180','CF-HQ190'],
      'Fujinon': [ 'EG-530WR','EG-600WR','EG-760','EC-530WR','EC-600WR','EC-760LR'],
      'Pentax': ['EG-2990i','EC-2990Li'],
    },
     },
  ceye: {
    'autoclave de camara 95 l': { },
    'monitor': { },
  },

  ginecologia: {
    'Camilla Ginecologíca': { 
      'Stryker': ['Geynnie'],
    },
    'Ultrasonido': {
      'GE': [ 'Logic P3'],
    },
    'Impresora': {
      'Sony': [ 'UP-D897'],
    },
    'mesa de exploracion': { 
      'Midmark': ['Modelo 404',' Ritte 622']
    },
  },

  material: {
    'Limpiador y Desengrasante': {
      'Steren': ['Desengrasante Y Limpiador']
    },
  },
};
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
  // 1. Llenar Tipos
  fillSelect($tipo, Object.keys(tiposEquipos));

  // 2. Bind Events
  $tipo.addEventListener('change', onTipoChange);
  $subtipo.addEventListener('change', onSubtipoChange);
  $marca.addEventListener('change', onMarcaChange);
  $modelo.addEventListener('change', ()=> setHasValue(wrapModelo, $modelo));

  // 3. AUTO-SELECT (Pre-llenado en Editar)
  const tipoPick = pickExact(Object.keys(tiposEquipos), OLD.tipo);
  if(tipoPick){
    $tipo.value = tipoPick;
    onTipoChange(); // Llenar subtipos

    const subPick = pickExact(tiposEquipos[tipoPick] || [], OLD.subtipo);
    if(subPick){
      $subtipo.value = subPick;
      onSubtipoChange(); // Llenar marcas

      const marcas = getMarcas(tipoPick, subPick);
      const marcaPick = pickExact(marcas, OLD.marca);
      if(marcaPick){
        $marca.value = marcaPick;
        onMarcaChange(); // Llenar modelos

        const modelos = getModelos(tipoPick, subPick, marcaPick);
        const modeloPick = pickExact(modelos, OLD.modelo);
        if(modeloPick){
          $modelo.value = modeloPick;
        }
      }
    }
  }

  // 4. Aplicar estilos flotantes iniciales
  setHasValue(wrapTipo, $tipo);
  setHasValue(wrapSubtipo, $subtipo);
  setHasValue(wrapMarca, $marca);
  setHasValue(wrapModelo, $modelo);
});
</script>
@endsection