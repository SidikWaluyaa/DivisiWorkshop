<?php

namespace App\Services\Cx;

use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
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

        // 1. Fetch Entered Work Orders strictly within entry_date (Excluding SPK_PENDING, matching Warehouse Dashboard)
        $enteredOrders = WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$startStr, $endStr])
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING)
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
                'suggested_services'
            ]);

        // 2. Fetch QC Processed Work Orders within warehouse_qc_at period
        $qcOrders = WorkOrder::whereNotNull('warehouse_qc_status')
            ->where('warehouse_qc_status', '!=', '')
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
                'suggested_services'
            ]);

        // 3. Fetch Pending QC Work Orders (entry_date in period, status DITERIMA, QC status null/empty)
        $pendingQcOrders = WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$startStr, $endStr])
            ->where('status', WorkOrderStatus::DITERIMA)
            ->where(function ($q) {
                $q->whereNull('warehouse_qc_status')
                  ->orWhere('warehouse_qc_status', '');
            })
            ->get([
                'id', 
                'spk_number', 
                'cs_code', 
                'status',
                'entry_date', 
                'created_at'
            ]);

        // 4. Calculate Summary Banner Metrics using unified classification
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
        $belumQc = $pendingQcOrders->count();

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

        // 5. Build CS QC Contribution Table
        $csTable = $this->buildCsContributionTable($enteredOrders, $qcOrders, $pendingQcOrders);

        // 6. Build Daily QC Before Table
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

        // 1. Tolak: QC reject directly from Warehouse Reception
        if ($rawStatus === 'reject') {
            return 'tolak';
        }

        // 2. Lolos Catatan: Passed QC with additional suggested upsell services
        $hasSuggestedServices = !empty($order->suggested_services) 
            && is_array($order->suggested_services) 
            && count($order->suggested_services) > 0;

        if ($hasSuggestedServices) {
            return 'lolos_catatan';
        }

        // 3. Lolos: Clean pass without extra recommendations
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
    protected function buildCsContributionTable($enteredOrders, $qcOrders, $pendingQcOrders): array
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

        // Group Pending QC Orders by CS Code
        $pendingByCs = [];
        foreach ($pendingQcOrders as $order) {
            $code = $this->extractCsCode($order);
            $pendingByCs[$code] = ($pendingByCs[$code] ?? 0) + 1;
        }

        // Combine all distinct CS codes
        $allCodes = array_unique(array_merge(
            array_keys($csMap),
            array_keys($enteredByCs),
            array_keys($qcByCs),
            array_keys($pendingByCs)
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
            $blmQc = $pendingByCs[$code] ?? 0;

            $pctLolos = $terQc > 0 ? round(($lolos / $terQc) * 100, 2) : 0;
            $pctLolosCatatan = $terQc > 0 ? round(($lolosCatatan / $terQc) * 100, 2) : 0;
            $pctTolak = $terQc > 0 ? round(($tolak / $terQc) * 100, 2) : 0;
            $pctTerQc = $masuk > 0 ? round(($terQc / $masuk) * 100, 2) : ($terQc > 0 ? 100 : 0);
            $pctBlmQc = $masuk > 0 ? round(($blmQc / $masuk) * 100, 2) : 0;

            // Only add rows that have at least some activity or are valid CS users
            if ($masuk > 0 || $terQc > 0 || $blmQc > 0 || isset($csMap[$code])) {
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

    /**
     * Get detailed SPK records for the period (both QC processed & pending QC).
     */
    public function getSpkDetails(Carbon $startDate, Carbon $endDate): array
    {
        $startStr = $startDate->copy()->startOfDay()->toDateTimeString();
        $endStr = $endDate->copy()->endOfDay()->toDateTimeString();

        $userMap = User::pluck('name', 'id')->toArray();

        // 1. QC Processed Orders
        $qcOrders = WorkOrder::whereNotNull('warehouse_qc_status')
            ->where('warehouse_qc_status', '!=', '')
            ->whereBetween('warehouse_qc_at', [$startStr, $endStr])
            ->get([
                'id',
                'spk_number',
                'customer_name',
                'customer_phone',
                'shoe_brand',
                'shoe_type',
                'category',
                'cs_code',
                'status',
                'entry_date',
                'warehouse_qc_status',
                'warehouse_qc_notes',
                'warehouse_qc_at',
                'warehouse_qc_by',
                'suggested_services',
                'desc_kondisi_bawaan',
            ]);

        // 2. Pending QC Orders
        $pendingQcOrders = WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$startStr, $endStr])
            ->where('status', WorkOrderStatus::DITERIMA)
            ->where(function ($q) {
                $q->whereNull('warehouse_qc_status')
                  ->orWhere('warehouse_qc_status', '');
            })
            ->get([
                'id',
                'spk_number',
                'customer_name',
                'customer_phone',
                'shoe_brand',
                'shoe_type',
                'category',
                'cs_code',
                'status',
                'entry_date',
                'warehouse_qc_status',
                'warehouse_qc_notes',
                'warehouse_qc_at',
                'warehouse_qc_by',
                'suggested_services',
                'desc_kondisi_bawaan',
            ]);

        $list = [];

        foreach ($qcOrders as $o) {
            $classification = $this->classifyQcOrder($o);
            $csCode = $this->extractCsCode($o);
            $inspector = $userMap[$o->warehouse_qc_by] ?? 'Petugas Gudang';

            $list[] = [
                'id' => $o->id,
                'spk_number' => $o->spk_number,
                'customer_name' => $o->customer_name ?: 'Pelanggan',
                'customer_phone' => $o->customer_phone ?: '-',
                'brand_type' => trim(($o->shoe_brand ?: '') . ' ' . ($o->shoe_type ?: '')) ?: '-',
                'cs_code' => $csCode,
                'status_spk' => is_object($o->status) ? $o->status->value : (string)$o->status,
                'classification' => $classification, // 'lolos', 'lolos_catatan', 'tolak'
                'entry_date' => $o->entry_date ? Carbon::parse($o->entry_date)->format('d M Y H:i') : '-',
                'qc_date' => $o->warehouse_qc_at ? Carbon::parse($o->warehouse_qc_at)->format('d M Y H:i') : '-',
                'inspector' => $inspector,
                'suggested_services' => is_array($o->suggested_services) ? $o->suggested_services : [],
                'desc_kondisi_bawaan' => $o->desc_kondisi_bawaan ?: '',
                'warehouse_qc_notes' => $o->warehouse_qc_notes ?: '',
            ];
        }

        foreach ($pendingQcOrders as $o) {
            $csCode = $this->extractCsCode($o);
            $list[] = [
                'id' => $o->id,
                'spk_number' => $o->spk_number,
                'customer_name' => $o->customer_name ?: 'Pelanggan',
                'customer_phone' => $o->customer_phone ?: '-',
                'brand_type' => trim(($o->shoe_brand ?: '') . ' ' . ($o->shoe_type ?: '')) ?: '-',
                'cs_code' => $csCode,
                'status_spk' => is_object($o->status) ? $o->status->value : (string)$o->status,
                'classification' => 'belum_qc',
                'entry_date' => $o->entry_date ? Carbon::parse($o->entry_date)->format('d M Y H:i') : '-',
                'qc_date' => '-',
                'inspector' => '-',
                'suggested_services' => [],
                'desc_kondisi_bawaan' => '',
                'warehouse_qc_notes' => '',
            ];
        }

        // Sort: tolak first, then belum_qc, then lolos_catatan, then lolos; within group by qc_date/entry_date desc
        usort($list, function ($a, $b) {
            $priority = [
                'tolak' => 1,
                'belum_qc' => 2,
                'lolos_catatan' => 3,
                'lolos' => 4,
            ];
            $pA = $priority[$a['classification']] ?? 5;
            $pB = $priority[$b['classification']] ?? 5;
            if ($pA !== $pB) {
                return $pA <=> $pB;
            }
            return $b['id'] <=> $a['id'];
        });

        return $list;
    }
}

