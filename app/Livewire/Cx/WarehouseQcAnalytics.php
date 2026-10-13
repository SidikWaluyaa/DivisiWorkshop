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

    // Active Tab: 'cs', 'daily', 'spk'
    public $activeTab = 'cs';

    // Search Filter for CS table
    public $searchCs = '';

    // SPK Detail Filters & Pagination
    public $spkStatusFilter = 'all'; // 'all', 'lolos', 'lolos_catatan', 'tolak', 'belum_qc'
    public $spkSearch = '';
    public $spkCsFilter = '';
    public $spkPage = 1;
    public $spkPerPage = 15;

    protected $queryString = [
        'startDate' => ['except' => '', 'as' => 'start'],
        'endDate' => ['except' => '', 'as' => 'end'],
        'preset' => ['except' => 'this_month', 'as' => 'p'],
        'activeTab' => ['except' => 'cs', 'as' => 'tab'],
        'spkStatusFilter' => ['except' => 'all', 'as' => 'spk_status'],
        'spkCsFilter' => ['except' => '', 'as' => 'cs'],
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
                break;
        }
        $this->spkPage = 1;
    }

    public function setDateRange($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->preset = 'custom';
        $this->spkPage = 1;
    }

    public function updatedStartDate()
    {
        $this->preset = 'custom';
        $this->spkPage = 1;
    }

    public function updatedEndDate()
    {
        $this->preset = 'custom';
        $this->spkPage = 1;
    }

    public function updatedSpkStatusFilter()
    {
        $this->spkPage = 1;
    }

    public function updatedSpkSearch()
    {
        $this->spkPage = 1;
    }

    public function updatedSpkCsFilter()
    {
        $this->spkPage = 1;
    }

    public function filterSpkByStatus($status)
    {
        $this->activeTab = 'spk';
        $this->spkStatusFilter = $status;
        $this->spkPage = 1;
    }

    public function filterSpkByCs($csCode)
    {
        $this->activeTab = 'spk';
        $this->spkCsFilter = $csCode;
        $this->spkPage = 1;
    }

    public function gotoSpkPage($page)
    {
        $this->spkPage = max(1, (int)$page);
    }

    public function nextSpkPage($maxPages)
    {
        if ($this->spkPage < $maxPages) {
            $this->spkPage++;
        }
    }

    public function previousSpkPage()
    {
        if ($this->spkPage > 1) {
            $this->spkPage--;
        }
    }

    public function resetFilters()
    {
        $this->setPreset('this_month');
        $this->searchCs = '';
        $this->activeTab = 'cs';
        $this->spkStatusFilter = 'all';
        $this->spkSearch = '';
        $this->spkCsFilter = '';
        $this->spkPage = 1;
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

        $service = app(WarehouseQcAnalyticsService::class);
        $analytics = $service->getAnalytics($start, $end);

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

        // Fetch detailed SPK list
        $allSpk = $service->getSpkDetails($start, $end);

        // Calculate count for each status badge
        $spkCounts = [
            'all' => count($allSpk),
            'lolos' => 0,
            'lolos_catatan' => 0,
            'tolak' => 0,
            'belum_qc' => 0,
        ];
        $distinctCs = [];

        foreach ($allSpk as $item) {
            $cls = $item['classification'] ?? 'other';
            if (isset($spkCounts[$cls])) {
                $spkCounts[$cls]++;
            }
            if (!empty($item['cs_code'])) {
                $distinctCs[$item['cs_code']] = true;
            }
        }
        ksort($distinctCs);

        // Apply filters to SPK list
        $filteredSpk = array_filter($allSpk, function ($item) {
            // Status filter
            if ($this->spkStatusFilter !== 'all' && $item['classification'] !== $this->spkStatusFilter) {
                return false;
            }

            // CS filter
            if (!empty($this->spkCsFilter) && $item['cs_code'] !== $this->spkCsFilter) {
                return false;
            }

            // Search query
            if (!empty($this->spkSearch)) {
                $q = strtolower(trim($this->spkSearch));
                $match = str_contains(strtolower($item['spk_number']), $q)
                    || str_contains(strtolower($item['customer_name']), $q)
                    || str_contains(strtolower($item['customer_phone']), $q)
                    || str_contains(strtolower($item['brand_type']), $q)
                    || str_contains(strtolower($item['cs_code']), $q);
                if (!$match) return false;
            }

            return true;
        });

        $filteredSpk = array_values($filteredSpk);
        $totalFiltered = count($filteredSpk);
        $totalPages = max(1, (int)ceil($totalFiltered / $this->spkPerPage));

        if ($this->spkPage > $totalPages) {
            $this->spkPage = $totalPages;
        }

        // Paginate slice
        $offset = ($this->spkPage - 1) * $this->spkPerPage;
        $paginatedSpk = array_slice($filteredSpk, $offset, $this->spkPerPage);

        return view('livewire.cx.warehouse-qc-analytics', [
            'analytics' => $analytics,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'preset' => $this->preset,
            'activeTab' => $this->activeTab,
            'searchCs' => $this->searchCs,
            'paginatedSpk' => $paginatedSpk,
            'totalSpkFiltered' => $totalFiltered,
            'spkCounts' => $spkCounts,
            'totalPages' => $totalPages,
            'distinctCs' => array_keys($distinctCs),
        ])->layout('layouts.app');
    }
}
