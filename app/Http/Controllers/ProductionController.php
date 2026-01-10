<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
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
        // Fetch all Production orders
        $orders = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
            ->with(['services'])
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'asc') // Stable FIFO
            ->get();

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

        // Fetch Technicians by Specialization
        $techs = [
            'sol' => User::where('role', 'technician')->where('specialization', 'Sol Repair')->get(),
            'upper' => User::where('role', 'technician')->where('specialization', 'Upper Repair')->get(),
            'treatment' => User::where('role', 'technician')->whereIn('specialization', ['Washing', 'Repaint', 'Treatment', 'Clean Up'])->get(),
        ];

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

        return view('production.index', compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns'));
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
                WorkOrderStatus::PRODUCTION->value
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
        $order = WorkOrder::findOrFail($id);

        // Double check completion just in case
        $needsSol = $order->services->contains(fn($s) => stripos($s->category, 'sol') !== false);
        $needsUpper = $order->services->contains(fn($s) => stripos($s->category, 'upper') !== false);
        $needsTreatment = $order->services->contains(fn($s) => 
            stripos($s->category, 'cleaning') !== false || 
            stripos($s->category, 'whitening') !== false || 
            stripos($s->category, 'repaint') !== false ||
            stripos($s->category, 'treatment') !== false
        );

        $doneSol = !$needsSol || $order->prod_sol_completed_at;
        $doneUpper = !$needsUpper || $order->prod_upper_completed_at;
        $doneTreatment = !$needsTreatment || $order->prod_cleaning_completed_at;

        if (!$doneSol || !$doneUpper || !$doneTreatment) {
            return back()->with('error', 'Semua proses harus selesai sebelum dikirim ke QC.');
        }
        
        // Reset revision flag if it was revising
        if ($order->is_revising) {
            $order->is_revising = false;
            $order->save();
        }

        $this->workflow->updateStatus($order, WorkOrderStatus::QC, 'Production Manual Finish. Moving to QC.');
        return redirect()->route('production.index')->with('success', 'Order berhasil dikirim ke QC.');
    }
}
