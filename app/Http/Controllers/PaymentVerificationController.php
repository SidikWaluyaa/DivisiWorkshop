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

        $candidates = $this->verificationService->findCandidates($search, $matchType);

        return view('finance.verifications.index', compact('candidates', 'search', 'matchType'));
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
}
