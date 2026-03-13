<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;

class UrgentActionGrid extends Component
{
    public $urgentOrders;
    public string $search = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $activeStatuses = [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ];

        $query = WorkOrder::whereIn('status', $activeStatuses)
            ->whereNotNull('estimation_date');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                    ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        $orders = $query->orderByRaw("DATEDIFF(estimation_date, NOW()) ASC")->get();

        $this->urgentOrders = $orders->filter(function ($o) {
            return $o->days_remaining !== null && $o->days_remaining <= 5;
        })->take(20)->values();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function getRouteForStatus(string $statusValue, int $id): string
    {
        return match ($statusValue) {
            'ASSESSMENT' => route('assessment.create', $id),
            'PREPARATION' => route('preparation.show', $id),
            'SORTIR' => route('sortir.show', $id),
            'QC' => route('qc.show', $id),
            default => '#',
        };
    }

    public function render()
    {
        return view('livewire.workshop.widgets.urgent-action-grid');
    }
}
