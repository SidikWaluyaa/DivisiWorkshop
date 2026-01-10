<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PreparationController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // Fetch all orders in PREPARATION status
        $allOrders = WorkOrder::where('status', WorkOrderStatus::PREPARATION->value)
                    ->with(['services', 'prepWashingBy', 'prepSolBy', 'prepUpperBy'])
                    ->orderBy('id', 'asc') // Stable FIFO
                    ->get();

        // Station 1: Washing (All orders usually need checking/cleaning, or customizable)
        // For now, assume ALL orders go through Washing station logic.
        $queueWashing = $allOrders->filter(function($order) {
            return is_null($order->prep_washing_completed_at);
        });

        // Station 2: Bongkar Sol (Only if has Reparasi Sol service AND Washing is Done)
        $queueSol = $allOrders->filter(function($order) {
            // Must be washed first
            $isWashed = !is_null($order->prep_washing_completed_at);
            return $order->needs_sol && $isWashed && is_null($order->prep_sol_completed_at);
        });

        // Station 3: Bongkar Upper (Only if has Reparasi Upper/Repaint service AND Washing is Done)
        $queueUpper = $allOrders->filter(function($order) {
            // Must be washed first
            $isWashed = !is_null($order->prep_washing_completed_at);
            return $order->needs_upper && $isWashed && is_null($order->prep_upper_completed_at);
        });

        // Calculate progress loop removed - Logic moved to WorkOrder Accessors (needs_sol, needs_upper, is_ready)
        
        $techWashing = \App\Models\User::whereIn('specialization', ['Washing', 'Treatment', 'Clean Up'])->get();
        $techSol = \App\Models\User::whereIn('specialization', ['Sol Repair', 'PIC Material Sol'])->get();
        $techUpper = \App\Models\User::whereIn('specialization', ['Upper Repair', 'Repaint', 'Jahit', 'PIC Material Upper'])->get();

        return view('preparation.index', compact('allOrders', 'queueWashing', 'queueSol', 'queueUpper', 'techWashing', 'techSol', 'techUpper'));
    }

    public function updateStation(Request $request, $id)
    {
        try {
            \Illuminate\Support\Facades\Log::info("updateStation called for Order ID: $id", $request->all());

            $order = WorkOrder::with('services')->findOrFail($id);
            $type = $request->input('type'); // washing, sol, upper
            $action = $request->input('action', 'finish'); // start, finish
            
            // For 'start', usage input technician_id. For 'finish', usage existing or current auth (fallback)
            $inputTechId = $request->input('technician_id');
            $techId = $inputTechId ?: Auth::id(); 
            
            $now = Carbon::now();

            if (!in_array($type, ['washing', 'sol', 'upper'])) {
                return response()->json(['success' => false, 'message' => 'Invalid station type'], 400);
            }

            $this->handleStationUpdate($order, $type, $action, $techId, $inputTechId);

            $order->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($type) . ($action === 'start' ? ' dimulai.' : ' selesai.'),
                    'is_ready' => $order->is_ready 
                ]);
            }

            return back()->with('success', ucfirst($type) . ' updated.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Update Station Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function handleStationUpdate($order, $type, $action, $techId, $inputTechId)
    {
        $now = Carbon::now();
        $columnPrefix = "prep_{$type}"; // prep_washing, prep_sol, prep_upper

        if ($action === 'start') {
            if (!$inputTechId) {
                throw new \Exception('Pilih teknisi terlebih dahulu.');
            }
            $order->{"{$columnPrefix}_by"} = $inputTechId;
            $order->{"{$columnPrefix}_started_at"} = $now;
        
            $logDescription = "Memulai proses " . ucfirst($type);
        } else {
            $order->{"{$columnPrefix}_completed_at"} = $now;
            // Do not overwrite assigned technician
            
            $logDescription = "Menyelesaikan proses " . ucfirst($type);
        }

        WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => $techId,
            'action' => "preparation_{$type}",
            'description' => $logDescription,
            'step' => WorkOrderStatus::PREPARATION->value
        ]);
    }

    public function finish($id)
    {
        $order = WorkOrder::with('services')->findOrFail($id);
        
        // Final check using Accessor
        if (!$order->is_ready) {
             return back()->with('error', 'Semua tahapan wajib belum selesai!');
        }

        $this->workflow->updateStatus($order, WorkOrderStatus::SORTIR, 'Preparation Completed. Proceed to Sortir.');
        
        return redirect()->route('preparation.index')->with('success', 'Preparation selesai. Order lanjut ke Sortir.');
    }
}
