@extends('layouts.app')
@section('title', 'Financiamientos')
@section('titulo', 'Financiamientos')

@section('content')
@include('partials.submenu-cotizaciones')
<div class="submenu-page-spacer" aria-hidden="true"></div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/deudores.css') }}?v={{ time() }}">
<style>
  .type-filter-wrap{display:flex;gap:8px;align-items:center;padding:6px;border-radius:18px;background:rgba(255,255,255,.72);border:1px solid rgba(148,163,184,.22);box-shadow:0 8px 24px rgba(15,23,42,.06);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);margin-top:10px;}
  .type-btn{border:0;border-radius:14px;padding:9px 13px;font-weight:600;font-size:.82rem;color:#475569;background:transparent;transition:.2s ease;white-space:nowrap;}
  .type-btn:hover{background:rgba(15,23,42,.06);color:#0f172a;}
  .type-btn.active{color:#0f172a;background:rgba(16,185,129,.18);border:1px solid rgba(16,185,129,.35);box-shadow:0 10px 18px rgba(16,185,129,.15);}
  .type-btn i{margin-right:5px;}
  @media (min-width: 768px) {
    .container.py-3 {
        margin-left: calc(88px + 32px) !important;
        max-width: calc(100% - 88px - 48px) !important;
        overflow: visible !important;
    }
}
    .filter-toolbar,
    .dropdown-filter-wrap,
    .dropdown {
        overflow: visible !important;
    }
    
    .dropdown-filter-menu {
        max-height: none !important;
        overflow-y: visible !important;
        top: 100% !important;
        margin-top: 0.25rem !important;
        min-width: 220px !important;
  white-space: normal !important;
  width: auto !important;        
}

.dropdown-filter-item {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  white-space: normal !important;  

.dropdown-filter-item .form-check-label {
  flex: 1;          
  white-space: normal !important;
  word-break: break-word;
}

/* Checklist en Moviles */
@media (max-width: 576px) {
  .dropdown-filter-menu {
    max-width: 90vw !important;
    left: 0 !important;
    right: auto !important;
  }
}
  }            
.dropdown-filter-wrap {
    margin-top: 10px;
}

.dropdown-filter-btn {
    border: 1px solid rgba(148, 163, 184, 0.22);
    border-radius: 18px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: .85rem;
    color: #1e293b;
    background: rgba(255, 255, 255, 0.72);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    transition: all .2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dropdown-filter-btn:hover,
.dropdown-filter-btn:focus {
    background: #fff;
    border-color: rgba(148, 163, 184, 0.4);
    color: #0f172a;
}

/* Menú desplegable con fondo semi-transparente  */
.dropdown-filter-menu {
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.25);
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
    padding: 8px;
    min-width: 240px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    z-index: 9999 !important;
    overflow: visible !important;
    white-space: normal !important;
}

.dropdown-filter-item {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 10px 12px !important;
    border-radius: 10px;
    transition: background 0.15s ease;
    white-space: normal !important;
}

.dropdown-filter-item:hover {
    background: rgba(15, 23, 42, 0.08);
}

.dropdown-filter-item .form-check-input {
    width: 1.1em;
    height: 1.1em;
    margin-top: 0;
    cursor: pointer;
    flex-shrink: 0;
}

.dropdown-filter-item .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

/* Label del checkbox */
.dropdown-filter-item .form-check-label {
    flex: 1;
    font-size: 0.85rem;
    font-weight: 500;
    color: #1e293b;
    cursor: pointer;
    white-space: normal !important;
    word-break: break-word;
    line-height: 1.4;
}

/* Separador */
.dropdown-filter-menu .dropdown-divider {
    margin: 0.5rem 0;
    border-top-color: rgba(148, 163, 184, 0.3);
}

/* Ajuste para pantallas pequeñas*/
@media (max-width: 576px) {
    .dropdown-filter-menu {
        max-width: calc(100vw - 32px);
        left: 0 !important;
        right: auto !important;
    }
}
.dropdown-filter-menu {
    overflow: visible !important;
}

.dropdown-filter-item {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 10px 16px !important;
    overflow: visible !important;
}

.dropdown-filter-item .form-check-input {
    width: 1.1rem !important;
    height: 1.1rem !important;
    margin: 0 !important;       
    flex-shrink: 0 !important;
    transform: none !important;
    background-color: #fff;
    border: 1px solid #cbd5e1;
}

/* Checkbox cuando está marcado */
.dropdown-filter-item .form-check-input:checked {
    background-color: #10b981 !important;
    border-color: #10b981 !important;
}
.dropdown-filter-item .form-check-label {
    margin: 0 !important;
    line-height: 1.4 !important;
    padding: 0 !important;
}
</style>

@php
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PagoRecordatorio;

/* ✅ si NO viene $ordenes desde el controller, evitamos error */
$ordenes = $ordenes ?? ($os ?? ($ordenesServicio ?? ($ordenes_servicio ?? collect())));

/* =========================================================
   ✅ PERMISOS: SOLO ADMIN VE DINERO / RESÚMENES
   ========================================================= */
$user = auth()->user();
$isAdmin =
    auth()->check() && (
        (($user->is_admin ?? false) === true)
        || (($user->role ?? null) === 'admin')
        || (method_exists($user, 'hasRole') && $user->hasRole('admin'))
    );

/* ===== Helpers de servidor ===== */
if (!function_exists('extraerPagoInicialDePagos')) {
    function extraerPagoInicialDePagos($pagos) {
        foreach ($pagos as $pago) {
            if (
                stripos((string)$pago->descripcion, 'Pago inicial') !== false
                || stripos((string)$pago->descripcion, 'Enganche') !== false
            ) {
                return (float) $pago->monto;
            }
        }
        return 0;
    }
}

if (!function_exists('extraerFechasDePagos')) {
    function extraerFechasDePagos($pagos) {
        $fechas = [];
        foreach ($pagos as $pago) {
            if ($pago->fecha_pago) {
                try {
                    $fechas[] = Carbon::parse($pago->fecha_pago);
                } catch (\Exception $e) {}
            }
        }
        return $fechas;
    }
}

if (!function_exists('obtenerPagoVencidoMasAntiguo')) {
    function obtenerPagoVencidoMasAntiguo($pagosPendientes) {
        $masAntiguo = null;

        foreach ($pagosPendientes as $p) {
            if ($p->fecha_pago) {
                try {
                    $f   = Carbon::parse($p->fecha_pago)->startOfDay();
                    $hoy = Carbon::today();

                    if ($f->lt($hoy)) {
                        if (!$masAntiguo || $f->lt($masAntiguo)) {
                            $masAntiguo = $f;
                        }
                    }
                } catch (\Throwable $e) {}
            }
        }

        return $masAntiguo;
    }
}

/** Ya notificado HOY por email o whatsapp para este pago? */
if (!function_exists('yaNotificadoHoy')) {
    function yaNotificadoHoy(int $pagoId): bool {
        return PagoRecordatorio::where('pago_financiamiento_id', $pagoId)
            ->whereIn('channel', ['email','whatsapp'])
            ->whereDate('sent_at', Carbon::today()->toDateString())
            ->exists();
    }
}

/* ✅ helper para NO romper si no existe una route */
if (!function_exists('route_or_url')) {
    function route_or_url($name, $params, $fallbackUrl) {
        try {
            $router = app('router');
            if ($router && $router->has($name)) {
                return route($name, $params);
            }
        } catch (\Throwable $e) {}
        return $fallbackUrl;
    }
}

/* =========================================================
   ✅ Helper central: obtiene estado real de una venta
   usando la misma lógica base de remisión
   ========================================================= */
if (!function_exists('resolverEstadoFinanciamientoVenta')) {
    function resolverEstadoFinanciamientoVenta($venta): array
    {
        /*
         * REGLA PRINCIPAL:
         * La tabla pagos manda SIEMPRE.
         *
         * - Si existe pago aprobado en pagos, se cuenta como pagado.
         * - Si eliminas o reviertes ese pago, deja de contar.
         * - Aunque pagos_financiamiento.pagado se haya quedado en true,
         *   aquí NO se usa esa bandera para liquidar ni para ocultar deuda.
         */

        $pagosVenta = \App\Models\Pago::where('venta_id', $venta->id)->get();

        $pagosVentaAprobados = $pagosVenta
            ->filter(function ($p) {
                return (bool)($p->aprobado ?? false);
            })
            ->values();

        /*
         * Cuotas del plan que tienen un pago real aprobado relacionado.
         * Este ID es la única prueba confiable de que esa cuota ya fue pagada.
         */
        $finIdsPagados = $pagosVentaAprobados
            ->pluck('financiamiento_id')
            ->filter()
            ->unique()
            ->values();

        $pagosPlan = collect($venta->pagosFinanciamiento ?? [])
            ->filter(function ($p) {
                return !((bool)($p->cancelado ?? false));
            })
            ->values();

        /*
         * Pagos del plan realmente pagados.
         * NO usamos $p->pagado porque puede quedarse marcado aunque borres el pago.
         */
        $pagosPagados = $pagosPlan
            ->filter(function ($p) use ($finIdsPagados) {
                return $finIdsPagados->contains($p->id)
                    && (float)($p->monto ?? 0) > 0;
            })
            ->values();

        /*
         * Pendientes reales.
         * Si no hay un pago aprobado ligado a la cuota, sigue pendiente.
         * Esto hace que deudores cambie automáticamente cuando borras/reviertes pagos.
         */
        $pagosPendientes = $pagosPlan
            ->filter(function ($p) use ($finIdsPagados) {
                return !$finIdsPagados->contains($p->id)
                    && (float)($p->monto ?? 0) > 0;
            })
            ->sortBy('fecha_pago')
            ->values();

        $pagoInicial = extraerPagoInicialDePagos($pagosVentaAprobados);

        $totalPagadoPlan = (float) $pagosPagados->sum(function ($p) {
            return (float)($p->monto ?? 0);
        });

        $totalPendientePlan = (float) $pagosPendientes->sum(function ($p) {
            return (float)($p->monto ?? 0);
        });

        $totalOriginal = (float) ($venta->total_original ?? $venta->total ?? 0);

        $totalNeto = (float) (
            $venta->total_contrato
            ?? $venta->total_neto
            ?? $venta->total
            ?? $totalOriginal
        );

        /*
         * Total real pagado.
         * SOLO suma pagos aprobados de la tabla pagos.
         */
        $totalPagadoGlobal = (float) $pagosVentaAprobados->sum(function ($p) {
            return (float)($p->monto ?? 0);
        });

        $montoAnticipo = (float) $pagosVentaAprobados
            ->filter(function($p){
                return (bool) ($p->es_anticipo ?? false);
            })
            ->sum('monto');

        $montoTradeIn = (float) $pagosVentaAprobados
            ->filter(function($p){
                $metodo = strtolower(trim(
                    $p->metodo
                    ?? $p->metodo_pago
                    ?? $p->forma_pago
                    ?? ''
                ));

                return in_array($metodo, ['trade-in', 'trade in', 'tradein'], true);
            })
            ->sum('monto');

        /*
         * Saldo real por dinero.
         */
        $restanteFinanciamiento = max(0, $totalNeto - $totalPagadoGlobal);

        /*
         * Si todavía falta dinero, también respetamos cuotas pendientes.
         * Liquidada solamente cuando el dinero aprobado cubre el total.
         */
        $restantePorCuotas = ($restanteFinanciamiento <= 0.01)
            ? 0
            : max(0, $totalPendientePlan);

        $restanteGlobal = max($restanteFinanciamiento, $restantePorCuotas);

        $fechasPago = ($restanteGlobal <= 0.01)
            ? []
            : extraerFechasDePagos($pagosPendientes);

        $pagoHoy = collect($fechasPago)->contains(function ($f) {
            return $f->isToday();
        });

        $vencidoMasAntiguo = ($restanteGlobal <= 0.01)
            ? null
            : obtenerPagoVencidoMasAntiguo($pagosPendientes);

        $diasAtraso = $vencidoMasAntiguo
            ? (int) $vencidoMasAntiguo->diffInDays(Carbon::today())
            : 0;

        $tienePagosPendientesReales = $restanteGlobal > 0.01
            && $pagosPendientes->count() > 0
            && $totalPendientePlan > 0.01;

        if ($restanteGlobal <= 0.01) {
            $estado = 'liquidada';
        } elseif ($vencidoMasAntiguo) {
            $estado = 'atrasada';
        } else {
            $estado = 'pendiente';
        }

        $pagoObjetivo = $pagosPendientes->first(function($p) use ($vencidoMasAntiguo, $pagoHoy, $restanteGlobal) {
            if ($restanteGlobal <= 0.01) {
                return false;
            }

            try {
                if ($vencidoMasAntiguo) {
                    return $p->fecha_pago
                        && Carbon::parse($p->fecha_pago)->isSameDay($vencidoMasAntiguo);
                }

                if ($pagoHoy) {
                    return $p->fecha_pago
                        && Carbon::parse($p->fecha_pago)->isToday();
                }
            } catch (\Throwable $e) {}

            return false;
        });

        return [
            'pagos_venta'                 => $pagosVenta,
            'pagos_venta_aprobados'       => $pagosVentaAprobados,
            'fin_ids_pagados'             => $finIdsPagados,
            'pagos_plan'                  => $pagosPlan,
            'pagos_pendientes'            => $pagosPendientes,
            'pagos_pagados'               => $pagosPagados,
            'pago_inicial'                => $pagoInicial,
            'total_pagado_plan'           => $totalPagadoPlan,
            'total_pendiente_plan'        => $totalPendientePlan,
            'total_original'              => $totalOriginal,
            'total_neto'                  => $totalNeto,
            'monto_anticipo'              => $montoAnticipo,
            'monto_trade_in'              => $montoTradeIn,
            'total_pagado_global'         => $totalPagadoGlobal,
            'restante_financiamiento'     => $restanteFinanciamiento,
            'restante_por_cuotas'         => $restantePorCuotas,
            'restante_global'             => $restanteGlobal,
            'fechas_pago'                 => $fechasPago,
            'pago_hoy'                    => $pagoHoy,
            'vencido_mas_antiguo'         => $vencidoMasAntiguo,
            'dias_atraso'                 => $diasAtraso,
            'tiene_pagos_pendientes'      => $tienePagosPendientesReales,
            'estado'                      => $estado,
            'pago_objetivo'               => $pagoObjetivo,
        ];
    }
}

/* ===== Métricas y colecciones para banners + resumen financiero ===== */
$ventasConPagosProximos = [];
$ventasConPagosVencidos = [];

/* ✅ ahora incluye ventas + órdenes */
$metrics = ['total'=>0,'ventas'=>0,'ordenes'=>0,'pendientes'=>0,'atrasadas'=>0,'liquidadas'=>0,'saldo'=>0.0];

/* Totales globales de dinero */
$moneyGlobal = [
    'original' => 0.0,
    'pagado'   => 0.0,
    'restante' => 0.0,
];

/* Totales por estado (dinero) */
$moneyByStatus = [
    'pendiente' => ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0],
    'atrasada'  => ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0],
    'liquidada' => ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0],
];

/* Resumen por año y por mes */
$summaryByYear  = [];
$summaryByMonth = [];

/* ==========================================================
   ✅ MÉTRICAS DE VENTAS CORREGIDAS
   ========================================================== */
foreach ($ventas as $ventaTmp) {
    $metrics['total']++;
    $metrics['ventas']++;

    $calcVentaTmp = resolverEstadoFinanciamientoVenta($ventaTmp);

    $totalOriginalTmp     = (float) $calcVentaTmp['total_original'];
    $totalPagadoGlobalTmp = (float) $calcVentaTmp['total_pagado_global'];
    $restanteGlobalTmp    = (float) $calcVentaTmp['restante_global'];
    $estadoTmp            = $calcVentaTmp['estado'];
    $pagosPendientesTmp   = $calcVentaTmp['pagos_pendientes'];
    $vencidoMasAntiguoTmp = $calcVentaTmp['vencido_mas_antiguo'];

    $moneyGlobal['original'] += $totalOriginalTmp;
    $moneyGlobal['pagado']   += $totalPagadoGlobalTmp;
    $moneyGlobal['restante'] += $restanteGlobalTmp;

    $fechaVentaTmp = $ventaTmp->created_at ? Carbon::parse($ventaTmp->created_at) : null;
    if ($fechaVentaTmp) {
        $yearKey  = $fechaVentaTmp->format('Y');
        $monthKey = $fechaVentaTmp->format('Y-m');

        if (!isset($summaryByYear[$yearKey])) {
            $summaryByYear[$yearKey] = ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
        }
        $summaryByYear[$yearKey]['original'] += $totalOriginalTmp;
        $summaryByYear[$yearKey]['pagado']   += $totalPagadoGlobalTmp;
        $summaryByYear[$yearKey]['restante'] += $restanteGlobalTmp;

        if (!isset($summaryByMonth[$monthKey])) {
            $summaryByMonth[$monthKey] = [
                'label'    => $fechaVentaTmp->translatedFormat('F Y'),
                'original' => 0.0,
                'pagado'   => 0.0,
                'restante' => 0.0,
            ];
        }
        $summaryByMonth[$monthKey]['original'] += $totalOriginalTmp;
        $summaryByMonth[$monthKey]['pagado']   += $totalPagadoGlobalTmp;
        $summaryByMonth[$monthKey]['restante'] += $restanteGlobalTmp;
    }

    if ($estadoTmp === 'liquidada') {
        $metrics['liquidadas']++;
    } elseif ($estadoTmp === 'atrasada') {
        $metrics['atrasadas']++;
        $metrics['saldo'] += $restanteGlobalTmp;
    } else {
        $metrics['pendientes']++;
        $metrics['saldo'] += $restanteGlobalTmp;
    }

    if (isset($moneyByStatus[$estadoTmp])) {
        $moneyByStatus[$estadoTmp]['original'] += $totalOriginalTmp;
        $moneyByStatus[$estadoTmp]['pagado']   += $totalPagadoGlobalTmp;
        $moneyByStatus[$estadoTmp]['restante'] += $restanteGlobalTmp;
    }

    $fechas = $calcVentaTmp['fechas_pago'];
    foreach ($fechas as $fecha) {
        if ($fecha->isToday() || $fecha->isTomorrow() || $fecha->between(Carbon::now(), Carbon::now()->addDays(7), true)) {
            $ventasConPagosProximos[] = ['venta'=>$ventaTmp,'fecha'=>$fecha];
            break;
        }
    }

    if ($vencidoMasAntiguoTmp) {
        $pagoVencido = $pagosPendientesTmp->first(function($p) use ($vencidoMasAntiguoTmp) {
            try {
                return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isSameDay($vencidoMasAntiguoTmp);
            } catch (\Throwable $e) {
                return false;
            }
        });

        $ventasConPagosVencidos[] = [
            'venta' => $ventaTmp,
            'fecha' => $vencidoMasAntiguoTmp,
            'pago'  => $pagoVencido,
            'dias'  => (int) $vencidoMasAntiguoTmp->diffInDays(Carbon::today()),
            'ya'    => $pagoVencido ? yaNotificadoHoy($pagoVencido->id) : false,
        ];
    }
}

/* ==========================================================
   ✅ MÉTRICAS DE ÓRDENES DE SERVICIO
   ========================================================== */
foreach ($ordenes as $ordenTmp) {
    $metrics['total']++;
    $metrics['ordenes']++;

    $cant = (float)($ordenTmp->remision_cantidad ?? 0);
    $prec = (float)($ordenTmp->remision_precio ?? 0);
    $totalOriginalTmp = (float)($ordenTmp->remision_subtotal ?? 0);
    if ($totalOriginalTmp <= 0 && $cant > 0 && $prec > 0) {
        $totalOriginalTmp = $cant * $prec;
    }

    $pagosOrdenTmp = \App\Models\Pago::where('orden_id', $ordenTmp->id)
        ->where('aprobado', true)
        ->get();

    $totalPagadoGlobalTmp = (float) $pagosOrdenTmp->sum('monto');
    $restanteGlobalTmp    = max(0, $totalOriginalTmp - $totalPagadoGlobalTmp);

    $moneyGlobal['original'] += $totalOriginalTmp;
    $moneyGlobal['pagado']   += $totalPagadoGlobalTmp;
    $moneyGlobal['restante'] += $restanteGlobalTmp;

    $fechaTmp = $ordenTmp->created_at ? Carbon::parse($ordenTmp->created_at) : null;
    if ($fechaTmp) {
        $yearKey  = $fechaTmp->format('Y');
        $monthKey = $fechaTmp->format('Y-m');

        if (!isset($summaryByYear[$yearKey])) {
            $summaryByYear[$yearKey] = ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
        }
        $summaryByYear[$yearKey]['original'] += $totalOriginalTmp;
        $summaryByYear[$yearKey]['pagado']   += $totalPagadoGlobalTmp;
        $summaryByYear[$yearKey]['restante'] += $restanteGlobalTmp;

        if (!isset($summaryByMonth[$monthKey])) {
            $summaryByMonth[$monthKey] = [
                'label'    => $fechaTmp->translatedFormat('F Y'),
                'original' => 0.0,
                'pagado'   => 0.0,
                'restante' => 0.0,
            ];
        }
        $summaryByMonth[$monthKey]['original'] += $totalOriginalTmp;
        $summaryByMonth[$monthKey]['pagado']   += $totalPagadoGlobalTmp;
        $summaryByMonth[$monthKey]['restante'] += $restanteGlobalTmp;
    }

    if ($restanteGlobalTmp <= 0.01) {
        $estadoTmp = 'liquidada';
        $metrics['liquidadas']++;
    } else {
        $estadoTmp = 'pendiente';
        $metrics['pendientes']++;
        $metrics['saldo'] += $restanteGlobalTmp;
    }

    if (isset($moneyByStatus[$estadoTmp])) {
        $moneyByStatus[$estadoTmp]['original'] += $totalOriginalTmp;
        $moneyByStatus[$estadoTmp]['pagado']   += $totalPagadoGlobalTmp;
        $moneyByStatus[$estadoTmp]['restante'] += $restanteGlobalTmp;
    }
}

/* Ordenamos año y mes para el modal */
ksort($summaryByYear);
ksort($summaryByMonth);
@endphp

<div class="top-progress"><div class="bar" id="topBar"></div></div>

<div class="container py-3">
  {{-- ===== Toolbar Desktop ===== --}}
  <div class="filter-toolbar d-none d-md-flex mb-3">
      <div class="dropdown dropdown-filter-wrap">
  <button class="btn dropdown-filter-btn dropdown-toggle" type="button" id="dropdownStatusFilter" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
    <i class="bi bi-filter-square"></i> <span id="dropdownLabel">Todas</span>
  </button>
  <ul class="dropdown-menu dropdown-filter-menu" aria-labelledby="dropdownStatusFilter">
    <li>
      <div class="dropdown-item dropdown-filter-item form-check">
        <input class="form-check-input chk-status-all" type="checkbox" value="all" id="chkAll" checked>
        <label class="form-check-label" for="chkAll">Todas</label>
      </div>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
      <div class="dropdown-item dropdown-filter-item form-check">
        <input class="form-check-input chk-status" type="checkbox" value="pendiente" id="chkPendiente">
        <label class="form-check-label" for="chkPendiente">Pendientes ({{ $metrics['pendientes'] }})</label>
      </div>
    </li>
    <li>
      <div class="dropdown-item dropdown-filter-item form-check">
        <input class="form-check-input chk-status" type="checkbox" value="atrasada" id="chkAtrasada">
        <label class="form-check-label" for="chkAtrasada">Atrasados ({{ $metrics['atrasadas'] }})</label>
      </div>
    </li>
    <li>
      <div class="dropdown-item dropdown-filter-item form-check">
        <input class="form-check-input chk-status" type="checkbox" value="liquidada" id="chkLiquidada">
        <label class="form-check-label" for="chkLiquidada">Liquidadas ({{ $metrics['liquidadas'] }})</label>
      </div>
    </li>
  </ul>
</div>

    <div class="type-filter-wrap" role="tablist" aria-label="Filtro de tipo">
      <button type="button" class="type-btn active" data-type="all" aria-pressed="true"><i class="bi bi-grid-3x3-gap"></i>Todo</button>
      <button type="button" class="type-btn" data-type="venta" aria-pressed="false"><i class="bi bi-receipt"></i>Ventas ({{ $metrics['ventas'] }})</button>
      <button type="button" class="type-btn" data-type="os" aria-pressed="false"><i class="bi bi-tools"></i>OS ({{ $metrics['ordenes'] }})</button>
    </div>

    <div class="search-chip" title="Buscar por nombre, #venta o cantidad">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input id="txtSearchDesktop" type="text" placeholder="Buscar… (nombre, #remisión / OS o cantidad)">
      <button class="clear-btn" id="btnClearSearchDesktop" title="Limpiar">×</button>
    </div>

    @if($isAdmin)
      <div class="ms-auto d-flex gap-2 align-items-center summary-badges">
        <span class="badge" style="background:#eff6ff;color:#1d4ed8;">Registros: {{ $metrics['total'] }}</span>
        <span class="badge" style="background:#e5e7eb;color:#111827;">Saldo: ${{ number_format($metrics['saldo'],2) }}</span>
      </div>
    @endif
  </div>

  {{-- ===== Botón filtros: Móvil ===== --}}
  <button class="btn-filter d-md-none mb-3" id="openFilterSheet" aria-haspopup="dialog" aria-controls="filterSheet">
    Filtros y búsqueda
  </button>

  <div class="sheet-overlay" id="sheetOverlay" aria-hidden="true"></div>
  <div class="filter-sheet d-md-none" id="filterSheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
    <div class="sheet-handle"></div>
    <h6 class="sheet-title mb-3" id="sheetTitle">Filtrar financiamientos</h6>

    <div class="mb-3">
      <label class="form-label small text-muted">Tipo</label>
      <div class="d-flex gap-2 pill-group" id="tipoMob">
        <label class="pill active"><input type="radio" name="tipoMob" value="all" checked> Todo</label>
        <label class="pill"><input type="radio" name="tipoMob" value="venta"> Ventas</label>
        <label class="pill"><input type="radio" name="tipoMob" value="os"> OS</label>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label small text-muted">Estado</label>
      <div class="d-flex gap-2 pill-group" id="estadoMob">
        <label class="pill active"><input type="radio" name="estadoMob" value="all" checked> Todas</label>
        <label class="pill"><input type="radio" name="estadoMob" value="pendiente"> Pendientes</label>
        <label class="pill"><input type="radio" name="estadoMob" value="atrasada"> Atrasadas</label>
        <label class="pill"><input type="radio" name="estadoMob" value="liquidada"> Liquidadas</label>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label small text-muted">Buscar</label>
      <div class="input-chip">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" id="txtSearchMobile" placeholder="Nombre, #remisión / OS o cantidad… (>, <, 800-1200)">
      </div>
    </div>

    @if($isAdmin)
      <div class="mb-3">
        <label class="form-label small text-muted">Buscar cantidad en</label>
        <div class="d-flex gap-2 pill-group">
          <label class="pill active"><input type="radio" name="campoMob" value="restante" checked> Restante</label>
          <label class="pill"><input type="radio" name="campoMob" value="total"> Total</label>
          <label class="pill"><input type="radio" name="campoMob" value="pagado"> Pagado</label>
        </div>
      </div>
    @endif

    <div class="d-flex gap-2">
      <button class="btn-ghost w-50" id="btnClear">Limpiar</button>
      <button class="btn-apply w-50" id="btnApply">Aplicar</button>
    </div>
  </div>

  <h1 class="titulo-principal">Financiamientos</h1>
  <div class="subtitulo mb-2">
    Control de pagos, atrasos y saldo global.
  </div>

  @if($isAdmin ?? false)
    <div class="d-flex flex-wrap gap-2 mb-3">
      <a href="{{ route('financiamientos.auditoria') }}" class="btn btn-soft-info">
        Ver auditoría inteligente
      </a>
    </div>
  @endif

  <div id="noResults" class="no-ventas d-none mt-2">No hay resultados con los filtros aplicados.</div>
  <div id="resultToast" class="toast-results"></div>

  @php
    $hayVentas  = isset($ventas) && $ventas->count();
    $hayOrdenes = isset($ordenes) && $ordenes->count();
  @endphp

  @if(!$hayVentas && !$hayOrdenes)
    <div class="no-ventas mt-3">No hay registros.</div>
  @else
    <div id="ventasGrid" class="row g-4 mt-1">
      {{-- =========================================================
           ✅ VENTAS
           ========================================================= --}}
      @if($hayVentas)
        @foreach($ventas as $venta)
          @php
            $calcVenta = resolverEstadoFinanciamientoVenta($venta);

            $pagosPlan         = $calcVenta['pagos_plan'];
            $pagosPendientes   = $calcVenta['pagos_pendientes'];
            $pagosPagados      = $calcVenta['pagos_pagados'];
            $pagoInicial       = (float) $calcVenta['pago_inicial'];
            $totalPagadoPlan   = (float) $calcVenta['total_pagado_plan'];
            $totalOriginal     = (float) $calcVenta['total_original'];
            $totalNeto         = (float) $calcVenta['total_neto'];
            $montoAnticipo     = (float) $calcVenta['monto_anticipo'];
            $montoTradeIn      = (float) $calcVenta['monto_trade_in'];
            $totalPagadoGlobal = (float) $calcVenta['total_pagado_global'];
            $restanteGlobal    = (float) $calcVenta['restante_global'];
            $fechasPago        = $calcVenta['fechas_pago'];
            $pagoHoy           = $calcVenta['pago_hoy'];
            $vencidoMasAntiguo = $calcVenta['vencido_mas_antiguo'];
            $diasAtraso        = (int) $calcVenta['dias_atraso'];
            $estado            = $calcVenta['estado'];
            $pagoObjetivo      = $calcVenta['pago_objetivo'];

            $clienteNombre = trim((optional($venta->cliente)->nombre.' '.optional($venta->cliente)->apellido) ?? '');
            $yaNotificado = $pagoObjetivo ? yaNotificadoHoy($pagoObjetivo->id) : false;

            $primerProductoNombre = null;
            $primerProductoSub    = null;
            $primerProductoSrc    = null;

            $primerItem = $venta->productos->first();
            if ($primerItem && $primerItem->producto) {
                $p = $primerItem->producto;

                if (!empty($p->tipo_equipo)) {
                    $primerProductoNombre = mb_strtoupper($p->tipo_equipo, 'UTF-8');
                } elseif (!empty($p->nombre)) {
                    $primerProductoNombre = mb_strtoupper($p->nombre, 'UTF-8');
                }

                $subPartes = [];
                if (!empty($p->modelo)) {
                    $subPartes[] = mb_strtoupper($p->modelo, 'UTF-8');
                }
                if (!empty($p->marca)) {
                    $subPartes[] = mb_strtoupper($p->marca, 'UTF-8');
                }
                $primerProductoSub = count($subPartes) ? implode(' | ', $subPartes) : null;

                $primerProductoImg = $p->imagen_url ?? $p->imagen ?? null;

                if ($primerProductoImg) {
                    if (Str::startsWith($primerProductoImg, ['http://','https://'])) {
                        $primerProductoSrc = $primerProductoImg;
                    } elseif (Str::startsWith($primerProductoImg, ['storage/','images/'])) {
                        $primerProductoSrc = asset(ltrim($primerProductoImg, '/'));
                    } else {
                        $primerProductoSrc = asset('storage/'.ltrim($primerProductoImg, '/'));
                    }
                }
            }
          @endphp

          <div class="col-md-6 col-lg-4 venta-item"
               data-tipo="venta"
               data-estado="{{ $estado }}"
               data-id="{{ $venta->id }}"
               data-cliente="{{ Str::of($clienteNombre)->lower() }}"
               @if($isAdmin)
                 data-total="{{ (float)$totalOriginal }}"
                 data-pagado="{{ (float)$totalPagadoGlobal }}"
                 data-restante="{{ (float)$restanteGlobal }}"
               @else
                 data-total="0"
                 data-pagado="0"
                 data-restante="0"
               @endif
               data-atraso="{{ (int)$diasAtraso }}">
            <div class="card-venta h-100">
              <div class="card-venta-header">
                <div>
                  <div class="cliente-nombre js-name">{{ $clienteNombre }}</div>
                  <div class="venta-id js-id">Remisión: 2025-{{ $venta->id }}</div>
                </div>

                @if($estado === 'liquidada')
                  <span class="estado-liquidada badge">Liquidada</span>
                @elseif($estado === 'atrasada')
                  <span class="estado-atrasada badge">Atrasada</span>
                @else
                  <span class="estado-pendiente badge">Pendiente</span>
                @endif
              </div>

              <div class="detalle-venta mt-3">
                @if($primerProductoNombre)
                  <div class="producto-mini">
                    <div class="prod-thumb">
                      @if($primerProductoSrc)
                        <img src="{{ $primerProductoSrc }}" alt="{{ $primerProductoNombre }}">
                      @else
                        <div class="prod-thumb-fallback">
                          {{ mb_substr($primerProductoNombre, 0, 1, 'UTF-8') }}
                        </div>
                      @endif
                    </div>

                    <div>
                      <div class="prod-name">{{ $primerProductoNombre }}</div>
                      @if($primerProductoSub)
                        <div class="prod-sub">{{ $primerProductoSub }}</div>
                      @endif
                    </div>
                  </div>
                @endif

                @if($estado === 'atrasada')
                  <div class="d-flex align-items-center mb-2">
                    <span class="pill-atraso" title="Días de atraso">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#b91c1c" viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zM13 17h-2v-2h2v2zm0-4h-2V9h2v4z"/>
                      </svg>
                      @if($diasAtraso === 1)
                        1 día de atraso
                      @else
                        {{ $diasAtraso }} días de atraso
                      @endif
                    </span>
                  </div>
                @elseif($pagoHoy && $restanteGlobal > 0.01)
                  <div class="alert d-flex align-items-center mb-3 p-2 px-3" style="border-radius:12px;border:1px solid #fecaca;background:#fef2f2;color:var(--danger-ink);font-size:.8rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="18" height="18" fill="#dc2626" viewBox="0 0 24 24">
                      <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2V9h2v4z"/>
                    </svg>
                    <strong class="me-1">Hoy hay un pago de este plan.</strong>
                  </div>
                @endif

                <p><strong>Emitido:</strong> {{ Carbon::parse($venta->created_at)->format('d/m/Y') }}</p>

                @if($isAdmin)
                  <p><strong>Total venta:</strong> ${{ number_format($totalOriginal, 2) }}</p>

                  @if($montoTradeIn > 0)
                    <p><strong>Trade-in aplicado:</strong> -${{ number_format($montoTradeIn, 2) }}</p>
                  @endif

                  @if($montoAnticipo > 0)
                    <p><strong>Anticipo:</strong> -${{ number_format($montoAnticipo, 2) }}</p>
                  @endif

                  @if(abs($totalNeto - $totalOriginal) > 0.01)
                    <p><strong>Total neto (a financiar):</strong> ${{ number_format($totalNeto, 2) }}</p>
                  @endif

                  @if($pagoInicial > 0)
                    <p><strong>Pago inicial del plan:</strong> ${{ number_format($pagoInicial, 2) }}</p>
                  @endif

                  <p>
                    <strong>Total pagado (trade-in + anticipo + plan):</strong>
                    ${{ number_format($totalPagadoGlobal, 2) }}
                  </p>

                  <p>
                    <strong>Restante por pagar:</strong>
                    <span class="restante js-restante">${{ number_format($restanteGlobal, 2) }}</span>
                  </p>
                @endif

                @if(count($fechasPago))
                  <p class="mt-2 mb-1"><strong>Fechas de pagos pendientes del plan:</strong></p>
                  <ul class="mb-0">
                    @foreach($fechasPago as $f)
                      <li>{{ $f->isoFormat('DD [de] MMMM [de] YYYY') }}</li>
                    @endforeach
                  </ul>
                @endif
              </div>

              <div class="card-actions mt-3 px-1">
                <a href="{{ route('ventas.pagos.index', $venta->id) }}" class="btn btn-soft-info">
                  Ver pagos
                </a>

                <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-soft-success">
                  Ver remisión
                </a>

                @if($pagoObjetivo && !$yaNotificado)
                  <button class="btn-send reenviar-btn" data-pago-id="{{ $pagoObjetivo->id }}">
                    <span class="spinner" aria-hidden="true"></span>
                    <svg class="check" viewBox="0 0 24 24" fill="none">
                      <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="label">{{ $estado === 'atrasada' ? 'Avisar por WhatsApp' : 'Notificar' }}</span>
                  </button>
                @elseif($pagoObjetivo && $yaNotificado)
                  <span class="badge rounded-pill bg-secondary align-self-center" style="font-size:.78rem;">
                    Notificado hoy
                  </span>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      @endif

      {{-- =========================================================
           ✅ ÓRDENES DE SERVICIO
           ========================================================= --}}
      @if($hayOrdenes)
        @foreach($ordenes as $orden)
          @php
            $clienteOrden = trim((optional($orden->cliente)->nombre.' '.optional($orden->cliente)->apellido) ?? '');
            if(!$clienteOrden) $clienteOrden = 'SIN CLIENTE';

            $cant = (float)($orden->remision_cantidad ?? 0);
            $prec = (float)($orden->remision_precio ?? 0);
            $totalOS = (float)($orden->remision_subtotal ?? 0);
            if ($totalOS <= 0 && $cant > 0 && $prec > 0) $totalOS = $cant * $prec;

            $pagosOS = \App\Models\Pago::where('orden_id', $orden->id)->where('aprobado', true)->get();
            $pagadoOS = (float)$pagosOS->sum('monto');
            $restanteOS = max(0, $totalOS - $pagadoOS);

            $estadoOS = $restanteOS <= 0.01 ? 'liquidada' : 'pendiente';

            $descOS = $orden->remision_descripcion
              ?? ('MANTENIMIENTO DE ' . mb_strtoupper(($orden->equipo ?? 'EQUIPO'), 'UTF-8'));

            $osIdText = 'OS: 2025-' . $orden->id;

            $urlPagosOS = route_or_url('ordenes.pagos.index', $orden->id, url('/ordenes/'.$orden->id.'/pagos'));
            $urlPdfOS   = route_or_url('ordenes.remision.pdf', $orden->id, url('/ordenes/'.$orden->id.'/remision-pdf'));
          @endphp

          <div class="col-md-6 col-lg-4 venta-item"
               data-tipo="os"
               data-estado="{{ $estadoOS }}"
               data-id="OS-{{ $orden->id }}"
               data-cliente="{{ Str::of($clienteOrden)->lower() }}"
               @if($isAdmin)
                 data-total="{{ (float)$totalOS }}"
                 data-pagado="{{ (float)$pagadoOS }}"
                 data-restante="{{ (float)$restanteOS }}"
               @else
                 data-total="0"
                 data-pagado="0"
                 data-restante="0"
               @endif
               data-atraso="0">
            <div class="card-venta h-100">
              <div class="card-venta-header">
                <div>
                  <div class="cliente-nombre js-name">{{ $clienteOrden }}</div>
                  <div class="venta-id js-id">{{ $osIdText }}</div>
                </div>

                @if($estadoOS === 'liquidada')
                  <span class="estado-liquidada badge">Liquidada</span>
                @else
                  <span class="estado-pendiente badge">Pendiente</span>
                @endif
              </div>

              <div class="detalle-venta mt-3">
                <p><strong>Emitido:</strong> {{ $orden->created_at ? Carbon::parse($orden->created_at)->format('d/m/Y') : '—' }}</p>

                <p>
                  <strong>Servicio:</strong>
                  {{ mb_strtoupper(($orden->equipo ?? 'MANTENIMIENTO'), 'UTF-8') }}
                </p>

                <p class="mb-2">
                  <strong>Descripción:</strong><br>
                  <span class="small text-muted">{!! nl2br(e($descOS)) !!}</span>
                </p>

                @if($isAdmin)
                  <p><strong>Total OS:</strong> ${{ number_format($totalOS, 2) }}</p>
                  <p><strong>Total pagado:</strong> ${{ number_format($pagadoOS, 2) }}</p>
                  <p>
                    <strong>Restante por pagar:</strong>
                    <span class="restante js-restante">${{ number_format($restanteOS, 2) }}</span>
                  </p>
                @endif
              </div>

              <div class="card-actions mt-3 px-1">
                <a href="{{ $urlPagosOS }}" class="btn btn-soft-info">
                  Ver pagos
                </a>
                <a href="{{ $urlPdfOS }}" class="btn btn-soft-success">
                  Ver remisión
                </a>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  @endif

  {{-- ===== Modal: Resumen financiero (SOLO ADMIN) ===== --}}
  @if($isAdmin)
    <div class="modal fade" id="resumenFinancieroModal" tabindex="-1" aria-labelledby="resumenFinancieroLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-financial">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title" id="resumenFinancieroLabel">Resumen financiero</h5>
              <div class="small-muted">
                Vista rápida por estado, año y mes. Cifras en MXN.
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body pt-3">
            <h6>Global</h6>
            <div class="row g-2 mb-3">
              <div class="col-md-4">
                <div class="chip-card">
                  <div class="chip-title">Total vendido</div>
                  <div class="chip-value">${{ number_format($moneyGlobal['original'], 2) }}</div>
                  <div class="chip-tag text-muted">Suma de remisiones y órdenes</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chip-card">
                  <div class="chip-title">Total cobrado</div>
                  <div class="chip-value">${{ number_format($moneyGlobal['pagado'], 2) }}</div>
                  <div class="chip-tag text-success">Ventas + órdenes</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chip-card">
                  <div class="chip-title">Saldo pendiente</div>
                  <div class="chip-value">${{ number_format($moneyGlobal['restante'], 2) }}</div>
                  <div class="chip-tag text-danger">Aún por cobrar</div>
                </div>
              </div>
            </div>

            <h6 class="mt-2">Por estado</h6>
            <div class="row g-2 mb-3">
              <div class="col-md-4">
                <div class="chip-card">
                  <div class="chip-title">Pendientes ({{ $metrics['pendientes'] }})</div>
                  <div class="chip-value">${{ number_format($moneyByStatus['pendiente']['restante'] ?? 0, 2) }}</div>
                  <div class="chip-tag">Saldo pendiente (sin atraso)</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chip-card">
                  <div class="chip-title">Atrasadas ({{ $metrics['atrasadas'] }})</div>
                  <div class="chip-value">${{ number_format($moneyByStatus['atrasada']['restante'] ?? 0, 2) }}</div>
                  <div class="chip-tag text-danger">Saldo vencido</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chip-card">
                  <div class="chip-title">Liquidadas ({{ $metrics['liquidadas'] }})</div>
                  <div class="chip-value">${{ number_format($moneyByStatus['liquidada']['pagado'] ?? 0, 2) }}</div>
                  <div class="chip-tag text-success">Cobrado</div>
                </div>
              </div>
            </div>

            <h6 class="mt-3">Por año</h6>
            <div class="small-muted mb-1">Total vendido, cobrado y saldo por año.</div>
            <div class="table-responsive mb-3">
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Año</th>
                    <th class="text-end">Vendido</th>
                    <th class="text-end">Cobrado</th>
                    <th class="text-end">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                @forelse($summaryByYear as $year => $row)
                  <tr>
                    <td>{{ $year }}</td>
                    <td class="text-end">${{ number_format($row['original'], 2) }}</td>
                    <td class="text-end">${{ number_format($row['pagado'], 2) }}</td>
                    <td class="text-end">${{ number_format($row['restante'], 2) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted">Sin información aún.</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>

            <h6 class="mt-3">Por mes</h6>
            <div class="small-muted mb-1">Resumen por mes (Y-m).</div>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Mes</th>
                    <th class="text-end">Vendido</th>
                    <th class="text-end">Cobrado</th>
                    <th class="text-end">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                @forelse($summaryByMonth as $key => $row)
                  <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-end">${{ number_format($row['original'], 2) }}</td>
                    <td class="text-end">${{ number_format($row['pagado'], 2) }}</td>
                    <td class="text-end">${{ number_format($row['restante'], 2) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted">Sin información aún.</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<script>
(function(){
  const $$=(s,c=document)=>Array.from(c.querySelectorAll(s));
  const $ =(s,c=document)=>c.querySelector(s);
  const grid=$('#ventasGrid'), noResults=$('#noResults'), toast=$('#resultToast'), topBar=$('#topBar');
  const segmented=$('#segmented'); const indicator=segmented?.querySelector('.indicator');

  const selVencidosVis = document.getElementById('selVencidosVis');
  const vencidosList   = document.getElementById('vencidosList');
  const vencidosRes    = document.getElementById('vencidosResumen');

  let estadoMode='all', tipoMode='all', debounceTimer;

  const norm = s => (s||'').toString().toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,'');

  function parseNumericQuery(q){
    if(!q) return null;
    const s=q.replace(/\s+/g,'').replace(',','.');
    if(/^(\d+(\.\d+)?)\-(\d+(\.\d+)?)$/.test(s)){
      const [a,b]=s.split('-').map(parseFloat);
      return {type:'range',min:Math.min(a,b),max:Math.max(a,b)};
    }
    const m=s.match(/^([<>]=?|=)(\d+(\.\d+)?)$/);
    if(m){ return {type:m[1],value:parseFloat(m[2])}; }
    if(/^\d+(\.\d+)?$/.test(s)){ return {type:'eq-soft',value:parseFloat(s)}; }
    return null;
  }

  function matchNumeric(val,nq){
    if(nq==null) return true;
    const v=Number(val)||0;
    switch(nq.type){
      case 'range':return v>=nq.min&&v<=nq.max;
      case '>':return v>nq.value;
      case '>=':return v>=nq.value;
      case '<':return v<nq.value;
      case '<=':return v<=nq.value;
      case '=':return v===nq.value;
      case 'eq-soft':return (''+v).includes((''+nq.value));
      default:return true;
    }
  }

  function getCampo(){
    const d=$('#selCampoDesktop');
    if (window.matchMedia('(min-width:768px)').matches && d) return d.value||'restante';
    const r=document.querySelector('input[name="campoMob"]:checked');
    return r ? r.value : 'restante';
  }

  function showToast(msg){
    if(!toast) return;
    toast.textContent=msg;
    toast.style.display='block';
    clearTimeout(toast._tmr);
    toast._tmr=setTimeout(()=>toast.style.display='none',1400);
  }

  function setFiltering(on){
    document.body.classList.toggle('is-filtering',on);
    if(topBar){ topBar.style.width=on?'70%':'0%'; }
  }

  function clearHighlights(card){
    const n=card.querySelector('.js-name'), i=card.querySelector('.js-id');
    if(n) n.innerHTML=n.textContent;
    if(i) i.innerHTML=i.textContent;
  }

  function applyHighlights(card,q){
    if(!q) return;
    const qn=norm(q);
    const n=card.querySelector('.js-name'), i=card.querySelector('.js-id');

    function paint(el){
      const t=el.textContent;
      const idx=norm(t).indexOf(qn);
      if(idx>=0){
        el.innerHTML=t.slice(0,idx)+'<mark class="hl">'+t.slice(idx,idx+q.length)+'</mark>'+t.slice(idx+q.length);
      }
    }

    if(n) paint(n);
    if(i) paint(i);
  }

  function updateIndicator(){
    if(!segmented || !indicator) return;
    const active=segmented.querySelector('button.active') || segmented.querySelector('button');
    if(!active) return;
    const {offsetLeft,offsetWidth}=active;
    indicator.style.width = (offsetWidth - 8) + 'px';
    indicator.style.transform = `translateX(${offsetLeft - 4}px)`;
  }

  window.addEventListener('resize', updateIndicator);

  function applyAllFilters(){
    if(!grid) return;
    setFiltering(true);

    const items = $$('.venta-item', grid);
    const qDesktop = $('#txtSearchDesktop')?.value || '';
    const qMobile  = $('#txtSearchMobile')?.value || '';
    const q = (window.matchMedia('(min-width:768px)').matches ? qDesktop : qMobile) || '';
    const campo = getCampo();
    const nq = parseNumericQuery(q);
    let visible=0;

    items.forEach(it=>{
      clearHighlights(it);

      const st=it.dataset.estado;
      const tp=it.dataset.tipo || 'venta';
      const cid=(it.dataset.id||'').toString();
      const cli=it.dataset.cliente||'';
      const total=it.dataset.total;
      const pagado=it.dataset.pagado;
      const restante=it.dataset.restante;

      const passEstado = estadoMode === 'all' ||estadoMode.split('|').includes(st);
      const passTipo=(tipoMode==='all')||(tipoMode===tp);
      const passTexto = !q ? true : ( cli.includes(norm(q)) || cid.includes(q) || (tp === 'os' && norm('orden de servicio os servicio').includes(norm(q))) || (tp === 'venta' && norm('venta remision ventas').includes(norm(q))) );
      let passNumero=true;

      if(nq){
        const val = campo==='total'?total:(campo==='pagado'?pagado:restante);
        passNumero=matchNumeric(val,nq);
      } else if(q && /\d/.test(q)){
        const direct=[total,pagado,restante].some(v=>(''+Number(v||0)).includes(q.replace(/\s+/g,'')));
        passNumero=direct||passTexto;
      }

      const show=passEstado && passTipo && passTexto && passNumero;
      it.classList.toggle('hidden',!show);
      if(show){ visible++; applyHighlights(it,q); }
    });

    if(noResults) noResults.classList.toggle('d-none', visible>0);
    showToast(visible + ' resultado' + (visible===1?'':'s'));

    if(topBar){
      topBar.style.width='100%';
      setTimeout(()=> setFiltering(false),220);
    }
  }

  function debouncedFilter(){
    clearTimeout(debounceTimer);
    setFiltering(true);
    if(topBar) topBar.style.width='45%';
    debounceTimer=setTimeout(applyAllFilters,200);
  }

  $$('.seg-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      $$('.seg-btn').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      estadoMode=btn.dataset.filter;
      updateIndicator();
      debouncedFilter();
    });
  });

  $$('.type-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      $$('.type-btn').forEach(b=>{
        b.classList.remove('active');
        b.setAttribute('aria-pressed','false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-pressed','true');
      tipoMode=btn.dataset.type || 'all';
      debouncedFilter();
    });
  });

  updateIndicator();

  $('#txtSearchDesktop')?.addEventListener('input', debouncedFilter);
  $('#btnClearSearchDesktop')?.addEventListener('click', ()=>{
    const t=$('#txtSearchDesktop');
    if(!t) return;
    t.value='';
    debouncedFilter();
  });

  $('#selCampoDesktop')?.addEventListener('change', debouncedFilter);

  const sheet=$('#filterSheet'), overlay=$('#sheetOverlay');
  const openBtn=$('#openFilterSheet'); const txtMob=$('#txtSearchMobile');

  function openSheet(){ sheet?.classList.add('open'); overlay?.classList.add('open'); }
  function closeSheet(){ sheet?.classList.remove('open'); overlay?.classList.remove('open'); }

  openBtn?.addEventListener('click', openSheet);
  overlay?.addEventListener('click', closeSheet);

  $$('#estadoMob .pill').forEach(p=>{
    p.addEventListener('click', ()=>{
      $$('#estadoMob .pill').forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
      p.querySelector('input').checked=true;
    });

  $$('#tipoMob .pill').forEach(p=>{
    p.addEventListener('click', ()=>{
      $$('#tipoMob .pill').forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
      p.querySelector('input').checked=true;
    });
  });
  });

  $('#btnClear')?.addEventListener('click', ()=>{
    const all=$('#estadoMob .pill input[value="all"]')?.closest('.pill');
    $$('#estadoMob .pill').forEach(x=>x.classList.remove('active'));
    if (all){
      all.classList.add('active');
      all.querySelector('input').checked=true;
    }

    const allTipo=$('#tipoMob .pill input[value="all"]')?.closest('.pill');
    $$('#tipoMob .pill').forEach(x=>x.classList.remove('active'));
    if (allTipo){
      allTipo.classList.add('active');
      allTipo.querySelector('input').checked=true;
    }
    tipoMode='all';

    const rest=document.querySelector('input[name="campoMob"][value="restante"]');
    if(rest){ rest.checked=true; }

    if(txtMob) txtMob.value='';

    document.querySelectorAll('.pill-group input[name="campoMob"]').forEach(inp=>{
      const lab = inp.closest('.pill');
      if (lab) lab.classList.toggle('active', inp.value==='restante');
    });
  });

  $('#btnApply')?.addEventListener('click', ()=>{
    const estadoSel = document.querySelector('input[name="estadoMob"]:checked')?.value || 'all';
    estadoMode = estadoSel;

    const tipoSel = document.querySelector('input[name="tipoMob"]:checked')?.value || 'all';
    tipoMode = tipoSel;

    const typeBtn=document.querySelector(`.type-btn[data-type="${tipoMode}"]`);
    if(typeBtn){
      $$('.type-btn').forEach(b=>{
        b.classList.remove('active');
        b.setAttribute('aria-pressed','false');
      });
      typeBtn.classList.add('active');
      typeBtn.setAttribute('aria-pressed','true');
    }

    const segBtn=document.querySelector(`.seg-btn[data-filter="${estadoMode}"]`);
    if(segBtn){
      $$('.seg-btn').forEach(b=>b.classList.remove('active'));
      segBtn.classList.add('active');
      updateIndicator();
    }

    const checkedMob=document.querySelector('input[name="campoMob"]:checked')?.value||'restante';
    const selDesk=$('#selCampoDesktop'); if(selDesk) selDesk.value=checkedMob;

    const txtDesk=$('#txtSearchDesktop'); if(txtDesk && txtMob) txtDesk.value=txtMob.value;

    debouncedFilter();
    closeSheet();
  });

  const io=new IntersectionObserver((entries)=>{
    entries.forEach(e=>{
      if(e.isIntersecting){
        e.target.classList.add('revealed');
        io.unobserve(e.target);
      }
    });
  },{rootMargin:'0px 0px -10% 0px',threshold:.15});

  $$('.card-venta').forEach(c=>io.observe(c));

  function sendNotify(btn){
    const pagoId = btn.dataset.pagoId;
    const csrf   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if(!pagoId || !csrf) return;

    btn.disabled = true;
    btn.classList.add('sending');
    btn.querySelector('.label').textContent = 'Enviando...';

    fetch(`/financiamientos/notificar/${pagoId}`,{
      method:'POST',
      headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}
    })
    .then(r=>{
      if(r.ok){
        btn.classList.remove('sending');
        btn.classList.add('sent');
        btn.querySelector('.label').textContent = 'Enviado';
        setTimeout(()=>{
          btn.replaceWith(Object.assign(document.createElement('span'), {
            className:'badge rounded-pill bg-secondary',
            textContent:'Notificado hoy'
          }));
        }, 450);
      }else{
        throw new Error('Solicitud fallida');
      }
    })
    .catch(()=>{
      btn.classList.remove('sending');
      btn.disabled = false;
      btn.querySelector('.label').textContent = 'Error, reintentar';
    });
  }

  document.querySelectorAll('.reenviar-btn').forEach(button=>{
    button.addEventListener('click', function(){ sendNotify(this); });
  });

  debouncedFilter();

  (function initVencidosSelect(){
    if (!selVencidosVis || !vencidosList) return;
    const KEY = 'fin_vencidos_vis';
    const saved = localStorage.getItem(KEY) || 'resumen';
    selVencidosVis.value = saved;

    function applyView(v){
      const isLista = (v === 'lista');
      if (vencidosList) vencidosList.style.display = isLista ? '' : 'none';
      if (vencidosRes)  vencidosRes.style.display  = isLista ? 'none' : '';
      localStorage.setItem(KEY, v);
    }

    selVencidosVis.addEventListener('change', e => applyView(e.target.value));
    applyView(saved);
  })();
})();

</script>
@endsection