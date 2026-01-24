<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Imports\OrdersImport;
use App\Enums\WorkOrderStatus;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ReceptionController extends Controller
{
    protected \App\Services\WorkflowService $workflow;
    protected ImageManager $imageManager;

    public function __construct(\App\Services\WorkflowService $workflow)
    {
        $this->workflow = $workflow;
        $this->imageManager = new ImageManager(new Driver());
    }


    public function destroy($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Prevent deleting if already progressed too far?
        // Reception is early stage, so usually safe if Soft Delete.
        $order->delete();

        return redirect()->back()->with('success', 'Order reception berhasil dihapus.');
    }

    public function receive($id)
    {
        $order = WorkOrder::findOrFail($id);

        // Ensure status is valid for receiving
        // Using strict comparison for Enum if casted, or use value if raw.
        // Assuming casted since we use Enum class in comparison.
        if ($order->status !== WorkOrderStatus::SPK_PENDING) {
             // Fallback check if it's value
             if($order->status instanceof WorkOrderStatus && $order->status !== WorkOrderStatus::SPK_PENDING) {
                  return redirect()->back()->with('error', 'Status order tidak valid untuk diterima.');
             }
             if(is_string($order->status) && $order->status !== WorkOrderStatus::SPK_PENDING->value) {
                  return redirect()->back()->with('error', 'Status order tidak valid untuk diterima.');
             }
        }

        // Update Status to DITERIMA
        $this->workflow->updateStatus($order, WorkOrderStatus::DITERIMA, 'Diterima oleh Admin Gudang', \Illuminate\Support\Facades\Auth::id());
        
        // Update Location
        $order->update(['current_location' => 'Gudang Penerimaan']);

        return redirect()->back()->with('success', 'Order berhasil diterima dan masuk antrian QC. Silakan cek tab "Diterima (Warehouse)".');
    }

    public function exportExcel()
    {
        return Excel::download(new \App\Exports\ReceptionExport, 'gudang_penerimaan_' . date('Y-m-d_His') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\OrdersTemplateExport, 'template_import_order.xlsx');
    }

    public function index(Request $request)
    {
        $query = WorkOrder::where('status', WorkOrderStatus::DITERIMA->value);

        // Search Filter (SPK, Customer Name, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        // Priority Filter
        if ($request->filled('priority')) {
            if ($request->priority == 'Prioritas') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } elseif ($request->priority == 'Reguler') {
                $query->whereIn('priority', ['Reguler', 'Normal']);
            } else {
                $query->where('priority', $request->priority);
            }
        }

        // Sort and Paginate
        // Prioritize: Prioritas/Urgent/Express (1) > Reguler/Normal (2)
        $query->orderByRaw("CASE 
            WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 1 
            ELSE 2 
        END ASC");
        
        $orders = $query->orderBy('entry_date', 'asc')
                        ->paginate(20)
                        ->appends($request->except('page'));

        // Fetch SPK Pending (from CS) for separate tab
        $pendingQuery = WorkOrder::where('status', WorkOrderStatus::SPK_PENDING->value);
        if ($request->filled('search')) {
             $search = $request->search;
             $pendingQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
             });
        }
        $pendingOrders = $pendingQuery->orderBy('created_at', 'desc')->get();

        // Fetch Processed Orders (already processed by warehouse)
        $processedQuery = WorkOrder::whereIn('status', [
            WorkOrderStatus::ASSESSMENT->value,
            WorkOrderStatus::WAITING_PAYMENT->value,
            WorkOrderStatus::CX_FOLLOWUP->value,
            WorkOrderStatus::PREPARATION->value, // Include prep so they stick around longer?
        ])->whereNotNull('warehouse_qc_status'); // Only show if warehouse QC was done
        
        if ($request->filled('search')) {
             $search = $request->search;
             $processedQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
             });
        }
        $processedOrders = $processedQuery->orderBy('warehouse_qc_at', 'desc')->get();

        // Fetch Available Accessory Racks
        $accessoryRacks = \App\Models\StorageRack::accessories()
            ->available()
            ->orderBy('rack_code')
            ->get();
            
        // Fetch Services for CS Selection
        $services = \App\Models\Service::all();

        return view('reception.index', compact('orders', 'pendingOrders', 'processedOrders', 'accessoryRacks', 'services'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $importer = new OrdersImport;
                Excel::import($importer, $request->file('file'));
            });
            
            // Success Logic (No Exception thrown)
            return redirect()->back()->with('success', 'Data berhasil diimport & SPK dibuat!');

        } catch (\App\Exceptions\ImportValidationException $e) {
            // Validation Failed - Show Dedicated Error Page
            return view('reception.import-errors', [
                'failures' => $e->getErrors()
            ]);

        } catch (\Exception $e) {
            // General Error
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'spk_number' => 'required|string|max:255|unique:work_orders,spk_number', // Manual Input Required
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'customer_city' => 'nullable|string|max:100',
            'customer_province' => 'nullable|string|max:100',
            'customer_district' => 'nullable|string|max:100',
            'customer_village' => 'nullable|string|max:100',
            'customer_postal_code' => 'nullable|string|max:20',
            'shoe_brand' => 'required|string|max:255',
            'category' => 'nullable|string|max:100', // Added category
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:100',
            'entry_date' => 'required|date',
            'estimation_date' => 'required|date|after_or_equal:entry_date',
            'priority' => 'required|in:Normal,Urgent,Express,Reguler,Prioritas',
            // New Fields
            'accessories_data' => 'nullable|array',
            'notes' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            // QC Removed
            'accessory_rack_code' => 'nullable|exists:storage_racks,rack_code',
            // Services
            'services' => 'nullable|array',
        ]);

        // Sync to Customer Master Data
        \App\Models\Customer::updateOrCreate(
            ['phone' => $validated['customer_phone']],
            [
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'],
                'address' => $validated['customer_address'],
                'city' => $validated['customer_city'],
                'province' => $validated['customer_province'],
                'district' => $validated['customer_district'],
                'village' => $validated['customer_village'],
                'postal_code' => $validated['customer_postal_code'],
            ]
        );

        // Status Logic (Default Accepted)
        $validated['status'] = WorkOrderStatus::DITERIMA->value;
        $validated['reception_qc_passed'] = null; // Ensure null for new manual orders
        $validated['warehouse_qc_status'] = null; // Ensure null for new manual orders
        $validated['current_location'] = 'Gudang Penerimaan';
        $validated['created_by'] = \Illuminate\Support\Facades\Auth::id();

        try {
            return DB::transaction(function () use ($validated, $request) {
            $order = WorkOrder::create($validated);
            
            // Handle Services (Same logic as Assessment)
            if ($request->has('services') && is_array($request->services)) {
                $totalCost = 0;
                foreach ($request->services as $svc) {
                    $hasId = !empty($svc['service_id']);
                    
                    // Decode details if string
                    $details = isset($svc['details']) ? (is_string($svc['details']) ? json_decode($svc['details'], true) : $svc['details']) : [];

                    $order->workOrderServices()->create([
                        'service_id' => $hasId && $svc['service_id'] !== 'custom' ? $svc['service_id'] : null,
                        'custom_service_name' => $svc['custom_name'] ?? ($hasId ? null : 'Custom Service'), // Fallback
                        'category_name' => $svc['category'] ?? 'Custom',
                        'cost' => $svc['price'] ?? 0,
                        'service_details' => $details,
                        'status' => 'PENDING'
                    ]);
                    
                    $totalCost += (int) ($svc['price'] ?? 0);
                }
                
                $order->update(['total_service_price' => $totalCost]);
            }
            
                // Log success for manual creation
                $order->logs()->create([
                    'step' => 'RECEPTION',
                    'action' => 'MANUAL_ORDER_CREATED',
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'description' => 'Order Manual Dibuat - Menunggu Pengecekan Fisik'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order manual berhasil ditambahkan!',
                    'order' => $order
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil ditambahkan!',
                    'order' => $order
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printTag($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Allow print if QC Passed OR if Status is NOT CX_FOLLOWUP (meaning approved/resumed)
        // If status is CX_FOLLOWUP, it means it's still pending CX action.
        if (!$order->reception_qc_passed && $order->status === WorkOrderStatus::CX_FOLLOWUP) {
            abort(403, 'Order belum lolos QC Penerimaan dan belum disetujui Customer (CX).');
        }

        // Use SVG format (Does not require Imagick extension)
        $barcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($order->spk_number);
        
        return view('reception.print-tag', compact('order', 'barcode'));
    }

    public function confirm($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            // Check if status is correct
            if ($order->status !== WorkOrderStatus::SPK_PENDING) {
                return redirect()->back()->with('error', 'Status order tidak valid untuk konfirmasi.');
            }

            // Update Status to DITERIMA
            // We use workflow service to ensure logs are created
            $this->workflow->updateStatus($order, WorkOrderStatus::DITERIMA, 'SPK Dikonfirmasi dan Diterima Fisik di Gudang');
            
            // Update Entry Date to NOW
            $order->update([
                'entry_date' => now(),
            ]);

            return redirect()->back()->with('success', 'SPK Berhasil Dikonfirmasi dan Masuk Antrian Gudang!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengonfirmasi order: ' . $e->getMessage());
        }
    }

    public function process($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            // Move to ASSESSMENT directly
            $this->workflow->updateStatus($order, WorkOrderStatus::ASSESSMENT, 'Dikirim dari Reception ke Assessment');

            return redirect()->back()->with('success', 'Sepatu berhasil dikirim ke Assessment!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id'
        ]);

        try {
            DB::beginTransaction();
            
            foreach ($request->ids as $id) {
                $order = WorkOrder::find($id);
                
                if ($order) {
                    // Delete related records
                    $order->services()->detach();
                    $order->materials()->detach();
                    $order->logs()->delete();
                    $order->photos()->delete();
                    $order->complaints()->delete();
                    
                    // Now soft delete the order
                    $order->delete();
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', count($request->ids) . ' data order berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    

    public function updateShoeInfo(Request $request, $id)
    {
        $validated = $request->validate([
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
        ]);

        try {
            $order = WorkOrder::findOrFail($id);
            $order->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Info sepatu berhasil diupdate!',
                'shoe_brand' => $order->shoe_brand,
                'shoe_size' => $order->shoe_size,
                'shoe_color' => $order->shoe_color,
                'category' => $order->category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update info sepatu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email|max:255'
        ]);

        try {
            $order = WorkOrder::findOrFail($id);
            $order->customer_email = $request->email;
            $order->save();

            return response()->json([
                'success' => true, 
                'message' => 'Email berhasil diupdate.',
                'email' => $order->customer_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal update email: ' . $e->getMessage()
            ]);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validated = $request->validate([
            'spk_number' => 'required|string|max:255|unique:work_orders,spk_number,' . $id,
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            'priority' => 'required|in:Normal,Urgent,Express,Reguler,Prioritas',
        ]);

        try {
            DB::beginTransaction();
            
            $order->update($validated);

            // Sync to Customer Master Data if phone changed or name updated
            \App\Models\Customer::updateOrCreate(
                ['phone' => $validated['customer_phone']],
                [
                    'name' => $validated['customer_name']
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data order berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendEmail($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            if (!$order->customer_email) {
                return response()->json(['success' => false, 'message' => 'Email customer tidak tersedia.']);
            }

            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderReceived($order));

            return response()->json(['success' => true, 'message' => 'Nota digital berhasil dikirim ke email customer.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }

    /**
     * Show reception detail form
     */
    public function show($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Fetch active services and materials for dropdowns
        $services = \App\Models\Service::all(); // Optionally filter by active
        $materials = \App\Models\Material::all(); // Optionally filter by stock > 0
        
        // Fetch Available Accessory Racks
        $accessoryRacks = \App\Models\StorageRack::accessories()
            ->available()
            ->orderBy('rack_code')
            ->get();

        // Check for existing assignment
        $currentAccessoryRack = $order->storageAssignments()
            ->where('item_type', 'accessories')
            ->where('status', 'stored')
            ->latest()
            ->first()?->rack_code;

        return view('reception.show', compact('order', 'services', 'materials', 'accessoryRacks', 'currentAccessoryRack'));
    }

    /**
     * Process reception with accessories, QC, and photos
     */
    public function processReception(Request $request, $id)
    {
        $request->validate([
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'customer_city' => 'nullable|string|max:100',
            'customer_province' => 'nullable|string|max:100',
            'customer_district' => 'nullable|string|max:100',
            'customer_village' => 'nullable|string|max:100',
            'customer_postal_code' => 'nullable|string|max:20',
            'entry_date' => 'required|date',
            'estimation_date' => 'nullable|date',
            'accessories_tali' => 'required|in:Simpan,Nempel,Tidak Ada,S,N,T', // Support both formats
            'accessories_insole' => 'required|in:Simpan,Nempel,Tidak Ada,S,N,T',
            'accessories_box' => 'required|in:Simpan,Nempel,Tidak Ada,S,N,T',
            'accessories_other' => 'nullable|string|max:500',
            'accessory_rack_code' => 'nullable|exists:storage_racks,rack_code',
            // QC Restored
            'reception_qc_passed' => 'required|boolean',
            'reception_rejection_reason' => 'required_if:reception_qc_passed,0|nullable|string',
            'technician_notes' => 'nullable|string', // Technical Instructions
        ]);

        try {
            DB::transaction(function() use ($request, $id) {
                $order = WorkOrder::findOrFail($id);
                
                // Determine Status based on QC
                $newStatus = $request->boolean('reception_qc_passed') 
                    ? \App\Enums\WorkOrderStatus::ASSESSMENT->value // Go to Assessment if Passed
                    : \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value; // Hold for CX if Failed
                
                // 1. Update order details (Customer & Accessories)
                $order->update([
                    // Customer Data Updates
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'customer_address' => $request->customer_address,
                    'entry_date' => $request->entry_date,
                    'estimation_date' => $request->estimation_date,
                    
                    // Accessories
                    'accessories_tali' => $request->accessories_tali,
                    'accessories_insole' => $request->accessories_insole,
                    'accessories_box' => $request->accessories_box,
                    'accessories_other' => $request->accessories_other,
                    
                    // QC Data
                    'reception_qc_passed' => $request->boolean('reception_qc_passed'),
                    'warehouse_qc_status' => $request->boolean('reception_qc_passed') ? 'lolos' : 'reject',
                    'warehouse_qc_notes' => $request->boolean('reception_qc_passed') ? null : $request->reception_rejection_reason,
                    
                    'technician_notes' => $request->technician_notes, // Save Technical Instructions
                    'warehouse_qc_by' => \Illuminate\Support\Facades\Auth::id(),
                    'warehouse_qc_at' => now(),
                    
                    // Update Main Status if changed (don't regress if already further?) 
                    // Removal: Status is updated via workflow object below to prevent ASSESSMENT -> ASSESSMENT error
                ]);

                // 2. Handle Accessory Storage Assignment
                // Check if any accessory is marked as store (S or Simpan)
                $hasStoredAccessories = in_array($request->accessories_tali, ['Simpan', 'S']) ||
                                        in_array($request->accessories_insole, ['Simpan', 'S']) ||
                                        in_array($request->accessories_box, ['Simpan', 'S']);

                if ($hasStoredAccessories && $request->accessory_rack_code) {
                    $rack = \App\Models\StorageRack::where('rack_code', $request->accessory_rack_code)->firstOrFail();
                    
                    // Create Assignment
                    \App\Models\StorageAssignment::create([
                        'work_order_id' => $order->id,
                        'rack_code' => $rack->rack_code,
                        'item_type' => 'accessories',
                        'stored_at' => now(),
                        'stored_by' => \Illuminate\Support\Facades\Auth::id(),
                        'status' => 'stored',
                        'notes' => 'Aksesoris: ' . ($request->accessories_other ?? 'Lihat Detail Order'),
                    ]);

                    // Increment Rack Count
                    $rack->increment('current_count');
                    
                    // Update Status to Full if needed
                    if ($rack->current_count >= $rack->capacity) {
                        $rack->update(['status' => 'full']);
                    }
                }


                // 3. Log rejection and Create CX Issue if REJECTED
                if (!$request->boolean('reception_qc_passed')) {
                    // 3a. Create Log
                    $order->logs()->create([
                        'step' => 'RECEPTION',
                        'action' => 'QC_REJECTED',
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'description' => 'QC Awal Gagal: ' . $request->reception_rejection_reason
                    ]);

                    // 3b. Create CxIssue
                    \App\Models\CxIssue::create([
                        'work_order_id' => $order->id,
                        'reported_by' => \Illuminate\Support\Facades\Auth::id(),
                        'type' => 'FOLLOW_UP',
                        'category' => 'Kondisi Awal', // Specific category for Reception
                        'description' => 'QC Awal Gagal (Reception): ' . $request->reception_rejection_reason,
                        'status' => 'OPEN',
                        'photos' => [] // No photos yet in this flow
                    ]);
                } else {
                    // Log success
                    $order->logs()->create([
                        'step' => 'RECEPTION',
                        'action' => 'QC_PASSED',
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'description' => 'QC Awal Lolos'
                    ]);
                }

                // 1.5. Sync to Customer Master Data
                // Use the NEW values from request
                $customer = \App\Models\Customer::updateOrCreate(
                    ['phone' => $request->customer_phone],
                    [
                        'name' => $request->customer_name,
                        'address' => $request->customer_address,
                        'city' => $request->customer_city,
                        'province' => $request->customer_province,
                        'district' => $request->customer_district,
                        'village' => $request->customer_village,
                        'postal_code' => $request->customer_postal_code,
                        'email' => $request->customer_email, // Sync Email
                    ]
                );
                
                // 2. Process photos (compress + watermark)
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $this->processPhoto($order, $photo);
                    }
                }
                
                if ($request->boolean('reception_qc_passed')) {
                    // → ASSESSMENT (Teknisi)
                    $this->workflow->updateStatus(
                        $order,
                        WorkOrderStatus::ASSESSMENT,
                        'Lolos QC Gudang - Menunggu Assessment Teknisi',
                        \Illuminate\Support\Facades\Auth::id()
                    );
                } else {
                    // → CX (CX_FOLLOWUP) - CX Issue already created above
                    $this->workflow->updateStatus(
                        $order,
                        WorkOrderStatus::CX_FOLLOWUP,
                        'Tidak Lolos QC Gudang - Menunggu Konfirmasi Customer',
                        \Illuminate\Support\Facades\Auth::id()
                    );
                }
            });

            $redirect = redirect()->route('reception.index')
                ->with('success', 'Order berhasil diproses')
                ->with('activeTab', 'processed');

            // Print SPK moved to Assessment, so removed here.
            // if ($request->warehouse_qc_status === 'lolos') {
            //     $redirect->with('print_spk_id', $id);
            // }

            return $redirect;

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memproses order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process photo: compress + watermark (logo + shoe size)
     */
    protected function processPhoto($order, $photo)
    {
        // 1. Load image using Intervention Image v3
        $image = $this->imageManager->read($photo);
        
        // 2. Scale if too large (max 1920px width, maintain aspect ratio)
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }
        
        // 3. Add watermark
        try {
            // 3a. Load logo
            $logoPath = public_path('images/logo-watermark.png');
            if (file_exists($logoPath)) {
                $logo = $this->imageManager->read($logoPath);
                
                // Scale logo (300px width)
                $logo->scale(width: 300);
                
                // Place logo at bottom-right with padding
                $image->place($logo, 'bottom-right', 20, 100);
            }
            
            
        } catch (\Exception $e) {
            // If watermark fails, continue without it
            Log::warning('Watermark failed for order ' . $order->id . ': ' . $e->getMessage());
        }
        
        // 4. Save with high quality compression (85%)
        $filename = 'before_' . time() . '_' . uniqid() . '.jpg';
        $path = 'photos/orders/' . $filename;
        
        // v3 uses toJpeg() or other format methods
        $encoded = $image->toJpeg(85);
        
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $encoded);
        
        // 5. Create photo record
        \App\Models\WorkOrderPhoto::create([
            'work_order_id' => $order->id,
            'step' => 'WAREHOUSE_BEFORE',
            'file_path' => $path,
            'caption' => 'Foto Before - Gudang',
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'is_public' => false,
        ]);
    }

    /**
     * Print Detailed SPK for Warehouse
     */
    public function printSpk($id)
    {
        $order = \App\Models\WorkOrder::with(['workOrderServices.service', 'customer', 'photos'])->findOrFail($id);
        
        // Generate QR Code/Barcode using same logic as Assessment
        $barcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($order->spk_number);

        return view('assessment.print-spk-premium', compact('order', 'barcode'));
    }

    /**
     * Skip Assessment and move directly to Preparation
     */
    public function skipAssessment($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            // Move to PREPARATION directly
            // Note: We might want to ensure QC was passed first, but "Direct" implies bypassing checks or assuming they are done.
            // If the user wants to skip Assessment, they likely have done physical check manually or it's a simple order.
            
            $this->workflow->updateStatus(
                $order, 
                WorkOrderStatus::PREPARATION, 
                'Langsung ke Preparation (Skip Assessment)', 
                \Illuminate\Support\Facades\Auth::id()
            );
            
            // Update Location
            $order->update(['current_location' => 'Preparation Area']);

            return redirect()->back()->with('success', 'Order berhasil dikirim langsung ke Preparation!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Skip Assessment - Direct to Preparation
     */
    public function bulkSkipAssessment(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id'
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->ids as $id) {
            try {
                $order = WorkOrder::findOrFail($id);
                
                $this->workflow->updateStatus(
                    $order, 
                    WorkOrderStatus::PREPARATION, 
                    'Langsung ke Preparation (Bulk Skip Assessment)', 
                    \Illuminate\Support\Facades\Auth::id()
                );
                
                $order->update(['current_location' => 'Preparation Area']);
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Proses massal selesai. Berhasil: $successCount, Gagal: $failCount"
        ]);
    }
}

