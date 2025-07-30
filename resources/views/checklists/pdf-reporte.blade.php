<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Checklist</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #f8fafc; color: #222; font-size: 14px; }
        h1, h2, h3 { color: #228be6; }
        .seccion { margin-bottom: 18px; border-radius: 9px; padding: 14px 18px; background: #e3f0fd; }
        .subtitulo { color: #6d5dfc; font-weight: 600; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #eee; padding: 5px 8px; border-radius: 6px; }
        th { background: #e9f2fb; }
        .info { color: #444; margin: 0 0 6px 0; }
        .footer { margin-top: 25px; color: #868e96; font-size: 13px; text-align: center; }
        .firma-img { max-height: 60px; margin-top: 6px; border-radius: 7px; border: 1px solid #e0e7ef; }
        .pastel { background: #e3f0fd !important; }
        .pastel2 { background: #fdf8e3 !important; }
        .pastel3 { background: #f2ffe3 !important; }
    </style>
</head>
<body>
    <h1>Reporte de Checklist</h1>

    <div class="seccion">
        <div class="subtitulo">Información General</div>
        <div class="info"><b>Venta:</b> {{ $venta->folio ?? $venta->id }}</div>
        <div class="info"><b>Creado por:</b> {{ $checklist->user->name ?? '-' }}</div>
        <div class="info"><b>Fecha de creación:</b> {{ $checklist->created_at?->format('d/m/Y H:i') }}</div>
        <div class="info"><b>Última actualización:</b> {{ $checklist->updated_at?->format('d/m/Y H:i') }}</div>
    </div>

    {{-- INGENIERÍA --}}
    @if($ingenieria)
    <div class="seccion pastel">
        <div class="subtitulo">Ingeniería</div>
        @php
            $componentesIng = is_array($ingenieria->componentes) ? $ingenieria->componentes : json_decode($ingenieria->componentes, true);
        @endphp
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Componente</th>
                    <th>Estado</th>
                    <th>Incidencia</th>
                </tr>
            </thead>
            <tbody>
            @if(is_array($componentesIng))
                @foreach($componentesIng as $productoId => $comps)
                    @foreach($comps as $comp => $det)
                        <tr>
                            <td>
                                @php
                                    $prod = $productos->firstWhere('id', $productoId);
                                @endphp
                                {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                            </td>
                            <td>{{ $comp }}</td>
                            <td>{{ ucfirst($det['estado'] ?? '-') }}</td>
                            <td>{{ $det['incidencia'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr><td colspan="4">No hay componentes registrados.</td></tr>
            @endif
            </tbody>
        </table>
        <div><b>Incidente General:</b> {{ $ingenieria->incidente ?? 'Sin reporte' }}</div>
        @if(!empty($ingenieria->firma_responsable))
            <div>
                <b>Firma Responsable:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $ingenieria->firma_responsable) }}">
            </div>
        @endif
        @if(!empty($ingenieria->firma_supervisor))
            <div>
                <b>Firma Supervisor:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $ingenieria->firma_supervisor) }}">
            </div>
        @endif
    </div>
    @endif

    {{-- EMBALAJE --}}
    @if($embalaje)
    <div class="seccion pastel2">
        <div class="subtitulo" style="color:#f6c453;">Embalaje</div>
        @php
            $componentesEmb = is_array($embalaje->componentes) ? $embalaje->componentes : json_decode($embalaje->componentes, true);
        @endphp
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Componente</th>
                    <th>Estado</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
            @if(is_array($componentesEmb))
                @foreach($componentesEmb as $productoId => $comps)
                    @foreach($comps as $comp => $det)
                        <tr>
                            <td>
                                @php $prod = $productos->firstWhere('id', $productoId); @endphp
                                {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                            </td>
                            <td>{{ $comp }}</td>
                            <td>{{ ucfirst($det['estado'] ?? '-') }}</td>
                            <td>{{ $det['observacion'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr><td colspan="4">No hay componentes registrados.</td></tr>
            @endif
            </tbody>
        </table>
        <div><b>Observaciones generales:</b> {{ $embalaje->observaciones ?? 'Sin observación' }}</div>
        @if(!empty($embalaje->firma_responsable))
            <div>
                <b>Firma Responsable:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $embalaje->firma_responsable) }}">
            </div>
        @endif
        @if(!empty($embalaje->firma_supervisor))
            <div>
                <b>Firma Supervisor:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $embalaje->firma_supervisor) }}">
            </div>
        @endif
    </div>
    @endif

    {{-- ENTREGA --}}
    @if($entrega)
    <div class="seccion pastel">
        <div class="subtitulo">Entrega</div>
        <div><b>Tipo entrega:</b> {{ ucfirst($entrega->datos_entrega['tipo_entrega'] ?? '-') }}</div>
        <div><b>Comentario final:</b> {{ $entrega->observaciones ?? '—' }}</div>
        @if(!empty($entrega->firma_cliente))
            <div>
                <b>Firma Cliente:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $entrega->firma_cliente) }}">
            </div>
        @endif
        @if(!empty($entrega->firma_entrega))
            <div>
                <b>Firma Entrega:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $entrega->firma_entrega) }}">
            </div>
        @endif
    </div>
    @endif

    {{-- RECEPCIÓN HOSPITALARIA --}}
    @if($recepcion)
    <div class="seccion pastel3">
        <div class="subtitulo" style="color:#54b12a;">Recepción Hospitalaria</div>
        <div><b>Responsable:</b> {{ $recepcion->nombre_responsable }}</div>
        <div><b>Fecha recepción:</b> {{ $recepcion->created_at?->format('d/m/Y H:i') }}</div>
        <div><b>Observaciones:</b> {{ $recepcion->observaciones ?? 'Ninguna' }}</div>
        @php
            $checkItems = is_array($recepcion->checklist) ? $recepcion->checklist : json_decode($recepcion->checklist, true);
        @endphp
        @if(is_array($checkItems))
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Componente</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($checkItems as $productoId => $comps)
                    @foreach($comps as $comp => $estado)
                        <tr>
                            <td>
                                @php $prod = $productos->firstWhere('id', $productoId); @endphp
                                {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                            </td>
                            <td>{{ $comp }}</td>
                            <td>{{ ucfirst($estado) }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        @endif
        @if(!empty($recepcion->firma_recepcion))
            <div>
                <b>Firma Recepción:</b><br>
                <img class="firma-img" src="{{ public_path('storage/' . $recepcion->firma_recepcion) }}">
            </div>
        @endif
    </div>
    @endif

    <div class="footer">
        Generado automáticamente por Medibuy el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
