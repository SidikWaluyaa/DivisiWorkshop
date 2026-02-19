<?php

namespace App\Http\Controllers;

use App\Models\StorageRack;
use App\Models\ManualStorageItem;
use Illuminate\Http\Request;
use App\Enums\StorageCategory;
use App\Enums\RackStatus;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ManualRackController extends Controller
{
    /**
     * Display a listing of Manual Racks.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $racks = StorageRack::manual()
            ->when($search, function ($query, $search) {
                return $query->where('rack_code', 'like', "%{$search}%")
                             ->orWhere('location', 'like', "%{$search}%");
            })
            ->orderBy('rack_code')
            ->paginate(20)
            ->withQueryString();

        return view('manual-storage.racks.index', compact('racks'));
    }

    /**
     * Store a newly created manual rack.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rack_code' => 'required|string|max:20|unique:storage_racks,rack_code',
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'category' => 'required|in:manual,manual_tl,manual_tn,manual_l',
            'notes' => 'nullable|string',
        ]);

        StorageRack::create([
            'rack_code' => strtoupper($request->rack_code),
            'location' => $request->location,
            'capacity' => $request->capacity,
            'category' => $request->category,
            'current_count' => 0,
            'status' => RackStatus::ACTIVE,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Rak Manual berhasil ditambahkan dalam kategori ' . $request->category);
    }

    /**
     * Update the specified manual rack.
     */
    public function update(Request $request, $id)
    {
        $rack = StorageRack::manual()->findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:' . $rack->current_count,
            'category' => 'required|in:manual,manual_tl,manual_tn,manual_l',
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

        return back()->with('success', 'Rak Manual berhasil diperbarui.');
    }

    /**
     * Remove the specified manual rack.
     */
    public function destroy($id)
    {
        $rack = StorageRack::manual()->findOrFail($id);

        if ($rack->current_count > 0) {
            return back()->with('error', 'Gagal: Rak masih berisi item. Kosongkan dulu.');
        }

        // Hard delete is fine for manual racks as they are standalone, 
        // but looking at the model they use SoftDeletes. 
        // We'll use delete() which triggers soft delete.
        $rack->delete();

        return back()->with('success', 'Rak Manual berhasil dihapus.');
    }

    /**
     * Sync manual rack counts.
     */
    public function sync()
    {
        $racks = StorageRack::manual()->get();
        $updated = 0;

        foreach ($racks as $rack) {
            $realCount = ManualStorageItem::where('rack_code', $rack->rack_code)
                ->where('status', 'stored')
                ->count();
            
            if ($rack->current_count !== $realCount) {
                $rack->update(['current_count' => $realCount]);
                $updated++;
            }
        }

        return back()->with('success', "Sinkronisasi selesai. $updated rak diperbarui.");
    }

    /**
     * Print PDF Manifest for a rack.
     */
    public function printPDF($id)
    {
        $rack = StorageRack::manual()->findOrFail($id);
        
        $items = ManualStorageItem::where('rack_code', $rack->rack_code)
            ->where('status', 'stored')
            ->with(['storer'])
            ->orderBy('in_date', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.rack_manifest', compact('rack', 'items'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("Manifest-{$rack->rack_code}.pdf");
    }
}
