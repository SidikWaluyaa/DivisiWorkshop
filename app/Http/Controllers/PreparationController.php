<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PreparationController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'washing');

        // Base Query
    $baseQuery = WorkOrder::where('status', WorkOrderStatus::PREPARATION->value);

    // Helper queries for "Needs Sol" and "Needs Upper"
    $solQuery = function($q) {
        $q->whereHas('services', function($query) {
            $query->where('category', 'like', '%Sol%')
                  ->orWhere('name', 'like', '%Sol%');
        });
    };
    
    $upperQuery = function($q) {
        $q->whereHas('services', function($query) {
            $query->where('category', 'like', '%Upper%')
                  ->orWhere('name', 'like', '%Upper%')
                  ->orWhere('category', 'like', '%Repaint%')
                  ->orWhere('name', 'like', '%Repaint%')
                  ->orWhere('category', 'like', '%Jahit%')
                  ->orWhere('name', 'like', '%Jahit%');
        });
    };

    // Calculate Counts
    $counts = [
        'washing' => (clone $baseQuery)->whereNull('prep_washing_completed_at')->count(),
        
        
        'sol' => (clone $baseQuery)->where($solQuery)
                    ->whereNotNull('prep_washing_completed_at')
                    ->whereNull('prep_sol_completed_at')
                    ->count(),
                    
        'upper' => (clone $baseQuery)->where($upperQuery)
                    ->whereNotNull('prep_washing_completed_at')
                    // Only show in Upper tab if Sol is done (or not needed)
                    ->where(function($q) use ($solQuery) {
                        $q->whereDoesntHave('services', function($sq) {
                             $sq->where('category', 'like', '%Sol%')
                                ->orWhere('name', 'like', '%Sol%');
                        })->orWhereNotNull('prep_sol_completed_at');
                    })
                    ->whereNull('prep_upper_completed_at')
                    ->count(),
                    
        // Review: Washing Done AND (Sol Done OR Not Needed) AND (Upper Done OR Not Needed)
        'review' => (clone $baseQuery)->whereNotNull('prep_washing_completed_at')
                    ->where(function ($q) use ($solQuery) {
                        $q->whereDoesntHave('services', function($sq) {
                             $sq->where('category', 'like', '%Sol%')
                                ->orWhere('name', 'like', '%Sol%');
                        })->orWhereNotNull('prep_sol_completed_at');
                    })
                    ->where(function ($q) use ($upperQuery) {
                        $q->whereDoesntHave('services', function($sq) {
                             $sq->where('category', 'like', '%Upper%')
                                ->orWhere('name', 'like', '%Upper%')
                                ->orWhere('category', 'like', '%Repaint%')
                                ->orWhere('name', 'like', '%Repaint%')
                                ->orWhere('category', 'like', '%Jahit%')
                                ->orWhere('name', 'like', '%Jahit%');
                        })->orWhereNotNull('prep_upper_completed_at');
                    })
                    ->count(),
         'all' => (clone $baseQuery)->count(), 
    ];

    // Fetch Data based on Active Tab
    $ordersQuery = clone $baseQuery;
    
    // Check if user is filtering by work_status
    $hasStatusFilter = $request->filled('work_status') && $request->work_status !== 'all';
    
    switch ($activeTab) {
        case 'sol':
            $ordersQuery->where($solQuery)
                        ->whereNotNull('prep_washing_completed_at');
            // Only apply default "not completed" filter if no status filter
            if (!$hasStatusFilter) {
                $ordersQuery->whereNull('prep_sol_completed_at');
            }
            break;
        case 'upper':
            $ordersQuery->where($upperQuery)
                        ->whereNotNull('prep_washing_completed_at')
                        // Only show if Sol is done (or not needed)
                        ->where(function($q) use ($solQuery) {
                            $q->whereDoesntHave('services', function($sq) {
                                 $sq->where('category', 'like', '%Sol%')
                                    ->orWhere('name', 'like', '%Sol%');
                            })->orWhereNotNull('prep_sol_completed_at');
                        });
            // Only apply default "not completed" filter if no status filter
            if (!$hasStatusFilter) {
                $ordersQuery->whereNull('prep_upper_completed_at');
            }
            break;
        case 'review':
             $ordersQuery->whereNotNull('prep_washing_completed_at')
                    ->where(function ($q) {
                        $q->whereDoesntHave('services', function($sq) {
                             $sq->where('category', 'like', '%Sol%')
                                ->orWhere('name', 'like', '%Sol%');
                        })->orWhereNotNull('prep_sol_completed_at');
                    })
                    ->where(function ($q) {
                        $q->whereDoesntHave('services', function($sq) {
                             $sq->where('category', 'like', '%Upper%')
                                ->orWhere('name', 'like', '%Upper%')
                                ->orWhere('category', 'like', '%Repaint%')
                                ->orWhere('name', 'like', '%Repaint%')
                                ->orWhere('category', 'like', '%Jahit%')
                                ->orWhere('name', 'like', '%Jahit%');
                        })->orWhereNotNull('prep_upper_completed_at');
                    });
            break;
        case 'all':
            // No specific filter
            break;
        case 'washing':
        default:
            // Only apply default "not completed" filter if no status filter
            if (!$hasStatusFilter) {
                $ordersQuery->whereNull('prep_washing_completed_at');
            }
            break;
    }


        // === FILTERS ===
        
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

        // Work Status Filter (not_started, in_progress, completed)
        // Skip status filter on 'all' tab as it doesn't make sense
        if ($hasStatusFilter && $activeTab !== 'all') {
            $statusColumn = "prep_{$activeTab}";
            
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
            $techColumn = "prep_{$activeTab}_by";
            $ordersQuery->where($techColumn, $request->technician);
        }

        $orders = $ordersQuery->with(['services', 'workOrderServices', 'prepWashingBy', 'prepSolBy', 'prepUpperBy', 'cxIssues'])
                              ->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
                              ->orderBy('id', 'asc')
                              ->paginate(200)
                              ->appends($request->all());

        // Technicians
        $techWashing = User::whereIn('specialization', ['Washing', 'Treatment', 'Clean Up'])->get();
        $techSol = User::whereIn('specialization', ['Sol Repair', 'PIC Material Sol'])->get();
        $techUpper = User::whereIn('specialization', ['Upper Repair', 'Repaint', 'Jahit', 'PIC Material Upper'])->get();

        return view('preparation.index', compact('orders', 'counts', 'activeTab', 'techWashing', 'techSol', 'techUpper'));
    }

    public function updateStation(Request $request, $id)
    {
        try {
            Log::info("updateStation called for Order ID: $id", $request->all());

            $order = WorkOrder::with('services')->findOrFail($id);
            $this->authorize('updatePreparation', $order);

            $type = $request->input('type'); // washing, sol, upper
            $action = $request->input('action', 'finish'); // start, finish
            
            // For 'start', usage input technician_id. For 'finish', usage existing or current auth (fallback)
            $inputTechId = $request->input('technician_id');
            $techId = $inputTechId ?: Auth::id(); 
            
            $now = Carbon::now();

            if (!in_array($type, ['washing', 'sol', 'upper'])) {
                return response()->json(['success' => false, 'message' => 'Invalid station type'], 400);
            }

            $this->handleStationUpdate($order, $type, $action, Auth::id(), $inputTechId, $request->input('finished_at'));

            $order->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($type) . ($action === 'start' ? ' dimulai.' : ' selesai.'),
                    'is_ready' => $order->is_ready 
                ]);
            }

            return back()->with('success', ucfirst($type) . ' updated.');

        } catch (\Throwable $e) {
            Log::error('Preparation Update Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function handleStationUpdate($order, $type, $action, $techId, $inputTechId, $finishedAt = null)
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
            $completionTime = $finishedAt ? Carbon::parse($finishedAt)->setTimeFrom($now) : $now;
            $order->{"{$columnPrefix}_completed_at"} = $completionTime;
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

    private function performApprove(WorkOrder $order)
    {
        // Boomerang Logic: If in revision, jump back to previous status
        if ($order->is_revising && $order->previous_status instanceof WorkOrderStatus) {
            $targetStatus = $order->previous_status; 
            $note = "Revision completed in Preparation. Returning to " . $targetStatus->value;
            
            $this->workflow->updateStatus($order, $targetStatus, $note);

            // Re-fetch or refresh flags because updateStatus might have saved the model
            $order->is_revising = false;
            $order->previous_status = null;
            $order->save();
            return;
        } 
        
        if ($order->is_revising) {
            $order->is_revising = false;
            $order->save();
        }

        $this->workflow->updateStatus($order, WorkOrderStatus::SORTIR, 'Preparation Approved by Admin. Proceed to Sortir.');
    }

    public function approve($id)
    {
        $order = WorkOrder::with('services')->findOrFail($id);
        $this->authorize('updatePreparation', $order); // Reuse update policy or create specific 'approve'
        
        try {
            $this->performApprove($order);
            return back()->with('success', 'Preparation disetujui. Order lanjut ke Sortir.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'target_status' => 'required|string', // e.g., 'PREPARATION'
            'target_stations' => 'nullable|array' // e.g., ['prep_washing']
        ]);

        $order = WorkOrder::findOrFail($id);
        
        try {
            $targetStatus = WorkOrderStatus::from($request->target_status);
            $stations = $request->input('target_stations', []);

            $this->workflow->revise($order, $targetStatus, $request->reason, $stations);

            return back()->with('warning', "Order direvisi ke status " . $targetStatus->label() . ".");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses revisi: ' . $e->getMessage());
        }
    }
    public function bulkUpdate(Request $request)
    {
        Log::info('Preparation Bulk Update Request:', $request->all());

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
            'type' => 'nullable|string',
            'action' => 'required|in:start,finish,assign,approve',
            'technician_id' => 'nullable|exists:users,id'
        ]);

        $ids = $request->input('ids');
        $type = $request->input('type');
        $action = $request->input('action');
        $techId = Auth::id(); 
        $assigneeId = $request->input('technician_id');

        // Map 'assign' to 'start' for logic
        $effectiveAction = $action === 'assign' ? 'start' : $action;

        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $order = WorkOrder::with('services')->findOrFail($id);
                $this->authorize('updatePreparation', $order);
                
                if ($action === 'approve') {
                     $this->performApprove($order); 
                     $successCount++;
                     continue;
                }

                 if ($effectiveAction === 'start' && !$assigneeId) {
                     $assigneeId = Auth::id(); 
                }

                $currentVal = $order->{"prep_{$type}_completed_at"};
                Log::info("Order #{$id} Processing: Type={$type}, Action={$effectiveAction}. Current CompletedAt: " . $currentVal);
                
                // Skip if already completed
                if ($effectiveAction === 'finish' && $currentVal !== null) {
                    Log::warning("Order #{$id} already completed for {$type}. Skipping.");
                    $errors[] = "Order #$id: Already completed for {$type}";
                    continue;
                }
                
                $this->handleStationUpdate(
                    $order, 
                    $type, 
                    $effectiveAction, 
                    $techId, 
                    $assigneeId,
                    null 
                );
                
                Log::info("Order #{$id} Dirty Attributes: ", $order->getDirty());

                $saved = $order->save();
                $freshOrder = $order->fresh();
                Log::info("Order #{$id} Save Result: " . ($saved ? 'True' : 'False') . ". Persisted Value: " . $freshOrder->{"prep_{$type}_completed_at"});

                $successCount++;
                
            } catch (\Throwable $e) {
                Log::error("Order #{$id} Bulk Error: " . $e->getMessage());
                $errors[] = "Order #$id: " . $e->getMessage();
            }
        }

        if ($request->ajax()) {
            $isTotallySuccessful = ($successCount > 0 && count($errors) === 0);
            $isPartiallySuccessful = ($successCount > 0 && count($errors) > 0);
            
            return response()->json([
                'success' => $successCount > 0, 
                'message' => $isTotallySuccessful 
                    ? "$successCount item berhasil diproses." 
                    : ($isPartiallySuccessful 
                        ? "$successCount item berhasil, namun ada beberapa kendala." 
                        : "Gagal memproses item."),
                'errors' => $errors,
                'success_count' => $successCount
            ]);
        }

        if ($successCount > 0) {
            return back()->with('success', "$successCount item berhasil diproses.");
        }
        
        return back()->with('error', "Gagal memproses item: " . implode(', ', $errors));
    }
}
