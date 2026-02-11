<?php

namespace App\Services;

use App\Enums\RackStatus;
use App\Enums\StorageCategory;
use App\Enums\WorkOrderStatus;
use App\Models\StorageAssignment;
use App\Models\StorageRack;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceptionService
{
    protected CustomerService $customerService;
    protected WorkflowService $workflowService;

    public function __construct(CustomerService $customerService, WorkflowService $workflowService)
    {
        $this->customerService = $customerService;
        $this->workflowService = $workflowService;
    }

    /**
     * Create a new Manual WorkOrder
     */
    public function createManualOrder(array $data): WorkOrder
    {
        return DB::transaction(function () use ($data) {
            // 1. Sync Customer
            $this->customerService->syncCustomer($data);

            // 2. Prepare Order Data
            $orderData = $data;
            $orderData['status'] = WorkOrderStatus::DITERIMA->value;
            $orderData['current_location'] = 'Gudang Penerimaan';
            $orderData['created_by'] = Auth::id();
            
            // Ensure nulls for QC
            $orderData['reception_qc_passed'] = null;
            $orderData['warehouse_qc_status'] = null;

            // 3. Create Order
            $order = WorkOrder::create($orderData);

            // 4. Handle Services
            if (!empty($data['services']) && is_array($data['services'])) {
                $this->processServices($order, $data['services']);
            }

            // 5. Log
            $order->logs()->create([
                'step' => 'RECEPTION',
                'action' => 'MANUAL_ORDER_CREATED',
                'user_id' => Auth::id(),
                'description' => 'Order Manual Dibuat - Menunggu Pengecekan Fisik'
            ]);

            return $order;
        });
    }

    /**
     * Process Services for an Order
     */
    protected function processServices(WorkOrder $order, array $services): void
    {
        $totalCost = 0;
        foreach ($services as $svc) {
            $hasId = !empty($svc['service_id']);
            
            // Decode details if string
            $details = isset($svc['details']) ? (is_string($svc['details']) ? json_decode($svc['details'], true) : $svc['details']) : [];

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
        
        $order->update(['total_service_price' => $totalCost]);
    }

    /**
     * Confirm SPK (Move from Pending to Diterima) and Assign Rack
     */
    public function confirmOrder(WorkOrder $order, ?string $manualRackCode = null): void
    {
        // Use Workflow Service for status update (handles logging)
        $this->workflowService->updateStatus(
            $order, 
            WorkOrderStatus::DITERIMA, 
            'SPK Dikonfirmasi dan Diterima Fisik di Gudang'
        );
        
        $order->update(['entry_date' => now()]);

        // Auto-assign to Transit Rack
        $this->assignToTransitRack($order, $manualRackCode);
    }

    /**
     * Process Reception QC and Updates
     */
    public function processReceptionQC(WorkOrder $order, array $data): WorkOrder
    {
        return DB::transaction(function () use ($order, $data) {
            // 1. Determine Status based on QC
            $passed = filter_var($data['reception_qc_passed'], FILTER_VALIDATE_BOOLEAN);
            
            // 2. Sync Customer
            $this->customerService->syncCustomer($data);

            // 3. Update Order Details
            $updateData = [
                // Access customer data via Input names directly or mapped? 
                // The dataset passed here comes from request, so uses field names.
                // We'll trust the array keys match the model attributes or request input names.
                'entry_date' => $data['entry_date'],
                'estimation_date' => $data['estimation_date'] ?? null,
                
                'accessories_tali' => $data['accessories_tali'],
                'accessories_insole' => $data['accessories_insole'],
                'accessories_box' => $data['accessories_box'],
                'accessories_other' => $data['accessories_other'] ?? null,
                
                'reception_qc_passed' => $passed,
                'warehouse_qc_status' => $passed ? 'lolos' : 'reject',
                'warehouse_qc_notes' => $passed ? null : ($data['reception_rejection_reason'] ?? null),
                
                'technician_notes' => $data['technician_notes'] ?? null,
                'warehouse_qc_by' => Auth::id(),
                'warehouse_qc_at' => now(),

                // Allow updating item details
                'shoe_brand' => $data['shoe_brand'] ?? $order->shoe_brand,
                'shoe_type' => $data['shoe_type'] ?? $order->shoe_type,
                'shoe_size' => $data['shoe_size'] ?? $order->shoe_size,
                'shoe_color' => $data['shoe_color'] ?? $order->shoe_color,
                'category' => $data['category'] ?? $order->category,
            ];

            $order->update($updateData);

            // Update Status via Workflow (to ensure consistent logging vs manual update)
            // Note: The original controller handled this manually to avoid logic loops, 
            // but we should use standard flow if possible.
            // Original: "Status is updated via workflow object below" - wait, the controller comment said:
            // "Removal: Status is updated via workflow object below" but then it seemingly didn't call workflow?
            // Ah, line 680 calculated $newStatus.
            $newStatus = $passed ? WorkOrderStatus::ASSESSMENT : WorkOrderStatus::CX_FOLLOWUP;
            
            // We'll update the status manually to allow for the specific logic without double-logging if needed,
            // or better, just use the Workflow service.
            // Since we want "QC Passed" or "QC Rejected" logs, Workflow might be too generic unless we pass description.
            $order->status = $newStatus;
            $order->save();

            // 4. Handle Accessory Storage
            $this->handleAccessoryStorage($order, $data);

            // 5. Logs & CX Issues
            if (!$passed) {
                // QC Rejected
                $order->logs()->create([
                    'step' => 'RECEPTION',
                    'action' => 'QC_REJECTED',
                    'user_id' => Auth::id(),
                    'description' => 'QC Awal Gagal: ' . ($data['reception_rejection_reason'] ?? '-')
                ]);

                // Handle Evidence Photos
                $evidencePaths = [];
                if (!empty($data['evidence_photos']) && is_array($data['evidence_photos'])) {
                    foreach ($data['evidence_photos'] as $index => $photo) {
                        try {
                            $filename = 'RECEPTION_REJECT_' . $order->spk_number . '_' . time() . '_' . $index;
                            $evidencePaths[] = \App\Utils\ImageHelper::convertToJpg($photo, 'cx-issues', $filename);
                        } catch (\Exception $e) {
                            Log::error('Failed to upload evidence photo: ' . $e->getMessage());
                        }
                    }
                }

                // Create Issue
                \App\Models\CxIssue::create([
                    'work_order_id' => $order->id,
                    'spk_number' => $order->spk_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'reported_by' => Auth::id(),
                    'type' => 'FOLLOW_UP',
                    'category' => 'Kondisi Awal',
                    'description' => ($data['reception_rejection_reason'] ?? '-'),
                    'suggested_services' => !empty($data['suggested_services']) 
                        ? collect($data['suggested_services'])->map(fn($s, $idx) => ($idx + 1) . ". " . $s)->implode("\n") 
                        : null,
                    'recommended_services' => !empty($data['recommended_services']) 
                        ? collect($data['recommended_services'])->map(fn($s, $idx) => ($idx + 1) . ". " . $s)->implode("\n") 
                        : null,
                    'photos' => $evidencePaths,
                    'status' => 'OPEN',
                ]);
            } else {
                // QC Passed
                $order->logs()->create([
                    'step' => 'RECEPTION',
                    'action' => 'QC_PASSED',
                    'user_id' => Auth::id(),
                    'description' => 'QC Awal Lolos'
                ]);
            }

            return $order;
        });
    }

    /**
     * Handle Accessory Storage Logic
     */
    protected function handleAccessoryStorage(WorkOrder $order, array $data): void
    {
        $hasStoredAccessories = 
            in_array($data['accessories_tali'], ['Simpan', 'S']) ||
            in_array($data['accessories_insole'], ['Simpan', 'S']) ||
            in_array($data['accessories_box'], ['Simpan', 'S']);

        $rackCode = $data['accessory_rack_code'] ?? null;

        if ($hasStoredAccessories && $rackCode) {
            $rack = StorageRack::where('rack_code', $rackCode)
                ->where('category', StorageCategory::ACCESSORIES)
                ->firstOrFail();
            
            StorageAssignment::create([
                'work_order_id' => $order->id,
                'rack_code' => $rack->rack_code,
                'category' => $rack->category, // Enum cast handles value?
                'item_type' => 'accessories',
                'stored_at' => now(),
                'stored_by' => Auth::id(),
                'status' => 'stored',
                'notes' => 'Aksesoris: ' . ($data['accessories_other'] ?? 'Lihat Detail Order'),
            ]);

            $rack->incrementCount();

            if ($rack->current_count >= $rack->capacity) {
                $rack->update(['status' => RackStatus::FULL]);
            }
        }
    }

    /**
     * Assign Order to Transit Rack (Before)
     */
    public function assignToTransitRack(WorkOrder $order, ?string $manualRackCode = null): void
    {
        if (is_null($order->storage_rack_code)) {
            $transitRackCode = $manualRackCode ?? 'RAK-BEFORE';
            
            $rack = StorageRack::where('rack_code', $transitRackCode)
                ->where('category', StorageCategory::BEFORE)
                ->first();
            
            if (!$rack) {
                $rack = StorageRack::create([
                    'rack_code' => $transitRackCode,
                    'location' => 'Gudang Penerimaan', 
                    'category' => StorageCategory::BEFORE, 
                    'capacity' => 9999, 
                    'status' => RackStatus::ACTIVE, 
                    'notes' => 'Rak transit manual/auto Reception -> Workshop'
                ]);
            }

            if ($rack) {
                $order->update([
                    'storage_rack_code' => $transitRackCode,
                    'current_location' => 'Gudang Penerimaan',
                    'stored_at' => now(),
                ]);

                StorageAssignment::create([
                    'work_order_id' => $order->id,
                    'rack_code' => $transitRackCode,
                    'category' => StorageCategory::BEFORE, 
                    'stored_at' => now(),
                    'stored_by' => Auth::id(),
                    'item_type' => 'shoes',
                    'status' => 'stored',
                    'notes' => 'Assigned at Reception',
                ]);

                $rack->incrementCount();
            }
        }
    }
}
