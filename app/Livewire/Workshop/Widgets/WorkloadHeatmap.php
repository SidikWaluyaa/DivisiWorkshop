<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class WorkloadHeatmap extends Component
{
    #[Reactive]
    public string $startDate;

    #[Reactive]
    public string $endDate;

    public array $stationData = [];
    public $technicianLoad;
    public string $bottleneck = '';
    public int $bottleneckCount = 0;

    public function loadData()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate)->endOfDay();

        $stations = [
            'Assessment' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->where('status', WorkOrderStatus::ASSESSMENT)->count(),
            'Preparation' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->where('status', WorkOrderStatus::PREPARATION)->count(),
            'Sortir' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->where('status', WorkOrderStatus::SORTIR)->count(),
            'Production' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->where('status', WorkOrderStatus::PRODUCTION)->count(),
            'QC' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->where('status', WorkOrderStatus::QC)->count(),
        ];

        $this->stationData = $stations;

        $sorted = collect($stations)->sortDesc();
        $this->bottleneck = $sorted->keys()->first();
        $this->bottleneckCount = $sorted->first();

        $this->technicianLoad = User::where('role', '!=', 'customer')
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'count' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)
                    ->whereIn('status', [WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])
                    ->where(fn($q) => $q->where('technician_production_id', $user->id)
                        ->orWhere('qc_final_pic_id', $user->id)
                        ->orWhere('prod_sol_by', $user->id)
                        ->orWhere('prod_upper_by', $user->id)
                        ->orWhere('prod_cleaning_by', $user->id))
                    ->count()
            ])
            ->where('count', '>', 0)
            ->sortByDesc('count')
            ->take(8)
            ->values();
    }

    public function render()
    {
        $this->loadData();
        return view('livewire.workshop.widgets.workload-heatmap');
    }
}
