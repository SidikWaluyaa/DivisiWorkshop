<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;

class ProductionLateController extends Controller
{
    /**
     * Display the Late Production monitoring page.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::productionLate();
        
        // Status Filter
        if ($request->filled('status')) {
            $status = strtoupper($request->status);
            if (in_array($status, ['LATE', 'WARNING', 'ON TRACK'])) {
                $query->having('warning_status', '=', $status);
            }
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->paginate(50)->withQueryString();
        
        return view('production.late-info', compact('orders'));
    }

    /**
     * Update the description/reason for a late production order via AJAX.
     */
    public function updateDescription(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'description' => 'nullable|string'
        ]);

        $order = WorkOrder::findOrFail($request->id);
        $order->late_description = $request->description;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Deskripsi berhasil diperbarui.'
        ]);
    }

    /**
     * JSON API for Google Sheets or other external sync tools.
     */
    public function sync(Request $request)
    {
        // Simple token security for sync
        $envToken = config('app.sync_token', 'SECRET_TOKEN_12345');
        if ($request->get('token') !== $envToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $orders = WorkOrder::productionLate()->get();

        return response()->json([
            'status' => 'success',
            'count' => $orders->count(),
            'data' => $orders->map(function($order) {
                // Ensure late_description is explicitly included or just return the model
                return $order;
            })
        ]);
    }
}
