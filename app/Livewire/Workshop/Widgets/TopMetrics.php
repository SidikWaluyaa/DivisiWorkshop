<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class TopMetrics extends Component
{
    #[Reactive]
    public string $startDate;
    
    #[Reactive]
    public string $endDate;

    public function mount(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->loadData();
    }

    public function updatedStartDate()
    {
        $this->loadData();
    }

    public function updatedEndDate()
    {
        $this->loadData();
    }

    // Snapshot (real-time)
    public int $inProgress = 0;
    public int $urgentCount = 0;
    public int $overdueCount = 0;

    // Historical (filter-based)
    public int $throughput = 0;
    public int $qcPassRate = 0;
    public float $avgLeadTime = 0;
    public float $revenue = 0;

    public function loadData()
    {
        $activeStatuses = [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ];

        $allActive = WorkOrder::whereIn('status', $activeStatuses)->get();

        $this->inProgress = $allActive->count();
        $this->urgentCount = $allActive->filter(fn($o) => $o->days_remaining !== null && $o->days_remaining <= 3 && $o->days_remaining > 0)->count();
        $this->overdueCount = $allActive->filter(fn($o) => $o->is_overdue)->count();

        // Historical
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate)->endOfDay();

        $completed = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $start)
            ->whereDate('finished_date', '<=', $end)
            ->get();

        $this->throughput = $completed->count();
        $this->revenue = $completed->sum('total_service_price');

        if ($this->throughput > 0) {
            $totalDays = $completed->sum(fn($o) => $o->entry_date ? $o->entry_date->diffInDays($o->finished_date) : 0);
            $this->avgLeadTime = round($totalDays / $this->throughput, 1);
            $onTimeCount = $completed->filter(fn($o) => $o->finished_date && $o->estimation_date && $o->finished_date <= $o->estimation_date)->count();
            $this->qcPassRate = round(($completed->where('is_revising', false)->count() / $this->throughput) * 100);
        } else {
            $this->avgLeadTime = 0;
            $this->qcPassRate = 0;
        }
    }

    public function render()
    {
        return view('livewire.workshop.widgets.top-metrics');
    }
}
