<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use App\Models\WorkOrderLog;

class RecentActivityFeed extends Component
{
    public $logs;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->logs = WorkOrderLog::latest()
            ->take(15)
            ->with(['user', 'workOrder'])
            ->get();
    }

    public function render()
    {
        return view('livewire.workshop.widgets.recent-activity-feed');
    }
}
