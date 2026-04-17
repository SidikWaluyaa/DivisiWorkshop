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
        $orders = WorkOrder::with(['workOrderServices.service', 'materials'])
            ->whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL, WorkOrderStatus::DIANTAR])
            ->get();

        // 2. Initialize Groups
        $groups = [
            'Persiapan' => [
                'Washing' => ['count' => 0, 'total_hours' => 0],
                'Sol Repair' => ['count' => 0, 'total_hours' => 0],
                'Upper & Repaint' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0,
                'total_followup' => 0
            ],
            'Sortir' => [
                'Belum Request' => ['count' => 0, 'total_hours' => 0],
                'In Procurement' => ['count' => 0, 'total_hours' => 0],
                'Siap Produksi' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0,
                'total_followup' => 0
            ],
            'Produksi' => [
                'Reparasi Sol' => ['count' => 0, 'total_hours' => 0],
                'Reparasi Upper' => ['count' => 0, 'total_hours' => 0],
                'Repaint & Treatment' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0,
                'total_followup' => 0
            ],
            'Post' => [
                'QC Jahit' => ['count' => 0, 'total_hours' => 0],
                'QC Cleanup' => ['count' => 0, 'total_hours' => 0],
                'QC Final' => ['count' => 0, 'total_hours' => 0],
                'Followup' => ['count' => 0, 'total_hours' => 0],
                'total' => 0,
                'total_followup' => 0
            ]
        ];

        // 3. Process Logic
        foreach ($orders as $order) {
            $status = $order->status instanceof WorkOrderStatus ? $order->status : WorkOrderStatus::from($order->status);
            $now = Carbon::now();
            $entryTime = $order->entry_date ?? $order->waktu ?? $order->updated_at;
            
            // Ensure entryTime is not in the future to avoid negative hours
            if ($entryTime && $entryTime->isFuture()) {
                $hoursStuck = 0;
            } else {
                $hoursStuck = $entryTime ? $now->diffInHours($entryTime) : 0;
            }

            $effectiveStatus = $status;
            $isExplicitFollowup = ($status === WorkOrderStatus::CX_FOLLOWUP);
            
            if ($isExplicitFollowup && $order->previous_status) {
                $effectiveStatus = $order->previous_status instanceof WorkOrderStatus 
                    ? $order->previous_status 
                    : WorkOrderStatus::tryFrom($order->previous_status);
            }

            // Group Flagging
            $isPrep = ($effectiveStatus === WorkOrderStatus::PREPARATION);
            $isSortir = ($effectiveStatus === WorkOrderStatus::SORTIR);
            $isProduksi = ($effectiveStatus === WorkOrderStatus::PRODUCTION);
            $isPost = ($effectiveStatus === WorkOrderStatus::QC);

            if ($isPrep) {
                if ($isExplicitFollowup) {
                    $groups['Persiapan']['total_followup']++;
                    $this->incrementStage($groups['Persiapan'], 'Followup', $hoursStuck);
                } else {
                    $groups['Persiapan']['total']++;
                    if (!$order->prep_washing_completed_at) {
                        $this->incrementStage($groups['Persiapan'], 'Washing', $hoursStuck);
                    } elseif (!$order->prep_sol_completed_at) {
                        $this->incrementStage($groups['Persiapan'], 'Sol Repair', $hoursStuck);
                    } else {
                        $this->incrementStage($groups['Persiapan'], 'Upper & Repaint', $hoursStuck);
                    }
                }
            }

            elseif ($isSortir) {
                if ($isExplicitFollowup) {
                    $groups['Sortir']['total_followup']++;
                    $this->incrementStage($groups['Sortir'], 'Followup', $hoursStuck);
                } else {
                    $groups['Sortir']['total']++;
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
                if ($isExplicitFollowup) {
                    $groups['Produksi']['total_followup']++;
                    $this->incrementStage($groups['Produksi'], 'Followup', $hoursStuck);
                } else {
                    $groups['Produksi']['total']++;
                    // Station Sequential Logic
                    $hasSol = $this->hasCategory($order, ['Sol']);
                    $hasUpper = $this->hasCategory($order, ['Upper', 'Repaint', 'Jahit']);

                    if (!$order->prod_sol_completed_at && $hasSol) {
                        $this->incrementStage($groups['Produksi'], 'Reparasi Sol', $hoursStuck);
                    } elseif (!$order->prod_upper_completed_at && $hasUpper) {
                        $this->incrementStage($groups['Produksi'], 'Reparasi Upper', $hoursStuck);
                    } else {
                        $this->incrementStage($groups['Produksi'], 'Repaint & Treatment', $hoursStuck);
                    }
                }
            }

            elseif ($isPost) {
                if ($isExplicitFollowup) {
                    $groups['Post']['total_followup']++;
                    $this->incrementStage($groups['Post'], 'Followup', $hoursStuck);
                } else {
                    $groups['Post']['total']++;
                    // QC Sequential Logic
                    $needsJahit = $this->hasCategory($order, ['Sol', 'Upper', 'Repaint', 'Jahit']);

                    if (!$order->qc_jahit_completed_at && $needsJahit) {
                        $this->incrementStage($groups['Post'], 'QC Jahit', $hoursStuck);
                    } elseif (!$order->qc_cleanup_completed_at) {
                        $this->incrementStage($groups['Post'], 'QC Cleanup', $hoursStuck);
                    } else {
                        $this->incrementStage($groups['Post'], 'QC Final', $hoursStuck);
                    }
                }
            }
            
            elseif ($isExplicitFollowup) {
                $groups['Persiapan']['total_followup']++;
                $this->incrementStage($groups['Persiapan'], 'Followup', $hoursStuck);
            }
        }

        // Finalize averages and identify bottlenecks
        foreach ($groups as $name => &$data) {
            $bottleneckStage = null;
            $maxAvg = -1;

            foreach ($data as $stageKey => &$stageData) {
                if (in_array($stageKey, ['total', 'total_followup', 'bottleneck'])) continue;

                $stageData['avg_hours'] = $stageData['count'] > 0 
                    ? round($stageData['total_hours'] / $stageData['count'], 1) 
                    : 0;

                if ($stageData['avg_hours'] > $maxAvg && $stageData['count'] > 0) {
                    $maxAvg = $stageData['avg_hours'];
                    $bottleneckStage = $stageKey;
                }
            }
            $data['bottleneck'] = $bottleneckStage;
        }

        return [
            'groups' => $groups,
            'total_spk' => $orders->count()
        ];
    }

    private function hasCategory(WorkOrder $order, array $categories): bool
    {
        return $order->workOrderServices->contains(function ($ws) use ($categories) {
            $cat = strtolower($ws->category_name ?? $ws->service?->category ?? '');
            foreach ($categories as $c) {
                if (str_contains($cat, strtolower($c))) return true;
            }
            return false;
        });
    }

    private function incrementStage(array &$group, string $stage, float $hours): void
    {
        $group[$stage]['count']++;
        $group[$stage]['total_hours'] += $hours;
    }
}
