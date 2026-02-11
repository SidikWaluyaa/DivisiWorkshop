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
        if (!$filename) {
            $filename = Str::random(40);
        }

        $filename = $filename . '.jpg';
        $fullPath = $directory . '/' . $filename;

        // Use Intervention Image to convert/encode to JPG
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image = $manager->read($file);
        $encoded = $image->toJpeg(80); // 80% quality

        // Store to public disk
        Storage::disk('public')->put($fullPath, (string) $encoded);

        return 'storage/' . $fullPath;
    }
}
