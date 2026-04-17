<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class WorkshopMetricsService
{
    /**
     * Get real-time snapshot metrics (In Progress, Urgent, Overdue)
     */
    public function getSnapshotMetrics()
    {
        $productionStatuses = [
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ];

        $allActive = WorkOrder::whereIn('status', $productionStatuses)
            ->orWhere(function($q) use ($productionStatuses) {
                $q->where('status', WorkOrderStatus::CX_FOLLOWUP)
                  ->whereIn('previous_status', $productionStatuses);
            })
            ->get();

        return [
            'in_progress' => $allActive->count(),
            'urgent' => $allActive->filter(fn($o) => $o->days_remaining !== null && $o->days_remaining <= 3 && $o->days_remaining > 0)->count(),
            'overdue' => $allActive->filter(fn($o) => $o->is_overdue)->count(),
        ];
    }

    /**
     * Get historical metrics based on date range (Revenue, Throughput, QC Pass Rate)
     */
    public function getHistoricalMetrics(Carbon $start, Carbon $end)
    {
        $completed = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $start)
            ->whereDate('finished_date', '<=', $end->endOfDay())
            ->get();

        $throughput = $completed->count();
        $revenue = (float) $completed->sum('total_service_price');

        $avgLeadTime = 0;
        $qcPassRate = 0;

        if ($throughput > 0) {
            $totalDays = $completed->sum(fn($o) => $o->entry_date ? $o->entry_date->diffInDays($o->finished_date) : 0);
            $avgLeadTime = round($totalDays / $throughput, 1);
            $qcPassRate = round(($completed->where('is_revising', false)->count() / $throughput) * 100);
        }

        return [
            'throughput' => $throughput,
            'revenue' => $revenue,
            'avg_lead_time' => $avgLeadTime,
            'qc_pass_rate' => $qcPassRate,
        ];
    }
}
