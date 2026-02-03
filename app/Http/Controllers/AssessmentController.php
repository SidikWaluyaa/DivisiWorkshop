<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Models\CsSpk;

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
        $order = WorkOrder::with(['photos', 'workOrderServices.service', 'storageAssignments.rack'])->findOrFail($id);
        
        // Ensure status is correct
        // Use ->value because status is cast to Enum
        $currentStatusValue = $order->status instanceof WorkOrderStatus ? $order->status->value : $order->status;
        
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
            'services' => 'required|array|min:1',
            'notes' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            'priority' => 'required|string',
            'discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                // Update Assessment-specific data only
                $order->update([
                    'notes' => $request->notes,
                    'technician_notes' => $request->technician_notes,
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
                
                // 3. Determine Next Step (CS Payment or Finance Gate)
                // Check if already paid enough from SPK stage or manual payments
                $alreadyPaid = $order->payments->sum('amount_total');
                
                // Fallback check against CsSpk if it's a lead-based order and payments table hasn't been synced yet
                if ($order->spk_number) {
                    $spk = CsSpk::where('spk_number', $order->spk_number)->first();
                    if ($spk && $spk->dp_status === CsSpk::DP_PAID) {
                        $alreadyPaid = max($alreadyPaid, (float)$spk->dp_amount);
                    }
                }

                if ($alreadyPaid >= $finalTotal && $finalTotal > 0) {
                    // Auto-pass Finance Gate as it was already verified at SPK stage
                    $order->update([
                        'status' => WorkOrderStatus::PREPARATION,
                        'current_location' => 'Persiapan',
                    ]);

                    $order->logs()->create([
                        'step' => 'ASSESSMENT',
                        'action' => 'AUTO_PASS_FINANCE',
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'description' => "Pembayaran mencukupi (Rp ".number_format($alreadyPaid, 0, ',', '.')."). Melewati gerbang Finance ke Persiapan."
                    ]);
                } else {
                    // Move to WAITING_PAYMENT for CS to handle
                    $this->workflow->updateStatus(
                        $order, 
                        WorkOrderStatus::WAITING_PAYMENT, 
                        "Assessment Selesai. Menunggu Pembayaran (Selisih/DP). Notes: " . $request->notes
                    );
                }
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
        $order = WorkOrder::with(['workOrderServices.service', 'customer', 'photos'])->findOrFail($id);
        
        // Generate QR Code for SPK number
        /** @var \SimpleSoftwareIO\QrCode\Generator $qr */
        $qr = QrCode::size(100);
        $barcode = $qr->generate($order->spk_number);

        return view('assessment.print-spk-premium', compact('order', 'barcode'));
    }

    /**
     * Skip Assessment and move directly to Production
     */
    public function skipToProduction($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Strict Check: Only Admin/Owner/Manager
        // If the user role check in SortirController is used, I should stick to it.
        // But some systems use 'access' middleware. 
        // Let's use the same logic as SortirController for consistency.
        if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            abort(403, 'Unauthorized action. Only Admin/Manager can skip Assessment.');
        }

        try {
            $oldStatus = $order->status;

            DB::transaction(function () use ($order, $oldStatus) {
                $order->status = WorkOrderStatus::PRODUCTION;
                $order->current_location = 'Rumah Abu'; // Production Area
                $order->save();

                // Dispatch Event
                \App\Events\WorkOrderStatusUpdated::dispatch(
                    $order, 
                    $oldStatus, 
                    WorkOrderStatus::PRODUCTION, 
                    'Direct to Production (Skip Assessment Stage)', 
                    \Illuminate\Support\Facades\Auth::id()
                );
            });

            return redirect()->back()->with('success', 'Order berhasil dikirim langsung ke Production!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }
    }
}
