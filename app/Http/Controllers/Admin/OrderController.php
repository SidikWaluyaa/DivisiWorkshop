<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Service;
use App\Models\WorkOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display the comprehensive detail of a work order.
     */
    public function show($id)
    {
        $order = WorkOrder::with([
            'customer',
            'services',
            'materials',
            'photos.uploader', 
            'logs.user',
            'payments',
            'invoice', // Added invoice relation
            'storageAssignments.rack',
            'storageAssignments.storedByUser',
            'workOrderServices.service',
            'prepWashingBy', 'prepSolBy', 'prepUpperBy',
            'prodSolBy', 'prodUpperBy', 'prodCleaningBy',
            'qcJahitBy', 'qcCleanupBy', 'qcFinalBy',
            'revisions.creator', 'revisions.resolver',
            'warranties.creator', 'warranties.finisher', 'warranties.reworkWorkOrder',
            'cxIssues.reporter', 'cxIssues.resolver',
            'otos.creator',
            'lead',
        ])->findOrFail($id);

        // All available services for the "add service" dropdown
        $allServices = Service::orderBy('category')->orderBy('name')->get(['id', 'name', 'category', 'price']);

        return view('admin.orders.show', compact('order', 'allServices'));
    }

    public function printShippingLabel($id)
    {
        $order = WorkOrder::with(['customer'])->findOrFail($id);
        return view('admin.orders.shipping-label', compact('order'));
    }

    /**
     * Add a service to a work order.
     * Supports both existing services (by service_id) and custom services.
     */
    public function addService(Request $request, $id)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'category_name' => 'nullable|string|max:255',
            'custom_service_name' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'service_details' => 'nullable|array',
        ]);

        $order = WorkOrder::findOrFail($id);

        $data = [
            'work_order_id' => $order->id,
            'cost' => $request->cost,
            'created_by' => auth()->id(),
        ];

        if ($request->service_id) {
            $service = Service::findOrFail($request->service_id);
            $data['service_id'] = $service->id;
            $data['custom_service_name'] = $service->name;
            $data['category_name'] = $service->category;
        } else {
            $data['custom_service_name'] = $request->custom_service_name ?? 'Layanan Tambahan';
            $data['category_name'] = $request->category_name ?? 'Custom';
        }

        if ($request->service_details) {
            $data['service_details'] = ['manual_detail' => $request->service_details];
        }

        WorkOrderService::create($data);

        // [AUDIT LOG] Record adding service
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'SERVICE_ADDED',
            'description' => "Admin menambahkan layanan: " . $data['custom_service_name'] . " (Rp " . number_format($data['cost'], 0, ',', '.') . ")"
        ]);

        // Recalculate finance
        $order->recalculateTotalPrice(true);
        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil ditambahkan',
            'total_transaksi' => $order->total_transaksi,
            'sisa_tagihan' => $order->sisa_tagihan,
            'status_pembayaran' => $order->status_pembayaran,
        ]);
    }

    /**
     * Update a service's cost or name on a work order.
     */
    public function updateService(Request $request, $id, $serviceId)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'cost' => 'required|numeric|min:0',
            'category_name' => 'nullable|string|max:255',
            'custom_service_name' => 'nullable|string|max:255',
            'service_details' => 'nullable|array',
        ]);

        $order = WorkOrder::findOrFail($id);
        $wos = WorkOrderService::where('work_order_id', $order->id)
            ->where('id', $serviceId)
            ->firstOrFail();

        $updateData = [
            'cost' => $request->cost,
            'category_name' => $request->category_name ?? $wos->category_name,
            'custom_service_name' => $request->custom_service_name ?? $wos->custom_service_name,
        ];

        if ($request->has('service_details')) {
            $existing = $wos->service_details ?? [];
            $existing['manual_detail'] = $request->service_details;
            $updateData['service_details'] = $existing;
        }

        $wos->update($updateData);

        // [AUDIT LOG] Record service update
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'SERVICE_UPDATED',
            'description' => "Admin mengubah layanan [{$wos->custom_service_name}] menjadi: " . $updateData['custom_service_name'] . " (Rp " . number_format($updateData['cost'], 0, ',', '.') . ")"
        ]);

        // Recalculate finance
        $order->recalculateTotalPrice(true);
        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil diperbarui',
            'total_transaksi' => $order->total_transaksi,
            'sisa_tagihan' => $order->sisa_tagihan,
            'status_pembayaran' => $order->status_pembayaran,
        ]);
    }

    /**
     * Remove a service from a work order.
     */
    public function removeService($id, $serviceId)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $order = WorkOrder::findOrFail($id);
        $wos = WorkOrderService::where('work_order_id', $order->id)
            ->where('id', $serviceId)
            ->firstOrFail();

        $oldServiceName = $wos->custom_service_name;
        $wos->delete();

        // [AUDIT LOG] Record service removal
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'SERVICE_REMOVED',
            'description' => "Admin menghapus layanan: " . $oldServiceName
        ]);

        // Recalculate finance
        $order->recalculateTotalPrice(true);
        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dihapus',
            'total_transaksi' => $order->total_transaksi,
            'sisa_tagihan' => $order->sisa_tagihan,
            'status_pembayaran' => $order->status_pembayaran,
        ]);
    }

    /**
     * Update the estimation date of a work order.
     */
    public function updateEstimationDate(Request $request, $id)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'estimation_date' => 'required|date',
        ]);

        $order = WorkOrder::findOrFail($id);
        $oldDate = $order->estimation_date ? $order->estimation_date->format('d/m/Y') : 'Belum ada';
        
        $order->estimation_date = $request->estimation_date;
        $order->is_manual_estimasi = true; // Lock manual override
        $order->save();

        // Also update related invoice manual override and date if exists
        if ($order->invoice) {
            $order->invoice->update([
                'estimasi_selesai' => $request->estimation_date,
                'is_manual_estimasi' => true,
            ]);
        }

        // [AUDIT LOG] Record estimation change
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'ESTIMATION_UPDATED',
            'description' => "Admin mengubah estimasi pengerjaan dari {$oldDate} ke " . $order->estimation_date->format('d/m/Y')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estimasi tanggal berhasil diperbarui',
            'estimation_date' => $order->estimation_date->format('d M Y'),
        ]);
    }

    /**
     * Update the shoe information (brand, size, color, etc.) of a work order.
     */
    public function updateShoeInfo(Request $request, $id)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'nullable|string|max:50',
            'shoe_color' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'accessories_tali' => 'nullable|string|max:50',
            'accessories_insole' => 'nullable|string|max:50',
            'accessories_box' => 'nullable|string|max:50',
            'accessories_other' => 'nullable|string|max:500',
            'hk_days' => 'nullable|integer|min:0',
            'is_warranty' => 'nullable|boolean',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $oldCategory = $order->category;
        $newCategory = $request->category;

        $order->update($request->only([
            'shoe_brand', 'shoe_size', 'shoe_color', 'category',
            'accessories_tali', 'accessories_insole', 'accessories_box', 'accessories_other',
            'hk_days', 'is_warranty'
        ]));

        // If category changed, update SPK prefix
        if ($oldCategory !== $newCategory) {
            $catToPrefix = [
                'Sepatu' => 'S',
                'Tas' => 'T',
                'Topi' => 'H', // Headwear
                'Apparel' => 'A',
                'Lainnya' => 'L',
            ];

            if (isset($catToPrefix[$newCategory])) {
                $newPrefix = $catToPrefix[$newCategory];
                $spkParts = explode('-', $order->spk_number);
                if (count($spkParts) >= 2) {
                    $spkParts[0] = $newPrefix;
                    $newSpk = implode('-', $spkParts);
                    
                    // Check for uniqueness before updating SPK to avoid crash
                    if (!WorkOrder::where('spk_number', $newSpk)->where('id', '!=', $order->id)->exists()) {
                        $order->spk_number = $newSpk;
                        $order->save();
                    }
                }
            }
        }

        // Log the change
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'MANUAL_EDIT_DETAIL',
            'description' => 'Admin mengubah detail ' . ($order->category ?? 'barang') . ': ' . $order->shoe_brand . ' (' . ($order->shoe_size ?? '-') . ')' . ($oldCategory !== $newCategory ? '. Kategori diubah dari ' . ($oldCategory ?? 'Kosong') . ' ke ' . $newCategory : '')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail sepatu berhasil diperbarui'
        ]);
    }

    public function updateShippingAddress(Request $request, $id)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'city_id' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:100',
            'district_id' => 'nullable|string|max:50',
            'village' => 'nullable|string|max:100',
            'village_id' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:100',
            'province_id' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        // Update WorkOrder local address
        $order->customer_address = $request->address;
        $order->save();

        // Update Customer Master Data if exists (match by phone)
        $customer = \App\Models\Customer::where('phone', $order->customer_phone)->first();
        if ($customer) {
            $customer->update([
                'address' => $request->address,
                'province' => $request->province,
                'province_id' => $request->province_id,
                'city' => $request->city,
                'city_id' => $request->city_id,
                'district' => $request->district,
                'district_id' => $request->district_id,
                'village' => $request->village,
                'village_id' => $request->village_id,
                'postal_code' => $request->postal_code,
            ]);
        }

        // Log the change
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'MANUAL_EDIT_ADDRESS',
            'description' => 'Admin memperbarui alamat pengiriman dan data master customer'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat pengiriman berhasil diperbarui'
        ]);
    }

    public function updateCustomerInfo(Request $request, $id)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'channel' => 'required|string|in:ONLINE,OFFLINE',
        ]);

        $order = WorkOrder::with(['lead'])->findOrFail($id);
        
        $oldPhone = $order->customer_phone;
        $oldChannel = $order->channel;
        
        $order->customer_name = $request->name;
        $order->customer_phone = $request->phone;
        $order->customer_email = $request->email;
        $order->channel = $request->channel;
        $order->save();

        // Sync with associated CsLead if exists
        if ($order->lead) {
            $order->lead->channel = $request->channel;
            $order->lead->save();
        }

        // Log the change
        $channelLogText = ($oldChannel !== $request->channel) ? " & mengubah channel dari {$oldChannel} ke {$request->channel}" : "";
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'MANUAL_EDIT_CUSTOMER',
            'description' => "Admin mengubah identitas customer dari {$oldPhone} ke {$order->customer_phone}{$channelLogText}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Identitas customer berhasil diperbarui'
        ]);
    }

    /**
     * Update the warranty information of a work order manually.
     */
    public function updateWarrantyInfo(Request $request, $id)
    {
        $this->authorize('manageOrder', WorkOrder::class);

        $request->validate([
            'warranty_duration_months' => 'nullable|integer|min:0|max:120',
            'warranty_unit' => 'nullable|string|in:days,months',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        if (is_null($request->warranty_duration_months) || $request->warranty_duration_months === '') {
            $order->warranty_duration_months = null;
            $order->warranty_expires_at = null;
            $order->save();

            \App\Models\WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id(),
                'step' => $order->status->value,
                'action' => 'WARRANTY_REMOVED',
                'description' => "Admin menghapus durasi garansi dari SPK"
            ]);
        } else {
            $duration = (int) $request->warranty_duration_months;
            $unit = $request->input('warranty_unit', 'months');
            $baseDate = $order->taken_date ?? now();
            
            if ($unit === 'days') {
                $order->warranty_duration_months = (int) ceil($duration / 30); // Kasar estimasi untuk kompabilitas kolom
                $order->warranty_expires_at = $baseDate->copy()->addDays($duration);
            } else {
                $order->warranty_duration_months = $duration;
                $order->warranty_expires_at = $baseDate->copy()->addMonths($duration);
            }
            $order->save();

            \App\Models\WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id(),
                'step' => $order->status->value,
                'action' => 'WARRANTY_UPDATED',
                'description' => "Admin memperbarui durasi garansi menjadi {$duration} " . ($unit === 'days' ? 'hari' : 'bulan') . " (Berlaku hingga " . $order->warranty_expires_at->format('d/m/Y') . ")"
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Informasi garansi berhasil diperbarui',
            'warranty_duration_months' => $order->warranty_duration_months,
            'warranty_expires_at' => $order->warranty_expires_at ? $order->warranty_expires_at->format('d M Y') : '-',
        ]);
    }

    /**
     * Update the SPK description of a work order.
     */
    public function updateSpkDescription(Request $request, $id)
    {
        $this->authorize('updateSpkDescription', WorkOrder::class);

        $request->validate([
            'spk_description' => 'nullable|string|max:5000',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $order->spk_description = $request->spk_description;
        $order->save();

        // [AUDIT LOG] Record change
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'SPK_DESCRIPTION_UPDATED',
            'description' => "Admin memperbarui deskripsi SPK"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deskripsi SPK berhasil diperbarui',
            'spk_description' => $order->spk_description,
        ]);
    }

    /**
     * Update the technician notes of a work order.
     */
    public function updateTechnicianNotes(Request $request, $id)
    {
        $this->authorize('updateSpkDescription', WorkOrder::class);

        $request->validate([
            'technician_notes' => 'nullable|string|max:5000',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $order->technician_notes = $request->technician_notes;
        $order->save();

        // [AUDIT LOG] Record change
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value ?? $order->status,
            'action' => 'TECHNICIAN_NOTES_UPDATED',
            'description' => "Admin memperbarui catatan gudang SPK"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan gudang berhasil diperbarui',
            'notes' => $order->technician_notes
        ]);
    }

    /**
     * Cancel a work order and manage its associated invoice.
     */
    public function cancel(Request $request, $id)
    {
        // 1. Authorization: Only admin can perform this
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengguna dengan peran Admin yang dapat membatalkan SPK ini.'
            ], 403);
        }

        // 2. Validation
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $order = WorkOrder::with(['invoice.workOrders', 'payments'])->findOrFail($id);

        // Check if already cancelled or finished
        if ($order->status === \App\Enums\WorkOrderStatus::BATAL) {
            return response()->json([
                'success' => false,
                'message' => 'SPK ini sudah dibatalkan sebelumnya.'
            ], 422);
        }

        if ($order->status === \App\Enums\WorkOrderStatus::SELESAI || $order->status === \App\Enums\WorkOrderStatus::HISTORY) {
            return response()->json([
                'success' => false,
                'message' => 'SPK yang sudah selesai tidak dapat dibatalkan.'
            ], 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $invoiceLog = '';
            
            // Check if there is an invoice linked
            if ($order->invoice_id && $order->invoice) {
                $invoice = $order->invoice;
                
                // Get all SPKs linked to this invoice (including this one)
                $invoiceWorkOrders = $invoice->workOrders;
                $activeCount = $invoiceWorkOrders->count();

                if ($activeCount > 1) {
                    // Scenario A: More than 1 SPK in the invoice.
                    // Unlink this SPK from the invoice
                    $order->invoice_id = null;
                    $order->save();

                    // Recalculate Invoice totals
                    $invoice->syncFinancials();
                    $invoice->syncSpkStatus();

                    $invoiceLog = " Dilepas dari Invoice {$invoice->invoice_number}. Total tagihan invoice disinkronkan kembali.";
                } else {
                    // Scenario B: Only 1 SPK in the invoice.
                    // Check if there are any recorded payments (verified or unverified)
                    $hasPayments = $invoice->payments()->exists() || $invoice->invoicePayments()->exists();

                    if (!$hasPayments) {
                        // Unlink first to avoid foreign key / dependency issues during delete
                        $order->invoice_id = null;
                        $order->save();

                        $invoiceNumber = $invoice->invoice_number;
                        $invoice->delete();
                        $invoiceLog = " Invoice {$invoiceNumber} dihapus karena tidak memiliki SPK aktif lainnya dan tidak memiliki catatan pembayaran.";
                    } else {
                        // There are payments, so do NOT delete the Invoice to maintain financial compliance (SOX)
                        // Mark Invoice status as cancelled/batal
                        $invoice->status = 'Batal';
                        $invoice->save();
                        $invoiceLog = " Invoice {$invoice->invoice_number} diubah statusnya menjadi Batal (tidak dihapus karena terdapat riwayat pembayaran).";
                    }
                }
            }

            // Update WorkOrder status to BATAL
            $order->status = \App\Enums\WorkOrderStatus::BATAL;
            $order->reception_rejection_reason = $request->reason;
            $order->save();

            // Create Audit Log
            \App\Models\WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id(),
                'step' => \App\Enums\WorkOrderStatus::BATAL->value,
                'action' => 'ORDER_CANCELLED',
                'description' => "SPK dibatalkan oleh " . auth()->user()->name . ". Alasan: " . $request->reason . "." . $invoiceLog
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SPK berhasil dibatalkan.' . $invoiceLog
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan SPK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bypass the current status of a work order and mark it as finished directly.
     */
    public function bypassToFinish(Request $request, $id)
    {
        // 1. Authorization: Whitelist emails
        $allowedEmails = [
            'elin@workshop.com',
            'sandi@workshop.com',
            'indra@workshop.com',
            'siska@workshop.com',
            'admin@workshop.com'
        ];

        if (!in_array(auth()->user()->email, $allowedEmails)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses khusus untuk melakukan bypass status SPK ini.'
            ], 403);
        }

        // 2. Validation
        $request->validate([
            'note' => 'required|string|min:10|max:1000',
        ]);

        $order = WorkOrder::with(['photos'])->findOrFail($id);

        // Check if already finished or cancelled
        if ($order->status === \App\Enums\WorkOrderStatus::BATAL) {
            return response()->json([
                'success' => false,
                'message' => 'SPK yang sudah dibatalkan tidak dapat di-bypass.'
            ], 422);
        }

        if ($order->status === \App\Enums\WorkOrderStatus::SELESAI || $order->status === \App\Enums\WorkOrderStatus::HISTORY) {
            return response()->json([
                'success' => false,
                'message' => 'SPK sudah berstatus selesai atau history.'
            ], 422);
        }

        // Check if a photo with step = 'FINISH' exists
        $hasFinishPhoto = $order->photos()->where('step', 'FINISH')->exists();
        if (!$hasFinishPhoto) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan bypass. Silakan unggah foto After (FINISH) terlebih dahulu di galeri foto.'
            ], 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $oldStatus = $order->status->value;

            // Update status & finished info
            $order->status = \App\Enums\WorkOrderStatus::SELESAI;
            $order->finished_date = now();
            $order->current_location = 'Rak Selesai / Pickup Area (Rumah Hijau)';
            $order->save();

            // Create Audit Log
            \App\Models\WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id(),
                'step' => \App\Enums\WorkOrderStatus::SELESAI->value,
                'action' => 'ORDER_BYPASSED',
                'description' => "Status SPK di-bypass langsung dari " . $oldStatus . " ke SELESAI oleh " . auth()->user()->name . ". Catatan: " . $request->note
            ]);

            // Dispatch pasca-selesai triggers
            if (class_exists(\App\Jobs\GeneratePhotoReportJob::class)) {
                \App\Jobs\GeneratePhotoReportJob::dispatch($order);
            }

            if (class_exists(\App\Services\CxConfirmationService::class)) {
                app(\App\Services\CxConfirmationService::class)->createFromOrder($order);
            }

            if (class_exists(\App\Services\Storage\StorageService::class)) {
                app(\App\Services\Storage\StorageService::class)->releaseFromInbound($order);
            }

            if (class_exists(\App\Models\CxIssue::class)) {
                \App\Models\CxIssue::where('work_order_id', $order->id)
                    ->where('status', 'OPEN')
                    ->where('category', 'like', 'Revisi %')
                    ->update([
                        'status' => 'RESOLVED',
                        'resolved_by' => auth()->id(),
                        'resolved_at' => now(),
                        'resolution_notes' => 'Diselesaikan otomatis karena status SPK di-bypass langsung ke SELESAI'
                    ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status SPK berhasil di-bypass langsung ke SELESAI.'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat bypass status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a cancelled work order back to its previous active status.
     */
    public function restore(Request $request, $id)
    {
        // 1. Authorization: Only admin can perform this
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengguna dengan peran Admin yang dapat memulihkan SPK ini.'
            ], 403);
        }

        $order = WorkOrder::findOrFail($id);

        // Check if status is indeed BATAL
        if ($order->status !== \App\Enums\WorkOrderStatus::BATAL) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya SPK yang dibatalkan yang dapat dipulihkan.'
            ], 422);
        }

        // 2. Find the last active step before BATAL in logs
        $lastActiveLog = \App\Models\WorkOrderLog::where('work_order_id', $order->id)
            ->where('step', '!=', \App\Enums\WorkOrderStatus::BATAL->value)
            ->orderBy('id', 'desc')
            ->first();

        // Fallback status if no logs are found
        $restoreStatus = $lastActiveLog ? $lastActiveLog->step : \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value;

        // Try to match with Enum instance if it exists
        $enumStatus = \App\Enums\WorkOrderStatus::tryFrom($restoreStatus);
        if (!$enumStatus) {
            $enumStatus = \App\Enums\WorkOrderStatus::WAITING_PAYMENT;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Restore SPK columns
            $order->status = $enumStatus;
            $order->reception_rejection_reason = null; // Clear rejection reason
            $order->invoice_id = null; // Keep unlinked as loose SPK for safety
            $order->save();

            // Create Audit Log
            \App\Models\WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id(),
                'step' => $enumStatus->value,
                'action' => 'ORDER_RESTORED',
                'description' => "SPK dipulihkan dari status BATAL oleh " . auth()->user()->name . ". Status dikembalikan ke: " . $enumStatus->label()
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => "SPK berhasil dipulihkan dan dikembalikan ke status " . $enumStatus->label() . "."
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulihkan SPK: ' . $e->getMessage()
            ], 500);
        }
    }
}

