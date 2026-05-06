<?php

namespace App\Utils;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Convert and store image as JPG
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory Directory relative to 'public' disk
     * @param string|null $filename Custom filename (without extension)
     * @return string Path relative to 'storage' (e.g., 'storage/cx-issues/abc.jpg')
     */
    public static function convertToJpg($file, $directory, $filename = null)
    {
        try {
            if (!$filename) {
                $filename = Str::random(40);
            }

            // Boost memory for image processing
            ini_set('memory_limit', '1024M');

            $filename = $filename . '.jpg';
            $fullPath = $directory . '/' . $filename;

            // Use Intervention Image v3 Manager
            $manager = \Intervention\Image\ImageManager::gd();
            $image = $manager->read($file);
            
            // Compress heavily by scaling down to max 800x800 while preserving aspect ratio
            $image->scaleDown(800, 800);
            
            $encoded = $image->toJpeg(60); // 60% quality

            // Store to public disk
            Storage::disk('public')->put($fullPath, (string) $encoded);

            return 'storage/' . $fullPath;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ImageHelper Error: " . $e->getMessage());
            throw $e;
        }
    }
}
