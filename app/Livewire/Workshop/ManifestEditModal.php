<?php

namespace App\Livewire\Workshop;

use Livewire\Component;
use App\Models\WorkshopManifest;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;

class ManifestEditModal extends Component
{
    public $manifestId;
    public $showModal = false;

    // Editable fields
    public $notes = '';
    public $dispatched_at = '';

    // Search for adding new SPK
    public $searchSpk = '';

    public function mount($manifestId)
    {
        $this->manifestId = $manifestId;
    }

    public function openModal()
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Akses Terbatas',
                'text' => 'Hanya Super Administrator (admin@workshop.com) yang berhak mengedit muatan Manifest saat pengiriman.'
            ]);
            return;
        }

        $manifest = WorkshopManifest::findOrFail($this->manifestId);
        $this->notes = $manifest->notes ?? '';
        $this->dispatched_at = $manifest->dispatched_at ? $manifest->dispatched_at->format('Y-m-d\TH:i') : '';
        $this->searchSpk = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->dispatch('refreshParent');
    }

    public function saveMetadata()
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $manifest = WorkshopManifest::findOrFail($this->manifestId);
        $manifest->update([
            'notes' => $this->notes,
            'dispatched_at' => $this->dispatched_at ? \Carbon\Carbon::parse($this->dispatched_at) : $manifest->dispatched_at,
        ]);

        ActivityLogger::log('Edit Metadata Manifest', "Super Admin ({$user->name}) memperbarui info Manifest #{$manifest->manifest_number}.");

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Info Disimpan',
            'text' => 'Catatan & tanggal pengiriman Manifest berhasil diperbarui!'
        ]);
    }

    public function removeSpk($workOrderId)
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $manifest = WorkshopManifest::findOrFail($this->manifestId);
        $wo = WorkOrder::findOrFail($workOrderId);

        $isOutbound = str_starts_with($manifest->manifest_number, 'MNF-OUT-');

        DB::transaction(function () use ($manifest, $wo, $user, $isOutbound) {
            if ($isOutbound) {
                $wo->update([
                    'workshop_manifest_id' => null,
                    'current_location' => 'QC Workshop (Staging Outbound)',
                    'status' => WorkOrderStatus::STAGING_OUTBOUND,
                ]);
            } else {
                $wo->update([
                    'workshop_manifest_id' => null,
                    'status' => WorkOrderStatus::READY_TO_DISPATCH,
                ]);
            }

            $wo->logs()->create([
                'user_id' => $user->id,
                'step' => 'LOGISTICS',
                'action' => 'REMOVED_FROM_MANIFEST',
                'description' => "SPK dikeluarkan dari Manifest #{$manifest->manifest_number} dan dikembalikan ke antrian siap kirim oleh Super Admin.",
            ]);

            ActivityLogger::log('Hapus Item Manifest', "Super Admin mengeluarkan SPK {$wo->spk_number} dari Manifest #{$manifest->manifest_number}.");
        });

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'SPK Dikeluarkan & Siap Dikirim Ulang',
            'text' => "SPK #{$wo->spk_number} berhasil dikeluarkan dan otomatis kembali ke daftar antrian pengiriman!"
        ]);
    }

    public function addSpk($workOrderId)
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $manifest = WorkshopManifest::findOrFail($this->manifestId);
        $wo = WorkOrder::findOrFail($workOrderId);

        $isOutbound = str_starts_with($manifest->manifest_number, 'MNF-OUT-');

        DB::transaction(function () use ($manifest, $wo, $user, $isOutbound) {
            if ($isOutbound) {
                $wo->update([
                    'workshop_manifest_id' => $manifest->id,
                    'current_location' => 'Dalam Pengiriman (Outbound)',
                ]);
            } else {
                $wo->update([
                    'workshop_manifest_id' => $manifest->id,
                    'status' => WorkOrderStatus::OTW_WORKSHOP,
                ]);
            }

            $wo->logs()->create([
                'user_id' => $user->id,
                'step' => 'LOGISTICS',
                'action' => 'ADDED_TO_MANIFEST',
                'description' => "SPK ditambahkan ke Manifest #{$manifest->manifest_number} oleh Super Admin (admin@workshop.com).",
            ]);

            ActivityLogger::log('Tambah Item Manifest', "Super Admin memasukkan SPK {$wo->spk_number} ke Manifest #{$manifest->manifest_number}.");
        });

        $this->searchSpk = '';

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'SPK Ditambahkan',
            'text' => "SPK #{$wo->spk_number} berhasil dimasukkan ke dalam Manifest!"
        ]);
    }

    public function deleteEmptyManifest()
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $manifest = WorkshopManifest::with('workOrders')->findOrFail($this->manifestId);
        $isOutbound = str_starts_with($manifest->manifest_number, 'MNF-OUT-');

        DB::transaction(function () use ($manifest, $user, $isOutbound) {
            foreach ($manifest->workOrders as $wo) {
                if ($isOutbound) {
                    $wo->update([
                        'workshop_manifest_id' => null,
                        'current_location' => 'QC Workshop (Staging Outbound)',
                        'status' => WorkOrderStatus::STAGING_OUTBOUND,
                    ]);
                } else {
                    $wo->update([
                        'workshop_manifest_id' => null,
                        'status' => WorkOrderStatus::READY_TO_DISPATCH,
                    ]);
                }
            }

            $manifestNumber = $manifest->manifest_number;
            $manifest->delete();

            ActivityLogger::log('Hapus Manifest', "Super Admin ({$user->name}) menghapus/membatalkan Manifest #{$manifestNumber}.");
        });

        session()->flash('success', "Manifest #{$manifest->manifest_number} berhasil dibatalkan dan dihapus.");
        return redirect()->route($isOutbound ? 'qc.outbound' : 'manifest.index');
    }

    public function render()
    {
        $manifest = WorkshopManifest::with([
            'dispatcher',
            'receiver',
            'workOrders.services',
            'workOrders.customer'
        ])->find($this->manifestId);

        $availableWorkOrders = collect();

        if ($this->showModal && !empty($this->searchSpk)) {
            $search = trim($this->searchSpk);

            $availableWorkOrders = WorkOrder::whereNull('workshop_manifest_id')
                ->where(function($q) use ($search) {
                    $q->where('spk_number', 'LIKE', "%{$search}%")
                      ->orWhere('customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('shoe_brand', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.workshop.manifest-edit-modal', [
            'manifest' => $manifest,
            'availableWorkOrders' => $availableWorkOrders,
        ]);
    }
}
