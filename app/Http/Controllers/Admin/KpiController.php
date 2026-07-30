<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\KpiDurasiExport;
use Maatwebsite\Excel\Facades\Excel;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $dateRange = $request->input('date_range');

        // Parse date range
        $startDate = null;
        $endDate = null;
        if (!empty($dateRange)) {
            $parts = explode(' to ', $dateRange);
            $startDate = Carbon::parse($parts[0])->startOfDay();
            if (isset($parts[1])) {
                $endDate = Carbon::parse($parts[1])->endOfDay();
            } else {
                $endDate = Carbon::parse($parts[0])->endOfDay();
            }
        } else {
            // Default to current month
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $dateRange = $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d');
        }

        // Get KPI summary for each stage
        $summary = $this->getKpiSummary($startDate, $endDate);

        return view('admin.kpi.index', [
            'summary' => $summary,
            'dateRange' => $dateRange,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $dateRange = $request->input('date_range');

        // Parse date range
        $startDate = null;
        $endDate = null;
        if (!empty($dateRange)) {
            $parts = explode(' to ', $dateRange);
            $startDate = Carbon::parse($parts[0])->startOfDay();
            if (isset($parts[1])) {
                $endDate = Carbon::parse($parts[1])->endOfDay();
            } else {
                $endDate = Carbon::parse($parts[0])->endOfDay();
            }
        } else {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        // Get KPI summary for each stage
        $summary = $this->getKpiSummary($startDate, $endDate);

        $exportData = [];
        foreach ($summary as $stageName => $metrics) {
            $exportData[] = [
                'stage' => $stageName,
                'total_masuk' => $metrics['total_masuk'] . ' SPK',
                'total_keluar' => $metrics['total_keluar'] . ' SPK',
                'avg_duration' => $metrics['avg_duration'],
            ];
        }

        $startLabel = $startDate->format('d-m-Y');
        $endLabel = $endDate->format('d-m-Y');

        return Excel::download(
            new KpiDurasiExport($exportData, $startLabel, $endLabel),
            "Laporan_Ringkasan_KPI_Tahapan_SPK_{$startLabel}_sd_{$endLabel}.xlsx"
        );
    }

    private function getKpiSummary($startDate, $endDate): array
    {
        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];
        $summary = [];

        foreach ($stages as $stage) {
            // 1. Total Masuk: unique SPKs that entered the stage in the range
            $totalMasuk = \App\Models\WorkOrderLog::where('step', $stage)
                ->where('action', 'STATUS_CHANGE')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('workOrder', function($q) {
                    $q->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING);
                })
                ->distinct('work_order_id')
                ->count('work_order_id');

            // 2. Total Keluar: unique SPKs that exited the stage in the range
            $totalKeluar = \App\Models\WorkOrderLog::where('action', 'STATUS_CHANGE')
                ->where('description', 'like', "Status berubah dari {$stage} ke %")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('workOrder', function($q) {
                    $q->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING);
                })
                ->distinct('work_order_id')
                ->count('work_order_id');

            // 3. Average Duration: get all work_order_ids that had activity in this stage during the range
            $activeOrderIds = \App\Models\WorkOrderLog::where(function($q) use ($stage) {
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
                ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING)
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
}
