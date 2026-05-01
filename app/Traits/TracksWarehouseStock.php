<?php

namespace App\Traits;

use App\Models\Material;
use App\Models\MaterialTransaction;
use Illuminate\Support\Facades\DB;

trait TracksWarehouseStock
{
    /**
     * Log material transaction and update stock
     */
    public function recordStockTransaction(Material $material, int $quantity, string $type, string $referenceType, int $referenceId, string $notes = null)
    {
        return DB::transaction(function () use ($material, $quantity, $type, $referenceType, $referenceId, $notes) {
            // 1. Update Material Stock
            if ($type === 'IN') {
                $material->increment('stock', $quantity);
            } elseif ($type === 'OUT') {
                $material->decrement('stock', $quantity);
            }

            // 2. Create Transaction Log
            return MaterialTransaction::create([
                'material_id' => $material->id,
                'type' => $type,
                'quantity' => $quantity,
                'balance_after' => $material->stock, // Fresh stock after increment/decrement
                'unit_price' => $material->price,
                'total_value' => $material->price * $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => auth()->id() ?? 1, // Default to 1 if no auth (e.g. CLI)
                'notes' => $notes,
            ]);
        });
    }
}
