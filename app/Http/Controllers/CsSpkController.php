<?php

namespace App\Http\Controllers;

use App\Models\CsSpk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CsSpkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CsSpk::with(['lead', 'customer', 'workOrder'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Metrics
        $totalSpk = $query->count();
        $totalRevenue = $query->sum('total_price');
        $waitingHandover = (clone $query)->whereNull('work_order_id')->count();

        $spks = $query->paginate(20);

        return view('cs.spk.index', compact('spks', 'totalSpk', 'totalRevenue', 'waitingHandover'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Pilih data yang akan dihapus!');
        }

        CsSpk::whereIn('id', $ids)->get()->each->delete();

        return redirect()->back()->with('success', count($ids) . ' data SPK berhasil dihapus.');
    }
}
