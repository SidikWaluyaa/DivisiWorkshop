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
        $type = $request->type;
        $subCategory = $request->sub_category;

        $query = Material::with('pic');

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($subCategory && $subCategory !== 'all') {
            $query->where('sub_category', $subCategory);
        }

        $query = $this->applyFilters($query, $request);

        $materials = $query->latest()->paginate(15)->withQueryString();
        
        $pics = User::whereIn('role', ['admin', 'pic', 'gudang'])->get();
        
        // Count for the badge
        $totalCount = Material::count();
        $trashCount = Material::onlyTrashed()->count();

        return view('admin.materials.index', compact('materials', 'pics', 'totalCount', 'trashCount'));
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
            'type' => 'required|string|max:255',
            'custom_type' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:SHOPPING,PRODUCTION',
            'sub_category' => 'nullable|string|in:Sol Potong,Sol Jadi,Foxing,Vibram',
            'size' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        if ($validated['type'] === 'other' && !empty($validated['custom_type'])) {
            $validated['type'] = $validated['custom_type'];
        }
        unset($validated['custom_type']);

        $material = Material::create($validated);

        // Trigger Auto-Allocation for waiting Work Orders
        app(\App\Services\MaterialManagementService::class)->autoAllocateStock($material->id);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil ditambahkan.');
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'custom_type' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:SHOPPING,PRODUCTION',
            'sub_category' => 'nullable|string|in:Sol Potong,Sol Jadi,Foxing,Vibram',
            'size' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        if ($validated['type'] === 'other' && !empty($validated['custom_type'])) {
            $validated['type'] = $validated['custom_type'];
        }
        unset($validated['custom_type']);

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
            $query = Material::query();

            if ($request->type && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->sub_category && $request->sub_category !== 'all') {
                $query->where('sub_category', $request->sub_category);
            }
            
            $query = $this->applyFilters($query, $request);

            $count = $query->count();
            $query->delete();

            return redirect()->route('admin.materials.index', $request->except(['select_all_matching', 'ids']))->with('success', "{$count} material hasil filter berhasil dihapus.");
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:materials,id',
        ]);

        Material::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.materials.index', $request->except('ids'))->with('success', count($request->ids) . ' material berhasil dihapus.');
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

    /**
     * Display a list of trashed materials.
     */
    public function trash(Request $request)
    {
        $materials = Material::onlyTrashed()->latest('deleted_at')->paginate(15);
        return view('admin.materials.trash', compact('materials'));
    }

    /**
     * Bulk restore trashed materials.
     */
    public function bulkRestore(Request $request)
    {
        if ($request->boolean('select_all_matching')) {
            $count = Material::onlyTrashed()->count();
            Material::onlyTrashed()->restore();
            return redirect()->route('admin.materials.index')->with('success', "{$count} material berhasil dikembalikan.");
        }

        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = Material::onlyTrashed()->whereIn('id', $request->ids)->restore();

        return redirect()->route('admin.materials.index')->with('success', "{$count} material berhasil dikembalikan.");
    }

    /**
     * Bulk permanently delete materials.
     */
    public function bulkForceDelete(Request $request)
    {
        if ($request->boolean('select_all_matching')) {
            $ids = Material::onlyTrashed()->pluck('id')->toArray();
        } else {
            $request->validate([
                'ids' => 'required|array',
            ]);
            $ids = $request->ids;
        }

        if (empty($ids)) {
            return redirect()->route('admin.materials.trash')->with('error', 'Tidak ada material terpilih untuk dihapus.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($ids) {
            \App\Models\MaterialReservation::whereIn('material_id', $ids)->delete();
            \App\Models\MaterialTransaction::whereIn('material_id', $ids)->delete();
            \App\Models\MaterialRequestItem::whereIn('material_id', $ids)->delete();
            \Illuminate\Support\Facades\DB::table('work_order_materials')->whereIn('material_id', $ids)->delete();
            
            Material::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        });

        return redirect()->route('admin.materials.trash')->with('success', count($ids) . ' material berhasil dihapus secara permanen.');
    }

    /**
     * Restore a trashed material.
     */
    public function restore($id)
    {
        $material = Material::onlyTrashed()->findOrFail($id);
        $material->restore();

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil dikembalikan.');
    }

    /**
     * Permanently delete a material.
     */
    public function forceDelete($id)
    {
        $material = Material::onlyTrashed()->findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($material) {
            $material->reservations()->delete();
            $material->transactions()->delete();
            $material->materialRequests()->delete();
            \Illuminate\Support\Facades\DB::table('work_order_materials')->where('material_id', $material->id)->delete();
            $material->forceDelete();
        });

        return redirect()->route('admin.materials.trash')->with('success', 'Material berhasil dihapus secara permanen.');
    }
}
