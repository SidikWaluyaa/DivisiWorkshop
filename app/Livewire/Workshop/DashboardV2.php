<?php

namespace App\Livewire\Workshop;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

use Livewire\Attributes\Computed;
use App\Models\WorkOrder;

#[Layout('layouts.app')]
#[Title('Workshop Dashboard V2')]
class DashboardV2 extends Component
{
    public string $startDate;
    public string $endDate;
    public string $preset = 'month';

    // Modal properties
    public bool $showModal = false;
    public string $modalTitle = '';
    public string $selectedMetric = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->preset = 'custom';
        $this->closeModal();
    }

    public function updatedEndDate()
    {
        $this->preset = 'custom';
        $this->closeModal();
    }

    public function applyPreset(string $preset)
    {
        $this->preset = $preset;
        $this->closeModal();

        switch ($preset) {
            case 'today':
                $this->startDate = now()->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = now()->subDays(6)->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case '3month':
                $this->startDate = now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
        }
    }

    public function openDetailModal(string $metric)
    {
        $this->selectedMetric = $metric;
        $this->showModal = true;
        unset($this->fastTrackData);

        switch ($metric) {
            case 'total_fast_track':
                $this->modalTitle = 'Daftar Semua SPK Fast Track';
                break;
            case 'total_revenue':
                $this->modalTitle = 'Rincian Pendapatan SPK Fast Track';
                break;
            case 'failed_fast_track':
                $this->modalTitle = 'Daftar SPK Fast Track Gagal SLA (Stasiun)';
                break;
            case 'operational_failed_fast_track':
                $this->modalTitle = 'Daftar Fast Track Gagal Operasional (Non-SLA)';
                break;
            case 'pending_fast_track':
                $this->modalTitle = 'Daftar SPK Fast Track Pending (CS)';
                break;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedMetric = '';
        $this->modalTitle = '';
        unset($this->fastTrackData);
    }

    #[Computed]
    public function fastTrackData()
    {
        // Query seluruh SPK Fast Track aktif ATAU SPK yang pernah di-downgrade dari Fast Track
        $allOrders = WorkOrder::query()
            ->with(['logs', 'customer', 'cxIssues'])
            ->where(function($q) {
                $q->where('fast_track_status', 'yes')
                  ->orWhereHas('logs', function($l) {
                      $l->where('action', 'fast_track_downgrade');
                  });
            })
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->get();

        // 1. Khusus SPK Pending CS (Status SPK_PENDING)
        $pendingOrders = $allOrders->filter(function($o) {
            return $o->status->value === 'SPK_PENDING' && $o->fast_track_status === 'yes';
        });
        $pendingCount = $pendingOrders->count();
        $pendingRevenue = $pendingOrders->sum('total_transaksi');

        // 2. SPK Workshop Aktif (Abaikan status SPK_PENDING)
        $orders = $allOrders->filter(function($o) {
            return $o->status->value !== 'SPK_PENDING';
        });

        // Total Fast Track (hanya yang fast_track_status = yes)
        $ftActiveOrders = $orders->where('fast_track_status', 'yes');
        $totalCount = $ftActiveOrders->count();
        $totalRevenue = $ftActiveOrders->sum('total_transaksi');

        $activeTotal = $ftActiveOrders->filter(function($o) {
            return !in_array($o->status->value, ['SELESAI', 'HISTORY', 'BATAL', 'DONASI']);
        })->count();
        $finishedTotal = $ftActiveOrders->filter(function($o) {
            return in_array($o->status->value, ['SELESAI', 'HISTORY']);
        })->count();

        // SLA Failures (Hanya untuk SPK Fast Track aktif)
        $failedOrders = $ftActiveOrders->filter(function($order) {
            return $order->hasEverViolatedSla();
        });
        $failedCount = $failedOrders->count();

        $activeFailed = $failedOrders->filter(function($o) {
            return !in_array($o->status->value, ['SELESAI', 'HISTORY', 'BATAL', 'DONASI']);
        })->count();
        $finishedFailed = $failedOrders->filter(function($o) {
            return in_array($o->status->value, ['SELESAI', 'HISTORY']);
        })->count();

        // Non-SLA Operational Failures (Tambah Jasa, CX FollowUp, Batal)
        $operationalFailedOrders = $orders->filter(function($order) {
            return $order->getNonSlaFailureReason() !== null;
        });
        $operationalFailedCount = $operationalFailedOrders->count();

        $tambahJasaCount = $orders->filter(fn($o) => $o->getNonSlaFailureReason() === 'TAMBAH_JASA')->count();
        $cxFollowUpCount = $orders->filter(fn($o) => $o->getNonSlaFailureReason() === 'CX_FOLLOWUP')->count();
        $batalCount = $orders->filter(fn($o) => $o->getNonSlaFailureReason() === 'BATAL_DONASI')->count();

        $modalOrders = collect();
        if ($this->selectedMetric === 'total_fast_track' || $this->selectedMetric === 'total_revenue') {
            $modalOrders = $ftActiveOrders;
        } elseif ($this->selectedMetric === 'failed_fast_track') {
            $modalOrders = $failedOrders;
        } elseif ($this->selectedMetric === 'operational_failed_fast_track') {
            $modalOrders = $operationalFailedOrders;
        } elseif ($this->selectedMetric === 'pending_fast_track') {
            $modalOrders = $pendingOrders;
        }

        return [
            'totalCount' => $totalCount,
            'totalRevenue' => $totalRevenue,
            'failedCount' => $failedCount,
            'activeTotal' => $activeTotal,
            'finishedTotal' => $finishedTotal,
            'activeFailed' => $activeFailed,
            'finishedFailed' => $finishedFailed,
            'operationalFailedCount' => $operationalFailedCount,
            'tambahJasaCount' => $tambahJasaCount,
            'cxFollowUpCount' => $cxFollowUpCount,
            'batalCount' => $batalCount,
            'pendingCount' => $pendingCount,
            'pendingRevenue' => $pendingRevenue,
            'modalOrders' => $modalOrders,
        ];
    }

    public function render()
    {
        return view('livewire.workshop.dashboard-v2');
    }
}
