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
        if ($tab !== 'history') {
            $tab = 'candidates';
        }

        // Auto-verify all older matches (exact and closest recommended) in the background
        $autoCountVerified = $this->verificationService->autoVerifyExactMatches();
        if ($autoCountVerified > 0) {
            session()->flash('success', "💡 System Auto-Matching Sukses: {$autoCountVerified} mutasi bank (kemarin/sebelumnya) telah diverifikasi secara otomatis oleh sistem!");
        }

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
            
            return view('finance.verifications.index', compact('history', 'search', 'matchType', 'tab', 'candidatesCount'));
        }

        return view('finance.verifications.index', compact('candidates', 'search', 'matchType', 'tab', 'candidatesCount'));
    }

    /**
     * Verify a bank mutation against an invoice.
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'target_type' => 'required|in:DP,Pelunasan',
        ]);

        try {
            $this->verificationService->verifyPayment((int) $request->invoice_id, (int) $id, $request->target_type);

            return redirect()
                ->route('finance.verifications.index')
                ->with('success', 'Mutasi bank berhasil diverifikasi dan dicatat pada invoice.');
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
