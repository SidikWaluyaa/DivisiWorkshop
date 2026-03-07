<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Services\Finance\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display list of all invoice payments.
     */
    public function index(Request $request)
    {
        $query = InvoicePayment::with(['invoice', 'creator', 'verification'])
            ->orderByDesc('created_at');

        // Filter by verification status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->where('verified', true);
            } elseif ($request->status === 'unverified') {
                $query->where('verified', false);
            }
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%');
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('finance.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $invoices = Invoice::where('status', '!=', 'Lunas')
            ->with('customer')
            ->orderByDesc('created_at')
            ->get();

        return view('finance.payments.create', compact('invoices'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ], [
            'invoice_id.required' => 'Pilih invoice terlebih dahulu.',
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.min' => 'Jumlah pembayaran harus lebih dari 0.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        try {
            $this->paymentService->createPayment($request->invoice_id, [
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('finance.payments.index')
                ->with('success', 'Pembayaran sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
