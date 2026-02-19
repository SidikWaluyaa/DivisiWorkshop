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
        $activeTab = $this->determineActiveTab($request);
        
        // 1. Get Counts
        $counts = $this->getTabCounts();

        // 2. Build Query & Apply Filters
        $query = $this->buildBaseQuery();
        $this->applyFilters($query, $request, $activeTab);
        
        // 3. Apply Tab Scoping
        $this->applyTabScope($query, $activeTab, $request);

        // 4. Pagination
        $orders = $query->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->paginate(50)
            ->appends(request()->except('page'));

        // 5. Prepare View Data
        $queues = $this->mapOrdersToTab($orders, $activeTab);
        $techs = $this->getTechniciansByRole();
        $queueReview = $this->getReviewQueue($activeTab);
        
        // Column Definitions (Static)
        $startedAtColumns = [
            'sol' => 'prod_sol_started_at', 'upper' => 'prod_upper_started_at', 'treatment' => 'prod_cleaning_started_at',
        ];
        $byColumns = [
            'sol' => 'prod_sol_by', 'upper' => 'prod_upper_by', 'treatment' => 'prod_cleaning_by',
        ];

        return view('production.index', array_merge(
            compact('orders', 'queues', 'techs', 'startedAtColumns', 'byColumns', 'activeTab', 'queueReview'),
            $counts
        ));
    }

    private function determineActiveTab(Request $request): string
    {
        $tab = $request->input('tab');
        return is_string($tab) ? $tab : 'sol';
    }

    private function getTabCounts(): array
    {
        return [
            'countSol' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionSol()->whereNull('prod_sol_completed_at')->count(),
            'countUpper' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionUpper()->whereNull('prod_upper_completed_at')->count(),
            'countTreatment' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionTreatment()->whereNull('prod_cleaning_completed_at')->count(),
            'countAll' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->count(),
        ];
    }

    private function buildBaseQuery()
    {
        return WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
            ->where('is_revising', false)
            ->with(['customer', 'services', 'workOrderServices', 'materials', 'technicianProduction', 'cxIssues', 
                    'prodSolBy', 'prodUpperBy', 'prodCleaningBy',
                    'logs' => function($query) {
                        $query->latest()->limit(10); 
                    }]);
    }

    private function applyFilters($query, Request $request, string $activeTab)
    {
        // Search Filter
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('spk_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('shoe_brand', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        });

        // Priority Filter
        $query->when($request->filled('priority') && $request->priority !== 'all', function($q) use ($request) {
            if ($request->priority === 'urgent') {
                $q->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } else {
                $q->where('priority', 'Regular');
            }
        });

        // Technician Filter
        $query->when($request->filled('technician') && $request->technician !== 'all', function($q) use ($request, $activeTab) {
            $column = match($activeTab) {
                'upper' => 'prod_upper_by',
                'treatment' => 'prod_cleaning_by',
                default => 'prod_sol_by'
            };
            $q->where($column, $request->technician);
        });
    }

    private function applyTabScope($query, string $activeTab, Request $request)
    {
        switch ($activeTab) {
            case 'sol':
                $query->productionSol();
                if (!$request->filled('search')) { 
                    $query->whereNull('prod_sol_completed_at');
                }
                break;
            case 'upper':
                $query->productionUpper();
                if (!$request->filled('search')) {
                    $query->whereNull('prod_upper_completed_at');
                }
                break;
            case 'treatment':
                $query->productionTreatment();
                if (!$request->filled('search')) {
                    $query->whereNull('prod_cleaning_completed_at');
                }
                break;
            case 'all':
                break;
        }
    }

    private function mapOrdersToTab($orders, string $activeTab): array
    {
        return [
            'sol' => $activeTab === 'sol' ? $orders : collect([]),
            'upper' => $activeTab === 'upper' ? $orders : collect([]),
            'treatment' => $activeTab === 'treatment' ? $orders : collect([]),
            'all' => $activeTab === 'all' ? $orders : collect([]),
        ];
    }

    private function getTechniciansByRole(): array
    {
        return [
            'sol' => User::where('role', 'technician')->where('specialization', 'Sol Repair')->select('id', 'name')->get(),
            'upper' => User::where('role', 'technician')->where('specialization', 'Upper Repair')->select('id', 'name')->get(),
            'treatment' => User::where('role', 'technician')->whereIn('specialization', ['Washing', 'Repaint', 'Treatment', 'Clean Up'])->select('id', 'name')->get(),
        ];
    }

    private function getReviewQueue(string $activeTab)
    {
        if ($activeTab === 'all' || str_contains($activeTab, 'review')) {
             return WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
                ->get()
                ->filter(fn($o) => $o->is_production_finished);
        }
        return collect([]);
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
            
            // SECURITY CHECK:
            $this->authorize('updateProduction', $order);
            
            $order->save();
            
            // Check overall progress to see if order is fully done
            $this->checkOverallCompletion($order);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Status updated.']);
            }
            return back()->with('success', 'Status updated.');
            
        } catch (\Throwable $e) {
            Log::error('Production Update Error: ' . $e->getMessage());
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
        // Boomerang Logic: If in revision, jump back to previous status
        if ($order->is_revising && $order->previous_status instanceof WorkOrderStatus) {
            $targetStatus = $order->previous_status;
            $statusLabel = $targetStatus->value;
            $note = "Revision completed in Production. Returning to " . $statusLabel;
            
            $this->workflow->updateStatus($order, $targetStatus, $note);

            // Clear revision flags AFTER successful transition
            $order->is_revising = false;
            $order->previous_status = null;
            $order->save();
            return;
        } 
        
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
            // SECURITY: Only authorized users can approve
            $this->authorize('approveProduction', $order);

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
            'target_status' => 'required|string',
            'target_stations' => 'nullable|array'
        ]);

        $order = WorkOrder::findOrFail($id);
        
        try {
            $targetStatus = WorkOrderStatus::from($request->target_status);
            $stations = $request->input('target_stations', []);

            // SECURITY: Only authorized users can reject
            $this->authorize('rejectProduction', $order);

            $this->workflow->revise($order, $targetStatus, $request->reason, $stations);

            return back()->with('warning', "Order direvisi ke status " . $targetStatus->label() . ".");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses revisi: ' . $e->getMessage());
        }
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
                    $this->authorize('approveProduction', $order);
                    $this->performApprove($order);
                    $successCount++;
                    continue;
                }
                
                // For regular updates
                $this->authorize('updateProduction', $order);

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
