<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxIssue;
use App\Models\WorkOrderPhoto;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class ConvertExistingCxPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-cx-photos {--dry-run : Only show what would be converted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert existing CX issue photos and QC reject evidence to JPG format';

    protected $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting photograph conversion to JPG...');

        // 1. Process CX Issues
        $this->info('Processing CxIssue records...');
        $cxIssues = CxIssue::whereNotNull('photos')->get();
        foreach ($cxIssues as $issue) {
            /** @var \App\Models\CxIssue $issue */
            $photos = $issue->photos;
            if (!is_array($photos)) continue;

            $updatedPhotos = [];
            $changed = false;

            foreach ($photos as $photoPath) {
                $newPath = $this->convertFile($photoPath, $dryRun);
                if ($newPath !== $photoPath) {
                    $updatedPhotos[] = $newPath;
                    $changed = true;
                } else {
                    $updatedPhotos[] = $photoPath;
                }
            }

            if ($changed && !$dryRun) {
                $issue->photos = $updatedPhotos;
                $issue->save();
            }
        }

        // 2. Process QC Reject Evidence in WorkOrderPhoto
        $this->info('Processing WorkOrderPhoto records (QC_REJECT_EVIDENCE)...');
        $qcPhotos = WorkOrderPhoto::where('step', 'QC_REJECT_EVIDENCE')->get();
        foreach ($qcPhotos as $photo) {
            /** @var \App\Models\WorkOrderPhoto $photo */
            $newPath = $this->convertFile($photo->file_path, $dryRun);
            if ($newPath !== $photo->file_path && !$dryRun) {
                $photo->file_path = $newPath;
                $photo->save();
            }
        }

        $this->info('Conversion process completed.');
    }

    /**
     * Convert a single file to JPG if necessary
     */
    protected function convertFile($path, $dryRun = false)
    {
        // Clean path (sometimes it has 'storage/' prefix in DB, sometimes not)
        $cleanPath = str_replace('storage/', '', $path);
        
        if (!Storage::disk('public')->exists($cleanPath)) {
            $this->warn("File not found: {$cleanPath}");
            return $path;
        }

        $extension = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
        
        // Already JPG or JPEG
        if (in_array($extension, ['jpg', 'jpeg'])) {
            return $path;
        }

        $newCleanPath = preg_replace('/\.[^.]+$/', '.jpg', $cleanPath);
        $absoluteInputPath = Storage::disk('public')->path($cleanPath);

        if ($dryRun) {
            $this->line("Would convert: [{$extension}] {$cleanPath} -> JPG");
            return $path;
        }

        try {
            $this->line("Converting: [{$extension}] {$cleanPath} to JPG...");
            
            $image = $this->manager->read($absoluteInputPath);
            $encoded = $image->toJpeg(85);
            
            Storage::disk('public')->put($newCleanPath, (string) $encoded);
            
            // Delete old file
            Storage::disk('public')->delete($cleanPath);
            
            // Return original format of path (with or without 'storage/')
            return str_contains($path, 'storage/') ? 'storage/' . $newCleanPath : $newCleanPath;

        } catch (\Exception $e) {
            $this->error("Failed to convert {$cleanPath}: " . $e->getMessage());
            return $path;
        }
    }
}
