<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkshopManifest;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
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

    public function index()
    {
        $manifests = WorkshopManifest::with(['dispatcher', 'receiver'])
            ->withCount('workOrders')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manifest.index', compact('manifests'));
    }

    public function create()
    {
        // Items ready for dispatch
        $orders = WorkOrder::where('status', WorkOrderStatus::READY_TO_DISPATCH)
            ->whereNull('workshop_manifest_id')
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

    public function receive(Request $request, $id)
    {
        $manifest = WorkshopManifest::findOrFail($id);

        if ($manifest->status !== 'SENT') {
            return back()->with('error', 'Manifest ini tidak dapat diterima karena statusnya ' . $manifest->status);
        }

        return DB::transaction(function () use ($manifest) {
            $manifest->update([
                'status' => 'RECEIVED',
                'receiver_id' => Auth::id(),
                'received_at' => now(),
            ]);

            foreach ($manifest->workOrders as $order) {
                // Defensive: Only transition if still OTW
                if ($order->status === WorkOrderStatus::OTW_WORKSHOP) {
                    $this->workflow->updateStatus(
                        $order, 
                        WorkOrderStatus::PREPARATION, 
                        "Received at Workshop Hijau from Manifest #{$manifest->manifest_number}"
                    );
                }
            }

            return redirect()->route('manifest.index')->with('success', "Manifest #{$manifest->manifest_number} berhasil diterima. Barang siap di-Preparation.");
        });
    }
}
