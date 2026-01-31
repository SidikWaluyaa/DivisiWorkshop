<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\MaterialReservation;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaterialManagementService
{
    /**
     * Check material availability and categorize them
     * 
     * @param array $materials [['material_id' => 1, 'quantity' => 5], ...]
     * @param int|null $workOrderId
     * @param int|null $otoId
     * @return array
     */
    public function processMaterialSelection(array $materials, ?int $workOrderId = null, ?int $otoId = null): array
    {
        $shoppingItems = [];
        $availableProduction = [];
        $unavailableProduction = [];

        foreach ($materials as $item) {
            $material = Material::find($item['material_id']);
            
            if (!$material) {
                continue;
            }

            $quantity = $item['quantity'] ?? 1;

            // Category SHOPPING - langsung masuk ke pengajuan budget
            if ($material->isShopping()) {
                $shoppingItems[] = [
                    'material' => $material,
                    'quantity' => $quantity,
                ];
                continue;
            }

            // Category PRODUCTION - cek stok
            if ($material->isProduction()) {
                $availableStock = $material->getAvailableStock();
                
                if ($material->isStockAvailable($quantity)) {
                    // Stok tersedia - bisa langsung ke produksi
                    $availableProduction[] = [
                        'material' => $material,
                        'quantity' => $quantity,
                        'available_stock' => $availableStock,
                    ];
                } else {
                    // Stok tidak tersedia - butuh PO atau Followup
                    $unavailableProduction[] = [
                        'material' => $material,
                        'requested_quantity' => $quantity,
                        'available_stock' => $availableStock,
                        'shortage' => $quantity - $availableStock,
                    ];
                }
            }
        }

        return [
            'shopping_items' => $shoppingItems,
            'available_production' => $availableProduction,
            'unavailable_production' => $unavailableProduction,
            'requires_action' => !empty($shoppingItems) || !empty($unavailableProduction),
        ];
    }

    /**
     * Create shopping material request (Pengajuan Budget)
     */
    public function createShoppingRequest(array $items, ?int $workOrderId = null, ?int $otoId = null, ?string $notes = null): MaterialRequest
    {
        return DB::transaction(function () use ($items, $workOrderId, $otoId, $notes) {
            $request = MaterialRequest::create([
                'request_number' => MaterialRequest::generateRequestNumber(),
                'work_order_id' => $workOrderId,
                'oto_id' => $otoId,
                'requested_by' => Auth::id(),
                'type' => 'SHOPPING',
                'status' => 'PENDING',
                'notes' => $notes,
            ]);

            $totalCost = 0;

            foreach ($items as $item) {
                $material = $item['material'];
                $quantity = $item['quantity'];
                $estimatedPrice = $material->price ?? 0;

                $request->items()->create([
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'specification' => trim(($material->sub_category ?? '') . ' - ' . ($material->size ?? '')),
                    'quantity' => $quantity,
                    'unit' => $material->unit,
                    'estimated_price' => $estimatedPrice,
                ]);

                $totalCost += ($quantity * $estimatedPrice);
            }

            $request->update(['total_estimated_cost' => $totalCost]);

            return $request;
        });
    }

    /**
     * Create production PO request
     */
    public function createProductionPO(array $items, ?int $workOrderId = null, ?int $otoId = null, ?string $notes = null): MaterialRequest
    {
        return DB::transaction(function () use ($items, $workOrderId, $otoId, $notes) {
            $request = MaterialRequest::create([
                'request_number' => MaterialRequest::generateRequestNumber(),
                'work_order_id' => $workOrderId,
                'oto_id' => $otoId,
                'requested_by' => Auth::id(),
                'type' => 'PRODUCTION_PO',
                'status' => 'PENDING',
                'notes' => $notes,
            ]);

            $totalCost = 0;

            foreach ($items as $item) {
                $material = $item['material'];
                $shortage = $item['shortage'];
                $estimatedPrice = $material->price ?? 0;

                $request->items()->create([
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'specification' => trim(($material->sub_category ?? '') . ' - ' . ($material->size ?? '')),
                    'quantity' => $shortage,
                    'unit' => $material->unit,
                    'estimated_price' => $estimatedPrice,
                    'notes' => "Kekurangan stok: {$shortage} {$material->unit}. Stok tersedia: {$item['available_stock']}",
                ]);

                $totalCost += ($shortage * $estimatedPrice);
            }

            $request->update(['total_estimated_cost' => $totalCost]);

            return $request;
        });
    }

    /**
     * Reserve available production materials (Soft Reservation)
     */
    public function reserveProductionMaterials(array $items, ?int $workOrderId = null, ?int $otoId = null): array
    {
        $reservations = [];

        DB::transaction(function () use ($items, $workOrderId, $otoId, &$reservations) {
            foreach ($items as $item) {
                $material = $item['material'];
                $quantity = $item['quantity'];

                // Create soft reservation (24 hours to confirm)
                $reservation = MaterialReservation::create([
                    'material_id' => $material->id,
                    'work_order_id' => $workOrderId,
                    'oto_id' => $otoId,
                    'quantity' => $quantity,
                    'type' => 'SOFT',
                    'status' => 'ACTIVE',
                    'expires_at' => now()->addHours(24),
                ]);

                // Increment reserved stock
                $material->increment('reserved_stock', $quantity);

                $reservations[] = $reservation;
            }
        });

        return $reservations;
    }

    /**
     * Process complete material workflow
     * This is the main entry point that handles everything
     */
    public function processCompleteMaterialWorkflow(array $materials, ?int $workOrderId = null, ?int $otoId = null, ?string $notes = null): array
    {
        // Step 1: Analyze materials
        $analysis = $this->processMaterialSelection($materials, $workOrderId, $otoId);

        $result = [
            'success' => true,
            'shopping_request' => null,
            'production_reservations' => [],
            'unavailable_materials' => $analysis['unavailable_production'],
            'message' => '',
        ];

        // Step 2: Handle shopping materials
        if (!empty($analysis['shopping_items'])) {
            $result['shopping_request'] = $this->createShoppingRequest(
                $analysis['shopping_items'],
                $workOrderId,
                $otoId,
                $notes
            );
        }

        // Step 3: Reserve available production materials
        if (!empty($analysis['available_production'])) {
            $result['production_reservations'] = $this->reserveProductionMaterials(
                $analysis['available_production'],
                $workOrderId,
                $otoId
            );
        }

        // Step 4: Handle unavailable production materials (Create PO)
        if (!empty($analysis['unavailable_production'])) {
            $result['production_po'] = $this->createProductionPO(
                $analysis['unavailable_production'],
                $workOrderId,
                $otoId,
                $notes
            );
        }

        // Step 5: Sync with Work Order pivot table (if applicable)
        if ($workOrderId) {
            $order = WorkOrder::find($workOrderId);
            if ($order) {
                // Prepare sync data
                $syncData = [];
                
                // 1. Available Production (ALLOCATED)
                foreach ($analysis['available_production'] as $item) {
                    $syncData[$item['material']->id] = [
                        'quantity' => $item['quantity'],
                        'status' => 'ALLOCATED'
                    ];
                }
                
                // 2. Unavailable Production (REQUESTED)
                foreach ($analysis['unavailable_production'] as $item) {
                    $syncData[$item['material']->id] = [
                        'quantity' => $item['requested_quantity'],
                        'status' => 'REQUESTED'
                    ];
                }
                
                // 3. Shopping Items (REQUESTED)
                foreach ($analysis['shopping_items'] as $item) {
                    $syncData[$item['material']->id] = [
                        'quantity' => $item['quantity'],
                        'status' => 'REQUESTED'
                    ];
                }
                
                // Sync without detaching existing ones (to allow additive requests)
                foreach ($syncData as $matId => $pivotData) {
                    $order->materials()->updateExistingPivot($matId, $pivotData);
                    if (!$order->materials->contains($matId)) {
                        $order->materials()->attach($matId, $pivotData);
                    }
                }
            }
        }

        // Step 4: Build result message
        $messages = [];
        
        if ($result['shopping_request']) {
            $messages[] = "Pengajuan budget dibuat: {$result['shopping_request']->request_number}";
        }
        
        if (!empty($result['production_reservations'])) {
            $count = count($result['production_reservations']);
            $messages[] = "{$count} material produksi berhasil direserve";
        }
        
        if (!empty($result['unavailable_materials'])) {
            $count = count($result['unavailable_materials']);
            if (isset($result['production_po'])) {
                $messages[] = "Dibuatkan PO #{$result['production_po']->request_number} untuk {$count} material kurang";
            } else {
                $messages[] = "{$count} material produksi tidak tersedia";
            }
        }

        $result['message'] = implode('. ', $messages);

        return $result;
    }
}
