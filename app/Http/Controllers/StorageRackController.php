<?php

namespace App\Http\Controllers;

use App\Models\StorageRack;
use App\Models\StorageAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageRackController extends Controller
{
    /**
     * Display a listing of the racks.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Handle Category Persistence
        if ($request->has('category')) {
            $category = $request->input('category');
            if (!in_array($category, ['shoes', 'accessories', 'before'])) {
                 $category = 'shoes';
            }
            session(['storage_category' => $category]);
        } else {
            $category = session('storage_category', 'shoes');
        }
        
        $racks = StorageRack::where('category', $category)
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                     $q->where('rack_code', 'like', "%{$search}%")
                       ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy('rack_code')
            ->paginate(20)
            ->withQueryString(); // Preserve query parameters in pagination

        return view('storage.racks.index', compact('racks', 'category'));
    }

    /**
     * Display the specified rack (Redirect to index with search).
     */
    public function show($id)
    {
        $rack = StorageRack::findOrFail($id);
        
        // Redirect to index with the rack code as search query to "show" it in context
        return redirect()->route('storage.racks.index', [
            'search' => $rack->rack_code,
            'category' => $rack->category
        ]);
    }

    /**
     * Store a newly created rack in storage.
     */
    public function store(Request $request)
    {
        $rackCode = strtoupper($request->rack_code);

        // Manual check for unique within category to give better error message with SoftDeletes
        $existing = StorageRack::withTrashed()
            ->where('rack_code', $rackCode)
            ->where('category', $request->category)
            ->first();

        if ($existing) {
            $catLabel = $request->category === 'shoes' ? 'Sepatu' : ($request->category === 'accessories' ? 'Aksesoris' : 'Inbound');
            
            if ($existing->trashed()) {
                return back()->withInput()->withErrors([
                    'rack_code' => "Kode rak '$rackCode' sudah ada di Tempat Sampah (Kategori: $catLabel). Silakan pulihkan data dari sampah atau gunakan kode lain."
                ]);
            }
            return back()->withInput()->withErrors([
                'rack_code' => "Kode rak '$rackCode' sudah digunakan di Kategori $catLabel."
            ]);
        }

        $request->validate([
            'rack_code' => 'required|string|max:20',
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'category' => 'required|in:shoes,accessories,before',
            'notes' => 'nullable|string',
        ], [
            'rack_code.required' => 'Kode rak wajib diisi.',
            'location.required' => 'Lokasi rak wajib diisi.',
            'capacity.required' => 'Kapasitas wajib diisi.',
            'capacity.min' => 'Kapasitas minimal 1.',
            'category.required' => 'Kategori rak wajib dipilih.',
            'category.in' => 'Kategori rak tidak valid.',
        ]);

        StorageRack::create([
            'rack_code' => $rackCode,
            'location' => $request->location,
            'capacity' => $request->capacity,
            'category' => $request->category,
            'current_count' => 0,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Rak berhasil ditambahkan.');
    }

    /**
     * Update the specified rack in storage.
     */
    public function update(Request $request, $id)
    {
        $rack = StorageRack::findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:' . $rack->current_count, // Capacity cannot be less than current items
            'category' => 'required|in:shoes,accessories,before',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,maintenance,full',
        ]);

        $rack->update([
            'location' => $request->location,
            'capacity' => $request->capacity,
            'category' => $request->category,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Data rak berhasil diperbarui.');
    }

    /**
     * Remove the specified rack from storage.
     */
    public function destroy($id)
    {
        $rack = StorageRack::findOrFail($id);

        if ($rack->current_count > 0) {
            return back()->with('error', 'Gagal menghapus: Rak masih berisi ' . $rack->current_count . ' item. Silakan kosongkan terlebih dahulu.');
        }

        $rack->delete();

        return back()->with('success', 'Rak berhasil dihapus.');
    }

    /**
     * Display a list of trashed racks.
     */
    public function trash(Request $request)
    {
        $category = session('storage_category', 'shoes');
        $racks = StorageRack::onlyTrashed()
            ->where('category', $category)
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('storage.racks.trash', compact('racks', 'category'));
    }

    /**
     * Restore a trashed rack.
     */
    public function restore($id)
    {
        $rack = StorageRack::onlyTrashed()->findOrFail($id);
        $rack->restore();

        return redirect()->route('storage.racks.index')->with('success', 'Rak berhasil dikembalikan.');
    }

    /**
     * Permanently delete a rack and its assignments.
     */
    public function forceDelete($id)
    {
        $rack = StorageRack::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($rack) {
            // Delete all assignments related to this rack code to avoid FK constraints
            StorageAssignment::where('rack_code', $rack->rack_code)->delete();
            
            // Delete the rack permanently
            $rack->forceDelete();
        });

        return back()->with('success', 'Rak dan riwayat data berhasil dihapus permanen.');
    }
    /**
     * Synchronize rack counts with actual assignments.
     */
    public function sync()
    {
        $racks = StorageRack::all();
        $updatedCount = 0;

        foreach ($racks as $rack) {
            $actualCount = StorageAssignment::where('rack_code', $rack->rack_code)
                ->where('status', 'stored')
                ->whereHas('workOrder') // Ensure WorkOrder still exists
                ->count();
            
            if ($rack->current_count != $actualCount) {
                $rack->update(['current_count' => $actualCount]);
                $updatedCount++;
            }
        }

        return back()->with('success', "Sinkronisasi selesai. $updatedCount rak diperbarui.");
    }
}
