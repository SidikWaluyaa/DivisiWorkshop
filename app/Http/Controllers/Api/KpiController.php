<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KpiService;
use Carbon\Carbon;

class KpiController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Fetch Workshop KPI metrics via API.
     */
    public function getWorkshopKpi(Request $request)
    {
        $dateRange = $request->input('date_range');
        
        if ($dateRange) {
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

        try {
            $data = $this->kpiService->getWorkshopKpi($startDate, $endDate);
            
            // Format response as planned
            $kpiStages = [];
            foreach (['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'] as $stage) {
                $kpiStages[$stage] = [
                    'masuk_kotor'   => $data['summary'][$stage]['total_masuk'],
                    'masuk_dari_cx' => $data['cx_transitions'][$stage]['from_cx'],
                    'masuk_bersih'  => $data['summary'][$stage]['masuk_bersih'],
                    'keluar_kotor'  => $data['summary'][$stage]['total_keluar'],
                    'keluar_ke_cx'  => $data['cx_transitions'][$stage]['to_cx'],
                    'keluar_bersih' => $data['summary'][$stage]['keluar_bersih'],
                    'rerata_durasi' => $data['summary'][$stage]['avg_duration']
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data KPI Workshop berhasil ditarik',
                'data' => [
                    'periode' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end'   => $endDate->format('Y-m-d'),
                    ],
                    'kpi_stages' => $kpiStages
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menarik data KPI Workshop.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function gudangKpi(Request $request)
    {
        $dateRange = $request->input('date_range');
        
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

        $data = $this->kpiService->getGudangKpi($startDate, $endDate);

        return response()->json([
            'status' => 'success',
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'data' => $data,
            'last_updated' => now()->toIso8601String(),
        ]);
    }

    public function financeKpi(Request $request)
    {
        $dateRange = $request->input('date_range');
        
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

        $data = $this->kpiService->getFinanceKpi($startDate, $endDate);

        return response()->json([
            'status' => 'success',
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'data' => $data,
            'last_updated' => now()->toIso8601String(),
        ]);
    }
}
