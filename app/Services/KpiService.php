<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;

class KpiService
{
    /**
     * Get the aggregated KPI data for the Workshop division.
     */
    public function getWorkshopKpi($startDate, $endDate): array
    {
        $summary = $this->getKpiSummary($startDate, $endDate);
        $cxTransitions = $this->getCxTransitions($startDate, $endDate);

        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];
        foreach ($stages as $stage) {
            $summary[$stage]['masuk_bersih'] = $summary[$stage]['total_masuk'] - $cxTransitions[$stage]['from_cx'];
            $summary[$stage]['keluar_bersih'] = $summary[$stage]['total_keluar'] - $cxTransitions[$stage]['to_cx'];
        }

        return [
            'summary' => $summary,
            'cx_transitions' => $cxTransitions,
        ];
    }

    private function getKpiSummary($startDate, $endDate): array
    {
        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];
        $summary = [];

        foreach ($stages as $stage) {
            // 1. Total Masuk: unique SPKs that entered the stage in the range
            $totalMasuk = WorkOrderLog::where('step', $stage)
                ->where('action', 'STATUS_CHANGE')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('workOrder', function($q) {
                    $q->where('status', '!=', WorkOrderStatus::SPK_PENDING);
                })
                ->distinct('work_order_id')
                ->count('work_order_id');

            // 2. Total Keluar: unique SPKs that exited the stage in the range
            $totalKeluar = WorkOrderLog::where('action', 'STATUS_CHANGE')
                ->where('description', 'like', "Status berubah dari {$stage} ke %")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('workOrder', function($q) {
                    $q->where('status', '!=', WorkOrderStatus::SPK_PENDING);
                })
                ->distinct('work_order_id')
                ->count('work_order_id');

            // Average Duration Calculation
            $activeOrderIds = WorkOrderLog::where(function($q) use ($stage) {
                    $q->where(function($sub) use ($stage) {
                        $sub->where('step', $stage)->where('action', 'STATUS_CHANGE');
                    })->orWhere(function($sub) use ($stage) {
                        $sub->where('action', 'STATUS_CHANGE')->where('description', 'like', "Status berubah dari {$stage} ke %");
                    });
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct()
                ->pluck('work_order_id');

            $orders = WorkOrder::whereIn('id', $activeOrderIds)
                ->where('status', '!=', WorkOrderStatus::SPK_PENDING)
                ->with(['logs' => function($q) {
                    $q->orderBy('created_at', 'asc');
                }])
                ->get();

            $totalSeconds = 0;
            $count = 0;
            foreach ($orders as $order) {
                $kpi = $this->calculateKpiForOrder($order);
                if (isset($kpi[$stage]) && $kpi[$stage]['seconds'] > 0) {
                    $totalSeconds += $kpi[$stage]['seconds'];
                    $count++;
                }
            }

            $avgSeconds = $count > 0 ? $totalSeconds / $count : 0;

            // Format average duration into readable string
            $durationStr = '-';
            if ($avgSeconds > 0) {
                $days = floor($avgSeconds / 86400);
                $hours = floor(($avgSeconds % 86400) / 3600);
                $minutes = floor(($avgSeconds % 3600) / 60);

                $parts = [];
                if ($days > 0) {
                    $parts[] = "{$days} Hari";
                }
                if ($hours > 0) {
                    $parts[] = "{$hours} Jam";
                }
                if ($minutes > 0 || empty($parts)) {
                    $parts[] = "{$minutes} Menit";
                }

                $durationStr = implode(' ', $parts);
            }

            $summary[$stage] = [
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'avg_duration' => $durationStr,
                'avg_seconds' => $avgSeconds,
            ];
        }

        return $summary;
    }

    private function calculateKpiForOrder($order): array
    {
        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];
        $kpi = [];
        $logs = $order->logs->sortBy('created_at')->values();

        foreach ($stages as $stage) {
            $totalSeconds = 0;
            $tempEnter = null;

            foreach ($logs as $log) {
                $stepName = $log->step;
                $isTarget = ($stepName === $stage);

                if ($isTarget) {
                    if (is_null($tempEnter)) {
                        $tempEnter = $log->created_at;
                    }
                } else {
                    if (!is_null($tempEnter)) {
                        $totalSeconds += $log->created_at->diffInSeconds($tempEnter);
                        $tempEnter = null;
                    }
                }
            }

            if (!is_null($tempEnter)) {
                $totalSeconds += now()->diffInSeconds($tempEnter);
            }

            $kpi[$stage] = [
                'seconds' => $totalSeconds
            ];
        }

        return $kpi;
    }

    private function getCxTransitions($startDate, $endDate)
    {
        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];
        $transitions = [];

        foreach ($stages as $stage) {
            // Stage -> CX_FOLLOWUP
            $toCx = WorkOrderLog::where('step', 'CX_FOLLOWUP')
                ->where('action', 'STATUS_CHANGE')
                ->where('description', 'like', "Status berubah dari {$stage} ke %")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('workOrder', function($q) {
                    $q->where('status', '!=', WorkOrderStatus::SPK_PENDING);
                })
                ->distinct('work_order_id')
                ->count('work_order_id');

            // CX_FOLLOWUP -> Stage
            $fromCx = WorkOrderLog::where('step', $stage)
                ->where('action', 'STATUS_CHANGE')
                ->where('description', 'like', "Status berubah dari CX_FOLLOWUP ke %")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('workOrder', function($q) {
                    $q->where('status', '!=', WorkOrderStatus::SPK_PENDING);
                })
                ->distinct('work_order_id')
                ->count('work_order_id');

            $transitions[$stage] = [
                'to_cx' => $toCx,
                'from_cx' => $fromCx
            ];
        }

        return $transitions;
    }

    /**
     * Get the aggregated KPI data for the Gudang division.
     */
    public function getGudangKpi($startDate, $endDate): array
    {
        // 1. Sepatu Masuk (Status DITERIMA ke atas - entry_date)
        $sepatuMasuk = WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING)
            ->count();

        // 2. SPK Print / Otw Ws (Historically captured via logs)
        $spkOtw = WorkOrderLog::where('step', 'OTW_WORKSHOP')
            ->where('action', 'STATUS_CHANGE')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('work_order_id')
            ->count('work_order_id');

        // 3. SPK Tertahan / QC Reject (Historical Rejections in this Period)
        $qcReject = WorkOrderLog::where('action', 'QC_REJECTED')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 4. After Masuk (Status SELESAI - finished_date)
        $afterMasuk = WorkOrder::whereNotNull('finished_date')
            ->whereBetween('finished_date', [$startDate, $endDate])
            ->count();

        // 5. Sepatu Keluar (taken_date filled)
        $sepatuKeluar = WorkOrder::whereNotNull('taken_date')
            ->whereBetween('taken_date', [$startDate, $endDate])
            ->count();

        return [
            'sepatu_masuk' => $sepatuMasuk,
            'spk_otw' => $spkOtw,
            'qc_reject' => $qcReject,
            'after_masuk' => $afterMasuk,
            'sepatu_keluar' => $sepatuKeluar,
        ];
    }

    /**
     * Get the aggregated KPI data for the Finance division.
     */
    public function getFinanceKpi($startDate, $endDate): array
    {
        $startStr = $startDate->toDateString();
        $endStr = $endDate->toDateString();

        // 1. Invoices Created in Period
        $heroInvoices = \App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(SUM(total_amount + shipping_cost - discount), 0) as total_invoiced,
                COALESCE(SUM(total_amount + shipping_cost - paid_amount - discount), 0) as total_outstanding
            ')
            ->first();

        $totalInvoiced = (float) $heroInvoices->total_invoiced;

        // 1b. Global / All-Time Invoice Totals (Without Date Filter)
        $globalInvoices = \App\Models\Invoice::selectRaw('
                COALESCE(SUM(total_amount + shipping_cost - discount), 0) as total_invoiced_all_time,
                COALESCE(SUM(CASE WHEN status != "Lunas" THEN (total_amount + shipping_cost - paid_amount - discount) ELSE 0 END), 0) as total_outstanding_all_time
            ')
            ->first();

        $totalInvoicedAllTime = (float) $globalInvoices->total_invoiced_all_time;
        $activeReceivablesAllTime = max(0, (float) $globalInvoices->total_outstanding_all_time);

        // 2. Verified Cash Received in Period & All-Time
        $cashReceived = (float) \App\Models\InvoicePayment::where('verified', true)
            ->whereBetween('payment_date', [$startStr, $endStr])
            ->sum('amount');

        $cashReceivedAllTime = (float) \App\Models\InvoicePayment::where('verified', true)
            ->sum('amount');

        // Total Diskon Diberikan (in period)
        $totalDiscount = (float) \App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount');

        // Sisa Piutang Aktif (Piutang berjalan dari tagihan periode terpilih)
        $activeReceivables = max(0, (float) $heroInvoices->total_outstanding);

        // Collection Rate
        $collectionRate = $totalInvoiced > 0 ? round(($cashReceived / $totalInvoiced) * 100, 2) : 0;

        // 3. Invoice Status Breakdown (in period)
        $statusCounts = \App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as cnt, COALESCE(SUM(total_amount + shipping_cost - discount), 0) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $statusDistribution = [
            'belum_bayar' => [
                'count' => (int) ($statusCounts->get('Belum Bayar')?->cnt ?? 0),
                'total' => (float) ($statusCounts->get('Belum Bayar')?->total ?? 0),
            ],
            'dp_cicil' => [
                'count' => (int) ($statusCounts->get('DP/Cicil')?->cnt ?? 0),
                'total' => (float) ($statusCounts->get('DP/Cicil')?->total ?? 0),
            ],
            'lunas' => [
                'count' => (int) ($statusCounts->get('Lunas')?->cnt ?? 0),
                'total' => (float) ($statusCounts->get('Lunas')?->total ?? 0),
            ],
        ];

        // 4. Payment Type Distribution (verified payments in period)
        $types = [
            'dp_awal'     => 'BEFORE',
            'pelunasan'   => 'AFTER',
            'tambah_jasa' => 'TAMBAH_JASA',
            'lunas_awal'  => 'LUNAS_AWAL',
            'ongkir'      => 'ONGKIR',
            'oto'         => 'OTO',
        ];

        $paymentTypeDistribution = [];
        foreach ($types as $key => $typeCode) {
            $query = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', $typeCode)
                ->whereBetween('payment_date', [$startStr, $endStr]);

            $paymentTypeDistribution[$key] = [
                'count' => (int) $query->count(),
                'total' => (float) $query->sum('amount'),
            ];
        }

        // 5. Revenue Realization (Omset Closing Valid)
        // Match /cs/analytics logic: total_transaksi from valid WorkOrders (entry_date in period, not SPK_PENDING or BATAL)
        $revenueRealization = (float) \App\Models\WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING->value)
            ->where('status', '!=', \App\Enums\WorkOrderStatus::BATAL->value)
            ->sum('total_transaksi');

        if ($revenueRealization == 0) {
            $revenueRealization = (float) \App\Models\WorkOrder::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING->value)
                ->where('status', '!=', \App\Enums\WorkOrderStatus::BATAL->value)
                ->sum('total_transaksi');
        }

        if ($revenueRealization == 0) {
            $revenueRealization = (float) \App\Models\CsSpk::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', \App\Models\CsSpk::STATUS_DRAFT)
                ->sum('total_price');
        }

        return [
            'total_invoiced' => $totalInvoiced,
            'total_invoiced_all_time' => $totalInvoicedAllTime,
            'cash_received' => $cashReceived,
            'cash_received_all_time' => $cashReceivedAllTime,
            'active_receivables' => $activeReceivables,
            'active_receivables_all_time' => $activeReceivablesAllTime,
            'collection_rate' => $collectionRate,
            'total_discount' => $totalDiscount,
            'status_distribution' => $statusDistribution,
            'payment_type_distribution' => $paymentTypeDistribution,
            'revenue_realization' => $revenueRealization,
        ];
    }
}
