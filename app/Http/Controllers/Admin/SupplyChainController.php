<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyChainController extends Controller
{
    /**
     * Display the Supply Chain Dashboard.
     */
    public function index()
    {
        $stats = [
            'total_materials' => Material::count(),
            'low_stock_count' => Material::whereColumn('stock', '<=', 'min_stock')->count(),
            'total_valuation' => Material::sum(DB::raw('stock * price')),
            'pending_requests' => \App\Models\MaterialRequest::where('status', 'PENDING')->count(),
        ];

        // Top consumed materials (from transactions type 'OUT' in last 30 days)
        $topConsumed = MaterialTransaction::where('type', 'OUT')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('material_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('material_id')
            ->with('material')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Recent transactions
        $recentTransactions = MaterialTransaction::with(['material', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Orders waiting for materials
        $waitingOrders = WorkOrder::waitingForMaterials()
            ->with(['materials' => function($q) {
                $q->where('work_order_materials.status', 'REQUESTED');
            }])
            ->orderBy('priority', 'desc')
            ->take(10)
            ->get();

        return view('admin.supply-chain.index', compact('stats', 'topConsumed', 'recentTransactions', 'waitingOrders'));
    }

    /**
     * Display the full Transaction Ledger.
     */
    public function transactions(Request $request)
    {
        $query = MaterialTransaction::with(['material', 'user']);

        // Filters
        if ($request->material_id) {
            $query->where('material_id', $request->material_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(50)->withQueryString();
        $materials = Material::orderBy('name')->get();

        return view('admin.supply-chain.transactions', compact('transactions', 'materials'));
    }
}
