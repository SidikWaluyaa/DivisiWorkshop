<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WarrantyClaim;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarrantyClaimsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'PENDING'; // PENDING, APPROVED, REJECTED, ALL
    public $selectedClaimId = null;
    
    // Rejection state
    public $rejection_reason = '';
    public $selectedRejectionReasonType = 'Masa Garansi Habis';
    public $customRejectionNote = '';
    public $showRejectModal = false;

    // Date Filter State
    public $dateStart = '';
    public $dateEnd = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'PENDING'],
        'dateStart' => ['except' => ''],
        'dateEnd' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
        $this->selectedClaimId = null;
    }



    public function updatingDateStart()
    {
        $this->resetPage();
    }

    public function updatingDateEnd()
    {
        $this->resetPage();
    }

    /**
     * Select a claim to view in details
     */
    public function selectClaim($id)
    {
        $this->selectedClaimId = $id;
        $this->reset(['rejection_reason', 'customRejectionNote', 'selectedRejectionReasonType', 'showRejectModal']);
    }

    /**
     * Action: Approve Claim & Auto-Generate Rework SPK
     */
    public function approveClaim($claimId)
    {
        $claim = WarrantyClaim::find($claimId);
        if (!$claim || $claim->status !== 'PENDING') {
            session()->flash('error', 'Klaim tidak valid atau sudah diproses.');
            return;
        }

        $originalWo = $claim->workOrder;
        if (!$originalWo) {
            session()->flash('error', 'Work Order orisinal tidak ditemukan.');
            return;
        }

        try {
            DB::transaction(function() use ($claim, $originalWo) {
                // 1. Generate new rework SPK number with suffix -GR (e.g. SPK-2026-0001-GR)
                $parts = explode('-', $originalWo->spk_number);
                if (count($parts) > 1) {
                    array_pop($parts);
                    $parts[] = 'GR';
                    $garansiSpk = implode('-', $parts);
                } else {
                    $garansiSpk = $originalWo->spk_number . '-GR';
                }

                // Add increment if SPK exists
                $baseSpk = $garansiSpk;
                $counter = 1;
                while (
                    WorkOrderWarranty::where('garansi_spk_number', $garansiSpk)->exists() ||
                    WorkOrder::where('spk_number', $garansiSpk)->exists()
                ) {
                    $garansiSpk = $baseSpk . '-' . $counter;
                    $counter++;
                }

                // 2. Create the Rework WorkOrder in the production queue (taken from original)
                $reworkWo = WorkOrder::create([
                    'spk_number' => $garansiSpk,
                    'customer_name' => $originalWo->customer_name,
                    'customer_phone' => $originalWo->customer_phone,
                    'customer_email' => $originalWo->customer_email,
                    'customer_address' => $originalWo->customer_address,
                    'shoe_brand' => $originalWo->shoe_brand,
                    'shoe_type' => $originalWo->shoe_type,
                    'shoe_color' => $originalWo->shoe_color,
                    'shoe_size' => $originalWo->shoe_size,
                    'category' => $originalWo->category,
                    'category_spk' => $originalWo->category_spk,
                    'status' => WorkOrderStatus::SELESAI->value, // Complete state for rework trigger in FINISH
                    'is_warranty' => true,
                    'parent_id' => $originalWo->id,
                    'notes' => 'GARANSI MANDIRI (DISETUJUI CX) DARI SPK: ' . $originalWo->spk_number . '. Keluhan: ' . $claim->problem_description,
                    'total_transaksi' => 0,
                    'status_pembayaran' => 'L', // Paid / Rp0
                    'created_by' => Auth::id(),
                    'entry_date' => now(),
                ]);

                // 3. Create the WorkOrderWarranty record
                WorkOrderWarranty::create([
                    'work_order_id' => $originalWo->id,
                    'garansi_spk_number' => $garansiSpk,
                    'description' => $claim->problem_description,
                    'photos' => [$claim->problem_photo], // Hold the uploaded problem photo
                    'status' => 'OPEN',
                    'created_by' => Auth::id()
                ]);

                // 4. Update the claim status
                $claim->update([
                    'status' => 'APPROVED',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            });

            session()->flash('success', 'Klaim Garansi disetujui! SPK Rework baru berhasil dibuat.');
            $this->selectedClaimId = null;

        } catch (\Exception $e) {
            Log::error('Error approving warranty claim: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan sistem saat menyetujui klaim.');
        }
    }

    /**
     * Trigger Rejection Modal
     */
    public function openRejectModal()
    {
        $this->showRejectModal = true;
    }

    /**
     * Action: Reject Claim with Reasons
     */
    public function rejectClaim()
    {
        $claim = WarrantyClaim::find($this->selectedClaimId);
        if (!$claim || $claim->status !== 'PENDING') {
            session()->flash('error', 'Klaim tidak valid.');
            return;
        }

        // Formulate rejection text
        $finalReason = $this->selectedRejectionReasonType;
        if (!empty(trim($this->customRejectionNote))) {
            $finalReason .= ' — ' . trim($this->customRejectionNote);
        }

        try {
            $claim->update([
                'status' => 'REJECTED',
                'rejection_reason' => $finalReason,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            session()->flash('success', 'Klaim Garansi berhasil ditolak.');
            $this->showRejectModal = false;
            $this->selectedClaimId = null;

        } catch (\Exception $e) {
            Log::error('Error rejecting warranty claim: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memproses penolakan.');
        }
    }

    /**
     * Get computed property for the currently selected claim
     */
    public function getSelectedClaimProperty()
    {
        if ($this->selectedClaimId) {
            return WarrantyClaim::with([
                'workOrder.workOrderServices.service', 
                'workOrder.workOrderServices.technician',
                'workOrder.photos',
                'processor'
            ])->find($this->selectedClaimId);
        }
        return null;
    }

    public function render()
    {
        $query = WarrantyClaim::with(['workOrder', 'processor'])->latest();

        // Apply Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply Status Filter
        if ($this->statusFilter !== 'ALL') {
            $query->where('status', $this->statusFilter);
        }

        // Apply Date Range Filter
        if ($this->dateStart && $this->dateEnd) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($this->dateStart)->startOfDay(),
                \Carbon\Carbon::parse($this->dateEnd)->endOfDay()
            ]);
        }

        // Count pending for notification badge
        $pendingCount = WarrantyClaim::where('status', 'PENDING')->count();

        return view('livewire.cx.warranty-claims-index', [
            'claims' => $query->paginate(10),
            'pendingCount' => $pendingCount,
            'selectedClaim' => $this->selectedClaim,
        ])->layout('layouts.app');
    }
}
