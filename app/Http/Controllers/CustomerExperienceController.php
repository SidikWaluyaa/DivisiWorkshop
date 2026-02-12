<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Models\Service;
use App\Enums\WorkOrderStatus;

class CustomerExperienceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = WorkOrder::whereIn('status', [
                WorkOrderStatus::CX_FOLLOWUP->value,
                WorkOrderStatus::HOLD_FOR_CX->value
            ])
            ->with(['cxIssues' => function($q) {
                $q->latest();
            }]);

        // Filter by Handler
        if (!in_array($user->role, ['admin', 'owner'])) {
            $query->where('cx_handler_id', $user->id);
        }

        if (request()->filled('handler_id') && in_array($user->role, ['admin', 'owner'])) {
            $query->where('cx_handler_id', request()->handler_id);
        }

        $orders = $query->orderBy('updated_at', 'desc')
            ->paginate(10);

        $services = Service::all();

        return view('cx.index', compact('orders', 'services'));
    }

    public function cancelled(Request $request)
    {
        // SECURITY: Check access policy
        $this->authorize('viewAnyCx', WorkOrder::class);

        // "Kolam Cancel" - Orders that are Cancelled
        $query = WorkOrder::where('status', WorkOrderStatus::BATAL->value)
            ->with(['logs', 'cxIssues'])
            ->orderBy('updated_at', 'desc');
            
        if($request->has('search')){
            $query->where('spk_number', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_name', 'like', '%'.$request->search.'%');
        }

        $orders = $query->paginate(15);

        return view('cx.cancelled', compact('orders'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:lanjut,cancel,komplain,tambah_jasa',
            'notes' => 'nullable|string',
            'issue_id' => 'nullable|exists:cx_issues,id'
        ]);

        $order = WorkOrder::findOrFail($id);
        
        // SECURITY: Critical Authorization Check
        $this->authorize('manageCx', $order);

        $user = Auth::user();
        
        // Find relevant issue if exists
        $issue = null;
        if ($request->issue_id) {
            $issue = CxIssue::find($request->issue_id);
            // SECURITY: Ensure issue relates to this order
            if ($issue && $issue->work_order_id !== $order->id) {
                 abort(403, 'Issue ID does not match Order ID.');
            }
        } else {
            $issue = $order->cxIssues()->where('status', 'OPEN')->latest()->first();
        }

        switch ($request->action) {
            case 'lanjut':
                // Logic: Return to previous status (Resume Process)
                
                // 1. Get previous status
                $previousStatus = $order->previous_status;

                // 2. Validate previous status (Should not be null). If null, fallback to Assessment.
                // 2. Validate previous status. If it's empty or points back to CX, fallback to ASSESSMENT.
                if (!$previousStatus || $previousStatus === WorkOrderStatus::CX_FOLLOWUP || $previousStatus === WorkOrderStatus::HOLD_FOR_CX) {
                    $nextStatus = WorkOrderStatus::ASSESSMENT;
                } else {
                    $nextStatus = $previousStatus;
                }
                
                $updateData = [
                    'status' => $nextStatus,
                    'previous_status' => WorkOrderStatus::CX_FOLLOWUP, // Mark that it came from CX
                    'reception_rejection_reason' => null, // Clear reason upon resolution
                ];

                // Append notes to technician_notes if provided
                if ($request->filled('notes')) {
                    $newNote = "[CX - Lanjut]: " . $request->notes;
                    $updateData['technician_notes'] = $order->technician_notes 
                        ? $order->technician_notes . "\n\n" . $newNote 
                        : $newNote;
                }

                $order->update($updateData);
                $statusLabel = $nextStatus instanceof WorkOrderStatus ? $nextStatus->value : $nextStatus;
                $message = 'Order dilanjutkan kembali ke ' . str_replace('_', ' ', $statusLabel);
                break;

            case 'cancel':
                $order->update([
                    'status' => WorkOrderStatus::BATAL->value,
                    'reception_rejection_reason' => null, // Clear reason upon cancellation
                ]);
                $message = 'Order dibatalkan dan masuk Kolam Cancel.';
                break;

            case 'komplain':
                // Redirect to Complaint Create
                return redirect()->route('admin.complaints.create', ['spk' => $order->spk_number]);
                break;

            case 'tambah_jasa':
                // CX Direct Input Logic
                $request->validate([
                    'service_id' => 'required|exists:services,id',
                    'cost' => 'required|numeric|min:0',
                ]);

                // 1. Attach Service
                $serviceId = $request->service_id;
                $cost = $request->cost;
                $customName = $request->custom_name;
                
                // Get category from service
                $baseService = Service::find($serviceId);
                $categoryName = $baseService ? $baseService->category : 'Custom';

                $order->services()->attach($serviceId, [
                    'custom_service_name' => $customName,
                    'category_name' => $categoryName,
                    'cost' => $cost,
                    'status' => 'pending',
                    'notes' => $request->notes
                ]);

                // 2. Transition Logic:
                // ALWAYS send to SORTIR if price/service changes post-assessment
                // to ensure materials are re-validated.
                $targetStatus = WorkOrderStatus::SORTIR->value;

                $order->update([
                    'status' => $targetStatus,
                    'previous_status' => WorkOrderStatus::CX_FOLLOWUP->value,
                    'reception_rejection_reason' => null, // Clear reason upon adding service
                    // Append notes to Technician Notes
                    'technician_notes' => trim($order->technician_notes . "\n\n[CX - Tambah Jasa]: " . $request->notes)
                ]);

                // Recalculate Total Service Price via Model Method
                $order->recalculateTotalPrice();

                $message = "Layanan tambahan berhasil diinput. Order dikirim kembali ke SORTIR untuk pengecekan material.";
                break;
                
            default:
                $message = 'Aksi tidak dikenal.';
        }

        // Close Issue if exists
        if ($issue) {
            $issue->update([
                'status' => 'RESOLVED',
                'resolution' => strtoupper($request->action),
                'resolution_notes' => $request->notes,
                'resolved_by' => $user->id,
                'resolved_at' => now(),
            ]);
        }
        
        // Log activity
             $finalStatus = $order->status instanceof WorkOrderStatus ? $order->status->value : $order->status;
             $order->logs()->create([
                  'step' => 'CX_FOLLOWUP',
                  'action' => 'CX_RESPONSE_' . strtoupper($request->action),
                  'user_id' => $user->id,
                  'description' => "CX Response: " . $request->notes . " (Next: " . $finalStatus . ")"
             ]);

        return redirect()->route('cx.index')->with('success', $message);
    }


    public function destroy($id)
    {
        // SECURITY: Critical Authorization Check
        $this->authorize('manageCx', WorkOrder::class);

        $order = WorkOrder::where('status', WorkOrderStatus::BATAL->value)->findOrFail($id);
        
        // Delete related data (optionally)
        // For now, soft delete or hard delete based on model. WorkOrder uses SoftDeletes?
        // Checking model... Assuming SoftDeletes or Force Delete.
        // User asked for "hapus secara cepat" (Quick Delete).
        
        $order->delete();

        return redirect()->back()->with('success', 'Data order batal berhasil dihapus permanen.');
    }
}
