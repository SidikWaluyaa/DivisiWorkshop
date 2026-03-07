<?php

namespace App\Services\Finance;

use App\Models\InvoicePayment;
use App\Models\BankMutation;
use App\Models\PaymentVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PaymentVerificationService
{
    /**
     * Find verification candidates: unverified payments matched with unused mutations.
     * 
     * Match Types:
     * - exact: Same invoice_number AND same amount
     * - partial: Same invoice_number but DIFFERENT amount (needs manual verify)
     * - none: No matching mutation found
     *
     * @param string|null $search Search by invoice number or customer name
     * @param string|null $matchTypeFilter Filter by match type (exact|partial|none)
     * @return Collection
     */
    public function findCandidates(?string $search = null, ?string $matchTypeFilter = null): Collection
    {
        $query = InvoicePayment::with(['invoice.customer'])
            ->unverified();

        if ($search) {
            $query->whereHas('invoice', function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($qCustomer) use ($search) {
                      $qCustomer->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $unverifiedPayments = $query->get();

        $candidates = collect();

        foreach ($unverifiedPayments as $payment) {
            $invoiceNumber = $payment->invoice->invoice_number ?? null;

            if (!$invoiceNumber) continue;

            // 1. Try exact match first: same invoice_number AND same amount
            $exactMatch = BankMutation::unused()
                ->creditsOnly()
                ->where('invoice_number', $invoiceNumber)
                ->where('amount', $payment->amount)
                ->first();

            if ($exactMatch) {
                $candidates->push([
                    'payment' => $payment,
                    'mutation' => $exactMatch,
                    'match_type' => 'exact',
                    'partial_mutations' => collect(),
                ]);
                continue;
            }

            // 2. Try partial match: same invoice_number, different amount
            $partialMatches = BankMutation::unused()
                ->creditsOnly()
                ->where('invoice_number', $invoiceNumber)
                ->get();

            if ($partialMatches->isNotEmpty()) {
                $candidates->push([
                    'payment' => $payment,
                    'mutation' => null,
                    'match_type' => 'partial',
                    'partial_mutations' => $partialMatches,
                ]);
                continue;
            }

            // 3. No match at all
            $candidates->push([
                'payment' => $payment,
                'mutation' => null,
                'match_type' => 'none',
                'partial_mutations' => collect(),
            ]);
        }

        // Filter candidates collection by match type if requested
        if ($matchTypeFilter) {
            $candidates = $candidates->filter(function ($candidate) use ($matchTypeFilter) {
                return $candidate['match_type'] === $matchTypeFilter;
            })->values();
        }

        return $candidates;
    }

    /**
     * Verify a payment by linking it with a bank mutation.
     * Logs amount discrepancy if amounts differ.
     *
     * @param int $paymentId
     * @param int $mutationId
     * @return PaymentVerification
     * @throws \Exception
     */
    public function verifyPayment(int $paymentId, int $mutationId): PaymentVerification
    {
        return DB::transaction(function () use ($paymentId, $mutationId) {
            // Gunakan lockForUpdate untuk mencegah race condition ganda (Double Spending)
            $payment = InvoicePayment::with('invoice')->lockForUpdate()->findOrFail($paymentId);
            $mutation = BankMutation::lockForUpdate()->findOrFail($mutationId);

            // Validate: Payment sudah terverifikasi
            if ($payment->verified) {
                throw new \Exception('Pembayaran ini sudah terverifikasi sebelumnya.');
            }

            // Validate: Mutation sudah digunakan
            if ($mutation->used) {
                throw new \Exception('Mutasi bank ini sudah digunakan untuk verifikasi lain.');
            }

            // Validate: Invoice number HARUS cocok (keamanan utama)
            $invoiceNumber = $payment->invoice->invoice_number ?? '';
            if ($mutation->invoice_number !== $invoiceNumber) {
                throw new \Exception('Invoice number mutasi (' . $mutation->invoice_number . ') tidak cocok dengan pembayaran (' . $invoiceNumber . '). Verifikasi ditolak.');
            }

            // Build verification notes
            $notes = null;
            $discrepancy = abs((float)$payment->amount - (float)$mutation->amount);
            if ($discrepancy > 0) {
                $notes = 'Selisih nominal: Rp ' . number_format($discrepancy, 0, ',', '.') . 
                         ' (Pembayaran: Rp ' . number_format((float)$payment->amount, 0, ',', '.') . 
                         ' vs Mutasi: Rp ' . number_format((float)$mutation->amount, 0, ',', '.') . ')';
            }

            // 1. Create verification record
            $verification = PaymentVerification::create([
                'payment_id' => $paymentId,
                'mutation_id' => $mutationId,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'notes' => $notes,
            ]);

            // 2. Mark payment as verified
            $payment->update(['verified' => true]);

            // 3. Mark mutation as used
            $mutation->update(['used' => true]);

            return $verification;
        });
    }
}
