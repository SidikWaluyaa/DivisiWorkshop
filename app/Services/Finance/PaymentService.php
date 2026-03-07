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

            // Auto-verify if payment method is TUNAI/CASH
            $paymentMethod = $data['payment_method'] ?? '';
            $isCash = in_array(strtoupper($paymentMethod), ['TUNAI', 'CASH']);
            $notes = $data['notes'] ?? ('Pembayaran via ' . $paymentMethod);
            if ($isCash) {
                $notes .= ' [TUNAI - Auto Verified]';
            }

            // 1. Create payment record
            $payment = InvoicePayment::create([
                'invoice_id' => $invoiceId,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'notes' => $notes,
                'verified' => $isCash,
                'created_by' => Auth::id(),
            ]);

            // 2. Update invoice paid_amount
            $invoice->paid_amount += $data['amount'];

            // 3. Update invoice status using existing syncFinancials-like logic
            $this->updateInvoiceStatus($invoice);

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
