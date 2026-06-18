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
    public $comparisonYear = null;
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

    public function updatedComparisonYear()
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

        $primaryData = $this->calculateYearData($this->selectedYear);

        if (!empty($this->comparisonYear) && $this->comparisonYear != $this->selectedYear) {
            $comparisonData = $this->calculateYearData($this->comparisonYear);
            for ($month = 0; $month < 12; $month++) {
                $primaryData[$month]['comparison'] = $comparisonData[$month];
            }
        }

        $this->monthlyData = $primaryData;
    }

    /**
     * Helper to compute all forecasting metrics for a given year
     */
    private function calculateYearData($year)
    {
        $monthlyData = [];

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $now = Carbon::now();

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
            
            // Smart days calculation
            if ($year == $now->year && $month == $now->month) {
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

            // Money Stage Metrics (Aligned with Finance Dashboard)
            $omsetTotal = \App\Models\Invoice::whereBetween('created_at', [$monthStart, $monthEnd])
                ->selectRaw('COALESCE(SUM(total_amount + shipping_cost - discount), 0) as total_invoiced')
                ->value('total_invoiced');

            $terbayar = \App\Models\InvoicePayment::where('verified', true)
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $dp = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', 'BEFORE')
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $lunasAwal = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', 'LUNAS_AWAL')
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $pelunasan = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', 'AFTER')
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $tambahJasa = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', 'TAMBAH_JASA')
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $oto = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', 'OTO')
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $ongkir = \App\Models\InvoicePayment::where('verified', true)
                ->where('type', 'ONGKIR')
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            // Percentages
            $pctTerbayar = $omsetTotal > 0 ? ($terbayar / $omsetTotal) * 100 : 0;
            $pctDp = $omsetTotal > 0 ? ($dp / $omsetTotal) * 100 : 0;
            $pctLunasAwal = $omsetTotal > 0 ? ($lunasAwal / $omsetTotal) * 100 : 0;
            $pctPelunasan = $omsetTotal > 0 ? ($pelunasan / $omsetTotal) * 100 : 0;
            $pctTambahJasa = $omsetTotal > 0 ? ($tambahJasa / $omsetTotal) * 100 : 0;
            $pctOto = $omsetTotal > 0 ? ($oto / $omsetTotal) * 100 : 0;
            $pctOngkir = $omsetTotal > 0 ? ($ongkir / $omsetTotal) * 100 : 0;

            $monthlyData[] = [
                'month_name' => $monthNames[$month] . ' ' . $year,
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
                
                // Money Stage
                'omset_total' => $omsetTotal,
                'terbayar' => $terbayar,
                'terbayar_pct' => round($pctTerbayar, 2),
                'total_dp' => $dp,
                'dp_pct' => round($pctDp, 2),
                'total_lunas_awal' => $lunasAwal,
                'lunas_awal_pct' => round($pctLunasAwal, 2),
                'total_pelunasan' => $pelunasan,
                'pelunasan_pct' => round($pctPelunasan, 2),
                'tambah_jasa' => $tambahJasa,
                'tambah_jasa_pct' => round($pctTambahJasa, 2),
                'oto' => $oto,
                'oto_pct' => round($pctOto, 2),
                'ongkir' => $ongkir,
                'ongkir_pct' => round($pctOngkir, 2),
            ];
        }

        return $monthlyData;
    }

    public function render()
    {
        return view('livewire.cs.forecasting')->layout('layouts.app');
    }
}
