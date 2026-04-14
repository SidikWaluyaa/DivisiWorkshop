<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\WorkOrderService;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TopServiceNames extends Component
{
    #[Reactive]
    public string $startDate;

    #[Reactive]
    public string $endDate;

    public array $services = [];

    public function loadData()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate)->endOfDay();

        $results = WorkOrderService::query()
            ->whereHas('workOrder', function($q) use ($start, $end) {
                $q->where('status', WorkOrderStatus::SELESAI)
                  ->whereDate('finished_date', '>=', $start)
                  ->whereDate('finished_date', '<=', $end);
            })
            ->leftJoin('services', 'work_order_services.service_id', '=', 'services.id')
            ->select(
                DB::raw("COALESCE(services.name, custom_service_name, 'Layanan Kustom') as service_name"),
                DB::raw("COUNT(*) as total_count"),
                DB::raw("SUM(cost) as total_revenue")
            )
            ->groupBy('service_name')
            ->orderByDesc('total_revenue')
            ->take(7)
            ->get();

        $maxRevenue = $results->max('total_revenue') ?: 1;

        $this->services = $results->map(fn($item) => [
            'name' => $item->service_name,
            'count' => $item->total_count,
            'revenue' => $item->total_revenue,
            'percentage' => ($item->total_revenue / $maxRevenue) * 100
        ])->toArray();
    }

    public function render()
    {
        $this->loadData();
        return view('livewire.workshop.widgets.top-service-names');
    }
}
