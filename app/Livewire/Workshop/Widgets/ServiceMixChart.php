<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\WorkOrderService;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class ServiceMixChart extends Component
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
        $end = Carbon::parse($this->endDate)->endOfDay();

        $mix = WorkOrderService::whereHas('workOrder', fn($q) =>
                $q->where('status', WorkOrderStatus::SELESAI)
                    ->whereDate('finished_date', '>=', $start)
                    ->whereDate('finished_date', '<=', $end))
            ->selectRaw("COALESCE(category_name, 'Lainnya') as category, SUM(cost) as total_revenue, COUNT(*) as order_count")
            ->groupBy('category')
            ->orderByDesc('total_revenue')
            ->take(8)
            ->get();

        $this->chartData = [
            'labels' => $mix->pluck('category')->toArray(),
            'revenue' => $mix->pluck('total_revenue')->toArray(),
            'counts' => $mix->pluck('order_count')->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.workshop.widgets.service-mix-chart');
    }
}
