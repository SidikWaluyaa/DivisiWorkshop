<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $query->where('cx_assigned_to', Auth::id());
        }
        
        // Order by Valid Until ASC (Urgency) then Created At DESC (Newest)
        $otos = $query->orderBy('valid_until', 'asc')->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistics
        $stats = $this->getStats();
        
        return view('cx.oto.index', compact('otos', 'stats', 'filter'));
    }

    /**
     * Get OTO statistics.
     *
     * @return array
     */
    public function getStats()
    {
        $acceptedOTOs = \App\Models\OTO::where('status', 'ACCEPTED')->get();
        $totalRevenue = $acceptedOTOs->sum(function($oto) {
            // Helper to parse "Rp. 115.000" back to numeric
            return (float) str_replace(['Rp. ', '.', ','], '', $oto->total_oto_price);
        });

        return [
            'pending' => \App\Models\OTO::where('status', 'PENDING_CX')->count(),
            'contacted' => \App\Models\OTO::where('status', 'CONTACTED')->count(),
            'accepted' => $acceptedOTOs->count(),
            'total_revenue' => $totalRevenue,
            'total_cancelled' => \App\Models\OTO::where('status', 'CANCELLED')->count(),
        ];
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
                'cx_assigned_to' => Auth::id(),
                'cx_contacted_at' => now(),
                'cx_contact_method' => $request->contact_method,
                'cx_notes' => $request->notes,
                'cx_follow_up_count' => $oto->cx_follow_up_count + 1,
            ]);
            
            // Log contact
            \App\Models\OTOContactLog::create([
                'oto_id' => $oto->id,
                'contacted_by' => Auth::id(),
                'contact_method' => $request->contact_method,
                'notes' => $request->notes,
                'customer_response' => $request->customer_response,
            ]);
            
            // If customer not interested, cancel OTO within transaction
            if ($request->customer_response === 'NOT_INTERESTED') {
                $oto->update(['status' => 'CANCELLED']);
                $oto->delete(); // Soft delete
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
            // Helper to parse "Rp. 115.000" back to numeric
            $parsePrice = fn($str) => (float) str_replace(['Rp. ', '.', ','], '', $str);
            $totalOTOPrice = $parsePrice($oto->total_oto_price);

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
                'oto_discount_amount' => $parsePrice($oto->total_discount), // Keep for reference
                'oto_priority_boost' => 30, // Significant boost for OTO
                'priority' => 'Express', // AUTOMATIC FAST TRACK as per user request
            ]);
            
            // Add services to work order
            // Since proposed_services is now a string like "Repaint, Deep Clean", we find services by name
            $serviceNames = explode(', ', (string) $oto->proposed_services);
            $needsSol = false;
            $needsUpper = false;
            $needsCleaning = false;

            foreach ($serviceNames as $name) {
                $service = \App\Models\Service::where('name', $name)->first();
                if ($service) {
                    $oto->workOrder->services()->attach($service->id, [
                        'cost' => $service->price, // Current price as fallback
                        'custom_name' => 'OTO: ' . $service->name,
                    ]);

                    $cat = strtolower($service->category);
                    if (str_contains($cat, 'sol') || str_contains($cat, 'reglue')) $needsSol = true;
                    if (str_contains($cat, 'upper') || str_contains($cat, 'jahit')) $needsUpper = true;
                    if (str_contains($cat, 'cleaning') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment') || str_contains($cat, 'whitening')) $needsCleaning = true;
                }
            }
            
            // Recalculate total
            $oto->workOrder->refresh(); 
            $oto->workOrder->total_service_price += $totalOTOPrice;
            $oto->workOrder->save();
            
            // Reset Workflow Timestamps for clean OTO tracking
            $resetData = [
                'status' => \App\Enums\WorkOrderStatus::PREPARATION,
                'taken_date' => null,
                'finished_date' => null,
                
                // Always reset QC columns (Timestamps AND Technicians)
                'qc_jahit_started_at' => null, 'qc_jahit_completed_at' => null, 'qc_jahit_technician_id' => null,
                'qc_cleanup_started_at' => null, 'qc_cleanup_completed_at' => null, 'qc_cleanup_technician_id' => null,
                'qc_final_started_at' => null, 'qc_final_completed_at' => null, 'qc_final_pic_id' => null,
            ];

            if ($needsSol) {
                $resetData['prod_sol_started_at'] = null;
                $resetData['prod_sol_completed_at'] = null;
                $resetData['prod_sol_by'] = null; 
            }
            
            if ($needsUpper) {
                $resetData['prod_upper_started_at'] = null;
                $resetData['prod_upper_completed_at'] = null;
                $resetData['prod_upper_by'] = null; 
            }
            
            if ($needsCleaning) {
                $resetData['prod_cleaning_started_at'] = null;
                $resetData['prod_cleaning_completed_at'] = null;
                $resetData['prod_cleaning_by'] = null; 
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
        
        $oto->delete(); // Soft delete
        
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
        
        $oto->delete(); // Soft delete
        
        return back()->with('success', 'OTO dibatalkan manual.');
    }
}
