<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\CekatService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    protected CekatService $cekatService;

    public function __construct(CekatService $cekatService)
    {
        $this->cekatService = $cekatService;
    }

    public function index(Request $request)
    {
        $query = Complaint::with('workOrder');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('workOrder', function($sq) use ($search) {
                      $sq->where('spk_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $complaints = $query->latest()->paginate(10);

        // Calculate Status Counts
        $statusCounts = [
            'total' => Complaint::count(),
            'PENDING' => Complaint::where('status', 'PENDING')->count(),
            'PROCESS' => Complaint::where('status', 'PROCESS')->count(),
            'RESOLVED' => Complaint::where('status', 'RESOLVED')->count(),
            'REJECTED' => Complaint::where('status', 'REJECTED')->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'statusCounts'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load([
            'workOrder', 
            'workOrder.services', 
            'workOrder.technicianProduction',
            'workOrder.prodSolBy',
            'workOrder.prodUpperBy',
            'workOrder.prodCleaningBy'
        ]);
        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:PENDING,PROCESS,RESOLVED,REJECTED',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $complaint->status;
        
        $complaint->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        // Automatic Work Order Logging
        if ($oldStatus !== $request->status) {
            $complaint->workOrder->logs()->create([
                'step' => 'COMPLAINT',
                'action' => 'COMPLAINT_STATUS_UPDATED',
                'user_id' => auth()->id(),
                'description' => "Status keluhan (#{$complaint->id}) diperbarui dari {$oldStatus} menjadi {$request->status}. " . ($request->admin_notes ? "Catatan: " . $request->admin_notes : ""),
            ]);
        }

        return back()->with('success', 'Status keluhan berhasil diperbarui.');
    }

    public function apiReply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // Dispatch Job to Queue (Async)
        \App\Jobs\SendWhatsAppMessage::dispatch(
            $complaint->customer_phone, 
            $request->message
        );

        // Log locally immediately (assuming success for UI responsiveness)
        $complaint->workOrder->logs()->create([
            'step' => 'COMPLAINT',
            'action' => 'COMPLAINT_REPLIED_CEKAT',
            'user_id' => auth()->id(),
            'description' => "Balasan dijadwalkan via Cekat.ai (#{$complaint->id}): " . $request->message,
        ]);

        return back()->with('success', 'Pesan sedang dikirim di latar belakang.');
    }
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return redirect()->route('admin.complaints.index')->with('success', 'Keluhan berhasil dihapus (masuk ke Sampah).');
    }

    public function trash()
    {
        $deletedComplaints = Complaint::onlyTrashed()->with('workOrder')->latest('deleted_at')->paginate(10);
        return view('admin.complaints.trash', compact('deletedComplaints'));
    }

    public function restore($id)
    {
        $complaint = Complaint::onlyTrashed()->findOrFail($id);
        $complaint->restore();
        return redirect()->route('admin.complaints.trash')->with('success', 'Keluhan berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $complaint = Complaint::onlyTrashed()->findOrFail($id);
        
        // Optional: Delete physical files
        if ($complaint->photos) {
            foreach ($complaint->photos as $photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($photo);
            }
        }

        $complaint->forceDelete();
        return redirect()->route('admin.complaints.trash')->with('success', 'Keluhan berhasil dihapus permanen.');
    }
}
