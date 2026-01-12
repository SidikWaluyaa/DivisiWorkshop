<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;

class FinishController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // 1. Ready for Pickup (SELESAI)
        $readyQuery = WorkOrder::where('status', WorkOrderStatus::SELESAI->value);

        if ($search) {
            $readyQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $ready = $readyQuery->with('services')
                    ->orderBy('finished_date', 'desc')
                    ->get();

        // 2. Taken/Completed History
        $historyQuery = WorkOrder::whereNotNull('taken_date');

        if ($search) {
            $historyQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $history = $historyQuery->with('services')
                    ->orderBy('taken_date', 'asc')
                    ->orderBy('id', 'asc') // Consistent ordering
                    ->limit(50) // Increased limit slightly
                    ->get();

        return view('finish.index', compact('ready', 'history'));
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

        // Delete orders where taken_date is exactly this date (ignoring time for the match logic? 
        // taken_date is datetime, so we need whereDate)
        $count = WorkOrder::whereDate('taken_date', $date)
                    ->whereNotNull('taken_date') // Safety check
                    ->delete();

        return back()->with('success', "Berhasil menghapus {$count} data riwayat pengambilan pada tanggal {$date}.");
    }

    public function show($id)
    {
        $order = WorkOrder::with([
            'services', 
            'logs', 
            'picSortirSol', 
            'picSortirUpper', 
            'technicianProduction', 
            'qcJahitTechnician', 
            'qcCleanupTechnician', 
            'qcFinalPic'
        ])->findOrFail($id);

        $services = \App\Models\Service::all();
        return view('finish.show', compact('order', 'services'));
    }

    public function pickup(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $order->taken_date = now();
        $order->save();
        
        $order->logs()->create([
            'step' => WorkOrderStatus::SELESAI->value,
            'action' => 'PICKUP',
            'user_id' => $request->user()?->id,
            'description' => 'Customer picked up the shoes.'
        ]);
        
        return back()->with('success', 'Sepatu telah diambil customer.');
    }

    public function addService(Request $request, $id)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $order = WorkOrder::findOrFail($id);
        $service = \App\Models\Service::findOrFail($request->service_id);

        // 1. Attach Service
        $order->services()->attach($service->id);

        // 2. Reset Workflows
        $order->status = WorkOrderStatus::PREPARATION->value; // Go back to Prep
        $order->finished_date = null; // Not finished anymore
        $order->taken_date = null; // Not taken anymore (if it was)
        
        // 3. Reset QC (Must be re-verified)
        $order->qc_jahit_started_at = null;
        $order->qc_jahit_completed_at = null;
        $order->qc_cleanup_started_at = null;
        $order->qc_cleanup_completed_at = null;
        $order->qc_final_started_at = null;
        $order->qc_final_completed_at = null;

        // 4. Smart Reset Production
        // based on new service category
        $cat = strtolower($service->category);
        $cat = strtolower($service->category);
        
        // --- 4a. Reset Production Timestamps ---
        if (str_contains($cat, 'sol')) {
            $order->prod_sol_started_at = null;
            $order->prod_sol_completed_at = null;
        }
        if (str_contains($cat, 'upper') || str_contains($cat, 'jahit') || str_contains($cat, 'repaint')) {
            $order->prod_upper_started_at = null;
            $order->prod_upper_completed_at = null;
        }
        if (str_contains($cat, 'cleaning') || str_contains($cat, 'whitening') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment')) {
             $order->prod_cleaning_started_at = null;
             $order->prod_cleaning_completed_at = null;
        }

        $order->save();

        // 5. Log
        $order->logs()->create([
             'step' => WorkOrderStatus::PREPARATION->value,
             'action' => 'UPSELL',
             'user_id' => $request->user()?->id,
             'description' => "Added Service: {$service->name}. Order reset to PREPARATION."
        ]);

        // 6. Handle Photo Upload
        if ($request->hasFile('upsell_photo')) {
            $file = $request->file('upsell_photo');
            $filename = 'UPSELL_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos/upsell', $filename, 'public');

            \App\Models\WorkOrderPhoto::create([
                'work_order_id' => $order->id,
                'step' => 'UPSELL_BEFORE',
                'file_path' => $path,
                'is_public' => true,
            ]);
        }

        return redirect()->route('finish.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');
    }
}
