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
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::ASSESSMENT->value);

        // 1. Hitung agregat antrean secara real-time untuk ditampilkan di tab filter
        $counts = [
            'all' => (clone $baseQuery)->count(),
            'lunas' => (clone $baseQuery)->whereHas('invoice', function($q) { $q->where('status', 'Lunas'); })->count(),
            'dp' => (clone $baseQuery)->whereHas('invoice', function($q) { $q->where('status', 'DP/Cicil'); })->count(),
            'belum_bayar' => (clone $baseQuery)->whereHas('invoice', function($q) { $q->where('status', 'Belum Bayar'); })->count(),
            'none' => (clone $baseQuery)->whereNull('invoice_id')->count(),
        ];

        // 2. Terapkan query filter
        $query = WorkOrder::with('invoice')->where('status', WorkOrderStatus::ASSESSMENT->value);
        
        // Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        
        // Filter Berdasarkan Status Invoice (Mendukung Multi-select Array)
        if ($request->has('invoice_status')) {
            $statuses = (array) $request->invoice_status;
            
            // Bersihkan array dari nilai kosong
            $statuses = array_filter($statuses, function($value) {
                return $value !== '';
            });

            if (!empty($statuses)) {
                $query->where(function($q) use ($statuses) {
                    $hasNone = in_array('none', $statuses);
                    $otherStatuses = array_diff($statuses, ['none']);

                    if ($hasNone) {
                        $q->whereNull('invoice_id');
                        if (!empty($otherStatuses)) {
                            $q->orWhereHas('invoice', function($subQ) use ($otherStatuses) {
                                $subQ->whereIn('status', $otherStatuses);
                            });
                        }
                    } else {
                        $q->whereHas('invoice', function($subQ) use ($otherStatuses) {
                            $subQ->whereIn('status', $otherStatuses);
                        });
                    }
                });
            }
        }
        
        // Paginate & Append request parameters
        $queue = $query->orderBy('updated_at', 'asc')
                       ->paginate(20)
                       ->appends($request->all());

        return view('assessment.index', compact('queue', 'counts'));
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
        $order = WorkOrder::with(['payments', 'invoice'])->findOrFail($id);

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

                // Deteksi apakah invoice sudah berstatus DP/Cicil atau Lunas
                $invoiceIsPaidOrDp = false;
                if ($order->invoice && in_array($order->invoice->status, ['DP/Cicil', 'Lunas'])) {
                    $invoiceIsPaidOrDp = true;
                }

                if ($invoiceIsPaidOrDp || ($alreadyPaid >= $finalTotal && $finalTotal > 0)) {
                    // Auto-pass Finance Gate as it was already verified at SPK stage or Invoice is already paid/DP
                    $order->update([
                        'status' => WorkOrderStatus::READY_TO_DISPATCH,
                        'current_location' => 'Gudang (Pool Kirim)',
                    ]);

                    $order->logs()->create([
                        'step' => 'ASSESSMENT',
                        'action' => 'AUTO_PASS_FINANCE',
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'description' => $invoiceIsPaidOrDp 
                            ? "Status Invoice '" . $order->invoice->status . "' terverifikasi. Siap dikirim ke Workshop (Masuk Antrian Manifest)."
                            : "Pembayaran mencukupi (Rp ".number_format($alreadyPaid, 0, ',', '.')."). Siap dikirim ke Workshop (Masuk Antrian Manifest)."
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

    public function printBulk(Request $request)
    {
        $idsString = $request->query('ids', '');
        if (empty($idsString)) {
            return redirect()->back()->with('error', 'Pilih minimal satu SPK untuk dicetak massal.');
        }

        $ids = explode(',', $idsString);
        $orders = WorkOrder::with(['workOrderServices.service', 'customer', 'photos', 'csLead'])
            ->whereIn('id', $ids)
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Data SPK tidak ditemukan.');
        }

        $barcodes = [];
        foreach ($orders as $order) {
            if (!$order->csLead) {
                $fallbackLead = \App\Models\CsLead::where('customer_phone', $order->customer_phone)->latest()->first();
                if ($fallbackLead) {
                    $order->setRelation('csLead', $fallbackLead);
                }
            }

            /** @var \SimpleSoftwareIO\QrCode\Generator $qr */
            $qr = QrCode::size(100);
            $barcodes[$order->id] = $qr->generate($order->spk_number);
        }

        return view('assessment.print-bulk', compact('orders', 'barcodes'));
    }

    public function printSpk($id)
    {
        $order = WorkOrder::with(['workOrderServices.service', 'customer', 'photos', 'csLead'])->findOrFail($id);
        
        // Fallback: If no direct lead linked, try to find by phone number
        if (!$order->csLead) {
            $fallbackLead = \App\Models\CsLead::where('customer_phone', $order->customer_phone)->latest()->first();
            if ($fallbackLead) {
                $order->setRelation('csLead', $fallbackLead);
            }
        }

        // Generate QR Code for SPK number
        /** @var \SimpleSoftwareIO\QrCode\Generator $qr */
        $qr = QrCode::size(100);
        $barcode = $qr->generate($order->spk_number);

        return view('assessment.print-spk-premium', compact('order', 'barcode'));
    }

    /**
     * Skip Assessment and move directly to Production
     */
    public function skipToDispatch($id)
    {
        $order = WorkOrder::with(['payments', 'workOrderServices', 'invoice'])->findOrFail($id);
        
        // Strict Check: Only Admin/Owner/Manager
        if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            abort(403, 'Unauthorized action. Only Admin/Manager can skip Assessment.');
        }

        try {
            $oldStatus = $order->status;

            // Calculate final total based on existing services
            $totalCost = $order->workOrderServices->sum('cost');
            $discount = $order->discount ?? 0;
            $finalTotal = max(0, $totalCost - $discount);

            // Calculate amount already paid
            $alreadyPaid = $order->payments->sum('amount_total');
            if ($order->spk_number) {
                $spk = \App\Models\CsSpk::where('spk_number', $order->spk_number)->first();
                if ($spk && $spk->dp_status === \App\Models\CsSpk::DP_PAID) {
                    $alreadyPaid = max($alreadyPaid, (float)$spk->dp_amount);
                }
            }

            // Deteksi apakah invoice sudah berstatus DP/Cicil atau Lunas
            $invoiceIsPaidOrDp = false;
            if ($order->invoice && in_array($order->invoice->status, ['DP/Cicil', 'Lunas'])) {
                $invoiceIsPaidOrDp = true;
            }

            DB::transaction(function () use ($order, $oldStatus, $finalTotal, $alreadyPaid, $invoiceIsPaidOrDp) {
                if ($invoiceIsPaidOrDp || ($alreadyPaid >= $finalTotal && $finalTotal > 0)) {
                    // Skenario A: Lunas / DP Cukup -> Siap Kirim ke Workshop (Antrean Manifest)
                    $order->status = WorkOrderStatus::READY_TO_DISPATCH;
                    $order->current_location = 'Gudang (Pool Kirim)';
                    $order->save();

                    // Dispatch Event
                    \App\Events\WorkOrderStatusUpdated::dispatch(
                        $order, 
                        $oldStatus, 
                        WorkOrderStatus::READY_TO_DISPATCH, 
                        'Direct to Dispatch (Skip Assessment - Lunas/DP Cukup atau Status Invoice DP/Lunas, Masuk Antrean Manifest)', 
                        \Illuminate\Support\Facades\Auth::id()
                    );
                } else {
                    // Skenario B: Pembayaran Kurang (BB) -> Masuk ke Antrean WAITING_PAYMENT untuk Finance Gate
                    $order->status = WorkOrderStatus::WAITING_PAYMENT;
                    $order->current_location = 'Gudang (Pool Kirim)';
                    $order->save();

                    // Dispatch Event
                    \App\Events\WorkOrderStatusUpdated::dispatch(
                        $order, 
                        $oldStatus, 
                        WorkOrderStatus::WAITING_PAYMENT, 
                        'Move to Waiting Payment (Skip Assessment - Pembayaran Belum Mencukupi / BB)', 
                        \Illuminate\Support\Facades\Auth::id()
                    );
                }
            });

            if ($invoiceIsPaidOrDp || ($alreadyPaid >= $finalTotal && $finalTotal > 0)) {
                return redirect()->back()->with('success', 'Order lunas/DP terverifikasi, berhasil dipindahkan ke Siap Kirim (Antrean Manifest)!');
            } else {
                return redirect()->back()->with('success', 'Order belum lunas (BB), berhasil dikirim ke antrean Menunggu Pembayaran (Finance Gate)!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }
    }
    /**
     * Quick save only technician notes before printing SPK
     */
    public function quickSaveNotes(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $request->validate([
            'technician_notes' => 'nullable|string'
        ]);

        $order->update([
            'technician_notes' => $request->technician_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil disimpan cepat.'
        ]);
    }

    /**
     * Bulk Skip Assessment and send multiple SPKs to Ready to Dispatch / Waiting Payment
     */
    public function skipToDispatchBulk(Request $request)
    {
        // Strict Check: Only Admin/Owner/Manager
        if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            abort(403, 'Unauthorized action. Only Admin/Manager can skip Assessment.');
        }

        $idsString = $request->input('ids', '');
        if (empty($idsString)) {
            return redirect()->back()->with('error', 'Pilih minimal satu SPK untuk diproses massal.');
        }

        $ids = explode(',', $idsString);
        $orders = WorkOrder::with(['payments', 'workOrderServices', 'invoice'])->whereIn('id', $ids)->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Data SPK tidak ditemukan.');
        }

        try {
            $processedToDispatch = 0;
            $processedToFinance = 0;

            DB::transaction(function () use ($orders, &$processedToDispatch, &$processedToFinance) {
                foreach ($orders as $order) {
                    $oldStatus = $order->status;

                    // Calculate final total based on existing services
                    $totalCost = $order->workOrderServices->sum('cost');
                    $discount = $order->discount ?? 0;
                    $finalTotal = max(0, $totalCost - $discount);

                    // Calculate amount already paid
                    $alreadyPaid = $order->payments->sum('amount_total');
                    if ($order->spk_number) {
                        $spk = \App\Models\CsSpk::where('spk_number', $order->spk_number)->first();
                        if ($spk && $spk->dp_status === \App\Models\CsSpk::DP_PAID) {
                            $alreadyPaid = max($alreadyPaid, (float)$spk->dp_amount);
                        }
                    }

                    // Deteksi apakah invoice sudah berstatus DP/Cicil atau Lunas
                    $invoiceIsPaidOrDp = false;
                    if ($order->invoice && in_array($order->invoice->status, ['DP/Cicil', 'Lunas'])) {
                        $invoiceIsPaidOrDp = true;
                    }

                    if ($invoiceIsPaidOrDp || ($alreadyPaid >= $finalTotal && $finalTotal > 0)) {
                        // Skenario A: Lunas / DP Cukup -> Langsung Lanjut ke READY_TO_DISPATCH
                        $order->status = WorkOrderStatus::READY_TO_DISPATCH;
                        $order->current_location = 'Gudang (Pool Kirim)';
                        $order->save();

                        // Dispatch Event
                        \App\Events\WorkOrderStatusUpdated::dispatch(
                            $order, 
                            $oldStatus, 
                            WorkOrderStatus::READY_TO_DISPATCH, 
                            'Direct to Dispatch (Bulk Skip Assessment - Lunas/DP Cukup atau Status Invoice DP/Lunas, Masuk Antrean Manifest)', 
                            \Illuminate\Support\Facades\Auth::id()
                        );
                        $processedToDispatch++;
                    } else {
                        // Skenario B: Pembayaran Kurang (BB) -> Masuk ke Antrean WAITING_PAYMENT untuk Finance Gate
                        $order->status = WorkOrderStatus::WAITING_PAYMENT;
                        $order->current_location = 'Gudang (Pool Kirim)';
                        $order->save();

                        // Dispatch Event
                        \App\Events\WorkOrderStatusUpdated::dispatch(
                            $order, 
                            $oldStatus, 
                            WorkOrderStatus::WAITING_PAYMENT, 
                            'Move to Waiting Payment (Bulk Skip Assessment - Pembayaran Belum Mencukupi / BB)', 
                            \Illuminate\Support\Facades\Auth::id()
                        );
                        $processedToFinance++;
                    }
                }
            });

            $msg = "Koleksi SPK diproses massal: {$processedToDispatch} SPK lunas/DP terverifikasi dikirim ke Siap Kirim (Antrean Manifest), {$processedToFinance} SPK belum lunas (BB) dikirim ke Menunggu Pembayaran (Finance Gate).";
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order massal: ' . $e->getMessage());
        }
    }
}
