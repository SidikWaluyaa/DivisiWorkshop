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
        $search = $request->input('search');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Query WorkOrders
        $query = WorkOrder::with(['logs' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Date Filter (SPK entry_date)
        if (!empty($startDateInput)) {
            $query->whereDate('entry_date', '>=', Carbon::parse($startDateInput));
        }
        if (!empty($endDateInput)) {
            $query->whereDate('entry_date', '<=', Carbon::parse($endDateInput));
        }

        $orders = $query->orderBy('id', 'desc')->paginate(25);

        // Process KPI metrics for each order
        foreach ($orders as $order) {
            $order->kpi_data = $this->calculateKpi($order);
        }

        return view('admin.kpi.index', [
            'orders' => $orders,
            'search' => $search,
            'startDate' => $startDateInput,
            'endDate' => $endDateInput,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Query WorkOrders for Export
        $query = WorkOrder::with(['logs' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if (!empty($startDateInput)) {
            $query->whereDate('entry_date', '>=', Carbon::parse($startDateInput));
        }
        if (!empty($endDateInput)) {
            $query->whereDate('entry_date', '<=', Carbon::parse($endDateInput));
        }

        $orders = $query->orderBy('id', 'desc')->get();

        $exportData = [];
        foreach ($orders as $order) {
            $kpi = $this->calculateKpi($order);
            $exportData[] = [
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'current_status' => $order->status->label() ?? $order->status->value ?? $order->status,
                'prep_enter' => $kpi['PREPARATION']['enter_at'],
                'prep_exit' => $kpi['PREPARATION']['exit_at'],
                'prep_duration' => $kpi['PREPARATION']['duration'],
                'sortir_enter' => $kpi['SORTIR']['enter_at'],
                'sortir_exit' => $kpi['SORTIR']['exit_at'],
                'sortir_duration' => $kpi['SORTIR']['duration'],
                'prod_enter' => $kpi['PRODUCTION']['enter_at'],
                'prod_exit' => $kpi['PRODUCTION']['exit_at'],
                'prod_duration' => $kpi['PRODUCTION']['duration'],
                'qc_enter' => $kpi['QC']['enter_at'],
                'qc_exit' => $kpi['QC']['exit_at'],
                'qc_duration' => $kpi['QC']['duration'],
            ];
        }

        $startLabel = $startDateInput ? Carbon::parse($startDateInput)->format('d-m-Y') : 'Awal';
        $endLabel = $endDateInput ? Carbon::parse($endDateInput)->format('d-m-Y') : 'Akhir';

        return Excel::download(
            new KpiDurasiExport($exportData, $startLabel, $endLabel),
            "Laporan_KPI_Durasi_SPK_{$startLabel}_sd_{$endLabel}.xlsx"
        );
    }

    private function calculateKpi($order): array
    {
        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];
        $kpi = [];
        $logs = $order->logs->sortBy('created_at')->values();

        foreach ($stages as $stage) {
            $enterAt = null;
            $exitAt = null;
            $totalSeconds = 0;
            $tempEnter = null;

            foreach ($logs as $log) {
                // Determine step name safely
                $stepName = $log->step;
                $isTarget = ($stepName === $stage);

                if ($isTarget) {
                    if (is_null($tempEnter)) {
                        $tempEnter = $log->created_at;
                        if (is_null($enterAt)) {
                            $enterAt = $log->created_at;
                        }
                    }
                } else {
                    if (!is_null($tempEnter)) {
                        $totalSeconds += $log->created_at->diffInSeconds($tempEnter);
                        $exitAt = $log->created_at;
                        $tempEnter = null;
                    }
                }
            }

            // If currently in this stage (no exit log yet)
            if (!is_null($tempEnter)) {
                $totalSeconds += now()->diffInSeconds($tempEnter);
            }

            // Format duration
            $durationStr = '-';
            if ($totalSeconds > 0) {
                $days = floor($totalSeconds / 86400);
                $hours = floor(($totalSeconds % 86400) / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);

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

            $kpi[$stage] = [
                'enter_at' => $enterAt ? $enterAt->format('d/m/Y H:i') : '-',
                'exit_at' => $exitAt ? $exitAt->format('d/m/Y H:i') : '-',
                'duration' => $durationStr,
                'seconds' => $totalSeconds
            ];
        }

        return $kpi;
    }
}
