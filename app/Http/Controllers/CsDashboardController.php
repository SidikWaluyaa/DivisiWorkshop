<?php

namespace App\Http\Controllers;

use App\Models\CsLead;
use App\Models\CsSpk;
use App\Models\CsSpkItem;
use App\Models\CsActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $csId = $request->cs_id;
        $selectedCs = $csId ? User::find($csId) : null;

        // 1. Overview Metrics (4 Cards)
        $overview = $this->getOverviewMetrics($startDate, $endDate, $csId);

        // 2. Closing Path Analysis
        $pathAnalysis = $this->getPathAnalysis($startDate, $endDate, $csId);

        // 3. Pipeline Funnel
        $funnel = $this->getPipelineFunnel($startDate, $endDate, $csId);

        // 4. Response Time Analytics
        $responseTime = $this->getResponseTimeMetrics($startDate, $endDate, $csId);

        // 5. Channel Performance
        $channelStats = $this->getChannelStats($startDate, $endDate, $csId);

        // 6. Lost Analysis
        $lostAnalysis = $this->getLostAnalysis($startDate, $endDate, $csId);

        // 7. Enhanced CS KPI Leaderboard
        $csKpis = $this->getCsPerformanceList($startDate, $endDate);

        // CS Users for filter
        $csUsers = User::where('access_rights', 'LIKE', '%"cs"%')
            ->orWhere('role', 'cs')
            ->orderBy('name')
            ->get();

        return view('admin.cs.dashboard.index', compact(
            'overview',
            'pathAnalysis',
            'funnel',
            'responseTime',
            'channelStats',
            'lostAnalysis',
            'csKpis',
            'csUsers',
            'startDate',
            'endDate',
            'selectedCs'
        ));
    }

    /**
     * Section 1: Overview Metrics (4 Cards)
     */
    private function getOverviewMetrics($start, $end, $csId = null)
    {
        $totalLeads = CsLead::whereBetween('created_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->count();

        $totalClosings = CsLead::whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
            ->whereBetween('updated_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->count();

        $totalRevenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
            ->whereNull('cs_leads.deleted_at')
            ->whereIn('cs_leads.status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
            ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
            ->whereBetween('cs_spk.created_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_spk.handed_by', $csId))
            ->sum('cs_spk.total_price');

        $conversionRate = $totalLeads > 0 ? round(($totalClosings / $totalLeads) * 100, 1) : 0;
        $avgDealValue = $totalClosings > 0 ? round($totalRevenue / $totalClosings) : 0;

        // NEW: Incoming Items (Fisik Sepatu Masuk)
        $totalIncomingItems = CsSpkItem::join('cs_spk', 'cs_spk_items.spk_id', '=', 'cs_spk.id')
            ->where('cs_spk.status', CsSpk::STATUS_HANDED_TO_WORKSHOP)
            ->whereBetween('cs_spk.handed_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_spk.handed_by', $csId))
            ->count();

        return [
            'total_leads' => $totalLeads,
            'total_closings' => $totalClosings,
            'total_revenue' => $totalRevenue,
            'conversion_rate' => $conversionRate,
            'avg_deal_value' => $avgDealValue,
            'total_incoming_items' => $totalIncomingItems,
        ];
    }

    /**
     * Section 2: Closing Path Analysis
     * Tracks whether closings came from Konsultasi directly or via Follow-up
     */
    private function getPathAnalysis($start, $end, $csId = null)
    {
        // Get all leads that reached CLOSING or CONVERTED in this period
        $closedLeadIds = CsLead::whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
            ->whereBetween('updated_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->pluck('id');

        // Among those, which ones have a "Status diubah ke FOLLOW_UP" activity?
        $closedViaFollowUp = CsActivity::whereIn('cs_lead_id', $closedLeadIds)
            ->where('type', CsActivity::TYPE_STATUS_CHANGE)
            ->where('content', 'LIKE', '%Status diubah ke FOLLOW_UP%')
            ->distinct('cs_lead_id')
            ->count('cs_lead_id');

        $closedDirect = $closedLeadIds->count() - $closedViaFollowUp;

        // Total leads that ever entered Follow-up in this period
        $totalToFollowUp = CsActivity::whereHas('lead', function ($q) use ($start, $end, $csId) {
                $q->whereBetween('created_at', [$start, $end])
                  ->when($csId, fn($qq) => $qq->where('cs_id', $csId));
            })
            ->where('type', CsActivity::TYPE_STATUS_CHANGE)
            ->where('content', 'LIKE', '%Status diubah ke FOLLOW_UP%')
            ->distinct('cs_lead_id')
            ->count('cs_lead_id');

        // Currently active in Follow-up
        $activeFollowUp = CsLead::where('status', CsLead::STATUS_FOLLOW_UP)
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->count();

        // Follow-up effectiveness (% of follow-up leads that closed)
        $followUpEffectiveness = $totalToFollowUp > 0 
            ? round(($closedViaFollowUp / $totalToFollowUp) * 100, 1) 
            : 0;

        return [
            'closed_direct' => $closedDirect,
            'closed_via_followup' => $closedViaFollowUp,
            'total_to_followup' => $totalToFollowUp,
            'active_followup' => $activeFollowUp,
            'followup_effectiveness' => $followUpEffectiveness,
            'total_closed' => $closedLeadIds->count(),
        ];
    }

    /**
     * Section 3: Pipeline Funnel
     */
    private function getPipelineFunnel($start, $end, $csId = null)
    {
        $statuses = [
            'GREETING' => CsLead::STATUS_GREETING,
            'KONSULTASI' => CsLead::STATUS_KONSULTASI,
            'FOLLOW_UP' => CsLead::STATUS_FOLLOW_UP,
            'CLOSING' => CsLead::STATUS_CLOSING,
            'CONVERTED' => CsLead::STATUS_CONVERTED,
            'LOST' => CsLead::STATUS_LOST,
        ];

        $funnel = [];
        $totalCreated = CsLead::whereBetween('created_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->count();

        foreach ($statuses as $label => $status) {
            $count = CsLead::where('status', $status)
                ->whereBetween('created_at', [$start, $end])
                ->when($csId, fn($q) => $q->where('cs_id', $csId))
                ->count();
            
            $funnel[] = [
                'status' => $label,
                'count' => $count,
                'percentage' => $totalCreated > 0 ? round(($count / $totalCreated) * 100, 1) : 0,
            ];
        }

        return [
            'stages' => $funnel,
            'total_created' => $totalCreated,
        ];
    }

    /**
     * Section 4: Response Time Analytics
     */
    private function getResponseTimeMetrics($start, $end, $csId = null)
    {
        $baseQuery = CsLead::whereBetween('created_at', [$start, $end])
            ->whereNotNull('response_time_minutes')
            ->where('response_time_minutes', '>', 0)
            ->when($csId, fn($q) => $q->where('cs_id', $csId));

        $avgResponse = round((clone $baseQuery)->avg('response_time_minutes') ?? 0);
        $fastest = (clone $baseQuery)->min('response_time_minutes') ?? 0;
        $slowest = (clone $baseQuery)->max('response_time_minutes') ?? 0;
        $totalWithResponse = (clone $baseQuery)->count();

        // Distribution buckets
        $under30 = (clone $baseQuery)->where('response_time_minutes', '<', 30)->count();
        $between30and60 = (clone $baseQuery)->whereBetween('response_time_minutes', [30, 60])->count();
        $between1and3h = (clone $baseQuery)->whereBetween('response_time_minutes', [61, 180])->count();
        $over3h = (clone $baseQuery)->where('response_time_minutes', '>', 180)->count();

        return [
            'avg_minutes' => $avgResponse,
            'fastest' => $fastest,
            'slowest' => $slowest,
            'total_with_response' => $totalWithResponse,
            'distribution' => [
                ['label' => '< 30 menit', 'count' => $under30, 'color' => '#22AF85'],
                ['label' => '30-60 menit', 'count' => $between30and60, 'color' => '#FFC232'],
                ['label' => '1-3 jam', 'count' => $between1and3h, 'color' => '#F97316'],
                ['label' => '> 3 jam', 'count' => $over3h, 'color' => '#EF4444'],
            ],
        ];
    }

    /**
     * Section 5: Channel Stats
     */
    private function getChannelStats($start, $end, $csId = null)
    {
        $channels = [CsLead::CHANNEL_ONLINE, CsLead::CHANNEL_OFFLINE];
        $result = [];

        foreach ($channels as $channel) {
            $leads = CsLead::where('channel', $channel)
                ->whereBetween('created_at', [$start, $end])
                ->when($csId, fn($q) => $q->where('cs_id', $csId))
                ->count();
            
            $closings = CsLead::where('channel', $channel)
                ->whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->whereBetween('updated_at', [$start, $end])
                ->when($csId, fn($q) => $q->where('cs_id', $csId))
                ->count();

            $revenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                ->where('cs_leads.channel', $channel)
                ->whereNull('cs_leads.deleted_at')
                ->whereIn('cs_leads.status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
                ->whereBetween('cs_spk.created_at', [$start, $end])
                ->when($csId, fn($q) => $q->where('cs_spk.handed_by', $csId))
                ->sum('cs_spk.total_price');

            $conversionRate = $leads > 0 ? round(($closings / $leads) * 100, 1) : 0;

            $result[] = [
                'channel' => $channel,
                'leads' => $leads,
                'closings' => $closings,
                'revenue' => $revenue,
                'conversion_rate' => $conversionRate,
            ];
        }

        return $result;
    }

    /**
     * Section 6: Lost Analysis
     */
    private function getLostAnalysis($start, $end, $csId = null)
    {
        $totalLost = CsLead::where('status', CsLead::STATUS_LOST)
            ->whereBetween('updated_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->count();

        $totalLeads = CsLead::whereBetween('created_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->count();
        $lostRate = $totalLeads > 0 ? round(($totalLost / $totalLeads) * 100, 1) : 0;

        // Lost reasons breakdown
        $reasons = CsLead::where('status', CsLead::STATUS_LOST)
            ->whereBetween('updated_at', [$start, $end])
            ->when($csId, fn($q) => $q->where('cs_id', $csId))
            ->whereNotNull('lost_reason')
            ->where('lost_reason', '!=', '')
            ->select('lost_reason', DB::raw('count(*) as count'))
            ->groupBy('lost_reason')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();

        // Lost per CS
        $lostPerCs = CsLead::where('cs_leads.status', CsLead::STATUS_LOST)
            ->whereBetween('cs_leads.updated_at', [$start, $end])
            ->join('users', 'cs_leads.cs_id', '=', 'users.id')
            ->select('users.name as cs_name', DB::raw('count(*) as count'))
            ->groupBy('users.name')
            ->orderByDesc('count')
            ->get()
            ->toArray();

        return [
            'total_lost' => $totalLost,
            'lost_rate' => $lostRate,
            'reasons' => $reasons,
            'per_cs' => $lostPerCs,
        ];
    }

    /**
     * Section 7: Enhanced CS Performance Leaderboard
     */
    private function getCsPerformanceList($start, $end)
    {
        $csUsers = User::where('access_rights', 'LIKE', '%"cs"%')
            ->orWhere('role', 'cs')
            ->get();

        $performance = [];

        foreach ($csUsers as $user) {
            $totalLeads = CsLead::where('cs_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $leadsOnline = CsLead::where('cs_id', $user->id)
                ->where('channel', CsLead::CHANNEL_ONLINE)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            
            $leadsOffline = $totalLeads - $leadsOnline;

            // Total closings for this CS
            $totalClosing = CsLead::where('cs_id', $user->id)
                ->whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->whereBetween('updated_at', [$start, $end])
                ->count();

            // Closing path breakdown for this CS
            $closedLeadIds = CsLead::where('cs_id', $user->id)
                ->whereIn('status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->whereBetween('updated_at', [$start, $end])
                ->pluck('id');

            $closingViaFollowUp = CsActivity::whereIn('cs_lead_id', $closedLeadIds)
                ->where('type', CsActivity::TYPE_STATUS_CHANGE)
                ->where('content', 'LIKE', '%Status diubah ke FOLLOW_UP%')
                ->distinct('cs_lead_id')
                ->count('cs_lead_id');

            $closingDirect = $closedLeadIds->count() - $closingViaFollowUp;

            // Follow-up count for this CS
            $followUpCount = CsLead::where('cs_id', $user->id)
                ->where('status', CsLead::STATUS_FOLLOW_UP)
                ->count();

            // Lost count
            $lostCount = CsLead::where('cs_id', $user->id)
                ->where('status', CsLead::STATUS_LOST)
                ->whereBetween('updated_at', [$start, $end])
                ->count();

            $lostRate = $totalLeads > 0 ? round(($lostCount / $totalLeads) * 100, 1) : 0;

            // Revenue
            $revenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                ->where('cs_spk.handed_by', $user->id)
                ->whereNull('cs_leads.deleted_at')
                ->whereIn('cs_leads.status', [CsLead::STATUS_CLOSING, CsLead::STATUS_CONVERTED])
                ->where('cs_spk.status', '!=', CsSpk::STATUS_DRAFT)
                ->whereBetween('cs_spk.created_at', [$start, $end])
                ->sum('cs_spk.total_price');

            // Avg Response Time
            $avgResponseTime = CsLead::where('cs_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->whereNotNull('response_time_minutes')
                ->where('response_time_minutes', '>', 0)
                ->avg('response_time_minutes');

            $avgDealValue = $totalClosing > 0 ? round($revenue / $totalClosing) : 0;

            // NEW: Incoming Items per CS
            $incomingItems = CsSpkItem::join('cs_spk', 'cs_spk_items.spk_id', '=', 'cs_spk.id')
                ->where('cs_spk.handed_by', $user->id)
                ->where('cs_spk.status', CsSpk::STATUS_HANDED_TO_WORKSHOP)
                ->whereBetween('cs_spk.handed_at', [$start, $end])
                ->count();

            $performance[] = [
                'cs_name' => $user->name,
                'total_leads' => $totalLeads,
                'online_leads' => $leadsOnline,
                'offline_leads' => $leadsOffline,
                'closings' => $totalClosing,
                'closing_direct' => $closingDirect,
                'closing_via_followup' => $closingViaFollowUp,
                'follow_up_active' => $followUpCount,
                'lost' => $lostCount,
                'lost_rate' => $lostRate,
                'revenue' => $revenue,
                'incoming_items' => $incomingItems,
                'aio' => $totalClosing > 0 ? round($incomingItems / $totalClosing, 2) : 0,
                'avg_response_time' => round($avgResponseTime ?? 0),
                'avg_deal_value' => $avgDealValue,
                'conversion_rate' => $totalLeads > 0 ? round(($totalClosing / $totalLeads) * 100, 1) : 0,
            ];
        }

        usort($performance, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return $performance;
    }
}
