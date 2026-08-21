<?php

namespace App\Livewire\Qc;

use App\Models\WorkOrder;
use App\Models\WorkshopManifest;
use App\Enums\WorkOrderStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutboundCreate extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $priority = 'all';

    public $selectedItems = [];
    public $selectAll = false;
    public $manifestNotes = '';
    public $courierName = '';

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->stagingOrders->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $count = $this->stagingOrders->count();
        $this->selectAll = count($this->selectedItems) === $count && $count > 0;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPriority()
    {
        $this->resetPage();
    }

    #[Computed]
    public function stagingOrders()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices.service', 'cxIssues', 'photos', 'revisions', 'workshopManifest'])
            ->where('status', WorkOrderStatus::STAGING_OUTBOUND)
            ->where(function($q) {
                $q->whereNull('workshop_manifest_id')
                  ->orWhereHas('workshopManifest', function($mq) {
                      $mq->where('manifest_number', 'not like', 'MNF-OUT-%');
                  });
            });

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->priority !== 'all') {
            if ($this->priority === 'urgent') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express', 'OTO']);
            } else {
                $query->where('priority', 'Regular');
            }
        }

        $query->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express', 'OTO') THEN 0 ELSE 1 END");
        $query->orderBy('updated_at', 'desc');

        return $query->get();
    }

    public function generateManifest()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih setidaknya 1 SPK untuk diterbitkan Manifest Outbound.');
            return;
        }

        try {
            DB::beginTransaction();

            $datePrefix = date('Ymd');
            $randomSuffix = strtoupper(substr(uniqid(), -4));
            $manifestNumber = "MNF-OUT-{$datePrefix}-{$randomSuffix}";

            $manifest = WorkshopManifest::create([
                'manifest_number'  => $manifestNumber,
                'dispatched_by'    => Auth::id(),
                'status'           => 'SENT',
                'notes'            => $this->manifestNotes ?: 'Manifest Outbound dari QC ke Gudang Utama.',
                'total_items'      => count($this->selectedItems),
                'courier_name'     => $this->courierName ?: 'Kurir Internal Workshop',
                'dispatched_at'    => now(),
            ]);

            foreach ($this->selectedItems as $woId) {
                $workOrder = WorkOrder::find($woId);
                if ($workOrder) {
                    $workOrder->update([
                        'workshop_manifest_id' => $manifest->id,
                        'current_location'     => 'Dalam Pengiriman (Outbound)',
                    ]);

                    $workOrder->logs()->create([
                        'step'        => 'OUTBOUND_QC',
                        'action'      => 'MANIFEST_GENERATED',
                        'user_id'     => Auth::id(),
                        'description' => "Diterbitkan dalam Manifest Outbound #{$manifestNumber} oleh " . Auth::user()->name,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', "Surat Jalan Manifest Outbound #{$manifestNumber} berhasil diterbitkan!");
            return redirect()->route('qc.outbound.show', $manifest->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate outbound manifest: " . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Gagal menerbitkan Manifest: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.qc.outbound-create')->layout('layouts.workshop-pwa');
    }
}
