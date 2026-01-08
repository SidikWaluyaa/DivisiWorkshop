<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index()
    {
        $users = User::withCount([
            'jobsSortirSol',
            'jobsSortirUpper',
            'jobsProduction',
            'jobsQcJahit',
            'jobsQcCleanup',
            'jobsQcFinal',
            'logs as prep_tasks_count' => function ($query) {
                // Must match the actions logged in PreparationController
                $query->whereIn('action', ['PREP_CLEANING_DONE', 'PREP_SOL_DONE', 'PREP_UPPER_DONE']);
            }
        ])->get();

        return view('admin.performance.index', compact('users'));
    }
}
