<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use App\Models\Material;
use App\Models\WorkOrderLog;
use App\Models\Purchase;
use App\Models\CxIssue;
use App\Enums\WorkOrderStatus;
use App\Services\Storage\StorageService;
use App\Services\WarehouseDashboardApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseDashboardController extends Controller
{
    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        // 1. Hero Stats (Always base total, unaffected by search)
        $stats = [
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'needs_processing' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)->count(),
            'stored_items' => StorageAssignment::stored()->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->count(),
        ];

        // 2. Storage Statistics & Overdue
        $storageStats = $this->storageService->getStatistics();
        $overdueItems = $this->storageService->getOverdueItems(7);

        // 3. Rack Utilization
        $rackStats = $this->storageService->getRackUtilization();
        $racksByCategory = StorageRack::active()
            ->orderBy('rack_code')
            ->get()
            ->groupBy('category');

        // 4. Low Stock Materials
        $lowStockMaterials = Material::whereRaw('stock < min_stock')
            ->orderByRaw('(stock / min_stock) ASC')
            ->take(5)
            ->get();

        // 5. Recent Warehouse Activity
        $recentLogs = WorkOrderLog::whereIn('step', ['RECEPTION', 'STORAGE'])
            ->with(['user', 'workOrder'])
            ->latest()
            ->take(10)
            ->get();

        // 6. Action Queues (Filtered by Search)
        $queues = [
            'reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
            'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
            'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->where(function($q) {
                    $q->whereNotNull('warehouse_qc_status')
                      ->orWhere('status', WorkOrderStatus::SELESAI);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
            'pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->with(['storageAssignments' => fn($q) => $q->stored()])
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
        ];

        return view('warehouse.dashboard.index', compact(
            'stats',
            'storageStats',
            'overdueItems',
            'rackStats',
            'racksByCategory',
            'lowStockMaterials',
            'recentLogs',
            'queues',
            'search'
        ))->with([
            'inventoryValue' => $this->getInventoryValue(),
            'supplierAnalytics' => $this->getSupplierAnalytics(),
            'qcRejectTrends' => $this->getQCRejectTrends(),
            'qcRejectReasons' => $this->getQCRejectReasons(),
            'materialTrends' => $this->getMaterialTrends(),
        ]);
    }

    /**
     * API endpoint for realtime polling (JSON).
     */
    public function apiStats()
    {
        $threeMonthsAgo = now()->subMonths(3);

        $stats = [
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'needs_processing' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)->count(),
            'stored_items' => StorageAssignment::stored()->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->count(),
            'shoes_in_rack_count' => StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
                ->stored()
                ->count(),
            'donation_candidates_count' => StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
                ->stored()
                ->where('stored_at', '<=', $threeMonthsAgo)
                ->count(),
        ];

        $rackStats = $this->storageService->getRackUtilization();

        $lowStockMaterials = Material::whereRaw('stock < min_stock')
            ->orderByRaw('(stock / min_stock) ASC')
            ->take(5)
            ->get()
            ->map(fn($m) => [
                'name' => $m->name,
                'stock' => $m->stock,
                'min_stock' => $m->min_stock,
                'unit' => $m->unit,
            ]);

        $donationCandidates = StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
            ->stored()
            ->where('stored_at', '<=', $threeMonthsAgo)
            ->with(['workOrder'])
            ->get()
            ->map(function($assignment) {
                $wo = $assignment->workOrder;
                $storedAt = $assignment->stored_at;
                return [
                    'id' => $wo?->id,
                    'spk_number' => $wo?->spk_number ?? 'N/A',
                    'customer_name' => $wo?->customer_name ?? 'N/A',
                    'shoe_brand' => $wo?->shoe_brand,
                    'shoe_type' => $wo?->shoe_type,
                    'shoe_color' => $wo?->shoe_color,
                    'wo_status' => $wo?->status?->value ?? ($wo?->status ?? '-'),
                    'rack_code' => $assignment->rack_code,
                    'stored_at' => $storedAt ? $storedAt->toDateTimeString() : null,
                    'days_stored' => $days = $storedAt ? (int) abs(round(now()->diffInDays($storedAt))) : 0,
                    'days_stored_formatted' => $days === 0 ? 'Hari Ini' : $days . ' Hari',
                ];
            });

        return response()->json([
            'stats' => $stats,
            'rack_stats' => [
                'utilization_percentage' => $rackStats['utilization_percentage'],
                'total_available' => $rackStats['total_available'],
                'full_racks' => $rackStats['full_racks'],
            ],
            'low_stock_materials' => $lowStockMaterials,
            'donation_candidates' => $donationCandidates,
            'queue_counts' => [
                'reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
                'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)->count(),
                'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                    ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                    ->where(function($q) {
                        $q->whereNotNull('warehouse_qc_status')
                          ->orWhere('status', WorkOrderStatus::SELESAI);
                    })
                    ->count(),
                'pickup' => $stats['ready_for_pickup'],
            ],
            'timestamp' => now()->format('H:i:s'),
            'inventory_value' => $this->getInventoryValue()['total'],
            'focus_rack_type' => 'shoes',
            'qc_reject_count' => CxIssue::where('source', 'GUDANG')->count(),
        ]);
    }

    private function getInventoryValue()
    {
        $materials = Material::all();
        $totalValue = $materials->sum(fn($m) => $m->stock * $m->price);
        
        return [
            'total' => $totalValue,
            'by_category' => $materials->groupBy('category')->map(fn($group) => $group->sum(fn($m) => $m->stock * $m->price)),
        ];
    }

    private function getSupplierAnalytics()
    {
        $topBySpend = Purchase::whereNotNull('supplier_name')
            ->select('supplier_name', DB::raw('SUM(total_price) as total_spend'))
            ->groupBy('supplier_name')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        $topByRating = Purchase::whereNotNull('supplier_name')
            ->whereNotNull('quality_rating')
            ->select('supplier_name', DB::raw('AVG(quality_rating) as avg_rating'))
            ->groupBy('supplier_name')
            ->orderByDesc('avg_rating')
            ->take(5)
            ->get();

        return [
            'bySpend' => [
                'labels' => $topBySpend->pluck('supplier_name'),
                'data' => $topBySpend->pluck('total_spend'),
            ],
            'byRating' => [
                'labels' => $topByRating->pluck('supplier_name'),
                'data' => $topByRating->map(fn($item) => round($item->avg_rating, 1)),
            ]
        ];
    }

    private function getQCRejectTrends()
    {
        $startDate = now()->subDays(30);
        $trends = CxIssue::where('source', 'GUDANG')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];
        $current = $startDate->copy();
        while ($current <= now()) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $found = $trends->firstWhere('date', $dateStr);
            $data[] = $found ? $found->count : 0;
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data),
        ];
    }

    private function getQCRejectReasons()
    {
        $reasons = CxIssue::where('source', 'GUDANG')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        return [
            'labels' => $reasons->pluck('category'),
            'data' => $reasons->pluck('count'),
        ];
    }

    private function getMaterialTrends()
    {
        return [
            'labels' => Material::orderBy('stock', 'desc')->take(5)->pluck('name'),
            'data' => Material::orderBy('stock', 'desc')->take(5)->pluck('stock'),
        ];
    }

    /**
     * Export Sortir Dashboard report to PDF
     */
    public function exportSortirPdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;
        
        $filter = $request->input('filter');
        if (is_null($filter)) {
            $filter = $request->boolean('overdue_only', false) ? 'overdue' : 'all';
        }
        $serviceId = $request->input('service_id');
        $category = $request->input('category');
        $estStart = $request->input('est_start');
        $estEnd = $request->input('est_end');

        $summaryData = app(\App\Services\WarehouseDashboardApiService::class)
            ->getSortirSummary($startDate, $endDate, $search, $filter, $serviceId, $category, $estStart, $estEnd);

        $selectedServiceName = 'Semua';
        if ($serviceId) {
            $selectedServiceName = \App\Models\Service::find($serviceId)?->name ?: 'Semua';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warehouse.pdf.sortir-report', [
            'summary' => $summaryData['metrics'],
            'items' => $summaryData['items'],
            'period' => [
                'start' => $startDate->format('d M Y'),
                'end' => $endDate->format('d M Y'),
            ],
            'filter' => [
                'search' => $search ?: 'Semua',
                'overdue_only' => match($filter) {
                    'overdue' => 'Stagnan > 3 Hari',
                    'on_track' => 'On Track',
                    default => 'Semua Status'
                },
                'service_name' => $selectedServiceName,
                'category' => $category ?: 'Semua',
                'est_date' => ($estStart && $estEnd) ? Carbon::parse($estStart)->format('d M Y') . ' s/d ' . Carbon::parse($estEnd)->format('d M Y') : 'Semua',
            ],
            'date' => now()->format('d F Y, H:i')
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_sortir_stagnan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export Production Dashboard report to PDF
     */
    public function exportProductionPdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;
        $filter = $request->input('filter', 'all');
        $serviceId = $request->input('service_id');
        $category = $request->input('category');
        $estStart = $request->input('est_start');
        $estEnd = $request->input('est_end');
        $sort = $request->input('sort', 'asc');

        $summaryData = app(\App\Services\WarehouseDashboardApiService::class)
            ->getProductionSummary($startDate, $endDate, $search, $filter, $serviceId, $category, $estStart, $estEnd, $sort);

        $selectedServiceName = 'Semua';
        if ($serviceId) {
            $selectedServiceName = \App\Models\Service::find($serviceId)?->name ?: 'Semua';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warehouse.pdf.production-report', [
            'summary' => $summaryData['metrics'],
            'items' => $summaryData['items'],
            'period' => [
                'start' => $startDate->format('d M Y'),
                'end' => $endDate->format('d M Y'),
            ],
            'filter' => [
                'search' => $search ?: 'Semua',
                'status_filter' => match($filter) {
                    'overdue' => 'Terlewat Estimasi',
                    'upcoming' => 'Mendekati Estimasi (≤ 2 Hari)',
                    'on_track' => 'On Track',
                    default => 'Semua Status'
                },
                'service_name' => $selectedServiceName,
                'category' => $category ?: 'Semua',
                'est_date' => ($estStart && $estEnd) ? Carbon::parse($estStart)->format('d M Y') . ' s/d ' . Carbon::parse($estEnd)->format('d M Y') : 'Semua',
            ],
            'date' => now()->format('d F Y, H:i')
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_produksi_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export QC Dashboard report to PDF
     */
    public function exportQcPdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;
        $filter = $request->input('filter', 'all');
        $qcStart = $request->input('qc_start');
        $qcEnd = $request->input('qc_end');

        $summaryData = app(\App\Services\WarehouseDashboardApiService::class)
            ->getQcSummary($startDate, $endDate, $search, $filter, $qcStart, $qcEnd);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warehouse.pdf.qc-report', [
            'summary' => $summaryData['metrics'],
            'items' => $summaryData['items'],
            'period' => [
                'start' => $startDate->format('d M Y'),
                'end' => $endDate->format('d M Y'),
            ],
            'filter' => [
                'search' => $search ?: 'Semua',
                'status_filter' => match($filter) {
                    'overdue' => 'Terlewat Estimasi',
                    'upcoming' => 'Mendekati Estimasi (≤ 2 Hari)',
                    default => 'Semua Status'
                },
            ],
            'date' => now()->format('d F Y, H:i')
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_qc_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export Piutang Before (Belum Selesai) report to PDF
     */
    public function exportPiutangBeforePdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $ignoreDate = $request->boolean('ignore_date', true);
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;
        $status = $request->input('status', 'all');

        $query = \App\Models\Invoice::with(['customer', 'workOrders.workOrderServices.service'])
            ->where('status', '!=', 'Lunas')
            ->whereHas('workOrders', function ($q) {
                $q->whereIn('status', [
                    \App\Enums\WorkOrderStatus::DITERIMA->value,
                    \App\Enums\WorkOrderStatus::READY_TO_DISPATCH->value,
                    \App\Enums\WorkOrderStatus::ASSESSMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_VERIFICATION->value,
                ]);
            });

        if (!$ignoreDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function ($sub) use ($search) {
                      $sub->where('name', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('workOrders', function ($sub) use ($search) {
                      $sub->where('spk_number', 'like', '%' . $search . '%')
                          ->orWhere('customer_name', 'like', '%' . $search . '%')
                          ->orWhere('customer_phone', 'like', '%' . $search . '%');
                  });
            });
        }

        $items = $query->latest()->get();
        $totalOutstanding = $items->sum('remaining_balance');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warehouse.pdf.piutang-before-report', [
            'items' => $items,
            'total_outstanding' => $totalOutstanding,
            'period' => [
                'start' => $ignoreDate ? 'Semua' : $startDate->format('d M Y'),
                'end' => $ignoreDate ? 'Waktu' : $endDate->format('d M Y'),
            ],
            'filter' => [
                'search' => $search ?: 'Semua',
                'status_filter' => match($status) {
                    'Belum Bayar' => 'Belum Bayar',
                    'DP/Cicil' => 'DP/Cicil',
                    default => 'Semua Status'
                },
            ],
            'date' => now()->format('d F Y, H:i')
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_piutang_before_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export Piutang Before (Belum Selesai) report to Excel
     */
    public function exportPiutangBeforeExcel(Request $request)
    {
        $ignoreDate = $request->boolean('ignore_date', true);
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $search = $request->search;
        $status = $request->input('status', 'all');

        $filename = 'laporan_piutang_before_' . now()->format('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PiutangBeforeExport($startDate, $endDate, $search, $status, $ignoreDate),
            $filename
        );
    }

    /**
     * Display detailed SPK list page for Sepatu Masuk (Before) or After Masuk.
     */
    public function spkDetail(Request $request)
    {
        $type = $request->get('type', 'sepatu_masuk');
        $ignoreDate = $request->boolean('ignore_date', false);
        $startDate = $request->get('start_date', now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $search = $request->get('search');

        $start = $ignoreDate ? null : Carbon::parse($startDate)->startOfDay();
        $end = $ignoreDate ? null : Carbon::parse($endDate)->endOfDay();

        $apiService = app(WarehouseDashboardApiService::class);

        if ($type === 'sepatu_masuk') {
            $items = $apiService->getSepatuMasukDetail($start, $end, $search);
            $title = 'Rincian SPK: Sepatu Masuk (Before)';
        } else {
            $items = $apiService->getAfterMasukDetail($start, $end, $search);
            $title = 'Rincian SPK: After Masuk';
        }

        return view('warehouse.dashboard.detail', compact('items', 'title', 'type', 'startDate', 'endDate', 'search', 'ignoreDate'));
    }
}
