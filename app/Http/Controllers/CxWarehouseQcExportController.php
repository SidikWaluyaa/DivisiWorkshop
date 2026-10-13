<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Cx\WarehouseQcAnalyticsService;
use App\Exports\CxWarehouseQcExport;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class CxWarehouseQcExportController extends Controller
{
    protected $service;

    public function __construct(WarehouseQcAnalyticsService $service)
    {
        $this->service = $service;
    }

    /**
     * Parse date range from request.
     */
    protected function parseDates(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        return [$start, $end];
    }

    /**
     * Export data to Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        [$start, $end] = $this->parseDates($request);
        $data = $this->service->getAnalytics($start, $end);

        $filename = 'Laporan_Warehouse_QC_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.xlsx';

        return Excel::download(
            new CxWarehouseQcExport($data, $start, $end),
            $filename
        );
    }

    /**
     * Export data to PDF (Landscape).
     */
    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->parseDates($request);
        $data = $this->service->getAnalytics($start, $end);

        $pdf = Pdf::loadView('cx.pdf.warehouse-qc-report', [
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Warehouse_QC_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }
}
