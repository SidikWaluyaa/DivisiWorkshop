<?php

namespace App\Services\Finance;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    /**
     * Create a new manual payment for an invoice.
     *
     * @param int $invoiceId
     * @param array $data [amount, payment_date, payment_method, notes]
     * @return InvoicePayment
     * @throws \Exception
     */
    public function createPayment(int $invoiceId, array $data): InvoicePayment
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            $invoice = Invoice::findOrFail($invoiceId);

            // Validate: Invoice Lunas tidak boleh menerima pembayaran baru
            if ($invoice->status === 'Lunas') {
                throw new \Exception('Invoice sudah lunas, tidak bisa menerima pembayaran baru.');
            }

            // Validate: Pembayaran tidak boleh melebihi sisa tagihan
            $remainingBalance = $invoice->remaining_balance;
            if ($data['amount'] > $remainingBalance) {
                throw new \Exception('Jumlah pembayaran (Rp ' . number_format($data['amount'], 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($remainingBalance, 0, ',', '.') . ').');
            }

            // [FIX] Pembayaran manual oleh Admin/Finance SELALU auto-verified
            // Tidak perlu menunggu rekonsiliasi mutasi bank karena diinput langsung oleh staf berwenang
            $paymentMethod = $data['payment_method'] ?? 'Transfer';
            $notes = $data['notes'] ?? ('Pembayaran via ' . $paymentMethod);
            $notes .= ' [Auto Verified by Admin]';

            // 1. Create InvoicePayment record (auto-verified)
            $payment = InvoicePayment::create([
                'invoice_id' => $invoiceId,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'notes' => $notes,
                'verified' => true, // [FIX] Selalu true untuk input manual admin
                'type' => $invoice->paid_amount == 0 ? 'BEFORE' : 'AFTER',
                'created_by' => Auth::id(),
            ]);

            // 2. [FIX] Create companion OrderPayment record agar syncFinancials() mendeteksinya
            // Ini adalah root cause utama: tanpa record ini, syncFinancials() menghitung paid=0
            \App\Models\OrderPayment::create([
                'invoice_id' => $invoice->id,
                'spk_number_snapshot' => $invoice->invoice_number,
                'type' => $invoice->paid_amount == 0 ? 'BEFORE' : 'AFTER',
                'pic_id' => Auth::id() ?: 1,
                'amount_total' => $data['amount'],
                'payment_method' => $paymentMethod,
                'paid_at' => $data['payment_date'],
                'notes' => $notes,
                'is_verified' => true, // [FIX] Wajib TRUE agar dihitung di syncFinancials
                'services_snapshot' => 'Pembayaran Tagihan Gabungan ' . $invoice->workOrders()->count() . ' SPK',
                'customer_name_snapshot' => $invoice->customer->name ?? '',
                'customer_phone_snapshot' => $invoice->customer->phone ?? '',
                'total_bill_snapshot' => $invoice->total_amount,
                'discount_snapshot' => $invoice->discount ?? 0,
                'shipping_cost_snapshot' => $invoice->shipping_cost ?? 0,
                'balance_snapshot' => $remainingBalance - $data['amount'],
            ]);

            // 3. Panggil syncFinancials robust untuk re-kalkulasi status, SLA, dan URL otomatis
            $invoice->syncFinancials();

            return $payment;
        });
    }

    /**
     * Update invoice status based on paid_amount vs total.
     * Uses the existing status string convention: 'Belum Bayar', 'DP/Cicil', 'Lunas'.
     *
     * @param Invoice $invoice
     */
    public function updateInvoiceStatus(Invoice $invoice): void
    {
        $remaining = $invoice->total_amount + $invoice->shipping_cost - $invoice->paid_amount - $invoice->discount;

        if ($remaining <= 0 && $invoice->total_amount > 0) {
            $invoice->status = 'Lunas';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'DP/Cicil';
        } else {
            $invoice->status = 'Belum Bayar';
        }

        $invoice->save();
    }
}
