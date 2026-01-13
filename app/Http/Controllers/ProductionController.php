<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Models\WorkOrderLog;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    use \App\Traits\HasStationTracking;

    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // Fetch all Production orders with eager loading to prevent N+1 queries
        $orders = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
            ->with(['services', 'materials', 'technicianProduction', 'logs' => function($query) {
                $query->latest()->limit(10); // Only load last 10 logs
            }])
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'asc') // Stable FIFO
            ->paginate(30) // Add pagination - 30 items per page
            ->appends(request()->except('page'));

        // Categorize into Queues based on Services
        $queues = [
            'sol' => $orders->filter(function ($order) {
                return $order->services->contains(fn($s) => stripos($s->category, 'sol') !== false);
            }),
            'upper' => $orders->filter(function ($order) {
                // Upper only
                return $order->services->contains(fn($s) => stripos($s->category, 'upper') !== false);
            }),
            'treatment' => $orders->filter(function ($order) {
                // Repaint + Cleaning + Whitening + Treatment
                return $order->services->contains(fn($s) => 
                    stripos($s->category, 'cleaning') !== false || 
                    stripos($s->category, 'whitening') !== false || 
                    stripos($s->category, 'repaint') !== false ||
                    stripos($s->category, 'treatment') !== false
                );
            }),
        ];

        // Fetch Technicians by Specialization (cache these if possible)
        $techs = [
            'sol' => User::where('role', 'technician')->where('specialization', 'Sol Repair')->select('id', 'name', 'specialization')->get(),
            'upper' => User::where('role', 'technician')->where('specialization', 'Upper Repair')->select('id', 'name', 'specialization')->get(),
            'treatment' => User::where('role', 'technician')->whereIn('specialization', ['Washing', 'Repaint', 'Treatment', 'Clean Up'])->select('id', 'name', 'specialization')->get(),
        ];

        // Define which columns check for 'start' status for each station
        $startedAtColumns = [
            'sol' => 'prod_sol_started_at',
            'upper' => 'prod_upper_started_at',
            'treatment' => 'prod_cleaning_started_at', // Reuse cleaning columns for Treatment group
        ];

        // Review Queue for Admin
        $queueReview = $orders->filter(function($order) {
            return $order->is_production_finished;
        });

        // Exclude review items from active queues if desired, OR keep them to show status.
        // Let's exclude them from active queues to declutter.
        $queues['sol'] = $queues['sol']->filter(fn($o) => !$o->is_production_finished);
        $queues['upper'] = $queues['upper']->filter(fn($o) => !$o->is_production_finished);
        $queues['treatment'] = $queues['treatment']->filter(fn($o) => !$o->is_production_finished);

        // Define which columns check for 'start' status for each station
        $startedAtColumns = [
            'sol' => 'prod_sol_started_at',
            'upper' => 'prod_upper_started_at',
            'treatment' => 'prod_cleaning_started_at', // Reuse cleaning columns for Treatment group
        ];

        // Define which columns check for 'completed' status (By user)
        $byColumns = [
            'sol' => 'prod_sol_by',
            'upper' => 'prod_upper_by',
            'treatment' => 'prod_cleaning_by',
        ];



        return view('production.index', compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns', 'queueReview'));
    }

    public function updateStation(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $type = $request->input('type'); // prod_sol, prod_upper, prod_cleaning
        $action = $request->input('action'); // start, finish
        $techId = Auth::id();
        $assigneeId = $request->input('technician_id'); // If assigning someone else

        try {
            // Apply Trait Logic
            $this->handleStationUpdate(
                $order, 
                $type, 
                $action, 
                $techId, 
                $assigneeId, 
                WorkOrderStatus::PRODUCTION->value,
                $request->input('finished_at')
            );
            
            $order->save();
            
            // Check overall progress to see if order is fully done
            $this->checkOverallCompletion($order);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Status updated.']);
            }
            return back()->with('success', 'Status updated.');
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Production Update Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    protected function checkOverallCompletion($order)
    {
        // Check if all required stations are done.
        $needsSol = $order->services->contains(fn($s) => stripos($s->category, 'sol') !== false);
        // Upper Only
        $needsUpper = $order->services->contains(fn($s) => stripos($s->category, 'upper') !== false);
        // Repaint / Treatment / Cleaning Group
        $needsTreatment = $order->services->contains(fn($s) => 
            stripos($s->category, 'cleaning') !== false || 
            stripos($s->category, 'whitening') !== false || 
            stripos($s->category, 'repaint') !== false ||
            stripos($s->category, 'treatment') !== false
        );

        $doneSol = !$needsSol || $order->prod_sol_completed_at;
        $doneUpper = !$needsUpper || $order->prod_upper_completed_at;
        // Check prod_cleaning_completed_at for the Treatment group
        $doneTreatment = !$needsTreatment || $order->prod_cleaning_completed_at;

        if ($doneSol && $doneUpper && $doneTreatment) {
            // Auto-flow disabled by user request.
            // Move to QC
            // $this->workflow->updateStatus($order, WorkOrderStatus::QC, 'Production finished (All stations). Moving to QC.');
        }
    }

    public function finish(Request $request, $id)
    {
        // Legacy method kept for backward compatibility if needed, 
        // but now we prefer 'approve' for Admin Gate.
        return $this->approve($id);
    }

    public function approve($id)
    {
        $order = WorkOrder::findOrFail($id);

        try {
            if ($order->is_revising) {
                 $order->is_revising = false;
                 $order->save();
            }

            $this->workflow->updateStatus($order, WorkOrderStatus::QC, 'Production Approved by Admin. Moving to QC.');
            
            return back()->with('success', 'Production disetujui. Order lanjut ke QC.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'target_station' => 'required|in:prod_sol,prod_upper,prod_cleaning'
        ]);

        $order = WorkOrder::findOrFail($id);
        $type = $request->target_station; // e.g., prod_sol

        // Reset Timestamp matching the type
        $order->{"{$type}_completed_at"} = null;
        $order->is_revising = true;
        $order->save();

        WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => "REJEKSI_" . strtoupper($type),
            'description' => "Revisi Production " . str_replace('prod_', '', $type) . ": " . $request->reason,
            'step' => WorkOrderStatus::PRODUCTION->value
        ]);

        return back()->with('warning', "Proses dikembalikan ke teknisi untuk revisi.");
    }
}
