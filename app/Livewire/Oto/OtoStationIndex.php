<?php

namespace App\Livewire\Oto;

use App\Models\OTO;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.workshop-pwa')]
class OtoStationIndex extends Component
{
    use WithPagination;

    #[Url(except: 'antrean')]
    public $activeTab = 'antrean';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $technicianFilter = 'all';

    #[Url(except: 'all')]
    public $serviceFilter = 'all';

    #[Url(except: 'asc')]
    public $sort = 'asc';

    public $perPage = 10;
    public $selectedItems = [];
    public $selectAll = false;

    public function updatedActiveTab()
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->otos->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedItems = [];
        }
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
        $baseActiveQuery = OTO::whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
            ->whereHas('workOrder', fn($q) => $q->whereNull('taken_date'));
        $antreanCount = (clone $baseActiveQuery)->count();
        $completedCount = OTO::where(function($q) {
            $q->where('status', 'COMPLETED')
              ->orWhereHas('workOrder', fn($wq) => $wq->whereNotNull('taken_date'));
        })->count();

        // Financial Calculation Helper
        $parsePrice = fn($str) => (int) preg_replace('/[^0-9]/', '', (string) $str);
        $activeOtos = (clone $baseActiveQuery)->get();
        $totalPotentialRevenue = $activeOtos->sum(fn($o) => $parsePrice($o->total_oto_price));

        $completedTodayCount = OTO::where('status', 'COMPLETED')
            ->whereDate('completed_at', now()->toDateString())
            ->count();

        return [
            'antrean'            => $antreanCount,
            'completed'          => $completedCount,
            'completed_today'    => $completedTodayCount,
            'total_active'       => $antreanCount,
            'potential_revenue'  => $totalPotentialRevenue,
        ];
    }

    #[Computed]
    public function otos()
    {
        $query = OTO::query()
            ->with([
                'workOrder.customer',
                'workOrder.workOrderServices',
                'workOrder.photos',
                'workOrder.invoice',
                'creator',
                'otoSolBy',
                'otoUpperBy',
                'otoTreatmentBy',
                'otoCompletedBy'
            ]);

        // Tab Filter: 2 Tabs (Antrean & Selesai)
        if ($this->activeTab === 'completed') {
            $query->where(function($q) {
                $q->where('status', 'COMPLETED')
                  ->orWhereHas('workOrder', fn($wq) => $wq->whereNotNull('taken_date'));
            });
        } else {
            $query->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
                  ->whereHas('workOrder', fn($wq) => $wq->whereNull('taken_date'));
        }

        // Search Filter
        if (!empty(trim($this->search))) {
            $term = trim($this->search);
            $query->where(function($q) use ($term) {
                $q->where('spk_number', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%")
                  ->orWhere('proposed_services', 'like', "%{$term}%")
                  ->orWhereHas('workOrder', function($wq) use ($term) {
                      $wq->where('shoe_brand', 'like', "%{$term}%")
                         ->orWhere('spk_number', 'like', "%{$term}%");
                  });
            });
        }

        // Technician Filter
        if ($this->technicianFilter !== 'all') {
            $techId = $this->technicianFilter;
            $query->where(function($q) use ($techId) {
                $q->where('oto_sol_by', $techId)
                  ->orWhere('oto_upper_by', $techId)
                  ->orWhere('oto_treatment_by', $techId);
            });
        }

        // Service Filter
        if ($this->serviceFilter !== 'all') {
            $query->where('proposed_services', 'like', "%{$this->serviceFilter}%");
        }

        // Sorting
        if ($this->activeTab === 'completed') {
            $query->latest('completed_at');
        } else {
            if ($this->sort === 'desc') {
                $query->latest('id');
            } else {
                $query->oldest('id');
            }
        }

        return $query->paginate($this->perPage);
    }

    public function updateStationTechnician($otoId, $stationType, $techId)
    {
        $oto = OTO::find($otoId);
        if (!$oto) return;

        try {
            $column = "oto_{$stationType}_by";
            $oldTechId = $oto->{$column};
            $oldTechName = $oldTechId ? User::find($oldTechId)?->name : 'Kosong';

            $oto->{$column} = $techId ? (int) $techId : null;
            if ($oto->status === 'ACCEPTED') {
                $oto->status = 'IN_PROGRESS';
            }
            $oto->save();

            $techName = $techId ? User::find($techId)?->name : 'Dihapus';
            $stationLabel = strtoupper($stationType);

            // Audit Trail
            WorkOrderLog::create([
                'work_order_id' => $oto->work_order_id,
                'user_id'       => Auth::id(),
                'step'          => 'OTO',
                'action'        => 'OTO_TECH_UPDATED',
                'description'   => "Teknisi OTO ({$stationLabel}) diubah dari [{$oldTechName}] ke [{$techName}].",
            ]);

            $this->dispatch('swal:toast', icon: 'success', title: "Teknisi OTO {$stationLabel} diubah ke {$techName}");
        } catch (\Throwable $e) {
            Log::error("OTO Tech Update Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function startStationTask($otoId, $stationType, $techId = null)
    {
        $oto = OTO::find($otoId);
        if (!$oto) return;

        try {
            $startedCol = "oto_{$stationType}_started_at";
            $byCol = "oto_{$stationType}_by";

            if ($techId && !$oto->{$byCol}) {
                $oto->{$byCol} = (int) $techId;
            }

            $oto->{$startedCol} = now();
            if ($oto->status === 'ACCEPTED') {
                $oto->status = 'IN_PROGRESS';
                $oto->started_at = now();
            }
            $oto->save();

            $stationLabel = strtoupper($stationType);
            WorkOrderLog::create([
                'work_order_id' => $oto->work_order_id,
                'user_id'       => Auth::id(),
                'step'          => 'OTO',
                'action'        => 'OTO_STATION_STARTED',
                'description'   => "Pengerjaan OTO stasiun {$stationLabel} dimulai oleh " . ($oto->{$byCol} ? User::find($oto->{$byCol})?->name : Auth::user()->name) . ".",
            ]);

            $this->dispatch('swal:toast', icon: 'success', title: "Pengerjaan OTO {$stationLabel} dimulai");
        } catch (\Throwable $e) {
            Log::error("OTO Start Station Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function finishStationTask($otoId, $stationType)
    {
        $oto = OTO::find($otoId);
        if (!$oto) return;

        try {
            $completedCol = "oto_{$stationType}_completed_at";
            $oto->{$completedCol} = now();
            $oto->save();

            $stationLabel = strtoupper($stationType);
            WorkOrderLog::create([
                'work_order_id' => $oto->work_order_id,
                'user_id'       => Auth::id(),
                'step'          => 'OTO',
                'action'        => 'OTO_STATION_FINISHED',
                'description'   => "Pengerjaan OTO stasiun {$stationLabel} telah diselesaikan.",
            ]);

            $this->dispatch('swal:toast', icon: 'success', title: "Pengerjaan OTO {$stationLabel} selesai!");
        } catch (\Throwable $e) {
            Log::error("OTO Finish Station Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function completeOto($otoId)
    {
        $oto = OTO::find($otoId);
        if (!$oto) return;

        try {
            $oto->update([
                'status'           => 'COMPLETED',
                'completed_at'     => now(),
                'oto_completed_by' => Auth::id(),
            ]);

            // Update Work Order to Ready for Pickup / Finished
            if ($oto->workOrder) {
                $oto->workOrder->update([
                    'status'           => \App\Enums\WorkOrderStatus::SELESAI->value,
                    'has_active_oto'   => false,
                    'current_location' => 'Rak Selesai / Pickup Area (Gudang)',
                    'finished_date'    => $oto->workOrder->finished_date ?: now(),
                ]);

                WorkOrderLog::create([
                    'work_order_id' => $oto->work_order_id,
                    'user_id'       => Auth::id(),
                    'step'          => 'OTO',
                    'action'        => 'OTO_COMPLETED',
                    'description'   => "Seluruh layanan OTO ({$oto->proposed_services}) selesai dikerjakan. Sepatu dipindahkan ke Rak Selesai / Siap Pickup Gudang.",
                ]);
            }

            unset($this->otos);
            $this->dispatch('swal:toast', icon: 'success', title: "Paket OTO SPK #{$oto->spk_number} selesai! Siap diambil pelanggan.");
        } catch (\Throwable $e) {
            Log::error("OTO Complete Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function bulkComplete()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih minimal satu OTO.');
            return;
        }

        $count = 0;
        foreach ($this->selectedItems as $id) {
            $this->completeOto($id);
            $count++;
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        unset($this->otos);
        $this->dispatch('swal:toast', icon: 'success', title: "{$count} paket OTO berhasil diselesaikan.");
    }

    public function render()
    {
        return view('livewire.oto.oto-station-index', [
            'otos'          => $this->otos,
            'counts'        => $this->counts,
            'techs'         => $this->techs,
            'activeTab'     => $this->activeTab,
            'selectedItems' => $this->selectedItems,
        ])->layout('layouts.workshop-pwa');
    }
}
