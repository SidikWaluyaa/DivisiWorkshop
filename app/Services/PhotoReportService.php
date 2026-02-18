<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\WorkOrderPhoto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoReportService
{
    /**
     * Generate and save a PDF report for finished photos of a Work Order.
     *
     * @param WorkOrder $workOrder
     * @return string|null The relative path to the generated PDF
     */
    public function generateFinishReport(WorkOrder $workOrder)
    {
        // 1. Fetch relevant photos (Step: FINISH or UPSELL_BEFORE/AFTER)
        // Adjust steps based on your needs. User mentioned "Finish" or "After".
        $photos = WorkOrderPhoto::where('work_order_id', $workOrder->id)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($photos->isEmpty()) {
            // Explicitly clear the URL if no photos exist
            $workOrder->update(['finish_report_url' => null]);
            return null;
        }

        // [ROBUSTNESS] Filter out photos that don't exist or are corrupt to prevent PDF engine crash
        $validPhotos = $photos->filter(function($photo) {
            $path = public_path('storage/' . $photo->file_path);
            
            // Check existence
            if (!file_exists($path)) {
                \Illuminate\Support\Facades\Log::warning("PDF Generation: Skipping photo ID {$photo->id} - File not found: {$path}");
                return false;
            }

            // Optional: Basic integrity check using getimagesize
            if (@getimagesize($path) === false) {
                 \Illuminate\Support\Facades\Log::warning("PDF Generation: Skipping photo ID {$photo->id} - File corrupt or unreadable: {$path}");
                 return false;
            }

            return true;
        });

        if ($validPhotos->isEmpty()) {
            $workOrder->update(['finish_report_url' => null]);
            return null;
        }

        // 2. Prepare data for the PDF template
        $data = [
            'workOrder' => $workOrder,
            'photos' => $validPhotos,
            'generatedAt' => now()->format('d F Y, H:i'),
        ];

        // 3. Render PDF
        $pdf = Pdf::loadView('reports.finish-report-pdf', $data);
        
        // Optimize for speed/resizing if needed
        $pdf->setPaper('a4', 'portrait');

        // 4. Save to storage
        $filename = 'REPORT_FINISH_' . str_replace('/', '-', $workOrder->spk_number) . '.pdf';
        $path = 'reports/finish/' . $filename;

        // [CLEANUP] Delete old PDF file if it exists to keep server clean
        $oldUrl = $workOrder->finish_report_url;
        if ($oldUrl && str_contains($oldUrl, 'storage/reports/finish/')) {
            $oldRelativePath = 'reports/finish/' . basename(parse_url($oldUrl, PHP_URL_PATH));
            if ($oldRelativePath !== $path && Storage::disk('public')->exists($oldRelativePath)) {
                Storage::disk('public')->delete($oldRelativePath);
            }
        }

        Storage::disk('public')->put($path, $pdf->output());

        // 5. Update Work Order with the dynamic Route URL (Reliable headers)
        $workOrder->finish_report_url = route('finish.view-report', $workOrder->id);
        $workOrder->save();

        return $workOrder->finish_report_url;
    }
}
