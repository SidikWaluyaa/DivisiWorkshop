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
        if ($request->category === 'TEKNIS' || $request->category === 'MATERIAL') {
             $description = "Kendala: \n" . ($request->kendala ?: '-') . "\n\nOpsi Solusi: \n" . ($request->opsi_solusi ?: '-');
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
            'kendala' => $request->kendala,
            'opsi_solusi' => $request->opsi_solusi,
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

    public function update(Request $request, \App\Models\CxIssue $cxIssue)
    {
        $request->validate([
            'kendala' => 'nullable|string',
            'opsi_solusi' => 'nullable|string',
            'category' => 'nullable|string'
        ]);

        $category = $request->category ?: $cxIssue->category;
        
        // Format Description
        $description = $cxIssue->description;
        if ($category === 'TEKNIS' || $category === 'MATERIAL') {
             $description = "Kendala: \n" . ($request->kendala ?: '-') . "\n\nOpsi Solusi: \n" . ($request->opsi_solusi ?: '-');
        } elseif ($category === 'OVERLOAD') {
             $description = $request->estimasi_selesai ?: date('Y-m-d');
        }

        $cxIssue->update([
            'kendala' => $request->kendala,
            'opsi_solusi' => $request->opsi_solusi,
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
}
