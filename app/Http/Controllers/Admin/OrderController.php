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
}
