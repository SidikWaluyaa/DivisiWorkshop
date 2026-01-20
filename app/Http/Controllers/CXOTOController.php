<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CXOTOController extends Controller
{
    /**
     * Display OTO pool for CX team
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        $query = \App\Models\OTO::with(['workOrder', 'creator', 'contactLogs.contactedBy'])
            ->whereIn('status', ['PENDING_CX', 'CONTACTED']);
        
        // Filter
        if ($filter === 'urgent') {
            $query->where('valid_until', '<=', now()->addDays(3));
        } elseif ($filter === 'my') {
            $query->where('cx_assigned_to', auth()->id());
        }
        
        // Order by Valid Until ASC (Urgency) then Created At DESC (Newest)
        $otos = $query->orderBy('valid_until', 'asc')->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistics
        $stats = [
            'pending' => \App\Models\OTO::where('status', 'PENDING_CX')->count(),
            'contacted' => \App\Models\OTO::where('status', 'CONTACTED')->count(),
            'accepted' => \App\Models\OTO::where('status', 'ACCEPTED')->count(),
            'total_revenue' => \App\Models\OTO::whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
                ->sum('total_oto_price'),
        ];
        
        return view('cx.oto.index', compact('otos', 'stats', 'filter'));
    }
    
    /**
     * Mark OTO as contacted
     */
    public function markContacted(Request $request, $id)
    {
        $request->validate([
            'contact_method' => 'required|in:WHATSAPP,PHONE,EMAIL,IN_PERSON',
            'customer_response' => 'required|in:INTERESTED,NOT_INTERESTED,NEED_TIME,NO_ANSWER',
            'notes' => 'nullable|string',
        ]);
        
        $oto = \App\Models\OTO::findOrFail($id);
        
        DB::transaction(function() use ($oto, $request) {
            // Update OTO
            $oto->update([
                'status' => $request->customer_response === 'INTERESTED' ? 'PENDING_CUSTOMER' : 'CONTACTED',
                'cx_assigned_to' => auth()->id(),
                'cx_contacted_at' => now(),
                'cx_contact_method' => $request->contact_method,
                'cx_notes' => $request->notes,
                'cx_follow_up_count' => $oto->cx_follow_up_count + 1,
            ]);
            
            // Log contact
            \App\Models\OTOContactLog::create([
                'oto_id' => $oto->id,
                'contacted_by' => auth()->id(),
                'contact_method' => $request->contact_method,
                'notes' => $request->notes,
                'customer_response' => $request->customer_response,
            ]);
            
            // If customer not interested, cancel OTO within transaction
            if ($request->customer_response === 'NOT_INTERESTED') {
                $oto->update(['status' => 'CANCELLED']);
                // Release materials
                foreach ($oto->materialReservations as $reservation) {
                    $reservation->release();
                }
                $oto->workOrder->update(['has_active_oto' => false]);
            }
        });
        
        return back()->with('success', 'Contact log berhasil disimpan!');
    }
    
    /**
     * Customer accepts OTO
     */
    public function customerAccept($id)
    {
        $oto = \App\Models\OTO::findOrFail($id);
        
        DB::transaction(function() use ($oto) {
            // Update OTO status
            $oto->update([
                'status' => 'ACCEPTED',
                'customer_responded_at' => now(),
            ]);
            
            // Convert soft reserve to hard reserve
            foreach ($oto->materialReservations as $reservation) {
                $reservation->confirmReservation();
            }
            
            // Update work order
            $oto->workOrder->update([
                // 'oto_addition_amount' => $oto->total_oto_price, // DONT SET THIS! Will cause double counting in Finance
                'oto_discount_amount' => $oto->total_discount, // Keep for reference
                'oto_priority_boost' => 30, // Significant boost for OTO
                'priority' => 'Express', // AUTOMATIC FAST TRACK as per user request
            ]);
            
            // Add services to work order
            foreach ($oto->proposed_services as $service) {
                $oto->workOrder->services()->attach($service['service_id'], [
                    'cost' => $service['oto_price'],
                    'custom_name' => 'OTO: ' . $service['service_name'],
                ]);
            }
            
            // Recalculate total
            $oto->workOrder->refresh(); // Refresh to get latest totals if needed logic applied elsewhere
            // Assuming simple addition for now, real implementation might need detailed recalculation
            $oto->workOrder->total_service_price += $oto->total_oto_price;
            $oto->workOrder->save();
            
            // Reset Workflow Timestamps for clean OTO tracking
            // Smart Reset: Only reset workflow steps relevant to the new services
            $resetData = [
                'status' => \App\Enums\WorkOrderStatus::PREPARATION,
                'taken_date' => null,
                'finished_date' => null,
                
                // Always reset QC columns (Timestamps AND Technicians)
                'qc_jahit_started_at' => null, 'qc_jahit_completed_at' => null, 'qc_jahit_technician_id' => null,
                'qc_cleanup_started_at' => null, 'qc_cleanup_completed_at' => null, 'qc_cleanup_technician_id' => null,
                'qc_final_started_at' => null, 'qc_final_completed_at' => null, 'qc_final_pic_id' => null,
            ];

            // Check categories to reset specific production steps
            $categories = collect($oto->proposed_services)->pluck('service_category')->map(fn($c) => strtolower($c));
            
            $needsSol = $categories->contains(fn($c) => str_contains($c, 'sol') || str_contains($c, 'reglue'));
            $needsUpper = $categories->contains(fn($c) => str_contains($c, 'upper') || str_contains($c, 'jahit'));
            $needsCleaning = $categories->contains(fn($c) => str_contains($c, 'cleaning') || str_contains($c, 'repaint') || str_contains($c, 'treatment') || str_contains($c, 'whitening'));

            if ($needsSol) {
                $resetData['prod_sol_started_at'] = null;
                $resetData['prod_sol_completed_at'] = null;
                $resetData['prod_sol_by'] = null; // Reset technician
            }
            
            if ($needsUpper) {
                $resetData['prod_upper_started_at'] = null;
                $resetData['prod_upper_completed_at'] = null;
                $resetData['prod_upper_by'] = null; // Reset technician
            }
            
            if ($needsCleaning) {
                $resetData['prod_cleaning_started_at'] = null;
                $resetData['prod_cleaning_completed_at'] = null;
                $resetData['prod_cleaning_by'] = null; // Reset technician
            }

            $oto->workOrder->update($resetData);
        });
        
        return back()->with('success', 'OTO diterima customer! Order kembali ke proses produksi.');
    }
    
    /**
     * Customer rejects OTO
     */
    public function customerReject(Request $request, $id)
    {
        $oto = \App\Models\OTO::findOrFail($id);
        
        $oto->update([
            'status' => 'REJECTED',
            'customer_responded_at' => now(),
            'customer_note' => $request->note,
        ]);
        
        // Release material reservations
        foreach ($oto->materialReservations as $reservation) {
            $reservation->release();
        }
        
        // Update work order
        $oto->workOrder->update(['has_active_oto' => false]);
        
        return back()->with('info', 'OTO ditolak oleh customer.');
    }
    
    /**
     * Cancel OTO (by CX)
     */
    public function cancel($id)
    {
        $oto = \App\Models\OTO::findOrFail($id);
        
        $oto->update(['status' => 'CANCELLED']);
        
        // Release materials
        foreach ($oto->materialReservations as $reservation) {
            $reservation->release();
        }
        
        $oto->workOrder->update(['has_active_oto' => false]);
        
        return back()->with('success', 'OTO dibatalkan manual.');
    }
}
