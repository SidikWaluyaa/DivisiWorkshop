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

        $rawCounts = WorkOrder::withoutRnd()
            ->whereIn('status', [
                WorkOrderStatus::ASSESSMENT,
                WorkOrderStatus::PREPARATION,
                WorkOrderStatus::SORTIR,
                WorkOrderStatus::PRODUCTION,
                WorkOrderStatus::QC,
            ])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stations = [
            'Assessment' => (int) ($rawCounts[WorkOrderStatus::ASSESSMENT->value] ?? 0),
            'Preparation' => (int) ($rawCounts[WorkOrderStatus::PREPARATION->value] ?? 0),
            'Sortir' => (int) ($rawCounts[WorkOrderStatus::SORTIR->value] ?? 0),
            'Production' => (int) ($rawCounts[WorkOrderStatus::PRODUCTION->value] ?? 0),
            'QC' => (int) ($rawCounts[WorkOrderStatus::QC->value] ?? 0),
        ];

        $this->stationData = $stations;

        $sorted = collect($stations)->sortDesc();
        $this->bottleneck = $sorted->keys()->first();
        $this->bottleneckCount = $sorted->first();

        $activeWos = WorkOrder::whereDate('entry_date', '>=', $start)
            ->whereDate('entry_date', '<=', $end)
            ->whereIn('status', [WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])
            ->select('id', 'technician_production_id', 'qc_final_pic_id', 'prod_sol_by', 'prod_upper_by', 'prod_cleaning_by')
            ->get();

        $techLoadMap = [];
        foreach ($activeWos as $wo) {
            $tIds = array_filter(array_unique([
                $wo->technician_production_id,
                $wo->qc_final_pic_id,
                $wo->prod_sol_by,
                $wo->prod_upper_by,
                $wo->prod_cleaning_by,
            ]));
            foreach ($tIds as $tId) {
                $techLoadMap[$tId] = ($techLoadMap[$tId] ?? 0) + 1;
            }
        }

        $this->technicianLoad = User::where('role', '!=', 'customer')
            ->select('id', 'name')
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'count' => $techLoadMap[$user->id] ?? 0,
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
