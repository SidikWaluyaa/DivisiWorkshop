<?php

namespace App\Http\Controllers;

use App\Models\StorageRack;
use Illuminate\Http\Request;

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
            if (!in_array($category, ['shoes', 'accessories'])) {
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
            ->withCount('assignments as stored_items_count') // Count items physically stored
            ->orderBy('rack_code')
            ->paginate(20)
            ->withQueryString(); // Preserve query parameters in pagination

        return view('storage.racks.index', compact('racks', 'category'));
    }

    /**
     * Store a newly created rack in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rack_code' => 'required|string|unique:storage_racks,rack_code|max:20',
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'category' => 'required|in:shoes,accessories',
            'notes' => 'nullable|string',
        ]);

        StorageRack::create([
            'rack_code' => strtoupper($request->rack_code),
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
            'category' => 'required|in:shoes,accessories',
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
}
