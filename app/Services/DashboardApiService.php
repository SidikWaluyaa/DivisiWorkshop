<?php

namespace App\Services;

use App\Models\CsLead;
use App\Models\CsSpk;
use App\Models\CsSpkItem;
use App\Models\CsActivity;
use App\Models\User;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardApiService
{
    /**
     * Get dashboard summary metrics
     */
    public function getDashboardSummary(Carbon $start, Carbon $end, bool $forceRefresh = false)
    {
        $cacheKey = "dashboard_api_summary_{$start->format('Ymd')}_{$end->format('Ymd')}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($start, $end) {
            return [
                'global' => $this->getGlobalMetrics($start, $end),
                'per_cs' => $this->getCsPerformanceList($start, $end),
                'period' => [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ],
                'last_updated' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Calculate global metrics
     */
    private function getGlobalMetrics(Carbon $start, Carbon $end)
    {
        $totalLeads = CsLead::whereBetween('created_at', [$start, $end])->count();

        $totalClosings = CsLead::whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $totalRevenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
            ->whereNull('cs_leads.deleted_at')
            ->whereIn('cs_leads.status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
            ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
            ->whereBetween('cs_spk.created_at', [$start, $end])
            ->sum('cs_spk.total_price');

        $avgDealValue = $totalClosings > 0 ? round($totalRevenue / $totalClosings) : 0;
        $conversionRate = $totalLeads > 0 ? round(($totalClosings / $totalLeads) * 100, 1) : 0;

        $globalSpkIds = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
            ->whereBetween('cs_spk.created_at', [$start, $end])
            ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
            ->pluck('cs_spk.id');
            
        $totalIncomingItems = CsSpkItem::whereIn('spk_id', $globalSpkIds)->count();

        // In Gudang (Diterima Gudang)
        $inGudang = WorkOrder::whereBetween('entry_date', [$start, $end])
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING->value)
            ->where('status', '!=', WorkOrderStatus::BATAL->value)
            ->count();

        // Warehouse Metrics for Insight
        $totalSepatuDiRak = \App\Models\StorageRack::active()->sum('current_count');
        
        $totalFinishPeriode = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereBetween('updated_at', [$start, $end])
            ->count();
            
        $totalDiterimaPeriode = WorkOrder::where('status', WorkOrderStatus::DITERIMA)
            ->whereBetween('updated_at', [$start, $end])
            ->count();
            
        // QC Calculation (Lolos - Reject)
        $lolos = WorkOrder::where('warehouse_qc_status', 'lolos')
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->count();
            
        $reject = WorkOrder::where('warehouse_qc_status', 'reject')
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->count();
            
        $totalSpkPrint = $lolos - $reject;

        return [
            'total_closing' => $totalClosings,
            'total_sepatu_masuk' => $totalIncomingItems,
            'kalkulasi_closing' => $conversionRate . '%',
            'in_gudang' => $inGudang,
            'revenue' => (float)$totalRevenue,
            'avg_deal' => (float)$avgDealValue,
            // New Warehouse Insight Metrics
            'total_sepatu_dirak' => $totalSepatuDiRak,
            'total_sepatu_finish_periode' => $totalFinishPeriode,
            'total_sepatu_diterima_periode' => $totalDiterimaPeriode,
            'total_spk_print' => $totalSpkPrint,
        ];
    }

    /**
     * Calculate per-CS performance
     */
    private function getCsPerformanceList(Carbon $start, Carbon $end)
    {
        $csUsers = User::where(function($q) {
            $q->where('access_rights', 'LIKE', '%"cs"%')
              ->orWhere('role', 'cs');
        })->get();

        $performance = [];

        foreach ($csUsers as $user) {
            $totalLeads = CsLead::where('cs_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $totalClosing = CsLead::where('cs_id', $user->id)
                ->whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->whereBetween('updated_at', [$start, $end])
                ->count();

            // Revenue
            $revenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                ->where('cs_spk.handed_by', $user->id)
                ->whereNull('cs_leads.deleted_at')
                ->whereIn('cs_leads.status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
                ->whereBetween('cs_spk.created_at', [$start, $end])
                ->sum('cs_spk.total_price');

            $avgDealValue = $totalClosing > 0 ? round($revenue / $totalClosing) : 0;

            // Incoming Items
            $spkIds = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                ->where('cs_leads.cs_id', $user->id)
                ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
                ->whereBetween('cs_spk.created_at', [$start, $end])
                ->pluck('cs_spk.id');

            $incomingItems = CsSpkItem::whereIn('spk_id', $spkIds)->count();

            // In Gudang
            $workOrdersQuery = WorkOrder::whereBetween('entry_date', [$start, $end]);
            if (!empty($user->cs_code)) {
                $workOrdersQuery->where('spk_number', 'LIKE', '%-' . $user->cs_code);
            } else {
                $workOrdersQuery->where('created_by', $user->id);
            }
            $inGudang = (clone $workOrdersQuery)
                ->where('status', '!=', WorkOrderStatus::SPK_PENDING->value)
                ->where('status', '!=', WorkOrderStatus::BATAL->value)
                ->count();

            // Langsung (Direct Closing without Follow-up)
            $closedLeadIds = CsLead::where('cs_id', $user->id)
                ->whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->whereBetween('updated_at', [$start, $end])
                ->pluck('id');

            $viaFollowUp = CsActivity::whereIn('cs_lead_id', $closedLeadIds)
                ->where('type', CsActivity::TYPE_STATUS_CHANGE)
                ->where('content', 'LIKE', '%Status diubah ke FOLLOW_UP%')
                ->distinct('cs_lead_id')
                ->count('cs_lead_id');

            $langsung = $closedLeadIds->count() - $viaFollowUp;

            $performance[] = [
                'cs_id' => $user->id,
                'cs_name' => $user->name,
                'total_closing' => $totalClosing,
                'total_sepatu_masuk' => $incomingItems,
                'kalkulasi_closing' => ($totalLeads > 0 ? round(($totalClosing / $totalLeads) * 100, 1) : 0) . '%',
                'in_gudang' => $inGudang,
                'items_in' => $incomingItems,
                'langsung' => $langsung,
                'revenue' => (float)$revenue,
                'avg_deal' => (float)$avgDealValue,
            ];
        }

        // Sort by revenue descending
        usort($performance, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return $performance;
    }
}
