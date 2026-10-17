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
        // v2: payload memuat per_cs_kamus & harian_kamus; key baru agar cache lama (tanpa key itu) tidak terpakai
        $cacheKey = "dashboard_api_summary_v2_{$start->format('Ymd')}_{$end->format('Ymd')}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($start, $end) {
            $kamus = $this->getKamusBreakdown($start, $end);

            return [
                'global' => $this->getGlobalMetrics($start, $end),
                'per_cs' => $this->getCsPerformanceList($start, $end),
                'per_cs_kamus' => $kamus['per_cs'],
                'harian_kamus' => $kamus['harian'],
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

        // Kamus Metrik v2.0: lead (data diri customer) per created_at, tanpa baris hasil import greeting Excel.
        $leadsDataDiri = KamusMetrikQuery::leads($start, $end);

        $totalLeadsDataDiri = (clone $leadsDataDiri)->count();
        $totalLeadsConverted = (clone $leadsDataDiri)->where('cs_leads.status', 'CONVERTED')->count();

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
            
        $totalDiterimaPeriode = WorkOrder::whereBetween('entry_date', [$start, $end])
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING->value)
            ->where('status', '!=', WorkOrderStatus::BATAL->value)
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
            
        $totalSpkPrint = $lolos;

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
            // Kamus Metrik v2.0
            'total_leads' => $totalLeadsDataDiri,
            'total_leads_converted' => $totalLeadsConverted,
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

    /**
     * Kamus Metrik v2.0 per CS dan per hari. Sumber dan rumus sama dengan field global
     * (KamusMetrikQuery), sehingga Σ per CS = Σ harian = angka global.
     *
     * Atribusi CS:
     * - leads: cs_leads.cs_id (pemilik lead).
     * - spk_closing & SPK di invoice: pemilik lead asal SPK, fallback user dengan cs_code SPK.
     * - invoice: omset & jumlah SPK dibagi per SPK ke CS masing-masing; jumlah_tagihan (1 per invoice)
     *   ke CS dengan jasa bersih terbesar di invoice itu (seri: SPK pertama).
     */
    private function getKamusBreakdown(Carbon $start, Carbon $end): array
    {
        $emptyRow = ['leads' => 0, 'spk_closing' => 0, 'jumlah_tagihan' => 0, 'jumlah_spk_tagihan' => 0, 'omset_tagihan' => 0.0];

        $harian = [];
        for ($day = $start->copy()->startOfDay(); $day <= $end; $day->addDay()) {
            $harian[$day->toDateString()] = $emptyRow;
        }

        $perCs = [];
        $add = function ($csId, string $tanggal, string $field, $value) use (&$perCs, &$harian, $emptyRow) {
            $key = $csId === null ? 'none' : (int) $csId;
            $perCs[$key] = $perCs[$key] ?? $emptyRow;
            $perCs[$key][$field] += $value;
            if (isset($harian[$tanggal])) {
                $harian[$tanggal][$field] += $value;
            }
        };

        $leadRows = KamusMetrikQuery::leads($start, $end)
            ->selectRaw('cs_leads.cs_id, DATE(cs_leads.created_at) as tanggal, COUNT(*) as n')
            ->groupBy('cs_leads.cs_id', DB::raw('DATE(cs_leads.created_at)'))
            ->toBase()
            ->get();

        foreach ($leadRows as $row) {
            $add($row->cs_id, $row->tanggal, 'leads', (int) $row->n);
        }

        $spkRows = KamusMetrikQuery::spkClosing($start, $end)
            ->selectRaw(KamusMetrikQuery::SPK_CS_ID_SQL . ' as cs_id, DATE(work_orders.created_at) as tanggal')
            ->toBase()
            ->get();

        foreach ($spkRows as $row) {
            $add($row->cs_id, $row->tanggal, 'spk_closing', 1);
        }

        $invoiceSpkRows = KamusMetrikQuery::validInvoiceSpk($start, $end)
            ->orderBy('invoices.id')
            ->orderBy('work_orders.id')
            ->get()
            ->groupBy('invoice_id');

        foreach ($invoiceSpkRows as $spks) {
            $owner = $spks->first();
            foreach ($spks as $spk) {
                $add($spk->cs_id, $spk->tanggal, 'jumlah_spk_tagihan', 1);
                $add($spk->cs_id, $spk->tanggal, 'omset_tagihan', (float) $spk->jasa_bersih);
                if ((float) $spk->jasa_bersih > (float) $owner->jasa_bersih) {
                    $owner = $spk;
                }
            }
            $add($owner->cs_id, $owner->tanggal, 'jumlah_tagihan', 1);
        }

        // Satu baris per CS (semua user CS seperti per_cs, ditambah user lain yang muncul di data), lalu "Tanpa CS"
        $csUserIds = User::where(fn($q) => $q->where('access_rights', 'LIKE', '%"cs"%')->orWhere('role', 'cs'))->pluck('id');
        $userIds = $csUserIds->merge(array_filter(array_keys($perCs), 'is_int'))->unique();
        $names = User::withTrashed()->whereIn('id', $userIds)->pluck('name', 'id');

        $rows = $userIds->map(fn($id) => array_merge(
            ['cs_id' => $id, 'cs_name' => $names[$id] ?? ('User #' . $id)],
            $perCs[$id] ?? $emptyRow
        ))->sortByDesc(fn($r) => [$r['omset_tagihan'], $r['leads']])->values()->all();

        $rows[] = array_merge(['cs_id' => null, 'cs_name' => 'Tanpa CS'], $perCs['none'] ?? $emptyRow);

        $round = function (array $r) {
            $r['omset_tagihan'] = round($r['omset_tagihan'], 2);
            return $r;
        };

        return [
            'per_cs' => array_map($round, $rows),
            'harian' => array_map($round, $harian),
        ];
    }
}
