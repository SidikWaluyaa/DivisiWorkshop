<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProductionLeadTimeChart extends Component
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
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Daily completions
        $completions = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $start)
            ->whereDate('finished_date', '<=', $end)
            ->selectRaw('DATE(finished_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Daily new entries
        $entries = WorkOrder::whereDate('entry_date', '>=', $start)
            ->whereDate('entry_date', '<=', $end)
            ->selectRaw('DATE(entry_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $labels = [];
        $completionData = [];
        $entryData = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $completionData[] = $completions[$key] ?? 0;
            $entryData[] = $entries[$key] ?? 0;
        }

        $totalCompletions = array_sum($completionData);
        $totalEntries = array_sum($entryData);
        $ratio = $totalEntries > 0 ? round(($totalCompletions / $totalEntries) * 100) : ($totalCompletions > 0 ? 100 : 0);

        $this->chartData = [
            'labels' => $labels,
            'completions' => $completionData,
            'entries' => $entryData,
            'totalCompletions' => $totalCompletions,
            'totalEntries' => $totalEntries,
            'ratio' => $ratio,
        ];
    }

    public function render()
    {
        return view('livewire.workshop.widgets.production-lead-time-chart');
    }
}
