<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManualStorageItem;
use App\Models\StorageRack;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ManualStorageController extends Controller
{

    /**
     * Display dashboard of Manual Storage
     */
    public function index(Request $request)
    {
        $query = ManualStorageItem::where('status', 'stored');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('rack_code', 'LIKE', "%{$search}%");
            });
        }

        // Rack Filter
        if ($request->filled('rack_code')) {
            $query->where('rack_code', $request->rack_code);
        }

        $items = $query->with(['storer', 'rack'])->orderBy('in_date', 'desc')->paginate(20);
        
        // Racks for filter
        $racks = StorageRack::where('status', \App\Enums\RackStatus::ACTIVE)->orderBy('rack_code')->get();

        // Calculate Summary stats for Dashboard Header
        $totalItems = ManualStorageItem::where('status', 'stored')->sum('quantity');
        $todayIn = ManualStorageItem::whereDate('in_date', now())->count();
        $todayOut = ManualStorageItem::whereDate('out_date', now())->count();

        return view('manual-storage.index', compact('items', 'racks', 'totalItems', 'todayIn', 'todayOut'));
    }

    /**
     * Show form to add new item
     */
    public function create()
    {
        // Only show Manual Racks matching M- prefixed racks
        $racks = StorageRack::manual()
            ->where('status', \App\Enums\RackStatus::ACTIVE)
            ->orderBy('rack_code')
            ->get();
            
        return view('manual-storage.create', compact('racks'));
    }

    /**
     * Store new item
     */
    public function store(Request $request)
    {
        $request->validate([
            'spk_number' => 'required|string|max:255',
            'payment_status' => 'required|in:lunas,tagih_nanti,tagih_lunas,manual',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'item_name' => 'required|string|max:255',
            'rack_code' => 'required',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240', // 10MB
        ]);

        try {
            DB::beginTransaction();

            // Check rack capacity and Category Match
            $rack = StorageRack::where('rack_code', $request->rack_code)->firstOrFail();
            
            // Validate Rack Category against Payment Status
            $expectedCategory = match($request->payment_status) {
                'tagih_lunas' => \App\Enums\StorageCategory::MANUAL_TL,
                'tagih_nanti' => \App\Enums\StorageCategory::MANUAL_TN,
                'lunas' => \App\Enums\StorageCategory::MANUAL_L,
                'manual' => \App\Enums\StorageCategory::MANUAL,
                default => \App\Enums\StorageCategory::MANUAL,
            };

            // Allow General Manual racks as fallback? No, strict mode per plan.
            // But if the rack is general 'manual', maybe allow it?
            // "If tagih_lunas, only can choose manual_tl".
            // Let's enforce strictness for the new categories.
            if ($rack->category !== $expectedCategory && $rack->category !== \App\Enums\StorageCategory::MANUAL) {
                 return redirect()->back()->with('error', "Salah Rak! Status '{$request->payment_status}' harus masuk ke rak kategori " . $expectedCategory->label())->withInput();
            }

            if ($rack->current_count >= $rack->capacity) {
                return redirect()->back()->with('error', 'Rak ' . $request->rack_code . ' sudah penuh!')->withInput();
            }

            $imagePath = null;
            if ($request->hasFile('photo')) {
                $imagePath = $this->uploadPhoto($request->file('photo'));
            }

            ManualStorageItem::create([
                'spk_number' => $request->spk_number,
                'payment_status' => $request->payment_status,
                'total_price' => $request->total_price,
                'paid_amount' => $request->paid_amount,
                'item_name' => $request->item_name,
                'rack_code' => $request->rack_code,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'image_path' => $imagePath,
                'in_date' => now(),
                'stored_by' => Auth::id(),
                'status' => 'stored',
            ]);

            // Increment rack count
            $rack->increment('current_count');
            
            DB::commit();

            return redirect()->route('storage.manual.index')->with('success', 'Item berhasil disimpan di ' . $request->rack_code . ' (' . $request->payment_status . ')');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show item detail
     */
    public function show(string $id)
    {
        $item = ManualStorageItem::with(['storer', 'retriever', 'rack'])->findOrFail($id);
        return view('manual-storage.show', compact('item'));
    }

    /**
     * Edit item (only if stored)
     */
    public function edit(string $id)
    {
        $item = ManualStorageItem::findOrFail($id);
        if ($item->status !== 'stored') {
            return redirect()->route('storage.manual.index')->with('error', 'Item sudah diambil, tidak bisa diedit.');
        }

        // Only show Manual Racks
        $racks = StorageRack::manual()->where('status', \App\Enums\RackStatus::ACTIVE)->orderBy('rack_code')->get();
        return view('manual-storage.edit', compact('item', 'racks'));
    }

    /**
     * Update item details
     */
    public function update(Request $request, string $id)
    {
        $item = ManualStorageItem::findOrFail($id);
        
        $request->validate([
            'spk_number' => 'required|string|max:255',
            'payment_status' => 'required|in:lunas,tagih_nanti,tagih_lunas,manual',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'item_name' => 'required|string|max:255',
            'rack_code' => 'required',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240', 
        ]);

        try {
            DB::beginTransaction();

            $oldRackCode = $item->rack_code;
            $newRackCode = $request->rack_code;

            // Update Rack Count if rack changed
            if ($oldRackCode !== $newRackCode) {
                $newRack = StorageRack::where('rack_code', $newRackCode)->firstOrFail();
                
                 // Validate Rack Category against Payment Status
                $expectedCategory = match($request->payment_status) {
                    'tagih_lunas' => \App\Enums\StorageCategory::MANUAL_TL,
                    'tagih_nanti' => \App\Enums\StorageCategory::MANUAL_TN,
                    'lunas' => \App\Enums\StorageCategory::MANUAL_L,
                    default => \App\Enums\StorageCategory::MANUAL,
                };
                
                 if ($newRack->category !== $expectedCategory && $newRack->category !== \App\Enums\StorageCategory::MANUAL) {
                     return redirect()->back()->with('error', "Salah Rak! Status '{$request->payment_status}' harus masuk ke rak kategori " . $expectedCategory->label());
                }

                if ($newRack->current_count >= $newRack->capacity) {
                     return redirect()->back()->with('error', 'Rak tujuan ' . $newRackCode . ' sudah penuh!');
                }

                // Decrement old rack
                $oldRack = StorageRack::where('rack_code', $oldRackCode)->first();
                if ($oldRack) {
                    $oldRack->decrement('current_count');
                }

                // Increment new rack
                $newRack->increment('current_count');
            }

            $data = [
                'spk_number' => $request->spk_number,
                'payment_status' => $request->payment_status,
                'total_price' => $request->total_price,
                'paid_amount' => $request->paid_amount,
                'item_name' => $request->item_name,
                'rack_code' => $request->rack_code,
                'quantity' => $request->quantity,
                'description' => $request->description,
            ];

            if ($request->hasFile('photo')) {
                 // Delete old photo if exists
                 if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                     Storage::disk('public')->delete($item->image_path);
                 }
                 $data['image_path'] = $this->uploadPhoto($request->file('photo'));
            }

            $item->update($data);
            
            DB::commit();

            return redirect()->route('storage.manual.index')->with('success', 'Item berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Release / Take Out Item
     */
    public function release(string $id)
    {
        $item = ManualStorageItem::findOrFail($id);
        
        if ($item->status !== 'stored') {
            return redirect()->back()->with('error', 'Item sudah dikeluarkan sebelumnya.');
        }

        // [SECURITY] Prevent release if not PAID (Lunas)
        if ($item->payment_status !== 'lunas') {
            return redirect()->back()->with('error', 'GAGAL: Barang belum LUNAS. Harap update status pembayaran menjadi LUNAS di halaman Edit sebelum mengeluarkan barang.');
        }

        try {
            DB::beginTransaction();

            $item->update([
                'status' => 'retrieved',
                'out_date' => now(),
                'retrieved_by' => Auth::id(),
            ]);

            // Decrement rack count
            $rack = StorageRack::where('rack_code', $item->rack_code)->first();
            if ($rack) {
                $rack->decrementCount();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Item berhasil dikeluarkan (Release).');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal release: ' . $e->getMessage());
        }
    }
    
    /**
     * History (Retrieved Items)
     */
    public function history(Request $request)
    {
        $query = ManualStorageItem::where('status', 'retrieved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('item_name', 'LIKE', "%{$search}%");
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('out_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
             $query->whereDate('out_date', '<=', $request->date_to);
        }

        $items = $query->with(['storer', 'retriever', 'rack'])
            ->orderBy('out_date', 'desc')
            ->paginate(20);

        return view('manual-storage.history', compact('items'));
    }

    /**
     * Delete permanently
     */
    public function destroy(string $id)
    {
        $item = ManualStorageItem::findOrFail($id);
        
        try {
            DB::beginTransaction();

            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            
            // If item was still stored, decrement rack count
            if ($item->status === 'stored') {
                $rack = StorageRack::where('rack_code', $item->rack_code)->first();
                if ($rack) {
                    $rack->decrementCount();
                }
            }
            
            $item->delete();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Item berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Destroy
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:manual_storage_items,id',
        ]);

        $count = 0;
        $items = ManualStorageItem::whereIn('id', $request->ids)->get();

        DB::transaction(function() use ($items, &$count) {
            foreach ($items as $item) {
                // Handle Rack Count if somehow deleting 'stored' item
                if ($item->status === 'stored') {
                    $rack = StorageRack::where('rack_code', $item->rack_code)->first();
                    if ($rack) {
                        $rack->decrementCount();
                    }
                }

                // Delete image
                if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                    Storage::disk('public')->delete($item->image_path);
                }

                $item->delete();
                $count++;
            }
        });

        return back()->with('success', "$count item berhasil dihapus.");
    }

    /**
     * Helper: Upload Photo
     */
    private function uploadPhoto($file)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        
        // Resize max width 1000px
        if ($image->width() > 1000) {
            $image->scale(width: 1000);
        }

        $filename = 'manual_' . time() . '_' . uniqid() . '.jpg';
        $path = 'photos/manual-storage/' . $filename;
        
        // Encode to jpg 80% quality
        $encoded = $image->toJpeg(80);
        
        Storage::disk('public')->put($path, $encoded);
        
        return $path;
    }
}
