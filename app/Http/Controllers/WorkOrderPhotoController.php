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
