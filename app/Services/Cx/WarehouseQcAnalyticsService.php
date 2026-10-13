<?php

namespace App\Services\Cx;

use App\Models\WorkOrder;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class WarehouseQcAnalyticsService
{
    /**
     * Get complete QC Analytics data for given date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getAnalytics(Carbon $startDate, Carbon $endDate): array
    {
        $startStr = $startDate->copy()->startOfDay()->toDateTimeString();
        $endStr = $endDate->copy()->endOfDay()->toDateTimeString();

        // 1. Fetch Entered Work Orders strictly within entry_date
        $enteredOrders = WorkOrder::whereBetween('entry_date', [$startStr, $endStr])
            ->get([
                'id', 
                'spk_number', 
                'cs_code', 
                'status',
                'entry_date', 
                'created_at', 
                'warehouse_qc_status', 
                'warehouse_qc_notes', 
                'warehouse_qc_at',
                'desc_kondisi_bawaan',
                'suggested_services'
            ]);

        // 2. Fetch QC Processed Work Orders within warehouse_qc_at
        $qcOrders = WorkOrder::with(['cxIssues' => function ($q) {
                $q->where('source', 'GUDANG');
            }])
            ->whereNotNull('warehouse_qc_status')
            ->whereBetween('warehouse_qc_at', [$startStr, $endStr])
            ->get([
                'id', 
                'spk_number', 
                'cs_code', 
                'status',
                'warehouse_qc_status', 
                'warehouse_qc_notes', 
                'warehouse_qc_at', 
                'entry_date', 
                'created_at',
                'desc_kondisi_bawaan',
                'suggested_services'
            ]);

        // 3. Calculate Summary Banner Metrics using unified classification
        $totalMasuk = $enteredOrders->count();
        
        $totalLolos = 0;
        $totalLolosCatatan = 0;
        $totalTolak = 0;

        foreach ($qcOrders as $order) {
            $class = $this->classifyQcOrder($order);
            if ($class === 'lolos') {
                $totalLolos++;
            } elseif ($class === 'lolos_catatan') {
                $totalLolosCatatan++;
            } elseif ($class === 'tolak') {
                $totalTolak++;
            }
        }

        $totalQc = $totalLolos + $totalLolosCatatan + $totalTolak;
        $belumQc = max(0, $totalMasuk - $totalQc);

        $pctLolos = $totalQc > 0 ? round(($totalLolos / $totalQc) * 100, 2) : 0;
        $pctLolosCatatan = $totalQc > 0 ? round(($totalLolosCatatan / $totalQc) * 100, 2) : 0;
        $pctTolak = $totalQc > 0 ? round(($totalTolak / $totalQc) * 100, 2) : 0;

        $summary = [
            'total_masuk' => $totalMasuk,
            'total_lolos' => $totalLolos,
            'pct_lolos' => $pctLolos,
            'total_lolos_catatan' => $totalLolosCatatan,
            'pct_lolos_catatan' => $pctLolosCatatan,
            'total_tolak' => $totalTolak,
            'pct_tolak' => $pctTolak,
            'total_qc' => $totalQc,
            'belum_qc' => $belumQc,
        ];

        // 4. Build CS QC Contribution Table
        $csTable = $this->buildCsContributionTable($enteredOrders, $qcOrders);

        // 5. Build Daily QC Before Table
        $dailyTable = $this->buildDailyQcTable($startDate, $endDate, $enteredOrders, $qcOrders);

        return [
            'summary' => $summary,
            'cs_table' => $csTable,
            'daily_table' => $dailyTable,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'start_formatted' => $startDate->translatedFormat('d F Y'),
                'end_formatted' => $endDate->translatedFormat('d F Y'),
            ],
        ];
    }

    /**
     * Unified classifier for QC Work Order.
     * Returns: 'tolak', 'lolos_catatan', or 'lolos'.
     */
    public function classifyQcOrder($order): string
    {
        $rawStatus = strtolower((string)$order->warehouse_qc_status);
        $statusVal = is_object($order->status) ? $order->status->value : (string)$order->status;

        // 1. Check if this is an active unresolved rejection from Warehouse QC
        if ($rawStatus === 'reject') {
            $hasOpenIssue = $order->relationLoaded('cxIssues') 
                ? $order->cxIssues->where('status', 'OPEN')->where('source', 'GUDANG')->isNotEmpty()
                : false;

            if (in_array($statusVal, ['CX_FOLLOWUP', 'HOLD_FOR_CX']) || $hasOpenIssue) {
                return 'tolak';
            }
        }

        // 2. Check for condition notes or suggested services
        $hasSpecialNotes = !empty(trim((string)$order->desc_kondisi_bawaan))
            || (!empty($order->suggested_services) && is_array($order->suggested_services) && count($order->suggested_services) > 0);

        // 3. A warehouse reject that was resolved and proceeded to work counts as lolos_catatan
        if ($rawStatus === 'reject') {
            return 'lolos_catatan';
        }

        if ($hasSpecialNotes) {
            return 'lolos_catatan';
        }

        return 'lolos';
    }

    /**
     * Helper to extract CS Code from WorkOrder.
     */
    public function extractCsCode($order): string
    {
        if (!empty($order->cs_code) && $order->cs_code !== '-') {
            return strtoupper(trim($order->cs_code));
        }

        if (!empty($order->spk_number)) {
            $parts = explode('-', $order->spk_number);
            if (count($parts) >= 5) {
                return strtoupper(trim(end($parts)));
            }
        }

        return 'OTHER';
    }

    /**
     * Build CS Contribution Table.
     */
    protected function buildCsContributionTable($enteredOrders, $qcOrders): array
    {
        // Get all configured CS Users with cs_code
        $csUsers = User::whereNotNull('cs_code')
            ->where('cs_code', '!=', '')
            ->where('cs_code', '!=', '-')
            ->orderBy('name')
            ->get(['id', 'name', 'cs_code']);

        $csMap = [];
        foreach ($csUsers as $user) {
            $code = strtoupper(trim($user->cs_code));
            $csMap[$code] = [
                'name' => strtoupper(trim($user->name)),
                'code' => $code,
            ];
        }

        // Group Entered Orders by CS Code
        $enteredByCs = [];
        foreach ($enteredOrders as $order) {
            $code = $this->extractCsCode($order);
            $enteredByCs[$code] = ($enteredByCs[$code] ?? 0) + 1;
        }

        // Group QC Orders by CS Code and Status using unified classification
        $qcByCs = [];
        foreach ($qcOrders as $order) {
            $code = $this->extractCsCode($order);
            if (!isset($qcByCs[$code])) {
                $qcByCs[$code] = [
                    'lolos' => 0,
                    'lolos_catatan' => 0,
                    'tolak' => 0,
                ];
            }

            $class = $this->classifyQcOrder($order);
            if ($class === 'lolos') {
                $qcByCs[$code]['lolos']++;
            } elseif ($class === 'lolos_catatan') {
                $qcByCs[$code]['lolos_catatan']++;
            } elseif ($class === 'tolak') {
                $qcByCs[$code]['tolak']++;
            }
        }

        // Combine all distinct CS codes
        $allCodes = array_unique(array_merge(
            array_keys($csMap),
            array_keys($enteredByCs),
            array_keys($qcByCs)
        ));

        $rows = [];
        $totalMasuk = 0;
        $totalLolos = 0;
        $totalLolosCatatan = 0;
        $totalTolak = 0;
        $totalTerQc = 0;
        $totalBlmQc = 0;

        foreach ($allCodes as $code) {
            $name = $csMap[$code]['name'] ?? ($code === 'OTHER' ? 'TANPA KODE' : "CS {$code}");
            $displayName = "{$name} ({$code})";

            $masuk = $enteredByCs[$code] ?? 0;
            $lolos = $qcByCs[$code]['lolos'] ?? 0;
            $lolosCatatan = $qcByCs[$code]['lolos_catatan'] ?? 0;
            $tolak = $qcByCs[$code]['tolak'] ?? 0;

            $terQc = $lolos + $lolosCatatan + $tolak;
            $blmQc = max(0, $masuk - $terQc);

            $pctLolos = $terQc > 0 ? round(($lolos / $terQc) * 100, 2) : 0;
            $pctLolosCatatan = $terQc > 0 ? round(($lolosCatatan / $terQc) * 100, 2) : 0;
            $pctTolak = $terQc > 0 ? round(($tolak / $terQc) * 100, 2) : 0;
            $pctTerQc = $masuk > 0 ? round(($terQc / $masuk) * 100, 2) : ($terQc > 0 ? 100 : 0);
            $pctBlmQc = $masuk > 0 ? round(($blmQc / $masuk) * 100, 2) : 0;

            // Only add rows that have at least some activity or are valid CS users
            if ($masuk > 0 || $terQc > 0 || isset($csMap[$code])) {
                $rows[] = [
                    'code' => $code,
                    'name' => $name,
                    'display_name' => $displayName,
                    'masuk' => $masuk,
                    'lolos' => $lolos,
                    'pct_lolos' => $pctLolos,
                    'lolos_catatan' => $lolosCatatan,
                    'pct_lolos_catatan' => $pctLolosCatatan,
                    'tolak' => $tolak,
                    'pct_tolak' => $pctTolak,
                    'ter_qc' => $terQc,
                    'pct_ter_qc' => $pctTerQc,
                    'blm_qc' => $blmQc,
                    'pct_blm_qc' => $pctBlmQc,
                ];

                $totalMasuk += $masuk;
                $totalLolos += $lolos;
                $totalLolosCatatan += $lolosCatatan;
                $totalTolak += $tolak;
                $totalTerQc += $terQc;
                $totalBlmQc += $blmQc;
            }
        }

        // Sort by masuk desc, then ter_qc desc
        usort($rows, function ($a, $b) {
            if ($b['masuk'] === $a['masuk']) {
                return $b['ter_qc'] <=> $a['ter_qc'];
            }
            return $b['masuk'] <=> $a['masuk'];
        });

        $totalRow = [
            'display_name' => 'TOTAL KESELURUHAN',
            'masuk' => $totalMasuk,
            'lolos' => $totalLolos,
            'pct_lolos' => $totalTerQc > 0 ? round(($totalLolos / $totalTerQc) * 100, 2) : 0,
            'lolos_catatan' => $totalLolosCatatan,
            'pct_lolos_catatan' => $totalTerQc > 0 ? round(($totalLolosCatatan / $totalTerQc) * 100, 2) : 0,
            'tolak' => $totalTolak,
            'pct_tolak' => $totalTerQc > 0 ? round(($totalTolak / $totalTerQc) * 100, 2) : 0,
            'ter_qc' => $totalTerQc,
            'pct_ter_qc' => $totalMasuk > 0 ? round(($totalTerQc / $totalMasuk) * 100, 2) : 0,
            'blm_qc' => $totalBlmQc,
            'pct_blm_qc' => $totalMasuk > 0 ? round(($totalBlmQc / $totalMasuk) * 100, 2) : 0,
        ];

        return [
            'rows' => $rows,
            'total' => $totalRow,
        ];
    }

    /**
     * Build Daily QC Before Table.
     */
    protected function buildDailyQcTable(Carbon $startDate, Carbon $endDate, $enteredOrders, $qcOrders): array
    {
        // Map entered orders strictly by entry_date Y-m-d
        $enteredByDate = [];
        foreach ($enteredOrders as $order) {
            if (!$order->entry_date) continue;
            $dt = Carbon::parse($order->entry_date);
            $dStr = $dt->format('Y-m-d');
            $enteredByDate[$dStr] = ($enteredByDate[$dStr] ?? 0) + 1;
        }

        // Map QC orders by warehouse_qc_at date Y-m-d using unified classification
        $qcByDate = [];
        foreach ($qcOrders as $order) {
            if (!$order->warehouse_qc_at) continue;
            $dStr = Carbon::parse($order->warehouse_qc_at)->format('Y-m-d');
            if (!isset($qcByDate[$dStr])) {
                $qcByDate[$dStr] = [
                    'lolos' => 0,
                    'lolos_catatan' => 0,
                    'tolak' => 0,
                ];
            }

            $class = $this->classifyQcOrder($order);
            if ($class === 'lolos') {
                $qcByDate[$dStr]['lolos']++;
            } elseif ($class === 'lolos_catatan') {
                $qcByDate[$dStr]['lolos_catatan']++;
            } elseif ($class === 'tolak') {
                $qcByDate[$dStr]['tolak']++;
            }
        }

        // Generate each day in period
        $period = CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay());
        $rows = [];

        $totalMasuk = 0;
        $totalLolos = 0;
        $totalLolosCatatan = 0;
        $totalTolak = 0;
        $totalTerQc = 0;

        foreach ($period as $date) {
            $dStr = $date->format('Y-m-d');
            $labelTanggal = $date->translatedFormat('j F Y');

            $masuk = $enteredByDate[$dStr] ?? 0;
            $lolos = $qcByDate[$dStr]['lolos'] ?? 0;
            $lolosCatatan = $qcByDate[$dStr]['lolos_catatan'] ?? 0;
            $tolak = $qcByDate[$dStr]['tolak'] ?? 0;
            $terQc = $lolos + $lolosCatatan + $tolak;

            $rows[] = [
                'date' => $dStr,
                'tanggal' => $labelTanggal,
                'masuk' => $masuk,
                'lolos' => $lolos,
                'lolos_catatan' => $lolosCatatan,
                'tolak' => $tolak,
                'ter_qc' => $terQc,
            ];

            $totalMasuk += $masuk;
            $totalLolos += $lolos;
            $totalLolosCatatan += $lolosCatatan;
            $totalTolak += $tolak;
            $totalTerQc += $terQc;
        }

        $totalRow = [
            'tanggal' => 'TOTAL KESELURUHAN',
            'masuk' => $totalMasuk,
            'lolos' => $totalLolos,
            'lolos_catatan' => $totalLolosCatatan,
            'tolak' => $totalTolak,
            'ter_qc' => $totalTerQc,
        ];

        return [
            'rows' => $rows,
            'total' => $totalRow,
        ];
    }
}
