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
        $metricsService = app(\App\Services\WorkshopMetricsService::class);
        
        // Snapshot
        $snapshot = $metricsService->getSnapshotMetrics();
        $this->inProgress = $snapshot['in_progress'];
        $this->urgentCount = $snapshot['urgent'];
        $this->overdueCount = $snapshot['overdue'];

        // Historical
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $historical = $metricsService->getHistoricalMetrics($start, $end);
        $this->throughput = $historical['throughput'];
        $this->revenue = $historical['revenue'];
        $this->avgLeadTime = $historical['avg_lead_time'];
        $this->qcPassRate = $historical['qc_pass_rate'];
    }

    public function render()
    {
        return view('livewire.workshop.widgets.top-metrics');
    }
}
