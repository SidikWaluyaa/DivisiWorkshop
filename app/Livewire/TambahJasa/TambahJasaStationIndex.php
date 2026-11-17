<?php

namespace App\Livewire\TambahJasa;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderAdditionalService;
use App\Models\WorkOrderLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.workshop-pwa')]
class TambahJasaStationIndex extends Component
{
    use WithPagination;

    #[Url(except: 'antrean')]
    public $activeTab = 'antrean';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $technicianFilter = 'all';

    public $perPage = 12;

    // Detail SPK Modal state
    public $showDetailModal = false;
    public $detailWorkOrderId = null;

    public function openDetailModal($workOrderId)
    {
        $this->detailWorkOrderId = $workOrderId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailWorkOrderId = null;
    }

    #[Computed]
    public function detailWorkOrder()
    {
        if (!$this->detailWorkOrderId) {
            return null;
        }

        return WorkOrder::with([
            'photos',
            'workOrderServices.service',
            'additionalServices.solBy',
            'additionalServices.upperBy',
            'additionalServices.treatmentBy',
        ])->find($this->detailWorkOrderId);
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function techs()
    {
        $allTechs = User::where('role', 'technician')
            ->select('id', 'name', 'station', 'specialization')
            ->orderBy('name', 'asc')
            ->get();

        return [
            'upper'     => $allTechs->filter(fn($u) => $u->station === 'UPPER' || empty($u->station) || str_contains(strtolower($u->specialization ?? ''), 'upper')),
            'sol'       => $allTechs->filter(fn($u) => $u->station === 'SOLING' || empty($u->station) || str_contains(strtolower($u->specialization ?? ''), 'sol')),
            'treatment' => $allTechs->filter(fn($u) => $u->station === 'TREATMENT' || empty($u->station) || str_contains(strtolower($u->specialization ?? ''), 'clean') || str_contains(strtolower($u->specialization ?? ''), 'repaint')),
            'all'       => $allTechs,
        ];
    }

    #[Computed]
    public function counts()
    {
        $row = WorkOrderAdditionalService::selectRaw("
            COUNT(DISTINCT CASE WHEN status = 'IN_PROGRESS' THEN work_order_id END) as antrean,
            COUNT(DISTINCT CASE WHEN status = 'COMPLETED' THEN work_order_id END) as riwayat
        ")->first();

        return [
            'antrean' => (int) ($row->antrean ?? 0),
            'riwayat' => (int) ($row->riwayat ?? 0),
        ];
    }

    public function assignTechnician($id, $station, $userId)
    {
        $item = WorkOrderAdditionalService::find($id);
        if (!$item || !in_array($station, ['sol', 'upper', 'treatment'], true)) {
            return;
        }

        $colBy = "{$station}_by";
        $colStart = "{$station}_started_at";
        $val = $userId ?: null;

        $item->update([
            $colBy => $val,
            $colStart => $val ? ($item->$colStart ?? now()) : null,
        ]);

        unset($this->counts);
        session()->flash('success', "Teknisi untuk layanan '{$item->service_name}' berhasil diperbarui.");
    }

    public function markWorkOrderCompleted($workOrderId)
    {
        $activeItems = WorkOrderAdditionalService::where('work_order_id', $workOrderId)
            ->where('status', 'IN_PROGRESS')
            ->get();

        if ($activeItems->isEmpty()) {
            return;
        }

        // Validate that every active service item has at least one technician assigned
        $missingTech = $activeItems->filter(function ($item) {
            return empty($item->sol_by) && empty($item->upper_by) && empty($item->treatment_by);
        });

        if ($missingTech->isNotEmpty()) {
            $names = $missingTech->pluck('service_name')->implode(', ');
            session()->flash('error', "Harap pilih teknisi terlebih dahulu untuk layanan: {$names}");
            return;
        }

        $now = now();
        $userId = Auth::id() ?? 1;
        $spkNumber = $activeItems->first()->spk_number;

        DB::transaction(function () use ($activeItems, $now, $userId, $workOrderId) {
            foreach ($activeItems as $item) {
                $updates = [
                    'status' => 'COMPLETED',
                    'completed_at' => $now,
                    'completed_by' => $userId,
                ];

                foreach (['sol', 'upper', 'treatment'] as $station) {
                    if ($item->{"{$station}_by"} && !$item->{"{$station}_completed_at"}) {
                        $updates["{$station}_completed_at"] = $now;
                    }
                }

                $item->update($updates);
            }

            WorkOrderLog::create([
                'work_order_id' => $workOrderId,
                'user_id' => $userId,
                'step' => 'PRODUCTION',
                'action' => 'TAMBAH_JASA_COMPLETED',
                'description' => 'Tambah Jasa selesai dikerjakan: ' . $activeItems->pluck('service_name')->implode(', '),
            ]);
        });

        unset($this->counts);
        $this->dispatch('tambah-jasa-completed', spk: $spkNumber);
        session()->flash('success', "Seluruh Tambah Jasa untuk SPK {$spkNumber} berhasil ditandai SELESAI!");
    }

    public function render()
    {
        $targetStatus = $this->activeTab === 'antrean' ? 'IN_PROGRESS' : 'COMPLETED';
        $search = trim($this->search);
        $techId = $this->technicianFilter;

        $query = WorkOrder::query()
            ->whereHas('additionalServices', function ($q) use ($targetStatus, $search, $techId) {
                $q->where('status', $targetStatus);

                if ($search !== '') {
                    $q->where(function ($sq) use ($search) {
                        $sq->where('spk_number', 'like', "%{$search}%")
                           ->orWhere('customer_name', 'like', "%{$search}%")
                           ->orWhere('service_name', 'like', "%{$search}%");
                    });
                }

                if ($techId !== 'all') {
                    $q->where(function ($tq) use ($techId) {
                        $tq->where('sol_by', $techId)
                           ->orWhere('upper_by', $techId)
                           ->orWhere('treatment_by', $techId);
                    });
                }
            })
            ->with([
                'photos',
                'additionalServices' => function ($q) use ($targetStatus) {
                    $q->where('status', $targetStatus)
                      ->with(['creator', 'completer', 'solBy', 'upperBy', 'treatmentBy'])
                      ->orderBy('id', 'asc');
                },
            ])
            ->orderByDesc(
                WorkOrderAdditionalService::select('created_at')
                    ->whereColumn('work_order_id', 'work_orders.id')
                    ->where('status', $targetStatus)
                    ->latest('id')
                    ->limit(1)
            );

        return view('livewire.tambah-jasa.tambah-jasa-station-index', [
            'workOrders' => $query->paginate($this->perPage),
        ]);
    }
}
