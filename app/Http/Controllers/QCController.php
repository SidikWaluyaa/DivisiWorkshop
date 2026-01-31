<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Log;
use App\Models\WorkOrderPhoto;

class QCController extends Controller
{
    use \App\Traits\HasStationTracking;

    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index(Request $request)
    {
        // Validate and sanitize activeTab
        $activeTab = $request->get('tab', 'jahit');
        $validTabs = ['jahit', 'cleanup', 'final', 'all'];
        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'jahit'; // Default fallback
        }
        
        // Base Query: Fetch QC (Active) AND Revision (Production with is_revising=true)
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::QC)
            ->orWhere(function($q) {
                $q->where('status', WorkOrderStatus::PRODUCTION)
                      ->where('is_revising', true);
            });

        // Calculate Counts (for Stats Cards) - Using Database Counts for Accuracy
        $counts = [
            'jahit' => (clone $baseQuery)->qcJahit()->whereNull('qc_jahit_completed_at')->count(),
            'cleanup' => (clone $baseQuery)->qcCleanup()->whereNull('qc_cleanup_completed_at')->count(),
            'final' => (clone $baseQuery)->qcFinal()->whereNull('qc_final_completed_at')->count(),
            'all' => (clone $baseQuery)->count()
        ];
        // Populate queues array with dummy counts for view compatibility if needed, 
        // or just rely on $orders for the active tab list.
        // The view expects $queues['jahit'] to be a collection, but we only show ONE list now based on activeTab.
        // To maintain view compatibility without massive rewrite, we can pass empty collections for inactive tabs
        // OR update the view to just iterate $orders.
        //
        // DECISION: We will update the VIEW to just use $orders, but we need to pass $queues with counts/objects
        // so the stats cards works? Actually stats update is separate.
        // Let's stick to the "Filtered Main Query" approach.

        $ordersQuery = clone $baseQuery;

        // === FILTER BY TAB ===
        switch ($activeTab) {
            case 'jahit':
                $ordersQuery->qcJahit()->whereNull('qc_jahit_completed_at');
                break;
            case 'cleanup':
                $ordersQuery->qcCleanup()->whereNull('qc_cleanup_completed_at');
                break;
            case 'final':
                $ordersQuery->qcFinal()->whereNull('qc_final_completed_at');
                break;
            case 'all':
                // Show everything, or maybe just "Ready for Review"?
                // The previous logic for 'all' showed "queueReview" which is fully finished QCs.
                // Let's assume 'all' tab implies "Review / Finished" or "All in Progress".
                // Based on UI, 'Review' section was separate.
                //$ordersQuery->qcReview(); 
                break;
        }

        // === ADVANCED FILTERS ===
        
        // Search Filter (SPK, Customer, Brand, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $ordersQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Work Status Filter
        if ($request->filled('work_status') && $request->work_status !== 'all') {
            $statusColumn = "qc_{$activeTab}";
            // Only applicable if activeTab is not 'all'
            if ($activeTab !== 'all') {
                switch ($request->work_status) {
                    case 'not_started':
                        $ordersQuery->whereNull("{$statusColumn}_by");
                        break;
                    case 'in_progress':
                        $ordersQuery->whereNotNull("{$statusColumn}_by")
                              ->whereNull("{$statusColumn}_completed_at");
                        break;
                    case 'completed':
                        $ordersQuery->whereNotNull("{$statusColumn}_completed_at");
                        break;
                }
            }
        }

        // Priority Filter
        if ($request->filled('priority') && $request->priority !== 'all') {
            if ($request->priority === 'urgent') {
                $ordersQuery->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } else {
                $ordersQuery->where('priority', 'Regular');
            }
        }

        // Technician Filter
        if ($request->filled('technician') && $request->technician !== 'all') {
            $techColumn = "qc_{$activeTab}_by";
             if ($activeTab !== 'all') {
                $ordersQuery->where($techColumn, $request->technician);
             }
        }

        $orders = $ordersQuery->with(['services', 'workOrderServices', 'materials', 'cxIssues', 'logs' => function($query) {
                $query->latest()->limit(10); // Only load last 10 logs
            }])
            ->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->paginate(200) // Increase pagination limit
            ->appends(request()->except('page'));

        // For Compatibility with View (we need to trick the view to use $orders)
        // The View iterates $queues[$activeTab]
        // So we populate $queues with the Paginated Result for the CURRENT tab
        $queues = [
            'jahit' => ($activeTab === 'jahit') ? $orders : collect([]),
            'cleanup' => ($activeTab === 'cleanup') ? $orders : collect([]),
            'final' => ($activeTab === 'final') ? $orders : collect([]),
            // 'all' is handled separately usually
        ];

        // Review Queue for Admin (Ready to Finish) - Separate Query so it doesn't interfere
        $queueReview = (clone $baseQuery)->qcReview()->get(); // Or Paginate? better get for now as it might be small.

        if ($activeTab === 'all') {
             // If tab is 'all', maybe we show the review queue as the main list?
             // Or just show all?
             $queues['all'] = $orders;
        }

        // Fetch Technicians by Specialization (select only needed columns)
        $techs = [
            'jahit' => User::where('role', 'technician')->where('specialization', 'Jahit')->select('id', 'name', 'specialization')->get(),
            'cleanup' => User::where('role', 'technician')->whereIn('specialization', ['Clean Up', 'Washing'])->select('id', 'name', 'specialization')->get(),
            'final' => User::where('role', 'technician')->where('specialization', 'PIC QC')->select('id', 'name', 'specialization')->get(),
        ];

        // Fallback if empty specializations found
        if ($techs['jahit']->isEmpty()) $techs['jahit'] = User::where('role', 'technician')->get();
        if ($techs['cleanup']->isEmpty()) $techs['cleanup'] = User::where('role', 'technician')->get();
        if ($techs['final']->isEmpty()) $techs['final'] = User::where('role', 'technician')->get();
        
        // Add 'all' key for when activeTab is 'all'
        $techs['all'] = User::where('role', 'technician')->select('id', 'name', 'specialization')->get();

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

        return view('qc.index', compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns', 'queueReview', 'activeTab', 'counts'));
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
                WorkOrderStatus::QC->value,
                $request->input('finished_at')
            );
            
            $order->save();
            
            // Auto-check final completion
            $this->checkOverallCompletion($order);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'QC Check updated.']);
            }
            return back()->with('success', 'QC Check updated.');
            
        } catch (\Throwable $e) {
            Log::error('QC Update Error: ' . $e->getMessage());
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
        // Wrapper for approve
        return $this->approve($id);
    }

    public function approve($id)
    {
        $order = WorkOrder::findOrFail($id);

        try {
            // WorkflowService handles validation (is_qc_finished)
            $this->workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'QC Approved by Admin. Order Finished.');
            
            return back()->with('success', 'QC Approved. Order selesai!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'target_status' => 'required|string',
            'target_stations' => 'nullable|array',
            'evidence_photo' => 'nullable|image|max:2048'
        ]);

        $order = WorkOrder::findOrFail($id);

        try {
            $targetStatus = WorkOrderStatus::from($request->target_status);
            $stations = $request->input('target_stations', []);

            // Handle Evidence Photo
            if ($request->hasFile('evidence_photo')) {
                $file = $request->file('evidence_photo');
                $filename = 'QC_REJECT_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('photos/qc_reject', $filename, 'public');

                WorkOrderPhoto::create([
                    'work_order_id' => $order->id,
                    'step' => 'QC_REJECT_EVIDENCE',
                    'file_path' => $path,
                    'is_public' => true, 
                ]);
            }
            
            $this->workflow->revise($order, $targetStatus, $request->reason, $stations);

            return back()->with('warning', 'Order dikembalikan ke ' . $targetStatus->label() . '.');

        } catch (\Throwable $e) {
            Log::error('QC Reject Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses revisi: ' . $e->getMessage());
        }
    }
    
    public function fail(Request $request, $id)
    {
         // Wrap fail to use reject logic for consistency
         return $this->reject($request, $id);
    }
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
            'action' => 'required|in:checked,approve,reject,start',
            'type' => 'nullable|string', // for 'checked' action (qc_jahit, qc_cleanup, etc)
            'notes' => 'nullable|string', // for rejection
            'rejected_stations' => 'nullable|array', // for rejection
            'technician_id' => 'nullable|exists:users,id'
        ]);

        $ids = $request->ids;
        $action = $request->action;
        $assigneeId = $request->input('technician_id');
        $successCount = 0;
        $failCount = 0;

        foreach ($ids as $id) {
            try {
                $order = WorkOrder::with('services')->findOrFail($id);
                
                if ($action === 'start') {
                    // Bulk Assign / Start
                     $type = $request->type;
                     if (!$type) continue;
                     
                     if (!$assigneeId) $assigneeId = Auth::id();

                     $this->handleStationUpdate(
                        $order, 
                        $type, 
                        'start', 
                        Auth::id(), 
                        $assigneeId,
                        null 
                    );
                    $order->save();
                }
                elseif ($action === 'approve') {
                    // Bulk Finish / Approve for Admin
                    $this->workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'QC Approved by Admin (Bulk).');
                } 
                elseif ($action === 'reject') {
                    // Bulk Reject
                     $stations = $request->input('rejected_stations', []);
                     $reason = $request->input('notes', 'Bulk QC Reject');

                    if (empty($stations)) {
                        // Default to resetting all if not specified, or handle error
                         $order->qc_jahit_completed_at = null;
                         $order->qc_cleanup_completed_at = null;
                         $order->qc_final_completed_at = null;
                    } else {
                         if(in_array('qc_jahit', $stations)) {
                             $order->qc_jahit_started_at = null;
                             $order->qc_jahit_completed_at = null;
                         }
                         if(in_array('qc_cleanup', $stations)) {
                             $order->qc_cleanup_started_at = null;
                             $order->qc_cleanup_completed_at = null;
                         }
                         if(in_array('qc_final', $stations)) {
                             $order->qc_final_started_at = null;
                             $order->qc_final_completed_at = null;
                         }
                    }

                    $order->is_revising = true;
                    $order->save();
                    $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'QC Failed (Bulk): ' . $reason);
                }
                elseif ($action === 'checked') {
                    // Bulk "Mark as Done" for a specific step (e.g. Jahit, Cleanup)
                    // Currently not fully supported in UI as "Approve" button, 
                    // but useful if we add "Mark Checked" button for techs.
                    // For now, let's treat this as "Mark Current Step as Done".
                    // However, we need 'type'.
                    
                    $type = $request->type;
                    if (!$type) continue; // Skip if no type

                    // Re-use handleStationUpdate logic manually
                    $actionType = 'finish';
                    $techId = Auth::id();
                    
                    $this->handleStationUpdate(
                        $order, 
                        $type, 
                        $actionType, 
                        $techId, 
                        null, 
                        WorkOrderStatus::QC->value
                    );
                    $order->save();
                    $this->checkOverallCompletion($order);
                }

                $successCount++;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Bulk QC Error ID $id: " . $e->getMessage());
                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk Action ($action) applied. Success: $successCount, Failed: $failCount"
        ]);
    }
}
