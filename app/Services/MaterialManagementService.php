<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\MaterialReservation;
use App\Models\MaterialTransaction;
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
                $workOrderIdInItem = $item['work_order_id'] ?? $workOrderId;

                $request->items()->create([
                    'material_id' => $material->id,
                    'work_order_id' => $workOrderIdInItem,
                    'material_name' => $material->name,
                    'specification' => trim(($material->sub_category ?? '') . ' - ' . ($material->size ?? '')),
                    'quantity' => $quantity,
                    'unit' => $material->unit,
                    'estimated_price' => $estimatedPrice,
                    'notes' => $item['notes'] ?? null,
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
                $workOrderIdInItem = $item['work_order_id'] ?? $workOrderId;

                $request->items()->create([
                    'material_id' => $material->id,
                    'work_order_id' => $workOrderIdInItem,
                    'material_name' => $material->name,
                    'specification' => trim(($material->sub_category ?? '') . ' - ' . ($material->size ?? '')),
                    'quantity' => $shortage,
                    'unit' => $material->unit,
                    'estimated_price' => $estimatedPrice,
                    'notes' => $item['notes'] ?? "Kekurangan stok: {$shortage} {$material->unit}. Stok tersedia: " . ($item['available_stock'] ?? 0),
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
    /**
     * Log a material transaction for audit trail
     */
    public function logTransaction(Material $material, string $type, int $quantity, ?string $refType = null, ?int $refId = null, ?string $notes = null): MaterialTransaction
    {
        return \App\Models\MaterialTransaction::create([
            'material_id' => $material->id,
            'type' => strtoupper($type),
            'quantity' => $quantity,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'user_id' => Auth::id() ?? 1, // Default to system user if no auth
            'notes' => $notes,
        ]);
    }

    /**
     * Physically deduct stock and log transaction
     */
    public function deductStock(Material $material, int $quantity, ?int $workOrderId = null, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($material, $quantity, $workOrderId, $notes) {
            if ($material->stock < $quantity) {
                return false; // Not enough stock
            }

            // 1. Decrement stock
            $material->decrement('stock', $quantity);

            // 2. Log transaction
            $this->logTransaction(
                $material,
                'OUT',
                $quantity,
                $workOrderId ? 'WorkOrder' : null,
                $workOrderId,
                $notes ?? ($workOrderId ? "Penggunaan produksi untuk SPK #{$workOrderId}" : "Pengurangan manual")
            );

            // 3. Update WorkOrder pivot status if applicable
            if ($workOrderId) {
                DB::table('work_order_materials')
                    ->where('work_order_id', $workOrderId)
                    ->where('material_id', $material->id)
                    ->update(['status' => 'CONSUMED']);
            }

            return true;
        });
    }

    /**
     * Deduct all ALLOCATED materials for a WorkOrder
     */
    public function deductWorkOrderMaterials(WorkOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('materials');
            
            foreach ($order->materials as $material) {
                // Only deduct if status is ALLOCATED (meaning it wasn't consumed yet)
                if ($material->pivot->status === 'ALLOCATED') {
                    $this->deductStock(
                        $material, 
                        $material->pivot->quantity, 
                        $order->id, 
                        "Keluar untuk Produksi SPK #{$order->spk_number}"
                    );
                }
            }
        });
    }

    /**
     * Physically add stock (Restock) and log transaction
     */
    public function restock(Material $material, int $quantity, ?string $notes = null, ?string $refType = null, ?int $refId = null): MaterialTransaction
    {
        $transaction = DB::transaction(function () use ($material, $quantity, $notes, $refType, $refId) {
            // 1. Increment stock
            $material->increment('stock', $quantity);

            // 2. Log transaction
            return $this->logTransaction(
                $material,
                'IN',
                $quantity,
                $refType,
                $refId,
                $notes ?? "Restock barang baru"
            );
        });

        // 3. Trigger Auto-Allocation for waiting Work Orders
        $this->autoAllocateStock($material->id);

        return $transaction;
    }

    /**
     * Automatically allocate stock to Work Orders waiting for materials
     */
    public function autoAllocateStock(?int $materialId = null): void
    {
        // 1. Find all WorkOrder materials in REQUESTED status where stock is actually available
        $query = DB::table('work_order_materials')
            ->join('work_orders', 'work_order_materials.work_order_id', '=', 'work_orders.id')
            ->join('materials', 'work_order_materials.material_id', '=', 'materials.id')
            ->where('work_order_materials.status', 'REQUESTED')
            ->whereRaw('materials.stock >= work_order_materials.quantity'); // Only items we can fulfill

        if ($materialId) {
            $query->where('work_order_materials.material_id', $materialId);
        }

        // 2. Prioritize: Priority (Express/Urgent/Prioritas) > Date (FIFO)
        $query->orderByRaw("CASE 
            WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 1 
            ELSE 2 
        END ASC, work_orders.created_at ASC");

        $waitingItems = $query->select(
            'work_order_materials.*', 
            'work_orders.spk_number', 
            'work_orders.status as wo_status'
        )->get();

        foreach ($waitingItems as $item) {
            $material = Material::find($item->material_id);
            if (!$material) continue;

            $quantityNeeded = $item->quantity;

            if ($material->stock >= $quantityNeeded) {
                DB::transaction(function () use ($material, $item, $quantityNeeded) {
                    // Lock for update to be safe
                    $freshMaterial = Material::where('id', $material->id)->lockForUpdate()->first();
                    
                    if ($freshMaterial->stock >= $quantityNeeded) {
                        // A. Log transaction
                        $this->logTransaction(
                            $freshMaterial,
                            'OUT',
                            $quantityNeeded,
                            'WorkOrder',
                            $item->work_order_id,
                            "Otomatis alokasi dari penambahan stok (SPK #{$item->spk_number})"
                        );

                        // B. Decrement stock
                        $freshMaterial->decrement('stock', $quantityNeeded);

                        // C. Update pivot status
                        DB::table('work_order_materials')
                            ->where('work_order_id', $item->work_order_id)
                            ->where('material_id', $item->material_id)
                            ->update(['status' => 'ALLOCATED']);

                        // D. Log to WorkOrder history
                        $order = WorkOrder::find($item->work_order_id);
                        if ($order) {
                            $order->logs()->create([
                                'step' => $order->status->value,
                                'action' => 'MATERIAL_ALLOCATED',
                                'user_id' => 1, // System
                                'description' => "Material {$freshMaterial->name} ({$quantityNeeded} {$freshMaterial->unit}) otomatis dialokasikan karena stok tersedia."
                            ]);
                        }
                    }
                });
            }
        }
    }

    /**
     * Evaluate if a WorkOrder has all materials available
     */
    public function evaluateWorkOrderAvailability(WorkOrder $order): array
    {
        $materials = $order->materials()
            ->wherePivot('status', 'REQUESTED')
            ->get();

        $ready = true;
        $missing = [];

        foreach ($materials as $material) {
            $required = $material->pivot->quantity;
            if ($material->stock < $required) {
                $ready = false;
                $missing[] = [
                    'id' => $material->id,
                    'name' => $material->name,
                    'required' => $required,
                    'current_stock' => $material->stock,
                    'shortage' => $required - $material->stock
                ];
            }
        }

        return [
            'is_ready' => $ready,
            'missing_materials' => $missing,
        ];
    }

    /**
     * Identify missing materials for a WorkOrder and generate a MaterialRequest (PO)
     */
    public function requestMissingMaterialsForWorkOrder(WorkOrder $order, ?string $notes = null): ?MaterialRequest
    {
        return DB::transaction(function () use ($order, $notes) {
            // 1. Get all materials in REQUESTED status
            $missingItems = $order->materials()
                ->wherePivot('status', 'REQUESTED')
                ->get();

            if ($missingItems->isEmpty()) {
                return null;
            }

            // 2. Format for createProductionPO
            $formattedItems = $missingItems->map(function ($material) use ($order) {
                return [
                    'material' => $material,
                    'work_order_id' => $order->id,
                    'shortage' => $material->pivot->quantity,
                    'available_stock' => $material->stock,
                ];
            })->toArray();

            // 3. Create the PO
            return $this->createProductionPO($formattedItems, $order->id, null, $notes ?? "Request otomatis dari Sortir untuk SPK #{$order->spk_number}");
        });
    }
}
