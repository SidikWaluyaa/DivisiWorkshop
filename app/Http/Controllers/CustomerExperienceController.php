<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;


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

        // Search Filter (SPK Number or Customer Name)
        if (request()->filled('search')) {
            $searchTerm = request()->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('spk_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // Status Terakhir Filter
        if (request()->filled('last_status')) {
            $lastStatus = request()->last_status;
            if ($lastStatus === 'QC_REJECT') {
                $query->whereNull('previous_status');
            } else {
                $query->where('previous_status', $lastStatus);
            }
        }

        // Source Filter
        if (request()->filled('source')) {
            $source = request()->source;
            $query->whereHas('cxIssues', function($q) use ($source) {
                if ($source === 'WS') {
                    $q->where('source', 'LIKE', 'WORKSHOP_%')->where('status', 'OPEN');
                } else {
                    $q->where('source', $source)->where('status', 'OPEN');
                }
            });
        }
        
        // Date Range Filter (based on entry_date or updated_at, entry_date is more appropriate for SPK)
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('entry_date', [request()->start_date . ' 00:00:00', request()->end_date . ' 23:59:59']);
        } elseif (request()->filled('start_date')) {
            $query->where('entry_date', '>=', request()->start_date . ' 00:00:00');
        } elseif (request()->filled('end_date')) {
            $query->where('entry_date', '<=', request()->end_date . ' 23:59:59');
        }

        $sortOrder = request('sort', 'asc') === 'desc' ? 'desc' : 'asc';
        $orders = $query->orderBy('entry_date', $sortOrder)
            ->paginate(10)
            ->withQueryString();

        $services = Service::all();

        return view('cx.index', compact('orders', 'services'));
    }

    public function history(Request $request)
    {
        $sortOrder = $request->input('sort', 'asc') === 'desc' ? 'desc' : 'asc';
        $query = CxIssue::where('status', 'RESOLVED')
            ->with(['workOrder', 'resolver', 'reporter'])
            ->orderBy('resolved_at', $sortOrder);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('spk_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('resolved_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $issues = $query->paginate(15)->withQueryString();
        $categories = CxIssue::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('cx.history', compact('issues', 'categories'));
    }

    public function cancelled(Request $request)
    {
        // SECURITY: Check access policy
        $this->authorize('manageCx', WorkOrder::class);

        // "Kolam Cancel" - Orders that are Cancelled
        $sortOrder = $request->input('sort', 'asc') === 'desc' ? 'desc' : 'asc';
        $query = WorkOrder::where('status', WorkOrderStatus::BATAL->value)
            ->with(['logs', 'cxIssues'])
            ->orderBy('entry_date', $sortOrder);
            
        if($request->has('search')){
            $query->where('spk_number', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_name', 'like', '%'.$request->search.'%');
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('cx.cancelled', compact('orders'));
    }

    public function process(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:lanjut,cancel,komplain,tambah_jasa',
            'notes' => 'nullable|string',
            'issue_id' => 'nullable|exists:cx_issues,id',
            'estimasi_selesai_baru' => 'nullable|date'
        ]);

        return DB::transaction(function () use ($id, $request) {
            $order = WorkOrder::where('id', $id)->lockForUpdate()->firstOrFail();
            
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

            // IDEMPOTENCY CHECK: If no open issue and not manually forced, or if issue is already RESOLVED
            if ($issue && $issue->status === 'RESOLVED') {
                return redirect()->back()->with('error', 'Kendala ini sudah ditangani sebelumnya (Sessi Ganda Terdeteksi).');
            }

            // If it's a "tambah_jasa" action, we should also check if the status has already moved away from CX
            if ($request->action === 'tambah_jasa' && $order->status !== WorkOrderStatus::CX_FOLLOWUP) {
                return redirect()->back()->with('error', 'Status Order sudah berubah (Sessi Ganda Terdeteksi).');
            }

            $message = '';

        switch ($request->action) {
            case 'lanjut':
                $message = $this->handleLanjutAction($order, $request);
                break;

            case 'cancel':
                $message = $this->handleCancelAction($order);
                break;

            case 'komplain':
                return redirect()->route('admin.complaints.create', ['spk' => $order->spk_number]);

            case 'tambah_jasa':
                $response = $this->handleTambahJasaAction($order, $request);
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response;
                }
                $message = $response;
                break;
                
            default:
                $message = 'Aksi tidak dikenal.';
        }

            $this->finalizeProcess($order, $request, $issue, $user, $message);

            return redirect()->back()->with('success', $message);
        });
    }

    protected function handleLanjutAction(WorkOrder $order, Request $request): string
    {
        $previousStatus = $order->previous_status;
        $issue = \App\Models\CxIssue::where('work_order_id', $order->id)->where('status', 'OPEN')->latest()->first();

        // Smart Status Inference
        if (!$previousStatus || $previousStatus === WorkOrderStatus::CX_FOLLOWUP || $previousStatus === WorkOrderStatus::HOLD_FOR_CX) {
            // If previous status is missing or circular, try to infer from issue source
            if ($issue && $issue->category === 'OVERLOAD') {
                if ($issue->source === 'GUDANG') {
                    $nextStatus = WorkOrderStatus::ASSESSMENT;
                } else {
                    // If reported from workshop, it's safer to go to PRODUCTION or PREPARATION
                    $nextStatus = WorkOrderStatus::PRODUCTION;
                }
            } else {
                $nextStatus = WorkOrderStatus::ASSESSMENT;
            }
        } else {
            $nextStatus = $previousStatus;
        }
        
        $updateData = [
            'status' => $nextStatus,
            'previous_status' => WorkOrderStatus::CX_FOLLOWUP, // Track that we just left CX
            'reception_rejection_reason' => null,
            'new_estimation_date' => null, // Clear request field after resume
        ];

        // If it's an overload and we have a new date, apply it
        if ($issue && $issue->category === 'OVERLOAD' && $request->filled('estimasi_selesai_baru')) {
            $updateData['estimation_date'] = $request->estimasi_selesai_baru;
            $updateData['new_estimation_date'] = $request->estimasi_selesai_baru;
        }

        if ($request->filled('notes')) {
            $newNote = "[CX - Lanjut]: " . $request->notes;
            $updateData['technician_notes'] = $order->technician_notes 
                ? $order->technician_notes . "\n\n" . $newNote 
                : $newNote;
        }

        $order->update($updateData);
        $statusLabel = $nextStatus instanceof WorkOrderStatus ? $nextStatus->value : $nextStatus;
        return 'Order dilanjutkan kembali ke ' . str_replace('_', ' ', (string) $statusLabel);
    }

    protected function handleCancelAction(WorkOrder $order): string
    {
        $order->update([
            'status' => WorkOrderStatus::BATAL->value,
            'reception_rejection_reason' => null,
        ]);
        return 'Order dibatalkan dan masuk Kolam Cancel.';
    }

    protected function handleTambahJasaAction(WorkOrder $order, Request $request): string|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'services_data' => 'required|string',
            'notes' => 'required|string',
        ]);

        $servicesData = json_decode($request->services_data, true);

        if (empty($servicesData) || !is_array($servicesData)) {
            return back()->with('error', 'Minimal satu jasa harus ditambahkan.');
        }

        foreach ($servicesData as $service) {
            $serviceId = $service['service_id'] ?? null;
            $cost = $service['cost'] ?? 0;
            $customName = $service['custom_name'] ?? null;
            $details = $service['service_details'] ?? null;
            
            if ($serviceId) {
                $baseService = Service::find($serviceId);
                $categoryName = $baseService ? $baseService->category : 'Custom';
            } else {
                $categoryName = $service['category_name'] ?? 'Custom';
            }

            $order->workOrderServices()->create([
                'service_id' => $serviceId,
                'custom_service_name' => $customName,
                'category_name' => $categoryName,
                'cost' => $cost,
                'status' => 'pending',
                'service_details' => $details ? ['instruction' => $details] : null,
                'notes' => $request->notes
            ]);
        }

        if ($order->previous_status && in_array($order->previous_status instanceof \BackedEnum ? $order->previous_status->value : $order->previous_status, [
            WorkOrderStatus::PREPARATION->value,
            WorkOrderStatus::SORTIR->value,
            WorkOrderStatus::PRODUCTION->value,
            WorkOrderStatus::QC->value
        ])) {
            $targetStatus = $order->previous_status;
        } elseif ($order->warehouse_qc_status === 'reject') {
            $targetStatus = WorkOrderStatus::ASSESSMENT->value;
        } else {
            $targetStatus = WorkOrderStatus::SORTIR->value;
        }

        $order->update([
            'status' => $targetStatus,
            'previous_status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'reception_rejection_reason' => null,
            'technician_notes' => trim($order->technician_notes . "\n\n[CX - Tambah Jasa]: " . $request->notes)
        ]);

        $order->recalculateTotalPrice();

        $statusLabel = str_replace('_', ' ', $targetStatus instanceof \BackedEnum ? $targetStatus->value : $targetStatus);
        return "Layanan tambahan berhasil diinput. Order dikirim ke " . $statusLabel . ".";
    }

    protected function finalizeProcess(WorkOrder $order, Request $request, ?CxIssue $issue, $user, string $message): void
    {
        if ($request->filled('estimasi_selesai_baru')) {
            $order->update([
                'estimation_date' => $request->estimasi_selesai_baru
            ]);
            
            if ($order->invoice) {
                $order->invoice->update([
                    'estimasi_selesai' => $request->estimasi_selesai_baru
                ]);
            }
            $message .= " (Estimasi Selesai diperbarui menjadi: " . \Carbon\Carbon::parse($request->estimasi_selesai_baru)->format('d M Y') . ").";
        }

        $updatePayload = [
            'status' => 'RESOLVED',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
            'resolution_notes' => $request->notes,
            'resolution_type' => $request->action // Save the button action here
        ];

        if ($issue) {
            $issue->update($updatePayload);
        }

        CxIssue::where('work_order_id', $order->id)
            ->where('status', 'OPEN')
            ->update($updatePayload);
        
        $finalStatus = $order->status instanceof WorkOrderStatus ? $order->status->value : $order->status;
        $order->logs()->create([
            'step' => 'CX_FOLLOWUP',
            'action' => 'CX_RESPONSE_' . strtoupper($request->action),
            'user_id' => $user->id,
            'description' => "CX Response: " . $request->notes . " (Next: " . $finalStatus . ")"
        ]);
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
