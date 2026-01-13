<?php

namespace App\Traits;

use App\Models\WorkOrderLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Enums\WorkOrderStatus;

trait HasStationTracking
{
    /**
     * Handle generic station update (start/finish)
     * 
     * @param \App\Models\WorkOrder $order
     * @param string $type e.g., 'washing', 'sol', 'prod_sol', 'qc_jahit'
     * @param string $action 'start' or 'finish'
     * @param int $techId ID of the logged in user performing the action
     * @param int|null $assigneeId ID of the technician being assigned (for start action)
     * @param string $logStep The workflow step enum string/value
     * @param string|null $finishedAt Optional manual finish date (Y-m-d)
     */
    protected function handleStationUpdate($order, $type, $action, $techId, $assigneeId, $logStep, $finishedAt = null)
    {
        $now = Carbon::now();
        // Determine column prefix. 
        // If type already has 'prod_' or 'qc_', use it as is? 
        // No, current logic prepends 'prep_'. 
        // Let's standardize: we pass the full prefix or just the dynamic part?
        // Let's make the $type argument fully dynamic mapping to column name segment.
        // e.g., preparation uses 'prep_washing', 'prep_sol'.
        // Production uses 'prod_sol', 'prod_cleaning'.
        // So we should probably pass the full prefix part as $type.
        
        // However, PreparationController calls it with 'washing', 'sol' and expects 'prep_' prefix.
        // To support legacy PreparationController without major refactor, we can check.
        
        $columnPrefix = $type; 
        
        // Auto-prefix for preparation legacy calls if needed, OR just refactor PreparationController later.
        // For now, let's assume the caller passes the EXACT column prefix base.
        // e.g. 'prep_washing', 'prod_sol', 'qc_final'.
        
        if ($action === 'start') {
            if (!$assigneeId) {
                throw new \Exception('Pilih teknisi terlebih dahulu.');
            }
            $order->{"{$columnPrefix}_by"} = $assigneeId;
            $order->{"{$columnPrefix}_started_at"} = $now;
        
            $logDescription = "Memulai proses " . $this->formatStationName($type);
        } else {
            // Use manual date if provided, otherwise Use NOW
            $completionTime = $finishedAt ? Carbon::parse($finishedAt)->setTimeFrom($now) : $now;
            
            $order->{"{$columnPrefix}_completed_at"} = $completionTime;
            // Do not overwrite assigned technician if it exists
            if (!$order->{"{$columnPrefix}_by"}) {
                 $order->{"{$columnPrefix}_by"} = $techId;
            }
            
            $dateNote = $finishedAt ? " (Manual: $finishedAt)" : "";
            $logDescription = "Menyelesaikan proses " . $this->formatStationName($type) . $dateNote;
        }

        // Determine who should be logged as the actor
        $logUserId = $techId;

        if ($action === 'start' && $assigneeId) {
            $logUserId = $assigneeId; // Log the starting action as the assigned technician
        } elseif ($action === 'finish') {
            // If finishing, try to attribute to the assigned technician if they exist
            // This is useful when Admin finishes a task on behalf of a technician
            if ($order->{"{$columnPrefix}_by"}) {
                $logUserId = $order->{"{$columnPrefix}_by"};
            }
        }

        // Save log
        WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => $logUserId,
            'action' => $type . '_' . $action, // e.g. prep_washing_start
            'description' => $logDescription,
            'step' => $logStep
        ]);
    }

    protected function formatStationName($type)
    {
        // Convert snake_case like 'prod_sol' or 'prep_washing' to readable 'Production Sol' or 'Preparation Washing'
        return ucwords(str_replace('_', ' ', $type));
    }
}
