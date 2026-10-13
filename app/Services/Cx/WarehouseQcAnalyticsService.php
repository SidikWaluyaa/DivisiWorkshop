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

        // 1. Fetch Entered Work Orders within period
        $enteredOrders = WorkOrder::where(function ($q) use ($startStr, $endStr) {
            $q->whereBetween('entry_date', [$startStr, $endStr])
              ->orWhere(function ($sub) use ($startStr, $endStr) {
                  $sub->whereNull('entry_date')
                      ->whereBetween('created_at', [$startStr, $endStr]);
              });
        })->get(['id', 'spk_number', 'cs_code', 'entry_date', 'created_at', 'warehouse_qc_status', 'warehouse_qc_notes', 'warehouse_qc_at']);

        // 2. Fetch QC Processed Work Orders within period
        $qcOrders = WorkOrder::whereNotNull('warehouse_qc_status')
            ->whereBetween('warehouse_qc_at', [$startStr, $endStr])
            ->get(['id', 'spk_number', 'cs_code', 'warehouse_qc_status', 'warehouse_qc_notes', 'warehouse_qc_at', 'entry_date', 'created_at']);

        // 3. Calculate Summary Banner Metrics
        $totalMasuk = $enteredOrders->count();
        
        $totalLolos = $qcOrders->filter(function ($order) {
            return strtolower((string)$order->warehouse_qc_status) === 'lolos' 
                && empty(trim((string)$order->warehouse_qc_notes));
        })->count();

        $totalLolosCatatan = $qcOrders->filter(function ($order) {
            return strtolower((string)$order->warehouse_qc_status) === 'lolos' 
                && !empty(trim((string)$order->warehouse_qc_notes));
        })->count();

        $totalTolak = $qcOrders->filter(function ($order) {
            return strtolower((string)$order->warehouse_qc_status) === 'reject';
        })->count();

        $totalQc = $totalLolos + $totalLolosCatatan + $totalTolak;
        $belumQc = $totalMasuk - $totalQc;

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

        // Group QC Orders by CS Code and Status
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

            $status = strtolower((string)$order->warehouse_qc_status);
            $hasNotes = !empty(trim((string)$order->warehouse_qc_notes));

            if ($status === 'lolos') {
                if ($hasNotes) {
                    $qcByCs[$code]['lolos_catatan']++;
                } else {
                    $qcByCs[$code]['lolos']++;
                }
            } elseif ($status === 'reject') {
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
        // Map entered orders by date Y-m-d
        $enteredByDate = [];
        foreach ($enteredOrders as $order) {
            $dt = $order->entry_date ? Carbon::parse($order->entry_date) : Carbon::parse($order->created_at);
            $dStr = $dt->format('Y-m-d');
            $enteredByDate[$dStr] = ($enteredByDate[$dStr] ?? 0) + 1;
        }

        // Map QC orders by date Y-m-d
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

            $status = strtolower((string)$order->warehouse_qc_status);
            $hasNotes = !empty(trim((string)$order->warehouse_qc_notes));

            if ($status === 'lolos') {
                if ($hasNotes) {
                    $qcByDate[$dStr]['lolos_catatan']++;
                } else {
                    $qcByDate[$dStr]['lolos']++;
                }
            } elseif ($status === 'reject') {
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
