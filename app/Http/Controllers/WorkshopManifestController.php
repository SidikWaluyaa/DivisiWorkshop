<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkshopManifest;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use App\Services\TechnicianAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkshopManifestController extends Controller
{
    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index(Request $request)
    {
        $query = WorkshopManifest::with(['dispatcher', 'receiver'])
            ->withCount('workOrders');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $manifests = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manifest.index', compact('manifests'));
    }

    public function create(Request $request)
    {
        // High-Intensity Repair: Force release ANY orders still in READY_TO_DISPATCH but having a manifest ID.
        // This is necessary because an order in this status SHOULD NEVER have a manifest ID.
        WorkOrder::where('status', WorkOrderStatus::READY_TO_DISPATCH)
            ->whereNotNull('workshop_manifest_id')
            ->update(['workshop_manifest_id' => null]);

        // Items ready for dispatch
        $query = WorkOrder::where('status', WorkOrderStatus::READY_TO_DISPATCH)
            ->whereNull('workshop_manifest_id');

        // Handle Search from Internal Tracking
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END")
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('manifest.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:work_orders,id',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Hardening: Verify all orders are actually ready to be dispatched
            $validOrders = WorkOrder::whereIn('id', $request->order_ids)
                ->where('status', WorkOrderStatus::READY_TO_DISPATCH)
                ->whereNull('workshop_manifest_id')
                ->get();

            if ($validOrders->count() !== count($request->order_ids)) {
                return back()->with('error', 'Beberapa item tidak valid atau sudah masuk ke manifest lain.');
            }

            $manifestNumber = 'MFST-' . date('Ymd') . '-' . strtoupper(uniqid());

            $manifest = WorkshopManifest::create([
                'manifest_number' => $manifestNumber,
                'dispatcher_id' => Auth::id(),
                'status' => 'SENT',
                'notes' => $request->notes,
                'dispatched_at' => now(),
            ]);

            WorkOrder::whereIn('id', $request->order_ids)->update([
                'workshop_manifest_id' => $manifest->id,
            ]);

            foreach ($validOrders as $order) {
                $this->workflow->updateStatus(
                    $order, 
                    WorkOrderStatus::OTW_WORKSHOP, 
                    "Sent to Workshop Hijau via Manifest #{$manifestNumber}"
                );
            }

            return redirect()->route('manifest.index')->with('success', "Manifest #{$manifestNumber} berhasil dibuat.");
        });
    }

    public function show($id)
    {
        $manifest = WorkshopManifest::with(['workOrders', 'dispatcher', 'receiver'])->findOrFail($id);
        return view('manifest.show', compact('manifest'));
    }

    public function receiveForm($id)
    {
        $manifest = WorkshopManifest::with(['workOrders', 'dispatcher'])->findOrFail($id);

        if ($manifest->status !== 'SENT') {
            return redirect()->route('manifest.show', $id)->with('error', 'Manifest ini tidak dalam status siap diterima.');
        }

        $assignmentService = app(TechnicianAssignmentService::class);
        
        $candidates_washing = TechnicianAssignmentService::getPrepWashingCandidates();
        $candidates_sol = $assignmentService->getQualifiedTechnicians('sol');
        $candidates_upper = $assignmentService->getQualifiedTechnicians('upper');

        foreach ($manifest->workOrders as $order) {
            // 1. Washing recommendation
            $recWashing = $assignmentService->getRecommendedPrepWashingTechnician($order, $candidates_washing);
            $order->recommended_prep_washing_by = $recWashing ? $recWashing->id : null;

            // 2. Sol Prep recommendation
            if ($order->needs_prep_sol) {
                $order->recommended_prep_sol_by = $candidates_sol->isEmpty() ? null : $candidates_sol->sortBy(function ($tech) {
                    return WorkOrder::where('prep_sol_by', $tech->id)
                        ->whereNull('prep_sol_completed_at')
                        ->count();
                })->first()->id;
            } else {
                $order->recommended_prep_sol_by = null;
            }

            // 3. Upper Prep recommendation
            if ($order->needs_prep_upper) {
                $order->recommended_prep_upper_by = $candidates_upper->isEmpty() ? null : $candidates_upper->sortBy(function ($tech) {
                    return WorkOrder::where('prep_upper_by', $tech->id)
                        ->whereNull('prep_upper_completed_at')
                        ->count();
                })->first()->id;
            } else {
                $order->recommended_prep_upper_by = null;
            }
        }

        return view('manifest.receive', compact(
            'manifest', 
            'candidates_washing', 
            'candidates_sol', 
            'candidates_upper'
        ));
    }

    public function receive(Request $request, $id)
    {
        $manifest = WorkshopManifest::with('workOrders')->findOrFail($id);

        if ($manifest->status !== 'SENT') {
            return redirect()->route('manifest.show', $id)->with('error', 'Manifest ini tidak dapat diterima karena statusnya ' . $manifest->status);
        }

        $request->validate([
            'prep_washing_by' => 'required|array',
            'prep_washing_by.*' => 'required|exists:users,id',
            'prep_sol_by' => 'nullable|array',
            'prep_sol_by.*' => 'nullable|exists:users,id',
            'prep_upper_by' => 'nullable|array',
            'prep_upper_by.*' => 'nullable|exists:users,id',
        ]);

        return DB::transaction(function () use ($manifest, $request) {
            $manifest->update([
                'status' => 'RECEIVED',
                'receiver_id' => Auth::id(),
                'received_at' => now(),
            ]);

            foreach ($manifest->workOrders as $order) {
                if ($order->status === WorkOrderStatus::OTW_WORKSHOP) {
                    $updateData = [
                        'prep_washing_by' => $request->prep_washing_by[$order->id] ?? null
                    ];

                    if ($order->needs_prep_sol && isset($request->prep_sol_by[$order->id])) {
                        $updateData['prep_sol_by'] = $request->prep_sol_by[$order->id];
                    }

                    if ($order->needs_prep_upper && isset($request->prep_upper_by[$order->id])) {
                        $updateData['prep_upper_by'] = $request->prep_upper_by[$order->id];
                    }

                    $order->update($updateData);

                    $this->workflow->updateStatus(
                        $order, 
                        WorkOrderStatus::PREPARATION, 
                        "Received at Workshop Hijau from Manifest #{$manifest->manifest_number} (Technicians assigned)"
                    );
                }
            }

            return redirect()->route('manifest.index')->with('success', "Manifest #{$manifest->manifest_number} berhasil diterima dan teknisi prep telah ditugaskan.");
        });
    }
}
