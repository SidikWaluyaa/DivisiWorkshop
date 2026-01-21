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
                'Persiapan Bahan' => 0,
                'Revisi' => 0,
                'Followup' => 0,
                'total' => 0
            ],
            'Reparasi' => [
                'Upper' => 0,
                'Sol' => 0,
                'Repaint' => 0,
                'Treatment' => 0,
                'Revisi' => 0,
                'Followup' => 0,
                'total' => 0
            ],
            'Post' => [
                'Jahit Sol' => 0,
                'Cleanup' => 0,
                'Qc' => 0,
                'Foto After' => 0,
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
                } elseif (!$order->prep_upper_completed_at) {
                    $groups['Persiapan']['Bongkar Upper']++;
                } else {
                    $groups['Persiapan']['Persiapan Bahan']++;
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
                    $groups['Reparasi']['Treatment']++; // Sortir often relates to material shopping/treatment prep
                } else {
                    // Split by service category for Production
                    $serviceCats = $order->services->pluck('category')->map(fn($c) => strtolower($c))->toArray();
                    $applied = false;
                    foreach ($serviceCats as $cat) {
                        if (str_contains($cat, 'repaint')) { $groups['Reparasi']['Repaint']++; $applied = true; break; }
                        if (str_contains($cat, 'upper')) { $groups['Reparasi']['Upper']++; $applied = true; break; }
                        if (str_contains($cat, 'sol')) { $groups['Reparasi']['Sol']++; $applied = true; break; }
                        if (str_contains($cat, 'cleaning') || str_contains($cat, 'treatment')) { $groups['Reparasi']['Treatment']++; $applied = true; break; }
                    }
                    if (!$applied) $groups['Reparasi']['Treatment']++; // Fallback
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
                    $groups['Post']['Jahit Sol']++;
                } elseif (!$order->qc_cleanup_completed_at) {
                    $groups['Post']['Cleanup']++;
                } elseif (!$order->qc_final_completed_at) {
                    $groups['Post']['Qc']++;
                } else {
                    $groups['Post']['Foto After']++;
                }
            }
        }

        return [
            'groups' => $groups,
            'total_spk' => $orders->count()
        ];
    }
}
