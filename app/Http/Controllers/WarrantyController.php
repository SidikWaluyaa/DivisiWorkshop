<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderWarranty;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function printSpk($id)
    {
        $warranty = WorkOrderWarranty::with(['workOrder.workOrderServices', 'workOrder.photos'])->findOrFail($id);
        $order = $warranty->workOrder;

        return view('garansi.print-spk', compact('warranty', 'order'));
    }

    /**
     * Export Monthly Warranty Report to PDF.
     */
    public function exportPdf(Request $request)
    {
        // 1. Determine Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $periodLabel = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
            $filePeriod = $startDate->format('Ymd') . '_' . $endDate->format('Ymd');
        } else {
            $month = (int) $request->input('month', now()->month);
            $year = (int) $request->input('year', now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
            $periodLabel = $startDate->translatedFormat('F Y');
            $filePeriod = $startDate->format('Y_m');
        }

        // 2. Query SPK Garansi Masuk Periode Ini (Berdasarkan created_at)
        $itemsMasuk = WorkOrderWarranty::with(['workOrder', 'creator', 'finisher'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Query SPK Garansi Diselesaikan Periode Ini (Berdasarkan finished_at)
        $itemsSelesai = WorkOrderWarranty::with(['workOrder', 'creator', 'finisher'])
            ->where('status', 'FINISHED')
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->orderBy('finished_at', 'asc')
            ->get();

        // 4. Hitung Metrik Eksekutif
        $totalMasuk = $itemsMasuk->count();
        $masukSelesai = $itemsMasuk->where('status', 'FINISHED')->count();
        $masukOpen = $itemsMasuk->where('status', 'OPEN')->count();
        $totalSelesai = $itemsSelesai->count();
        $activePending = WorkOrderWarranty::where('status', 'OPEN')->count();
        
        $completionRate = $totalMasuk > 0 ? round(($masukSelesai / $totalMasuk) * 100, 1) : 0;

        $data = [
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemsMasuk' => $itemsMasuk,
            'itemsSelesai' => $itemsSelesai,
            'totalMasuk' => $totalMasuk,
            'masukSelesai' => $masukSelesai,
            'masukOpen' => $masukOpen,
            'totalSelesai' => $totalSelesai,
            'activePending' => $activePending,
            'completionRate' => $completionRate,
            'printedBy' => auth()->user() ? auth()->user()->name : 'Sistem Workshop',
            'printedAt' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        $pdf = Pdf::loadView('reports.pdf.warranty-monthly-report', $data)
            ->setPaper('a4', 'landscape')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream('Laporan_Garansi_' . $filePeriod . '.pdf');
    }
}

