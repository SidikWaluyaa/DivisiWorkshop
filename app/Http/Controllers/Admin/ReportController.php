<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Financial Summary
        // Revenue: Based on TAKEN/FINISHED orders in range
        $revenue = WorkOrder::whereNotNull('taken_date')
            ->whereBetween('taken_date', ["$startDate 00:00:00", "$endDate 23:59:59"])
            ->get()
            ->sum('total_price');

        // Expenses: Based on Purchases made in range
        $expenses = Purchase::whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"])
            ->sum('total_price');

        $profit = $revenue - $expenses;

        // Productivity Summary (Top 5 Technicians)
        $topTechnicians = User::withCount(['logs' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"]);
        }])
        ->orderBy('logs_count', 'desc')
        ->limit(5)
        ->get();

        return view('admin.reports.index', compact(
            'startDate', 'endDate', 'revenue', 'expenses', 'profit', 'topTechnicians'
        ));
    }

    public function exportFinancial(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $orders = WorkOrder::whereNotNull('taken_date')
            ->whereBetween('taken_date', ["$startDate 00:00:00", "$endDate 23:59:59"])
            ->with(['services'])
            ->orderBy('taken_date', 'asc')
            ->get();

        $purchases = Purchase::whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"])
            ->with(['material'])
            ->orderBy('created_at', 'asc')
            ->get();

        $summary = [
            'total_revenue' => $orders->sum('total_price'),
            'total_expense' => $purchases->sum('total_price'),
            'net_profit' => $orders->sum('total_price') - $purchases->sum('total_price'),
        ];

        $rangeLabel = Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');

        $pdf = Pdf::loadView('admin.reports.financial_pdf', compact('orders', 'purchases', 'summary', 'rangeLabel'));
        return $pdf->stream('Laporan_Keuangan_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    public function exportProductivity(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Detailed productivity per user
        // We'll count specific actions for each workshop station
        $users = User::withCount([
            'jobsSortirSol as sortir_sol_count' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('updated_at', ["$startDate 00:00:00", "$endDate 23:59:59"])
                  ->wherePivot('status', 'READY');
            },
            'jobsProduction as production_count' => function ($q) use ($startDate, $endDate) {
                 // Production is complex, let's count completed items
                 $q->whereBetween('prod_sol_completed_at', ["$startDate 00:00:00", "$endDate 23:59:59"])
                   ->orWhereBetween('prod_upper_completed_at', ["$startDate 00:00:00", "$endDate 23:59:59"])
                   ->orWhereBetween('prod_cleaning_completed_at', ["$startDate 00:00:00", "$endDate 23:59:59"]);
            },
            'jobsQcFinal as qc_count' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('qc_final_completed_at', ["$startDate 00:00:00", "$endDate 23:59:59"]);
            }
        ])->get();

        $rangeLabel = Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');

        $pdf = Pdf::loadView('admin.reports.productivity_pdf', compact('users', 'rangeLabel'));
        return $pdf->stream('Laporan_Produktivitas_' . $startDate . '_to_' . $endDate . '.pdf');
    }
}
