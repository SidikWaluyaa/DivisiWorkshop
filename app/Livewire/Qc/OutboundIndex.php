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

class OutboundIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $priority = 'all';

    #[Url(except: 'desc')]
    public $sort = 'desc';

    public $selectedItems = [];
    public $selectAll = false;
    public $manifestNotes = '';
    public $activeSection = 'staging'; // 'staging' or 'history'
    public $expandedManifestId = null;

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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }

    public function toggleManifestDetail($manifestId)
    {
        $this->expandedManifestId = $this->expandedManifestId === $manifestId ? null : $manifestId;
    }

    #[Computed]
    public function stagingOrders()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices', 'cxIssues', 'photos', 'revisions'])
            ->where('status', WorkOrderStatus::STAGING_OUTBOUND)
            ->whereNull('workshop_manifest_id');

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
        $query->orderBy('updated_at', $this->sort === 'desc' ? 'desc' : 'asc');

        return $query->get();
    }

    #[Computed]
    public function outboundManifests()
    {
        return WorkshopManifest::with(['dispatcher', 'receiver', 'workOrders.customer'])
            ->where('manifest_number', 'like', 'MNF-OUT-%')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function createManifest()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih minimal satu SPK untuk dibuatkan Manifest Outbound.');
            return;
        }

        DB::beginTransaction();
        try {
            // Generate Manifest Number (MNF-OUT-YYYYMMDD-XXXX)
            $todayStr = now()->format('Ymd');
            $prefix = "MNF-OUT-{$todayStr}-";
            $lastManifest = WorkshopManifest::where('manifest_number', 'like', "{$prefix}%")
                ->withTrashed()
                ->orderBy('id', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastManifest) {
                $lastSeq = (int) substr($lastManifest->manifest_number, -4);
                $nextSeq = $lastSeq + 1;
            }

            $manifestNumber = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // 1. Create Manifest
            $manifest = WorkshopManifest::create([
                'manifest_number' => $manifestNumber,
                'dispatcher_id'   => Auth::id(),
                'status'          => 'SENT',
                'notes'           => $this->manifestNotes ?: 'Manifest Outbound dari QC Akhir ke Gudang / Finished Store',
                'dispatched_at'   => now(),
            ]);

            // 2. Process Selected Orders
            $processedCount = 0;

            foreach ($this->selectedItems as $orderId) {
                $order = WorkOrder::find($orderId);
                if (!$order || $order->status !== WorkOrderStatus::STAGING_OUTBOUND) {
                    continue;
                }

                $order->workshop_manifest_id = $manifest->id;
                $order->save();

                // Log audit trail
                $order->logs()->create([
                    'user_id'     => Auth::id(),
                    'step'        => 'OUTBOUND',
                    'action'      => 'OUTBOUND_MANIFEST_CREATED',
                    'description' => "SPK dimasukkan ke Manifest Outbound #{$manifestNumber} oleh " . (Auth::user()?->name ?? 'Admin Workshop') . ". Menunggu konfirmasi penerimaan Gudang.",
                ]);

                $processedCount++;
            }

            DB::commit();

            $this->selectedItems = [];
            $this->selectAll = false;
            $this->manifestNotes = '';
            unset($this->stagingOrders);
            unset($this->outboundManifests);

            $this->dispatch('swal:toast', icon: 'success', title: "Manifest Outbound #{$manifestNumber} berhasil dibuat dengan {$processedCount} SPK (Menunggu konfirmasi Gudang)!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create Outbound Manifest Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal membuat Manifest: ' . $e->getMessage());
        }
    }

    public function receiveManifest($manifestId)
    {
        $manifest = WorkshopManifest::with('workOrders')->find($manifestId);
        if (!$manifest) {
            $this->dispatch('swal:toast', icon: 'error', title: 'Manifest tidak ditemukan.');
            return;
        }

        if ($manifest->status !== 'SENT') {
            $this->dispatch('swal:toast', icon: 'info', title: 'Manifest ini sudah diterima atau berstatus ' . $manifest->status);
            return;
        }

        DB::beginTransaction();
        try {
            $manifest->update([
                'status'      => 'RECEIVED',
                'receiver_id' => Auth::id(),
                'received_at' => now(),
            ]);

            $workflow = app(\App\Services\WorkflowService::class);
            $processedCount = 0;

            foreach ($manifest->workOrders as $order) {
                // Update status to SELESAI
                $workflow->updateStatus($order, WorkOrderStatus::SELESAI, "Manifest Outbound #{$manifest->manifest_number} telah diterima oleh Gudang (" . (Auth::user()?->name ?? 'Admin Gudang') . "). Status SPK resmi SELESAI.");
                
                $order->logs()->create([
                    'user_id'     => Auth::id(),
                    'step'        => 'OUTBOUND',
                    'action'      => 'OUTBOUND_MANIFEST_RECEIVED',
                    'description' => "Manifest Outbound #{$manifest->manifest_number} diterima oleh Gudang. Status SPK resmi SELESAI.",
                ]);

                $processedCount++;
            }

            DB::commit();

            unset($this->outboundManifests);
            unset($this->stagingOrders);

            $this->dispatch('swal:toast', icon: 'success', title: "Manifest #{$manifest->manifest_number} berhasil diterima Gudang! {$processedCount} SPK resmi SELESAI.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Receive Outbound Manifest Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal menerima Manifest: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.qc.outbound-index', [
            'stagingOrders' => $this->stagingOrders,
            'manifests'     => $this->outboundManifests,
        ])->layout('layouts.workshop-pwa');
    }
}
