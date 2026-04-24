<?php

namespace App\Livewire\Production;

use App\Models\WorkOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Log;

class LateInfo extends Component
{
    use WithPagination;
    use WithFileUploads;

    #[Url(except: '')]
    public $status = '';

    #[Url(except: '')]
    public $search = '';

    public $photos = []; // For file uploads

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updateDescription($id, $description)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->late_description = $description;
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Deskripsi diperbarui');
        }
    }

    public function updateNewEstimationDate($id, $date)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->new_estimation_date = $date ?: null;
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Estimasi diperbarui');
        }
    }

    public function updateMaterialArrivalDate($id, $date)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->material_arrival_date = $date ?: null;
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Tanggal material diperbarui');
        }
    }

    public function updateMaterialName($id, $name)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->material_name = $name;
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Nama material diperbarui');
        }
    }

    public function updatedPhotos($value, $key)
    {
        $order = WorkOrder::find($key);
        if ($order && $value) {
            try {
                if ($order->material_photo_path) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $order->material_photo_path));
                }

                $path = $value->store('material_photos', 'public');
                $order->material_photo_path = 'storage/' . $path;
                $order->save();

                $this->dispatch('swal:toast', icon: 'success', title: 'Foto berhasil diunggah');
            } catch (\Exception $e) {
                Log::error('Upload material photo error: ' . $e->getMessage());
                $this->dispatch('swal:toast', icon: 'error', title: 'Gagal mengunggah foto');
            }
            
            // Clear the temporary file from the array so it doesn't take up memory
            unset($this->photos[$key]);
        }
    }

    public function deletePhoto($id)
    {
        $order = WorkOrder::find($id);
        if ($order && $order->material_photo_path) {
            Storage::disk('public')->delete(str_replace('storage/', '', $order->material_photo_path));
            $order->material_photo_path = null;
            $order->save();

            $this->dispatch('swal:toast', icon: 'success', title: 'Foto dihapus');
        }
    }

    public function render()
    {
        $query = WorkOrder::productionLate();
        
        // Status Filter
        if (!empty($this->status)) {
            $statusStr = strtoupper($this->status);
            if (in_array($statusStr, ['LATE', 'WARNING', 'ON TRACK'])) {
                $query->having('warning_status', '=', $statusStr);
            }
        }

        // Search Filter
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->paginate(50);

        return view('livewire.production.late-info', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}
