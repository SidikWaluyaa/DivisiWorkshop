<?php

namespace App\Services\Photo;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class PhotoProcessingService
{
    protected $manager;

    public function __construct()
    {
        // Use GD driver as it's standard, but could be switched to Imagick if available
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process an image: Resize (Smart HD), Watermark, and Convert to JPG/PNG
     */
    public function process($inputPath, $outputPath, $options = [])
    {
        $quality = $options['quality'] ?? 90;
        $maxWidth = $options['max_width'] ?? 2048;
        $addWatermark = $options['watermark'] ?? true;

        // 1. Read Image
        $image = $this->manager->read(Storage::disk('public')->path($inputPath));

        // 2. Smart HD Resize (Only scale down if wider than maxWidth)
        $image->scaleDown(width: $maxWidth);

        // 3. Apply Watermark
        if ($addWatermark) {
            $this->applyWatermark($image);
        }

        // 4. Encode & Save (High Quality)
        // Default to JPG for photos to balance size/quality, but can be PNG
        $encoded = $image->toJpeg($quality);
        
        Storage::disk('public')->put($outputPath, (string) $encoded);

        // Cleanup input if different from output
        if ($inputPath !== $outputPath) {
            Storage::disk('public')->delete($inputPath);
        }

        // Explicit memory cleanup
        unset($image);

        return $outputPath;
    }

    /**
     * Apply watermark with transparency and smart positioning
     */
    protected function applyWatermark($image)
    {
        $logoPath = public_path('images/logo-watermark.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/logo.png');
        }

        if (file_exists($logoPath)) {
            $watermark = $this->manager->read($logoPath);
            
            // Resize watermark to 40% of the main image width (Premium Large Branding)
            $watermarkWidth = $image->width() * 0.40;
            $watermark->scale(width: $watermarkWidth);

            // Place in bottom right with padding
            $image->place(
                $watermark,
                'bottom-right',
                25, // padding x
                25  // padding y
            );

            // Cleanup watermark object
            unset($watermark);
        }
    }
}
