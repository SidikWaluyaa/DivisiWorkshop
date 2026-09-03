<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\OrderPayment;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentVerificationIndex extends Component
{
    use WithPagination;

    public $activeTab = 'pending'; // 'pending', 'verified', 'rejected'
    public $search = '';
    public $selectedProofImage = null;
    
    // Payment Types selection per payment ID: [payment_id => 'BEFORE'|'AFTER'|...]
    public $selectedTypes = [];

    // Approval Modal State (for reviewing details before confirming)
    public $approveModalOpen = false;
    public $approvingPayment = null;
    public $approvePaymentType = 'BEFORE';

    // Reject Modal State
    public $rejectModalOpen = false;
    public $rejectPaymentId = null;
    public $rejectReason = '';

    protected $queryString = [
        'activeTab' => ['except' => 'pending'],
        'search'    => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function showProofModal($imageUrl)
    {
        $this->selectedProofImage = $imageUrl;
    }

    public function closeProofModal()
    {
        $this->selectedProofImage = null;
    }

    public function openApproveModal($paymentId)
    {
        $payment = OrderPayment::with(['invoice.customer', 'invoice.workOrders.workOrderServices.service'])->findOrFail($paymentId);
        $this->approvingPayment = $payment;

        // Auto determine recommended type
        if ($payment->invoice) {
            if ($payment->invoice->paid_amount == 0) {
                if ($payment->amount_total >= $payment->invoice->total_amount) {
                    $this->approvePaymentType = 'LUNAS_AWAL';
                } else {
                    $this->approvePaymentType = 'BEFORE'; // DP / Pencicilan
                }
            } else {
                $this->approvePaymentType = 'AFTER'; // Pelunasan
            }
        } else {
            $this->approvePaymentType = $payment->type ?? 'BEFORE';
        }

        $this->approveModalOpen = true;
    }

    public function closeApproveModal()
    {
        $this->approveModalOpen = false;
        $this->approvingPayment = null;
    }

    public function openRejectModal($paymentId)
    {
        $this->rejectPaymentId = $paymentId;
        $this->rejectReason = '';
        $this->rejectModalOpen = true;
    }

    public function closeRejectModal()
    {
        $this->rejectPaymentId = null;
        $this->rejectReason = '';
        $this->rejectModalOpen = false;
    }

    public function approvePaymentDirect($paymentId, $paymentType = null)
    {
        $type = $paymentType ?: ($this->selectedTypes[$paymentId] ?? 'BEFORE');
        $this->processApproval($paymentId, $type);
    }

    public function confirmApproveFromModal()
    {
        if (!$this->approvingPayment) return;
        $this->processApproval($this->approvingPayment->id, $this->approvePaymentType);
        $this->closeApproveModal();
    }

    private function processApproval($paymentId, $paymentType)
    {
        try {
            $payment = OrderPayment::with(['invoice.customer', 'invoice.workOrders'])->findOrFail($paymentId);

            if ($payment->is_verified) {
                $this->dispatch('swal:toast', icon: 'info', title: 'Pembayaran ini sudah diverifikasi sebelumnya.');
                return;
            }

            $typeLabel = match($paymentType) {
                'BEFORE'      => 'DP / Pencicilan',
                'AFTER'       => 'Pelunasan Pesanan',
                'TAMBAH_JASA' => 'Tambah Jasa',
                'LUNAS_AWAL'  => 'Lunas Awal',
                'ONGKIR'      => 'Pembayaran Ongkir',
                'OTO'         => 'Pembayaran OTO',
                default       => $paymentType
            };

            $payment->is_verified = true;
            $payment->type = $paymentType;
            $payment->pic_id = Auth::id();
            $payment->notes = ($payment->notes ? $payment->notes . ' ' : '') . "[Diverifikasi sbg {$typeLabel} oleh " . (Auth::user()->name ?? 'Finance') . ' pada ' . now()->format('d/m/Y H:i') . ']';
            $payment->save();

            // Update Invoice Balance & Status
            if ($payment->invoice) {
                $invoice = $payment->invoice;
                $invoice->paid_amount = (float)$invoice->paid_amount + (float)$payment->amount_total;

                if ($invoice->paid_amount >= $invoice->total_amount || in_array($paymentType, ['LUNAS_AWAL', 'AFTER'])) {
                    if ($invoice->paid_amount >= $invoice->total_amount) {
                        $invoice->status = 'Lunas';
                    } else {
                        $invoice->status = 'DP/Cicil';
                    }
                } elseif ($invoice->paid_amount > 0) {
                    $invoice->status = 'DP/Cicil';
                }
                $invoice->save();

                // Record in InvoicePayment for Invoice Detail View synchronization
                try {
                    InvoicePayment::create([
                        'invoice_id'   => $invoice->id,
                        'amount'       => $payment->amount_total,
                        'payment_date' => $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->toDateString() : now()->toDateString(),
                        'notes'        => $payment->notes,
                        'verified'     => true,
                        'type'         => $paymentType,
                        'created_by'   => Auth::id() ?: 1,
                    ]);
                } catch (\Throwable $invPayErr) {
                    Log::error("InvoicePayment creation error on verify: " . $invPayErr->getMessage());
                }

                // Synchronize invoice financials
                $invoice->syncFinancials();

                // Audit Log on associated work orders
                foreach ($invoice->workOrders as $wo) {
                    $wo->logs()->create([
                        'user_id'     => Auth::id(),
                        'step'        => 'PAYMENT',
                        'action'      => 'PAYMENT_VERIFIED',
                        'description' => "Pembayaran Rp " . number_format($payment->amount_total, 0, ',', '.') . " via {$payment->payment_method} ({$typeLabel}) diverifikasi oleh " . (Auth::user()->name ?? 'Finance') . ". Status Invoice: {$invoice->status}.",
                    ]);
                }
            }

            $this->dispatch('swal:toast', icon: 'success', title: "Pembayaran Rp " . number_format($payment->amount_total, 0, ',', '.') . " ({$typeLabel}) Berhasil Diverifikasi!");

        } catch (\Throwable $e) {
            Log::error("Approve Payment Error (#{$paymentId}): " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    public function confirmRejectPayment()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:3|max:500',
        ], [
            'rejectReason.required' => 'Tuliskan alasan penolakan bukti pembayaran.',
            'rejectReason.min'      => 'Alasan penolakan minimal 3 karakter.',
        ]);

        try {
            $payment = OrderPayment::with(['invoice.workOrders'])->findOrFail($this->rejectPaymentId);

            $payment->is_verified = false;
            $payment->notes = ($payment->notes ? $payment->notes . ' ' : '') . '[DITOLAK FINANCE: ' . $this->rejectReason . ' oleh ' . (Auth::user()->name ?? 'Finance') . ' pada ' . now()->format('d/m/Y H:i') . ']';
            $payment->save();

            if ($payment->invoice) {
                foreach ($payment->invoice->workOrders as $wo) {
                    $wo->logs()->create([
                        'user_id'     => Auth::id(),
                        'step'        => 'PAYMENT',
                        'action'      => 'PAYMENT_REJECTED',
                        'description' => "Bukti pembayaran Rp " . number_format($payment->amount_total, 0, ',', '.') . " ditolak: {$this->rejectReason}",
                    ]);
                }
            }

            $this->closeRejectModal();
            $this->dispatch('swal:toast', icon: 'warning', title: 'Bukti pembayaran telah ditolak.');

        } catch (\Throwable $e) {
            Log::error("Reject Payment Error (#{$this->rejectPaymentId}): " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = OrderPayment::with(['invoice.customer', 'invoice.workOrders.workOrderServices.service', 'pic'])
            ->latest('paid_at');

        if ($this->activeTab === 'pending') {
            $query->where(function($q) {
                $q->where('is_verified', false)
                  ->orWhereNull('is_verified');
            })->where('notes', 'NOT LIKE', '%[DITOLAK FINANCE%');
        } elseif ($this->activeTab === 'verified') {
            $query->where('is_verified', true);
        } elseif ($this->activeTab === 'rejected') {
            $query->where('notes', 'LIKE', '%[DITOLAK FINANCE%');
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('invoice', function($iq) use ($search) {
                      $iq->where('invoice_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $pendingCount = OrderPayment::where(function($q) {
            $q->where('is_verified', false)->orWhereNull('is_verified');
        })->where('notes', 'NOT LIKE', '%[DITOLAK FINANCE%')->count();

        $verifiedCount = OrderPayment::where('is_verified', true)->count();
        $rejectedCount = OrderPayment::where('notes', 'LIKE', '%[DITOLAK FINANCE%')->count();

        $payments = $query->paginate(15);

        // Prepopulate selectedTypes for rows if empty
        foreach ($payments as $pay) {
            if (!isset($this->selectedTypes[$pay->id])) {
                if ($pay->invoice && $pay->invoice->paid_amount == 0) {
                    $this->selectedTypes[$pay->id] = ($pay->amount_total >= $pay->invoice->total_amount) ? 'LUNAS_AWAL' : 'BEFORE';
                } else {
                    $this->selectedTypes[$pay->id] = $pay->type ?: 'AFTER';
                }
            }
        }

        return view('livewire.finance.payment-verification-index', [
            'payments'      => $payments,
            'pendingCount'  => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'rejectedCount' => $rejectedCount,
        ])->layout('layouts.app', ['title' => 'Verifikasi Pembayaran Customer — Finance']);
    }
}
