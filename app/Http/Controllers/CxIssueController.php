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
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Multiple photos (JPG/PNG only)
        ]);

        $order = \App\Models\WorkOrder::findOrFail($request->work_order_id);
        
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
            foreach ($request->file('photos') as $index => $photo) {
                // Use ImageHelper to convert to JPG
                $filename = 'CX_ISSUE_' . $order->spk_number . '_' . time() . '_' . $index;
                $photoPaths[] = \App\Utils\ImageHelper::convertToJpg($photo, 'cx-issues', $filename);
            }
        }

        // Derive source from order's current status
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
            'description' => $request->description,
            'desc_upper' => $request->desc_upper,
            'desc_sol' => $request->desc_sol,
            'desc_kondisi_bawaan' => $request->desc_kondisi_bawaan,
            'rec_service_1' => $request->rec_service_1,
            'rec_service_2' => $request->rec_service_2,
            'sug_service_1' => $request->sug_service_1,
            'sug_service_2' => $request->sug_service_2,
            'suggested_services' => $request->suggested_services 
                ? collect($request->suggested_services)->map(fn($s, $idx) => ($idx + 1) . ". " . $s)->implode("\n") 
                : null,
            'recommended_services' => $request->recommended_services 
                ? collect($request->recommended_services)->map(fn($s, $idx) => ($idx + 1) . ". " . $s)->implode("\n") 
                : null,
            'photos' => $photoPaths,
            'status' => 'OPEN',
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
}
