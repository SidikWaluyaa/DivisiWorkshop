<?php

namespace App\Http\Controllers;

use App\Models\CsLead;
use App\Models\CsSpk;
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

        // 1. Overview Metrics (Cards)
        $overview = $this->getOverviewMetrics($startDate, $endDate);

        // 2. Channel Performance (Online vs Offline)
        $channelStats = $this->getChannelStats($startDate, $endDate);

        // 3. individual CS KPI Leaderboard
        $csKpis = $this->getCsPerformanceList($startDate, $endDate);

        // Filter for specific CS if needed (Admin only)
        $csUsers = User::where('access_rights', 'LIKE', '%"cs"%')
            ->orWhere('role', 'cs')
            ->orderBy('name')
            ->get();

        return view('admin.cs.dashboard.index', compact(
            'overview',
            'channelStats',
            'csKpis',
            'csUsers',
            'startDate',
            'endDate'
        ));
    }

    private function getOverviewMetrics($start, $end)
    {
        $totalLeads = CsLead::whereBetween('created_at', [$start, $end])->count();
        $totalClosings = CsLead::where('status', CsLead::STATUS_CLOSING)
            ->whereBetween('updated_at', [$start, $end])
            ->count();
        
        $totalRevenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
            ->where('cs_leads.status', CsLead::STATUS_CLOSING)
            ->whereBetween('cs_spk.created_at', [$start, $end])
            ->sum('cs_spk.total_price');

        $conversionRate = $totalLeads > 0 ? round(($totalClosings / $totalLeads) * 100, 1) : 0;

        return [
            'total_leads' => $totalLeads,
            'total_closings' => $totalClosings,
            'total_revenue' => $totalRevenue,
            'conversion_rate' => $conversionRate
        ];
    }

    private function getChannelStats($start, $end)
    {
        $channels = [CsLead::CHANNEL_ONLINE, CsLead::CHANNEL_OFFLINE];
        $result = [];

        foreach ($channels as $channel) {
            $leads = CsLead::where('channel', $channel)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            
            $revenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                ->where('cs_leads.channel', $channel)
                ->where('cs_leads.status', CsLead::STATUS_CLOSING)
                ->whereBetween('cs_spk.created_at', [$start, $end])
                ->sum('cs_spk.total_price');

            $result[] = [
                'channel' => $channel,
                'leads' => $leads,
                'revenue' => $revenue
            ];
        }

        return $result;
    }

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

            $totalClosing = CsLead::where('cs_id', $user->id)
                ->where('status', CsLead::STATUS_CLOSING)
                ->whereBetween('updated_at', [$start, $end])
                ->count();

            $revenue = CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
                ->where('cs_spk.handed_by', $user->id)
                ->where('cs_leads.status', CsLead::STATUS_CLOSING)
                ->whereBetween('cs_spk.created_at', [$start, $end])
                ->sum('cs_spk.total_price');

            $performance[] = [
                'cs_name' => $user->name,
                'total_leads' => $totalLeads,
                'online_leads' => $leadsOnline,
                'offline_leads' => $leadsOffline,
                'closings' => $totalClosing,
                'revenue' => $revenue,
                'conversion_rate' => $totalLeads > 0 ? round(($totalClosing / $totalLeads) * 100, 1) : 0,
            ];
        }

        usort($performance, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return $performance;
    }
}
