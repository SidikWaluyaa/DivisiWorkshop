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

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->preset = 'custom';
    }

    public function updatedEndDate()
    {
        $this->preset = 'custom';
    }

    public function applyPreset(string $preset)
    {
        $this->preset = $preset;

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

    #[Computed]
    public function fastTrackData()
    {
        // Query seluruh SPK Fast Track aktif atau downgrade untuk data statistik agregat dashboard
        $allOrders = WorkOrder::query()
            ->select('id', 'status', 'fast_track_status', 'created_at', 'entry_date')
            ->with(['logs' => function($q) {
                $q->whereIn('action', ['STATUS_CHANGE', 'fast_track_downgrade']);
            }])
            ->where(function($q) {
                $q->where('fast_track_status', 'yes')
                  ->orWhereHas('logs', function($l) {
                      $l->where('action', 'fast_track_downgrade');
                  });
            })
            ->where(function($q) {
                $q->whereBetween('entry_date', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->orWhere(function($sub) {
                    $sub->where('status', 'SPK_PENDING')
                        ->whereBetween('created_at', [
                            Carbon::parse($this->startDate)->startOfDay(),
                            Carbon::parse($this->endDate)->endOfDay()
                        ]);
                });
            })
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
            $reason = $order->getNonSlaFailureReason();
            return $reason !== null && $reason !== 'TAMBAH_JASA';
        });
        $operationalFailedCount = $operationalFailedOrders->count();

        $tambahJasaCount = $orders->filter(fn($o) => $o->getNonSlaFailureReason() === 'TAMBAH_JASA')->count();
        $cxFollowUpCount = $orders->filter(fn($o) => $o->getNonSlaFailureReason() === 'CX_FOLLOWUP')->count();
        $batalCount = $orders->filter(fn($o) => $o->getNonSlaFailureReason() === 'BATAL_DONASI')->count();
        
        $downgradedOrders = $allOrders->where('fast_track_status', 'no');
        $downgradedCount = $downgradedOrders->count();

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
            'downgradedCount' => $downgradedCount,
        ];
    }

    public function render()
    {
        return view('livewire.workshop.dashboard-v2');
    }
}
