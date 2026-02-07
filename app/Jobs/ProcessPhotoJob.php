<?php

namespace App\Jobs;

use App\Models\WorkOrderPhoto;
use App\Services\Photo\PhotoProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photoId;
    protected $options;

    /**
     * Create a new job instance.
     */
    public function __construct($photoId, $options = [])
    {
        $this->photoId = $photoId;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(PhotoProcessingService $service)
    {
        try {
            $photo = WorkOrderPhoto::find($this->photoId);
            
            if (!$photo) {
                Log::warning("ProcessPhotoJob: Photo with ID {$this->photoId} not found.");
                return;
            }

            $inputPath = $photo->file_path;
            
            // Define final path (converting to .jpg for high-fidelity photo standards)
            $pathInfo = pathinfo($inputPath);
            $finalPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.jpg';

            // Process the image
            $service->process($inputPath, $finalPath, $this->options);

            // Update database with the processed path
            $photo->update([
                'file_path' => $finalPath
            ]);

        } catch (\Exception $e) {
            Log::error("ProcessPhotoJob Error [ID: {$this->photoId}]: " . $e->getMessage());
            throw $e;
        }
    }
}
