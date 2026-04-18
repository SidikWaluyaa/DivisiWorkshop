<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $subCategory = $request->sub_category;
        $activeTab = $request->get('tab', 'upper');

        $queryUpper = Material::with('pic')->where('type', 'Material Upper');
        $querySol = Material::with('pic')->where('type', 'Material Sol');

        $queryUpper = $this->applyFilters($queryUpper, $request);
        $querySol = $this->applyFilters($querySol, $request);

        if ($subCategory && $subCategory !== 'all') {
            $querySol->where('sub_category', $subCategory);
        }

        $upperMaterials = $queryUpper->latest()->paginate(10, ['*'], 'upper_page')->withQueryString();
        $solMaterials = $querySol->latest()->paginate(10, ['*'], 'sol_page')->withQueryString();
        
        $pics = User::whereIn('role', ['admin', 'pic', 'gudang'])->get();
        
        // Count for the badge
        $totalCount = Material::count();

        // For modals, we still need the edit objects. 
        $materials = $upperMaterials->getCollection()->merge($solMaterials->getCollection());

        return view('admin.materials.index', compact('materials', 'upperMaterials', 'solMaterials', 'pics', 'totalCount', 'activeTab'));
    }

    protected function applyFilters($query, Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sub_category', 'like', "%{$search}%")
                  ->orWhereHas('pic', function($picQ) use ($search) {
                      $picQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Material Sol,Material Upper',
            'category' => 'required|string|in:SHOPPING,PRODUCTION',
            'sub_category' => 'nullable|string|in:Sol Potong,Sol Jadi,Foxing,Vibram',
            'size' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        $material = Material::create($validated);

        // Trigger Auto-Allocation for waiting Work Orders
        app(\App\Services\MaterialManagementService::class)->autoAllocateStock($material->id);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil ditambahkan.');
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Material Sol,Material Upper',
            'category' => 'required|string|in:SHOPPING,PRODUCTION',
            'sub_category' => 'nullable|string|in:Sol Potong,Sol Jadi,Foxing,Vibram',
            'size' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        $material->update($validated);

        // Trigger Auto-Allocation for waiting Work Orders
        app(\App\Services\MaterialManagementService::class)->autoAllocateStock($material->id);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function reconcile(Request $request, Material $material)
    {
        $validated = $request->validate([
            'physical_stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        app(\App\Services\MaterialManagementService::class)->adjustStock(
            $material,
            $validated['physical_stock'],
            $validated['reason'],
            $validated['notes']
        );

        return redirect()->route('admin.materials.index')->with('success', "Audit stok untuk {$material->name} berhasil disimpan.");
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        if ($request->boolean('select_all_matching')) {
            $activeTab = $request->get('tab', 'upper');
            $type = $activeTab === 'upper' ? 'Material Upper' : 'Material Sol';
            
            $query = Material::where('type', $type);
            $query = $this->applyFilters($query, $request);

            if ($request->sub_category && $request->sub_category !== 'all' && $activeTab === 'sol') {
                $query->where('sub_category', $request->sub_category);
            }

            $count = $query->count();
            $query->delete();

            return redirect()->route('admin.materials.index', $request->query())->with('success', "{$count} material hasil filter berhasil dihapus.");
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:materials,id',
        ]);

        Material::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.materials.index', $request->query())->with('success', count($request->ids) . ' material berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MaterialsImport, $request->file('file'));
            return redirect()->route('admin.materials.index')->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $materials = Material::orderBy('type')->orderBy('name')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.materials.pdf', compact('materials'));
        
        return $pdf->download('laporan-stok-workshop-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MaterialsExport, 'laporan-stok-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MaterialTemplateExport, 'template_import_material.xlsx');
    }
}
