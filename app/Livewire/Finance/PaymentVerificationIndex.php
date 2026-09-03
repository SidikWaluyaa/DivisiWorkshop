<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
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

    public $activeTab = 'pending'; // 'pending', 'verified', 'all'
    public $search = '';
    public $selectedProofImage = null;
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

    public function approvePayment($paymentId)
    {
        try {
            $payment = OrderPayment::with(['invoice', 'workOrder'])->findOrFail($paymentId);

            if ($payment->is_verified) {
                $this->dispatch('swal:toast', icon: 'info', title: 'Pembayaran ini sudah diverifikasi sebelumnya.');
                return;
            }

            $payment->is_verified = true;
            $payment->pic_id = Auth::id();
            $payment->notes = ($payment->notes ? $payment->notes . ' ' : '') . '[Diverifikasi oleh ' . (Auth::user()->name ?? 'Finance') . ' pada ' . now()->format('d/m/Y H:i') . ']';
            $payment->save();

            // Update Invoice Balance & Status
            if ($payment->invoice) {
                $invoice = $payment->invoice;
                $invoice->paid_amount = (float)$invoice->paid_amount + (float)$payment->amount_total;

                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $invoice->status = 'Lunas';
                } elseif ($invoice->paid_amount > 0) {
                    $invoice->status = 'DP/Cicil';
                }
                $invoice->save();

                // Audit Log on work orders
                foreach ($invoice->workOrders as $wo) {
                    $wo->logs()->create([
                        'user_id'     => Auth::id(),
                        'step'        => 'PAYMENT',
                        'action'      => 'PAYMENT_VERIFIED',
                        'description' => "Pembayaran Rp " . number_format($payment->amount_total, 0, ',', '.') . " via {$payment->payment_method} telah diverifikasi oleh " . (Auth::user()->name ?? 'Finance') . ". Status Invoice: {$invoice->status}.",
                    ]);
                }
            }

            $this->dispatch('swal:toast', icon: 'success', title: "Pembayaran Rp " . number_format($payment->amount_total, 0, ',', '.') . " Berhasil Diverifikasi!");

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
        $query = OrderPayment::with(['invoice.customer', 'invoice.workOrders', 'pic'])
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

        return view('livewire.finance.payment-verification-index', [
            'payments'      => $query->paginate(15),
            'pendingCount'  => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'rejectedCount' => $rejectedCount,
        ])->layout('layouts.app', ['title' => 'Verifikasi Pembayaran Customer — Finance']);
    }
}
