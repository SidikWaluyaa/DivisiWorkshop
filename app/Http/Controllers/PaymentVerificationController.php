<?php

namespace App\Http\Controllers;

use App\Services\Finance\PaymentVerificationService;
use Illuminate\Http\Request;

class PaymentVerificationController extends Controller
{
    protected PaymentVerificationService $verificationService;

    public function __construct(PaymentVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Display verification candidates based on filters.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $matchType = $request->query('match_type');
        $tab = $request->query('tab', 'candidates'); // candidates or history

        // Always get candidates count for the tab label
        $candidates = $this->verificationService->findCandidates($search, $matchType);
        $candidatesCount = $candidates->count();

        if ($tab === 'history') {
            $query = \App\Models\PaymentVerification::with(['payment.invoice.customer', 'mutation', 'verifier']);
            
            if ($search) {
                $query->whereHas('payment.invoice', function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($qCustomer) use ($search) {
                          $qCustomer->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $history = $query->orderByDesc('verified_at')->paginate(25)->withQueryString();
            
            return view('finance.verifications.index', compact('history', 'search', 'matchType', 'tab', 'candidatesCount', 'candidates'));
        }

        return view('finance.verifications.index', compact('candidates', 'search', 'matchType', 'tab', 'candidatesCount'));
    }

    /**
     * Verify a payment-mutation pair.
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'mutation_id' => 'required|exists:bank_mutations,id',
        ]);

        try {
            $this->verificationService->verifyPayment((int) $id, (int) $request->mutation_id);

            return redirect()
                ->route('finance.verifications.index')
                ->with('success', 'Pembayaran berhasil diverifikasi dengan mutasi bank.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Unverify / Cancel a verification.
     */
    public function unverify($id)
    {
        try {
            $this->verificationService->unverifyPayment((int) $id);

            return redirect()
                ->route('finance.verifications.index', ['tab' => 'history'])
                ->with('success', 'Verifikasi berhasil dibatalkan. Pembayaran dan mutasi kini tersedia kembali.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
