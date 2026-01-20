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
            'photo' => 'required|image|max:10240', // Max 10MB input, will be compressed
            'step' => 'required|string',
            'caption' => 'nullable|string|max:255',
            'is_public' => 'boolean'
        ]);

        $order = WorkOrder::findOrFail($workOrderId);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            // 1. Generate Filename
            $filename = 'photo_' . time() . '_' . uniqid() . '.webp';
            $relativePath = "photos/orders/{$order->id}/{$filename}";
            $absolutePath = storage_path("app/public/{$relativePath}");

            // Ensure directory exists
            $directory = dirname($absolutePath);
            if (!is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }

            // 2. Load Image (Native GD)
            $source = imagecreatefromstring(file_get_contents($file));
            
            if ($source !== false) {
                // 3. Fix Rotation (based on Exif if available - skip for now as simple implementation)
                
                // 4. Resize if too big
                $width = imagesx($source);
                $height = imagesy($source);
                $maxWidth = 1024; // Target width

                if ($width > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = floor($height * ($maxWidth / $width));
                    $destination = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($source);
                    $source = $destination;
                    $width = $newWidth;
                    $height = $newHeight;
                }

                // 5. Add Watermark (Logo + Text)
            // Text: Shoe Size instead of SPK Number
            $watermarkText = 'Size: ' . $order->shoe_size;
            
            // Add Text Shadow Bar (larger)
            $barHeight = 50;
            $barColor = imagecolorallocatealpha($source, 0, 0, 0, 80); // Black 80% transparent
            imagefilledrectangle($source, 0, $height - $barHeight, $width, $height, $barColor);
            
            // Add Text (larger font)
            $white = imagecolorallocate($source, 255, 255, 255);
            $fontSize = 5; // Larger built-in font size (max is 5)
            imagestring($source, $fontSize, 15, $height - 32, $watermarkText, $white);

            // Add Logo Watermark (MUCH LARGER)
            $logoPath = public_path('images/logo.png');
            if (file_exists($logoPath)) {
                $logo = imagecreatefrompng($logoPath);
                if ($logo) {
                    $logoWidth = imagesx($logo);
                    $logoHeight = imagesy($logo);
                    
                    // Target Logo Width: 35% of Image Width (increased from 20%)
                    $targetLogoWidth = $width * 0.35;
                    $targetLogoHeight = ($logoHeight / $logoWidth) * $targetLogoWidth;

                    // Position: Bottom Right
                    $x = $width - $targetLogoWidth - 20;
                    $y = $height - $targetLogoHeight - 60; // Above the text bar
                    
                    // Copy Resampled Logo
                    imagecopyresampled($source, $logo, $x, $y, 0, 0, $targetLogoWidth, $targetLogoHeight, $logoWidth, $logoHeight);
                    imagedestroy($logo);
                }
            }

                // 6. Save as WebP (Compressed)
                imagewebp($source, $absolutePath, 75); // Quality 75%
                imagedestroy($source);
                
                $storedPath = $relativePath;
            } else {
                // Fallback if GD fails
                $storedPath = $file->store("photos/orders/{$order->id}", 'public');
            }

            // Determine Step/Caption
            $step = $request->step ?? 'assessment_' . time(); // Generic fallback
            
            $photo = WorkOrderPhoto::create([
                'work_order_id' => $order->id,
                'step' => $step,
                'file_path' => $storedPath,
                'caption' => $request->caption,
                'is_public' => $request->boolean('is_public', true),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload (Compressed & Watermarked).',
                'photo' => $photo,
                'url' => Storage::url($storedPath)
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
