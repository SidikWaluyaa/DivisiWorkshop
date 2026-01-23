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

    public function destroy($id)
    {
        $order = WorkOrder::findOrFail($id);
        $order->delete();

        return redirect()->back()->with('success', 'Order assessment berhasil dihapus.');
    }

    public function index(Request $request)
    {
        // Query builder
        $query = WorkOrder::where('status', WorkOrderStatus::ASSESSMENT->value);
        
        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        
        // Orders waiting for assessment (Coming from Washing)
        $queue = $query->orderBy('updated_at', 'asc')
                       ->paginate(20)
                       ->appends($request->all());

        return view('assessment.index', compact('queue'));
    }

    public function create($id)
    {
        $order = WorkOrder::with(['photos', 'workOrderServices.service'])->findOrFail($id);
        
        // Ensure status is correct
        // Use ->value because status is cast to Enum
        $currentStatusValue = $order->status instanceof \App\Enums\WorkOrderStatus ? $order->status->value : $order->status;
        
        if (!in_array($currentStatusValue, [WorkOrderStatus::ASSESSMENT->value, WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value])) {
            return redirect()->route('assessment.index')->with('error', 'Status sepatu tidak valid untuk assessment. Status saat ini: ' . $currentStatusValue);
        }

        $services = Service::all(); // Flat list for AlpineJS
        
        return view('assessment.create', compact('order', 'services'));
    }

    public function store(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $request->validate([
            'services' => 'required|array|min:1', // Now an array of objects
            'notes' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'priority' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                // Update Identitas Sepatu
                $order->update([
                    'shoe_brand' => $request->shoe_brand,
                    'shoe_size' => $request->shoe_size,
                    'shoe_color' => $request->shoe_color,
                    'notes' => $request->notes,
                    'technician_notes' => $request->technician_notes,
                    'customer_email' => $request->customer_email,
                    'customer_address' => $request->customer_address,
                    'priority' => $request->priority,
                    'discount' => $request->input('discount', 0),
                ]);

                // 1. Sync Services (Using new WorkOrderService model structure)
                // Delete existing first
                $order->workOrderServices()->delete();

                $totalCost = 0;
                
                if ($request->has('services')) {
                    foreach ($request->services as $svc) {
                        $hasId = !empty($svc['service_id']);
                        
                        // Handle details (parse if string)
                        $details = isset($svc['details']) ? (is_string($svc['details']) ? json_decode($svc['details'], true) : $svc['details']) : [];

                        $order->workOrderServices()->create([
                            'service_id' => $hasId && $svc['service_id'] !== 'custom' ? $svc['service_id'] : null,
                            'custom_service_name' => $svc['custom_name'], 
                            'category_name' => $svc['category'] ?? 'Custom',
                            'cost' => $svc['price'],
                            'service_details' => $details,
                            'status' => 'PENDING'
                        ]);

                        $totalCost += (int) $svc['price'];
                    }
                }

                // 2. Update Order Totals
                $discount = $request->input('discount', 0);
                
                $finalTotal = ($totalCost - $discount);
                if ($finalTotal < 0) $finalTotal = 0;

                $order->update([
                    'total_service_price' => $totalCost,
                    'discount' => $discount,
                    'shipping_cost' => 0, // Reset shipping cost from assessment stage
                    'total_amount_due' => $finalTotal,
                ]);
                
                // 3. Move to WAITING_PAYMENT
                $this->workflow->updateStatus(
                    $order, 
                    WorkOrderStatus::WAITING_PAYMENT, 
                    "Assessment Selesai. Menunggu Pembayaran. Notes: " . $request->notes
                );
            });

            return redirect()->route('assessment.index')
                ->with('success', 'Assessment selesai! Data masuk ke Finance untuk Approval/Pembayaran.')
                ->with('print_spk_final_id', $order->id);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan assessment: ' . $e->getMessage());
        }
    }

    public function printSpk($id)
    {
        $order = WorkOrder::with(['services', 'customer', 'photos'])->findOrFail($id);
        
        // Generate QR Code if needed, or Barcode
        // Using same library as reception tag
        $barcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($order->spk_number);

        return view('assessment.print-spk', compact('order', 'barcode'));
    }
}
