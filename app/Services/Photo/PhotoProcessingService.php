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
        try {
            $inputFullPath = Storage::disk('public')->path($inputPath);
            if (!file_exists($inputFullPath)) {
                throw new \Exception("File input tidak ditemukan: {$inputFullPath}");
            }
            if (filesize($inputFullPath) === 0) {
                throw new \Exception("File kosong (0 bytes). Kemungkinan gagal upload.");
            }

            // Optional: Basic Mime Check
            $mime = mime_content_type($inputFullPath);
            if (!str_starts_with($mime, 'image/')) {
                 throw new \Exception("Format file tidak didukung: {$mime}");
            }

            // DEBUG: Log image details before processing
            \Illuminate\Support\Facades\Log::info("PhotoProcessingService: Reading file [ID: N/A]", [
                'path' => $inputFullPath,
                'mime' => $mime,
                'size' => filesize($inputFullPath)
            ]);

            $image = $this->manager->read($inputFullPath);
        } catch (\Exception $e) {
            // Include specific details for debugging
            throw new \Exception("Gagal membaca gambar ({$inputPath}): " . $e->getMessage());
        }

        // 2. Smart HD Resize (Only scale down if wider than maxWidth)
        $image->scaleDown(width: $maxWidth);

        // 3. Apply Watermark
        if ($addWatermark) {
            $this->applyWatermark($image);
        }

        // 4. Encode & Save (High Quality)
        $encoded = $image->toJpeg($quality);
        $encodedString = (string) $encoded;

        // CRITICAL: Explicitly clear the image object BEFORE filesystem operations
        // This releases any file handles (especially important on Windows)
        unset($image);

        // 5. Windows-Safe Overwrite: Save to temporary file first, then replace
        $tempPath = $outputPath . '.tmp';
        Storage::disk('public')->put($tempPath, $encodedString);

        // Verification: Ensure the file was actually written
        if (!Storage::disk('public')->exists($tempPath)) {
            throw new \Exception("Gagal menulis file sementara ke disk.");
        }

        // Cleanup input if it's different (e.g. converting png to jpg)
        if ($inputPath !== $outputPath) {
            Storage::disk('public')->delete($inputPath);
        }

        // Final move (Overwrite original using direct SAVE to ensure reliability)
        // We've already unset $image, so the file handles are released.
        try {
            Storage::disk('public')->put($outputPath, $encodedString);
            
            // Cleanup temp file if it was used for verification logic earlier
            // Actually, we can just skip the tempPath entirely for a cleaner overwrite
            // But let's keep it clean:
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
        } catch (\Exception $e) {
            throw new \Exception("Gagal menghajar file asli dengan hasil kompresi: " . $e->getMessage());
        }

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
