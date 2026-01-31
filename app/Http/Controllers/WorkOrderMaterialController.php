<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Material;
use App\Services\MaterialManagementService;
use Illuminate\Http\Request;

class WorkOrderMaterialController extends Controller
{
    protected MaterialManagementService $materialService;

    public function __construct(MaterialManagementService $materialService)
    {
        $this->materialService = $materialService;
    }

    /**
     * Show the material selection form for a specific Work Order.
     */
    public function create(WorkOrder $workOrder)
    {
        // Load relationships needed for context
        $workOrder->load(['customer', 'services', 'materials']);
        
        // Prepare existing materials for AlpineJS
        $existingMaterials = $workOrder->materials->map(function($mat) {
            return [
                'id' => $mat->id . '_' . time(), // Unique ID for Alpine
                'material_id' => $mat->id,
                'quantity' => $mat->pivot->quantity,
                'unit' => $mat->unit,
                'available_stock' => $mat->getAvailableStock() + ($mat->pivot->status === 'ALLOCATED' ? $mat->pivot->quantity : 0),
                'category' => $mat->category,
                'notes' => $mat->pivot->notes ?? ''
            ];
        });

        // Calculate Recommended Materials based on Services
        $serviceCategories = $workOrder->services->pluck('category')->unique()->toArray();
        $recommendedTypes = [];
        foreach ($serviceCategories as $cat) {
            if (stripos($cat, 'Sol') !== false) $recommendedTypes[] = 'Material Sol';
            if (stripos($cat, 'Upper') !== false || stripos($cat, 'Repaint') !== false) $recommendedTypes[] = 'Material Upper';
        }
        
        $recommendedMaterials = Material::whereIn('type', $recommendedTypes)
            ->where('status', 'Ready')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Fetch active materials for selection
        $materials = Material::where('status', 'Ready')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('materials.selection', compact('workOrder', 'materials', 'existingMaterials', 'recommendedMaterials'));
    }

    /**
     * Process the selected materials.
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $result = $this->materialService->processCompleteMaterialWorkflow(
                $validated['materials'],
                $workOrder->id,
                null, // oto_id (can be added later if needed)
                $request->input('notes')
            );

            // Construct feedback message
            $message = $result['message'];
            if (!$result['success']) {
                return redirect()->back()->with('error', 'Gagal memproses material: ' . $message);
            }

            return redirect()->route('materials.selection.create', $workOrder->id)
                ->with('success', 'Material berhasil diproses. ' . $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
