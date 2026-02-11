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

        // Create Issue
        \App\Models\CxIssue::create([
            'work_order_id' => $order->id,
            'spk_number' => $order->spk_number,
            'customer_phone' => $order->customer_phone,
            'customer_name' => $order->customer_name,
            'reported_by' => \Illuminate\Support\Facades\Auth::id(),
            'type' => 'FOLLOW_UP', // Generic type
            'category' => $request->category,
            'description' => $request->description,
            'suggested_services' => $request->suggested_services ? implode(',', $request->suggested_services) : null,
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
        $order->logs()->create([
            'step' => 'WORKSHOP', // Generic step name or derive from previous status
            'action' => 'REPORT_ISSUE',
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'description' => "Reported Issue ({$request->category}): {$request->description}. Suggested: {$suggestions}"
        ]);

        return redirect()->back()->with('success', 'Laporan kendala berhasil dikirim ke CX.');
    }
}
