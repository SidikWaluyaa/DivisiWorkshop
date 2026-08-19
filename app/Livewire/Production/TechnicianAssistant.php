<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TechnicianAssistant extends Component
{
    use WithPagination;

    public $selectedTechnicianId = null;
    public $stationCategory = 'ALL'; // 'ALL', 'PREPARATION', 'SOLING', 'UPPER', 'TREATMENT', 'QC'
    public $activeTab = 'assigned'; // 'running', 'assigned', 'history'
    public $search = '';

    protected $queryString = [
        'selectedTechnicianId' => ['except' => null],
        'stationCategory' => ['except' => 'ALL'],
        'activeTab' => ['except' => 'assigned'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $filtered = $this->getFilteredTechnicians($this->stationCategory);
        if ($filtered->isNotEmpty()) {
            $this->selectedTechnicianId = $filtered->first()->id;
        } else {
            $firstTech = User::where('role', 'technician')->first();
            $this->selectedTechnicianId = $firstTech ? $firstTech->id : Auth::id();
        }
    }

    public function filterStationCategory($cat)
    {
        $this->stationCategory = $cat;

        $filtered = $this->getFilteredTechnicians($cat);
        if ($filtered->isNotEmpty()) {
            $this->selectedTechnicianId = $filtered->first()->id;
        }

        $this->resetPage();
    }

    public function selectTechnician($id)
    {
        $this->selectedTechnicianId = $id;
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    private function getFilteredTechnicians($cat = 'ALL')
    {
        // Exclude PIC Material & Non-repair staff as requested by USER
        $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'];

        $allTechs = User::where('role', 'technician')
            ->where(function ($q) use ($excludedSpecs) {
                $q->whereNotIn('specialization', $excludedSpecs)
                  ->orWhereNull('specialization');
            })
            ->where('name', 'not like', '%Dr. Shoe%')
            ->orderBy('name')
            ->get()
            ->map(function ($tech) {
                $st = strtoupper($tech->station ?? '');
                $spec = trim($tech->specialization ?? '');
                $specUpper = strtoupper($spec);

                if ($st === 'PREPARATION' || str_contains($specUpper, 'WASH') || str_contains($specUpper, 'CUCI') || str_contains($specUpper, 'PREP')) {
                    $tech->computed_category = 'PREPARATION';
                    $tech->category_label = 'Persiapan (Cuci)';
                    $tech->badge_color = 'bg-cyan-500 text-white';
                    $tech->display_spec = 'Washing & Cuci';
                } elseif ($st === 'SOLING' || str_contains($specUpper, 'SOL REPAIR') || str_contains($specUpper, 'SOLING') || str_contains($specUpper, 'SOL')) {
                    $tech->computed_category = 'SOLING';
                    $tech->category_label = 'Soling (Sol Repair)';
                    $tech->badge_color = 'bg-amber-500 text-white';
                    $tech->display_spec = 'Sol Repair';
                } elseif ($st === 'UPPER' || str_contains($specUpper, 'UPPER') || str_contains($specUpper, 'JAHIT')) {
                    $tech->computed_category = 'UPPER';
                    $tech->category_label = 'Upper & Jahit';
                    $tech->badge_color = 'bg-blue-600 text-white';
                    $tech->display_spec = str_contains($specUpper, 'JAHIT') ? 'Jahit' : 'Upper Repair';
                } elseif ($st === 'QC' || str_contains($specUpper, 'QC')) {
                    $tech->computed_category = 'QC';
                    $tech->category_label = 'Quality Control (QC)';
                    $tech->badge_color = 'bg-purple-600 text-white';
                    $tech->display_spec = 'Inspector QC';
                } else {
                    $tech->computed_category = 'TREATMENT';
                    $tech->category_label = 'Treatment & Repaint';
                    $tech->badge_color = 'bg-[#22AF85] text-white';
                    $tech->display_spec = !empty($spec) ? $spec : 'Treatment';
                }

                return $tech;
            });

        if ($cat === 'ALL') {
            return $allTechs;
        }

        return $allTechs->filter(fn($t) => $t->computed_category === $cat)->values();
    }

    public function startJob($workOrderId, $stationType)
    {
        $wo = WorkOrder::findOrFail($workOrderId);
        $now = Carbon::now();

        if ($stationType === 'sol') {
            $wo->prod_sol_started_at = $now;
            $stationName = 'Soling (Sol Repair)';
        } elseif ($stationType === 'upper') {
            $wo->prod_upper_started_at = $now;
            $stationName = 'Upper & Jahit';
        } elseif ($stationType === 'cleaning' || $stationType === 'treatment') {
            $wo->prod_cleaning_started_at = $now;
            $stationName = 'Treatment / Repaint';
        } elseif ($stationType === 'washing' || $stationType === 'prep') {
            $wo->prep_washing_started_at = $now;
            $stationName = 'Persiapan (Cuci)';
        } else {
            $stationName = 'Stasiun Produksi';
        }

        if (!$wo->production_started_at) {
            $wo->production_started_at = $now;
        }
        $wo->save();

        // Also start pivot service if matching
        $wos = WorkOrderService::where('work_order_id', $workOrderId)
            ->where('technician_id', $this->selectedTechnicianId)
            ->first();
        if ($wos) {
            $wos->started_at = $now;
            $wos->status = 'IN_PROGRESS';
            $wos->save();
        }

        WorkOrderLog::create([
            'work_order_id' => $wo->id,
            'user_id' => Auth::id() ?? 1,
            'step' => 'PRODUCTION_JOB_START',
            'action' => 'START',
            'description' => "Mulai pengerjaan stasiun {$stationName} pada SPK {$wo->spk_number}.",
        ]);

        session()->flash('success', "Pengerjaan stasiun {$stationName} pada SPK {$wo->spk_number} berhasil dimulai!");
    }

    public function completeJob($workOrderId, $stationType)
    {
        $wo = WorkOrder::findOrFail($workOrderId);
        $now = Carbon::now();
        $startedAt = null;

        if ($stationType === 'sol') {
            $startedAt = $wo->prod_sol_started_at;
            $wo->prod_sol_completed_at = $now;
            $stationName = 'Soling (Sol Repair)';
        } elseif ($stationType === 'upper') {
            $startedAt = $wo->prod_upper_started_at;
            $wo->prod_upper_completed_at = $now;
            $stationName = 'Upper & Jahit';
        } elseif ($stationType === 'cleaning' || $stationType === 'treatment') {
            $startedAt = $wo->prod_cleaning_started_at;
            $wo->prod_cleaning_completed_at = $now;
            $stationName = 'Treatment / Repaint';
        } elseif ($stationType === 'washing' || $stationType === 'prep') {
            $startedAt = $wo->prep_washing_started_at;
            $wo->prep_washing_completed_at = $now;
            $stationName = 'Persiapan (Cuci)';
        } else {
            $stationName = 'Stasiun Produksi';
        }

        $wo->save();

        $durationMinutes = 1;
        if ($startedAt) {
            $durationMinutes = max(1, (int) round(Carbon::parse($startedAt)->diffInMinutes($now)));
        }

        // Also complete pivot service if matching
        $wos = WorkOrderService::where('work_order_id', $workOrderId)
            ->where('technician_id', $this->selectedTechnicianId)
            ->first();
        if ($wos) {
            $wos->completed_at = $now;
            $wos->actual_duration_minutes = $durationMinutes;
            $wos->status = 'COMPLETED';
            $wos->save();
        }

        WorkOrderLog::create([
            'work_order_id' => $wo->id,
            'user_id' => Auth::id() ?? 1,
            'step' => 'PRODUCTION_JOB_COMPLETE',
            'action' => 'COMPLETE',
            'description' => "Selesai pengerjaan stasiun {$stationName} pada SPK {$wo->spk_number} (Durasi: {$durationMinutes} menit).",
        ]);

        session()->flash('success', "Pengerjaan stasiun {$stationName} pada SPK {$wo->spk_number} selesai! Durasi: {$durationMinutes} menit.");
    }

    private function getTechAssignedJobs($techId)
    {
        if (!$techId) return collect();

        // 1. Fetch work orders directly assigned to tech in station columns
        $workOrders = WorkOrder::with(['photos', 'workOrderServices.service'])
            ->where(function ($q) use ($techId) {
                $q->where('prep_washing_by', $techId)
                  ->orWhere('prod_sol_by', $techId)
                  ->orWhere('prod_upper_by', $techId)
                  ->orWhere('prod_cleaning_by', $techId)
                  ->orWhereHas('workOrderServices', fn($sq) => $sq->where('technician_id', $techId));
            });

        if (!empty($this->search)) {
            $search = $this->search;
            $workOrders->where(function ($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        $wos = $workOrders->orderBy('id', 'desc')->get();

        $jobs = collect();

        foreach ($wos as $wo) {
            // Check Washing Station Assignment
            if ($wo->prep_washing_by == $techId) {
                $jobs->push([
                    'id' => $wo->id . '_prep',
                    'work_order_id' => $wo->id,
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe_brand' => $wo->shoe_brand,
                    'shoe_type' => $wo->shoe_type,
                    'station_type' => 'washing',
                    'station_label' => 'Persiapan (Cuci)',
                    'service_title' => 'Persiapan & Washing',
                    'started_at' => $wo->prep_washing_started_at ? Carbon::parse($wo->prep_washing_started_at) : null,
                    'completed_at' => $wo->prep_washing_completed_at ? Carbon::parse($wo->prep_washing_completed_at) : null,
                    'wo_status' => is_object($wo->status) ? $wo->status->value : $wo->status,
                    'workOrder' => $wo,
                ]);
            }

            // Check Soling Station Assignment
            if ($wo->prod_sol_by == $techId) {
                $jobs->push([
                    'id' => $wo->id . '_sol',
                    'work_order_id' => $wo->id,
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe_brand' => $wo->shoe_brand,
                    'shoe_type' => $wo->shoe_type,
                    'station_type' => 'sol',
                    'station_label' => 'Soling (Sol Repair)',
                    'service_title' => 'Reparasi Soling',
                    'started_at' => $wo->prod_sol_started_at ? Carbon::parse($wo->prod_sol_started_at) : null,
                    'completed_at' => $wo->prod_sol_completed_at ? Carbon::parse($wo->prod_sol_completed_at) : null,
                    'wo_status' => is_object($wo->status) ? $wo->status->value : $wo->status,
                    'workOrder' => $wo,
                ]);
            }

            // Check Upper Station Assignment
            if ($wo->prod_upper_by == $techId) {
                $jobs->push([
                    'id' => $wo->id . '_upper',
                    'work_order_id' => $wo->id,
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe_brand' => $wo->shoe_brand,
                    'shoe_type' => $wo->shoe_type,
                    'station_type' => 'upper',
                    'station_label' => 'Upper & Jahit',
                    'service_title' => 'Reparasi Upper',
                    'started_at' => $wo->prod_upper_started_at ? Carbon::parse($wo->prod_upper_started_at) : null,
                    'completed_at' => $wo->prod_upper_completed_at ? Carbon::parse($wo->prod_upper_completed_at) : null,
                    'wo_status' => is_object($wo->status) ? $wo->status->value : $wo->status,
                    'workOrder' => $wo,
                ]);
            }

            // Check Cleaning / Treatment Station Assignment
            if ($wo->prod_cleaning_by == $techId) {
                $jobs->push([
                    'id' => $wo->id . '_cleaning',
                    'work_order_id' => $wo->id,
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe_brand' => $wo->shoe_brand,
                    'shoe_type' => $wo->shoe_type,
                    'station_type' => 'cleaning',
                    'station_label' => 'Treatment / Repaint',
                    'service_title' => 'Treatment & Repaint',
                    'started_at' => $wo->prod_cleaning_started_at ? Carbon::parse($wo->prod_cleaning_started_at) : null,
                    'completed_at' => $wo->prod_cleaning_completed_at ? Carbon::parse($wo->prod_cleaning_completed_at) : null,
                    'wo_status' => is_object($wo->status) ? $wo->status->value : $wo->status,
                    'workOrder' => $wo,
                ]);
            }
        }

        // Also check work_order_services pivot assignments if not already pushed
        $services = WorkOrderService::with(['workOrder.photos', 'service'])
            ->where('technician_id', $techId)
            ->get();

        foreach ($services as $svc) {
            $wo = $svc->workOrder;
            if (!$wo) continue;

            $exists = $jobs->first(fn($j) => $j['work_order_id'] == $wo->id);
            if (!$exists) {
                $jobs->push([
                    'id' => $wo->id . '_svc_' . $svc->id,
                    'work_order_id' => $wo->id,
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe_brand' => $wo->shoe_brand,
                    'shoe_type' => $wo->shoe_type,
                    'station_type' => 'service',
                    'station_label' => $svc->category_name ?: 'PRODUKSI',
                    'service_title' => $svc->custom_service_name ?: ($svc->service?->name ?: 'Jasa Workshop'),
                    'started_at' => $svc->started_at ? Carbon::parse($svc->started_at) : null,
                    'completed_at' => $svc->completed_at ? Carbon::parse($svc->completed_at) : null,
                    'wo_status' => is_object($wo->status) ? $wo->status->value : $wo->status,
                    'workOrder' => $wo,
                ]);
            }
        }

        return $jobs;
    }

    public function render()
    {
        $allTechnicians = $this->getFilteredTechnicians('ALL');
        $filteredTechnicians = $this->getFilteredTechnicians($this->stationCategory);

        $countAll = $allTechnicians->count();
        $countPrep = $allTechnicians->where('computed_category', 'PREPARATION')->count();
        $countSoling = $allTechnicians->where('computed_category', 'SOLING')->count();
        $countUpper = $allTechnicians->where('computed_category', 'UPPER')->count();
        $countTreatment = $allTechnicians->where('computed_category', 'TREATMENT')->count();
        $countQc = $allTechnicians->where('computed_category', 'QC')->count();

        $selectedTech = User::find($this->selectedTechnicianId);

        // Fetch All Jobs assigned to this technician (both work_orders and work_order_services)
        $allJobs = $this->getTechAssignedJobs($this->selectedTechnicianId);

        // Classify into 3 Tabs: Running (In Progress), Assigned (Queued), History (Completed)
        $runningJobs = $allJobs->filter(fn($j) => $j['started_at'] !== null && $j['completed_at'] === null);
        $assignedJobs = $allJobs->filter(fn($j) => $j['completed_at'] === null && $j['started_at'] === null);
        $completedJobs = $allJobs->filter(fn($j) => $j['completed_at'] !== null);

        $runningCount = $runningJobs->count();
        $assignedCount = $assignedJobs->count();
        $completedTodayCount = $completedJobs->filter(fn($j) => $j['completed_at']?->isToday())->count();
        $completedMonthCount = $completedJobs->filter(fn($j) => $j['completed_at']?->isCurrentMonth())->count();

        $avgDuration = 0;
        $completedWithDuration = $completedJobs->filter(fn($j) => $j['started_at'] && $j['completed_at']);
        if ($completedWithDuration->isNotEmpty()) {
            $totalMins = $completedWithDuration->sum(fn($j) => max(1, $j['started_at']->diffInMinutes($j['completed_at'])));
            $avgDuration = (int) round($totalMins / $completedWithDuration->count());
        }

        // Active List for Current Tab
        if ($this->activeTab === 'running') {
            $displayJobs = $runningJobs;
        } elseif ($this->activeTab === 'assigned') {
            $displayJobs = $assignedJobs;
        } else {
            $displayJobs = $completedJobs;
        }

        return view('livewire.production.technician-assistant', [
            'technicians' => $filteredTechnicians,
            'countAll' => $countAll,
            'countPrep' => $countPrep,
            'countSoling' => $countSoling,
            'countUpper' => $countUpper,
            'countTreatment' => $countTreatment,
            'countQc' => $countQc,
            'selectedTech' => $selectedTech,
            'runningCount' => $runningCount,
            'assignedCount' => $assignedCount,
            'completedTodayCount' => $completedTodayCount,
            'completedMonthCount' => $completedMonthCount,
            'avgDuration' => $avgDuration,
            'displayJobs' => $displayJobs,
        ])->layout('layouts.workshop-pwa');
    }
}
