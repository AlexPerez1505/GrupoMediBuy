<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use App\Models\Producto;
use App\Services\WooCommerceProductExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('tipo_equipo', 'asc')->get();
        return view('cotizaciones', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_equipo'    => 'required|string|max:255',
            'subtipo_equipo' => 'required|string|max:255',
            'marca'          => 'required|string|max:255',
            'modelo'         => 'required|string|max:255',
            'precio'         => 'required|numeric|min:0',
            'imagen'         => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
        ]);

        $stock = $request->input('stock', 1);

        $imagenPath = $request->hasFile('imagen')
            ? $request->file('imagen')->store('productos', 'public')
            : null;

        $producto = Producto::create([
            'tipo_equipo'    => $validated['tipo_equipo'],
            'subtipo_equipo' => $validated['subtipo_equipo'],
            'marca'          => $validated['marca'],
            'modelo'         => $validated['modelo'],
            'stock'          => $stock,
            'precio'         => $validated['precio'],
            'imagen'         => $imagenPath,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message'  => 'Producto creado exitosamente',
                'producto' => $producto,
            ]);
        }

        return redirect()->route('productos.cards')->with('success', 'Producto creado exitosamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $productos = Producto::where('tipo_equipo', 'like', "%{$search}%")
            ->orWhere('subtipo_equipo', 'like', "%{$search}%")
            ->orWhere('modelo', 'like', "%{$search}%")
            ->orWhere('marca', 'like', "%{$search}%")
            ->orderBy('tipo_equipo', 'asc')
            ->get()
            ->map(function ($producto) {
                return [
                    'id'             => $producto->id,
                    'tipo_equipo'    => strtoupper((string) $producto->tipo_equipo),
                    'subtipo_equipo' => strtoupper((string) $producto->subtipo_equipo),
                    'modelo'         => strtoupper((string) $producto->modelo),
                    'marca'          => strtoupper((string) $producto->marca),
                    'precio'         => $producto->precio,
                    'imagen'         => $producto->imagen,
                    'stock'          => $producto->stock,
                ];
            });

        return response()->json($productos);
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        $productos = Producto::where('tipo_equipo', 'like', '%' . $request->termino . '%')
            ->orWhere('subtipo_equipo', 'like', '%' . $request->termino . '%')
            ->orWhere('modelo', 'like', '%' . $request->termino . '%')
            ->orWhere('marca', 'like', '%' . $request->termino . '%')
            ->get();

        return response()->json($productos);
    }

    public function cardsVista()
    {
        $productos = Producto::orderBy('tipo_equipo')->get();

        $paquetes = Paquete::withCount('productos')
            ->with(['productos' => fn ($q) => $q->select('productos.*')])
            ->latest()
            ->get();

        return view('productos-cards', compact('productos', 'paquetes'));
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'tipo_equipo'    => 'required|string|max:255',
            'subtipo_equipo' => 'required|string|max:255',
            'marca'          => 'required|string|max:255',
            'modelo'         => 'required|string|max:255',
            'precio'         => 'required|numeric|min:0',
            'imagen'         => 'nullable|image|max:4096',
        ]);

        $producto->fill([
            'tipo_equipo'    => $validated['tipo_equipo'],
            'subtipo_equipo' => $validated['subtipo_equipo'],
            'marca'          => $validated['marca'],
            'modelo'         => $validated['modelo'],
            'precio'         => $validated['precio'],
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();

        return redirect()->route('productos.cards')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.cards')->with('success', 'Producto eliminado correctamente');
    }

    // ── Helpers de filtros ─────────────────────────────────────────────────────

    private function extractFilterList(Request $request, string $key): array
    {
        $raw = $request->input($key, $request->query($key, []));

        if (is_string($raw)) {
            $raw = trim($raw);
            return $raw === '' ? [] : [$raw];
        }

        if (!is_array($raw)) {
            return [];
        }

        return array_values(array_filter(
            array_map(fn ($v) => trim((string) $v), $raw),
            fn ($v) => $v !== ''
        ));
    }

    private function extractProductFilters(Request $request): array
    {
        $q     = trim((string) $request->input('q', $request->query('q', '')));
        $scope = trim((string) $request->input('scope', $request->query('scope', 'all')));
        
        $tipos    = $this->extractFilterList($request, 'tipo');
        $subtipos = $this->extractFilterList($request, 'subtipo');
        $marcas   = $this->extractFilterList($request, 'marca');
        $modelos  = $this->extractFilterList($request, 'modelo');

        $stockRaw = trim((string) $request->input('stock', $request->query('stock', 'all')));
        $stock = in_array($stockRaw, ['all', 'with_stock', 'without_stock', 'in', 'out'], true) ? $stockRaw : 'all';

        if ($stock === 'in')  $stock = 'with_stock';
        if ($stock === 'out') $stock = 'without_stock';

        $ai = $request->input('ai', $request->query('ai', 0));
        $ai = (int) (filter_var($ai, FILTER_VALIDATE_BOOLEAN) ? 1 : 0);

        return [
            'q'        => $q,
            'tipos'    => $tipos,
            'subtipos' => $subtipos,
            'marcas'   => $marcas,
            'modelos'  => $modelos,
            'tipo'     => $tipos[0] ?? '',
            'subtipo'  => $subtipos[0] ?? '',
            'marca'    => $marcas[0] ?? '',
            'modelo'   => $modelos[0] ?? '',
            'stock'    => $stock,
            'scope'    => $scope,
            'ai'       => $ai,
        ];
    }

    private function applySearchToProductos($query, string $q)
    {
        $q = trim($q);
        if ($q === '') return $query;

        return $query->where(function ($w) use ($q) {
            $w->where('tipo_equipo', 'like', "%{$q}%")
                ->orWhere('subtipo_equipo', 'like', "%{$q}%")
                ->orWhere('modelo', 'like', "%{$q}%")
                ->orWhere('marca', 'like', "%{$q}%")
                ->orWhere('precio', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%");
        });
    }

private function applyFilterProductos($query, array $filters)
    {
        $query = $this->applySearchToProductos($query, $filters['q'] ?? '');

        // Filtrar Categorías (tipo_equipo) soportando "SIN CATEGORÍA"
        $tipos = $filters['tipos'] ?? [];
        if (!empty($tipos)) {
            $tiposUpper = array_map('strtoupper', array_map('trim', $tipos));
            
            $query->where(function($q) use ($tiposUpper) {
                // Si seleccionaron "SIN CATEGORÍA", buscamos registros nulos o vacíos
                if (in_array('SIN CATEGORÍA', $tiposUpper) || in_array('SIN CATEGORIA', $tiposUpper)) {
                    $q->whereNull('tipo_equipo')->orWhere('tipo_equipo', '');
                    
                    // Si además de "SIN CATEGORÍA" hay otras categorías normales seleccionadas
                    $otrosTipos = array_filter($tiposUpper, fn($t) => $t !== 'SIN CATEGORÍA' && $t !== 'SIN CATEGORIA');
                    if (!empty($otrosTipos)) {
                        $q->orWhereIn('tipo_equipo', $otrosTipos);
                    }
                } else {
                    $q->whereIn('tipo_equipo', $tiposUpper);
                }
            });
        }

        // Filtrar Subtipos (subtipo_equipo)
        $subtipos = $filters['subtipos'] ?? [];
        if (!empty($subtipos)) {
            $subtiposUpper = array_map('strtoupper', array_map('trim', $subtipos));
            $query->whereIn('subtipo_equipo', $subtiposUpper);
        }

        // Filtrar Marcas
        $marcas = $filters['marcas'] ?? [];
        if (!empty($marcas)) {
            $marcasUpper = array_map('strtoupper', array_map('trim', $marcas));
            $query->whereIn('marca', $marcasUpper);
        }

        // Filtrar Modelos
        $modelos = $filters['modelos'] ?? [];
        if (!empty($modelos)) {
            $modelosUpper = array_map('strtoupper', array_map('trim', $modelos));
            $query->whereIn('modelo', $modelosUpper);
        }

        // Filtrar Stock
        $stock = $filters['stock'] ?? 'all';
        if ($stock === 'with_stock') {
            $query->where('stock', '>', 0);
        } elseif ($stock === 'without_stock') {
            $query->where(function ($w) {
                $w->whereNull('stock')->orWhere('stock', '<=', 0);
            });
        }

        return $query;
    }

    private function applyFilterPaquetes($query, array $filters)
    {
        $q = trim($filters['q'] ?? '');
        if ($q !== '') {
            $query->where(function ($w) {
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhereHas('productos', function ($pq) use ($q) {
                      $pq->where('tipo_equipo', 'like', "%{$q}%")
                        ->orWhere('subtipo_equipo', 'like', "%{$q}%")
                        ->orWhere('modelo', 'like', "%{$q}%")
                        ->orWhere('marca', 'like', "%{$q}%");
                  });
            });
        }

        // Relación masiva en Paquetes soportando "SIN CATEGORÍA"
        if (!empty($filters['tipos'])) {
            $tiposUpper = array_map('strtoupper', array_map('trim', $filters['tipos']));
            
            $query->whereHas('productos', function ($pq) use ($tiposUpper) {
                $pq->where(function($q) use ($tiposUpper) {
                    if (in_array('SIN CATEGORÍA', $tiposUpper) || in_array('SIN CATEGORIA', $tiposUpper)) {
                        $q->whereNull('tipo_equipo')->orWhere('tipo_equipo', '');
                        
                        $otrosTipos = array_filter($tiposUpper, fn($t) => $t !== 'SIN CATEGORÍA' && $t !== 'SIN CATEGORIA');
                        if (!empty($otrosTipos)) {
                            $q->orWhereIn('tipo_equipo', $otrosTipos);
                        }
                    } else {
                        $q->whereIn('tipo_equipo', $tiposUpper);
                    }
                });
            });
        }

        if (!empty($filters['subtipos'])) {
            $subtiposUpper = array_map('strtoupper', array_map('trim', $filters['subtipos']));
            $query->whereHas('productos', function ($pq) use ($subtiposUpper) {
                $pq->whereIn('subtipo_equipo', $subtiposUpper);
            });
        }

        if (!empty($filters['marcas'])) {
            $marcasUpper = array_map('strtoupper', array_map('trim', $filters['marcas']));
            $query->whereHas('productos', function ($pq) use ($marcasUpper) {
                $pq->whereIn('marca', $marcasUpper);
            });
        }

        return $query;
    }
    // ── Exportar PDF ───────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $filters = $this->extractProductFilters($request);
        $scope   = $filters['scope'] ?: 'all';

        $productos = collect();
        $paquetes  = collect();

        if ($scope === 'all' || $scope === 'productos') {
            $qp = Producto::orderBy('tipo_equipo');
            $qp = $this->applyFilterProductos($qp, $filters);
            $productos = $qp->get();
        }

        if ($scope === 'all' || $scope === 'paquetes') {
            $qk = Paquete::withCount('productos')
                ->with(['productos' => fn ($qq) => $qq->select('productos.*')])
                ->latest();
            $qk = $this->applyFilterPaquetes($qk, $filters);
            $paquetes = $qk->get();
        }

        $pdf = Pdf::loadView('exports.catalogo_pdf', [
            'productos'   => $productos,
            'paquetes'    => $paquetes,
            'q'           => $filters['q'],
            'scope'       => $scope,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $name = 'catalogo_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($name);
    }

    // ── Exportar Excel ─────────────────────────────────────────────────────────
    public function exportXlsx(Request $request)
    {
        $filters = $this->extractProductFilters($request);
        $scope   = $filters['scope'] ?: 'all';

        // Leer qué columnas incluir (todas activas por defecto)
        $cols = [
            'categoria'  => (int) $request->input('col_categoria',  1) === 1,
            'nombre'     => (int) $request->input('col_nombre',      1) === 1,
            'marca'      => (int) $request->input('col_marca',       1) === 1,
            'modelo'     => (int) $request->input('col_modelo',      1) === 1,
            'beneficios' => (int) $request->input('col_beneficios',  1) === 1,
            'precio'     => (int) $request->input('col_precio',      0) === 1,
            'stock_col'  => (int) $request->input('col_stock_col',   0) === 1,
        ];

        // Construir mapa de columna activa → [letra Excel, header, closure de valor]
        $colDefs = [];
        if ($cols['categoria'])  $colDefs[] = ['header' => 'CATEGORÍA',       'value' => fn($p) => strtoupper((string) ($p->tipo_equipo    ?? ''))];
        if ($cols['nombre'])     $colDefs[] = ['header' => 'NOMBRE DEL EQUIPO','value' => fn($p) => strtoupper((string) ($p->subtipo_equipo ?? ''))];
        if ($cols['marca'])      $colDefs[] = ['header' => 'MARCA',            'value' => fn($p) => strtoupper((string) ($p->marca          ?? ''))];
        if ($cols['modelo'])     $colDefs[] = ['header' => 'MODELO',           'value' => fn($p) => strtoupper((string) ($p->modelo         ?? ''))];
        if ($cols['beneficios']) $colDefs[] = ['header' => 'BENEFICIOS',       'value' => fn($p) => ''];
        if ($cols['precio'])     $colDefs[] = ['header' => 'PRECIO',           'value' => fn($p) => (float) ($p->precio ?? 0)];
        if ($cols['stock_col'])  $colDefs[] = ['header' => 'STOCK',            'value' => fn($p) => (int)   ($p->stock  ?? 0)];

        // Si no se seleccionó ninguna, usar todas por defecto
        if (empty($colDefs)) {
            $colDefs = [
                ['header' => 'CATEGORÍA',        'value' => fn($p) => strtoupper((string) ($p->tipo_equipo    ?? ''))],
                ['header' => 'NOMBRE DEL EQUIPO','value' => fn($p) => strtoupper((string) ($p->subtipo_equipo ?? ''))],
                ['header' => 'MARCA',            'value' => fn($p) => strtoupper((string) ($p->marca          ?? ''))],
                ['header' => 'MODELO',           'value' => fn($p) => strtoupper((string) ($p->modelo         ?? ''))],
                ['header' => 'BENEFICIOS',       'value' => fn($p) => ''],
            ];
        }

        $numCols   = count($colDefs);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numCols);

        $productos = collect();
        $paquetes  = collect();

        if ($scope === 'all' || $scope === 'productos') {
            $qp = Producto::orderBy('tipo_equipo');
            $qp = $this->applyFilterProductos($qp, $filters);
            $productos = $qp->get();
        }

        if ($scope === 'all' || $scope === 'paquetes') {
            $qk = Paquete::withCount('productos')
                ->with(['productos' => fn ($qq) => $qq->select('productos.*')])
                ->latest();
            $qk = $this->applyFilterPaquetes($qk, $filters);
            $paquetes = $qk->get();
        }

        $spreadsheet = new Spreadsheet();

        // ── Hoja: Productos ───────────────────────────────────────────────────
        $sheetP = $spreadsheet->getActiveSheet();
        $sheetP->setTitle('Productos');

        // Título
        $sheetP->setCellValue('A1', 'CATÁLOGO DE PRODUCTOS');
        $sheetP->mergeCells('A1:' . $lastColLetter . '1');
        $sheetP->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheetP->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheetP->getRowDimension(1)->setRowHeight(24);

        // Encabezados dinámicos
        $headers = array_column($colDefs, 'header');
        $sheetP->fromArray($headers, null, 'A2');
        $headerRange = 'A2:' . $lastColLetter . '2';
        $sheetP->getStyle($headerRange)->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheetP->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1565C0');
        $sheetP->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetP->getRowDimension(2)->setRowHeight(20);

        // Filas de datos
        $rowNum = 3;
        foreach ($productos as $p) {
            foreach ($colDefs as $idx => $def) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
                $sheetP->setCellValue($colLetter . $rowNum, ($def['value'])($p));
            }

            if ($rowNum % 2 === 0) {
                $sheetP->getStyle('A' . $rowNum . ':' . $lastColLetter . $rowNum)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE3F2FD');
            }

            $rowNum++;
        }

        // Anchos de columna automáticos
        foreach (range(1, $numCols) as $i) {
            $sheetP->getColumnDimension(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i)
            )->setAutoSize(true);
        }

        // ── Hoja: Paquetes (opcional) ─────────────────────────────────────────
        if (($scope === 'all' || $scope === 'paquetes') && $paquetes->isNotEmpty()) {
            $sheetK = $spreadsheet->createSheet();
            $sheetK->setTitle('Paquetes');

            $sheetK->setCellValue('A1', 'PAQUETES');
            $sheetK->mergeCells('A1:E1');
            $sheetK->getStyle('A1')->getFont()->setBold(true)->setSize(13);
            $sheetK->getStyle('A1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheetK->fromArray(['NOMBRE', 'CATEGORÍAS', 'MARCAS', 'MODELOS', 'TOTAL'], null, 'A2');
            $sheetK->getStyle('A2:E2')->getFont()->setBold(true)
                ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            $sheetK->getStyle('A2:E2')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1565C0');

            $rowNum = 3;
            foreach ($paquetes as $pkg) {
                $pkgTotal = isset($pkg->productos)
                    ? $pkg->productos->sum(fn ($pp) => (float)($pp->precio ?? 0) * max(1, (int)($pp->pivot->cantidad ?? 1)))
                    : 0;

                $cats   = isset($pkg->productos) ? collect($pkg->productos)->pluck('tipo_equipo')->filter()->unique()->join(', ') : '';
                $marcas = isset($pkg->productos) ? collect($pkg->productos)->pluck('marca')->filter()->unique()->join(', ') : '';
                $mods   = isset($pkg->productos) ? collect($pkg->productos)->pluck('modelo')->filter()->unique()->join(', ') : '';

                $sheetK->setCellValue('A' . $rowNum, (string) ($pkg->nombre ?? 'Paquete'));
                $sheetK->setCellValue('B' . $rowNum, strtoupper($cats));
                $sheetK->setCellValue('C' . $rowNum, strtoupper($marcas));
                $sheetK->setCellValue('D' . $rowNum, strtoupper($mods));
                $sheetK->setCellValue('E' . $rowNum, (float) $pkgTotal);

                $rowNum++;
            }

            foreach (range('A', 'E') as $col) {
                $sheetK->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        
        $fileName = 'catalogo_' . now()->format('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        if (ob_get_length()) ob_end_clean();

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'public',
        ]);
    }

    // ── Exportar WooCommerce ───────────────────────────────────────────────────

    public function exportWooCommerceXlsx(Request $request, WooCommerceProductExportService $wooCommerceProductExportService)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');
        @ini_set('memory_limit', '1024M');

        $filters = $this->extractProductFilters($request);

        $productos = Producto::orderBy('tipo_equipo');
        $productos = $this->applyFilterProductos($productos, $filters);
        $productos = $productos->get();

        if ($productos->isEmpty()) {
            return back()->with('error', 'No hay productos para exportar con esos filtros.');
        }

        $fileName = 'woocommerce_productos_' . now()->format('Y-m-d_His') . '.xlsx';
        $useAi    = (int) ($filters['ai'] ?? 0) === 1;
        $baseUrl  = $request->getSchemeAndHttpHost();

        return $wooCommerceProductExportService->download($productos, $fileName, $baseUrl, $useAi);
    }

    // ── API ────────────────────────────────────────────────────────────────────

    public function apiIndex()
    {
        $productos = Producto::select('id', 'tipo_equipo', 'subtipo_equipo', 'modelo', 'marca', 'stock', 'precio', 'imagen')
            ->orderBy('tipo_equipo', 'asc')
            ->get();

        $base = request()->getSchemeAndHttpHost();

        $productos->transform(function ($producto) use ($base) {
            $producto->imagen = $producto->imagen
                ? $base . '/storage/' . ltrim($producto->imagen, '/')
                : null;
            return $producto;
        });

        return response()->json($productos, 200);
    }

    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipo_equipo'    => 'required|string|max:255',
                'subtipo_equipo' => 'required|string|max:255',
                'modelo'         => 'required|string|max:255',
                'marca'          => 'required|string|max:255',
                'stock'          => 'required|integer|min:0',
                'precio'         => 'required|numeric|min:0',
                'imagen'         => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            ]);

            $imagenPath = $request->hasFile('imagen')
                ? $request->file('imagen')->store('productos', 'public')
                : null;

            $producto = Producto::create([
                'tipo_equipo'    => $validated['tipo_equipo'],
                'subtipo_equipo' => $validated['subtipo_equipo'],
                'modelo'         => $validated['modelo'],
                'marca'          => $validated['marca'],
                'stock'          => $validated['stock'],
                'precio'         => $validated['precio'],
                'imagen'         => $imagenPath,
            ]);

            return response()->json(['message' => 'Producto creado correctamente', 'producto' => $producto], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear producto', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiShow($id)
    {
        try {
            $producto = Producto::select('id', 'tipo_equipo', 'subtipo_equipo', 'modelo', 'marca', 'stock', 'precio', 'imagen')->find($id);

            if (!$producto) {
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            $base = request()->getSchemeAndHttpHost();
            $producto->imagen = (!empty($producto->imagen) && is_string($producto->imagen))
                ? $base . '/storage/' . ltrim($producto->imagen, '/')
                : null;

            return response()->json($producto, 200);
        } catch (\Throwable $e) {
            \Log::error('Error en apiShow producto: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Error interno al obtener producto', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $validated = $request->validate([
                'tipo_equipo'    => 'required|string|max:255',
                'subtipo_equipo' => 'required|string|max:255',
                'modelo'         => 'required|string|max:255',
                'marca'          => 'required|string|max:255',
                'stock'          => 'required|integer|min:0',
                'precio'         => 'required|numeric|min:0',
                'imagen'         => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            ]);

            $producto->tipo_equipo    = $validated['tipo_equipo'];
            $producto->subtipo_equipo = $validated['subtipo_equipo'];
            $producto->modelo         = $validated['modelo'];
            $producto->marca          = $validated['marca'];
            $producto->stock          = $validated['stock'];
            $producto->precio         = $validated['precio'];

            if ($request->hasFile('imagen')) {
                if ($producto->imagen && \Storage::disk('public')->exists($producto->imagen)) {
                    \Storage::disk('public')->delete($producto->imagen);
                }
                $producto->imagen = $request->file('imagen')->store('productos', 'public');
            }

            $producto->save();

            $base = $request->getSchemeAndHttpHost();
            $producto->imagen = $producto->imagen ? $base . '/storage/' . ltrim($producto->imagen, '/') : null;

            return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto], 200);
        } catch (\Throwable $e) {
            \Log::error('Error en apiUpdate producto: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Error al actualizar producto', 'error' => $e->getMessage()], 500);
        }
    }
}