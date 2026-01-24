<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CsLead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CsLeadController extends Controller
{
    /**
     * Display the CS Dashboard (Kanban / List).
     */
    public function destroy($id)
    {
        $lead = CsLead::findOrFail($id);
        $lead->delete();

        return redirect()->back()->with('success', 'Lead berhasil dihapus.');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base Query
        $query = CsLead::with('cs');

        // Filter by CS (if strictly personal view needed, or allow seeing all)
        // Usually CS sees their own leads, Admin sees all.
        // For now, let's show ALL to foster collaboration, or filter by user preference?
        // Let's defaulted to "My Leads" but allow "All".
        if ($request->has('view_all') && $user->role === 'admin') {
            // No filter
        } else {
            // Default: Show only assigned leads if standard user? 
            // Or maybe show all but highlight own?
            // "CS" flow usually implies owning the lead.
            // Let's filter by CS ID if standard technician/staff role.
            // Let's filter by CS ID if standard technician/staff role.
            if ($user->role !== 'admin') {
                $query->where('cs_id', $user->id);
            }
        }

        // Active Leads (For Kanban) - exclude CLOSED
        $activeLeads = (clone $query)->where('status', '!=', CsLead::STATUS_CLOSED)
                                     ->orderBy('last_updated_at', 'desc')
                                     ->get();

        // History Leads (For Table) - only CLOSED
        $historyLeads = (clone $query)->where('status', CsLead::STATUS_CLOSED)
                                      ->orderBy('last_updated_at', 'desc')
                                      ->paginate(20);

        // Group by Status for Kanban
        $lanes = [
            'NEW' => $activeLeads->where('status', CsLead::STATUS_NEW),
            'KONSULTASI' => $activeLeads->where('status', CsLead::STATUS_KONSULTASI),
            'INVEST_GREETING' => $activeLeads->where('status', CsLead::STATUS_INVEST_GREETING),
            'INVEST_KONSULTASI' => $activeLeads->where('status', CsLead::STATUS_INVEST_KONSULTASI),
        ];

        return view('cs.dashboard.index', compact('lanes', 'historyLeads'));
    }

    /**
     * Store a newly created resource in storage (Chat Masuk).
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_phone' => 'required|string',
            'customer_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            CsLead::create([
                'customer_phone' => $request->customer_phone,
                'customer_name' => $request->customer_name,
                'notes' => $request->notes,
                'cs_id' => Auth::id(),
                'status' => CsLead::STATUS_NEW,
                'last_updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Lead baru berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat lead: ' . $e->getMessage());
        }
    }

    /**
     * Update lead status (Drag & Drop or Button Click).
     */
    public function updateStatus(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        $status = $request->input('status'); // NEW, KONSULTASI, etc.

        if ($status) {
            $lead->status = $status;
            $lead->last_updated_at = now();
            $lead->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show form for closing / consultation details (SPK Entry).
     */
    public function show($id)
    {
        $lead = CsLead::with('cs')->findOrFail($id);
        
        // Ensure only CLOSING or CONSULTATION status leads can generate SPK
        if (!in_array($lead->status, ['CLOSING', 'KONSULTASI', 'INVEST_KONSULTASI'])) {
            // return redirect()->back()->with('error', 'Status lead belum siap untuk Closing.');
        }

        $services = \App\Models\Service::orderBy('category')->orderBy('name')->get();

        return view('cs.leads.show', compact('lead', 'services'));
    }

    /**
     * Generate SPK and Create Order.
     */
    public function storeSpk(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        // Basic Validation
        $request->validate([
            'delivery_type' => 'required|in:N,P,J,F', // Online, Pickup, Ojol, F (Offline/Dateng)
            'shoe_brand' => 'required|string',
            'shoe_size' => 'required|string',
            'shoe_color' => 'required|string',
            'category' => 'required|string', // Sepatu, Tas, etc.
            'services' => 'required|array', // Allow multiple services if needed, but for now string?
            // "Jasa Utama & Sub Jasa" - Let's assume input text or selection.
            'priority' => 'required|string',
            'estimation_date' => 'required|date|after_or_equal:today',
            'reference_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 1. Generate SPK Number
            // Format: [Type]-[YY][MM]-[DD]-[Seq]-[CS]
            $type = $request->delivery_type;
            $date = now();
            $yymm = $date->format('ym');
            $dd = $date->format('d');
            
            $yymm = $date->format('ym');
            $dd = $date->format('d');
            
            // Get CS Code (Manual Input > User Profile > 'XX')
            $csCode = strtoupper($request->cs_code);
            if(empty($csCode)) {
                 $csCode = Auth::user()->cs_code ?? 'XX';
            }
            
            // Sequence: Global sequence for the day? Or Month?
            // "No urut sepatu masuk" implies global increment.
            // Let's use ID or specific sequence table. Using count of WorkOrders today + 1.
            $countToday = \App\Models\WorkOrder::whereDate('created_at', today())->count() + 1;
            $seq = str_pad($countToday, 3, '0', STR_PAD_LEFT); // 001, 002...

            // Refined Sequence Logic requested: "9864" (4 digits running number globally?)
            // If running number is global (not reset daily), we check max ID or total count.
            // Let's assume Global Running (Total Orders).
            $globalCount = \App\Models\WorkOrder::count() + 1;
            $seqGlobal = str_pad($globalCount, 4, '0', STR_PAD_LEFT);

            // User Example: F-2505-31-9864-QA
            // [Type]-[YY][MM]-[DD]-[Seq]-[CS]
            // Note: 2505 is YYMM. 31 is DD.
            $spkNumber = sprintf(
                "%s-%s-%s-%s-%s",
                $type,
                $yymm,
                $dd,
                $seqGlobal,
                $csCode
            );

            // Prepare Address with District/Village
            $finalAddress = $request->customer_address;
            if ($request->customer_district) {
                $finalAddress .= ", Kec. " . $request->customer_district;
            }
            if ($request->customer_village) {
                $finalAddress .= ", Kel. " . $request->customer_village;
            }

            // Update Lead Information first (if changed)
            $lead->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $finalAddress,
                'customer_city' => $request->customer_city,
                'customer_province' => $request->customer_province,
                'last_updated_at' => now(),
            ]);

            // 2. Create Work Order (SPK Pending)
            $order = \App\Models\WorkOrder::create([
                'spk_number' => $spkNumber,
                'customer_name' => $request->customer_name ?? 'Guest',
                'customer_phone' => $request->customer_phone,
                'customer_address' => $finalAddress,
                'customer_email' => $lead->customer_email,
                
                'shoe_brand' => $request->shoe_brand,
                'shoe_type' => $request->shoe_type,
                'shoe_size' => $request->shoe_size,
                'shoe_color' => $request->shoe_color,
                'category' => $request->category,
                
                'status' => \App\Enums\WorkOrderStatus::SPK_PENDING->value,
                'current_location' => 'In Transit (CS -> Gudang)',
                
                'priority' => $request->priority,
                'estimation_date' => $request->estimation_date,
                'notes' => $request->notes . " (Ref Lead: #{$lead->id})",
                
                'created_by' => Auth::id(),
                'entry_date' => now(),
            ]);

            // 2b. Sync Customer Master Data
            \App\Models\Customer::updateOrCreate(
                ['phone' => $request->customer_phone],
                [
                    'name' => $order->customer_name,
                    'address' => $order->customer_address,
                    'city' => $request->customer_city,
                    'province' => $request->customer_province,
                ]
            );

            // 2c. Save Requested Services (Detailed)
            if ($request->has('services') && is_array($request->services)) {
                $totalCost = 0;
                
                foreach ($request->services as $svc) {
                    // Skip if invalid structure
                    if (!is_array($svc)) continue; 

                    $hasId = !empty($svc['service_id']);
                    
                    // Decode details if string (JSON from hidden input)
                    $details = [];
                    if (isset($svc['details'])) {
                        $details = is_string($svc['details']) ? json_decode($svc['details'], true) : $svc['details'];
                    }

                    $order->workOrderServices()->create([
                        'service_id' => $hasId && $svc['service_id'] !== 'custom' ? $svc['service_id'] : null,
                        'custom_service_name' => $svc['custom_name'] ?? ($hasId ? null : 'Custom Service'),
                        'category_name' => $svc['category'] ?? 'Custom',
                        'cost' => $svc['price'] ?? 0,
                        'service_details' => $details,
                        'status' => 'PENDING'
                    ]);
                    
                    $totalCost += (int) ($svc['price'] ?? 0);
                }
                
                // Update Order Total
                $order->update(['total_service_price' => $totalCost]);
            }

            // 3. Handle Reference Photo Upload
            if ($request->hasFile('reference_photo')) {
                $file = $request->file('reference_photo');
                $filename = 'ref_cs_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('photos/orders', $filename, 'public');

                \App\Models\WorkOrderPhoto::create([
                    'work_order_id' => $order->id,
                    'step' => 'REFERENCE',
                    'file_path' => 'photos/orders/' . $filename,
                    'caption' => 'Foto Referensi dari CS',
                    'user_id' => Auth::id(),
                    'is_public' => false
                ]);
            }

            // 3. Update Lead Status (Only if Final Save)
            if ($request->action === 'save_and_add') {
                DB::commit();
                return redirect()->route('cs.leads.show', $lead->id)
                    ->with('success', "SPK $spkNumber Berhasil Dibuat! Data Customer tersimpan. Silakan input sepatu berikutnya.");
            } else {
                $lead->status = CsLead::STATUS_CLOSED;
                $lead->save();
                DB::commit();
                return redirect()->route('cs.dashboard')->with('success', "SPK Berhasil Dibuat: $spkNumber");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate SPK: ' . $e->getMessage());
        }
    }

    /**
     * Show Public Form for Customer
     */
    public function guestForm($id)
    {
        $lead = CsLead::findOrFail($id);
        return view('cs.leads.guest-form', compact('lead'));
    }

    /**
     * Update Data from Public Form
     */
    public function guestUpdate(Request $request, $id)
    {
        $lead = CsLead::findOrFail($id);
        
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string',
            'customer_province' => 'required|string',
        ]);

        $lead->update([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_address' => $request->customer_address,
            'customer_city' => $request->customer_city,
            'customer_province' => $request->customer_province,
            'last_updated_at' => now(), // Keep track of activity
        ]);

        return redirect()->back()->with('success', 'Data Anda berhasil disimpan! Terima kasih.');
    }
}
