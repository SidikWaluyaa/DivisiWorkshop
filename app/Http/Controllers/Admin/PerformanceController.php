<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Default to current month
        $start = $startDateInput ? \Illuminate\Support\Carbon::parse($startDateInput)->startOfDay() : \Illuminate\Support\Carbon::now()->startOfMonth()->startOfDay();
        $end = $endDateInput ? \Illuminate\Support\Carbon::parse($endDateInput)->endOfDay() : \Illuminate\Support\Carbon::now()->endOfDay();

        // Get users with counts
        $users = User::whereIn('role', ['technician', 'pic'])
            ->withCount([
                'jobsPrepWashing as prep_washing_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('prep_washing_completed_at')
                          ->whereBetween('prep_washing_completed_at', [$start, $end]);
                },
                'jobsPrepSol as prep_sol_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('prep_sol_completed_at')
                          ->whereBetween('prep_sol_completed_at', [$start, $end]);
                },
                'jobsPrepUpper as prep_upper_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('prep_upper_completed_at')
                          ->whereBetween('prep_upper_completed_at', [$start, $end]);
                },
                'jobsSortirSol as sortir_sol_count' => function ($query) use ($start, $end) {
                    $query->whereIn('status', [\App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC, \App\Enums\WorkOrderStatus::SELESAI])
                          ->whereBetween('updated_at', [$start, $end]);
                },
                'jobsSortirUpper as sortir_upper_count' => function ($query) use ($start, $end) {
                    $query->whereIn('status', [\App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC, \App\Enums\WorkOrderStatus::SELESAI])
                          ->whereBetween('updated_at', [$start, $end]);
                },
                'jobsProdSol as prod_sol_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('prod_sol_completed_at')
                          ->whereBetween('prod_sol_completed_at', [$start, $end]);
                },
                'jobsProdUpper as prod_upper_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('prod_upper_completed_at')
                          ->whereBetween('prod_upper_completed_at', [$start, $end]);
                },
                'jobsProdCleaning as prod_cleaning_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('prod_cleaning_completed_at')
                          ->whereBetween('prod_cleaning_completed_at', [$start, $end]);
                },
                'qcJahitCompleted as qc_jahit_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('qc_jahit_completed_at')
                          ->whereBetween('qc_jahit_completed_at', [$start, $end]);
                },
                'qcCleanupCompleted as qc_cleanup_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('qc_cleanup_completed_at')
                          ->whereBetween('qc_cleanup_completed_at', [$start, $end]);
                },
                'qcFinalCompleted as qc_final_count' => function ($query) use ($start, $end) {
                    $query->whereNotNull('qc_final_completed_at')
                          ->whereBetween('qc_final_completed_at', [$start, $end]);
                }
            ])
            ->orderBy('specialization')
            ->orderBy('name')
            ->get();

        // Pre-fetch complaints count per technician in single query
        $complaints = \App\Models\Complaint::whereBetween('created_at', [$start, $end])
            ->with(['workOrder' => function ($q) {
                $q->select('id', 'prep_washing_by', 'prep_sol_by', 'prep_upper_by', 'pic_sortir_sol_id', 'pic_sortir_upper_id', 'prod_sol_by', 'prod_upper_by', 'prod_cleaning_by', 'qc_jahit_by', 'qc_cleanup_by', 'qc_final_by');
            }])
            ->get();

        $techComplaintCounts = [];
        foreach ($complaints as $complaint) {
            $wo = $complaint->workOrder;
            if (!$wo) continue;
            $techIds = array_filter(array_unique([
                $wo->prep_washing_by,
                $wo->prep_sol_by,
                $wo->prep_upper_by,
                $wo->pic_sortir_sol_id,
                $wo->pic_sortir_upper_id,
                $wo->prod_sol_by,
                $wo->prod_upper_by,
                $wo->prod_cleaning_by,
                $wo->qc_jahit_by,
                $wo->qc_cleanup_by,
                $wo->qc_final_by,
            ]));
            foreach ($techIds as $tId) {
                $techComplaintCounts[$tId] = ($techComplaintCounts[$tId] ?? 0) + 1;
            }
        }

        // Pre-fetch all completed work orders for technicians in single batch query
        $userIds = $users->pluck('id')->toArray();

        $allCompletedOrders = \App\Models\WorkOrder::where(function ($q) use ($userIds) {
            $q->whereIn('prep_washing_by', $userIds)
                ->orWhereIn('prep_sol_by', $userIds)
                ->orWhereIn('prep_upper_by', $userIds)
                ->orWhereIn('pic_sortir_sol_id', $userIds)
                ->orWhereIn('pic_sortir_upper_id', $userIds)
                ->orWhereIn('prod_sol_by', $userIds)
                ->orWhereIn('prod_upper_by', $userIds)
                ->orWhereIn('prod_cleaning_by', $userIds)
                ->orWhereIn('qc_jahit_by', $userIds)
                ->orWhereIn('qc_cleanup_by', $userIds)
                ->orWhereIn('qc_final_by', $userIds);
        })->where(function ($q) use ($start, $end) {
            $q->whereBetween('prep_washing_completed_at', [$start, $end])
                ->orWhereBetween('prep_sol_completed_at', [$start, $end])
                ->orWhereBetween('prep_upper_completed_at', [$start, $end])
                ->orWhereBetween('prod_sol_completed_at', [$start, $end])
                ->orWhereBetween('prod_upper_completed_at', [$start, $end])
                ->orWhereBetween('prod_cleaning_completed_at', [$start, $end])
                ->orWhereBetween('qc_jahit_completed_at', [$start, $end])
                ->orWhereBetween('qc_cleanup_completed_at', [$start, $end])
                ->orWhereBetween('qc_final_completed_at', [$start, $end])
                ->orWhere(function ($sub) use ($start, $end) {
                    $sub->whereIn('status', [\App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC, \App\Enums\WorkOrderStatus::SELESAI])
                        ->whereBetween('updated_at', [$start, $end]);
                });
        })
        ->with(['workOrderServices' => function ($q) {
            $q->select('id', 'work_order_id', 'custom_service_name');
        }])
        ->get();

        $techOrdersMap = [];
        foreach ($allCompletedOrders as $wo) {
            $treatment = $wo->workOrderServices->pluck('custom_service_name')->filter()->implode(', ');
            $date = $wo->updated_at ? $wo->updated_at->format('d M Y H:i') : '-';

            $userStations = [];
            if ($wo->prep_washing_by && $wo->prep_washing_completed_at) $userStations[$wo->prep_washing_by][] = 'Prep Washing';
            if ($wo->prep_sol_by && $wo->prep_sol_completed_at) $userStations[$wo->prep_sol_by][] = 'Prep Sol';
            if ($wo->prep_upper_by && $wo->prep_upper_completed_at) $userStations[$wo->prep_upper_by][] = 'Prep Upper';
            if ($wo->pic_sortir_sol_id && $wo->status != \App\Enums\WorkOrderStatus::PREPARATION && $wo->status != \App\Enums\WorkOrderStatus::SORTIR) $userStations[$wo->pic_sortir_sol_id][] = 'Sortir Sol';
            if ($wo->pic_sortir_upper_id && $wo->status != \App\Enums\WorkOrderStatus::PREPARATION && $wo->status != \App\Enums\WorkOrderStatus::SORTIR) $userStations[$wo->pic_sortir_upper_id][] = 'Sortir Upper';
            if ($wo->prod_sol_by && $wo->prod_sol_completed_at) $userStations[$wo->prod_sol_by][] = 'Prod Sol';
            if ($wo->prod_upper_by && $wo->prod_upper_completed_at) $userStations[$wo->prod_upper_by][] = 'Prod Upper';
            if ($wo->prod_cleaning_by && $wo->prod_cleaning_completed_at) $userStations[$wo->prod_cleaning_by][] = 'Prod Cleaning';
            if ($wo->qc_jahit_by && $wo->qc_jahit_completed_at) $userStations[$wo->qc_jahit_by][] = 'QC Jahit';
            if ($wo->qc_cleanup_by && $wo->qc_cleanup_completed_at) $userStations[$wo->qc_cleanup_by][] = 'QC Cleanup';
            if ($wo->qc_final_by && $wo->qc_final_completed_at) $userStations[$wo->qc_final_by][] = 'QC Final';

            foreach ($userStations as $tId => $stations) {
                $techOrdersMap[$tId][] = [
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe' => $wo->shoe_brand . ' - ' . $wo->shoe_type,
                    'treatment' => $treatment,
                    'stations' => implode(', ', $stations),
                    'date' => $date
                ];
            }
        }

        // Calculate total and complaints for each user, and attach pre-fetched detailed jobs
        foreach ($users as $user) {
            $user->total_jobs = $user->prep_washing_count + $user->prep_sol_count + $user->prep_upper_count +
                                $user->sortir_sol_count + $user->sortir_upper_count +
                                $user->prod_sol_count + $user->prod_upper_count + $user->prod_cleaning_count +
                                $user->qc_jahit_count + $user->qc_cleanup_count + $user->qc_final_count;

            $user->complaints_count = $techComplaintCounts[$user->id] ?? 0;
            $user->completed_orders_details = collect($techOrdersMap[$user->id] ?? []);
        }

        // Sort users by total_jobs descending for leaderboard/stats, then group by specialization
        $sortedUsersForLeaderboard = $users->sortByDesc('total_jobs')->values();
        $usersBySpecialization = $users->groupBy('specialization');

        return view('admin.performance.index', compact('users', 'usersBySpecialization', 'sortedUsersForLeaderboard', 'start', 'end'));
    }
}
