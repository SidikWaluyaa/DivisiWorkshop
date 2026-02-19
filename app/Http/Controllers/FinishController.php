<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\PhotoReportService;

class FinishController extends Controller
{
    protected WorkflowService $workflow;
    protected \App\Services\MaterialReservationService $materialService;

    public function __construct(WorkflowService $workflow, \App\Services\MaterialReservationService $materialService)
    {
        $this->workflow = $workflow;
        $this->materialService = $materialService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // 1. Ready for Pickup (SELESAI)
        $readyQuery = WorkOrder::where('status', WorkOrderStatus::SELESAI->value);

        if ($search) {
            $readyQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $ready = $readyQuery->with('services')
                    ->orderByRaw("CASE WHEN priority = 'Prioritas' THEN 0 ELSE 1 END")
                    ->orderBy('finished_date', 'desc')
                    ->limit(100)
                    ->get();

        // Split into two collections: Not Stored vs Stored
        $readyNotStored = $ready->whereNull('storage_rack_code');
        $readyStored = $ready->whereNotNull('storage_rack_code');

        // 2. Taken/Completed History
        $historyQuery = WorkOrder::whereNotNull('taken_date');

        if ($search) {
            $historyQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $history = $historyQuery->with('services')
                    ->orderBy('taken_date', 'desc') // Change to desc to see latest first
                    ->orderBy('id', 'desc')
                    ->limit(50) 
                    ->get();

        return view('finish.index', compact('readyNotStored', 'readyStored', 'history'));
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('manageFinish', WorkOrder::class);

        $request->validate([
            'date' => 'required|date',
        ]);

        $date = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

        // Delete orders where taken_date is exactly this date (ignoring time for the match logic? 
        // taken_date is datetime, so we need whereDate)
        $count = WorkOrder::whereDate('taken_date', $date)
                    ->whereNotNull('taken_date') // Safety check
                    ->delete();

        return back()->with('success', "Berhasil menghapus {$count} data riwayat pengambilan pada tanggal {$date}.");
    }

    public function destroy($id)
    {
        $this->authorize('manageFinish', WorkOrder::class);
        $order = WorkOrder::findOrFail($id);
        
        // Ensure only completed/taken orders can be deleted (Safety)
        if (is_null($order->taken_date)) {
            return back()->with('error', 'Hanya data riwayat (sudah diambil) yang boleh dihapus.');
        }

        $order->delete(); // Soft Delete

        return back()->with('success', 'Data berhasil dipindahkan ke Sampah.');
    }

    public function show($id)
    {
        $order = WorkOrder::with([
            'services', 
            'logs', 
            'picSortirSol', 
            'picSortirUpper', 
            'technicianProduction', 
            'qcJahitTechnician', 
            'qcCleanupTechnician', 
            'qcFinalPic'
        ])->findOrFail($id);

        $services = \App\Models\Service::where('category', 'OTO')->get();
        return view('finish.show', compact('order', 'services'));
    }

    public function pickup(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $this->authorize('updateFinish', $order);
        
        DB::beginTransaction();
        try {
            // 1. Release from storage if currently stored
            if ($order->storage_rack_code) {
                app(\App\Services\Storage\StorageService::class)->retrieveFromStorage($order->id, 'Customer Pickup');
            }

            // 2. Set taken date (redundant if retrieveFromStorage did it, but safe to ensure)
            $order->taken_date = now();
            $order->save();
            
            // 3. Auto-cancel and soft-delete pending OTOs
            \App\Models\OTO::where('work_order_id', $order->id)
                ->whereIn('status', ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER'])
                ->update(['status' => 'CANCELLED']);
            
            \App\Models\OTO::where('work_order_id', $order->id)
                ->whereIn('status', ['CANCELLED'])
                ->delete(); // Soft delete all cancelled ones

            $order->update(['has_active_oto' => false]);

            // 4. Log
            $order->logs()->create([
                'step' => WorkOrderStatus::SELESAI->value,
                'action' => 'PICKUP',
                'user_id' => $request->user()?->id,
                'description' => 'Customer picked up the shoes. Any pending OTOs were cancelled.'
            ]);

            DB::commit();
            return back()->with('success', 'Sepatu telah diambil customer.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengambilan: ' . $e->getMessage());
        }
    }

    public function addService(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $this->authorize('updateFinish', $order);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'custom_name' => 'nullable|string|max:255',
        ]);

        $service = \App\Models\Service::findOrFail($request->service_id);

        try {
            $order->services()->attach($service->id, [
                'custom_name' => $request->custom_name,
                'cost' => $service->price // Default to base price, finance can adjust later if needed
            ]);

            // 1.5 Recalculate Total Service Price
            $order->load('services'); // Reload relation
            $order->total_service_price = $order->services->sum(function($service) {
                return $service->pivot->cost;
            });
            $order->save();

            // 2. Reset Workflows
            // Logic reset timestamp is domain specific, keep it here or move to helper method.
            // For clarity, let's keep it but formatted cleanly.
            $order->finished_date = null; 
            $order->taken_date = null;
            
            // Reset QC
            $order->qc_jahit_started_at = null; $order->qc_jahit_completed_at = null;
            $order->qc_cleanup_started_at = null; $order->qc_cleanup_completed_at = null;
            $order->qc_final_started_at = null; $order->qc_final_completed_at = null;

            // Reset Production based on category
            $cat = strtolower($service->category);
            
            if (str_contains($cat, 'sol')) {
                $order->prod_sol_started_at = null; $order->prod_sol_completed_at = null;
            }
            if (str_contains($cat, 'upper') || str_contains($cat, 'jahit') || str_contains($cat, 'repaint')) {
                $order->prod_upper_started_at = null; $order->prod_upper_completed_at = null;
            }
            if (str_contains($cat, 'cleaning') || str_contains($cat, 'whitening') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment')) {
                 $order->prod_cleaning_started_at = null; $order->prod_cleaning_completed_at = null;
            }

            $order->save();

            // 3. Handle Photo (Before Status Update to keep flow logical)
            if ($request->hasFile('upsell_photo')) {
                $file = $request->file('upsell_photo');
                $filename = 'UPSELL_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('photos/upsell', $filename, 'public');

                \App\Models\WorkOrderPhoto::create([
                    'work_order_id' => $order->id,
                    'step' => 'UPSELL_BEFORE',
                    'file_path' => $path,
                    'is_public' => true,
                ]);
            }

            // 4. Use Service for Status Change
            // This handles logging and validation
            $this->workflow->updateStatus(
                $order, 
                WorkOrderStatus::PREPARATION, 
                "Added Service: {$service->name}. Resetting to Preparation."
            );
            
            return redirect()->route('finish.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create OTO (One Time Offer) for finished order
     */
    public function createOTO(Request $request, $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'services.*.oto_price' => 'required|numeric|min:0',
            'services.*.normal_price' => 'required|numeric|min:0',
            'services.*.discount' => 'nullable|numeric', 
            'services.*.custom_name' => 'nullable|string|max:255',
            'valid_days' => 'required|in:3,7,14',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validasi Gagal: ' . $validator->errors()->first());
        }

        $order = WorkOrder::findOrFail($id);
        $this->authorize('updateFinish', $order);

        // Validate order is in FINISH status
        if ($order->status !== WorkOrderStatus::SELESAI) {
            return back()->with('error', 'Order harus dalam status SELESAI untuk membuat OTO.');
        }

        try {
            DB::transaction(function() use ($request, $order) {
                // Calculate totals
                $serviceNames = [];
                $totalNormal = 0;
                $totalOTO = 0;

                foreach ($request->services as $serviceData) {
                    $service = \App\Models\Service::findOrFail($serviceData['id']);
                    $serviceNames[] = $service->name;
                    // Logic Reversal: normal_price comes from UI, oto_price is our base service price
                    $totalNormal += (float) $serviceData['normal_price'];
                    $totalOTO += (float) $serviceData['oto_price'];
                }

                $totalDiscount = $totalNormal - $totalOTO;
                $discountPercent = $totalNormal > 0 ? ($totalDiscount / $totalNormal) * 100 : 0;
                
                $formatPrice = fn($val) => 'Rp. ' . number_format((float)$val, 0, ',', '.');

                // Create OTO
                $oto = \App\Models\OTO::create([
                    'work_order_id' => $order->id,
                    'spk_number' => $order->spk_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'title' => 'Penawaran Jasa Tambahan untuk ' . $order->customer_name,
                    'description' => 'Sepatu Anda sudah selesai! Mau sekalian tambah layanan dengan harga spesial?',
                    'oto_type' => 'UPSELL',
                    'proposed_services' => implode(', ', $serviceNames),
                    'total_normal_price' => $formatPrice($totalNormal),
                    'total_oto_price' => $formatPrice($totalOTO),
                    'total_discount' => $formatPrice($totalDiscount),
                    'discount_percent' => round($discountPercent, 2),
                    'estimated_days' => 2, // Default 2 days for OTO
                    'valid_until' => now()->addDays((int) $request->valid_days),
                    'status' => 'PENDING_CX', // Directly to CX Pool
                    'dp_required' => $formatPrice($totalOTO * 0.5), // 50% DP
                    'created_by' => Auth::id(),
                ]);

                // Soft reserve materials
                $this->materialService->softReserveForOTO($oto);

                // Update work order
                $order->update([
                    'has_active_oto' => true,
                ]);
            });

            return redirect()->route('finish.show', $order->id)
                ->with('success', 'Penawaran OTO berhasil dibuat dan masuk ke Kolam CX untuk ditangani.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat OTO: ' . $e->getMessage());
        }
    }
    
    public function trash()
    {
        $this->authorize('manageFinish', WorkOrder::class);
        $deletedOrders = WorkOrder::onlyTrashed()
                            ->orderBy('deleted_at', 'desc')
                            ->get();

        return view('finish.trash', compact('deletedOrders'));
    }

    public function restore($id)
    {
        $this->authorize('manageFinish', WorkOrder::class);
        $order = WorkOrder::withTrashed()->findOrFail($id);
        $order->restore();

        return back()->with('success', "Order {$order->spk_number} berhasil dikembalikan (Restore).");
    }

    public function forceDelete($id)
    {
        $this->authorize('manageFinish', WorkOrder::class);
        $order = WorkOrder::withTrashed()->findOrFail($id);
        $order->forceDelete();

        return back()->with('success', "Order {$order->spk_number} berhasil dihapus permanen.");
    }

    public function bulkRestore(Request $request)
    {
        $this->authorize('manageFinish', WorkOrder::class);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
        ]);

        $count = WorkOrder::onlyTrashed()
            ->whereIn('id', $request->ids)
            ->restore();

        return back()->with('success', "Berhasil mengembalikan ({$count}) data order.");
    }

    public function bulkForceDelete(Request $request)
    {
        $this->authorize('manageFinish', WorkOrder::class);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
        ]);

        $count = 0;
        $orders = WorkOrder::onlyTrashed()->whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            $order->forceDelete();
            $count++;
        }

        return back()->with('success', "Berhasil menghapus permanen ({$count}) data order.");
    }
    public function sendEmail($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            if (!$order->customer_email) {
                return response()->json(['success' => false, 'message' => 'Email customer tidak tersedia.']);
            }

            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderFinished($order));

            return response()->json(['success' => true, 'message' => 'Notifikasi selesai berhasil dikirim ke email customer.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate or Regenerate the Finish Photo Report PDF
     */
    public function generateReport(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        try {
            $service = app(PhotoReportService::class);
            $path = $service->generateFinishReport($order);

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada foto finish/after yang ditemukan untuk order ini.'
                ], 422);
            }

            // Ensure invoice_token exists
            if (empty($order->invoice_token)) {
                $order->invoice_token = \Illuminate\Support\Str::uuid()->toString();
                $order->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil di-generate.',
                'report_url' => route('customer.report', [
                    'spk' => \Illuminate\Support\Str::slug($order->spk_number),
                    'token' => $order->invoice_token
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Delete selected history items
     */
    public function bulkDeleteSelection(Request $request)
    {
        $this->authorize('manageFinish', WorkOrder::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
        ]);

        $ids = $request->ids;

        try {
            DB::beginTransaction();
            
            // Soft delete them (moved to trash)
            WorkOrder::whereIn('id', $ids)->delete();

            DB::commit();
            return back()->with('success', count($ids) . ' data riwayat berhasil dihapus (dipindahkan ke sampah).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * View/Stream the PDF report directly with proper headers
     */
    public function viewReport($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Security: Strictly only allow public viewing for SELESAI orders
        if ($order->status !== WorkOrderStatus::SELESAI) {
             abort(403, 'Laporan hanya tersedia untuk order yang sudah SELESAI.');
        }

        // Reconstruct the standardized filename (matching PhotoReportService logic)
        $filename = 'REPORT_FINISH_' . str_replace('/', '-', $order->spk_number) . '.pdf';
        $path = storage_path('app/public/reports/finish/' . $filename);

        // [ROBUSTNESS] If physical file missing, try to generate it ON THE FLY
        if (!file_exists($path)) {
            try {
                $service = app(PhotoReportService::class);
                $service->generateFinishReport($order);
                
                // Re-check existence after generation
                if (!file_exists($path)) {
                    abort(404, 'File PDF tidak ditemukan dan gagal di-generate otomatis.');
                }
            } catch (\Exception $e) {
                abort(500, 'Gagal generate PDF otomatis: ' . $e->getMessage());
            }
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
}
