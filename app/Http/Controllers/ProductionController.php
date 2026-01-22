<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Models\WorkOrderLog;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductionController extends Controller
{
    use \App\Traits\HasStationTracking;

    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index(Request $request)
    {
        $activeTab = $request->input('tab');
        if (!is_string($activeTab)) {
            $activeTab = 'sol';
        }
        
        // Fetch all Production orders with eager loading to prevent N+1 queries
        $orders = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
            ->with(['services', 'workOrderServices', 'materials', 'technicianProduction', 'cxIssues', 
                    'prodSolBy', 'prodUpperBy', 'prodCleaningBy',
                    'logs' => function($query) {
                $query->latest()->limit(10); // Only load last 10 logs
            }])
            // === FILTERS ===
            
            // Search Filter (SPK, Customer, Brand, Phone)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('spk_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('shoe_brand', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            // Work Status Filter
            ->when($request->filled('work_status') && $request->work_status !== 'all', function($q) use ($request, &$activeTab) {
                $statusColumn = "prod_{$activeTab}";
                
                switch ($request->work_status) {
                    case 'not_started':
                        $q->whereNull("{$statusColumn}_by");
                        break;
                    case 'in_progress':
                        $q->whereNotNull("{$statusColumn}_by")
                          ->whereNull("{$statusColumn}_completed_at");
                        break;
                    case 'completed':
                        $q->whereNotNull("{$statusColumn}_completed_at");
                        break;
                }
            })
            // Priority Filter
            ->when($request->filled('priority') && $request->priority !== 'all', function($q) use ($request) {
                if ($request->priority === 'urgent') {
                    $q->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
                } else {
                    $q->where('priority', 'Regular');
                }
            })
            // Technician Filter
            ->when($request->filled('technician') && $request->technician !== 'all', function($q) use ($request, &$activeTab) {
                $techColumn = "prod_{$activeTab}_by";
                $q->where($techColumn, $request->technician);
            })
            ->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc') // Stable FIFO
            ->paginate(100) // increase pagination limit
            ->appends(request()->except('page'));

        // Categorize into Queues based on Services
        $queues = [
            'sol' => $orders->filter(function ($order) {
                return $order->services->contains(fn($s) => 
                    stripos($s->category, 'sol') !== false || 
                    stripos($s->service_name, 'sol') !== false
                );
            }),
            'upper' => $orders->filter(function ($order) {
                // Only show if needs Upper service
                $needsUpper = $order->services->contains(fn($s) => 
                    stripos($s->category, 'upper') !== false || 
                    stripos($s->service_name, 'upper') !== false
                );
                
                if (!$needsUpper) return false;
                
                // Check if needs Sol - if yes, Sol must be completed first
                $needsSol = $order->services->contains(fn($s) => 
                    stripos($s->category, 'sol') !== false || 
                    stripos($s->service_name, 'sol') !== false
                );
                
                if ($needsSol && !$order->prod_sol_completed_at) {
                    return false; // Sol not done yet, don't show in Upper
                }
                
                return true;
            }),
            'treatment' => $orders->filter(function ($order) {
                // Only show if needs Treatment service
                $needsTreatment = $order->services->contains(fn($s) => 
                    stripos($s->category, 'cleaning') !== false || 
                    stripos($s->category, 'whitening') !== false || 
                    stripos($s->category, 'repaint') !== false ||
                    stripos($s->category, 'treatment') !== false ||
                    stripos($s->category, 'cuci') !== false ||
                    stripos($s->service_name, 'cuci') !== false ||
                    stripos($s->service_name, 'cleaning') !== false
                );
                
                if (!$needsTreatment) return false;
                
                // Check if needs Sol - if yes, Sol must be completed first
                $needsSol = $order->services->contains(fn($s) => 
                    stripos($s->category, 'sol') !== false || 
                    stripos($s->service_name, 'sol') !== false
                );
                
                if ($needsSol && !$order->prod_sol_completed_at) {
                    return false; // Sol not done yet
                }
                
                // Check if needs Upper - if yes, Upper must be completed first
                $needsUpper = $order->services->contains(fn($s) => 
                    stripos($s->category, 'upper') !== false || 
                    stripos($s->service_name, 'upper') !== false
                );
                
                if ($needsUpper && !$order->prod_upper_completed_at) {
                    return false; // Upper not done yet
                }
                
                return true;
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

        return view('production.index', compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns', 'queueReview', 'activeTab'));
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

    private function performApprove($order)
    {
         if ($order->is_revising) {
             $order->is_revising = false;
             $order->save();
        }

        $this->workflow->updateStatus($order, WorkOrderStatus::QC, 'Production Approved by Admin. Moving to QC.');
    }

    public function approve($id)
    {
        $order = WorkOrder::findOrFail($id);

        try {
            $this->performApprove($order);
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
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
            'type' => 'nullable|string', // prod_sol, prod_upper, prod_cleaning. Nullable for 'approve' action.
            'action' => 'required|in:start,finish,assign,approve',
            'technician_id' => 'nullable|exists:users,id'
        ]);

        $ids = $request->input('ids');
        $type = $request->input('type');
        $action = $request->input('action');
        $techId = Auth::id(); // Current logged in user (Admin/Tech)
        $assigneeId = $request->input('technician_id'); // Selected tech for assignment

        // For 'assign' action, we treat it like 'start' but without setting timestamp if desired, 
        // OR we just use handleStationUpdate with action='start' which sets by+started_at.
        // Let's stick to handleStationUpdate logic:
        // 'start' -> Needs technician_id (assignee). Sets started_at = now.
        // 'finish' -> Sets completed_at = now.
        
        // If action is 'assign', it maps to 'start' but we ensure technician_id is passed.
        $effectiveAction = $action === 'assign' ? 'start' : $action;

        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $order = WorkOrder::findOrFail($id);
                
                if ($action === 'approve') {
                    $this->performApprove($order);
                    $successCount++;
                    continue;
                }

                // If action is assign/start, validate we have a technician
                if ($effectiveAction === 'start' && !$assigneeId) {
                     // If bulk starting by current user?
                     $assigneeId = Auth::id(); 
                }
                
                // If action is finish, check if item is already assigned
                if ($effectiveAction === 'finish') {
                    $byColumn = "{$type}_by";
                    if (!$order->$byColumn) {
                        throw new \Exception("Item belum di-assign teknisi. Assign dulu sebelum finish.");
                    }
                }

                $this->handleStationUpdate(
                    $order, 
                    $type, 
                    $effectiveAction, 
                    $techId, 
                    $assigneeId, 
                    WorkOrderStatus::PRODUCTION->value
                );
                
                $order->save();
                
                $this->checkOverallCompletion($order);
                $successCount++;
                
            } catch (\Throwable $e) {
                $errors[] = "Order #$id: " . $e->getMessage();
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => "$successCount item berhasil diproses.",
                'errors' => $errors
            ]);
        }

        return back()->with('success', "$successCount item berhasil diproses.");
    }
}
