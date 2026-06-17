<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\FinanceDashboardApiService;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;

class Dashboard extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;

    // Tab & Filter state
    public $activeTab = 'invoices';
    public $filterStatus = '';
    public $filterType = '';
    public $perPage = 15;

    protected $queryString = [
        'activeTab' => ['except' => 'invoices'],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function getFinanceSummary()
    {
        $startStr = $this->startDate ?: now()->startOfMonth()->toDateString();
        $endStr = $this->endDate ?: now()->toDateString();

        $start = Carbon::parse($startStr)->startOfDay();
        $end = Carbon::parse($endStr)->endOfDay();

        $apiService = app(FinanceDashboardApiService::class);
        return $apiService->getFinanceSummary($start, $end);
    }

    /**
     * Get paginated Invoices data with filters.
     */
    public function getInvoicesProperty()
    {
        $query = Invoice::with('customer');

        // Date filter on created_at
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        // Status filter (mapping BB/BL/L to DB values)
        if ($this->filterStatus) {
            $statusMap = [
                'BB' => 'Belum Bayar',
                'BL' => 'DP/Cicil',
                'L'  => 'Lunas',
            ];
            if (isset($statusMap[$this->filterStatus])) {
                $query->where('status', $statusMap[$this->filterStatus]);
            }
        }

        return $query->orderByDesc('created_at')->paginate($this->perPage);
    }

    /**
     * Get paginated InvoicePayments data with filters.
     */
    public function getPaymentsProperty()
    {
        $query = InvoicePayment::with(['invoice', 'creator']);

        // Date filter on payment_date
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('payment_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        // Type filter
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        return $query->orderByDesc('payment_date')->paginate($this->perPage);
    }

    /**
     * Build export PDF URL with current filters.
     */
    public function getExportPdfUrl()
    {
        $params = [
            'tab' => $this->activeTab,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];

        if ($this->activeTab === 'invoices' && $this->filterStatus) {
            $params['status'] = $this->filterStatus;
        }
        if ($this->activeTab === 'payments' && $this->filterType) {
            $params['type'] = $this->filterType;
        }

        return route('finance.dashboard.export-pdf', $params);
    }

    /**
     * Get breakdown of invoice_payments by type for the current date range.
     */
    public function getPaymentTypeBreakdown()
    {
        $query = InvoicePayment::where('verified', true);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('payment_date', [
                Carbon::parse($this->startDate)->toDateString(),
                Carbon::parse($this->endDate)->toDateString(),
            ]);
        }

        $types = ['BEFORE', 'AFTER', 'TAMBAH_JASA', 'LUNAS_AWAL', 'ONGKIR'];
        $breakdown = [];

        foreach ($types as $type) {
            $clone = clone $query;
            $result = $clone->where('type', $type)
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            $breakdown[$type] = [
                'count' => $result->count ?? 0,
                'total_amount' => $result->total_amount ?? 0,
            ];
        }

        // Add 'LAINNYA' category for payments with NULL type or not in the recognized list
        $clone = clone $query;
        $result = $clone->where(function($q) use ($types) {
                $q->whereNull('type')
                  ->orWhereNotIn('type', $types);
            })
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->first();

        $breakdown['LAINNYA'] = [
            'count' => $result->count ?? 0,
            'total_amount' => $result->total_amount ?? 0,
        ];

        // Calculate grand total for percentage bars
        $grandTotal = collect($breakdown)->sum('total_amount');
        foreach ($breakdown as $type => &$data) {
            $data['percentage'] = $grandTotal > 0 ? ($data['total_amount'] / $grandTotal * 100) : 0;
        }

        return $breakdown;
    }

    public function render()
    {
        // Authorize access
        if (!auth()->user()->hasAccess('finance')) {
            abort(403);
        }

        $data = $this->getFinanceSummary();

        return view('livewire.finance.dashboard', [
            'data' => $data,
            'apiKey' => config('app.dashboard_api_key'),
            'invoices' => $this->invoices,
            'payments' => $this->payments,
            'exportPdfUrl' => $this->getExportPdfUrl(),
            'paymentTypeBreakdown' => $this->getPaymentTypeBreakdown(),
        ])->layout('layouts.app');
    }
}
