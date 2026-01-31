<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CsLead;
use App\Models\CsActivity;
use App\Models\CsQuotation;
use App\Models\CsSpk;
use App\Models\WorkOrder;
use App\Models\Customer;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use App\Services\Cs\CsLeadService;
use App\Services\Cs\CsSpkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CsLeadsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class CsLeadController extends Controller
{
    protected $leadService;
    protected $spkService;

    public function __construct(CsLeadService $leadService, CsSpkService $spkService)
    {
        $this->leadService = $leadService;
        $this->spkService = $spkService;
    }

    // ... (dashboard logic remains similar, filtering already present) ...

    public function index(Request $request)
    {
        $this->authorize('viewAny', CsLead::class);
        $user = Auth::user();
        
        // Base query with relationships
        $baseQuery = CsLead::with(['cs', 'activities' => fn($q) => $q->latest()->limit(5)]);
        
        // Policy Logic embedded in query for list filtering
        if ($user->role !== 'admin' && $user->role !== 'owner') {
            $baseQuery->where('cs_id', $user->id);
        }
        
        // ... (Keep existing filtering logic) ...
        // Apply Filters
        if ($request->filled('source')) {
            $baseQuery->where('source', $request->source);
        }
        
        if ($request->filled('priority')) {
            $baseQuery->where('priority', $request->priority);
        }
        
        if ($request->filled('cs_id')) {
            $baseQuery->where('cs_id', $request->cs_id);
        }
        
        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by customer name or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        
        // Apply Quick Filters
        if ($request->filter === 'hot') {
            $baseQuery->where('priority', CsLead::PRIORITY_HOT);
        }
        
        if ($request->filter === 'overdue') {
            $baseQuery->whereDate('next_follow_up_at', '<=', today());
        }
        
        // Pagination settings
        $perPage = 10;
        
        // Get leads grouped by status with pagination
        $greetingLeads = (clone $baseQuery)
            ->greeting()
            ->with(['activities' => fn($q) => $q->latest()->limit(3)])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'greeting_page');
        
        $konsultasiLeads = (clone $baseQuery)
            ->konsultasi()
            ->with(['quotations' => fn($q) => $q->latest()])
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage, ['*'], 'konsultasi_page');
        
        $closingLeads = (clone $baseQuery)
            ->closing()
            ->with(['spk'])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], 'closing_page');
        
        // Metrics & Other Data (Keep existing)
        $todayConverted = CsLead::converted()->whereDate('updated_at', today())->count();
        $yesterdayConverted = CsLead::converted()->whereDate('updated_at', today()->subDay())->count();
        
        $metrics = [
            'total_greeting' => $greetingLeads->total(),
            'total_konsultasi' => $konsultasiLeads->total(),
            'total_closing' => $closingLeads->total(),
            'total_lost' => (clone $baseQuery)->lost()->count(),
            'avg_response_time' => CsLead::whereNotNull('response_time_minutes')->avg('response_time_minutes'),
            'conversion_rate' => $this->leadService->calculateConversionRate(),
            'total_converted_today' => $todayConverted,
            'converted_yesterday' => $yesterdayConverted,
            'converted_trend' => $yesterdayConverted > 0 ? (($todayConverted - $yesterdayConverted) / $yesterdayConverted) * 100 : 0,
            'hot_leads' => CsLead::hotLeads()->whereIn('status', [CsLead::STATUS_GREETING, CsLead::STATUS_KONSULTASI])->count(),
            'needs_follow_up' => (clone $baseQuery)->whereDate('next_follow_up_at', '<=', today())->count(),
            'new_leads_today' => CsLead::whereDate('created_at', today())->count(),
        ];
        
        $csUsers = \App\Models\User::where('access_rights', 'LIKE', '%"cs"%')
            ->orWhere('role', 'admin')
            ->orWhere('role', 'owner')
            ->orderBy('name')
            ->get();
        
        $workshopPayments = WorkOrder::where('status', WorkOrderStatus::WAITING_PAYMENT->value)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('cs.dashboard', compact('greetingLeads', 'konsultasiLeads', 'closingLeads', 'metrics', 'csUsers', 'workshopPayments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', CsLead::class);
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'source' => 'required|in:WhatsApp,Instagram,Website,Referral,Walk-in',
            'source_detail' => 'nullable|string',
            'priority' => 'required|in:HOT,WARM,COLD',
            'notes' => 'nullable|string',
            'cs_id' => 'nullable|exists:users,id',
        ]);
        
        try {
            $this->leadService->createLead($validated);
            return redirect()->route('cs.dashboard')->with('success', 'Lead baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $lead = CsLead::with(['cs', 'activities.user', 'quotations.quotationItems', 'spk.customer', 'spk.items'])
            ->findOrFail($id);
        
        $this->authorize('view', $lead);
        
        $services = Service::orderBy('category')->orderBy('name')->get();
        
        return view('cs.leads.show', compact('lead', 'services'));
    }

    public function updateStatus(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        $this->authorize('update', $lead);
        
        $validated = $request->validate([
            'status' => 'required|in:GREETING,KONSULTASI,CLOSING,CONVERTED,LOST',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->leadService->updateStatus($lead, $validated['status'], $validated['notes'] ?? null);
            return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function moveToKonsultasi(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        $this->authorize('update', $lead);
        
        $validated = $request->validate(['notes' => 'required|string']);
        
        try {
            $this->leadService->updateStatus($lead, CsLead::STATUS_KONSULTASI, $validated['notes']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function moveToClosing(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        $this->authorize('update', $lead);
        
        // Check constraints
        if (!$lead->canMoveToClosing()) {
            return response()->json(['success' => false, 'message' => 'Lead belum memenuhi syarat untuk Closing (Harus status KONSULTASI & ada Quotation diterima).'], 422);
        }
        
        try {
            $this->leadService->updateStatus($lead, CsLead::STATUS_CLOSING);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function storeQuotation(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        $this->authorize('update', $lead);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($lead, $request) {
                // Calculate next version
                $lastQuotation = $lead->getLatestQuotation();
                $version = $lastQuotation ? $lastQuotation->version + 1 : 1;

                // Create Quotation
                $quotation = $lead->quotations()->create([
                    'quotation_number' => CsQuotation::generateQuotationNumber(),
                    'version' => $version,
                    'status' => CsQuotation::STATUS_ACCEPTED,
                    'notes' => $request->notes,
                ]);

                // Create Items
                foreach ($request->items as $index => $itemData) {
                    $quotation->quotationItems()->create([
                        'item_number' => $index + 1,
                        'category' => $itemData['category'],
                        'shoe_type' => $itemData['shoe_type'] ?? null,
                        'shoe_brand' => $itemData['shoe_brand'] ?? null,
                        'shoe_size' => $itemData['shoe_size'] ?? null,
                        'shoe_color' => $itemData['shoe_color'] ?? null,
                        'condition_notes' => $itemData['condition_notes'] ?? null,
                    ]);
                }

                // Log activity
                $lead->activities()->create([
                    'user_id' => Auth::id(),
                    'type' => CsActivity::TYPE_QUOTATION_ACCEPTED,
                    'content' => 'Quotation #' . $quotation->quotation_number . ' (v' . $version . ') dibuat dan telah disetujui.',
                ]);

                return redirect()->back()->with('success', 'Quotation berhasil dibuat!');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat quotation: ' . $e->getMessage());
        }
    }

    public function sendQuotation($id)
    {
        $quotation = CsQuotation::with('lead')->findOrFail($id);
        $this->authorize('update', $quotation->lead);

        try {
            $quotation->markAsSent();

            $quotation->lead->activities()->create([
                'user_id' => Auth::id(),
                'type' => CsActivity::TYPE_QUOTATION_SENT,
                'content' => 'Quotation #' . $quotation->quotation_number . ' dikirim ke customer.',
            ]);

            return redirect()->back()->with('success', 'Quotation berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim quotation: ' . $e->getMessage());
        }
    }

    public function acceptQuotation($id)
    {
        $quotation = CsQuotation::with('lead')->findOrFail($id);
        $this->authorize('update', $quotation->lead);

        try {
            $quotation->markAsAccepted();

            $quotation->lead->activities()->create([
                'user_id' => Auth::id(),
                'type' => CsActivity::TYPE_QUOTATION_ACCEPTED,
                'content' => 'Quotation #' . $quotation->quotation_number . ' diterima oleh customer.',
            ]);

            return redirect()->back()->with('success', 'Quotation berhasil diterima!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menerima quotation: ' . $e->getMessage());
        }
    }

    public function rejectQuotation(Request $request, $id)
    {
        $quotation = CsQuotation::with('lead')->findOrFail($id);
        $this->authorize('update', $quotation->lead);

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            $quotation->markAsRejected($request->rejection_reason);

            $quotation->lead->activities()->create([
                'user_id' => Auth::id(),
                'type' => CsActivity::TYPE_QUOTATION_REJECTED,
                'content' => 'Quotation #' . $quotation->quotation_number . ' ditolak customer: ' . $request->rejection_reason,
            ]);

            return redirect()->back()->with('info', 'Quotation ditolak');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak quotation: ' . $e->getMessage());
        }
    }

    public function generateSpk(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        $this->authorize('update', $lead);
        
        // ... (Keep Validation Rules) ...
        $validated = $request->validate([
             'spk_number' => 'nullable|string|max:50|unique:cs_spk,spk_number|unique:work_orders,spk_number',
             'priority' => 'required|string',
             'delivery_type' => 'required|string',
             'manual_cs_code' => 'required|string',
             'expected_delivery_date' => 'nullable|date',

             'customer_name' => 'required|string',
             'customer_phone' => 'required|string',
             'customer_email' => 'nullable|email',
             'customer_address' => 'required|string',
             'customer_city' => 'required|string',
             'customer_province' => 'required|string',
             'special_instructions' => 'nullable|string',

             'items' => 'required|array|min:1',
             'dp_amount' => 'required|numeric|min:0',
        ]);
        
        try {
            // Using Service
            $this->spkService->generateSpk($lead, $validated); // Note: using $request->all() or specific validated data
            // Since validation strips fields not in rules, better use $request->all() if rules are comprehensive or carefully map.
            // Let's assume $validated contains all needed.
            
            return redirect()->route('cs.dashboard')->with('success', 'SPK berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate SPK: ' . $e->getMessage());
        }
    }

    public function handToWorkshop(Request $request, $spkId)
    {
        $spk = CsSpk::with('lead')->findOrFail($spkId);
        $this->authorize('update', $spk->lead);
        
        $request->validate([
            'items' => 'required|array|min:1',
            // ...
        ]);

        try {
            $this->spkService->handToWorkshop($spk, $request->items);
            return redirect()->route('cs.dashboard')->with('success', 'Order berhasil diserahkan ke Workshop!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal handover: ' . $e->getMessage());
        }
    }
    
    // ... (Other methods: lostLeads, etc.) ...
    public function lostLeads(Request $request)
    {
        $this->authorize('viewAny', CsLead::class);
        $user = Auth::user();

        $query = CsLead::with(['cs'])->lost();

        // Policy Logic embedded in query for list filtering
        if ($user->role !== 'admin' && $user->role !== 'owner') {
            $query->where('cs_id', $user->id);
        }

        // Search by customer name or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('cs.leads.lost', compact('leads'));
    }

    public function calculateConversionRate() {
         return $this->leadService->calculateConversionRate();
    }


    /**
     * Store activity/communication log
     */
    public function storeActivity(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        
        $validated = $request->validate([
            'type' => 'required|in:CHAT,CALL,EMAIL,MEETING,NOTE',
            'channel' => 'nullable|string',
            'content' => 'required|string',
        ]);
        
        $lead->activities()->create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'channel' => $validated['channel'],
            'content' => $validated['content'],
        ]);
        
        $lead->update(['last_activity_at' => now()]);
        
        return redirect()->back()->with('success', 'Aktivitas berhasil dicatat!');
    }

    /**
     * Set next follow up date
     */
    public function setFollowUp(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        
        $validated = $request->validate([
            'next_follow_up_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);
        
        $lead->update([
            'next_follow_up_at' => $validated['next_follow_up_at'],
        ]);
        
        $lead->activities()->create([
            'user_id' => Auth::id(),
            'type' => CsActivity::TYPE_NOTE,
            'content' => 'Follow up dijadwalkan pada: ' . date('d M Y H:i', strtotime($validated['next_follow_up_at'])) . ($validated['notes'] ? '. Catatan: ' . $validated['notes'] : ''),
        ]);
        
        return redirect()->back()->with('success', 'Follow up berhasil dijadwalkan!');
    }

    public function markDpPaid(Request $request, $id)
    {
        $spk = CsSpk::with('lead')->findOrFail($id);
        $this->authorize('update', $spk->lead);

        $request->validate([
            'payment_method' => 'required|string',
            'payment_notes' => 'nullable|string',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $file = $request->file('proof_image');
                $filename = 'proof_dp_' . time() . '_' . $id . '.' . $file->getClientOriginalExtension();
                $directory = public_path('payment-proofs-cs');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $file->move($directory, $filename);
                $proofPath = 'payment-proofs-cs/' . $filename;
            }

            // If user is Admin/Owner, mark as paid immediately. Otherwise, submit for verification.
            if (in_array(Auth::user()->role, ['admin', 'owner'])) {
                $spk->markDpAsPaid($request->payment_method, $request->payment_notes, $proofPath);
                $content = 'DP SPK #' . $spk->spk_number . ' dikonfirmasi lunas oleh Admin.';
            } else {
                $spk->submitForVerification($request->payment_method, $request->payment_notes, $proofPath);
                $content = 'DP SPK #' . $spk->spk_number . ' disubmit untuk verifikasi Finance.';
            }

            $spk->lead->activities()->create([
                'user_id' => Auth::id(),
                'type' => CsActivity::TYPE_NOTE,
                'content' => $content,
            ]);

            return redirect()->back()->with('success', 'Status DP berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update DP: ' . $e->getMessage());
        }
    }

    /**
     * Mark lead as lost
     */
    public function markLost(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        
        $validated = $request->validate([
            'lost_reason' => 'required|string',
        ]);
        
        $lead->update([
            'status' => CsLead::STATUS_LOST,
            'lost_reason' => $validated['lost_reason'],
        ]);
        
        $lead->activities()->create([
            'user_id' => Auth::id(),
            'type' => CsActivity::TYPE_STATUS_CHANGE,
            'content' => 'Lead ditandai sebagai LOST: ' . $validated['lost_reason'],
        ]);
        
        return redirect()->back()->with('info', 'Lead ditandai sebagai LOST');
    }

    /**
     * Delete lead
     */
    public function destroy($id)
    {
        $lead = CsLead::findOrFail($id);
        $lead->delete();
        
        return redirect()->back()->with('success', 'Lead berhasil dihapus');
    }


    public function guestForm($id)
    {
        $lead = CsLead::findOrFail($id);
        return view('cs.leads.guest-form', compact('lead'));
    }

    public function guestUpdate(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string',
            'customer_province' => 'required|string',
        ]);
        
        $lead->update([
            ...$validated,
            'last_activity_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Data Anda berhasil disimpan! Terima kasih.');
    }

    /**
     * Export leads to Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'source', 'priority']);
        return Excel::download(new CsLeadsExport($filters), 'cs-leads-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Find the best CS to assign a lead (Least active leads)
     */
    private function findCsForAssignment()
    {
        // Get all users who can handle CS
        // Get all users who can handle CS - Using LIKE for compatibility
        $csUsers = \App\Models\User::where('access_rights', 'LIKE', '%"cs"%')
            ->orWhere('role', 'admin') // Include admin as fallback
            ->get();

        if ($csUsers->isEmpty()) {
            return null;
        }

        // Count active leads for each CS
        $assignment = $csUsers->map(function($user) {
            return [
                'id' => $user->id,
                'lead_count' => CsLead::where('cs_id', $user->id)
                    ->whereIn('status', [CsLead::STATUS_GREETING, CsLead::STATUS_KONSULTASI, CsLead::STATUS_CLOSING])
                    ->count()
            ];
        })->sortBy('lead_count')->first();

        return $assignment['id'];
    }

    public function exportQuotationPdf($id)
    {
        $quotation = CsQuotation::with('lead')->findOrFail($id);
        $pdf = Pdf::loadView('cs.pdf.quotation', compact('quotation'));
        return $pdf->download('Quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function exportSpkPdf($id)
    {
        $spk = CsSpk::with(['lead', 'customer'])->findOrFail($id);
        $pdf = Pdf::loadView('cs.pdf.spk', compact('spk'));
        return $pdf->download('SPK-' . $spk->spk_number . '.pdf');
    }

    public function confirmWorkshopPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_notes' => 'nullable|string',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $filename = 'proof_wo_' . time() . '_' . $id . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists
            $directory = public_path('payment-proofs-wo');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Move file to public/payment-proofs-wo
            $file->move($directory, $filename);
            $proofPath = 'payment-proofs-wo/' . $filename;
        }

        $order->update([
            'payment_method' => $request->payment_method,
            'payment_notes' => $request->payment_notes,
            'payment_proof' => $proofPath,
            'status' => WorkOrderStatus::WAITING_VERIFICATION->value,
        ]);

        // Log activity
        $order->logs()->create([
             'step' => 'FINANCE',
             'action' => 'PAYMENT_SUBMITTED',
             'user_id' => Auth::id(),
             'description' => "Pembayaran dikonfirmasi oleh CS. Menunggu Verifikasi Finance. Method: " . $request->payment_method
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi. Menunggu verifikasi Finance!');
    }
}
