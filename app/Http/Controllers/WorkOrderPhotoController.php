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
            'photos' => 'required|array',
            'photos.*' => 'image|max:10240', // Max 10MB per file
            'step' => 'required|string',
            'caption' => 'nullable|string|max:255',
            'is_public' => 'boolean'
        ]);

        $order = WorkOrder::findOrFail($workOrderId);
        $uploadedPhotos = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
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
                    // 3. Resize (High Quality - Max 2048px)
                    $width = imagesx($source);
                    $height = imagesy($source);
                    $maxWidth = 2048; // Increased from 1024 for better detail

                    if ($width > $maxWidth) {
                        $newWidth = $maxWidth;
                        $newHeight = floor($height * ($maxWidth / $width));
                        $destination = imagecreatetruecolor($newWidth, $newHeight);
                        // Use highest quality resampling
                        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($source);
                        $source = $destination;
                        $width = $newWidth;
                        $height = $newHeight;
                    }

                    // 4. Add Watermark (Big Logo Style)
                    
                    // Try to use dedicated watermark logo first
                    $logoPath = public_path('images/logo-watermark.png');
                    if (!file_exists($logoPath)) {
                        $logoPath = public_path('images/logo.png');
                    }

                    if (file_exists($logoPath)) {
                        $logo = imagecreatefrompng($logoPath);
                        if ($logo) {
                            // FIX: Use native function to convert palette to true color while keeping transparency
                            // This fixes the "Black Box" background issue
                            if (!imageistruecolor($logo)) {
                                imagepalettetotruecolor($logo);
                            }
                            
                            // Ensure alpha channel is preserved/saved
                            imagealphablending($logo, false);
                            imagesavealpha($logo, true);

                            $logoWidth = imagesx($logo);
                            $logoHeight = imagesy($logo);
                            
                            // Target Logo Width: 20% of Image Width (Subtle)
                            $targetLogoWidth = $width * 0.20; 
                            $targetLogoHeight = ($logoHeight / $logoWidth) * $targetLogoWidth;

                            // Position: Bottom Right with padding
                            $paddingX = 40;
                            $paddingY = 40;
                            
                            $x = $width - $targetLogoWidth - $paddingX;
                            $y = $height - $targetLogoHeight - $paddingY;
                            
                            // COPY: Enable blending on destination ($source) to composite the transparent logo ON TOP
                            // This ensures the transparency of the logo blends with the photo content
                            imagealphablending($source, true);
                            
                            // Use copyresampled for high quality resizing of logo
                            imagecopyresampled($source, $logo, $x, $y, 0, 0, $targetLogoWidth, $targetLogoHeight, $logoWidth, $logoHeight);
                            imagedestroy($logo);

                        }
                    }

                    // 5. Ensure True Color (Fix for Palette Error in WebP)
                    if (!imageistruecolor($source)) {
                        $trueColor = imagecreatetruecolor(imagesx($source), imagesy($source));
                        
                        // Preserve transparency if exists
                        $transparent = imagecolortransparent($source);
                        if ($transparent >= 0) {
                            $rgb = imagecolorsforindex($source, $transparent);
                            $transparentNew = imagecolorallocate($trueColor, $rgb['red'], $rgb['green'], $rgb['blue']);
                            imagecolortransparent($trueColor, $transparentNew);
                            imagefill($trueColor, 0, 0, $transparentNew);
                        } else {
                            // If no transparency, just fill white (standard for photos) or preserve original logic
                            // Actually, for photos, we just copy.
                        }
                        
                        imagecopy($trueColor, $source, 0, 0, 0, 0, imagesx($source), imagesy($source));
                        imagedestroy($source);
                        $source = $trueColor;
                    }

                    // 6. Save as WebP (High Quality: 90)
                    // 90 is visually lossless for photos but much smaller than PNG or raw JPEG
                    imagewebp($source, $absolutePath, 90); 
                    imagedestroy($source);
                    
                    $storedPath = $relativePath;
                } else {
                    // Fallback
                    $storedPath = $file->store("photos/orders/{$order->id}", 'public');
                }

                // Determine Step/Caption
                $step = $request->step ?? 'assessment_' . time();
                
                $photo = WorkOrderPhoto::create([
                    'work_order_id' => $order->id,
                    'step' => $step,
                    'file_path' => $storedPath,
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
    public function setAsCover($id)
    {
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
    }
}
