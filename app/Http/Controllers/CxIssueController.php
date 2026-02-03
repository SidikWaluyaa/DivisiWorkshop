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
            'photos.*' => 'nullable|image|max:2048', // Multiple photos
        ]);

        $order = \App\Models\WorkOrder::findOrFail($request->work_order_id);
        
        // Handle Photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Determine destination based on category/type logic if needed, or generic
                // Using CxIssue directory
                $path = $photo->store('cx-issues', 'public');
                $photoPaths[] = 'storage/' . $path;
            }
        }

        // Create Issue
        \App\Models\CxIssue::create([
            'work_order_id' => $order->id,
            'reported_by' => \Illuminate\Support\Facades\Auth::id(),
            'type' => 'FOLLOW_UP', // Generic type
            'category' => $request->category,
            'description' => $request->description,
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
        $order->logs()->create([
            'step' => 'WORKSHOP', // Generic step name or derive from previous status
            'action' => 'REPORT_ISSUE',
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'description' => "Reported Issue ({$request->category}): {$request->description}"
        ]);

        return redirect()->back()->with('success', 'Laporan kendala berhasil dikirim ke CX.');
    }
}
