<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkshopMatrixService
{
    /**
     * Get the Matrix Data for the Dashboard based on Process Groups.
     */
    public function getMatrixData()
    {
        // 1. Fetch Active Orders
        $orders = WorkOrder::with(['services', 'materials'])
            ->whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL, WorkOrderStatus::DIANTAR])
            ->get();

        // 2. Initialize Groups
        $groups = [
            'Persiapan' => [
                'Cuci' => ['count' => 0, 'total_hours' => 0],
                'Bongkar Sol' => ['count' => 0, 'total_hours' => 0],
                'Bongkar Upper' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0
            ],
            'Sortir' => [
                'Belum Request' => ['count' => 0, 'total_hours' => 0],
                'In Procurement' => ['count' => 0, 'total_hours' => 0],
                'Siap Produksi' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0
            ],
            'Produksi' => [
                'Sol Repair' => ['count' => 0, 'total_hours' => 0],
                'Upper Repair' => ['count' => 0, 'total_hours' => 0],
                'Treatment/Repaint' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0
            ],
            'Post' => [
                'QC Jahit' => ['count' => 0, 'total_hours' => 0],
                'QC Cleanup' => ['count' => 0, 'total_hours' => 0],
                'QC Final' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0
            ]
        ];

        // 3. Process Logic
        foreach ($orders as $order) {
            $status = $order->status instanceof WorkOrderStatus ? $order->status : WorkOrderStatus::from($order->status);
            $now = Carbon::now();
            $entryTime = $order->waktu ?? $order->updated_at;
            $hoursStuck = $now->diffInHours($entryTime);

            // Determine Group Assignment logic
            // If CX_FOLLOWUP, resolve process by previous_status
            $effectiveStatus = $status;
            $isExplicitFollowup = ($status === WorkOrderStatus::CX_FOLLOWUP);
            
            if ($isExplicitFollowup && $order->previous_status) {
                $effectiveStatus = $order->previous_status instanceof WorkOrderStatus 
                    ? $order->previous_status 
                    : WorkOrderStatus::tryFrom($order->previous_status);
            }

            // A. PERSIAPAN GROUP (Only Technical Prep)
            $isPrep = ($effectiveStatus === WorkOrderStatus::PREPARATION);

            // B. SORTIR GROUP
            $isSortir = ($effectiveStatus === WorkOrderStatus::SORTIR);

            // C. PRODUKSI GROUP
            $isProduksi = ($effectiveStatus === WorkOrderStatus::PRODUCTION);

            // D. POST GROUP (QC)
            $isPost = ($effectiveStatus === WorkOrderStatus::QC);

            if ($isPrep) {
                $groups['Persiapan']['total']++;
                if ($isExplicitFollowup) {
                    $this->incrementStage($groups['Persiapan'], 'Followup', $hoursStuck);
                } elseif (!$order->prep_washing_completed_at) {
                    $this->incrementStage($groups['Persiapan'], 'Cuci', $hoursStuck);
                } elseif (!$order->prep_sol_completed_at) {
                    $this->incrementStage($groups['Persiapan'], 'Bongkar Sol', $hoursStuck);
                } else {
                    $this->incrementStage($groups['Persiapan'], 'Bongkar Upper', $hoursStuck);
                }
            }

            elseif ($isSortir) {
                $groups['Sortir']['total']++;
                if ($isExplicitFollowup) {
                    $this->incrementStage($groups['Sortir'], 'Followup', $hoursStuck);
                } else {
                    if ($order->readyForProduction()->where('work_orders.id', $order->id)->exists()) {
                        $this->incrementStage($groups['Sortir'], 'Siap Produksi', $hoursStuck);
                    } elseif ($order->waitingForMaterials()->where('work_orders.id', $order->id)->exists()) {
                        $this->incrementStage($groups['Sortir'], 'In Procurement', $hoursStuck);
                    } else {
                        $this->incrementStage($groups['Sortir'], 'Belum Request', $hoursStuck);
                    }
                }
            }

            elseif ($isProduksi) {
                $groups['Produksi']['total']++;
                if ($isExplicitFollowup) {
                    $this->incrementStage($groups['Produksi'], 'Followup', $hoursStuck);
                } else {
                    $hasSol = $order->services->contains(fn($s) => str_contains(strtolower($s->category), 'sol'));
                    $hasUpper = $order->services->contains(fn($s) => str_contains(strtolower($s->category), 'upper'));

                    if (!$order->prod_sol_completed_at && $hasSol) {
                        $this->incrementStage($groups['Produksi'], 'Sol Repair', $hoursStuck);
                    } elseif (!$order->prod_upper_completed_at && $hasUpper) {
                        $this->incrementStage($groups['Produksi'], 'Upper Repair', $hoursStuck);
                    } else {
                        $this->incrementStage($groups['Produksi'], 'Treatment/Repaint', $hoursStuck);
                    }
                }
            }

            elseif ($isPost) {
                $groups['Post']['total']++;
                if ($isExplicitFollowup) {
                    $this->incrementStage($groups['Post'], 'Followup', $hoursStuck);
                } elseif (!$order->qc_jahit_completed_at) {
                    $this->incrementStage($groups['Post'], 'QC Jahit', $hoursStuck);
                } elseif (!$order->qc_cleanup_completed_at) {
                    $this->incrementStage($groups['Post'], 'QC Cleanup', $hoursStuck);
                } else {
                    $this->incrementStage($groups['Post'], 'QC Final', $hoursStuck);
                }
            }
            
            // Fallback for CX_FOLLOWUP without valid previous_status (default to Prep if needed)
            elseif ($isExplicitFollowup) {
                $groups['Persiapan']['total']++;
                $this->incrementStage($groups['Persiapan'], 'Followup', $hoursStuck);
            }
        }

        // Finalize averages and identify bottlenecks
        foreach ($groups as $name => &$data) {
            $bottleneckStage = null;
            $maxAvg = -1;

            foreach ($data as $stageEmail => &$stageData) {
                if ($stageEmail === 'total') continue;

                $stageData['avg_hours'] = $stageData['count'] > 0 
                    ? round($stageData['total_hours'] / $stageData['count'], 1) 
                    : 0;

                if ($stageData['avg_hours'] > $maxAvg && $stageData['count'] > 0) {
                    $maxAvg = $stageData['avg_hours'];
                    $bottleneckStage = $stageEmail;
                }
            }
            $data['bottleneck'] = $bottleneckStage;
        }

        return [
            'groups' => $groups,
            'total_spk' => $orders->count()
        ];
    }

    private function incrementStage(array &$group, string $stage, int $hours): void
    {
        $group[$stage]['count']++;
        $group[$stage]['total_hours'] += $hours;
    }
}
