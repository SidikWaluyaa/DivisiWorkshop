<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceReportController extends Controller
{
    /**
     * Export filtered dashboard data as PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'tab' => 'required|in:invoices,payments',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:BB,BL,L',
            'type' => 'nullable|string',
        ]);

        $tab = $request->input('tab');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $periodLabel = 'Semua Periode';
        if ($startDate && $endDate) {
            $periodLabel = Carbon::parse($startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($endDate)->format('d/m/Y');
        }

        if ($tab === 'invoices') {
            return $this->exportInvoicesPdf($request, $startDate, $endDate, $periodLabel);
        }

        return $this->exportPaymentsPdf($request, $startDate, $endDate, $periodLabel);
    }

    /**
     * Generate PDF for Invoices tab.
     */
    private function exportInvoicesPdf(Request $request, $startDate, $endDate, $periodLabel)
    {
        $query = Invoice::with('customer');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        $filterStatus = $request->input('status');
        $statusLabel = 'Semua Status';

        if ($filterStatus) {
            $statusMap = [
                'BB' => 'Belum Bayar',
                'BL' => 'DP/Cicil',
                'L'  => 'Lunas',
            ];
            $statusLabelMap = [
                'BB' => 'Belum Bayar (BB)',
                'BL' => 'Belum Lunas / DP/Cicil (BL)',
                'L'  => 'Lunas (L)',
            ];
            if (isset($statusMap[$filterStatus])) {
                $query->where('status', $statusMap[$filterStatus]);
                $statusLabel = $statusLabelMap[$filterStatus];
            }
        }

        $invoices = $query->orderByDesc('created_at')->get();

        // Summary calculations
        $totalAmount = $invoices->sum(fn($inv) => $inv->total_amount + $inv->shipping_cost - $inv->discount);
        $totalPaid = $invoices->sum('paid_amount');
        $totalRemaining = $invoices->sum(fn($inv) => $inv->remaining_balance);

        // Status breakdown
        $statusBreakdown = [
            'BB' => ['label' => 'Belum Bayar', 'count' => 0, 'total' => 0],
            'BL' => ['label' => 'DP/Cicil', 'count' => 0, 'total' => 0],
            'L'  => ['label' => 'Lunas', 'count' => 0, 'total' => 0],
        ];
        foreach ($invoices as $inv) {
            $code = $inv->payment_status_code;
            if (isset($statusBreakdown[$code])) {
                $statusBreakdown[$code]['count']++;
                $statusBreakdown[$code]['total'] += ($inv->total_amount + $inv->shipping_cost - $inv->discount);
            }
        }

        $pdf = Pdf::loadView('finance.reports.pdf-invoices', [
            'invoices' => $invoices,
            'periodLabel' => $periodLabel,
            'statusLabel' => $statusLabel,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalRemaining' => $totalRemaining,
            'statusBreakdown' => $statusBreakdown,
            'printDate' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('A4', 'landscape');

        $fileName = 'Laporan-Invoices-' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->stream($fileName);
    }

    /**
     * Generate PDF for Invoice Payments tab.
     */
    private function exportPaymentsPdf(Request $request, $startDate, $endDate, $periodLabel)
    {
        $query = InvoicePayment::with(['invoice', 'creator']);

        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        $filterType = $request->input('type');
        $typeLabel = 'Semua Type';

        if ($filterType) {
            $query->where('type', $filterType);
            $typeLabel = $filterType;
        }

        $payments = $query->orderByDesc('payment_date')->get();

        // Summary
        $totalAmount = $payments->sum('amount');

        // Type breakdown
        $typeBreakdown = [];
        $types = ['BEFORE', 'AFTER', 'TAMBAH_JASA', 'LUNAS_AWAL', 'ONGKIR'];
        foreach ($types as $t) {
            $filtered = $payments->where('type', $t);
            $typeBreakdown[$t] = [
                'count' => $filtered->count(),
                'total' => $filtered->sum('amount'),
            ];
        }

        $pdf = Pdf::loadView('finance.reports.pdf-payments', [
            'payments' => $payments,
            'periodLabel' => $periodLabel,
            'typeLabel' => $typeLabel,
            'totalAmount' => $totalAmount,
            'typeBreakdown' => $typeBreakdown,
            'printDate' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('A4', 'landscape');

        $fileName = 'Laporan-Payments-' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->stream($fileName);
    }
}
