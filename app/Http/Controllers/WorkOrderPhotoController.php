<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WorkOrderPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class WorkOrderPhotoController extends Controller
{
    public function store(Request $request, $workOrderId)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // Max 5MB
            'step' => 'required|string',
            'caption' => 'nullable|string|max:255',
            'is_public' => 'boolean'
        ]);

        $order = WorkOrder::findOrFail($workOrderId);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Store in public/photos/orders/{id}
            $path = $file->store("photos/orders/{$order->id}", 'public');

            $photo = WorkOrderPhoto::create([
                'work_order_id' => $order->id,
                'step' => $request->step,
                'file_path' => $path,
                'caption' => $request->caption,
                'is_public' => $request->boolean('is_public', true), // Default true unless specified
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload.',
                'photo' => $photo,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    public function destroy($id)
    {
        $photo = WorkOrderPhoto::findOrFail($id);

        // Optional: Check permission (only uploader or admin)
        // if ($photo->user_id !== Auth::id()) { ... }

        // Delete file
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return response()->json(['success' => true, 'message' => 'Foto dihapus.']);
    }
}
