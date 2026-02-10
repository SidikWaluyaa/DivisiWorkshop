<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\OrderPayment;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CsSpk;
use App\Models\CsActivity;
use App\Models\CsLead;
use Carbon\Carbon;
use App\Exports\FinanceMonthlyExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\WorkOrderLog;

class FinanceController extends Controller
{
    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function destroy($id)
    {
        // SECURITY: Check access policy
        $this->authorize('manageFinance', WorkOrder::class);

        $order = WorkOrder::findOrFail($id);
        
        // Basic check: maybe don't delete if it has payment records?
        // But user asked for Delete. 
        // Logic: if soft delete, it works.
        $order->delete();

        return redirect()->route('finance.index')->with('success', 'Data Finance berhasil dihapus.');
    }

    public function index(Request $request)
    {
        $statusFilter = $request->status ?? 'ALL';
        $search = $request->search;
        $tab = $request->tab ?? 'waiting_dp'; // Default: Menunggu DP

        $query = WorkOrder::query();

        // Common joins/selects calculation
        $totalBillSql = '(COALESCE(total_service_price, 0) + COALESCE(cost_oto, 0) + COALESCE(cost_add_service, 0) + COALESCE(shipping_cost, 0))';
        $query->withSum('payments', 'amount_total'); // adds payments_sum_amount_total

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Temporal Filtering (Date Range) - Wrapped in closures to prevent logical OR leaks
        if ($request->has('date_from') && $request->date_from) {
            $query->where(function($q) use ($request) {
                $q->whereDate('finance_entry_at', '>=', $request->date_from)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNull('finance_entry_at')->whereDate('created_at', '>=', $request->date_from);
                  });
            });
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where(function($q) use ($request) {
                $q->whereDate('finance_entry_at', '<=', $request->date_to)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNull('finance_entry_at')->whereDate('created_at', '<=', $request->date_to);
                  });
            });
        }

        // Tab Filtering Logic
        switch ($tab) {
            case 'waiting_dp':
                // High Priority: Assessment Done, Waiting for initial payment to start job
                // Also include WAITING_VERIFICATION for finance to confirm
                $query->whereIn('status', [
                    WorkOrderStatus::WAITING_PAYMENT->value,
                    WorkOrderStatus::WAITING_VERIFICATION->value,
                ]);
                break;

            case 'in_progress':
                // Workshop Active but Payments < Total Bill (Piutang)
                // Also include Logistics/Transit statuses so they don't "disappear" from Finance
                $query->whereIn('status', [
                    WorkOrderStatus::READY_TO_DISPATCH->value, // Logistik Gudang
                    WorkOrderStatus::OTW_WORKSHOP->value,       // In-Transit
                    WorkOrderStatus::ASSESSMENT->value,         // Assessment Workshop
                    WorkOrderStatus::PREPARATION->value,
                    WorkOrderStatus::SORTIR->value,
                    WorkOrderStatus::PRODUCTION->value,
                    WorkOrderStatus::QC->value,
                    WorkOrderStatus::CX_FOLLOWUP->value,
                ])
                ->where(function($q) {
                    $q->where('sisa_tagihan', '>', 0)
                      ->orWhereNull('sisa_tagihan')
                      ->orWhere('total_transaksi', '<=', 0); // Include orders without billing
                });
                break;

            case 'ready_pickup':
                // Finished but not fully paid (Gatekeeper Out)
                $query->whereIn('status', [
                    WorkOrderStatus::SELESAI->value,
                    WorkOrderStatus::DIANTAR->value,
                ])
                ->where(function($q) {
                    $q->where('sisa_tagihan', '>', 0)
                      ->orWhereNull('sisa_tagihan')
                      ->orWhere('total_transaksi', '<=', 0); // Include orders without billing
                });
                break;

            case 'completed':
                // History: Fully Paid orders (any status)
                $query->where('sisa_tagihan', '<=', 0)
                      ->where('total_transaksi', '>', 0);
                break;
                
            default:
                 // Fallback to waiting_dp
                 $query->where('status', WorkOrderStatus::WAITING_PAYMENT->value);
        }
        
        // MySQL Sort Fix
        $query->orderByRaw('finance_entry_at IS NULL, finance_entry_at DESC');
        $query->orderBy('created_at', 'DESC');

        // Eager load everything to prevent N+1
        $orders = $query->with(['services', 'payments', 'customer'])
                        ->paginate(20)
                        ->withQueryString();

        // Prepare Data for View (Calculations)
        $orders->getCollection()->transform(function($order) {
            $this->calculateFinanceFields($order);
            return $order;
        });

        // Get Finance Team for dropdowns
        $financeTeam = User::where('role', 'finance')->get(); 
        if($financeTeam->isEmpty()) $financeTeam = User::where('role', 'admin')->get(); // Fallback to admin if finance empty

        // Dynamic Stats for High-Volume Management
        $stats = [
            'total_today' => WorkOrder::whereDate('created_at', Carbon::today())->count(),
            'pending_dp' => WorkOrder::whereIn('status', [WorkOrderStatus::WAITING_PAYMENT->value, WorkOrderStatus::WAITING_VERIFICATION->value])->count(),
            'ready_pickup' => WorkOrder::whereIn('status', [WorkOrderStatus::SELESAI->value, WorkOrderStatus::DIANTAR->value])
                                       ->where('sisa_tagihan', '>', 0)
                                       ->count(),
            'revenue_today' => OrderPayment::whereDate('paid_at', Carbon::today())->sum('amount_total'),
        ];

        return view('finance.index', compact('orders', 'financeTeam', 'stats'));
    }

    public function show(WorkOrder $workOrder)
    {
        $order = $workOrder;
        $this->calculateFinanceFields($order);
        
        // Eager load payments if not already
        $order->load(['payments.pic', 'services', 'customer']);

        // Get Finance Team
        $financeTeam = User::where('role', 'finance')->get();
        if($financeTeam->isEmpty()) $financeTeam = User::where('role', 'admin')->get();

        return view('finance.show', compact('order', 'financeTeam'));
    }

    public function storePayment(Request $request, WorkOrder $workOrder)
    {
        // SECURITY: Check access policy
        $this->authorize('manageFinance', $workOrder);

        // Calculate current balance first
        $this->calculateFinanceFields($workOrder);
        
        $request->validate([
            'payment_type' => 'required|in:BEFORE,AFTER',
            'amount_total' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $workOrder->sisa_tagihan, // Cannot exceed remaining balance
            ],
            'payment_method' => 'required|string',
            'paid_at' => 'required|date',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'notes' => 'nullable|string|max:500',
        ], [
            'amount_total.max' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp ' . number_format($workOrder->sisa_tagihan, 0, ',', '.') . ')',
            'amount_total.min' => 'Jumlah pembayaran harus lebih dari 0',
            'proof_image.max' => 'Ukuran file maksimal 5MB',
            'proof_image.mimes' => 'Format file harus JPG atau PNG',
        ]);

        DB::transaction(function() use ($request, $workOrder) {
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $file = $request->file('proof_image');
                $filename = 'payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Ensure directory exists
                $directory = public_path('payment-proofs');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Move file to public/payment-proofs
                $file->move($directory, $filename);
                $proofPath = 'payment-proofs/' . $filename;
            }

            // 1. Create Payment Record
            OrderPayment::create([
                'work_order_id' => $workOrder->id,
                'type' => $request->payment_type,
                'pic_id' => Auth::id(), // Use current logged-in user
                'amount_total' => $request->amount_total,
                'payment_method' => $request->payment_method,
                'paid_at' => $request->paid_at,
                'notes' => $request->notes,
                'proof_image' => $proofPath
            ]);

            // 2. Recalculate Totals
            $this->calculateFinanceFields($workOrder);
            $workOrder->save(); // CRITICAL: Save the updated fields!
            
            // 3. Update Status Logic (Automatic restore to previous station)
            // Concept: If status is WAITING_PAYMENT and payment is made, return to Origin.
            if ($workOrder->status === WorkOrderStatus::WAITING_PAYMENT) {
                
                // Determine destination
                $destination = WorkOrderStatus::READY_TO_DISPATCH; // Modern Flow: Go to Dispatch Pool
                
                if ($workOrder->previous_status && 
                    !in_array($workOrder->previous_status instanceof WorkOrderStatus ? $workOrder->previous_status->value : $workOrder->previous_status, [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::WAITING_PAYMENT->value])) {
                    // Restore to previous valid workshop status
                    $prevStatus = $workOrder->previous_status instanceof WorkOrderStatus 
                        ? $workOrder->previous_status 
                        : WorkOrderStatus::tryFrom($workOrder->previous_status);
                    $destination = $prevStatus ?? WorkOrderStatus::READY_TO_DISPATCH;
                }

                $this->workflow->updateStatus(
                    $workOrder, 
                    $destination, 
                    "Pembayaran diterima via Finance. Lanjut ke " . $destination->label() . ".",
                    Auth::id()
                );
            }
        });

        return redirect()->back()->with('success', 'Pembayaran berhasil disimpan.');
    }

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        // SECURITY: Check access policy
        $this->authorize('manageFinance', $workOrder);

        // Manual Trigger to move status (e.g. "Lanjut ke Workshop")
        if ($request->action === 'move_to_prep') {
            if ($workOrder->status === WorkOrderStatus::WAITING_PAYMENT) {
                // Ensure there is some payment?
                $this->workflow->updateStatus($workOrder, WorkOrderStatus::READY_TO_DISPATCH, 'Pembayaran dikonfirmasi Finance.');
                return response()->json(['success' => true, 'message' => 'Order dipindahkan ke Pool Pengiriman Gudang.']);
            }
        }
        
        return response()->json(['success' => false, 'message' => 'Aksi tidak valid.']);
    }

    public function updateShipping(Request $request, WorkOrder $workOrder)
    {
        // SECURITY: Check access policy
        $this->authorize('manageFinance', $workOrder);

        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_type' => 'nullable|string',
            'shipping_zone' => 'nullable|string',
        ]);

        $workOrder->update([
            'shipping_cost' => $request->shipping_cost,
            'shipping_type' => $request->shipping_type,
            'shipping_zone' => $request->shipping_zone,
        ]);

        // Trigger recalculation and save
        $this->calculateFinanceFields($workOrder);
        $workOrder->save();

        // 3. AUDIT LOG: Record shipping cost change
        WorkOrderLog::create([
            'work_order_id' => $workOrder->id,
            'user_id' => Auth::id(),
            'status' => $workOrder->status,
            'notes' => "[FINANCE] Update Biaya Ongkir: Rp " . number_format($request->shipping_cost, 0, ',', '.') . " (" . ($request->shipping_type ?? 'Ekspedisi') . ")"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Biaya pengiriman berhasil diperbarui.',
            'new_total' => number_format($workOrder->total_transaksi, 0, ',', '.'),
            'new_shipping' => number_format($workOrder->shipping_cost, 0, ',', '.'),
            'new_sisa' => number_format($workOrder->sisa_tagihan, 0, ',', '.'),
        ]);
    }

    private function calculateFinanceFields($order)
    {
        // 1. Calculate Transaction Total using Model Logic
        // Use preloaded relations if existing, otherwise query sum
        $jasa = $order->relationLoaded('workOrderServices') 
                ? $order->workOrderServices->sum('cost') 
                : $order->workOrderServices()->sum('cost');
        
        $oto = $order->cost_oto ?? 0;
        $add = $order->cost_add_service ?? 0;
        $ongkir = $order->shipping_cost ?? 0;
        $discount = $order->discount ?? 0;
        
        // Automation: Ensure Unique Code exists
        $uniqueCode = $order->unique_code ?: $order->ensureUniqueCode();

        $order->total_transaksi = ($jasa + $oto + $add + $ongkir + $uniqueCode) - $discount;
        if($order->total_transaksi < 0) $order->total_transaksi = 0;
        
        // 2. Calculate Paid
        $paid = $order->payments->sum('amount_total');
        $order->total_paid = $paid;
        
        // 3. Status Tagihan
        $order->sisa_tagihan = $order->total_transaksi - $paid;
        
        if ($order->sisa_tagihan <= 0 && $order->total_transaksi > 0) {
            $order->status_pembayaran = 'L'; // Lunas
        } elseif ($paid > 0) {
            $order->status_pembayaran = 'DP/Cicil';
        } else {
            $order->status_pembayaran = 'Belum Bayar';
        }

        // 4. Parse SPK for CS and Category (as per Plan)
        $parsed = $this->parseSpk($order->spk_number);
        $order->category_spk = $parsed['category'];
        $order->cs_code = $parsed['cs_code'];
        
        // IMPORTANT: We do NOT save here automatically to avoid N+1 save loops in lists
        // The calling method should save if needed. 
        // But for display purposes, setting attributes is enough.
        
        return $order;
    }

    private function parseSpk($spk)
    {
        // Format: F-2505-31-9864-QA (Category-Date-Unknown-Phone-CS)
        // Adjust based on real format observations or generic split
        if (!$spk) return ['category' => '-', 'cs_code' => '-'];

        $parts = explode('-', $spk);
        $catMap = [
            'N' => 'Online', 'P' => 'Pickup', 'J' => 'Ojol', 'F' => 'Offline'
        ];
        
        $category = $catMap[$parts[0] ?? ''] ?? ($parts[0] ?? '-');
        $csCode = end($parts); // Assume last part is CS Code based on sample

        return ['category' => $category, 'cs_code' => $csCode];
    }

    /**
     * Export Payment History as printable PDF view
     */
    public function exportPaymentHistory(WorkOrder $workOrder)
    {
        $order = $workOrder;
        $this->calculateFinanceFields($order);
        
        $order->load(['payments.pic', 'services', 'customer']);
        
        return view('finance.payment-history-export', compact('order'));
    }

    // ==========================================
    // SHIPPING API PROXY (RAJAONGKIR)
    // ==========================================
    public function proxyShippingSearch(Request $request) 
    {
        $query = $request->input('q');
        if (!$query || strlen($query) < 3) return response()->json([]);

        $service = new \App\Services\RajaOngkirService();
        return response()->json($service->searchCities($query));
    }

    public function proxyShippingRates(Request $request)
    {
        $destination = $request->input('destination');
        $weight = $request->input('weight', 1000);

        $service = new \App\Services\RajaOngkirService();
        
        // RajaOngkir Starter needs courier specifications. 
        // We'll fetch JNE, POS, TIKI (Standard Indonesian couriers)
        $couriers = ['jne', 'pos', 'tiki'];
        $allRates = [];

        foreach ($couriers as $courier) {
            $costs = $service->getCost($destination, $weight, $courier);
            foreach ($costs as $cost) {
                $allRates[] = [
                    'courier' => strtoupper($courier),
                    'service' => $cost['service'],
                    'cost' => $cost['cost'][0]['value'],
                    'etd' => $cost['cost'][0]['etd'] . ' Days'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'rates' => $allRates
        ]);
    }
    public function printInvoice($id)
    {
        $order = WorkOrder::with(['payments', 'customer', 'workOrderServices.service'])->findOrFail($id);
        $this->calculateFinanceFields($order); // Recalculate Totals
        return view('finance.print-invoice', compact('order'));
    }

    public function updateDueDate(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $request->validate([
            'payment_due_date' => 'nullable|date',
        ]);

        $order->update([
            'payment_due_date' => $request->payment_due_date
        ]);

        return response()->json(['success' => true, 'message' => 'Tanggal jatuh tempo diperbarui']);
    }
    public function donations()
    {
        $orders = WorkOrder::where('status', WorkOrderStatus::DONASI)
                           ->orderBy('donated_at', 'DESC')
                           ->paginate(20);
        
        $stats = [
            'total_archived' => WorkOrder::where('status', WorkOrderStatus::DONASI)->count(),
            'total_value' => WorkOrder::where('status', WorkOrderStatus::DONASI)->sum('total_transaksi'),
        ];

        return view('finance.donations.index', compact('orders', 'stats'));
    }

    public function restoreFromDonation($id)
    {
        // SECURITY: Check access policy
        $this->authorize('manageFinance', WorkOrder::class);

        $order = WorkOrder::findOrFail($id);
        
        // Restore logic: check valid previous state? Or just default to SELESAI if finished?
        // Safest: Set to SELESAI if it has finished_date, otherwise PREPARATION.
        $status = WorkOrderStatus::SELESAI;
        if (!$order->finished_date) {
            $status = WorkOrderStatus::PREPARATION; // Assume work in progress
        }
        
        $order->update([
            'status' => $status,
            'notes' => $order->notes . "\n[RESTORE] Dikembalikan dari Donasi oleh " . Auth::user()->name . " pada " . now()->format('d/m/Y H:i')
        ]);

        return redirect()->back()->with('success', 'Data berhasil dikembalikan dari status Donasi.');
    }

    public function forceDonation($id)
    {
        // SECURITY: Check access policy
        $this->authorize('manageFinance', WorkOrder::class);

        $order = WorkOrder::findOrFail($id);
        
        if ($order->sisa_tagihan <= 0) {
            return redirect()->back()->with('error', 'Tidak bisa memindahkan order lunas ke Donasi.');
        }

        $order->update([
            'status' => WorkOrderStatus::DONASI,
            'donated_at' => now(),
            'notes' => $order->notes . "\n[MANUAL] Dipindahkan ke status DONASI (Manual) oleh " . Auth::user()->name . " pada " . now()->format('d/m/Y H:i')
        ]);

        return redirect()->route('finance.donations')->with('success', 'Order berhasil dipindahkan ke Data Donasi.');
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('manageFinance', WorkOrder::class);
        
        $tab = $request->tab ?? 'completed';
        $search = $request->search;
        $filename = 'Laporan_Finance_' . $tab . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new FinanceMonthlyExport($tab, $search, $request->date_from, $request->date_to), $filename);
    }
}
