<?php

namespace App\Livewire\Gudang;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkshopManifest;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutboundReceiptIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $tab = 'pending'; // 'pending' (SENT) vs 'history' (RECEIVED)
    public ?int $expandedManifestId = null;

    protected $queryString = [
        'tab'    => ['except' => 'pending'],
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTab(): void
    {
        $this->resetPage();
        $this->expandedManifestId = null;
    }

    public function toggleManifestDetail(int $manifestId): void
    {
        $this->expandedManifestId = ($this->expandedManifestId === $manifestId) ? null : $manifestId;
    }

    public function confirmReceive(int $manifestId): void
    {
        $manifest = WorkshopManifest::with('workOrders')->find($manifestId);
        if (!$manifest) {
            $this->dispatch('swal:toast', icon: 'error', title: 'Manifest tidak ditemukan.');
            return;
        }

        if ($manifest->status !== 'SENT') {
            $this->dispatch('swal:toast', icon: 'info', title: 'Manifest ini sudah berstatus ' . $manifest->status);
            return;
        }

        DB::beginTransaction();
        try {
            $manifest->update([
                'status'      => 'RECEIVED',
                'receiver_id' => Auth::id(),
                'received_at' => now(),
            ]);

            $workflow = app(WorkflowService::class);
            $processedCount = 0;

            foreach ($manifest->workOrders as $order) {
                // Update SPK status to SELESAI
                $workflow->updateStatus(
                    $order, 
                    WorkOrderStatus::SELESAI, 
                    "Manifest Outbound #{$manifest->manifest_number} telah diterima fisik oleh Gudang Utama (" . (Auth::user()?->name ?? 'Admin Gudang') . ")."
                );
                
                $order->logs()->create([
                    'user_id'     => Auth::id(),
                    'step'        => 'OUTBOUND',
                    'action'      => 'OUTBOUND_MANIFEST_RECEIVED',
                    'description' => "Serah terima fisik Manifest Outbound #{$manifest->manifest_number} dikonfirmasi oleh Gudang Utama. Status SPK resmi SELESAI.",
                ]);

                $processedCount++;
            }

            DB::commit();

            $this->dispatch('swal:toast', 
                icon: 'success', 
                title: "Manifest #{$manifest->manifest_number} Berhasil Diterima Gudang!", 
                text: "{$processedCount} unit SPK kini resmi berstatus SELESAI di Gudang Utama."
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gudang Outbound Receipt Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal mengonfirmasi penerimaan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = WorkshopManifest::with([
            'dispatcher', 
            'receiver', 
            'workOrders.customer',
            'workOrders.workOrderServices.service'
        ])
        ->where('manifest_number', 'like', 'MNF-OUT-%');

        if ($this->tab === 'pending') {
            $query->where('status', 'SENT');
        } else {
            $query->where('status', 'RECEIVED');
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('manifest_number', 'like', $searchTerm)
                  ->orWhere('notes', 'like', $searchTerm)
                  ->orWhereHas('dispatcher', function ($dq) use ($searchTerm) {
                      $dq->where('name', 'like', $searchTerm);
                  })
                  ->orWhereHas('workOrders', function ($wq) use ($searchTerm) {
                      $wq->where('spk_number', 'like', $searchTerm)
                         ->orWhere('customer_name', 'like', $searchTerm)
                         ->orWhere('shoe_brand', 'like', $searchTerm);
                  });
            });
        }

        $manifests = $query->latest('dispatched_at')->paginate(10);

        // Stats summary counts
        $pendingCount = WorkshopManifest::where('manifest_number', 'like', 'MNF-OUT-%')->where('status', 'SENT')->count();
        $receivedTodayCount = WorkshopManifest::where('manifest_number', 'like', 'MNF-OUT-%')->where('status', 'RECEIVED')->whereDate('received_at', now())->count();
        $totalReceivedCount = WorkshopManifest::where('manifest_number', 'like', 'MNF-OUT-%')->where('status', 'RECEIVED')->count();

        return view('livewire.gudang.outbound-receipt-index', [
            'manifests'          => $manifests,
            'pendingCount'       => $pendingCount,
            'receivedTodayCount' => $receivedTodayCount,
            'totalReceivedCount' => $totalReceivedCount,
        ])->layout('layouts.app', ['title' => 'Penerimaan Outbound (QC ke Gudang)']);
    }
}
