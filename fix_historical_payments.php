<?php

/**
 * ================================================================
 * FIX HISTORICAL PAYMENT DATA - ONE-TIME RECONCILIATION SCRIPT
 * ================================================================
 * 
 * Root Cause: Manual payments via PaymentService only created
 * InvoicePayment (often unverified for non-cash) but NOT the
 * companion OrderPayment record. Since syncFinancials() sums
 * from order_payments WHERE is_verified=true, these payments
 * were invisible, causing invoices to revert to "Belum Bayar".
 *
 * This script:
 * 1. Auto-verifies all manual admin/finance payments (non-CS)
 * 2. Creates missing companion OrderPayment records
 * 3. Recalculates all invoice financials
 *
 * SAFE TO RUN: Uses DB transaction, can be re-run without duplicating data.
 * 
 * Usage: php fix_historical_payments.php
 * ================================================================
 */

use App\Models\Invoice;
use App\Models\OrderPayment;
use App\Models\InvoicePayment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "================================================================\n";
echo " STARTING DATABASE RECONCILIATION FOR MANUAL PAYMENTS\n";
echo " Time: " . now()->format('Y-m-d H:i:s') . "\n";
echo "================================================================\n\n";

\DB::transaction(function () {
    $stats = [
        'invoice_payments_fixed' => 0,
        'order_payments_fixed' => 0,
        'orphaned_records_created' => 0,
        'invoices_recalculated' => 0,
    ];

    // ================================================================
    // STEP 1: Fix unverified InvoicePayments that were input by admin
    // ================================================================
    echo "[STEP 1] Fixing unverified manual InvoicePayments...\n";
    
    $affectedInvoicePayments = InvoicePayment::where('verified', false)
        ->where(function ($q) {
            // Exclude system-generated records from mutation reconciliation
            $q->where('notes', 'NOT LIKE', '%dibuat otomatis via rekonsiliasi%')
              ->orWhereNull('notes');
        })
        ->get();

    foreach ($affectedInvoicePayments as $ip) {
        // Check if this is a CS payment (those should remain unverified until audit)
        $hasCorrespondingCsPayment = OrderPayment::where('invoice_id', $ip->invoice_id)
            ->where('amount_total', $ip->amount)
            ->where('is_verified', false)
            ->where('notes', 'LIKE', '%dari CS%')
            ->exists();
        
        if ($hasCorrespondingCsPayment) {
            echo "  [SKIP] InvoicePayment ID: {$ip->id} - CS payment, needs manual audit\n";
            continue;
        }
        
        $ip->update(['verified' => true]);
        $stats['invoice_payments_fixed']++;
        echo "  [FIXED] InvoicePayment ID: {$ip->id} (Invoice #{$ip->invoice_id}, Rp " . number_format($ip->amount, 0, ',', '.') . ") -> verified=true\n";
    }
    
    echo "  Total InvoicePayments fixed: {$stats['invoice_payments_fixed']}\n\n";

    // ================================================================
    // STEP 2: Fix unverified OrderPayments that were input by Finance
    // ================================================================
    echo "[STEP 2] Fixing unverified manual OrderPayments (non-CS)...\n";
    
    $affectedOrderPayments = OrderPayment::where('is_verified', false)
        ->where(function ($q) {
            // Only fix payments NOT from CS (CS payments need manual audit by Finance)
            $q->where('notes', 'NOT LIKE', '%dari CS%')
              ->where('notes', 'NOT LIKE', '%DP dari CS%')
              ->orWhereNull('notes');
        })
        ->get();

    foreach ($affectedOrderPayments as $op) {
        $op->update(['is_verified' => true]);
        $stats['order_payments_fixed']++;
        echo "  [FIXED] OrderPayment ID: {$op->id} (Invoice #{$op->invoice_id}, Rp " . number_format($op->amount_total, 0, ',', '.') . ") -> is_verified=true\n";
    }
    
    echo "  Total OrderPayments fixed: {$stats['order_payments_fixed']}\n\n";

    // ================================================================
    // STEP 3: Create missing companion OrderPayment records
    // ================================================================
    echo "[STEP 3] Creating missing companion OrderPayment records...\n";
    
    $verifiedInvoicePayments = InvoicePayment::where('verified', true)->get();
    
    foreach ($verifiedInvoicePayments as $ip) {
        // Check if a companion OrderPayment already exists for this payment
        $hasCompanion = OrderPayment::where('invoice_id', $ip->invoice_id)
            ->where('amount_total', $ip->amount)
            ->exists();

        if ($hasCompanion) {
            continue; // Already has a companion, skip
        }

        $invoice = Invoice::find($ip->invoice_id);
        if (!$invoice) {
            echo "  [WARN] Invoice #{$ip->invoice_id} not found, skipping InvoicePayment ID: {$ip->id}\n";
            continue;
        }

        OrderPayment::create([
            'invoice_id' => $invoice->id,
            'spk_number_snapshot' => $invoice->invoice_number,
            'type' => $ip->type ?? 'BEFORE',
            'pic_id' => $ip->created_by ?? 1,
            'amount_total' => $ip->amount,
            'payment_method' => 'Transfer',
            'paid_at' => $ip->payment_date,
            'notes' => ($ip->notes ?? 'Manual payment') . ' [Reconstructed by Fix Script]',
            'is_verified' => true,
            'services_snapshot' => 'Pembayaran Tagihan Gabungan ' . $invoice->workOrders()->count() . ' SPK',
            'customer_name_snapshot' => $invoice->customer->name ?? '',
            'customer_phone_snapshot' => $invoice->customer->phone ?? '',
            'total_bill_snapshot' => $invoice->total_amount,
            'discount_snapshot' => $invoice->discount ?? 0,
            'shipping_cost_snapshot' => $invoice->shipping_cost ?? 0,
            'balance_snapshot' => $invoice->remaining_balance - $ip->amount,
        ]);

        $stats['orphaned_records_created']++;
        echo "  [CREATED] OrderPayment for Invoice #{$invoice->id} ({$invoice->invoice_number}), Rp " . number_format($ip->amount, 0, ',', '.') . "\n";
    }
    
    echo "  Total companion records created: {$stats['orphaned_records_created']}\n\n";

    // ================================================================
    // STEP 4: Recalculate ALL invoice financials
    // ================================================================
    echo "[STEP 4] Recalculating financials for all invoices...\n";
    
    $invoices = Invoice::all();
    foreach ($invoices as $invoice) {
        $oldStatus = $invoice->status;
        $oldPaid = $invoice->paid_amount;
        
        $invoice->syncFinancials();
        
        $newStatus = $invoice->fresh()->status;
        $newPaid = $invoice->fresh()->paid_amount;
        
        if ($oldStatus !== $newStatus || (float)$oldPaid !== (float)$newPaid) {
            echo "  [CHANGED] Invoice #{$invoice->id} ({$invoice->invoice_number}): "
                . "Status: {$oldStatus} -> {$newStatus}, "
                . "Paid: Rp " . number_format($oldPaid, 0, ',', '.') . " -> Rp " . number_format($newPaid, 0, ',', '.') . "\n";
        }
        
        $stats['invoices_recalculated']++;
    }
    
    echo "  Total invoices recalculated: {$stats['invoices_recalculated']}\n\n";

    // ================================================================
    // SUMMARY
    // ================================================================
    echo "================================================================\n";
    echo " RECONCILIATION COMPLETED SUCCESSFULLY\n";
    echo "================================================================\n";
    echo " InvoicePayments fixed (verified=true):    {$stats['invoice_payments_fixed']}\n";
    echo " OrderPayments fixed (is_verified=true):   {$stats['order_payments_fixed']}\n";
    echo " Orphaned companion records created:       {$stats['orphaned_records_created']}\n";
    echo " Invoices recalculated:                    {$stats['invoices_recalculated']}\n";
    echo "================================================================\n";
});
