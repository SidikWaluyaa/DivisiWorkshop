<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // Orders waiting for assessment (Coming from Washing)
        $queue = WorkOrder::where('status', WorkOrderStatus::ASSESSMENT->value)
                    ->orderBy('updated_at', 'asc')
                    ->get();

        return view('assessment.index', compact('queue'));
    }

    public function create($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Ensure status is correct
        if ($order->status !== WorkOrderStatus::ASSESSMENT->value) {
            return redirect()->route('assessment.index')->with('error', 'Status sepatu tidak valid untuk assessment.');
        }

        $services = Service::all()->groupBy('category');

        return view('assessment.create', compact('order', 'services'));
    }

    public function store(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $request->validate([
            'services' => 'required|array|min:1',
            'notes' => 'nullable|string',
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:50',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                // Update Identitas Sepatu
                $order->update([
                    'shoe_brand' => $request->shoe_brand,
                    'shoe_size' => $request->shoe_size,
                    'shoe_color' => $request->shoe_color,
                ]);
                // 1. Sync Services
                // The Pivot table needs 'cost' and 'status'
                $syncData = [];
                $totalCost = 0;
                
                foreach ($request->services as $serviceId) {
                    $service = Service::find($serviceId);
                    if ($service) {
                        $syncData[$serviceId] = [
                            'cost' => $service->price,
                            'status' => 'PENDING'
                        ];
                        $totalCost += $service->price; // Simple default cost
                    }
                }
                
                $order->services()->sync($syncData);

                // 2. Update Order Details (Notes, Price estimation maybe?)
                // For now, just logging notes via Logs or maybe specific column if added. 
                // We'll put it in the log description for this step.
                
                // 3. Move to PREPARATION
                // The concept: "Layanan TERKUNCI".
                $this->workflow->updateStatus(
                    $order, 
                    WorkOrderStatus::PREPARATION, 
                    "Assessment Selesai. Notes: " . $request->notes . ". Services: " . count($syncData) . " items."
                );
            });

            return redirect()->route('assessment.index')->with('success', 'Assessment selesai! Sepatu masuk ke Preparation.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan assessment: ' . $e->getMessage());
        }
    }
}
