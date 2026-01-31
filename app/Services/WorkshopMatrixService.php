<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class WorkshopMatrixService
{
    /**
     * Get the Matrix Data for the Dashboard based on Process Groups.
     */
    public function getMatrixData()
    {
        // 1. Fetch Active Orders
        $orders = WorkOrder::with('services')
            ->whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL, WorkOrderStatus::DIANTAR])
            ->get();

        // 2. Initialize Groups
        $groups = [
            'Persiapan' => [
                'Cuci' => 0,
                'Bongkar Sol' => 0,
                'Bongkar Upper' => 0,
                'Revisi' => 0,
                'Followup' => 0,
                'total' => 0
            ],
            'Reparasi' => [
                'Sol Repair' => 0,
                'Upper Repair' => 0,
                'Treatment/Repaint' => 0,
                'Revisi' => 0,
                'Followup' => 0,
                'total' => 0
            ],
            'Post' => [
                'QC Jahit' => 0,
                'QC Cleanup' => 0,
                'QC Final' => 0,
                'Revisi' => 0,
                'Followup' => 0,
                'total' => 0
            ]
        ];

        // 3. Process Logic
        foreach ($orders as $order) {
            $status = $order->status instanceof WorkOrderStatus ? $order->status : WorkOrderStatus::from($order->status);
            
            // Check Overdue/Followup first (If > 3 days overdue)
            $isFollowup = false;
            if ($order->estimation_date && Carbon::now()->diffInDays($order->estimation_date, false) < -3) {
                $isFollowup = true;
            }

            // A. PERSIAPAN GROUP
            if ($status === WorkOrderStatus::PREPARATION) {
                $groups['Persiapan']['total']++;
                if ($isFollowup) {
                    $groups['Persiapan']['Followup']++;
                } elseif ($order->is_revising) {
                    $groups['Persiapan']['Revisi']++;
                } elseif (!$order->prep_washing_completed_at) {
                    $groups['Persiapan']['Cuci']++;
                } elseif (!$order->prep_sol_completed_at) {
                    $groups['Persiapan']['Bongkar Sol']++;
                } else {
                    $groups['Persiapan']['Bongkar Upper']++;
                }
            }

            // B. REPARASI GROUP (Sortir & Production)
            if ($status === WorkOrderStatus::SORTIR || $status === WorkOrderStatus::PRODUCTION) {
                $groups['Reparasi']['total']++;
                if ($isFollowup) {
                    $groups['Reparasi']['Followup']++;
                } elseif ($order->is_revising) {
                    $groups['Reparasi']['Revisi']++;
                } elseif ($status === WorkOrderStatus::SORTIR) {
                    $groups['Reparasi']['Treatment/Repaint']++; 
                } else {
                    // Split by actual production status
                    if (!$order->prod_sol_completed_at && $order->services->contains(fn($s) => str_contains(strtolower($s->category), 'sol'))) {
                        $groups['Reparasi']['Sol Repair']++;
                    } elseif (!$order->prod_upper_completed_at && $order->services->contains(fn($s) => str_contains(strtolower($s->category), 'upper'))) {
                        $groups['Reparasi']['Upper Repair']++;
                    } else {
                        $groups['Reparasi']['Treatment/Repaint']++;
                    }
                }
            }

            // C. POST GROUP (QC)
            if ($status === WorkOrderStatus::QC) {
                $groups['Post']['total']++;
                if ($isFollowup) {
                    $groups['Post']['Followup']++;
                } elseif ($order->is_revising) {
                    $groups['Post']['Revisi']++;
                } elseif (!$order->qc_jahit_completed_at) {
                    $groups['Post']['QC Jahit']++;
                } elseif (!$order->qc_cleanup_completed_at) {
                    $groups['Post']['QC Cleanup']++;
                } else {
                    $groups['Post']['QC Final']++;
                }
            }
        }

        return [
            'groups' => $groups,
            'total_spk' => $groups['Persiapan']['total'] + $groups['Reparasi']['total'] + $groups['Post']['total']
        ];
    }
}
