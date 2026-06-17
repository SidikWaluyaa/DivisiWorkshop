<?php

namespace App\Services\Finance;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\BankMutation;
use App\Models\PaymentVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PaymentVerificationService
{
    /**
     * Find verification candidates: unused mutations matched with active invoices.
     * 
     * Match Types:
     * - exact: Finds an active invoice where target DP or Pelunasan matches mutation amount exactly
     * - partial: Recommendations of active invoices sorted by nominal proximity (difference <= Rp 100.000)
     * - none: No close active invoices found
     *
     * @param string|null $search Search by bank code or description
     * @param string|null $matchTypeFilter Filter by match type (exact|partial|none)
     * @return Collection
     */
    public function findCandidates(?string $search = null, ?string $matchTypeFilter = null): Collection
    {
        // 1. Fetch unused credit mutations
        $query = BankMutation::unused()->creditsOnly();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('bank_code', 'like', "%{$search}%");
            });
        }

        $mutations = $query->orderByDesc('transaction_date')->orderByDesc('created_at')->get();

        // 2. Fetch active invoices (Belum Bayar or DP/Cicil)
        $activeInvoices = Invoice::with(['customer', 'workOrders'])
            ->whereIn('status', ['Belum Bayar', 'DP/Cicil'])
            ->get();

        $candidates = collect();

        foreach ($mutations as $mutation) {
            $amount = (float)$mutation->amount;
            $exactMatch = null;
            $exactType = null; // 'DP' or 'Pelunasan'

            // A. Try exact unique code match first
            foreach ($activeInvoices as $invoice) {
                $targetDp = (float) $invoice->total_dp_with_code;
                $targetPelunasan = (float) $invoice->total_pelunasan_with_code;

                if (abs($amount - $targetDp) < 0.01) {
                    $exactMatch = $invoice;
                    $exactType = 'DP';
                    break;
                } elseif (abs($amount - $targetPelunasan) < 0.01) {
                    $exactMatch = $invoice;
                    $exactType = 'Pelunasan';
                    break;
                }
            }

            if ($exactMatch) {
                $candidates->push([
                    'mutation' => $mutation,
                    'invoice' => $exactMatch,
                    'target_type' => $exactType,
                    'match_type' => 'exact',
                    'recommendations' => collect(),
                ]);
                continue;
            }

            // B. Find closest active invoices within a threshold (diff <= Rp 10.000)
            $recommendations = collect();
            foreach ($activeInvoices as $invoice) {
                $targetDp = (float) $invoice->total_dp_with_code;
                $targetPelunasan = (float) $invoice->total_pelunasan_with_code;

                $diffDp = abs($amount - $targetDp);
                $diffPelunasan = abs($amount - $targetPelunasan);

                $minDiff = min($diffDp, $diffPelunasan);
                $type = ($diffDp < $diffPelunasan) ? 'DP' : 'Pelunasan';
                $targetNominal = ($diffDp < $diffPelunasan) ? $targetDp : $targetPelunasan;

                if ($minDiff <= 10000) {
                    $recommendations->push([
                        'invoice' => $invoice,
                        'diff' => $minDiff,
                        'target_type' => $type,
                        'target_nominal' => $targetNominal,
                    ]);
                }
            }

            $sortedRecs = $recommendations->sortBy('diff')->take(10)->values();

            if ($sortedRecs->isNotEmpty()) {
                $candidates->push([
                    'mutation' => $mutation,
                    'invoice' => null,
                    'target_type' => null,
                    'match_type' => 'partial',
                    'recommendations' => $sortedRecs,
                ]);
            } else {
                $candidates->push([
                    'mutation' => $mutation,
                    'invoice' => null,
                    'target_type' => null,
                    'match_type' => 'none',
                    'recommendations' => collect(),
                ]);
            }
        }

        // Filter candidates by match type if requested
        if ($matchTypeFilter) {
            $candidates = $candidates->filter(function ($candidate) use ($matchTypeFilter) {
                return $candidate['match_type'] === $matchTypeFilter;
            })->values();
        }

        return $candidates;
    }

    /**
     * Verify a bank mutation against an invoice.
     * Auto-creates the InvoicePayment & OrderPayment if not yet created.
     *
     * @param int $invoiceId
     * @param int $mutationId
     * @param string $targetType 'DP' or 'Pelunasan'
     * @return PaymentVerification
     * @throws \Exception
     */
    public function verifyPayment(int $invoiceId, int $mutationId, string $targetType): PaymentVerification
    {
        return DB::transaction(function () use ($invoiceId, $mutationId, $targetType) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);
            $mutation = BankMutation::lockForUpdate()->findOrFail($mutationId);

            // Validate: Mutation sudah digunakan
            if ($mutation->used) {
                throw new \Exception('Mutasi bank ini sudah digunakan untuk verifikasi lain.');
            }

            $amount = (float)$mutation->amount;

            // 1. Find or create unverified InvoicePayment for this invoice
            $payment = InvoicePayment::where('invoice_id', $invoiceId)
                ->where('amount', $amount)
                ->where('verified', false)
                ->first();

            if (!$payment) {
                $paymentType = null;
                if ($targetType === 'DP') {
                    $paymentType = 'BEFORE';
                } elseif ($targetType === 'Pelunasan') {
                    $paymentType = 'AFTER';
                }

                $payment = InvoicePayment::create([
                    'invoice_id' => $invoiceId,
                    'amount' => $amount,
                    'payment_date' => $mutation->transaction_date ?: now(),
                    'payment_method' => 'Transfer Bank',
                    'created_by' => Auth::id() ?: 1,
                    'verified' => false,
                    'type' => $paymentType,
                    'notes' => 'Dibuat otomatis via rekonsiliasi mutasi bank.',
                ]);
            }

            // 2. Find or create unverified OrderPayment for this invoice
            $orderPayment = \App\Models\OrderPayment::where('invoice_id', $invoiceId)
                ->where('amount_total', $amount)
                ->first();

            if (!$orderPayment) {
                $orderPayment = \App\Models\OrderPayment::create([
                    'invoice_id' => $invoiceId,
                    'amount_total' => $amount,
                    'paid_at' => $mutation->transaction_date ?: now(),
                    'payment_method' => 'Transfer Bank',
                    'created_by' => Auth::id() ?: 1,
                    'is_verified' => true,
                    'notes' => 'Dibuat otomatis via rekonsiliasi mutasi bank.',
                ]);
            } else {
                $orderPayment->update(['is_verified' => true]);
            }

            // 3. Build audit notes
            $targetAmount = $targetType === 'DP' ? (float)$invoice->total_dp_with_code : (float)$invoice->total_pelunasan_with_code;
            $discrepancy = abs($amount - $targetAmount);
            $notes = null;
            if ($discrepancy > 0) {
                $notes = 'Selisih nominal: Rp ' . number_format($discrepancy, 0, ',', '.') . 
                         ' (Transfer: Rp ' . number_format($amount, 0, ',', '.') . 
                         ' vs Target ' . $targetType . ': Rp ' . number_format($targetAmount, 0, ',', '.') . ')';
            }

            // 4. Create verification record
            $verification = PaymentVerification::create([
                'payment_id' => $payment->id,
                'mutation_id' => $mutationId,
                'verified_by' => Auth::id() ?: 1,
                'verified_at' => now(),
                'notes' => $notes,
            ]);

            // 5. Mark payment as verified
            $payment->update(['verified' => true]);

            // 6. Force sync invoice financials
            $invoice->syncFinancials();

            // 7. Bind invoice number and mark mutation as used (Late-Binding)
            $mutation->update([
                'invoice_number' => $invoice->invoice_number,
                'used' => true
            ]);

            return $verification;
        });
    }

    /**
     * Reverse/Cancel a payment verification.
     *
     * @param int $verificationId
     * @return bool
     * @throws \Exception
     */
    public function unverifyPayment(int $verificationId): bool
    {
        return DB::transaction(function () use ($verificationId) {
            $verification = PaymentVerification::with(['payment.invoice', 'mutation'])->lockForUpdate()->findOrFail($verificationId);
            
            $payment = $verification->payment;
            $mutation = $verification->mutation;

            if (!$payment || !$mutation) {
                throw new \Exception('Data pembayaran atau mutasi tidak ditemukan.');
            }

            // 1. Reset Payment status
            $payment->update(['verified' => false]);

            // 1b. Reset corresponding OrderPayment status
            \App\Models\OrderPayment::where('invoice_id', $payment->invoice_id)
                ->where('amount_total', $payment->amount)
                ->whereDate('paid_at', $payment->payment_date)
                ->update(['is_verified' => false]);

            // 2. Reset Mutation status
            $mutation->update([
                'invoice_number' => null,
                'used' => false
            ]);

            // 3. Delete Verification record
            $verification->delete();

            // 4. Force sync invoice financials
            if ($payment->invoice) {
                $payment->invoice->syncFinancials();
            }

            return true;
        });
    }

    /**
     * Auto-verify all unverified bank mutations that have an exact unique code match.
     *
     * @return int Number of auto-verified mutations
     */
    public function autoVerifyExactMatches(): int
    {
        $unusedMutations = BankMutation::unused()->creditsOnly()->get();
        if ($unusedMutations->isEmpty()) {
            return 0;
        }

        $activeInvoices = Invoice::with(['customer', 'workOrders'])
            ->whereIn('status', ['Belum Bayar', 'DP/Cicil'])
            ->get();
        if ($activeInvoices->isEmpty()) {
            return 0;
        }

        $autoVerifiedCount = 0;

        foreach ($unusedMutations as $mutation) {
            $amount = (float) $mutation->amount;
            $exactMatch = null;
            $exactType = null;

            // 1. Try exact match first (runs immediately today!)
            foreach ($activeInvoices as $invoice) {
                $targetDp = (float) $invoice->total_dp_with_code;
                $targetPelunasan = (float) $invoice->total_pelunasan_with_code;

                if (abs($amount - $targetDp) < 0.01) {
                    $exactMatch = $invoice;
                    $exactType = 'DP';
                    break;
                } elseif (abs($amount - $targetPelunasan) < 0.01) {
                    $exactMatch = $invoice;
                    $exactType = 'Pelunasan';
                    break;
                }
            }

            if ($exactMatch) {
                try {
                    $this->verifyPayment($exactMatch->id, $mutation->id, $exactType);
                    $autoVerifiedCount++;
                    // Refresh active invoices to prevent double-matching
                    $activeInvoices = Invoice::with(['customer', 'workOrders'])
                        ->whereIn('status', ['Belum Bayar', 'DP/Cicil'])
                        ->get();
                    continue;
                } catch (\Exception $e) {
                    \Log::error("Failed auto-verifying exact match mutation {$mutation->id}: " . $e->getMessage());
                }
            }

            // 2. If it's tomorrow/older and NO exact match, auto-verify against closest recommended invoice (within Rp 10.000)
            if ($mutation->created_at && $mutation->created_at->isToday()) {
                continue; // Skip partial match auto-verification if uploaded today
            }

            $recommendations = [];
            foreach ($activeInvoices as $invoice) {
                $dpNominal = (float) $invoice->total_dp_with_code;
                $dpDiff = abs($amount - $dpNominal);
                if ($dpDiff <= 10000) {
                    $recommendations[] = [
                        'invoice' => $invoice,
                        'target_type' => 'DP',
                        'diff' => $dpDiff,
                    ];
                }

                $pelNominal = (float) $invoice->total_pelunasan_with_code;
                $pelDiff = abs($amount - $pelNominal);
                if ($pelDiff <= 10000) {
                    $recommendations[] = [
                        'invoice' => $invoice,
                        'target_type' => 'Pelunasan',
                        'diff' => $pelDiff,
                    ];
                }
            }

            if (!empty($recommendations)) {
                // Sort by closest difference
                usort($recommendations, fn($a, $b) => $a['diff'] <=> $b['diff']);
                $bestMatch = $recommendations[0];
                
                try {
                    $this->verifyPayment($bestMatch['invoice']->id, $mutation->id, $bestMatch['target_type']);
                    $autoVerifiedCount++;
                    // Refresh active invoices to prevent double-matching
                    $activeInvoices = Invoice::with(['customer', 'workOrders'])
                        ->whereIn('status', ['Belum Bayar', 'DP/Cicil'])
                        ->get();
                } catch (\Exception $e) {
                    \Log::error("Failed auto-verifying recommended mutation {$mutation->id}: " . $e->getMessage());
                }
            }
        }

        return $autoVerifiedCount;
    }
}
