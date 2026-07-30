<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\KpiDurasiExport;
use App\Exports\KpiGudangExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\KpiService;

class KpiController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

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

        // Get KPI summary for each stage from Service
        $data = $this->kpiService->getWorkshopKpi($startDate, $endDate);
        
        $summary = $data['summary'];
        $cxTransitions = $data['cx_transitions'];

        // Get Gudang KPI
        $gudangSummary = $this->kpiService->getGudangKpi($startDate, $endDate);

        return view('admin.kpi.index', [
            'summary' => $summary,
            'cxTransitions' => $cxTransitions,
            'gudangSummary' => $gudangSummary,
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

        // Get KPI summary for each stage from Service
        $data = $this->kpiService->getWorkshopKpi($startDate, $endDate);
        $summary = $data['summary'];
        $cxTransitions = $data['cx_transitions'];

        $exportData = [];
        foreach ($summary as $stage => $metrics) {
            
            $masukKotor = (int)$metrics['total_masuk'];
            $masukCx = (int)$cxTransitions[$stage]['from_cx'];
            $masukBersih = $masukKotor - $masukCx;

            $keluarKotor = (int)$metrics['total_keluar'];
            $keluarCx = (int)$cxTransitions[$stage]['to_cx'];
            $keluarBersih = $keluarKotor - $keluarCx;

            $exportData[] = [
                'stage' => $stage,
                'masuk_kotor' => $masukKotor . ' SPK',
                'masuk_cx' => $masukCx . ' SPK',
                'masuk_bersih' => $masukBersih . ' SPK',
                'keluar_kotor' => $keluarKotor . ' SPK',
                'keluar_cx' => $keluarCx . ' SPK',
                'keluar_bersih' => $keluarBersih . ' SPK',
            ];
        }

        $startLabel = $startDate->format('d-m-Y');
        $endLabel = $endDate->format('d-m-Y');

        return Excel::download(
            new KpiDurasiExport($exportData, $startLabel, $endLabel),
            "Laporan_Ringkasan_KPI_Tahapan_SPK_{$startLabel}_sd_{$endLabel}.xlsx"
        );
    }

    private function getStageName($stageId)
    {
        $map = [
            'PREPARATION' => 'Preparation',
            'SORTIR'      => 'Sortir',
            'PRODUCTION'  => 'Production',
            'QC'          => 'Quality Control'
        ];

        return $map[$stageId] ?? $stageId;
    }

    public function exportGudangExcel(Request $request)
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

        // Get KPI summary from Service
        $gudangSummary = $this->kpiService->getGudangKpi($startDate, $endDate);

        $startLabel = $startDate->format('d-m-Y');
        $endLabel = $endDate->format('d-m-Y');

        return Excel::download(
            new KpiGudangExport($gudangSummary, $startLabel, $endLabel),
            "Laporan_Ringkasan_KPI_Gudang_{$startLabel}_sd_{$endLabel}.xlsx"
        );
    }
}
