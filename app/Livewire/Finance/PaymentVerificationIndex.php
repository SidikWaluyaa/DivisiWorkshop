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
    
    // Dynamic Filter State
    public $dateRange = 'all'; // 'all', 'today', '7d', 'this_month', 'custom'
    public $startDate = null;
    public $endDate = null;
    public $filterBank = ''; // '', 'BCA', 'Mandiri', 'QRIS', 'Lainnya'
    public $filterType = ''; // '', 'BEFORE', 'AFTER', 'TAMBAH_JASA', 'LUNAS_AWAL', 'ONGKIR', 'OTO'
    public $sortBy = 'latest'; // 'latest', 'oldest', 'highest', 'lowest'

    // Payment Types selection per payment ID: [payment_id => 'BEFORE'|'AFTER'|...]
    public $selectedTypes = [];

    // Approval Modal State (for reviewing & editing details before confirming)
    public $approveModalOpen = false;
    public $approvingPayment = null;
    public $approvePaymentType = 'BEFORE';
    public $approveAmount = 0;
    public $approvePaidAt = '';

    // Reject Modal State
    public $rejectModalOpen = false;
    public $rejectPaymentId = null;
    public $rejectReason = '';

    // Delete Modal State
    public $deleteModalOpen = false;
    public $deletePaymentId = null;
    public $deletingPayment = null;

    protected $queryString = [
        'activeTab'   => ['except' => 'pending'],
        'search'      => ['except' => ''],
        'dateRange'   => ['except' => 'all'],
        'startDate'   => ['except' => null],
        'endDate'     => ['except' => null],
        'filterBank'  => ['except' => ''],
        'filterType'  => ['except' => ''],
        'sortBy'      => ['except' => 'latest'],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterBank() { $this->resetPage(); }
    public function updatingFilterType() { $this->resetPage(); }
    public function updatingSortBy() { $this->resetPage(); }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setDatePreset($preset)
    {
        $this->dateRange = $preset;
        if ($preset === 'today') {
            $this->startDate = date('Y-m-d');
            $this->endDate = date('Y-m-d');
        } elseif ($preset === '7d') {
            $this->startDate = now()->subDays(6)->format('Y-m-d');
            $this->endDate = date('Y-m-d');
        } elseif ($preset === 'this_month') {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
            $this->endDate = now()->endOfMonth()->format('Y-m-d');
        } elseif ($preset === 'all') {
            $this->startDate = null;
            $this->endDate = null;
        }
        $this->resetPage();
    }

    public function setCustomDates($start, $end)
    {
        $this->dateRange = 'custom';
        $this->startDate = $start;
        $this->endDate = $end;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->dateRange = 'all';
        $this->startDate = null;
        $this->endDate = null;
        $this->filterBank = '';
        $this->filterType = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function getActiveFilterCountProperty()
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if ($this->dateRange !== 'all') $count++;
        if (!empty($this->filterBank)) $count++;
        if (!empty($this->filterType)) $count++;
        if ($this->sortBy !== 'latest') $count++;
        return $count;
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
        $this->approveAmount = (float)$payment->amount_total;
        $this->approvePaidAt = $payment->paid_at ? $payment->paid_at->format('Y-m-d') : date('Y-m-d');

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
        $this->approveAmount = 0;
        $this->approvePaidAt = '';
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

    public function openDeleteModal($paymentId)
    {
        $this->deletePaymentId = $paymentId;
        $this->deletingPayment = OrderPayment::with(['invoice.customer'])->find($paymentId);
        $this->deleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->deletePaymentId = null;
        $this->deletingPayment = null;
        $this->deleteModalOpen = false;
    }

    public function confirmDeletePayment()
    {
        try {
            $payment = OrderPayment::with(['invoice.workOrders'])->findOrFail($this->deletePaymentId);

            // Delete proof file if exists
            if ($payment->proof_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($payment->proof_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->proof_image);
            }

            // Log on work orders
            if ($payment->invoice) {
                foreach ($payment->invoice->workOrders as $wo) {
                    $wo->logs()->create([
                        'user_id'     => Auth::id(),
                        'step'        => 'PAYMENT',
                        'action'      => 'PAYMENT_DELETED',
                        'description' => "Data bukti pembayaran Rp " . number_format($payment->amount_total, 0, ',', '.') . " (" . ($payment->notes ?? '') . ") telah dihapus permanen oleh " . (Auth::user()->name ?? 'Finance') . ".",
                    ]);
                }
            }

            $payment->delete();

            $this->closeDeleteModal();
            $this->dispatch('swal:toast', icon: 'success', title: 'Data bukti pembayaran berhasil dihapus permanen.');

        } catch (\Throwable $e) {
            Log::error("Delete Payment Error (#{$this->deletePaymentId}): " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function approvePaymentDirect($paymentId, $paymentType = null)
    {
        $type = $paymentType ?: ($this->selectedTypes[$paymentId] ?? 'BEFORE');
        $this->processApproval($paymentId, $type);
    }

    public function confirmApproveFromModal()
    {
        if (!$this->approvingPayment) return;

        $cleanAmount = preg_replace('/[^0-9]/', '', (string)$this->approveAmount);
        if (empty($cleanAmount) || (float)$cleanAmount <= 0) {
            $this->dispatch('swal:toast', icon: 'error', title: 'Nominal pembayaran harus lebih dari Rp 0.');
            return;
        }

        $this->processApproval(
            $this->approvingPayment->id, 
            $this->approvePaymentType, 
            (float)$cleanAmount, 
            $this->approvePaidAt
        );
        $this->closeApproveModal();
    }

    private function processApproval($paymentId, $paymentType, $overrideAmount = null, $overridePaidAt = null)
    {
        try {
            $payment = OrderPayment::with(['invoice.customer', 'invoice.workOrders'])->findOrFail($paymentId);

            if ($payment->is_verified) {
                $this->dispatch('swal:toast', icon: 'info', title: 'Pembayaran ini sudah diverifikasi sebelumnya.');
                return;
            }

            if ($overrideAmount !== null && (float)$overrideAmount > 0) {
                $payment->amount_total = (float)$overrideAmount;
                $payment->amount_service = (float)$overrideAmount;
            }

            if (!empty($overridePaidAt)) {
                $payment->paid_at = \Carbon\Carbon::parse($overridePaidAt)->setTime(now()->hour, now()->minute, now()->second);
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
        $query = OrderPayment::with(['invoice.customer', 'invoice.workOrders.workOrderServices.service', 'pic']);

        // Tab Filter
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

        // Live Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhere('amount_total', 'LIKE', "%" . preg_replace('/[^0-9]/', '', $search) . "%")
                  ->orWhereHas('invoice', function($iq) use ($search) {
                      $iq->where('invoice_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Bank / Payment Method Filter
        if ($this->filterBank) {
            $query->where('payment_method', $this->filterBank);
        }

        // Payment Type Filter
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        // Date Range Filter (Based on Tanggal Bayar: paid_at)
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('paid_at', [
                \Carbon\Carbon::parse($this->startDate)->startOfDay(),
                \Carbon\Carbon::parse($this->endDate)->endOfDay()
            ]);
        } elseif ($this->startDate) {
            $query->where('paid_at', '>=', \Carbon\Carbon::parse($this->startDate)->startOfDay());
        } elseif ($this->endDate) {
            $query->where('paid_at', '<=', \Carbon\Carbon::parse($this->endDate)->endOfDay());
        }

        // Sorting
        if ($this->sortBy === 'oldest') {
            $query->orderByRaw('DATE(paid_at) ASC')->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        } elseif ($this->sortBy === 'upload_latest') {
            $query->orderByDesc('created_at')->orderByDesc('id');
        } elseif ($this->sortBy === 'highest') {
            $query->orderByDesc('amount_total')->orderByDesc('id');
        } elseif ($this->sortBy === 'lowest') {
            $query->orderBy('amount_total', 'asc')->orderByDesc('id');
        } else {
            // Default latest: Order by Tanggal Bayar DESC, lalu Waktu Konfirmasi Masuk DESC, lalu ID DESC
            $query->orderByRaw('DATE(paid_at) DESC')->orderByDesc('created_at')->orderByDesc('id');
        }

        // Tab Counts (Independent of dynamic filters so tab badges remain accurate)
        $pendingCount = OrderPayment::where(function($q) {
            $q->where('is_verified', false)->orWhereNull('is_verified');
        })->where('notes', 'NOT LIKE', '%[DITOLAK FINANCE%')->count();

        $verifiedCount = OrderPayment::where('is_verified', true)->count();
        $rejectedCount = OrderPayment::where('notes', 'LIKE', '%[DITOLAK FINANCE%')->count();

        // Total Nominal of currently filtered items
        $filteredTotalAmount = (clone $query)->sum('amount_total');

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
            'payments'            => $payments,
            'pendingCount'        => $pendingCount,
            'verifiedCount'       => $verifiedCount,
            'rejectedCount'       => $rejectedCount,
            'filteredTotalAmount' => $filteredTotalAmount,
        ])->layout('layouts.app', ['title' => 'Verifikasi Pembayaran Customer — Finance']);
    }
}
