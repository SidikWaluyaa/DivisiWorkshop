<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->latest()->get();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $protectedNames = ['custom', 'custom service', 'custom services', 'lainnya', 'other'];

        if (in_array(strtolower($service->name), $protectedNames)) {
            return redirect()->back()->with('error', 'Layanan sistem ini tidak dapat dihapus.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:services,id',
        ]);

        $protectedNames = ['custom', 'custom service', 'custom services', 'lainnya', 'other'];
        
        $services = Service::whereIn('id', $request->ids)->get();
        
        $deletableIds = [];
        $protectedCount = 0;
        
        foreach($services as $service) {
             if (in_array(strtolower($service->name), $protectedNames)) {
                 $protectedCount++;
                 continue;
             }
             $deletableIds[] = $service->id;
        }

        if (count($deletableIds) > 0) {
             Service::whereIn('id', $deletableIds)->delete();
        }

        $msg = count($deletableIds) . ' layanan berhasil dihapus.';
        if ($protectedCount > 0) {
             $msg .= " ($protectedCount layanan diproteksi dan dibatalkan)";
        }

        return redirect()->route('admin.services.index')->with('success', $msg);
    }
    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ServicesExport, 'data-layanan-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ServiceTemplateExport, 'template_import_layanan.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ServicesImport, $request->file('file'));
            return redirect()->route('admin.services.index')->with('success', 'Data layanan berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
