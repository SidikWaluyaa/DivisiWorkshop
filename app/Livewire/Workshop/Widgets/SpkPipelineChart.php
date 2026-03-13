<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class SpkPipelineChart extends Component
{
    #[Reactive]
    public string $startDate;
    
    #[Reactive]
    public string $endDate;

    public array $chartData = [];
    public function mount(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->loadData();
    }

    public function updatedStartDate()
    {
        $this->loadData();
        $this->dispatch('refreshChart');
    }

    public function updatedEndDate()
    {
        $this->loadData();
        $this->dispatch('refreshChart');
    }
    public function loadData()
    {
        $statuses = [
            WorkOrderStatus::ASSESSMENT->value => ['label' => 'Assessment', 'color' => '#6366f1'],
            WorkOrderStatus::PREPARATION->value => ['label' => 'Preparation', 'color' => '#8b5cf6'],
            WorkOrderStatus::SORTIR->value => ['label' => 'Sortir', 'color' => '#f59e0b'],
            WorkOrderStatus::PRODUCTION->value => ['label' => 'Production', 'color' => '#14b8a6'],
            WorkOrderStatus::QC->value => ['label' => 'QC', 'color' => '#f97316'],
            WorkOrderStatus::SELESAI->value => ['label' => 'Selesai', 'color' => '#22c55e'],
            WorkOrderStatus::CX_FOLLOWUP->value => ['label' => 'CX Follow Up', 'color' => '#ef4444'],
        ];

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate)->endOfDay();

        $counts = WorkOrder::where(function($q) use ($start, $end) {
                $q->whereBetween('entry_date', [$start, $end])
                  ->orWhereBetween('finished_date', [$start, $end]);
            })
            ->whereIn('status', array_keys($statuses))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($statuses as $value => $info) {
            if (isset($counts[$value]) && $counts[$value] > 0) {
                $labels[] = $info['label'];
                $data[] = $counts[$value];
                $colors[] = $info['color'];
            }
        }

        $this->chartData = [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
            'total' => array_sum($data),
        ];
    }

    public function render()
    {
        return view('livewire.workshop.widgets.spk-pipeline-chart');
    }
}
