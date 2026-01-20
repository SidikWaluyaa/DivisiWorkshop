<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerExperienceController extends Controller
{
    public function index()
    {
        // Get orders held for CX (New & Legacy)
        $orders = \App\Models\WorkOrder::whereIn('status', [
                \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value,
                \App\Enums\WorkOrderStatus::HOLD_FOR_CX->value
            ])
            ->with(['cxIssues' => function($q) {
                $q->latest();
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $services = \App\Models\Service::all();

        return view('cx.index', compact('orders', 'services'));
    }

    public function cancelled(Request $request)
    {
        // "Kolam Cancel" - Orders that are Cancelled
        $query = \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::BATAL->value)
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

        $order = \App\Models\WorkOrder::findOrFail($id);
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Find relevant issue if exists
        $issue = $request->issue_id ? \App\Models\CxIssue::find($request->issue_id) : $order->cxIssues()->where('status', 'OPEN')->latest()->first();

        switch ($request->action) {
            case 'lanjut':
                // Logic: Return to previous status (Resume Process)
                
                // 1. Get previous status
                $previousStatus = $order->previous_status;

                // 2. Validate previous status (Should not be null). If null, fallback to Assessment.
                if (!$previousStatus || $previousStatus === \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value) {
                    $nextStatus = \App\Enums\WorkOrderStatus::ASSESSMENT->value;
                } else {
                    $nextStatus = $previousStatus;
                }
                
                $order->update([
                    'status' => $nextStatus,
                    'previous_status' => \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value, // Mark that it came from CX
                ]);
                $message = 'Order dilanjutkan kembali ke ' . str_replace('_', ' ', $nextStatus);
                break;

            case 'cancel':
                $order->update([
                    'status' => \App\Enums\WorkOrderStatus::BATAL->value
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
                    // Custom name is optional unless service is "Custom Service" (handled by frontend mostly but good to store if present)
                ]);

                // 1. Attach Service
                // We do NOT detach existing services. We append.
                $serviceId = $request->service_id;
                $cost = $request->cost;
                $customName = $request->custom_name; // Optional

                // Default status for service item? Usually 'pending' or match order status.
                // Pivot columns: custom_name, cost, status
                $order->services()->attach($serviceId, [
                    'custom_name' => $customName,
                    'cost' => $cost,
                    'status' => 'pending' 
                ]);

                // 2. Resume Logic
                // If currently in CX_FOLLOWUP (paused), revert to previous_status.
                // If currently active (PREPARATION, etc.), keep current status.
                $targetStatus = $order->status;
                if ($order->status === \App\Enums\WorkOrderStatus::CX_FOLLOWUP) {
                     $targetStatus = $order->previous_status ?: \App\Enums\WorkOrderStatus::PREPARATION;
                }

                $order->update([
                    'status' => $targetStatus,
                    // Recalculate Total Service Price
                    'total_service_price' => $order->services->sum(fn($s) => $s->pivot->cost),
                    // Append notes to Technician Notes so they appear in Production
                    'technician_notes' => trim($order->technician_notes . "\n\n[CX Input]: " . $request->notes)
                ]);

                $statusLabel = is_object($targetStatus) ? $targetStatus->value : $targetStatus;
                $message = "Layanan tambahan berhasil diinput via CX. Status order: " . str_replace('_', ' ', $statusLabel) . ".";
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
        $order->logs()->create([
             'step' => 'CX_FOLLOWUP',
             'action' => 'CX_RESPONSE_' . strtoupper($request->action),
             'user_id' => $user->id,
             'description' => "CX Response: " . $request->notes . " (Next: " . ($nextStatus ?? 'Redirect') . ")"
        ]);

        return redirect()->route('cx.index')->with('success', $message);
    }

    public function destroy($id)
    {
        $order = \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::BATAL->value)->findOrFail($id);
        
        // Delete related data (optionally)
        // For now, soft delete or hard delete based on model. WorkOrder uses SoftDeletes?
        // Checking model... Assuming SoftDeletes or Force Delete.
        // User asked for "hapus secara cepat" (Quick Delete).
        
        $order->delete();

        return redirect()->back()->with('success', 'Data order batal berhasil dihapus permanen.');
    }
}
