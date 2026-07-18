<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ServiceBatchEdit extends Component
{
    public $services = [];
    public $toDelete = [];

    protected $rules = [
        'services.*.name' => 'required|string|max:255',
        'services.*.category' => 'required|string|max:255',
        'services.*.price' => 'required|numeric|min:0',
        'services.*.duration_minutes' => 'required|integer|min:0',
        'services.*.hk_days' => 'nullable|integer|min:0',
        'services.*.allow_fast_track' => 'required|in:yes,no',
        'services.*.description' => 'nullable|string',
    ];

    protected $validationAttributes = [
        'services.*.name' => 'Nama Layanan',
        'services.*.category' => 'Kategori',
        'services.*.price' => 'Harga',
        'services.*.duration_minutes' => 'Durasi',
        'services.*.hk_days' => 'Hari Kerja',
        'services.*.allow_fast_track' => 'Fast Track',
        'services.*.description' => 'Deskripsi',
    ];

    public function mount()
    {
        $this->services = Service::orderBy('category', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'category' => $service->category,
                    'price' => $service->price,
                    'duration_minutes' => $service->duration_minutes,
                    'hk_days' => $service->hk_days ?? 0,
                    'allow_fast_track' => $service->allow_fast_track ?? 'no',
                    'description' => $service->description ?? '',
                    'is_new' => false,
                ];
            })
            ->toArray();
    }

    public function addRow()
    {
        $this->services[] = [
            'id' => 'new_' . uniqid(),
            'name' => '',
            'category' => '',
            'price' => 0,
            'duration_minutes' => 0,
            'hk_days' => 0,
            'allow_fast_track' => 'no',
            'description' => '',
            'is_new' => true,
        ];
    }

    public function removeRow($index)
    {
        $service = $this->services[$index];
        
        if (!$service['is_new']) {
            $protectedNames = ['custom', 'custom service', 'custom services', 'lainnya', 'other'];
            if (in_array(strtolower($service['name']), $protectedNames)) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Layanan sistem ini diproteksi (tidak dapat dihapus).']);
                return;
            }
            $this->toDelete[] = $service['id'];
        }

        unset($this->services[$index]);
        $this->services = array_values($this->services);
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // 1. Process Deletions
            if (!empty($this->toDelete)) {
                Service::whereIn('id', $this->toDelete)->delete();
            }

            // 2. Process Saves (Insert & Update)
            foreach ($this->services as $sData) {
                $data = [
                    'name' => $sData['name'],
                    'category' => $sData['category'],
                    'price' => $sData['price'],
                    'duration_minutes' => $sData['duration_minutes'],
                    'hk_days' => $sData['hk_days'] ?: null,
                    'allow_fast_track' => $sData['allow_fast_track'],
                    'description' => $sData['description'] ?: null,
                ];

                if ($sData['is_new']) {
                    Service::create($data);
                } else {
                    $service = Service::find($sData['id']);
                    if ($service) {
                        $service->update($data);
                    }
                }
            }

            DB::commit();

            session()->flash('success', 'Semua perubahan layanan berhasil disimpan massal.');
            return redirect()->route('admin.services.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan perubahan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.admin.service-batch-edit')
            ->layout('layouts.app', ['header' => 'Batch Edit Layanan (Excel Mode)']);
    }
}
