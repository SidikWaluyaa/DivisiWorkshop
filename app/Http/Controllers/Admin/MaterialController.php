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

        $queryUpper = Material::with('pic')->where('type', 'Material Upper');
        $querySol = Material::with('pic')->where('type', 'Material Sol');

        if ($search) {
            $searchLogic = function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sub_category', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('pic', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            };
            $queryUpper->where($searchLogic);
            $querySol->where($searchLogic);
        }

        // Using get() instead of paginate because we are using client-side tabs.
        // If data grows large, we should implement server-side tabs (query param ?tab=sol).
        // For now, ~200 items is safe for get().
        $upperMaterials = $queryUpper->latest()->get();
        $solMaterials = $querySol->latest()->get();
        $pics = User::where('role', 'pic')->get();
        
        // Merge for edit modal loop
        $materials = $upperMaterials->merge($solMaterials);

        return view('admin.materials.index', compact('materials', 'upperMaterials', 'solMaterials', 'pics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Material Sol,Material Upper',
            'sub_category' => 'nullable|string|in:Sol Potong,Sol Jadi,Foxing,Vibram',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        Material::create($validated);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil ditambahkan.');
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Material Sol,Material Upper',
            'sub_category' => 'nullable|string|in:Sol Potong,Sol Jadi,Foxing,Vibram',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        $material->update($validated);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:materials,id',
        ]);

        Material::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.materials.index')->with('success', count($request->ids) . ' material berhasil dihapus.');
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
