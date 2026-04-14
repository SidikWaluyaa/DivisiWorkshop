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
            'storageAssignments.rack',
            'storageAssignments.storedByUser',
            'workOrderServices.service',
            'prepWashingBy', 'prepSolBy', 'prepUpperBy',
            'prodSolBy', 'prodUpperBy', 'prodCleaningBy',
            'qcJahitBy', 'qcCleanupBy', 'qcFinalBy',
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
        $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'category_name' => 'nullable|string|max:255',
            'custom_service_name' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'service_details' => 'nullable|string|max:500',
        ]);

        $order = WorkOrder::findOrFail($id);

        $data = [
            'work_order_id' => $order->id,
            'cost' => $request->cost,
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
        $request->validate([
            'cost' => 'required|numeric|min:0',
            'custom_service_name' => 'nullable|string|max:255',
            'service_details' => 'nullable|string|max:500',
        ]);

        $order = WorkOrder::findOrFail($id);
        $wos = WorkOrderService::where('work_order_id', $order->id)
            ->where('id', $serviceId)
            ->firstOrFail();

        $updateData = [
            'cost' => $request->cost,
            'custom_service_name' => $request->custom_service_name ?? $wos->custom_service_name,
        ];

        if ($request->has('service_details')) {
            $existing = $wos->service_details ?? [];
            $existing['manual_detail'] = $request->service_details;
            $updateData['service_details'] = $existing;
        }

        $wos->update($updateData);

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
        $order = WorkOrder::findOrFail($id);
        $wos = WorkOrderService::where('work_order_id', $order->id)
            ->where('id', $serviceId)
            ->firstOrFail();

        $wos->delete();

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
        $request->validate([
            'estimation_date' => 'required|date',
        ]);

        $order = WorkOrder::findOrFail($id);
        $order->estimation_date = $request->estimation_date;
        $order->save();

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
        $request->validate([
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'nullable|string|max:50',
            'shoe_color' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'accessories_tali' => 'nullable|string|max:50',
            'accessories_insole' => 'nullable|string|max:50',
            'accessories_box' => 'nullable|string|max:50',
            'accessories_other' => 'nullable|string|max:500',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $oldCategory = $order->category;
        $newCategory = $request->category;

        $order->update($request->only([
            'shoe_brand', 'shoe_size', 'shoe_color', 'category',
            'accessories_tali', 'accessories_insole', 'accessories_box', 'accessories_other'
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
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $oldPhone = $order->customer_phone;
        
        $order->customer_name = $request->name;
        $order->customer_phone = $request->phone;
        $order->customer_email = $request->email;
        $order->save();

        // Log the change
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => $order->status->value,
            'action' => 'MANUAL_EDIT_CUSTOMER',
            'description' => "Admin mengubah identitas customer dari {$oldPhone} ke {$order->customer_phone}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Identitas customer berhasil diperbarui'
        ]);
    }
}
