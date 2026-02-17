<?php

namespace App\Jobs;

use App\Models\WorkOrder;
use App\Services\PhotoReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePhotoReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $workOrder;

    /**
     * Create a new job instance.
     */
    public function __construct(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
    }

    /**
     * Execute the job.
     */
    public function handle(PhotoReportService $service): void
    {
        try {
            Log::info("Generating finish photo report for SPK: {$this->workOrder->spk_number}");
            $service->generateFinishReport($this->workOrder);
        } catch (\Exception $e) {
            Log::error("Failed to generate report for SPK: {$this->workOrder->spk_number}. Error: " . $e->getMessage());
        }
    }
}
