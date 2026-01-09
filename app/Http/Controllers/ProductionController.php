<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // 1. Queue: Ready for Production (From Sortir)
        $queue = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
                    ->whereNull('taken_date') // Not yet taken by technician
                    ->with(['services']) // Eager load service definition for category
                    ->orderBy('updated_at', 'asc')
                    ->get();

        // Check for revision status (has been to QC before)
        $queue->transform(function ($order) {
            $order->is_revisi = $order->logs()->where('step', WorkOrderStatus::QC->value)->exists();
            // Group services by category for the view
            $order->groupedServices = $order->services->groupBy(function($s) {
                return $s->category ?? 'General';
            });
            return $order;
        });

        // 2. In Progress: Being worked on
        $inProgress = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
                    ->whereNotNull('taken_date')
                    ->with(['services']) // Load assigned technician per service
                    ->orderBy('updated_at', 'desc')
                    ->get();

        $allTechs = \App\Models\User::pluck('name', 'id');

        $inProgress->transform(function ($order) use ($allTechs) {
            $order->is_revisi = $order->logs()->where('step', WorkOrderStatus::QC->value)->exists();
            // Map assigned technician names
            $order->services->each(function($s) use ($allTechs) {
                $s->tech_name = isset($s->pivot->technician_id) ? ($allTechs[$s->pivot->technician_id] ?? 'Unknown') : '-';
            });
            // Check if all services are DONE
            $order->all_services_done = $order->services->every(function($s) {
                return $s->pivot->status === 'DONE';
            });
            return $order;
        });
                    
        // Categorize Technicians
        $allTechnicians = \App\Models\User::where('role', 'technician')->get();
        
        $techsByCategory = [
            'Reparasi Sol' => $allTechnicians->where('specialization', 'Sol Repair'),
            'Reparasi Upper' => $allTechnicians->where('specialization', 'Upper Repair'),
            'Repaint' => $allTechnicians->whereIn('specialization', ['Repaint', 'Upper Repair']), // Allowing Upper Repair for Repaint usually
            'Deep Cleaning' => $allTechnicians->where('specialization', 'Washing'),
            'Whitening' => $allTechnicians->where('specialization', 'Washing'),
            'General' => $allTechnicians, // Fallback
        ];
                    
        return view('production.index', compact('queue', 'inProgress', 'techsByCategory', 'allTechnicians'));
    }

    public function updateService(Request $request, $orderId, $serviceId)
    {
        $order = WorkOrder::findOrFail($orderId);
        
        // Update specific service status
        $order->services()->updateExistingPivot($serviceId, [
            'status' => 'DONE',
            'updated_at' => now()
        ]);
        
        // Log
        $serviceName = $order->services->find($serviceId)->name ?? 'Service';
        $order->logs()->create([
            'step' => WorkOrderStatus::PRODUCTION->value,
            'action' => 'SERVICE_DONE',
            'user_id' => Auth::id(),
            'description' => "Service '$serviceName' marked as DONE."
        ]);

        return back()->with('success', 'Status layanan berhasil diperbarui.');
    }

    public function start(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*' => 'exists:users,id', // Array of category => technician_id
        ]);
        
        $assignments = $request->assignments;
        
        // 1. Update Pivot Tables with Technicians
        // We need to map Category -> Services -> Update Pivot
        foreach ($order->services as $service) {
            $category = $service->category ?? 'General';
            
            if (isset($assignments[$category])) {
                // Update the pivot record directly
                $order->services()->updateExistingPivot($service->id, [
                    'technician_id' => $assignments[$category],
                    'status' => 'IN_PROGRESS' 
                ]);
            }
        }
        
        // 2. Mark Main Order as Taken
        // Use the first assigned technician as the 'main' PIC if needed, or just nullable
        $firstTech = reset($assignments);
        $order->technician_production_id = $firstTech; // Set primary PIC to one of them
        $order->taken_date = now();
        $order->save();
        
        $order->logs()->create([
            'step' => WorkOrderStatus::PRODUCTION->value,
            'action' => 'STARTED',
            'user_id' => Auth::id(), // Logged by Admin/User who assigned
            'description' => 'Production started with multi-technician assignment.'
        ]);
        
        return back()->with('success', 'Pekerjaan telah didistribusikan ke teknisi sesuai kategori.');
    }

    public function finish(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Validation: All services must be DONE
        $allDone = $order->services()->wherePivot('status', '!=', 'DONE')->doesntExist();
        
        if (!$allDone) {
            return back()->with('error', 'Selesaikan semua service layanan terlebih dahulu.');
        }

        // Logic Rejection/Revision:
        // If this was a rejected item (previously in QC), it goes back to QC.
        // Actually, normal flow is also to QC. So standard logic works.
        // But let's log specifically if it was a revision fix.
        
        $isRevision = $order->logs()->where('step', WorkOrderStatus::QC->value)->where('action', 'MOVED')->exists();
        $action = $isRevision ? 'REVISION_FIXED' : 'FINISHED';
        $desc = $isRevision ? 'Revision completed. Sending back to QC.' : 'Production finished. Sending to QC.';

        $this->workflow->updateStatus($order, WorkOrderStatus::QC, $desc);

        return redirect()->route('production.index')->with('success', 'Pekerjaan selesai! Dikirim ke QC.');
    }
}
