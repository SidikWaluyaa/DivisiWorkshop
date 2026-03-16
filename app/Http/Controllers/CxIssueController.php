<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CxIssueController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'category' => 'required|string',
            'description' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png',
            'desc_upper' => 'nullable|string',
            'desc_sol' => 'nullable|string',
            'desc_kondisi_bawaan' => 'nullable|string',
            'rec_service_1' => 'nullable|string',
            'rec_service_2' => 'nullable|string',
            'sug_service_1' => 'nullable|string',
            'sug_service_2' => 'nullable|string',
            'recommended_services' => 'nullable|string',
            'suggested_services' => 'nullable|string',
        ]);

        $order = \App\Models\WorkOrder::findOrFail($request->work_order_id);
        
        // Carry over shipping_status from previous OPEN issue if exists
        $previousOpenIssue = \App\Models\CxIssue::where('work_order_id', $order->id)
            ->where('status', 'OPEN')
            ->first();
        $inheritedShippingStatus = $previousOpenIssue ? $previousOpenIssue->shipping_status : 'HOLD';

        // 0. Auto-close (RESOLVED) any existing OPEN issues for this order
        // This prevents "old data" from lingering if multiple issues are reported sequentially.
        \App\Models\CxIssue::where('work_order_id', $order->id)
            ->where('status', 'OPEN')
            ->update([
                'status' => 'RESOLVED',
                'resolution' => 'SUPERSEDED',
                'resolution_notes' => 'Closed automatically by new issue report.',
                'resolved_by' => \Illuminate\Support\Facades\Auth::id(),
                'resolved_at' => now(),
            ]);

        // Handle Photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            // Temporarily increase memory limit for processing large images
            ini_set('memory_limit', '1024M');
            
            foreach ($request->file('photos') as $index => $photo) {
                // Use ImageHelper to convert to JPG
                $filename = 'CX_ISSUE_' . $order->spk_number . '_' . time() . '_' . $index;
                $photoPaths[] = \App\Utils\ImageHelper::convertToJpg($photo, 'cx-issues', $filename);
            }
        }

        // Determine source from order's current status
        $currentStatus = $order->status instanceof \App\Enums\WorkOrderStatus 
            ? $order->status->value 
            : $order->status;
        $source = match($currentStatus) {
            \App\Enums\WorkOrderStatus::PREPARATION->value  => 'WORKSHOP_PREP',
            \App\Enums\WorkOrderStatus::SORTIR->value       => 'WORKSHOP_SORTIR',
            \App\Enums\WorkOrderStatus::PRODUCTION->value   => 'WORKSHOP_PROD',
            \App\Enums\WorkOrderStatus::QC->value           => 'WORKSHOP_QC',
            \App\Enums\WorkOrderStatus::DITERIMA->value     => 'GUDANG',
            default                                         => 'MANUAL',
        };

        // Format description based on category
        $description = $request->description; // Use frontend payload if available, or logic below
        if (in_array($request->category, ['TEKNIS', 'MATERIAL', 'KONFIRMASI'])) {
            $k1 = $request->kendala_1 ? "1. " . $request->kendala_1 . "\n" : "";
            $k2 = $request->kendala_2 ? "2. " . $request->kendala_2 . "\n" : "";
            $kText = ($k1 || $k2) ? ($k1 . $k2) : "-\n";
            
            if ($request->category === 'KONFIRMASI') {
                $s1 = $request->opsi_solusi_1 ? "3. " . $request->opsi_solusi_1 . "\n" : "";
                $s2 = $request->opsi_solusi_2 ? "4. " . $request->opsi_solusi_2 . "\n" : "";
                $sText = ($s1 || $s2) ? ($s1 . $s2) : "-\n";
                $description = "Tujuan Konfirmasi:\n" . $kText . "\nTindakan:\n" . $sText;
            } else {
                $s1 = $request->opsi_solusi_1 ? "1. " . $request->opsi_solusi_1 . "\n" : "";
                $s2 = $request->opsi_solusi_2 ? "2. " . $request->opsi_solusi_2 . "\n" : "";
                $sText = ($s1 || $s2) ? ($s1 . $s2) : "-\n";
                $description = "Kendala:\n" . $kText . "\nOpsi Solusi:\n" . $sText;
            }
        } elseif ($request->category === 'OVERLOAD') {
             $description = $request->estimasi_selesai ?: date('Y-m-d');
        }

        // Create Issue
        \App\Models\CxIssue::create([
            'work_order_id' => $order->id,
            'spk_number' => $order->spk_number,
            'customer_phone' => $order->customer_phone,
            'customer_name' => $order->customer_name,
            'reported_by' => \Illuminate\Support\Facades\Auth::id(),
            'type' => 'FOLLOW_UP',
            'source' => $source,
            'category' => $request->category,
            'description' => $description,
            'kendala_1' => $request->kendala_1,
            'kendala_2' => $request->kendala_2,
            'opsi_solusi_1' => $request->opsi_solusi_1,
            'opsi_solusi_2' => $request->opsi_solusi_2,
            'desc_upper' => $request->desc_upper,
            'desc_sol' => $request->desc_sol,
            'desc_kondisi_bawaan' => $request->desc_kondisi_bawaan,
            'rec_service_1' => $request->rec_service_1,
            'rec_service_2' => $request->rec_service_2,
            'sug_service_1' => $request->sug_service_1,
            'sug_service_2' => $request->sug_service_2,
            'recommended_services' => $request->recommended_services,
            'suggested_services' => $request->suggested_services,
            'photos' => $photoPaths,
            'status' => 'OPEN',
            'shipping_status' => $inheritedShippingStatus,
        ]);

        // Update WorkOrder Status
        $previousStatus = $order->status instanceof \App\Enums\WorkOrderStatus 
            ? $order->status->value 
            : $order->status;

        $order->update([
            'status' => \App\Enums\WorkOrderStatus::CX_FOLLOWUP,
            'previous_status' => $order->status,
            'notes' => $order->notes . "\n[CX Issue Reported]: " . $request->description
        ]);

        // Create Log
        $suggestions = $request->suggested_services ? implode(', ', $request->suggested_services) : '-';
        $recommended = $request->recommended_services ? implode(', ', $request->recommended_services) : '-';
        $order->logs()->create([
            'step' => 'WORKSHOP', // Generic step name or derive from previous status
            'action' => 'REPORT_ISSUE',
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'description' => "Reported Issue ({$request->category}): {$request->description}. Recommended: {$recommended}. Optional: {$suggestions}"
        ]);

        return redirect()->back()->with('success', 'Laporan kendala berhasil dikirim ke CX.');
    }

    public function update(Request $request, \App\Models\CxIssue $cxIssue)
    {
        $request->validate([
            'kendala_1' => 'nullable|string',
            'kendala_2' => 'nullable|string',
            'opsi_solusi_1' => 'nullable|string',
            'opsi_solusi_2' => 'nullable|string',
            'category' => 'nullable|string',
            'desc_upper' => 'nullable|string',
            'desc_sol' => 'nullable|string',
            'desc_kondisi_bawaan' => 'nullable|string',
            'rec_service_1' => 'nullable|string',
            'rec_service_2' => 'nullable|string',
            'sug_service_1' => 'nullable|string',
            'sug_service_2' => 'nullable|string',
            'recommended_services' => 'nullable|string',
            'suggested_services' => 'nullable|string',
        ]);

        $category = $request->category ?: $cxIssue->category;
        
        // Format Description
        $description = $cxIssue->description;
        if (in_array($category, ['TEKNIS', 'MATERIAL', 'KONFIRMASI'])) {
            $k1 = $request->kendala_1 ? "1. " . $request->kendala_1 . "\n" : "";
            $k2 = $request->kendala_2 ? "2. " . $request->kendala_2 . "\n" : "";
            $kText = ($k1 || $k2) ? ($k1 . $k2) : "-\n";
            
            if ($category === 'KONFIRMASI') {
                $s1 = $request->opsi_solusi_1 ? "3. " . $request->opsi_solusi_1 . "\n" : "";
                $s2 = $request->opsi_solusi_2 ? "4. " . $request->opsi_solusi_2 . "\n" : "";
                $sText = ($s1 || $s2) ? ($s1 . $s2) : "-\n";
                $description = "Tujuan Konfirmasi:\n" . $kText . "\nTindakan:\n" . $sText;
            } else {
                $s1 = $request->opsi_solusi_1 ? "1. " . $request->opsi_solusi_1 . "\n" : "";
                $s2 = $request->opsi_solusi_2 ? "2. " . $request->opsi_solusi_2 . "\n" : "";
                $sText = ($s1 || $s2) ? ($s1 . $s2) : "-\n";
                $description = "Kendala:\n" . $kText . "\nOpsi Solusi:\n" . $sText;
            }
        } elseif ($category === 'OVERLOAD') {
             $description = $request->estimasi_selesai ?: date('Y-m-d');
        }

        $cxIssue->update([
            'kendala_1' => $request->kendala_1,
            'kendala_2' => $request->kendala_2,
            'opsi_solusi_1' => $request->opsi_solusi_1,
            'opsi_solusi_2' => $request->opsi_solusi_2,
            'desc_upper' => $request->desc_upper,
            'desc_sol' => $request->desc_sol,
            'desc_kondisi_bawaan' => $request->desc_kondisi_bawaan,
            'rec_service_1' => $request->rec_service_1,
            'rec_service_2' => $request->rec_service_2,
            'sug_service_1' => $request->sug_service_1,
            'sug_service_2' => $request->sug_service_2,
            'recommended_services' => $request->recommended_services,
            'suggested_services' => $request->suggested_services,
            'description' => $description,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan kendala / reject berhasil diperbarui.'
            ]);
        }

        return redirect()->back()->with('success', 'Catatan kendala / reject berhasil diperbarui.');
    }

    /**
     * Public Report URL for CX Issue (Landing Page Format)
     */
    public function report($spk_number)
    {
        $issue = \App\Models\CxIssue::where('spk_number', $spk_number)->latest()->firstOrFail();
        $order = \App\Models\WorkOrder::where('spk_number', $issue->spk_number)->first();
        
        $photos = is_array($issue->photos) ? $issue->photos : json_decode($issue->photos, true);
        if (!$photos) {
            $photos = [];
        }

        // Map relative paths to absolute URLs and check file sizes
        $photoUrls = [];
        $photoSizes = [];
        
        $dbPhotos = [];
        if (!empty($photos)) {
            $first = reset($photos);
            if (is_array($first) && isset($first['path'])) {
                $dbPhotos = array_column($photos, 'path');
            } elseif (is_string($first)) {
                $dbPhotos = $photos;
            }
        }
        
        foreach($dbPhotos as $path) {
            $url = str_starts_with($path, 'http') ? $path : '/' . ltrim($path, '/');
            $photoUrls[] = $url;
            
            $size = 'N/A';
            if (!str_starts_with($path, 'http')) {
                $relativePath = str_replace('storage/', '', ltrim($path, '/'));
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                    $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($relativePath);
                    $size = number_format($bytes / 1024, 1) . ' KB';
                }
            }
            $photoSizes[$url] = $size;
        }

        return view('cx.issue-report', compact('issue', 'order', 'photoUrls', 'photoSizes'));
    }
    public function toggleShipping(Request $request, \App\Models\CxIssue $cxIssue)
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $oldStatus = $cxIssue->shipping_status;
            // Toggle the shipping status
            $newStatus = $oldStatus === 'SEND' ? 'HOLD' : 'SEND';
            
            \Illuminate\Support\Facades\Log::info("CX Toggle Attempt: User {$user->name} (#{$user->id}) is changing Issue #{$cxIssue->id} ({$cxIssue->spk_number}) from {$oldStatus} to {$newStatus}");

            $updated = $cxIssue->update([
                'shipping_status' => $newStatus
            ]);

            if (!$updated) {
                throw new \Exception("Database update returned false.");
            }

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Status pengiriman diubah ke {$newStatus}"
            ], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("CX Toggle Error for Issue #{$cxIssue->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
