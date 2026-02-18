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
        // 1. Fetch only FINISH photos
        $photos = $workOrder->photos()
            ->where('step', 'FINISH')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($photos->isEmpty()) {
            return null;
        }

        // 2. [ROBUSTNESS] Convert images to Base64 to bypass server path/permission issues
        $validPhotos = $photos->map(function($photo) {
            $filePath = $photo->file_path;
            
            // If it's a full URL, extract the relative storage path
            if (str_starts_with($filePath, 'http')) {
                $filePath = Str::after($filePath, 'storage/');
            }
            
            $fullPath = public_path('storage/' . $filePath);
            
            // Check existence and read file
            if (!file_exists($fullPath)) {
                \Illuminate\Support\Facades\Log::warning("PDF Generation: Skipping photo ID {$photo->id} - File not found: {$fullPath}");
                return null;
            }

            try {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                
                // Add base64 data to the photo object
                $photo->base64_image = $base64;
                return $photo;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("PDF Generation: Failed to Base64 encode photo ID {$photo->id}: " . $e->getMessage());
                return null;
            }
        })->filter();

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

        // 5. Ensure invoice_token exists (Fallback)
        if (empty($workOrder->invoice_token)) {
            $workOrder->invoice_token = Str::uuid()->toString();
            $workOrder->save();
        }

        // 6. Update Work Order with the Digital Landing Page URL (Premium experience)
        $workOrder->finish_report_url = route('customer.report', [
            'spk' => Str::slug($workOrder->spk_number),
            'token' => $workOrder->invoice_token
        ]);
        $workOrder->save();

        return $workOrder->finish_report_url;
    }
}
