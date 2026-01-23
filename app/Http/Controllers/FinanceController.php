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
use Carbon\Carbon;

class FinanceController extends Controller
{
    protected $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function destroy($id)
    {
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
        $tab = $request->tab ?? 'waiting_dp'; // Default: Menunggu DP (Assessment -> Finance)

        $query = WorkOrder::query();

        // Common joins/selects calculation
        $totalBillSql = '(COALESCE(total_service_price, 0) + COALESCE(cost_oto, 0) + COALESCE(cost_add_service, 0) + COALESCE(shipping_cost, 0))';
        $query->withSum('payments', 'amount_total'); // adds payments_sum_amount_total

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Tab Filtering Logic
        switch ($tab) {
            case 'waiting_dp':
                // High Priority: Assessment Done, Waiting for initial payment to start job
                $query->where('status', WorkOrderStatus::WAITING_PAYMENT->value);
                break;

            case 'in_progress':
                // Workshop Active but Payments < Total Bill (Piutang)
                // Use stored columns for accurate filtering
                $query->whereIn('status', [
                    WorkOrderStatus::PREPARATION->value,
                    WorkOrderStatus::SORTIR->value,
                    WorkOrderStatus::PRODUCTION->value,
                    WorkOrderStatus::QC->value,
                    WorkOrderStatus::CX_FOLLOWUP->value, // Include CX Follow Up so it doesn't disappear
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

        $orders = $query->paginate(20)->withQueryString();

        // Prepare Data for View (Calculations)
        $orders->getCollection()->transform(function($order) {
            $this->calculateFinanceFields($order);
            return $order;
        });

        // If tab is requested, we might want to visually filter, but pagination makes it hard.
        // Let's accept that the "All" view is the default dashboard.
        // But if the user really wants separate tables, we can filter collection after query?
        // No, that breaks pagination.
        // Let's just create the "Notes" field first as requested by "mengembangkan...".
        // And I will add a 'status_payment' filter to the query later if strictly needed.
        
        // Let's keep the Controller simple for now and focus on the View enhancements.
        
        // Get Finance Team for dropdowns
        $financeTeam = User::where('role', 'finance')->get(); 
        if($financeTeam->isEmpty()) $financeTeam = User::where('role', 'admin')->get(); // Fallback to admin if finance empty

        return view('finance.index', compact('orders', 'financeTeam'));
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
                $destination = WorkOrderStatus::PREPARATION; // Default
                
                if ($workOrder->previous_status && 
                    !in_array($workOrder->previous_status, [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::WAITING_PAYMENT->value])) {
                    // Restore to previous valid workshop status (e.g. PRODUCTION, SORTIR)
                    $destination = WorkOrderStatus::tryFrom($workOrder->previous_status) ?? WorkOrderStatus::PREPARATION;
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
        // Manual Trigger to move status (e.g. "Lanjut ke Workshop")
        if ($request->action === 'move_to_prep') {
            if ($workOrder->status === WorkOrderStatus::WAITING_PAYMENT) {
                // Ensure there is some payment?
                $this->workflow->updateStatus($workOrder, WorkOrderStatus::PREPARATION, 'Pembayaran dikonfirmasi Finance.');
                return response()->json(['success' => true, 'message' => 'Order dipindahkan ke Workshop.']);
            }
        }
        
        return response()->json(['success' => false, 'message' => 'Aksi tidak valid.']);
    }

    public function updateShipping(Request $request, WorkOrder $workOrder)
    {
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
        // Ensure relations loaded
        $order->load(['services', 'payments', 'customer']);

        // 1. Calculate Transaction Total
        // Use Elvis operator ?: to fall back if total_service_price is 0 or null
        $jasa = $order->total_service_price ?: $order->services->sum(fn($s) => $s->pivot->cost);
        $oto = $order->cost_oto ?? 0;
        $add = $order->cost_add_service ?? 0;
        $ongkir = $order->shipping_cost ?? 0;
        $discount = $order->discount ?? 0;

        $order->total_transaksi = ($jasa + $oto + $add + $ongkir) - $discount;
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
        
        // Eager load payments
        $order->load(['payments.pic', 'services', 'customer']);
        
        // Return dedicated print view
        return view('finance.payment-history-export', compact('order'));
    }
}
