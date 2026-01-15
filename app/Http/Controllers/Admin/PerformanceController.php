<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index()
    {
        // Only get users with role 'technician' or 'pic'
        // Group by specialization for better analytics
        $users = User::whereIn('role', ['technician', 'pic'])
            ->withCount([
                'jobsSortirSol',
                'jobsSortirUpper',
                'jobsProduction',
                'jobsQcJahit',
                'jobsQcCleanup',
                'jobsQcFinal',
                'jobsProduction as complaints_count' => function ($query) {
                    $query->has('complaints');
                },
                'logs as prep_tasks_count' => function ($query) {
                    // Must match the actions logged in PreparationController
                    $query->whereIn('action', ['PREP_CLEANING_DONE', 'PREP_SOL_DONE', 'PREP_UPPER_DONE']);
                }
            ])
            ->orderBy('specialization')
            ->orderBy('name')
            ->get();

        // Group users by specialization for better display
        $usersBySpecialization = $users->groupBy('specialization');

        return view('admin.performance.index', compact('users', 'usersBySpecialization'));
    }
}
