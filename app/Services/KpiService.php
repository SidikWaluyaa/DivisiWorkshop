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
}
