<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with('workOrder');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('appointment_filter')) {
            if ($request->appointment_filter === 'has_appointment') {
                $query->whereNotNull('appointment_date');
            } elseif ($request->appointment_filter === 'today') {
                $query->whereDate('appointment_date', today());
            } elseif ($request->appointment_filter === 'upcoming') {
                $query->where('appointment_date', '>=', now())
                      ->where('appointment_status', 'SCHEDULED');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('workOrder', function($sq) use ($search) {
                      $sq->where('spk_number', 'LIKE', "%{$search}%")
                        ->orWhere('shoe_brand', 'LIKE', "%{$search}%");
                  });
            });
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();

        // Calculate Status Counts
        $statusCounts = [
            'total' => Complaint::count(),
            'PENDING' => Complaint::where('status', 'PENDING')->count(),
            'PROCESS' => Complaint::where('status', 'PROCESS')->count(),
            'RESOLVED' => Complaint::where('status', 'RESOLVED')->count(),
            'REJECTED' => Complaint::where('status', 'REJECTED')->count(),
            'with_appointments' => Complaint::whereNotNull('appointment_date')->count(),
            'today_appointments' => Complaint::whereDate('appointment_date', today())->count(),
        ];

        // Format calendar events for Alpine.js calendar grid
        $appointmentComplaints = Complaint::with('workOrder')
            ->whereNotNull('appointment_date')
            ->orderBy('appointment_date', 'asc')
            ->get();

        $calendarEvents = $appointmentComplaints->map(function ($c) {
            return [
                'id'                 => $c->id,
                'title'              => ($c->workOrder ? $c->workOrder->spk_number : 'Keluhan #' . $c->id) . ' - ' . $c->customer_name,
                'spk_number'         => $c->workOrder ? $c->workOrder->spk_number : '-',
                'customer_name'      => $c->customer_name,
                'customer_phone'     => $c->customer_phone,
                'shoe_info'          => $c->workOrder ? trim(($c->workOrder->shoe_brand ?? '') . ' ' . ($c->workOrder->shoe_type ?? '')) : '-',
                'category'           => $c->category,
                'date'               => $c->appointment_date ? $c->appointment_date->format('Y-m-d') : null,
                'time'               => $c->appointment_date ? $c->appointment_date->format('H:i') : null,
                'datetime_formatted' => $c->appointment_date ? $c->appointment_date->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'location'           => $c->appointment_location ?? 'WORKSHOP',
                'location_label'     => $c->location_label,
                'appointment_status' => $c->appointment_status ?? 'SCHEDULED',
                'appointment_badge'  => $c->appointment_badge,
                'appointment_notes'  => $c->appointment_notes ?? '-',
                'complaint_status'   => $c->status,
                'description'        => Str::limit($c->description, 140),
                'show_url'           => route('admin.complaints.show', $c->id),
            ];
        });

        // Upcoming appointments list (next 5)
        $upcomingAppointments = Complaint::with('workOrder')
            ->whereNotNull('appointment_date')
            ->where('appointment_date', '>=', now())
            ->where('appointment_status', '!=', 'CANCELLED')
            ->orderBy('appointment_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.complaints.index', compact('complaints', 'statusCounts', 'calendarEvents', 'upcomingAppointments'));
    }

    /**
     * Live search for SPKs (used in the complaint creation modal)
     */
    public function searchOrders(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $orders = WorkOrder::query()
            ->where(function ($query) use ($q) {
                $query->where('spk_number', 'LIKE', "%{$q}%")
                      ->orWhere('customer_name', 'LIKE', "%{$q}%")
                      ->orWhere('customer_phone', 'LIKE', "%{$q}%")
                      ->orWhere('shoe_brand', 'LIKE', "%{$q}%")
                      ->orWhere('shoe_type', 'LIKE', "%{$q}%");
            })
            ->select('id', 'spk_number', 'customer_name', 'customer_phone', 'shoe_brand', 'shoe_type', 'shoe_color', 'status')
            ->latest('id')
            ->limit(15)
            ->get();

        return response()->json($orders);
    }

    /**
     * Store an internal complaint
     */
    public function store(Request $request)
    {
        $request->validate([
            'work_order_id'        => 'required|exists:work_orders,id',
            'category'             => 'required|in:QUALITY,LATE,SERVICE,DAMAGE,OTHER',
            'description'          => 'required|string|min:5',
            'photos.*'             => 'nullable|image|max:10240',
            'appointment_date'     => 'nullable|date',
            'appointment_location' => 'nullable|in:WORKSHOP,CALL,VISIT',
            'appointment_notes'    => 'nullable|string|max:500',
        ]);

        $workOrder = WorkOrder::findOrFail($request->work_order_id);
        $customerName = $workOrder->customer_name ?: 'Pelanggan';
        $customerPhone = $workOrder->customer_phone ?: '-';

        // Process photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $photoPaths[] = $file->store('complaints', 'public');
            }
        }

        $appointmentDate = $request->filled('appointment_date') ? $request->appointment_date : null;
        $appointmentLocation = $appointmentDate ? ($request->appointment_location ?: 'WORKSHOP') : null;
        $appointmentStatus = $appointmentDate ? 'SCHEDULED' : null;

        $complaint = Complaint::create([
            'work_order_id'        => $workOrder->id,
            'customer_name'        => $customerName,
            'customer_phone'       => $customerPhone,
            'category'             => $request->category,
            'description'          => $request->description,
            'photos'               => $photoPaths,
            'status'               => 'PENDING',
            'appointment_date'     => $appointmentDate,
            'appointment_location' => $appointmentLocation,
            'appointment_status'   => $appointmentStatus,
            'appointment_notes'    => $request->appointment_notes,
        ]);

        // WorkOrder Log
        $logDesc = "Keluhan internal baru (#{$complaint->id}) dicatat: [{$complaint->category}] - " . Str::limit($complaint->description, 60);
        if ($complaint->appointment_date) {
            $logDesc .= " | Janji temu dijadwalkan: " . $complaint->appointment_date->format('d/m/Y H:i') . " ({$complaint->location_label})";
        }

        WorkOrderLog::create([
            'work_order_id' => $workOrder->id,
            'user_id'       => Auth::id() ?? 1,
            'step'          => $workOrder->status->value ?? 'COMPLAINT',
            'action'        => 'COMPLAINT_REGISTERED',
            'description'   => $logDesc,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Keluhan internal berhasil dicatat' . ($appointmentDate ? ' dan jadwal temu telah diagendakan.' : '.'));
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
            'status'               => 'required|in:PENDING,PROCESS,RESOLVED,REJECTED',
            'admin_notes'          => 'nullable|string',
            'appointment_date'     => 'nullable|date',
            'appointment_location' => 'nullable|in:WORKSHOP,CALL,VISIT',
            'appointment_status'   => 'nullable|in:SCHEDULED,COMPLETED,CANCELLED',
            'appointment_notes'    => 'nullable|string|max:500',
        ]);

        $oldStatus = $complaint->status;
        $oldAppStatus = $complaint->appointment_status;
        
        $complaint->update([
            'status'               => $request->status,
            'admin_notes'          => $request->admin_notes,
            'appointment_date'     => $request->appointment_date,
            'appointment_location' => $request->appointment_location,
            'appointment_status'   => $request->appointment_status,
            'appointment_notes'    => $request->appointment_notes,
        ]);

        // Automatic Work Order Logging
        $logChanges = [];
        if ($oldStatus !== $request->status) {
            $logChanges[] = "Status keluhan (#{$complaint->id}) diperbarui dari {$oldStatus} menjadi {$request->status}";
        }
        if ($oldAppStatus !== $request->appointment_status && $request->filled('appointment_status')) {
            $logChanges[] = "Status janji temu diperbarui menjadi {$request->appointment_status}";
        }

        if (!empty($logChanges) && $complaint->workOrder) {
            $desc = implode('. ', $logChanges) . ($request->admin_notes ? ". Catatan: " . $request->admin_notes : "");
            WorkOrderLog::create([
                'work_order_id' => $complaint->workOrder->id,
                'user_id'       => Auth::id() ?? 1,
                'step'          => $complaint->workOrder->status->value ?? 'COMPLAINT',
                'action'        => 'COMPLAINT_STATUS_UPDATED',
                'description'   => $desc,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return back()->with('success', 'Data keluhan dan janji temu berhasil diperbarui.');
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
        
        if ($complaint->photos) {
            foreach ($complaint->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $complaint->forceDelete();
        return redirect()->route('admin.complaints.trash')->with('success', 'Keluhan berhasil dihapus permanen.');
    }
}
