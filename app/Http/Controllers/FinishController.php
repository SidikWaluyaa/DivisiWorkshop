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
                    ->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
                    ->orderBy('finished_date', 'desc')
                    ->limit(100) // Prevent loading too many rows
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
                    ->orderBy('taken_date', 'desc') // Change to desc to see latest first
                    ->orderBy('id', 'desc')
                    ->limit(50) 
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

    public function destroy($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Ensure only completed/taken orders can be deleted (Safety)
        if (is_null($order->taken_date)) {
            return back()->with('error', 'Hanya data riwayat (sudah diambil) yang boleh dihapus.');
        }

        $order->delete(); // Soft Delete

        return back()->with('success', 'Data berhasil dipindahkan ke Sampah.');
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

        try {
             // 1. Attach Service
            $order->services()->attach($service->id);

            // 2. Reset Workflows
            // Logic reset timestamp is domain specific, keep it here or move to helper method.
            // For clarity, let's keep it but formatted cleanly.
            $order->finished_date = null; 
            $order->taken_date = null;
            
            // Reset QC
            $order->qc_jahit_started_at = null; $order->qc_jahit_completed_at = null;
            $order->qc_cleanup_started_at = null; $order->qc_cleanup_completed_at = null;
            $order->qc_final_started_at = null; $order->qc_final_completed_at = null;

            // Reset Production based on category
            $cat = strtolower($service->category);
            
            if (str_contains($cat, 'sol')) {
                $order->prod_sol_started_at = null; $order->prod_sol_completed_at = null;
            }
            if (str_contains($cat, 'upper') || str_contains($cat, 'jahit') || str_contains($cat, 'repaint')) {
                $order->prod_upper_started_at = null; $order->prod_upper_completed_at = null;
            }
            if (str_contains($cat, 'cleaning') || str_contains($cat, 'whitening') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment')) {
                 $order->prod_cleaning_started_at = null; $order->prod_cleaning_completed_at = null;
            }

            $order->save();

            // 3. Handle Photo (Before Status Update to keep flow logical)
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

            // 4. Use Service for Status Change
            // This handles logging and validation
            $this->workflow->updateStatus(
                $order, 
                WorkOrderStatus::PREPARATION, 
                "Added Service: {$service->name}. Resetting to Preparation."
            );
            
            return redirect()->route('finish.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function trash()
    {
        $deletedOrders = WorkOrder::onlyTrashed()
                            ->orderBy('deleted_at', 'desc')
                            ->get();

        return view('finish.trash', compact('deletedOrders'));
    }

    public function restore($id)
    {
        $order = WorkOrder::withTrashed()->findOrFail($id);
        $order->restore();

        return back()->with('success', "Order {$order->spk_number} berhasil dikembalikan (Restore).");
    }

    public function forceDelete($id)
    {
        $order = WorkOrder::withTrashed()->findOrFail($id);
        $order->forceDelete();

        return back()->with('success', "Order {$order->spk_number} berhasil dihapus permanen.");
    }
    public function sendEmail($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            if (!$order->customer_email) {
                return response()->json(['success' => false, 'message' => 'Email customer tidak tersedia.']);
            }

            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderFinished($order));

            return response()->json(['success' => true, 'message' => 'Notifikasi selesai berhasil dikirim ke email customer.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }
}
