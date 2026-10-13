<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use App\Services\Cx\WarehouseQcAnalyticsService;
use Carbon\Carbon;

class WarehouseQcAnalytics extends Component
{
    // Date Filters
    public $startDate = '';
    public $endDate = '';
    public $preset = 'this_month';

    // Active Tab: 'both', 'cs', 'daily'
    public $activeTab = 'both';

    // Search Filter for CS table
    public $searchCs = '';

    protected $queryString = [
        'startDate' => ['except' => '', 'as' => 'start'],
        'endDate' => ['except' => '', 'as' => 'end'],
        'preset' => ['except' => 'this_month', 'as' => 'p'],
        'activeTab' => ['except' => 'both', 'as' => 'tab'],
    ];

    public function mount()
    {
        if (empty($this->startDate)) {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }

        if (empty($this->endDate)) {
            $this->endDate = Carbon::now()->format('Y-m-d');
        }
    }

    public function setPreset($preset)
    {
        $this->preset = $preset;

        switch ($preset) {
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = Carbon::now()->subDays(6)->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'last_30_days':
                $this->startDate = Carbon::now()->subDays(29)->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'custom':
                // Retain current dates
                break;
        }
    }

    public function updatedStartDate()
    {
        $this->preset = 'custom';
    }

    public function updatedEndDate()
    {
        $this->preset = 'custom';
    }

    public function resetFilters()
    {
        $this->setPreset('this_month');
        $this->searchCs = '';
        $this->activeTab = 'both';
    }

    public function render()
    {
        $start = Carbon::parse($this->startDate ?: Carbon::now()->startOfMonth());
        $end = Carbon::parse($this->endDate ?: Carbon::now());

        if ($start->gt($end)) {
            $temp = $start;
            $start = $end;
            $end = $temp;
        }

        $analytics = app(WarehouseQcAnalyticsService::class)->getAnalytics($start, $end);

        // Filter CS Table rows if search query is provided
        if (!empty($this->searchCs)) {
            $search = strtolower(trim($this->searchCs));
            $analytics['cs_table']['rows'] = array_values(array_filter(
                $analytics['cs_table']['rows'],
                function ($row) use ($search) {
                    return str_contains(strtolower($row['name']), $search) 
                        || str_contains(strtolower($row['code']), $search)
                        || str_contains(strtolower($row['display_name']), $search);
                }
            ));
        }

        return view('livewire.cx.warehouse-qc-analytics', [
            'analytics' => $analytics,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'preset' => $this->preset,
            'activeTab' => $this->activeTab,
            'searchCs' => $this->searchCs,
        ])->layout('layouts.app');
    }
}
