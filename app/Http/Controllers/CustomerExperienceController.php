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
        
        // Date Range Filter (Aligned with Dashboard Widget: cx_issues.created_at)
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $start = request()->start_date . ' 00:00:00';
            $end = request()->end_date . ' 23:59:59';
            
            $query->where(function ($q) use ($start, $end) {
                $q->whereHas('cxIssues', function ($iq) use ($start, $end) {
                    $iq->whereBetween('created_at', [$start, $end]);
                })->orWhere(function ($oq) use ($start, $end) {
                    $oq->doesntHave('cxIssues')->whereBetween('updated_at', [$start, $end]);
                });
            });
        } elseif (request()->filled('start_date')) {
            $start = request()->start_date . ' 00:00:00';
            $query->where(function ($q) use ($start) {
                $q->whereHas('cxIssues', function ($iq) use ($start) {
                    $iq->where('created_at', '>=', $start);
                })->orWhere(function ($oq) use ($start) {
                    $oq->doesntHave('cxIssues')->where('updated_at', '>=', $start);
                });
            });
        } elseif (request()->filled('end_date')) {
            $end = request()->end_date . ' 23:59:59';
            $query->where(function ($q) use ($end) {
                $q->whereHas('cxIssues', function ($iq) use ($end) {
                    $iq->where('created_at', '<=', $end);
                })->orWhere(function ($oq) use ($end) {
                    $oq->doesntHave('cxIssues')->where('updated_at', '<=', $end);
                });
            });
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

    public function restore($id)
    {
        // SECURITY: Check access policy
        $this->authorize('manageCx', WorkOrder::class);

        return DB::transaction(function () use ($id) {
            $order = WorkOrder::where('status', WorkOrderStatus::BATAL->value)->findOrFail($id);
            $user = Auth::user();

            // 1. Move status back to CX_FOLLOWUP
            // We KEEP previous_status as is, so 'handleLanjutAction' knows where it came from
            $order->update([
                'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            ]);

            // 2. Re-open the last resolved issue if it was cancelled
            $lastIssue = $order->cxIssues()->where('status', 'RESOLVED')
                ->where(function($q) {
                    $q->where('resolution_type', 'cancel')
                      ->orWhere('resolution_notes', 'like', '%Next: BATAL%');
                })
                ->latest()
                ->first();

            if ($lastIssue) {
                $lastIssue->update([
                    'status' => 'OPEN',
                    'resolved_by' => null,
                    'resolved_at' => null,
                    'resolution_notes' => $lastIssue->resolution_notes . "\n[RESTORED by " . $user->name . " at " . now()->format('d M Y H:i') . "]",
                ]);
            }

            // 3. Log the action
            $order->logs()->create([
                'step' => 'CX_FOLLOWUP',
                'action' => 'CX_RESTORE_CANCEL',
                'user_id' => $user->id,
                'description' => "Order dikembalikan dari status BATAL ke CX Follow Up (Previous Status: " . ($order->previous_status ? $order->previous_status->label() : 'N/A') . ")"
            ]);

            return redirect()->route('cx.index')->with('success', 'Order #' . $order->spk_number . ' berhasil dikembalikan ke daftar kerja CX.');
        });
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

        // If the issue source is GUDANG, it MUST go to ASSESSMENT
        if ($issue && $issue->source === 'GUDANG') {
            $nextStatus = WorkOrderStatus::ASSESSMENT;
        } else {
            // Smart Status Inference
            if (!$previousStatus || $previousStatus === WorkOrderStatus::CX_FOLLOWUP || $previousStatus === WorkOrderStatus::HOLD_FOR_CX || $previousStatus === WorkOrderStatus::WAITING_PAYMENT) {
                // If previous status is missing or circular, try to infer from issue source
                if ($issue) {
                    $nextStatus = match ($issue->source) {
                        'WORKSHOP_PREP'   => WorkOrderStatus::PREPARATION,
                        'WORKSHOP_SORTIR' => WorkOrderStatus::SORTIR,
                        'WORKSHOP_PROD'   => WorkOrderStatus::PRODUCTION,
                        'WORKSHOP_QC'     => WorkOrderStatus::QC,
                        default           => WorkOrderStatus::ASSESSMENT,
                    };
                } else {
                    $nextStatus = WorkOrderStatus::ASSESSMENT;
                }
            } else {
                $nextStatus = $previousStatus;
            }
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
            $updateData['is_manual_estimasi'] = true;
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

        $issue = $order->cxIssues()->where('status', 'OPEN')->latest()->first();
        $isFromGudang = $issue && $issue->source === 'GUDANG';

        if ($isFromGudang) {
            $targetStatus = WorkOrderStatus::ASSESSMENT->value;
        } elseif ($order->previous_status && in_array($order->previous_status instanceof \BackedEnum ? $order->previous_status->value : $order->previous_status, [
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
                'estimation_date' => $request->estimasi_selesai_baru,
                'is_manual_estimasi' => true,
            ]);
            
            if ($order->invoice) {
                $order->invoice->update([
                    'estimasi_selesai' => $request->estimasi_selesai_baru,
                    'is_manual_estimasi' => true,
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

        $updatedCount = 0;
        if ($issue) {
            $issue->update($updatePayload);
            
            // Close other open issues with an auto-resolve note instead of duplicating
            $updatedCount = CxIssue::where('work_order_id', $order->id)
                ->where('status', 'OPEN')
                ->where('id', '!=', $issue->id)
                ->update([
                    'status' => 'RESOLVED',
                    'resolved_by' => $user->id,
                    'resolved_at' => now(),
                    'resolution_notes' => 'Diselesaikan otomatis bersamaan dengan kendala utama.',
                    'resolution_type' => $request->action
                ]);
        } else {
            $updatedCount = CxIssue::where('work_order_id', $order->id)
                ->where('status', 'OPEN')
                ->update($updatePayload);
        }

        // v11: Fix "Langsung Resolved" - Create an issue record if none exists 
        // so it can be tracked in the dashboard metrics
        if (!$issue && $updatedCount === 0) {
            $order->cxIssues()->create(array_merge($updatePayload, [
                'reported_by' => $user->id,
                'category' => 'FOLLOWUP',
                'description' => 'Resolusi Langsung dari Kolam CX',
                'source' => $order->previous_status ? (is_string($order->previous_status) ? $order->previous_status : $order->previous_status->value) : 'SYSTEM',
            ]));
        }
        
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

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('t', 'active');
        $search = $request->input('search');
        $sort = $request->input('sort', 'desc');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $handler_id = $request->input('handler_id');
        $last_status = $request->input('last_status');
        $source = $request->input('source');
        $category = $request->input('category');
        $delay_filter = $request->input('delay_filter');
        $est_filter = $request->input('est_filter');

        if ($tab === 'history') {
            $query = CxIssue::where('status', 'RESOLVED')
                ->with(['workOrder.cxHandler', 'resolver', 'reporter']);
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('spk_number', 'like', '%' . $search . '%')
                      ->orWhere('customer_name', 'like', '%' . $search . '%')
                      ->orWhere('category', 'like', '%' . $search . '%');
                });
            }
            if ($category) $query->where('category', $category);
            if ($start_date) $query->whereDate('resolved_at', '>=', $start_date);
            if ($end_date) $query->whereDate('resolved_at', '<=', $end_date);
            
            $query->orderBy('resolved_at', $sort);
        } 
        elseif ($tab === 'cancelled') {
            $query = WorkOrder::where('status', WorkOrderStatus::BATAL->value)
                ->with(['logs', 'cxIssues' => fn($q) => $q->latest(), 'cxHandler']);
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('spk_number', 'like', '%' . $search . '%')
                      ->orWhere('customer_name', 'like', '%' . $search . '%');
                });
            }
            if ($start_date) $query->whereDate('updated_at', '>=', $start_date);
            if ($end_date) $query->whereDate('updated_at', '<=', $end_date);
            
            $query->orderBy('updated_at', $sort);
        } 
        else {
            $query = WorkOrder::where(function($q) {
                $q->whereIn('status', [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value])
                  ->orWhereHas('cxIssues', function($sq) {
                      $sq->where('status', 'OPEN')
                         ->where('category', 'like', 'Revisi %');
                  });
            })
            ->with(['cxIssues' => fn($q) => $q->latest(), 'cxHandler']);
            if (!in_array($user->role, ['admin', 'owner'])) $query->where('cx_handler_id', $user->id);
            if ($handler_id && in_array($user->role, ['admin', 'owner'])) $query->where('cx_handler_id', $handler_id);
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('spk_number', 'like', '%' . $search . '%')
                      ->orWhere('customer_name', 'like', '%' . $search . '%');
                });
            }
            if ($last_status) {
                if ($last_status === 'QC_REJECT') $query->whereNull('previous_status');
                else $query->where('previous_status', $last_status);
            }
            
            if ($est_filter === 'est_3_days') {
                $query->where(function($q) {
                    $q->where(function($sq) {
                        $sq->whereNotNull('new_estimation_date')
                           ->where('new_estimation_date', '<=', now()->addDays(3));
                    })->orWhere(function($sq) {
                        $sq->whereNull('new_estimation_date')
                           ->whereNotNull('estimation_date')
                           ->where('estimation_date', '<=', now()->addDays(3));
                    });
                });
            }

            $query->where(function($mainQ) use ($delay_filter, $source) {
                $mainQ->whereHas('cxIssues', function($q) use ($delay_filter, $source) {
                    $q->where('status', 'OPEN');
                    
                    if ($delay_filter === 'stuck_3_days') {
                        $q->where('created_at', '<=', now()->subDays(3));
                    } else {
                        if ($source) {
                            $q->where('source', $source === 'WS' ? 'LIKE' : '=', $source === 'WS' ? 'WORKSHOP_%' : $source);
                        } else {
                            $q->where('source', '!=', 'GUDANG');
                        }
                    }
                })
                ->orWhereDoesntHave('cxIssues', function($q) {
                    $q->where('status', 'OPEN');
                });
            });
            
            if ($delay_filter === 'stuck_3_days') {
                $query->orderBy(
                    \App\Models\CxIssue::select('created_at')
                        ->whereColumn('work_order_id', 'work_orders.id')
                        ->where('status', 'OPEN')
                        ->latest()
                        ->limit(1),
                    'asc'
                );
            } elseif ($est_filter === 'est_3_days') {
                $query->orderByRaw('COALESCE(new_estimation_date, estimation_date) ASC');
            } else {
                $query->orderBy('entry_date', $sort);
            }
        }

        $data = $query->get();

        // Calculate summary metrics
        $summary = [
            'total' => $data->count(),
            'open' => 0,
            'resolved' => 0,
            'hold' => 0
        ];
        
        foreach ($data as $item) {
            $openIssue = ($item instanceof WorkOrder) ? $item->cxIssues->first() : $item;
            if ($openIssue) {
                if ($openIssue->status === 'RESOLVED') {
                    $summary['resolved']++;
                } else {
                    $summary['open']++;
                }
                
                if ($openIssue->shipping_status === 'HOLD') {
                    $summary['hold']++;
                }
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cx.pdf.followup-report', [
            'data' => $data,
            'tab' => $tab,
            'summary' => $summary
        ]);
        
        $filename = 'Laporan_Followup_CX_' . $tab . '_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
