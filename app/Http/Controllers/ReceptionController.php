<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Imports\OrdersImport;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ReceptionController extends Controller
{
    protected \App\Services\WorkflowService $workflow;
    protected \App\Services\ReceptionService $receptionService;
    protected ImageManager $imageManager;

    public function __construct(
        \App\Services\WorkflowService $workflow,
        \App\Services\ReceptionService $receptionService
    ) {
        $this->workflow = $workflow;
        $this->receptionService = $receptionService;
        $this->imageManager = new ImageManager(new Driver());
    }


    public function destroy($id)
    {
        $order = WorkOrder::findOrFail($id);
        $this->authorize('deleteReception', $order);
        
        $order->delete();
        return redirect()->back()->with('success', 'Order reception berhasil dihapus.');
    }

    public function receive($id)
    {
        $this->authorize('manageReception', WorkOrder::class);
        $order = WorkOrder::findOrFail($id);

        // Ensure status is valid for receiving
        if ($order->status !== WorkOrderStatus::SPK_PENDING->value && 
            $order->status !== WorkOrderStatus::SPK_PENDING) {
             return redirect()->back()->with('error', 'Status order tidak valid untuk diterima.');
        }

        // Use Service for Logic
        $this->receptionService->confirmOrder($order, request('rack_code'));

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
        $this->authorize('manageReception', WorkOrder::class);

        $user = Auth::user();
        $query = WorkOrder::where('status', WorkOrderStatus::DITERIMA->value);

        // Filter: Own Orders vs All (Admin/Owner can see all)
        if (!$user->isAdmin() && !$user->isOwner()) {
            $query->where('warehouse_qc_by', $user->id);
        }

        // Filter: Handler (Only for Admin/Owner)
        if ($request->filled('handler_id') && ($user->isAdmin() || $user->isOwner())) {
            $query->where('warehouse_qc_by', $request->handler_id);
        }

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
            
        // Fetch Available 'Before' Racks for Reception
        $availableBeforeRacks = \App\Models\StorageRack::before()
            ->active()
            ->orderBy('rack_code')
            ->get();

        // Fetch Services for CS Selection
        $services = \App\Models\Service::all();

        return view('reception.index', compact(
            'orders', 
            'pendingOrders', 
            'processedOrders', 
            'accessoryRacks', 
            'availableBeforeRacks',
            'services'
        ));
    }

    public function import(Request $request)
    {
        $this->authorize('manageReception', WorkOrder::class);
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
        $this->authorize('manageReception', WorkOrder::class);

        $validated = $request->validate([
            'spk_number' => 'required|string|max:255|unique:work_orders,spk_number',
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
            'category' => 'nullable|string|max:100', 
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:100',
            'entry_date' => 'required|date',
            'estimation_date' => 'required|date|after_or_equal:entry_date',
            'priority' => 'required|in:Normal,Urgent,Express,Reguler,Prioritas',
            'accessories_data' => 'nullable|array',
            'notes' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            'accessory_rack_code' => 'nullable|exists:storage_racks,rack_code',
            'services' => 'nullable|array',
        ]);

        try {
            // Use Service to create order
            $order = $this->receptionService->createManualOrder($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Order manual berhasil ditambahkan!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printTag($id)
    {
        $this->authorize('manageReception', WorkOrder::class);
        $order = WorkOrder::findOrFail($id);
        
        if (!$order->reception_qc_passed && $order->status === WorkOrderStatus::CX_FOLLOWUP) {
            abort(403, 'Order belum lolos QC Penerimaan dan belum disetujui Customer (CX).');
        }

        /** @var \SimpleSoftwareIO\QrCode\Generator $qr */
        $qr = QrCode::size(200);
        $barcode = $qr->generate($order->spk_number);
        
        return view('reception.print-tag', compact('order', 'barcode'));
    }

    public function confirm($id)
    {
        $this->authorize('manageReception', WorkOrder::class);
        try {
            $order = WorkOrder::findOrFail($id);
            
            if ($order->status !== WorkOrderStatus::SPK_PENDING) {
                return redirect()->back()->with('error', 'Status order tidak valid untuk konfirmasi.');
            }

            $this->receptionService->confirmOrder($order, request('rack_code'));

            return redirect()->back()->with('success', 'SPK Berhasil Dikonfirmasi dan Masuk Antrian Gudang (RAK-BEFORE)!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengonfirmasi order: ' . $e->getMessage());
        }
    }

    public function process($id)
    {
        $this->authorize('manageReception', WorkOrder::class);
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
        $this->authorize('deleteReception', WorkOrder::class);

        // Increase time limit for mass deletion
        set_time_limit(300);

        // Validation: Either ids array OR select_all flag
        $request->validate([
            'ids' => 'required_without:select_all|array',
            'ids.*' => 'exists:work_orders,id',
            'select_all' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Determine IDs to delete
            if ($request->boolean('select_all')) {
                // Re-apply filters from Index to get ALL matching IDs
                $query = WorkOrder::where('status', WorkOrderStatus::DITERIMA->value);

                // Search Filter
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('spk_number', 'LIKE', "%{$search}%")
                          ->orWhere('customer_name', 'LIKE', "%{$search}%")
                          ->orWhere('customer_phone', 'LIKE', "%{$search}%");
                    });
                }

                // Date Filter
                if ($request->filled('date_from')) $query->whereDate('entry_date', '>=', $request->date_from);
                if ($request->filled('date_to')) $query->whereDate('entry_date', '<=', $request->date_to);

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

                // Get IDs (Limit to reasonable amount to prevent crash, e.g. 5000)
                $targetIds = $query->limit(5000)->pluck('id');
            } else {
                $targetIds = $request->ids;
            }

            $count = 0;
            // Process Delete (Use Chunking if huge, but loop is fine for <2000)
            foreach ($targetIds as $id) {
                $order = WorkOrder::find($id);
                
                if ($order) {
                    // Detach relations
                    $order->services()->detach();
                    $order->materials()->detach();
                    
                    // Delete children
                    $order->logs()->delete();
                    $order->photos()->delete();
                    $order->complaints()->delete();
                    $order->cxIssues()->delete();
                    
                    // Soft delete main record
                    $order->delete();
                    $count++;
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', $count . ' data order berhasil dihapus massal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    

    public function updateShoeInfo(Request $request, $id)
    {
        $this->authorize('manageReception', WorkOrder::class);
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
        $this->authorize('manageReception', WorkOrder::class);
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
        $this->authorize('manageReception', WorkOrder::class);
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
                ['phone' => \App\Helpers\PhoneHelper::normalize($validated['customer_phone'])],
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
        $this->authorize('manageReception', WorkOrder::class);
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
        $this->authorize('manageReception', WorkOrder::class);
        $order = WorkOrder::findOrFail($id);
        
        if ($order->status !== WorkOrderStatus::DITERIMA) {
             return redirect()->route('reception.index')->with('warning', 'Order ini sudah diproses ke tahap selanjutnya.');
        }

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
        $this->authorize('manageReception', WorkOrder::class);
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'customer_city' => 'nullable|string|max:100',
            'customer_province' => 'nullable|string|max:100',
            'customer_district' => 'nullable|string|max:100',
            'customer_village' => 'nullable|string|max:100',
            'customer_postal_code' => 'nullable|string|max:20',
            'entry_date' => 'required|date',
            'estimation_date' => 'nullable|date',
            'accessories_tali' => 'required|in:Simpan,Nempel,Tidak Ada,S,N,T',
            'accessories_insole' => 'required|in:Simpan,Nempel,Tidak Ada,S,N,T',
            'accessories_box' => 'required|in:Simpan,Nempel,Tidak Ada,S,N,T',
            'accessories_other' => 'nullable|string|max:500',
            'accessory_rack_code' => 'nullable|exists:storage_racks,rack_code',
            'reception_qc_passed' => 'required|boolean',
            'reception_rejection_reason' => 'required_if:reception_qc_passed,0|nullable|string',
            'technician_notes' => 'nullable|string',
            'shoe_brand' => 'nullable|string|max:255',
            'shoe_type' => 'nullable|string|max:255',
            'shoe_size' => 'nullable|string|max:50',
            'shoe_color' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'photos.*' => 'image|max:10240', // Max 10MB
        ]);

        try {
            $order = WorkOrder::findOrFail($id);
            
            // 1. Service Call (Data Updates, QC Logic, Logging, Transaction)
            // We pass $request->all() to ensure all data is available, as validate might filter
            // but we need to ensure cleanliness. validated is safer.
            // Service expects 'reception_qc_passed' which is in verified.
            $this->receptionService->processReceptionQC($order, $request->all());

            // 2. Photos (Controller Logic)
            // Kept in controller as it involves file handling logic separate from biz logic for now
            if ($request->hasFile('photos')) {
                 foreach ($request->file('photos') as $photo) {
                     $this->processPhoto($order, $photo);
                 }
            }

            $message = $request->boolean('reception_qc_passed') 
                    ? 'Penerimaan berhasil & QC Lolos!' 
                    : 'Penerimaan berhasil, namun QC Gagal. Order masuk ke Tindak Lanjut CX.';

             return redirect()->route('reception.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses: ' . $e->getMessage());
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
            'user_id' => Auth::id(),
            'is_public' => false,
        ]);
    }

    /**
     * Print Detailed SPK for Warehouse
     */
    public function printSpk($id)
    {
        $this->authorize('manageReception', WorkOrder::class);
        $order = WorkOrder::with(['workOrderServices.service', 'customer', 'photos'])->findOrFail($id);
        
        // Generate QR Code/Barcode using same logic as Assessment
        /** @var \SimpleSoftwareIO\QrCode\Generator $qr */
        $qr = QrCode::size(100);
        $barcode = $qr->generate($order->spk_number);


        return view('assessment.print-spk-premium', compact('order', 'barcode'));
    }

    /**
     * Skip Assessment and move directly to Preparation
     */
    public function skipAssessment($id)
    {
        $this->authorize('manageReception', WorkOrder::class);
        try {
            $order = WorkOrder::findOrFail($id);
            
            // Move to PREPARATION directly
            // Note: We might want to ensure QC was passed first, but "Direct" implies bypassing checks or assuming they are done.
            // If the user wants to skip Assessment, they likely have done physical check manually or it's a simple order.
            
            $this->workflow->updateStatus(
                $order, 
                WorkOrderStatus::PREPARATION, 
                'Langsung ke Preparation (Skip Assessment)', 
                Auth::id()
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
        $this->authorize('manageReception', WorkOrder::class);
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
                    Auth::id()
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
    /**
     * View soft-deleted records (Trash)
     */
    public function trash(Request $request)
    {
        $this->authorize('deleteReception', WorkOrder::class);
        $query = WorkOrder::onlyTrashed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->orderBy('deleted_at', 'desc')->paginate(50);
        
        return view('reception.trash', compact('orders'));
    }

    /**
     * Restore a soft-deleted record
     */
    public function restore($id)
    {
        $this->authorize('deleteReception', WorkOrder::class);

        try {
            DB::beginTransaction();

            $order = WorkOrder::onlyTrashed()->findOrFail($id);
            
            // Log restoration
            $order->logs()->withTrashed()->create([
                'step' => 'SYSTEM',
                'action' => 'RESTORED',
                'description' => 'Data dipulihkan dari Tempat Sampah',
                'user_id' => Auth::id()
            ]);

            $order->restore();
            
            DB::commit();

            return redirect()->route('reception.trash')->with('success', "Order #{$order->spk_number} berhasil dipulihkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memulihkan order: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a record (Destroy relation + record)
     */
    public function forceDelete($id)
    {
        $this->authorize('forceDeleteReception', WorkOrder::class);

        try {
            $order = WorkOrder::onlyTrashed()->findOrFail($id);
            $spk = $order->spk_number;

            DB::beginTransaction();
            
            // Delete child records permanently
            $order->logs()->forceDelete();
            
            // Handle Photos (Delete files + records)
            foreach ($order->photos as $photo) {
                if ($photo->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($photo->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->file_path);
                }
                $photo->forceDelete();
            }
            
            $order->workOrderServices()->forceDelete();
            $order->materials()->detach();
            $order->complaints()->forceDelete();
            $order->cxIssues()->forceDelete();
            $order->storageAssignments()->forceDelete();
            
            // Force delete main record
            $order->forceDelete();

            DB::commit();

            return redirect()->route('reception.trash')->with('success', "Order {$spk} berhasil dihapus permanen.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    /**
     * Mass Permanently delete records
     */
    public function bulkForceDelete(Request $request)
    {
        $this->authorize('forceDeleteReception', WorkOrder::class);

        if (!$request->has('ids') || empty($request->ids)) {
            return redirect()->back()->with('error', 'Pilih item terlebih dahulu.');
        }

        try {
            DB::beginTransaction();
            $count = 0;
            foreach ($request->ids as $id) {
                $order = WorkOrder::onlyTrashed()->find($id);
                if ($order) {
                    $order->logs()->forceDelete();
                    
                    foreach ($order->photos as $p) {
                         if ($p->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($p->file_path)) {
                             \Illuminate\Support\Facades\Storage::disk('public')->delete($p->file_path);
                         }
                         $p->forceDelete();
                    }
                    
                    $order->workOrderServices()->forceDelete();
                    $order->materials()->detach();
                    $order->complaints()->forceDelete();
                    $order->cxIssues()->forceDelete();
                    $order->storageAssignments()->forceDelete();
                    
                    $order->forceDelete();
                    $count++;
                }
            }
            DB::commit();
            return redirect()->route('reception.trash')->with('success', "{$count} data berhasil dihapus permanen.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal hapus masal: ' . $e->getMessage());
        }
    }
}

