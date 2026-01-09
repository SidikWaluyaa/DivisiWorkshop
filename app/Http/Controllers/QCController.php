<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;

class QCController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        $queue = WorkOrder::where('status', WorkOrderStatus::QC->value)
                    ->orderBy('updated_at', 'asc')
                    ->get();

        return view('qc.index', compact('queue'));
    }

    public function show($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Check previously logged QC steps similar to Preparation
        $logs = $order->logs()->get();
        // $logs = $order->logs()->where('step', WorkOrderStatus::QC->value)->pluck('action')->toArray();
        
        $qcStartLog = $logs->where('step', WorkOrderStatus::QC->value)->where('action', 'MOVED')->first();
        $qcStartTime = $qcStartLog ? $qcStartLog->created_at : $order->updated_at;

        $trackTask = function($actionKey) use ($logs, $qcStartTime) {
            $log = $logs->where('action', $actionKey)->first();
            $done = $log !== null;
            $end = $done ? $log->created_at : null;
            $duration = $done ? $qcStartTime->diffInMinutes($end) : null;
            
            return [
                'done' => $done,
                'start' => $qcStartTime,
                'end' => $end,
                'duration' => $duration
            ];
        };
        
        // Define sub-tasks
        $subtasks = [
            'jahit' => $trackTask('QC_JAHIT_DONE'),
            'clean_up' => $trackTask('QC_CLEANUP_DONE'),
            'final' => $trackTask('QC_FINAL_DONE'),
        ];
        
        // Logic: Jahit is optional if no Sol service? 
        // For simplicity, let's say QC Manager decides manually what to check.
        // We just track status.
        
        $techJahit = \App\Models\User::where('role', 'technician')->where('specialization', 'Jahit')->get();
        $techCleanup = \App\Models\User::where('role', 'technician')->where('specialization', 'Clean Up')->get();
        $techFinal = \App\Models\User::where('role', 'technician')->where('specialization', 'PIC QC')->get();

        return view('qc.show', compact('order', 'subtasks', 'techJahit', 'techCleanup', 'techFinal'));
    }

    public function update(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $type = $request->input('type'); // jahit, clean_up, final
        
        $request->validate([
            'worker_id' => 'required|exists:users,id',
        ]);
        
        $workerId = $request->input('worker_id');

        $actionMap = [
            'jahit' => 'QC_JAHIT_DONE',
            'clean_up' => 'QC_CLEANUP_DONE',
            'final' => 'QC_FINAL_DONE'
        ];
        
        // Map types to columns
        $columnMap = [
            'jahit' => 'qc_jahit_technician_id',
            'clean_up' => 'qc_cleanup_technician_id',
            'final' => 'qc_final_pic_id'
        ];

        if (!isset($actionMap[$type])) {
            return back()->with('error', 'Invalid QC type');
        }

        // Update the specific column
        if (isset($columnMap[$type])) {
            $order->update([$columnMap[$type] => $workerId]);
        }
        
        $order->logs()->create([
            'step' => WorkOrderStatus::QC->value,
            'action' => $actionMap[$type],
            'user_id' => $workerId,
            'description' => "QC Check: " . ucfirst(str_replace('_', ' ', $type)) . " Passed."
        ]);

        return back()->with('success', 'QC Item Checked.');
    }

    public function fail(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $reason = $request->input('note', 'QC Failed');

        // Logic: Return to PRODUCTION
        // And reset status to 'PENDING'? No, just back to PRODUCTION status.
        // Also maybe clear the 'taken_date' so it appears in Queue again?
        
        $order->taken_date = null; // Reset taken date so it appears in Production Queue
        $order->save();

        $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'QC Failed: ' . $reason);

        return redirect()->route('qc.index')->with('error', 'QC Failed. Sepatu dikembalikan ke Produksi.');
    }

    public function pass($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Move to SELESAI
        $order->finished_date = now();
        $order->save();

        $this->workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'All QC Passed. Ready for Pickup.');

        return redirect()->route('qc.index')->with('success', 'QC Lolos! Sepatu siap diambil customer.');
    }
}
