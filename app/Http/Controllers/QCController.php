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
                    ->get()
                    ->transform(function($order) {
                        // Check if it's a revision (has been in QC before)
                        // Logic: If it has entered QC (action=MOVED) more than once, it's a revision.
                        $qcEntries = $order->logs()
                            ->where('step', WorkOrderStatus::QC->value)
                            ->where('action', 'MOVED')
                            ->count();
                            
                        $order->is_revision = $qcEntries > 1; // 1 is normal (first time), >1 is revision
                        return $order;
                    })
                    ->sortByDesc('is_revision') // Revisions first
                    ->sortBy(function($order) { 
                        // Then by standard FIFO (updated_at). 
                        // But sortBy is stable? No. 
                        // Use array sort or chaining.
                        // Laravel collection sortBy is not stable for multiple keys easily?
                        // Let's us values()->sort...
                        return $order->updated_at;
                    });
                    
        // Re-sort properly: Revisions (True > False), then updated_at (Asc)
        $queue = $queue->sort(function($a, $b) {
            if ($a->is_revision === $b->is_revision) {
                return $a->updated_at <=> $b->updated_at;
            }
            return $b->is_revision <=> $a->is_revision; // True comes before False
        });

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
        $rejectedServices = $request->input('rejected_services', []);

        // 1. Update rejected services to REVISI
        if (!empty($rejectedServices)) {
            $order->services()->whereIn('service_id', $rejectedServices)->updateExistingPivot($rejectedServices, [
                'status' => 'REVISI',
                'updated_at' => now()
            ]);
            
            // Log details
            $serviceNames = $order->services()->whereIn('service_id', $rejectedServices)->pluck('name')->join(', ');
            $reason .= " (Services: $serviceNames)";
        }

        // 2. Return to PRODUCTION status
        // DO NOT reset taken_date, so it stays assigned to the technicians
        
        $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'QC Failed: ' . $reason);

        return redirect()->route('qc.index')->with('error', 'QC Failed. Sepatu dikembalikan ke Produksi untuk Revisi.');
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
