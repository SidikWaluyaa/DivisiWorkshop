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

        // Calculate total and complaints for each user manually for maximum reliability, and attach detailed jobs
        foreach ($users as $user) {
            $user->total_jobs = $user->prep_washing_count + $user->prep_sol_count + $user->prep_upper_count +
                                $user->sortir_sol_count + $user->sortir_upper_count +
                                $user->prod_sol_count + $user->prod_upper_count + $user->prod_cleaning_count +
                                $user->qc_jahit_count + $user->qc_cleanup_count + $user->qc_final_count;

            // Fetch actual complaints count on work orders where the user was assigned as a technician
            $user->complaints_count = \App\Models\Complaint::whereHas('workOrder', function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('prep_washing_by', $user->id)
                        ->orWhere('prep_sol_by', $user->id)
                        ->orWhere('prep_upper_by', $user->id)
                        ->orWhere('pic_sortir_sol_id', $user->id)
                        ->orWhere('pic_sortir_upper_id', $user->id)
                        ->orWhere('prod_sol_by', $user->id)
                        ->orWhere('prod_upper_by', $user->id)
                        ->orWhere('prod_cleaning_by', $user->id)
                        ->orWhere('qc_jahit_by', $user->id)
                        ->orWhere('qc_cleanup_by', $user->id)
                        ->orWhere('qc_final_by', $user->id);
                });
            })->whereBetween('created_at', [$start, $end])->count();

            // Fetch detailed completed jobs list
            $completedOrders = \App\Models\WorkOrder::where(function ($q) use ($user) {
                $q->where('prep_washing_by', $user->id)
                    ->orWhere('prep_sol_by', $user->id)
                    ->orWhere('prep_upper_by', $user->id)
                    ->orWhere('pic_sortir_sol_id', $user->id)
                    ->orWhere('pic_sortir_upper_id', $user->id)
                    ->orWhere('prod_sol_by', $user->id)
                    ->orWhere('prod_upper_by', $user->id)
                    ->orWhere('prod_cleaning_by', $user->id)
                    ->orWhere('qc_jahit_by', $user->id)
                    ->orWhere('qc_cleanup_by', $user->id)
                    ->orWhere('qc_final_by', $user->id);
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
            ->with(['workOrderServices.service'])
            ->get()
            ->map(function ($wo) use ($user) {
                // Determine which station the technician completed
                $stations = [];
                if ($wo->prep_washing_by == $user->id && $wo->prep_washing_completed_at) $stations[] = 'Prep Washing';
                if ($wo->prep_sol_by == $user->id && $wo->prep_sol_completed_at) $stations[] = 'Prep Sol';
                if ($wo->prep_upper_by == $user->id && $wo->prep_upper_completed_at) $stations[] = 'Prep Upper';
                if ($wo->pic_sortir_sol_id == $user->id && $wo->status != \App\Enums\WorkOrderStatus::PREPARATION && $wo->status != \App\Enums\WorkOrderStatus::SORTIR) $stations[] = 'Sortir Sol';
                if ($wo->pic_sortir_upper_id == $user->id && $wo->status != \App\Enums\WorkOrderStatus::PREPARATION && $wo->status != \App\Enums\WorkOrderStatus::SORTIR) $stations[] = 'Sortir Upper';
                if ($wo->prod_sol_by == $user->id && $wo->prod_sol_completed_at) $stations[] = 'Prod Sol';
                if ($wo->prod_upper_by == $user->id && $wo->prod_upper_completed_at) $stations[] = 'Prod Upper';
                if ($wo->prod_cleaning_by == $user->id && $wo->prod_cleaning_completed_at) $stations[] = 'Prod Cleaning';
                if ($wo->qc_jahit_by == $user->id && $wo->qc_jahit_completed_at) $stations[] = 'QC Jahit';
                if ($wo->qc_cleanup_by == $user->id && $wo->qc_cleanup_completed_at) $stations[] = 'QC Cleanup';
                if ($wo->qc_final_by == $user->id && $wo->qc_final_completed_at) $stations[] = 'QC Final';

                return [
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'shoe' => $wo->shoe_brand . ' - ' . $wo->shoe_type,
                    'treatment' => $wo->workOrderServices->pluck('custom_service_name')->implode(', '),
                    'stations' => implode(', ', $stations),
                    'date' => $wo->updated_at->format('d M Y H:i')
                ];
            });

            $user->completed_orders_details = $completedOrders;
        }

        // Sort users by total_jobs descending for leaderboard/stats, then group by specialization
        $sortedUsersForLeaderboard = $users->sortByDesc('total_jobs')->values();
        $usersBySpecialization = $users->groupBy('specialization');

        return view('admin.performance.index', compact('users', 'usersBySpecialization', 'sortedUsersForLeaderboard', 'start', 'end'));
    }
}
