<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceDashboardApiService
{
    /**
     * Get finance dashboard summary metrics and lists.
     * 
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function getFinanceSummary(Carbon $start, Carbon $end): array
    {
        // 1. Hero Metrics (based on invoices created in period)
        // Run aggregation in database to avoid loading all invoices into memory
        $heroMetrics = Invoice::whereBetween('created_at', [$start, $end])
            ->selectRaw('
                COALESCE(SUM(total_amount + shipping_cost - discount), 0) as total_invoiced,
                COALESCE(SUM(total_amount + shipping_cost - paid_amount - discount), 0) as total_outstanding
            ')
            ->first();

        $totalInvoicedValue = (float) $heroMetrics->total_invoiced;
        $totalOutstandingReceivables = (float) $heroMetrics->total_outstanding;

        // 1b. Kamus Metrik v2.0 — invoice valid: created_at di periode, status <> 'Batal', jasa bersih > 0.
        // Jasa per SPK = total_service_price; bila 0, pakai total_transaksi dikembalikan ke harga jasa
        // (total_transaksi sudah termasuk ongkir SPK dan sudah dikurangi diskon SPK). Diskon dikurangi sekali.
        $jasaSpkSql = 'CASE WHEN work_orders.total_service_price > 0 THEN work_orders.total_service_price
            ELSE GREATEST(COALESCE(work_orders.total_transaksi, 0) - COALESCE(work_orders.shipping_cost, 0) + COALESCE(work_orders.discount, 0), 0) END';
        $jasaBersihSql = "SUM({$jasaSpkSql}) - SUM(COALESCE(work_orders.discount, 0))";

        $validInvoices = DB::table('invoices')
            ->join('work_orders', 'work_orders.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.created_at', [$start, $end])
            ->where('invoices.status', '!=', 'Batal')
            ->whereNull('work_orders.deleted_at')
            ->whereNotIn('work_orders.status', [WorkOrderStatus::BATAL->value, WorkOrderStatus::DONASI->value])
            ->groupBy('invoices.id', 'invoices.shipping_cost', 'invoices.created_at')
            ->selectRaw("invoices.id, DATE(invoices.created_at) as tanggal, COALESCE(invoices.shipping_cost, 0) as shipping_cost, COUNT(work_orders.id) as spk_count, {$jasaBersihSql} as jasa_bersih")
            ->havingRaw("{$jasaBersihSql} > 0");

        $kamusMetrics = DB::query()
            ->fromSub($validInvoices, 'valid_invoices')
            ->selectRaw('
                COUNT(*) as jumlah_tagihan,
                COALESCE(SUM(jasa_bersih), 0) as omset_tagihan,
                COALESCE(SUM(shipping_cost), 0) as ongkir_tagihan,
                COALESCE(SUM(spk_count), 0) as jumlah_spk_tagihan
            ')
            ->first();

        // Omset tagihan per tanggal invoice dibuat — himpunan invoice dan rumus sama dengan metrics.omset_tagihan
        $dailyOmsetRaw = DB::query()
            ->fromSub($validInvoices, 'valid_invoices')
            ->selectRaw('tanggal, SUM(jasa_bersih) as omset')
            ->groupBy('tanggal')
            ->pluck('omset', 'tanggal');

        // Cash received is based on payment_date of verified invoice payments in the period
        $totalCashReceived = (float) InvoicePayment::where('verified', true)
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        // Collection Rate
        $collectionRate = $totalInvoicedValue > 0
            ? round(($totalCashReceived / $totalInvoicedValue) * 100, 2)
            : 0.0;

        // 2. Invoice Status Breakdown (in-period)
        // Run aggregation in database
        $statusCountsRaw = Invoice::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as cnt, COALESCE(SUM(total_amount + shipping_cost - discount), 0) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $statusBreakdown = [
            'belum_bayar' => [
                'count' => $statusCountsRaw->get('Belum Bayar')?->cnt ?? 0,
                'total_amount' => (float) ($statusCountsRaw->get('Belum Bayar')?->total ?? 0.0),
            ],
            'dp_cicil' => [
                'count' => $statusCountsRaw->get('DP/Cicil')?->cnt ?? 0,
                'total_amount' => (float) ($statusCountsRaw->get('DP/Cicil')?->total ?? 0.0),
            ],
            'lunas' => [
                'count' => $statusCountsRaw->get('Lunas')?->cnt ?? 0,
                'total_amount' => (float) ($statusCountsRaw->get('Lunas')?->total ?? 0.0),
            ]
        ];

        // 3. Overdue Invoices (Overall unpaid/partially paid invoices that have passed their due_date)
        // Run aggregation in database and load only 10 items
        $overdueCount = Invoice::where('status', '!=', 'Lunas')
            ->where('due_date', '<', now())
            ->count();

        $overdueTotalOutstanding = (float) Invoice::where('status', '!=', 'Lunas')
            ->where('due_date', '<', now())
            ->selectRaw('COALESCE(SUM(total_amount + shipping_cost - paid_amount - discount), 0) as total')
            ->value('total');

        $overdueItems = Invoice::where('status', '!=', 'Lunas')
            ->where('due_date', '<', now())
            ->with('customer')
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_name' => $invoice->customer?->name ?? $invoice->customer_name ?? 'N/A',
                    'due_date' => $invoice->due_date ? $invoice->due_date->toDateString() : null,
                    'due_date_formatted' => $invoice->due_date ? $invoice->due_date->format('d M Y') : '-',
                    'remaining_balance' => (float) $invoice->remaining_balance,
                    'status' => $invoice->status,
                ];
            })->values()->all();

        $overdueMetrics = [
            'count' => $overdueCount,
            'total_outstanding' => $overdueTotalOutstanding,
            'items' => $overdueItems
        ];

        // 4. Daily Cash Inflow (Verified payments grouped by payment_date)
        $dailyPaymentsRaw = InvoicePayment::where('verified', true)
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->select(DB::raw('payment_date'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_date')
            ->orderBy('payment_date', 'asc')
            ->get();

        $labels = [];
        $cashInflowData = [];
        $omsetTagihanHarian = [];

        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $labels[] = $current->format('d M');
            
            $found = $dailyPaymentsRaw->first(function ($item) use ($dateStr) {
                $itemDate = $item->payment_date instanceof Carbon ? $item->payment_date->toDateString() : (string)$item->payment_date;
                return $itemDate === $dateStr;
            });
            
            $cashInflowData[] = $found ? (float) $found->total_amount : 0.0;
            $omsetTagihanHarian[$dateStr] = (float) ($dailyOmsetRaw[$dateStr] ?? 0);
            $current->addDay();
        }

        // 5. Recent Payments Table
        $recentPayments = InvoicePayment::with(['invoice.customer', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_number' => $payment->invoice?->invoice_number ?? 'N/A',
                    'customer_name' => $payment->invoice?->customer?->name ?? 'N/A',
                    'amount' => (float) $payment->amount,
                    'payment_date' => $payment->payment_date ? ($payment->payment_date instanceof Carbon ? $payment->payment_date->toDateString() : (string)$payment->payment_date) : null,
                    'notes' => $payment->notes,
                    'verified' => (bool) $payment->verified,
                    'creator_name' => $payment->creator?->name ?? 'System',
                ];
            });

        return [
            'metrics' => [
                'total_invoiced_value' => (float) $totalInvoicedValue,
                'total_cash_received' => (float) $totalCashReceived,
                'total_outstanding_receivables' => (float) $totalOutstandingReceivables,
                'collection_rate' => (float) $collectionRate,
                // Kamus Metrik v2.0
                'jumlah_tagihan' => (int) $kamusMetrics->jumlah_tagihan,
                'omset_tagihan' => (float) $kamusMetrics->omset_tagihan,
                'ongkir_tagihan' => (float) $kamusMetrics->ongkir_tagihan,
                'jumlah_spk_tagihan' => (int) $kamusMetrics->jumlah_spk_tagihan,
            ],
            'status_breakdown' => $statusBreakdown,
            'overdue_invoices' => $overdueMetrics,
            'chart_data' => [
                'labels' => $labels,
                'cash_inflow' => $cashInflowData,
                // Kamus Metrik v2.0: { "Y-m-d": omset }, tanggal tanpa invoice = 0
                'omset_tagihan_harian' => $omsetTagihanHarian,
            ],
            'recent_payments' => $recentPayments,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'last_updated' => now()->toIso8601String(),
        ];
    }
}
