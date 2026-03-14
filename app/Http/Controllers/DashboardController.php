<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Models\User;
use App\Models\CsLead;
use App\Models\CsSpk;
use App\Models\CxIssue;
use App\Models\Complaint;
use App\Models\StorageAssignment;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        [$startDate, $endDate, $prevStart, $prevEnd, $filter, $periodLabel] = $this->parseDateFilter($request);

        $data = [
            'kpi' => $this->getKpiCards($startDate, $endDate, $prevStart, $prevEnd),
            'journey' => $this->getJourneyFlow(),
            'production' => $this->getProductionInsights($startDate, $endDate),
            'businessIntel' => $this->getBusinessIntel($startDate, $endDate),
            'urgentActions' => $this->getUrgentActions(),
            'quickStats' => $this->getQuickStats(),

            // Filter metadata
            'selectedPeriod' => $filter,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'periodLabel' => $periodLabel,
        ];

        return view('dashboard-v2', $data);
    }

    /**
     * JSON API for real-time AJAX polling
     */
    public function apiData(Request $request)
    {
        [$startDate, $endDate, $prevStart, $prevEnd, $filter, $periodLabel] = $this->parseDateFilter($request);

        $urgentActions = $this->getUrgentActions();

        return response()->json([
            'kpi' => $this->getKpiCards($startDate, $endDate, $prevStart, $prevEnd),
            'journey' => $this->getJourneyFlow(),
            'production' => $this->getProductionInsights($startDate, $endDate),
            'businessIntel' => $this->getBusinessIntel($startDate, $endDate),
            'urgentActions' => [
                'overdue_spks' => $urgentActions['overdue_spks']->map(fn($s) => [
                    'id' => $s->id,
                    'spk_number' => $s->spk_number,
                    'customer_name' => $s->customer_name,
                    'status' => $s->status,
                    'estimation_date' => $s->estimation_date,
                    'estimation_diff' => Carbon::parse($s->estimation_date)->diffForHumans(),
                ]),
                'stuck_spks' => $urgentActions['stuck_spks']->map(fn($s) => [
                    'id' => $s->id,
                    'spk_number' => $s->spk_number,
                    'customer_name' => $s->customer_name,
                    'status' => $s->status,
                    'updated_diff' => $s->updated_at->diffForHumans(),
                ]),
                'cx_overdue' => $urgentActions['cx_overdue']->map(fn($i) => [
                    'spk_number' => $i->workOrder->spk_number ?? $i->spk_number ?? '-',
                    'category' => $i->category,
                    'created_diff' => $i->created_at->diffForHumans(),
                ]),
                'low_stock' => $urgentActions['low_stock']->map(fn($m) => [
                    'name' => $m->name,
                    'stock' => $m->stock,
                    'min_stock' => $m->min_stock,
                    'unit' => $m->unit,
                ]),
            ],
            'quickStats' => $this->getQuickStats(),
            'periodLabel' => $periodLabel,
            'serverTime' => now()->format('H:i'),
            'serverDate' => Carbon::now()->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * Parse date filter from request
     */
    private function parseDateFilter(Request $request)
    {
        $filter = $request->input('period', '30d');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay();
        $periodLabel = '30 Hari Terakhir';

        if ($filter === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $periodLabel = 'Hari Ini';
        } elseif ($filter === '7d') {
            $startDate = now()->subDays(7)->startOfDay();
            $endDate = now()->endOfDay();
            $periodLabel = '7 Hari Terakhir';
        } elseif ($filter === 'this_month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $periodLabel = now()->format('F Y');
        } elseif ($filter === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
            $periodLabel = now()->subMonth()->format('F Y');
        } elseif ($filter === 'ytd') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfDay();
            $periodLabel = 'Year to Date (' . now()->year . ')';
        } elseif ($filter === 'custom' && $customStart && $customEnd) {
            $startDate = Carbon::parse($customStart)->startOfDay();
            $endDate = Carbon::parse($customEnd)->endOfDay();
            $periodLabel = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
        }

        $periodDays = $startDate->diffInDays($endDate);
        $prevStart = $startDate->copy()->subDays($periodDays + 1)->startOfDay();
        $prevEnd = $startDate->copy()->subDay()->endOfDay();

        return [$startDate, $endDate, $prevStart, $prevEnd, $filter, $periodLabel];
    }

    /**
     * Section 1: KPI Cards — CS, Workshop, Gudang, CX
     */
    private function getKpiCards($start, $end, $prevStart, $prevEnd)
    {
        // === CS: Leads & Conversion ===
        $csLeads = CsLead::whereBetween('created_at', [$start, $end])->count();
        $csClosings = CsLead::whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
            ->whereBetween('updated_at', [$start, $end])->count();
        $csConversion = $csLeads > 0 ? round(($csClosings / $csLeads) * 100, 1) : 0;

        $prevCsLeads = CsLead::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $csLeadsDelta = $prevCsLeads > 0 ? round((($csLeads - $prevCsLeads) / $prevCsLeads) * 100, 1) : 0;

        // === Workshop: Utilitas Produksi ===
        $activeOrders = WorkOrder::whereNotIn('status', ['SELESAI', 'DIBATALKAN', 'TERKIRIM'])->count();
        $completedPeriod = WorkOrder::where('status', 'SELESAI')
            ->whereBetween('updated_at', [$start, $end])->count();
        $overdueOrders = WorkOrder::whereNotIn('status', ['SELESAI', 'DIBATALKAN', 'TERKIRIM'])
            ->whereNotNull('estimation_date')
            ->where('estimation_date', '<', now())
            ->count();

        $prevCompleted = WorkOrder::where('status', 'SELESAI')
            ->whereBetween('updated_at', [$prevStart, $prevEnd])->count();
        $completedDelta = $prevCompleted > 0 ? round((($completedPeriod - $prevCompleted) / $prevCompleted) * 100, 1) : 0;

        // === Gudang: Inventori ===
        $inventoryValue = Material::all()->sum(fn($m) => $m->stock * $m->price);
        $lowStockCount = Material::whereRaw('stock <= min_stock')->count();
        $storedItems = StorageAssignment::where('status', 'stored')->count();

        // === CX: Resolution ===
        $cxTotal = CxIssue::whereBetween('created_at', [$start, $end])->count();
        $cxResolved = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])->count();
        $cxResolutionRate = $cxTotal > 0 ? round(($cxResolved / $cxTotal) * 100, 1) : 0;
        $cxAvgResponse = CxIssue::where('status', 'RESOLVED')
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$start, $end])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
        $cxAvgResponse = $cxAvgResponse ? round($cxAvgResponse, 1) : 0;
        $cxOpenIssues = CxIssue::where('status', 'OPEN')->count();

        $prevCxTotal = CxIssue::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $prevCxResolved = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$prevStart, $prevEnd])->count();
        $prevCxRate = $prevCxTotal > 0 ? round(($prevCxResolved / $prevCxTotal) * 100, 1) : 0;
        $cxRateDelta = $cxResolutionRate - $prevCxRate;

        return [
            'cs' => [
                'leads' => $csLeads,
                'closings' => $csClosings,
                'conversion' => $csConversion,
                'leads_delta' => $csLeadsDelta,
            ],
            'workshop' => [
                'active' => $activeOrders,
                'completed' => $completedPeriod,
                'overdue' => $overdueOrders,
                'completed_delta' => $completedDelta,
            ],
            'gudang' => [
                'inventory_value' => $inventoryValue,
                'low_stock' => $lowStockCount,
                'stored_items' => $storedItems,
            ],
            'cx' => [
                'total' => $cxTotal,
                'resolved' => $cxResolved,
                'resolution_rate' => $cxResolutionRate,
                'avg_response' => $cxAvgResponse,
                'open_issues' => $cxOpenIssues,
                'rate_delta' => round($cxRateDelta, 1),
            ],
        ];
    }

    /**
     * Section 2: Customer Journey Map — Real-time SPK flow
     */
    private function getJourneyFlow()
    {
        return [
            ['stage' => 'CS Terima', 'icon' => '📋', 'count' => CsLead::whereNotIn('status', ['CONVERTED', 'LOST'])->count(), 'color' => '#3b82f6'],
            ['stage' => 'Gudang Masuk', 'icon' => '📥', 'count' => WorkOrder::where('status', 'DITERIMA')->count(), 'color' => '#8b5cf6'],
            ['stage' => 'Assessment', 'icon' => '🔍', 'count' => WorkOrder::where('status', 'ASSESSMENT')->count(), 'color' => '#FFC232'],
            ['stage' => 'Preparation', 'icon' => '🧼', 'count' => WorkOrder::where('status', 'PREPARATION')->count(), 'color' => '#0ea5e9'],
            ['stage' => 'Production', 'icon' => '🔨', 'count' => WorkOrder::where('status', 'PRODUCTION')->count(), 'color' => '#f97316'],
            ['stage' => 'QC', 'icon' => '✅', 'count' => WorkOrder::where('status', 'QC')->count(), 'color' => '#22AF85'],
            ['stage' => 'Selesai', 'icon' => '🛍️', 'count' => WorkOrder::where('status', 'SELESAI')->whereNull('taken_date')->count(), 'color' => '#10b981'],
        ];
    }

    /**
     * Section 3: Production Insights — Funnel + Top Technicians
     */
    private function getProductionInsights($start, $end)
    {
        // Production funnel (current snapshot, not filtered by period)
        $funnel = [
            ['label' => 'Diterima', 'count' => WorkOrder::where('status', 'DITERIMA')->count(), 'color' => '#3b82f6'],
            ['label' => 'Assessment', 'count' => WorkOrder::where('status', 'ASSESSMENT')->count(), 'color' => '#FFC232'],
            ['label' => 'Preparation', 'count' => WorkOrder::where('status', 'PREPARATION')->count(), 'color' => '#0ea5e9'],
            ['label' => 'Sortir', 'count' => WorkOrder::where('status', 'SORTIR')->count(), 'color' => '#6366f1'],
            ['label' => 'Production', 'count' => WorkOrder::where('status', 'PRODUCTION')->count(), 'color' => '#f97316'],
            ['label' => 'QC', 'count' => WorkOrder::where('status', 'QC')->count(), 'color' => '#22AF85'],
        ];

        // Top 5 technicians by jobs assigned — direct query against FK columns
        $techColumns = [
            'technician_production_id',
            'pic_sortir_sol_id',
            'pic_sortir_upper_id',
            'qc_jahit_technician_id',
            'qc_cleanup_technician_id',
        ];

        $techCounts = collect();
        foreach ($techColumns as $col) {
            $results = WorkOrder::whereNotNull($col)
                ->select($col, DB::raw('count(*) as cnt'))
                ->groupBy($col)
                ->get();
            foreach ($results as $row) {
                $userId = $row->$col;
                $techCounts[$userId] = ($techCounts[$userId] ?? 0) + $row->cnt;
            }
        }

        $topTechIds = $techCounts->sortDesc()->take(5);
        $techUsers = User::whereIn('id', $topTechIds->keys())->get()->keyBy('id');

        $technicians = $topTechIds->map(function($count, $userId) use ($techUsers) {
            $user = $techUsers[$userId] ?? null;
            return [
                'name' => $user ? $user->name : 'Unknown',
                'count' => $count,
                'specialization' => $user->specialization ?? '-',
            ];
        })->values();


        return [
            'funnel' => $funnel,
            'technicians' => $technicians,
        ];
    }

    /**
     * Section 4: Business Intelligence — Revenue + CX Sentiment
     */
    private function getBusinessIntel($start, $end)
    {
        // Revenue trend (daily, this period) — from completed work orders
        $completedOrders = WorkOrder::whereIn('status', ['SELESAI', 'TERKIRIM'])
            ->whereBetween('updated_at', [$start, $end])
            ->with('services')
            ->get();

        $totalRevenue = $completedOrders->sum(fn($o) => $o->services->sum('price'));

        $labels = [];
        $revenueData = [];
        $diffDays = $start->diffInDays($end);

        if ($diffDays > 60) {
            // Monthly aggregation
            $current = $start->copy()->startOfMonth();
            while ($current <= $end) {
                $labels[] = $current->format('M Y');
                $monthStr = $current->format('Y-m');
                $revenueData[] = $completedOrders->filter(fn($o) => $o->updated_at->format('Y-m') === $monthStr)
                    ->sum(fn($o) => $o->services->sum('price'));
                $current->addMonth();
            }
        } else {
            $current = $start->copy();
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('d M');
                $revenueData[] = $completedOrders->filter(fn($o) => $o->updated_at->format('Y-m-d') === $dateStr)
                    ->sum(fn($o) => $o->services->sum('price'));
                $current->addDay();
            }
        }

        // CX Top Issue Categories
        $topIssues = CxIssue::whereBetween('created_at', [$start, $end])
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Complaint Analytics
        $complaintsPending = Complaint::where('status', 'PENDING')->count();
        $complaintsProcess = Complaint::where('status', 'PROCESS')->count();
        $complaintsOverdue = Complaint::where('status', 'PENDING')
            ->where('created_at', '<', now()->subHours(48))->count();

        return [
            'revenue' => [
                'total' => $totalRevenue,
                'labels' => $labels,
                'data' => $revenueData,
            ],
            'topIssues' => $topIssues,
            'complaints' => [
                'pending' => $complaintsPending,
                'process' => $complaintsProcess,
                'overdue' => $complaintsOverdue,
            ],
        ];
    }

    /**
     * Section 5: Urgent Actions — At-Risk SPKs + CX Alerts
     */
    private function getUrgentActions()
    {
        // At-Risk: Overdue SPKs (past estimation date)
        $overdueSpks = WorkOrder::whereNotIn('status', ['SELESAI', 'DIBATALKAN', 'TERKIRIM'])
            ->whereNotNull('estimation_date')
            ->where('estimation_date', '<', now())
            ->with('services')
            ->orderBy('estimation_date', 'asc')
            ->limit(5)
            ->get();

        // Stuck SPKs (same status > 48 hours)
        $stuckSpks = WorkOrder::whereNotIn('status', ['SELESAI', 'DIBATALKAN', 'TERKIRIM'])
            ->where('updated_at', '<', now()->subHours(48))
            ->orderBy('updated_at', 'asc')
            ->limit(5)
            ->get();

        // CX Open Issues > 3 days
        $cxOverdue = CxIssue::where('status', 'OPEN')
            ->where('created_at', '<', now()->subDays(3))
            ->with(['workOrder', 'reporter'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // Low stock materials
        $lowStockMaterials = Material::whereRaw('stock <= min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return [
            'overdue_spks' => $overdueSpks,
            'stuck_spks' => $stuckSpks,
            'cx_overdue' => $cxOverdue,
            'low_stock' => $lowStockMaterials,
        ];
    }

    /**
     * Quick stats for badges on quick action buttons
     */
    private function getQuickStats()
    {
        return [
            'pending_complaints' => Complaint::where('status', 'PENDING')->count(),
            'low_stock_count' => Material::whereRaw('stock <= min_stock')->count(),
            'pending_po' => Purchase::where('status', 'pending')->count(),
            'today_deadlines' => WorkOrder::whereDate('estimation_date', today())
                ->whereNotIn('status', ['SELESAI', 'DIBATALKAN'])->count(),
        ];
    }
}
