<?php

namespace App\Livewire\Cs;

use Livewire\Component;
use App\Models\CsLead;
use App\Models\CsSpk;
use App\Models\CsSpkItem;
use App\Models\CsActivity;
use App\Models\WorkOrder;
use Carbon\Carbon;

class Forecasting extends Component
{
    public $selectedYear;
    public $monthlyData = [];

    public function mount()
    {
        // Authorize access
        if (!auth()->user()->hasAccess('cs')) {
            abort(403);
        }

        // Default: Current Year
        $this->selectedYear = Carbon::now()->year;

        $this->calculateForecast();
    }

    public function updatedSelectedYear()
    {
        $this->calculateForecast();
    }

    /**
     * Calculate monthly metrics dynamically for all 12 months of selected year
     */
    public function calculateForecast()
    {
        if (empty($this->selectedYear)) {
            $this->selectedYear = Carbon::now()->year;
        }

        $this->monthlyData = [];

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $now = Carbon::now();

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($this->selectedYear, $month, 1)->startOfMonth()->startOfDay();
            
            // Smart days calculation
            if ($this->selectedYear == $now->year && $month == $now->month) {
                // Current month: active days up to today
                $monthEnd = $now->copy()->endOfDay();
                $daysInPeriod = $now->day;
            } else {
                $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();
                $daysInPeriod = $monthStart->daysInMonth;
            }

            // 1. Get closed leads
            $closedLeads = CsLead::whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->get();

            $closedLeadIds = $closedLeads->pluck('id');
            $closingOnlineTotal = $closedLeads->where('channel', CsLead::CHANNEL_ONLINE)->count();
            $closingOffline = $closedLeads->where('channel', CsLead::CHANNEL_OFFLINE)->count();

            // 2. Closing via Follow Up (has activity Status diubah ke FOLLOW_UP)
            $closingFollowUp = CsActivity::whereIn('cs_lead_id', $closedLeadIds)
                ->where('type', CsActivity::TYPE_STATUS_CHANGE)
                ->where('content', 'LIKE', '%Status diubah ke FOLLOW_UP%')
                ->distinct('cs_lead_id')
                ->count('cs_lead_id');

            $closingOnlineDirect = max(0, $closingOnlineTotal - $closingFollowUp);

            // 3. Closing Tidak Kirim (SPK Sepatu berstatus SPK_PENDING in work_orders)
            $statusPendingValue = \App\Enums\WorkOrderStatus::SPK_PENDING->value ?? 'SPK_PENDING';
            $closingTidakKirim = WorkOrder::where('status', $statusPendingValue)
                ->where('category', 'Sepatu')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            // 4. Sepatu Masuk Online & FU vs Offline (from work_orders category 'Sepatu', excluding SPK_PENDING)
            $sepatuMasukOnline = WorkOrder::where('category', 'Sepatu')
                ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('id', function ($query) {
                    $query->select('cs_spk_items.work_order_id')
                        ->from('cs_spk_items')
                        ->join('cs_spk', 'cs_spk_items.spk_id', '=', 'cs_spk.id')
                        ->join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                        ->where('cs_leads.channel', CsLead::CHANNEL_ONLINE)
                        ->whereNotNull('cs_spk_items.work_order_id');
                })
                ->count();

            $sepatuMasukOffline = WorkOrder::where('category', 'Sepatu')
                ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('id', function ($query) {
                    $query->select('cs_spk_items.work_order_id')
                        ->from('cs_spk_items')
                        ->join('cs_spk', 'cs_spk_items.spk_id', '=', 'cs_spk.id')
                        ->join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                        ->where('cs_leads.channel', CsLead::CHANNEL_OFFLINE)
                        ->whereNotNull('cs_spk_items.work_order_id');
                })
                ->count();

            // Calculate daily rates based on active days in period
            $dailyOnline = $closingOnlineDirect / $daysInPeriod;
            $dailyFU = $closingFollowUp / $daysInPeriod;
            $dailyOffline = $closingOffline / $daysInPeriod;
            $dailyTidakKirim = $closingTidakKirim / $daysInPeriod;

            // Total closing balance = Online Direct + Follow Up + Offline
            $totalClosing = $closingOnlineDirect + $closingFollowUp + $closingOffline;

            // Percentages
            $pctClosingOnline = $totalClosing > 0 ? ($closingOnlineDirect / $totalClosing) * 100 : 0;
            $pctClosingFollowUp = $totalClosing > 0 ? ($closingFollowUp / $totalClosing) * 100 : 0;
            $pctClosingOffline = $totalClosing > 0 ? ($closingOffline / $totalClosing) * 100 : 0;

            $onlineAndFU = $closingOnlineDirect + $closingFollowUp;
            $pctClosingTidakKirim = $onlineAndFU > 0 ? ($closingTidakKirim / $onlineAndFU) * 100 : 0;

            $sepatuTotal = $sepatuMasukOnline + $sepatuMasukOffline;
            $pctSepatuOnline = $sepatuTotal > 0 ? ($sepatuMasukOnline / $sepatuTotal) * 100 : 0;
            $pctSepatuOffline = $sepatuTotal > 0 ? ($sepatuMasukOffline / $sepatuTotal) * 100 : 0;

            $this->monthlyData[] = [
                'month_name' => $monthNames[$month] . ' ' . $this->selectedYear,
                'days_in_period' => $daysInPeriod,
                'closing_online' => $closingOnlineDirect,
                'closing_online_pct' => round($pctClosingOnline, 2),
                'closing_online_per_day' => round($dailyOnline, 2),
                'closing_followup' => $closingFollowUp,
                'closing_followup_pct' => round($pctClosingFollowUp, 2),
                'closing_followup_per_day' => round($dailyFU, 2),
                'closing_offline' => $closingOffline,
                'closing_offline_pct' => round($pctClosingOffline, 2),
                'closing_offline_per_day' => round($dailyOffline, 2),
                'closing_tidak_kirim' => $closingTidakKirim,
                'closing_tidak_kirim_pct' => round($pctClosingTidakKirim, 2),
                'closing_tidak_kirim_per_day' => round($dailyTidakKirim, 2),
                'total_closing' => $totalClosing,
                'sepatu_masuk_online' => $sepatuMasukOnline,
                'sepatu_masuk_offline' => $sepatuMasukOffline,
                'sepatu_online_pct' => round($pctSepatuOnline, 2),
                'sepatu_offline_pct' => round($pctSepatuOffline, 2),
            ];
        }
    }

    public function render()
    {
        return view('livewire.cs.forecasting')->layout('layouts.app');
    }
}
