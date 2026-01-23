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

    public function index(Request $request)
    {
        // Validate and sanitize activeTab to prevent invalid array access
        $activeTab = $request->get('tab', 'jahit');
        $validTabs = ['jahit', 'cleanup', 'final', 'all'];
        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'jahit'; // Default fallback
        }
        
        // Fetch all QC orders with eager loading to prevent N+1 queries
        // Fetch QC (Active) AND Revision (Production with is_revising=true)
        $query = WorkOrder::where('status', WorkOrderStatus::QC->value)
            ->orWhere(function($q) {
                $q->where('status', WorkOrderStatus::PRODUCTION->value)
                      ->where('is_revising', true);
            });

        // === FILTERS ===
        
        // Search Filter (SPK, Customer, Brand, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Work Status Filter
        if ($request->filled('work_status') && $request->work_status !== 'all') {
            $statusColumn = "qc_{$activeTab}";
            
            switch ($request->work_status) {
                case 'not_started':
                    $query->whereNull("{$statusColumn}_by");
                    break;
                case 'in_progress':
                    $query->whereNotNull("{$statusColumn}_by")
                          ->whereNull("{$statusColumn}_completed_at");
                    break;
                case 'completed':
                    $query->whereNotNull("{$statusColumn}_completed_at");
                    break;
            }
        }

        // Priority Filter
        if ($request->filled('priority') && $request->priority !== 'all') {
            if ($request->priority === 'urgent') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } else {
                $query->where('priority', 'Regular');
            }
        }

        // Technician Filter
        if ($request->filled('technician') && $request->technician !== 'all') {
            $techColumn = "qc_{$activeTab}_by";
            $query->where($techColumn, $request->technician);
        }

        $orders = $query->with(['services', 'workOrderServices', 'materials', 'cxIssues', 'logs' => function($query) {
                $query->latest()->limit(10); // Only load last 10 logs
            }])
            ->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->paginate(100) // Increase pagination limit
            ->appends(request()->except('page'));

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

        // Determine if we are filtering for specific status
        $filterStatus = $request->get('work_status', 'all');

        // Categorize queues with DYNAMIC LOGIC based on Filter
        $queues = [
            'jahit' => $orders->filter(function($o) use ($needsJahitQc, $filterStatus) {
                if (!$needsJahitQc($o)) return false;
                
                // If specific filter is applied, trust the query (which already filtered by status)
                // Just ensure it belongs to this station context
                if ($filterStatus && $filterStatus !== 'all') {
                    // For 'completed', we want items DONE in this station
                    if ($filterStatus === 'completed') return !is_null($o->qc_jahit_completed_at);
                    
                    // For 'in_progress', we want items STARTED but not DONE
                    if ($filterStatus === 'in_progress') return !is_null($o->qc_jahit_started_at) && is_null($o->qc_jahit_completed_at);

                     // For 'not_started', we want items NOT STARTED
                    if ($filterStatus === 'not_started') return is_null($o->qc_jahit_by);
                }
                
                // Default Queue Logic (Active/Pending Items)
                return is_null($o->qc_jahit_completed_at);
            }),
            
            'cleanup' => $orders->filter(function($o) use ($needsJahitQc, $filterStatus) {
                 // Check prerequisites if NO filter is active (strict queue mode)
                 $prereqMet = !$needsJahitQc($o) || !is_null($o->qc_jahit_completed_at);
                 
                 // If filtering, we might want to see cleanup items even if previous steps aren't "officially" done 
                 // (though the query usually handles this via status).
                 // Let's stick to the station-specific check.
                 
                if ($filterStatus && $filterStatus !== 'all') {
                    if ($filterStatus === 'completed') return !is_null($o->qc_cleanup_completed_at);
                    if ($filterStatus === 'in_progress') return !is_null($o->qc_cleanup_started_at) && is_null($o->qc_cleanup_completed_at);
                    if ($filterStatus === 'not_started') return is_null($o->qc_cleanup_by);
                }

                 return is_null($o->qc_cleanup_completed_at) && $prereqMet;
            }),
            
            'final' => $orders->filter(function($o) use ($filterStatus) {
                $prereqMet = !is_null($o->qc_cleanup_completed_at);

                if ($filterStatus && $filterStatus !== 'all') {
                    if ($filterStatus === 'completed') return !is_null($o->qc_final_completed_at);
                    if ($filterStatus === 'in_progress') return !is_null($o->qc_final_started_at) && is_null($o->qc_final_completed_at);
                    if ($filterStatus === 'not_started') return is_null($o->qc_final_by);
                }
                
                return is_null($o->qc_final_completed_at) && $prereqMet;
            }),
        ];

        // Review Queue for Admin (Ready to Finish)
        $queueReview = $orders->filter(function($order) {
            return $order->is_qc_finished;
        });

        // Optional: Exclude reviewed items from active queues
        $queues['jahit'] = $queues['jahit']->filter(fn($o) => !$o->is_qc_finished);
        $queues['cleanup'] = $queues['cleanup']->filter(fn($o) => !$o->is_qc_finished);
        $queues['final'] = $queues['final']->filter(fn($o) => !$o->is_qc_finished);

        // Fetch Technicians by Specialization

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

        return view('qc.index', compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns', 'queueReview', 'activeTab'));
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
            'rejected_stations' => 'required|array',
            'notes' => 'required|string',
        ]);

        $order = WorkOrder::findOrFail($id);

        try {
            $stations = $request->rejected_stations;
            
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
         
         // Reset started_at timestamps for QC steps to ensure full process restart consistent with Production
         $order->qc_jahit_started_at = null;
         $order->qc_cleanup_started_at = null;
         $order->qc_final_started_at = null;
         
         $order->save();
         
         $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'QC Failed: ' . $reason);
         return back()->with('error', 'Order returned to Production with notes.');
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
