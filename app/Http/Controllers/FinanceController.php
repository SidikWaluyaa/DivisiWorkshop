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
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Customer;
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

    /**
     * Show List of Grouped Invoices
     */
    public function indexInvoices(Request $request)
    {
        $search = $request->input('search');

        $paymentStatus = $request->input('payment_status'); // Get Payment Status filter
        $gateway = $request->input('gateway'); // Get Gateway filter
        
        $query = Invoice::with(['customer', 'workOrders' => function($q) {
            $q->select('id', 'invoice_id', 'spk_number', 'cs_code', 'shoe_brand', 'shoe_type', 'status');
        }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
                  });
            });
        }



        if ($paymentStatus) {
            $query->where('status', $paymentStatus);
        }

        if ($gateway) {
            $query->whereHas('workOrders', function($q) use ($gateway) {
                $q->where('cs_code', $gateway);
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Get unique gateways for filter
        $gateways = WorkOrder::whereNotNull('cs_code')
            ->where('cs_code', '!=', '-')
            ->distinct()
            ->orderBy('cs_code')
            ->pluck('cs_code');

        return view('finance.invoices', compact('invoices', 'search', 'paymentStatus', 'gateway', 'gateways'));
    }

    /**
     * Show UI to Create Grouped Invoice
     */
    public function createInvoice(Request $request)
    {
        $search = $request->input('search');
        $groupedOrders = [];
        $customer = null;

        if ($search) {
            // Find orders matching customer name, phone, or SPK that DO NOT have an invoice yet
            $orders = WorkOrder::with(['customer', 'workOrderServices.service'])
                ->whereNull('invoice_id')
                ->where(function ($q) use ($search) {
                    $q->where('customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                      ->orWhere('spk_number', 'LIKE', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();

            if ($orders->isNotEmpty()) {
                $customer = $orders->first();
                
                // Group by the "Arrival Suffix" (e.g., -2502-28-0012-SW)
                // This identifies all items brought in the same session
                $tempGroups = $orders->groupBy(function ($order) {
                    $parts = explode('-', $order->spk_number);
                    if (count($parts) >= 2) {
                        return implode('-', array_slice($parts, 1));
                    }
                    return $order->spk_number;
                });

                foreach ($tempGroups as $suffix => $groupOrders) {
                    // Try to find the original CsSpk record for this arrival to get the original SPK number and date
                    $csSpk = CsSpk::where('spk_number', 'LIKE', "%-{$suffix}")->first();
                    
                    // Display the Original CS SPK number (or suffix if not found) for the card header
                    $displayTitle = $csSpk ? $csSpk->spk_number : $suffix;
                    $arrivalDate = $csSpk ? $csSpk->created_at : $groupOrders->first()->created_at;

                    $groupedOrders[$displayTitle . '|' . $arrivalDate] = $groupOrders;
                }
            }
        }

        return view('finance.create-invoice', compact('groupedOrders', 'search', 'customer'));
    }

    /**
     * Store (Generate) Grouped Invoice from Selection
     */
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'work_order_ids' => 'required|array',
            'work_order_ids.*' => 'exists:work_orders,id',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $orders = WorkOrder::whereIn('id', $request->work_order_ids)
            ->whereNull('invoice_id')
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'Tidak ada Work Order valid yang dipilih (mungkin sudah dibuatkan invoice).');
        }

        DB::beginTransaction();
        try {
            // 1. Calculate Total from selected orders
            $totalAmount = 0;
            $totalPaid = 0;
            $totalDiscount = 0;
            $shippingCost = $request->shipping_cost ?? 0;

            foreach ($orders as $order) {
                $totalAmount += $order->total_transaksi;
                // Currently if they pay per order, we should theoretically aggregate it, 
                // but since this is a new invoice, paid is usually DP that was recorded.
                // Assuming DP is recorded somewhere or let kasir input later. 
                // For now, assume 0 paid at the instant of invoice creation, 
                // or sum existing payments if migrating.
                $totalPaid += $order->payments()->sum('amount_total'); 
                $totalDiscount += $order->discount ?? 0;
            }

            // Status Determination
            $status = 'Belum Bayar';
            if ($totalPaid >= ($totalAmount + $shippingCost - $totalDiscount) && ($totalAmount + $shippingCost) > 0) {
                $status = 'Lunas';
            } elseif ($totalPaid > 0) {
                $status = 'DP/Cicil';
            }

            // 2. Create Invoice
            $invoiceNumber = 'INV-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            
            // Derive customer_id from WorkOrder → Customer relationship (most reliable)
            $firstOrder = $orders->first();
            $customer = $firstOrder->customer; // Uses phone-based belongsTo relation
            $customerId = $customer ? $customer->id : null;

            // Fallback: try normalized phone lookup if relationship failed
            if (!$customerId) {
                $normalizedPhone = \App\Helpers\PhoneHelper::normalize($request->customer_phone);
                $customerId = Customer::where('phone', $normalizedPhone)->value('id');
            }

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId ?? 1, // Fallback to 1 if no customer match
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPaid,
                'discount' => $totalDiscount,
                'status' => $status,
                'due_date' => Carbon::now()->addDays(7), // Example default
            ]);

            // Link WorkOrders to this Invoice FIRST
            WorkOrder::whereIn('id', $request->work_order_ids)->update(['invoice_id' => $invoice->id]);

            // Then let the robust syncFinancials handle the recalculation and URL generation
            $invoice->syncFinancials();

            DB::commit();
            return redirect()->route('finance.invoices.index')->with('success', 'Invoice ' . $invoiceNumber . ' berhasil dibuat untuk ' . count($orders) . ' item.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat Invoice: ' . $e->getMessage());
        }
    }

    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['customer', 'workOrders' => function($q) {
            $q->with(['payments', 'workOrderServices']);
        }, 'invoicePayments' => function($q) {
            $q->with(['creator', 'verification.mutation'])->orderByDesc('created_at');
        }]);

        return view('finance.show-invoice', compact('invoice'));
    }

    /**
     * Delete an Invoice (only if status is "Belum Bayar" and no payments exist)
     * Unlinks all associated WorkOrders so they can be re-invoiced.
     */
    public function deleteInvoice(Invoice $invoice)
    {
        // Safety Guard 1: Only "Belum Bayar" invoices can be deleted
        if ($invoice->status !== 'Belum Bayar') {
            return back()->with('error', 'Hanya invoice berstatus "Belum Bayar" yang dapat dihapus.');
        }

        // Safety Guard 2: Cannot delete if there are any payments recorded
        $hasPayments = $invoice->payments()->exists() || $invoice->invoicePayments()->exists();
        if ($hasPayments) {
            return back()->with('error', 'Invoice ini sudah memiliki catatan pembayaran dan tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $invoiceNumber = $invoice->invoice_number;

            // 1. Unlink all WorkOrders from this Invoice
            WorkOrder::where('invoice_id', $invoice->id)->update(['invoice_id' => null]);

            // 2. Delete the Invoice record
            $invoice->delete();

            DB::commit();
            return redirect()->route('finance.invoices.index')
                ->with('success', 'Invoice ' . $invoiceNumber . ' berhasil dihapus. Work Order terkait sudah dilepas dan bisa dibuatkan invoice baru.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Invoice: ' . $e->getMessage());
        }
    }

    /**
     * Store (Record) Payment at the Invoice level
     */
    public function storeInvoicePayment(Request $request, Invoice $invoice)
    {
        // Calculate remaining balance dynamically
        $remainingBalance = $invoice->remaining_balance;

        $request->validate([
            'amount_total' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $remainingBalance, // Cannot exceed remaining balance
            ],
            'payment_method' => 'required|string',
            'paid_at' => 'required|date',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'notes' => 'nullable|string|max:500',
        ], [
            'amount_total.max' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp ' . number_format($remainingBalance, 0, ',', '.') . ')',
            'amount_total.min' => 'Jumlah pembayaran harus lebih dari 0',
            'proof_image.max' => 'Ukuran file maksimal 5MB',
            'proof_image.mimes' => 'Format file harus JPG atau PNG',
        ]);

        DB::transaction(function() use ($request, $invoice, $remainingBalance) {
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $file = $request->file('proof_image');
                $filename = 'payment_inv_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                $directory = public_path('payment-proofs');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                $file->move($directory, $filename);
                $proofPath = 'payment-proofs/' . $filename;
            }

            $newBalance = $remainingBalance - $request->amount_total;

            // 1. Create Payment Record on the Invoice
            OrderPayment::create([
                'invoice_id' => $invoice->id,
                'spk_number_snapshot' => $invoice->invoice_number,
                'type' => 'BEFORE', // Assuming initial payments, but can be adaptive
                'pic_id' => Auth::id(),
                'amount_total' => $request->amount_total,
                'payment_method' => $request->payment_method,
                'paid_at' => $request->paid_at,
                'notes' => $request->notes,
                'proof_image' => $proofPath,
                // Snapshots for Invoice
                'services_snapshot' => 'Pembayaran Tagihan Gabungan ' . $invoice->workOrders->count() . ' SPK',
                'customer_name_snapshot' => $invoice->customer->name ?? '',
                'customer_phone_snapshot' => $invoice->customer->phone ?? '',
                'total_bill_snapshot' => $invoice->total_amount,
                'discount_snapshot' => $invoice->discount,
                'shipping_cost_snapshot' => $invoice->shipping_cost,
                'balance_snapshot' => $newBalance
            ]);

            // 1b. Also record in invoice_payments for Payment Verification System
            // Cash/Tunai payments are auto-verified (no bank mutation to match)
            $isCash = in_array(strtoupper($request->payment_method), ['TUNAI', 'CASH']);
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount_total,
                'payment_date' => $request->paid_at,
                'notes' => ($request->notes ?? ('Pembayaran via ' . $request->payment_method)) 
                           . ($isCash ? ' [TUNAI - Auto Verified]' : ''),
                'verified' => $isCash,
                'created_by' => Auth::id(),
            ]);

            // 2. Sync Financials
            $invoice->syncFinancials();
            
            // 3. CASCADE EVENT: Update connected SPKs
            // If the work order is WAITING_PAYMENT, moving it to READY_TO_DISPATCH
            foreach ($invoice->workOrders as $spk) {
                if ($spk->status === WorkOrderStatus::WAITING_PAYMENT->value || $spk->status === WorkOrderStatus::WAITING_PAYMENT) {
                    $this->workflow->updateStatus(
                        $spk, 
                        WorkOrderStatus::READY_TO_DISPATCH, 
                        "Pembayaran Invoice [{$invoice->invoice_number}] diterima. Melanjutkan SPK ke Logistik/Gudang.",
                        Auth::id()
                    );
                }
            }
        });

        return redirect()->back()->with('success', 'Pembayaran sebesar Rp ' . number_format($request->amount_total, 0, ',', '.') . ' berhasil disimpan.');
    }

    public function updateInvoiceShipping(Request $request, Invoice $invoice)
    {
        // Require authorization
        // $this->authorize('manageFinance', \App\Models\WorkOrder::class);

        $request->validate([
            'shipping_cost' => 'required|numeric|min:0'
        ]);

        $invoice->shipping_cost = $request->shipping_cost;
        $invoice->save();
        $invoice->syncFinancials();

        return back()->with('success', 'Ongkos Kirim Invoice #'.$invoice->invoice_number.' berhasil diperbarui.');
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
        $orders = $query->with(['services', 'payments', 'customer', 'invoice'])
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
        
        // Eager load payments and invoice if not already
        $order->load(['payments.pic', 'services', 'customer', 'invoice']);

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

            // 0. Build Snapshots
            $servicesSummary = $workOrder->workOrderServices->map(function($ws) {
                $name = $ws->custom_service_name ?? ($ws->service ? $ws->service->name : 'Layanan');
                return "{$ws->workOrder->shoe_brand} - {$name} (Rp " . number_format($ws->cost, 0, ',', '.') . ")";
            })->implode("\n");

            $newBalance = $workOrder->sisa_tagihan - $request->amount_total;

            // 1. Create Payment Record
            OrderPayment::create([
                'work_order_id' => $workOrder->id,
                'spk_number_snapshot' => $workOrder->spk_number,
                'type' => $request->payment_type,
                'pic_id' => Auth::id(), // Use current logged-in user
                'amount_total' => $request->amount_total,
                'payment_method' => $request->payment_method,
                'paid_at' => $request->paid_at,
                'notes' => $request->notes,
                'proof_image' => $proofPath,
                // Snapshots
                'services_snapshot' => $servicesSummary,
                'customer_name_snapshot' => $workOrder->customer_name,
                'customer_phone_snapshot' => $workOrder->customer_phone,
                'total_bill_snapshot' => $workOrder->total_transaksi,
                'discount_snapshot' => $workOrder->discount ?? 0,
                'shipping_cost_snapshot' => $workOrder->shipping_cost ?? 0,
                'balance_snapshot' => $newBalance
            ]);

            // 2. Recalculate Totals
            $this->calculateFinanceFields($workOrder);
            $workOrder->save(); // CRITICAL: Save the updated fields!

            // If this SPK belongs to an Invoice, sync the Invoice as well
            if ($workOrder->invoice_id && $workOrder->invoice) {
                $workOrder->invoice->syncFinancials();

                // Also record in invoice_payments for Payment Verification System
                $isCash = in_array(strtoupper($request->payment_method), ['TUNAI', 'CASH']);
                InvoicePayment::create([
                    'invoice_id' => $workOrder->invoice_id,
                    'amount' => $request->amount_total,
                    'payment_date' => $request->paid_at,
                    'notes' => ($request->notes ?? ('Pembayaran SPK ' . $workOrder->spk_number . ' via ' . $request->payment_method))
                               . ($isCash ? ' [TUNAI - Auto Verified]' : ''),
                    'verified' => $isCash,
                    'created_by' => Auth::id(),
                ]);
            }
            
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

    private function calculateFinanceFields($order, $save = false)
    {
        $order->recalculateTotalPrice($save);
        return $order;
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

    public function updateEstimasi(Request $request, Invoice $invoice)
    {
        $request->validate([
            'estimasi_selesai' => 'required|date',
        ]);

        $invoice->update([
            'estimasi_selesai' => $request->estimasi_selesai
        ]);

        return back()->with('success', 'Estimasi selesai berhasil diperbarui.');
    }
}
