<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CxIssue;
use App\Services\CxDashboardService;
use Carbon\Carbon;

class CxDashboardController extends Controller
{
    protected $cxService;

    public function __construct(CxDashboardService $cxService)
    {
        $this->cxService = $cxService;
    }

    public function index(Request $request)
    {
        // Date Filter
        $filterStartDate = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $filterEndDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        $start = Carbon::parse($filterStartDate)->startOfDay();
        $end = Carbon::parse($filterEndDate)->endOfDay();
        
        $forceRefresh = $request->has('refresh');

        // Fetch Metrics from the Restored Service (1:1 with original logic)
        $summary = $this->cxService->getSummary($start, $end, $forceRefresh);
        
        $kpi = $summary['kpi'];
        $upsell = $summary['upsell'];
        $trend = $summary['trend'];

        return view('cx.dashboard.index', [
            // KPI Data (Restored Parity)
            'totalIssues' => $kpi['total'],
            'openIssues' => $kpi['open'],
            'inProgressIssues' => $kpi['progress'],
            'resolvedIssues' => $kpi['resolved'],
            'cancelledIssues' => $kpi['cancelled'],
            'resolvedWithUpsell' => $kpi['resolved_with_upsell'],
            'resolvedNoUpsell' => $kpi['resolved_no_upsell'],
            'avgResponseTime' => $kpi['avg_response_time_hours'],
            'resolutionRate' => $kpi['resolution_rate'],
            
            // Upsell / Tambah Jasa Data
            'totalTambahJasaNominal' => $upsell['total_nominal'],
            'totalSpkTambahJasa' => $upsell['total_volume'],
            'arpuTambahJasa' => $upsell['arpu_tambah_jasa'],
            'tambahJasaItems' => $upsell['tambah_jasa_items'],
            
            // OTO Data (Fixed Rp0 with String Parsing)
            'totalOtoNominal' => $upsell['oto_nominal'],
            'totalSpkOto' => $upsell['oto_volume'],
            'arpuOto' => $upsell['arpu_oto'],
            'otoItems' => $upsell['oto_items'],
            
            // Trend Data
            'trendLabels' => $trend['labels'],
            'trendOpen' => $trend['incoming'],
            'trendResolved' => $trend['resolved'],
            
            // Operational & Activity Data
            'issuesByCategory' => $summary['problems'],
            'issuesBySource' => $summary['source'],
            'topResolvers' => $summary['resolvers'],
            'recentIssues' => $summary['recent'],
            'overdueIssues' => $summary['overdue'],
            'commonProblems' => $summary['problems'],
            
            // Filter Data
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
        ]);
    }

    /**
     * API for Dashboard Realtime Polling
     */
    public function apiStats(Request $request)
    {
        $start = $request->has('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->has('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        
        // Always force refresh for API polling to ensure "instant" updates
        $summary = $this->cxService->getSummary($start, $end, true);
        $kpi = $summary['kpi'];
        $upsell = $summary['upsell'];

        return response()->json([
            'total_issues' => $kpi['total'],
            'open_issues' => $kpi['open'],
            'in_progress_issues' => $kpi['progress'],
            'resolved_issues' => $kpi['resolved'],
            'cancelled_issues' => $kpi['cancelled'],
            'avg_response_time' => $kpi['avg_response_time_hours'],
            'resolution_rate' => $kpi['resolution_rate'],
            'total_tambah_jasa' => $upsell['total_nominal'],
            'vol_tambah_jasa' => $upsell['total_volume'],
            'arpu_tambah_jasa' => $upsell['arpu_tambah_jasa'],
            'total_oto' => $upsell['oto_nominal'],
            'vol_oto' => $upsell['oto_volume'],
            'arpu_oto' => $upsell['arpu_oto'],
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * Export Overdue SLA Audit Report to PDF (Landscape A4)
     */
    public function exportOverduePdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $today = Carbon::now();
        $now = $today->toDateTimeString();

        $activeCard = $request->input('card');
        $searchSpk = $request->input('spk');
        $searchCustomer = $request->input('customer');
        $startDate = $request->input('start');
        $endDate = $request->input('end');
        $filterEstimation = $request->input('est', 'all');

        $query = \App\Models\WorkOrder::query()
            ->leftJoin('shippings', 'shippings.work_order_id', '=', 'work_orders.id')
            ->select('work_orders.*')
            ->selectRaw("
                CASE 
                    -- 1. Prioritas Utama: Jika estimasi tersedia dan valid
                    WHEN work_orders.estimation_date IS NOT NULL AND work_orders.estimation_date > '2000-01-01' THEN
                        CASE 
                            WHEN work_orders.estimation_date < ? THEN DATEDIFF(?, work_orders.estimation_date)
                            ELSE 0
                        END
                    -- 2. Fallback: Jika estimasi belum di-set
                    -- A. Khusus DIANTAR (jika ada data shipping dengan is_verified = 0)
                    WHEN shippings.id IS NOT NULL AND shippings.is_verified = 0 THEN
                        GREATEST(0, DATEDIFF(?, COALESCE(shippings.tanggal_masuk, work_orders.waktu, work_orders.updated_at)) - 1)
                    -- B. Khusus SELESAI (SLA-based)
                    WHEN work_orders.status = 'SELESAI' THEN
                        GREATEST(0, DATEDIFF(?, COALESCE(work_orders.waktu, work_orders.updated_at)) - 2)
                    -- C. Untuk status pengerjaan lainnya (PREPARATION, SORTIR, PRODUCTION, QC, REVISI)
                    ELSE DATEDIFF(?, COALESCE(work_orders.waktu, work_orders.updated_at))
                END as days_overdue
            ", [$now, $now, $now, $now, $now])
            ->whereNotIn('work_orders.status', [
                \App\Enums\WorkOrderStatus::BATAL->value, 
                \App\Enums\WorkOrderStatus::DONASI->value,
                \App\Enums\WorkOrderStatus::SPK_PENDING->value
            ])
            ->with('shipping');

        // Filter: Active card / Stage filter
        if ($activeCard) {
            if ($activeCard === 'GLOBAL') {
                $query->where(function($q) {
                        $q->whereIn('work_orders.status', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI'])
                          ->orWhere(function($sub) {
                              $sub->where('work_orders.status', 'SELESAI')
                                  ->whereNull('work_orders.taken_date')
                                  ->where(function($inner) {
                                      $inner->whereNull('shippings.id')
                                            ->orWhere('shippings.is_verified', 1);
                                  });
                          })
                          ->orWhere(function($sub) {
                              $sub->where('work_orders.status', 'DIANTAR')
                                  ->orWhere(function($inner) {
                                      $inner->where('work_orders.status', 'SELESAI')
                                            ->whereNotNull('work_orders.taken_date')
                                            ->where('shippings.is_verified', 0);
                                  });
                          });
                    })
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            } elseif ($activeCard === 'SELESAI') {
                $query->where('work_orders.status', 'SELESAI')
                    ->whereNull('work_orders.taken_date')
                    ->where(function($q) {
                        $q->whereNull('shippings.id')
                          ->orWhere('shippings.is_verified', 1);
                    })
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            } elseif ($activeCard === 'DIANTAR') {
                $query->whereNotNull('shippings.id')
                    ->where('shippings.is_verified', 0)
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            } elseif ($activeCard === 'REVISI') {
                $query->where('work_orders.status', 'REVISI');
            } else {
                // PREPARATION, SORTIR, PRODUCTION, QC
                $query->where('work_orders.status', $activeCard)
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            }
        } else {
            // Default view: Show only overdue/alert items across all valid stages
            $query->where(function($q) use ($today) {
                // 1. Any REVISI status
                $q->where('work_orders.status', 'REVISI')
                  // 2. Or any other active status with passed/missing estimation_date
                  ->orWhere(function($sub) use ($today) {
                      $sub->whereIn('work_orders.status', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'])
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('work_orders.estimation_date')
                                    ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                                    ->orWhere('work_orders.estimation_date', '<', $today);
                          });
                  })
                  // 3. Or SELESAI status (without active/unverified shipping)
                  ->orWhere(function($sub) use ($today) {
                      $sub->where('work_orders.status', 'SELESAI')
                          ->whereNull('work_orders.taken_date')
                          ->where(function($inner) {
                              $inner->whereNull('shippings.id')
                                    ->orWhere('shippings.is_verified', 1);
                          })
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('work_orders.estimation_date')
                                    ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                                    ->orWhere('work_orders.estimation_date', '<', $today);
                          });
                  })
                  // 4. Or active shipping (DIANTAR)
                  ->orWhere(function($sub) use ($today) {
                      $sub->whereNotNull('shippings.id')
                          ->where('shippings.is_verified', 0)
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('work_orders.estimation_date')
                                    ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                                    ->orWhere('work_orders.estimation_date', '<', $today);
                          });
                  });
            });
        }

        // Filter: Estimation Status
        if ($filterEstimation === 'missing') {
            $query->where(function($q) {
                $q->whereNull('work_orders.estimation_date')
                  ->orWhere('work_orders.estimation_date', '<=', '2000-01-01');
            });
        } elseif ($filterEstimation === 'set') {
            $query->whereNotNull('work_orders.estimation_date')
                  ->where('work_orders.estimation_date', '>', '2000-01-01');
        }

        // Filter: Search Box for SPK Number
        if ($searchSpk) {
            $query->where('work_orders.spk_number', 'like', "%{$searchSpk}%");
        }

        // Filter: Customer Name
        if ($searchCustomer) {
            $query->where('work_orders.customer_name', 'like', "%{$searchCustomer}%");
        }

        // Filter: Date Range (based on waktu stage entry date)
        if ($startDate && $endDate) {
            $query->whereBetween('work_orders.waktu', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $orders = $query->orderBy('days_overdue', 'desc')->get();

        $title = "Laporan Audit Overdue SLA";
        if ($activeCard) {
            $stageLabel = $activeCard === 'GLOBAL' ? 'Global' : ($activeCard === 'SELESAI' ? 'Selesai (Hold)' : ($activeCard === 'DIANTAR' ? 'Diantar' : ucfirst(strtolower($activeCard))));
            $title .= " - Tahap " . $stageLabel;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cx.pdf.overdue-report', [
            'orders' => $orders,
            'title' => $title,
            'filter' => [
                'card' => $activeCard ? ($activeCard === 'GLOBAL' ? 'Global' : ($activeCard === 'SELESAI' ? 'Selesai (Hold)' : ($activeCard === 'DIANTAR' ? 'Diantar' : $activeCard))) : 'Semua Tahapan',
                'spk' => $searchSpk ?: 'Semua',
                'customer' => $searchCustomer ?: 'Semua',
                'date_range' => ($startDate && $endDate) ? "$startDate s/d $endDate" : 'Semua Tanggal',
                'estimation' => $filterEstimation === 'missing' ? 'Belum Set Estimasi' : ($filterEstimation === 'set' ? 'Sudah Set Estimasi' : 'Semua')
            ],
            'date' => now()->translatedFormat('d F Y, H:i')
        ])->setPaper('a4', 'landscape');

        $filename = str_replace(' ', '_', strtolower($title)) . '_' . now()->format('Ymd_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
