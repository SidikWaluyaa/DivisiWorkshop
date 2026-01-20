<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialReservation;
use App\Models\OTO;

class MaterialReservationService
{
    /**
     * Soft reserve materials for OTO
     */
    public function softReserveForOTO(OTO $oto): array
    {
        $warnings = [];
        
        foreach ($oto->proposed_services as $serviceData) {
            $materials = $this->getRequiredMaterials($serviceData['service_id']);
            
            foreach ($materials as $materialData) {
                $material = Material::find($materialData['material_id']);
                if (!$material) continue;

                $requiredQty = $materialData['quantity'];
                $availableStock = $material->stock - $material->reserved_stock;
                
                if ($availableStock >= $requiredQty) {
                    MaterialReservation::create([
                        'material_id' => $material->id,
                        'oto_id' => $oto->id,
                        'work_order_id' => $oto->work_order_id,
                        'quantity' => $requiredQty,
                        'type' => 'SOFT',
                        'status' => 'ACTIVE',
                        'expires_at' => $oto->valid_until,
                    ]);
                    
                    $material->increment('reserved_stock', $requiredQty);
                } else {
                    $warnings[] = "Material {$material->name} stock rendah! Available: {$availableStock}, Required: {$requiredQty}";
                }
            }
        }
        
        if (count($warnings) == 0) {
            $oto->update(['materials_reserved' => true]);
        }
        
        return $warnings;
    }
    
    /**
     * Auto-release expired soft reservations
     */
    public function releaseExpiredReservations(): int
    {
        $expired = MaterialReservation::expired()->get();
        $count = 0;
        
        foreach ($expired as $reservation) {
            $reservation->release();
            $count++;
        }
        
        return $count;
    }
    
    private function getRequiredMaterials($serviceId)
    {
        // Placeholder for service-material mapping
        // In real implementation, this should query a pivot table
        
        // Example logic:
        // Service 1 (Deep Clean) needs Material 1 (Cleaner) x 10ml
        // For now returning empty array to prevent error since mapping doesn't exist yet
        return []; 
    }
}
