<?php

namespace App\Livewire\Workshop;

use Livewire\Component;
use App\Models\SuratJalan;
use App\Models\SuratJalanItem;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;

class SuratJalanEditModal extends Component
{
    public $suratJalanId;
    public $showModal = false;

    // Editable fields
    public $catatan = '';
    public $dikirim_at = '';

    // Search for adding new SPK
    public $searchSpk = '';
    public $kondisi_serah_terima = 'Lengkap & Baik';

    public function mount($suratJalanId)
    {
        $this->suratJalanId = $suratJalanId;
    }

    public function openModal()
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Akses Terbatas',
                'text' => 'Hanya Super Administrator (admin@workshop.com) yang berhak mengedit muatan Surat Jalan saat pengiriman.'
            ]);
            return;
        }

        $suratJalan = SuratJalan::findOrFail($this->suratJalanId);
        $this->catatan = $suratJalan->catatan ?? '';
        $this->dikirim_at = $suratJalan->dikirim_at ? $suratJalan->dikirim_at->format('Y-m-d\TH:i') : '';
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

        $suratJalan = SuratJalan::findOrFail($this->suratJalanId);
        $suratJalan->update([
            'catatan' => $this->catatan,
            'dikirim_at' => $this->dikirim_at ? \Carbon\Carbon::parse($this->dikirim_at) : $suratJalan->dikirim_at,
        ]);

        ActivityLogger::log('Edit Metadata Surat Jalan', "Super Admin ({$user->name}) memperbarui info Surat Jalan #{$suratJalan->nomor_surat}.");

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Info Disimpan',
            'text' => 'Catatan & tanggal pengiriman Surat Jalan berhasil diperbarui!'
        ]);
    }

    public function removeSpk($itemId)
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $item = SuratJalanItem::with(['workOrder', 'suratJalan'])->findOrFail($itemId);
        $suratJalan = $item->suratJalan;
        $wo = $item->workOrder;

        DB::transaction(function () use ($item, $suratJalan, $wo, $user) {
            $spkNumber = $wo ? $wo->spk_number : "Item #{$item->id}";
            
            // Delete the item from Surat Jalan
            $item->delete();

            // Log activity
            if ($wo) {
                $wo->logs()->create([
                    'user_id' => $user->id,
                    'step' => 'SURAT_JALAN',
                    'action' => 'REMOVED_FROM_SURAT_JALAN',
                    'description' => "SPK dikeluarkan dari Surat Jalan #{$suratJalan->nomor_surat} oleh Super Admin (admin@workshop.com).",
                ]);
            }

            ActivityLogger::log('Hapus Item Surat Jalan', "Super Admin mengeluarkan SPK {$spkNumber} dari Surat Jalan #{$suratJalan->nomor_surat}.");
        });

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'SPK Dikeluarkan',
            'text' => 'Item SPK berhasil dikeluarkan dari daftar muatan Surat Jalan!'
        ]);
    }

    public function addSpk($workOrderId)
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $suratJalan = SuratJalan::findOrFail($this->suratJalanId);
        $wo = WorkOrder::findOrFail($workOrderId);

        // Check if already in this surat jalan
        $exists = SuratJalanItem::where('surat_jalan_id', $suratJalan->id)
            ->where('work_order_id', $wo->id)
            ->exists();

        if ($exists) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Sudah Ada',
                'text' => 'SPK ini sudah ada di dalam muatan Surat Jalan ini.'
            ]);
            return;
        }

        DB::transaction(function () use ($suratJalan, $wo, $user) {
            SuratJalanItem::create([
                'surat_jalan_id' => $suratJalan->id,
                'work_order_id' => $wo->id,
                'kondisi_serah_terima' => $this->kondisi_serah_terima,
            ]);

            $wo->logs()->create([
                'user_id' => $user->id,
                'step' => 'SURAT_JALAN',
                'action' => 'ADDED_TO_SURAT_JALAN',
                'description' => "SPK ditambahkan ke Surat Jalan #{$suratJalan->nomor_surat} oleh Super Admin (admin@workshop.com).",
            ]);

            ActivityLogger::log('Tambah Item Surat Jalan', "Super Admin memasukkan SPK {$wo->spk_number} ke Surat Jalan #{$suratJalan->nomor_surat}.");
        });

        $this->searchSpk = '';

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'SPK Ditambahkan',
            'text' => "SPK #{$wo->spk_number} berhasil ditambahkan ke muatan Surat Jalan!"
        ]);
    }

    public function deleteEmptySuratJalan()
    {
        $user = Auth::user();
        if (!$user || $user->email !== 'admin@workshop.com') {
            abort(403);
        }

        $suratJalan = SuratJalan::with('items')->findOrFail($this->suratJalanId);

        DB::transaction(function () use ($suratJalan, $user) {
            $suratJalan->items()->delete();
            $nomorSurat = $suratJalan->nomor_surat;
            $suratJalan->delete();

            ActivityLogger::log('Hapus Surat Jalan', "Super Admin ({$user->name}) menghapus/membatalkan Surat Jalan #{$nomorSurat}.");
        });

        session()->flash('success', "Surat Jalan #{$suratJalan->nomor_surat} berhasil dibatalkan dan dihapus.");
        return redirect()->route('surat-jalan.index');
    }

    public function render()
    {
        $suratJalan = SuratJalan::with([
            'pengirim',
            'penerima',
            'items.workOrder.services',
            'items.workOrder.materials',
            'items.workOrder.customer'
        ])->find($this->suratJalanId);

        $availableWorkOrders = collect();

        if ($this->showModal && !empty($this->searchSpk)) {
            $existingWoIds = $suratJalan ? $suratJalan->items->pluck('work_order_id')->filter() : collect();
            $search = trim($this->searchSpk);

            $availableWorkOrders = WorkOrder::whereNotIn('id', $existingWoIds)
                ->where(function($q) use ($search) {
                    $q->where('spk_number', 'LIKE', "%{$search}%")
                      ->orWhere('customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('shoe_brand', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.workshop.surat-jalan-edit-modal', [
            'suratJalan' => $suratJalan,
            'availableWorkOrders' => $availableWorkOrders,
        ]);
    }
}
