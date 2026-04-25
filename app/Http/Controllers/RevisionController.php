<?php

namespace App\Http\Controllers;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use App\Models\WorkOrderRevision;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RevisionController extends Controller
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of active revisions.
     */
    public function index(Request $request)
    {
        $baseQuery = WorkOrderRevision::with(['workOrder', 'creator', 'resolver']);

        // Dynamic Filtering Logic
        if ($request->filled('q')) {
            $baseQuery->whereHas('workOrder', function($q) use ($request) {
                $q->where('spk_number', 'LIKE', "%{$request->q}%")
                  ->orWhere('customer_name', 'LIKE', "%{$request->q}%");
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $baseQuery->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('pic')) {
            $baseQuery->where('created_by', $request->pic);
        }

        if ($request->filled('brand')) {
            $baseQuery->whereHas('workOrder', function($q) use ($request) {
                $q->where('shoe_brand', $request->brand);
            });
        }

        // Split into Active and History with Pagination
        $active = (clone $baseQuery)->where('status', 'OPEN')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_active')
            ->withQueryString();

        $history = (clone $baseQuery)->where('status', 'FINISHED')
            ->orderBy('finished_at', 'desc')
            ->paginate(10, ['*'], 'page_history')
            ->withQueryString();

        // Fetch Dropdown Options Dynamically
        $reporters = \App\Models\User::whereIn('id', WorkOrderRevision::pluck('created_by')->unique())->get(['id', 'name']);
        $brands = \App\Models\WorkOrder::whereIn('id', WorkOrderRevision::pluck('work_order_id')->unique())
            ->pluck('shoe_brand')
            ->unique()
            ->filter()
            ->values();

        return view('revision.index', compact('active', 'history', 'reporters', 'brands'));
    }

    /**
     * Display the details of a specific revision.
     */
    public function show(WorkOrderRevision $revision)
    {
        $revision->load(['workOrder', 'creator']);
        return view('revision.show', compact('revision'));
    }

    /**
     * Submit a revision request for a finished SPK.
     */
    public function request(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120', // Max 5MB per image
        ]);

        try {
            DB::transaction(function () use ($request, $workOrder) {
                // 1. Move WorkOrder status to REVISI
                $this->workflowService->updateStatus(
                    $workOrder,
                    WorkOrderStatus::REVISI,
                    'Mengajukan revisi teknik: ' . $request->description
                );

                // 2. Handle Multiple Photo Uploads
                $photoPaths = [];
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $photoPaths[] = $photo->store('workshop/revisions', 'public');
                    }
                }

                // 3. Create Revision Record
                WorkOrderRevision::create([
                    'work_order_id' => $workOrder->id,
                    'description' => $request->description,
                    'photo_path' => $photoPaths[0] ?? null, // First photo for compatibility
                    'photo_paths' => $photoPaths,           // All photos
                    'status' => 'OPEN',
                    'created_by' => auth()->id(),
                ]);
            });

            return redirect()->back()->with('success', 'Revisi berhasil diajukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengajukan revisi: ' . $e->getMessage());
        }
    }

    /**
     * Mark a revision as completed and return to SELESAI status.
     */
    public function complete(Request $request, WorkOrderRevision $revision)
    {
        try {
            DB::transaction(function () use ($revision) {
                $workOrder = $revision->workOrder;

                // 1. Update Revision Status
                $revision->update([
                    'status' => 'FINISHED',
                    'resolved_by' => auth()->id(),
                    'finished_at' => now(),
                ]);

                // 2. Move WorkOrder back to SELESAI
                $this->workflowService->updateStatus(
                    $workOrder,
                    WorkOrderStatus::SELESAI,
                    'Revisi selesai dikerjakan.'
                );
            });

            return redirect()->route('revision.index')->with('success', 'Revisi telah diselesaikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyelesaikan revisi: ' . $e->getMessage());
        }
    }
}
