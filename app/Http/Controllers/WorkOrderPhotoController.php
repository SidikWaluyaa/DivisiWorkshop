<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WorkOrderPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkOrderPhotoController extends Controller
{
    /**
     * Store standard (non-chunked) photo uploads
     */
    public function store(Request $request, $workOrderId)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|max:10240', // Max 10MB per file
            'step' => 'required|string',
            'caption' => 'nullable|string|max:255',
            'is_public' => 'boolean'
        ]);

        $order = WorkOrder::findOrFail($workOrderId);
        $uploadedPhotos = [];

        if ($request->hasFile('photos')) {
            // 1. Quota Check (Enterprise Standard: Max 50 photos)
            $currentCount = WorkOrderPhoto::where('work_order_id', $order->id)->count();
            $newPhotosCount = count($request->file('photos'));

            if (($currentCount + $newPhotosCount) > 50) {
                if (!$request->expectsJson()) {
                    return redirect()->back()->with('error', 'Batas maksimal 50 foto per SPK telah tercapai.');
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Batas maksimal 50 foto per SPK telah tercapai.'
                ], 422);
            }
            
            foreach ($request->file('photos') as $index => $file) {
                // 2. Concurrency-Safe Naming (Sequence + Random Hash)
                $sequence = $currentCount + $index + 1;
                $safeSpk = str_replace(['/', '\\', ' '], '-', $order->spk_number);
                $hash = bin2hex(random_bytes(2)); // 4 chars random
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$safeSpk}_{$sequence}_{$hash}.{$extension}";
                $relativePath = "photos/orders/{$order->id}/{$fileName}";
                
                // Save file
                $file->storeAs("photos/orders/{$order->id}", $fileName, 'public');

                // Determine Step
                $step = $request->step ?? 'assessment_' . time();
                
                // 3. Create DB Record
                $photo = WorkOrderPhoto::create([
                    'work_order_id' => $order->id,
                    'step' => $step,
                    'file_path' => $relativePath,
                    'caption' => $request->caption,
                    'is_public' => $request->boolean('is_public', true),
                    'user_id' => Auth::id(),
                ]);

                $uploadedPhotos[] = $photo;
            }

            // Redirect back if standard request (not AJAX)
            if (!$request->expectsJson()) {
                 return redirect()->back()->with('success', count($uploadedPhotos) . ' foto berhasil diupload!');
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos) . ' foto berhasil diupload.',
                'photos' => $uploadedPhotos
            ]);
        }

        if (!$request->expectsJson()) {
             return redirect()->back()->with('error', 'Tidak ada file yang dipilih.');
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    /**
     * Process a single photo (Compress & Resize) - Sequential Flow
     */
    public function process($id)
    {
        // Emergency Memory & Time Boost for Large Photos
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        
        // Force garbage collection to free up memory from previous processes
        gc_collect_cycles();
        
        $debugFile = storage_path('logs/photo_debug.log');
        file_put_contents($debugFile, "[" . date('Y-m-d H:i:s') . "] Starting process for ID: {$id}\n", FILE_APPEND);

        try {
            $photo = WorkOrderPhoto::findOrFail($id);
            $oldSize = Storage::disk('public')->exists($photo->file_path) ? Storage::disk('public')->size($photo->file_path) : 0;
            
            file_put_contents($debugFile, "[" . date('Y-m-d H:i:s') . "] Found photo: {$photo->file_path} (Size: {$oldSize})\n", FILE_APPEND);

            // Ensure no unexpected output disrupts JSON response
            if (ob_get_level()) ob_clean();

            // Dispatch the job synchronously for on-demand compression
            \App\Jobs\ProcessPhotoJob::dispatchSync($photo->id, [
                'watermark' => false,
                'quality' => 75,
                'max_width' => 1600
            ]);

            $photo->refresh();
            $newSize = Storage::disk('public')->exists($photo->file_path) ? Storage::disk('public')->size($photo->file_path) : 0;

            file_put_contents($debugFile, "[" . date('Y-m-d H:i:s') . "] SUCCESS: ID {$id} | Before: {$oldSize} | After: {$newSize}\n", FILE_APPEND);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dikompres.',
                'original_size' => $oldSize,
                'final_size' => $newSize,
                'path' => Storage::url($photo->file_path)
            ]);

        } catch (\Exception $e) {
            $err = "FAILED [ID: {$id}]: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
            file_put_contents($debugFile, "[" . date('Y-m-d H:i:s') . "] {$err}\n", FILE_APPEND);
            
            Log::error("WorkOrderPhotoController@process " . $err);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengompres foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->ids;
            if (empty($ids) || !is_array($ids)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada foto yang dipilih.'], 400);
            }

            $photos = WorkOrderPhoto::whereIn('id', $ids)->get();
            $deletedCount = 0;

            foreach ($photos as $photo) {
                /** @var \App\Models\WorkOrderPhoto $photo */
                // Delete file permanently from disk
                if (Storage::disk('public')->exists($photo->file_path)) {
                    Storage::disk('public')->delete($photo->file_path);
                }
                $photo->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' foto berhasil dihapus secara permanen.'
            ]);
        } catch (\Exception $e) {
            Log::error("WorkOrderPhotoController@bulkDestroy Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus foto massal.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $photo = WorkOrderPhoto::findOrFail($id);

            // Delete file
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }

            $photo->delete();

            return response()->json(['success' => true, 'message' => 'Foto dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus foto.'], 500);
        }
    }

    public function setAsCover($id)
    {
        try {
            $photo = WorkOrderPhoto::findOrFail($id);
            
            // 1. Reset all covers for this work order
            WorkOrderPhoto::where('work_order_id', $photo->work_order_id)
                ->update(['is_spk_cover' => false]);

            // 2. Set this one as cover
            $photo->update(['is_spk_cover' => true]);

            return response()->json([
                'success' => true, 
                'message' => 'Foto telah diatur sebagai cover SPK.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengatur cover.'], 500);
        }
    }
}
