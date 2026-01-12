<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;

class QCController extends Controller
{
    use \App\Traits\HasStationTracking;

    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // Fetch all QC orders
        // Fetch QC (Active) AND Revision (Production with is_revising=true)
        $orders = WorkOrder::where('status', WorkOrderStatus::QC->value)
            ->orWhere(function($query) {
                $query->where('status', WorkOrderStatus::PRODUCTION->value)
                      ->where('is_revising', true);
            })
            ->with(['services'])
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        // Categorize queues (QC flows linearly but we show tabs for tracking points)
        // All orders go through all QC steps ideally, or as needed.
        // For simplified tracking, we show them in all tabs until that specific check is done?
        // Or simpler: Queues based on what is NOT done yet.

        // Helper to check if order needs QC Jahit
        $needsJahitQc = function ($order) {
            return $order->services->contains(fn($s) => 
                stripos($s->category, 'sol') !== false || 
                stripos($s->category, 'upper') !== false || 
                stripos($s->category, 'repaint') !== false
            );
        };

        // Categorize queues
        $queues = [
            // Jahit: Only if needs jahit AND not done
            'jahit' => $orders->filter(fn($o) => $needsJahitQc($o) && is_null($o->qc_jahit_completed_at)),
            // Cleanup: For all (or if we want 'smart' here too, usually all shoes need cleanup check)
            'cleanup' => $orders->filter(fn($o) => is_null($o->qc_cleanup_completed_at)),
            // Final: For all
            'final' => $orders->filter(fn($o) => is_null($o->qc_final_completed_at)),
        ];

        // Fetch Technicians by Specialization
        $techs = [
            'jahit' => User::where('role', 'technician')->where('specialization', 'Jahit')->get(),
            'cleanup' => User::where('role', 'technician')->whereIn('specialization', ['Clean Up', 'Washing'])->get(),
            'final' => User::where('role', 'technician')->where('specialization', 'PIC QC')->get(),
        ];

        // Fallback if empty specializations found
        if ($techs['jahit']->isEmpty()) $techs['jahit'] = User::where('role', 'technician')->get();
        if ($techs['cleanup']->isEmpty()) $techs['cleanup'] = User::where('role', 'technician')->get();
        if ($techs['final']->isEmpty()) $techs['final'] = User::where('role', 'technician')->get();

        // Columns
        $startedAtColumns = [
            'jahit' => 'qc_jahit_started_at',
            'cleanup' => 'qc_cleanup_started_at',
            'final' => 'qc_final_started_at',
        ];

        $byColumns = [
            'jahit' => 'qc_jahit_by',
            'cleanup' => 'qc_cleanup_by',
            'final' => 'qc_final_by',
        ];

        return view('qc.index', compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns'));
    }

    public function updateStation(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $type = $request->input('type'); // qc_jahit, qc_cleanup, qc_final
        $action = $request->input('action'); // start, finish
        $techId = Auth::id();
        $assigneeId = $request->input('technician_id');

        try {
            $this->handleStationUpdate(
                $order, 
                $type, 
                $action, 
                $techId, 
                $assigneeId, 
                WorkOrderStatus::QC->value
            );
            
            $order->save();
            
            // Auto-check final completion
            $this->checkOverallCompletion($order);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'QC Check updated.']);
            }
            return back()->with('success', 'QC Check updated.');
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('QC Update Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    protected function checkOverallCompletion($order)
    {
        // Check Needs
        $needsJahit = $order->services->contains(fn($s) => 
            stripos($s->category, 'sol') !== false || 
            stripos($s->category, 'upper') !== false || 
            stripos($s->category, 'repaint') !== false
        );

        $doneJahit = !$needsJahit || $order->qc_jahit_completed_at;
        $doneCleanup = $order->qc_cleanup_completed_at; // Assume mandatory
        $doneFinal = $order->qc_final_completed_at; // Assume mandatory

        // If all needed checks are done
        if ($doneJahit && $doneCleanup && $doneFinal) {
            // Auto finish disabled by user request
            // $this->workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'QC Passed. Order Finished.');
        }
    }

    public function finish(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        // Check Needs
        $needsJahit = $order->services->contains(fn($s) => 
            stripos($s->category, 'sol') !== false || 
            stripos($s->category, 'upper') !== false || 
            stripos($s->category, 'repaint') !== false
        );

        $doneJahit = !$needsJahit || $order->qc_jahit_completed_at;
        $doneCleanup = $order->qc_cleanup_completed_at; 
        $doneFinal = $order->qc_final_completed_at;

        if ($doneJahit && $doneCleanup && $doneFinal) {
            $this->workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'Manual QC Finish Triggered.');
            return redirect()->route('qc.index')->with('success', 'Order berhasil dipindahkan ke Finish/Pickup.');
        }

        return back()->with('error', 'QC belum lengkap.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejected_stations' => 'required|array',
            'notes' => 'required|string',
        ]);

        $order = WorkOrder::findOrFail($id);

        try {
            // Update Status to PRODUCTION (Back to Production)
            $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'QC REJECTED: ' . $request->notes);

            // Reset Production Timestamps for selected stations
            $stations = $request->rejected_stations;
            
            if (in_array('sol', $stations)) {
                $order->prod_sol_started_at = null;
                $order->prod_sol_completed_at = null;
                // Keep technician assigned or nullify? User wants to know "siapa teknisinya", so keeping it is better.
                // But logically if it's "New Job", maybe reset? 
                // Let's Keep it so it shows "Assigned to X" immediately in Production.
            }
            if (in_array('upper', $stations)) {
                $order->prod_upper_started_at = null;
                $order->prod_upper_completed_at = null;
            }
            if (in_array('cleaning', $stations)) {
                $order->prod_cleaning_started_at = null;
                $order->prod_cleaning_completed_at = null;
            }

            // Always Reset QC Timestamps because new production means new QC needed
            // But maybe keep "Started" to show it was once started? 
            // Better to clean slate for QC so they re-check properly.
            $order->qc_jahit_started_at = null;
            $order->qc_jahit_completed_at = null;
            $order->qc_cleanup_started_at = null;
            $order->qc_cleanup_completed_at = null;
            $order->qc_final_started_at = null;
            $order->qc_final_completed_at = null;
            
            // Set Revision Flag
            $order->is_revising = true;
            
            // Handle Evidence Photo
            if ($request->hasFile('evidence_photo')) {
                $file = $request->file('evidence_photo');
                $filename = 'QC_REJECT_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('photos/qc_reject', $filename, 'public');

                \App\Models\WorkOrderPhoto::create([
                    'work_order_id' => $order->id,
                    'step' => 'QC_REJECT_EVIDENCE',
                    'file_path' => $path,
                    'is_public' => true, 
                ]);
            }
            
            $order->save();

            return back()->with('success', 'Order dikembalikan ke Production (Revisi).');

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('QC Reject Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses revisi: ' . $e->getMessage());
        }
    }
    
    // Keep fail method for explicit rejection handling
    public function fail(Request $request, $id)
    {
         $order = WorkOrder::findOrFail($id);
         $reason = $request->input('note', 'QC Failed');
         
         // Handle Evidence Photo
         if ($request->hasFile('evidence_photo')) {
             $file = $request->file('evidence_photo');
             $filename = 'QC_REJECT_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
             $path = $file->storeAs('photos/qc_reject', $filename, 'public');

             \App\Models\WorkOrderPhoto::create([
                 'work_order_id' => $order->id,
                 'step' => 'QC_REJECT_EVIDENCE',
                 'file_path' => $path,
                 'is_public' => true, // Visible to technician (and maybe customer?)
             ]);
         }
         
         // Set Revision Flag
         $order->is_revising = true;
         // Reset QC timestamps to force re-check
         $order->qc_jahit_completed_at = null;
         $order->qc_cleanup_completed_at = null;
         $order->qc_final_completed_at = null;
         
         $order->save();
         
         $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'QC Failed: ' . $reason);
         return back()->with('error', 'Order returned to Production with notes.');
    }
}
