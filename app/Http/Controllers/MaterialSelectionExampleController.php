<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Services\MaterialManagementService;
use Illuminate\Http\Request;

/**
 * Example controller showing how to use MaterialManagementService
 * in production workflows (Sortir, Production, OTO, etc.)
 */
class MaterialSelectionExampleController extends Controller
{
    protected $materialService;

    public function __construct(MaterialManagementService $materialService)
    {
        $this->materialService = $materialService;
    }

    /**
     * Example: Process material selection for a Work Order
     * 
     * This can be used in Sortir, Production, or any workflow
     * where materials need to be selected and checked
     */
    public function processMaterialSelection(Request $request)
    {
        $validated = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.notes' => 'nullable|string',
        ]);

        $workOrderId = $validated['work_order_id'];
        $materials = $validated['materials'];

        // Process the complete material workflow
        $result = $this->materialService->processCompleteMaterialWorkflow(
            $materials,
            $workOrderId,
            null, // oto_id (optional)
            'Material untuk Work Order #' . $workOrderId
        );

        // Result structure:
        // [
        //     'available' => [...],      // Materials ready to use
        //     'shopping' => [...],       // Shopping materials (budget requests created)
        //     'unavailable' => [...],    // Production materials out of stock
        //     'shopping_request' => MaterialRequest|null,
        //     'reservations' => [...]    // Created reservations
        // ]

        // Handle the results
        if (!empty($result['unavailable'])) {
            // Show modal/page for user to choose: PO or Followup
            return response()->json([
                'status' => 'needs_action',
                'message' => 'Beberapa material produksi tidak tersedia',
                'data' => $result
            ]);
        }

        if (!empty($result['shopping'])) {
            // Shopping request created automatically
            $requestNumber = $result['shopping_request']->request_number ?? 'N/A';
            
            return response()->json([
                'status' => 'partial_success',
                'message' => "Material produksi tersedia. Budget request dibuat: {$requestNumber}",
                'data' => $result
            ]);
        }

        // All materials available
        return response()->json([
            'status' => 'success',
            'message' => 'Semua material tersedia dan telah direserve',
            'data' => $result
        ]);
    }

    /**
     * Example: Create PO for unavailable materials
     */
    public function createPOForUnavailable(Request $request)
    {
        $validated = $request->validate([
            'work_order_id' => 'nullable|exists:work_orders,id',
            'oto_id' => 'nullable|exists:otos,id',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.estimated_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $po = $this->materialService->createProductionPO(
            $validated['materials'],
            $validated['work_order_id'] ?? null,
            $validated['oto_id'] ?? null,
            $validated['notes'] ?? null
        );

        return response()->json([
            'status' => 'success',
            'message' => "Purchase Order berhasil dibuat: {$po->request_number}",
            'data' => $po
        ]);
    }

    /**
     * Example: Get materials with category filter
     */
    public function getMaterialsForSelection(Request $request)
    {
        $query = Material::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Only active materials
        $query->where('status', 'Ready');

        $materials = $query->get()->map(function ($material) {
            return [
                'id' => $material->id,
                'name' => $material->name,
                'type' => $material->type,
                'category' => $material->category,
                'stock' => $material->stock,
                'available_stock' => $material->getAvailableStock(),
                'unit' => $material->unit,
                'price' => $material->price,
                'stock_status' => $material->getStockStatus(),
                'is_shopping' => $material->isShopping(),
                'is_production' => $material->isProduction(),
            ];
        });

        return response()->json($materials);
    }

    /**
     * Example: Check stock availability for specific materials
     */
    public function checkStockAvailability(Request $request)
    {
        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $result = $this->materialService->processMaterialSelection($validated['materials']);

        return response()->json([
            'available' => $result['available'],
            'shopping' => $result['shopping'],
            'unavailable' => $result['unavailable'],
            'summary' => [
                'total' => count($validated['materials']),
                'available_count' => count($result['available']),
                'shopping_count' => count($result['shopping']),
                'unavailable_count' => count($result['unavailable']),
            ]
        ]);
    }
}
