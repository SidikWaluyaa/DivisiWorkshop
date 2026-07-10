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
        $search = $request->get('search');

        // Active statuses = semua lead yang masih butuh tindakan CX
        $activeStatuses = ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER'];
        
        // Pool Query
        $query = \App\Models\OTO::with(['workOrder', 'creator', 'contactLogs.contactedBy']);
        
        if ($filter === 'pending') {
            // New Leads = belum pernah dicontact sama sekali
            $query->where('status', 'PENDING_CX');
        } elseif ($filter === 'contacted') {
            // Follow Up = sudah dicontact, termasuk yang pending customer response
            $query->whereIn('status', ['CONTACTED', 'PENDING_CUSTOMER']);
        } elseif ($filter === 'accepted') {
            $query->where('status', 'ACCEPTED');
        } elseif ($filter === 'cancelled') {
            $query->whereIn('status', ['CANCELLED', 'REJECTED']);
        } elseif ($filter === 'urgent') {
            // Urgent = active lead yang expired dalam 3 hari
            $query->whereIn('status', $activeStatuses)
                  ->where('valid_until', '<=', now()->addDays(3));
        } elseif ($filter === 'my') {
            // My OTO = active lead yang di-assign ke user ini
            $query->whereIn('status', $activeStatuses)
                  ->where('cx_assigned_to', Auth::id());
        } else {
            // All = semua active leads
            $query->whereIn('status', $activeStatuses);
        }

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('workOrder', function($wq) use ($search) {
                    $wq->where('spk_number', 'like', "%{$search}%")
                       ->orWhere('customer_name', 'like', "%{$search}%")
                       ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            });
        }

        $otos = $query->latest()->paginate(10);

        $parsePrice = function($priceStr) {
            return (int) preg_replace('/[^0-9]/', '', $priceStr);
        };

        // Success Metrics (Last 30 Days)
        $acceptedOTOs = \App\Models\OTO::where('status', 'ACCEPTED')->where('updated_at', '>=', now()->subDays(30))->get();
        $totalProcessed = \App\Models\OTO::whereIn('status', ['ACCEPTED', 'CANCELLED', 'REJECTED'])->where('updated_at', '>=', now()->subDays(30))->count();
        
        $stats = [
            // Active Leads = semua status yang masih butuh tindakan
            'pending'         => \App\Models\OTO::where('status', 'PENDING_CX')->count(),
            'contacted'       => \App\Models\OTO::whereIn('status', ['CONTACTED', 'PENDING_CUSTOMER'])->count(),
            'active_total'    => \App\Models\OTO::whereIn('status', $activeStatuses)->count(),
            'total_potential' => \App\Models\OTO::whereIn('status', $activeStatuses)->get()->sum(fn($o) => $parsePrice($o->total_oto_price)),
            'total_achieved'  => $acceptedOTOs->sum(fn($o) => $parsePrice($o->total_oto_price)),
            'closing_rate'    => $totalProcessed > 0 ? round(($acceptedOTOs->count() / $totalProcessed) * 100, 1) : 0,
            'count_achieved'  => $acceptedOTOs->count(),
        ];
        
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
                // INTERESTED = tunggu konfirmasi customer → PENDING_CUSTOMER (tetap active di pool)
                // Selain itu = CONTACTED (masuk Follow Up)
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
            
            // [AUDIT LOG] Record OTO contact
            \App\Models\WorkOrderLog::create([
                'work_order_id' => $oto->work_order_id,
                'user_id' => Auth::id(),
                'step' => 'SELESAI',
                'action' => 'OTO_CONTACTED',
                'description' => "CX menghubungi customer via {$request->contact_method}. Respon: {$request->customer_response}. Jasa: {$oto->proposed_services}"
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

                // [AUDIT LOG] OTO cancelled due to not interested
                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $oto->work_order_id,
                    'user_id' => Auth::id(),
                    'step' => 'SELESAI',
                    'action' => 'OTO_CANCELLED',
                    'description' => "OTO dibatalkan: Customer tidak tertarik. Jasa: {$oto->proposed_services}"
                ]);
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
                'priority' => 'OTO', // AUTOMATIC FAST TRACK as per user request
            ]);
            
            // Add services to work order
            // Since proposed_services is now a string like "Repaint, Deep Clean", we find services by name
            $serviceNames = explode(', ', (string) $oto->proposed_services);
            $needsSol = false;
            $needsUpper = false;
            $needsCleaning = false;

            foreach ($serviceNames as $name) {
                // 1. Find OTO service to get the promotional OTO price
                $otoService = \App\Models\Service::where('name', $name)->where('category', 'OTO')->first();

                // 2. Find regular service to get the actual production category
                $regularService = \App\Models\Service::where('name', $name)->where('category', '!=', 'OTO')->first();

                $serviceToAttach = null;
                $cost = 0;

                if ($otoService && $regularService) {
                    // Match: Attach regular service for correct workflow, but overwrite cost with OTO price
                    $serviceToAttach = $regularService;
                    $cost = (float) $otoService->price;
                } elseif ($otoService) {
                    // Fallback to OTO service itself if no regular service exists
                    $serviceToAttach = $otoService;
                    $cost = (float) $otoService->price;
                } else {
                    // General fallback
                    $serviceToAttach = $regularService ?: \App\Models\Service::where('name', $name)->first();
                    $cost = $serviceToAttach ? (float) $serviceToAttach->price : 0;
                }

                if ($serviceToAttach) {
                    // Determine production category name
                    $categoryName = $serviceToAttach->category ?: 'Repaint';

                    // If OTO category fallback, infer actual production category from name
                    if ($categoryName === 'OTO') {
                        $lowerName = strtolower($serviceToAttach->name);
                        if (str_contains($lowerName, 'sol') || str_contains($lowerName, 'reglue')) {
                            $categoryName = 'Reparasi Sol';
                        } elseif (str_contains($lowerName, 'upper') || str_contains($lowerName, 'jahit')) {
                            $categoryName = 'Reparasi Upper';
                        } else {
                            $categoryName = 'Repaint';
                        }
                    }

                    $oto->workOrder->services()->attach($serviceToAttach->id, [
                        'cost' => $cost,
                        'custom_service_name' => 'OTO: ' . $serviceToAttach->name,
                        'category_name' => $categoryName,
                    ]);

                    $cat = strtolower($categoryName);
                    if (str_contains($cat, 'sol') || str_contains($cat, 'reglue')) $needsSol = true;
                    if (str_contains($cat, 'upper') || str_contains($cat, 'jahit')) $needsUpper = true;
                    if (str_contains($cat, 'cleaning') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment') || str_contains($cat, 'whitening')) $needsCleaning = true;
                }
            }
            
            // Recalculate total and sync financials
            $oto->workOrder->refresh(); 
            $oto->workOrder->recalculateTotalPrice(true);
            
            $otoHkDays = (int) ($oto->estimated_days ?? 0);

            // Reset Workflow Timestamps for clean OTO tracking
            $resetData = [
                'status' => \App\Enums\WorkOrderStatus::PRODUCTION, // Direct to Production as requested
                'taken_date' => null,
                'finished_date' => null,
                'has_active_oto' => true, // Keep flag active for UI badges
                'hk_days' => ($oto->workOrder->hk_days ?? 0) + $otoHkDays, // Add OTO HK to SPK
                
                // Always reset QC columns (Timestamps AND Technicians) to ensure re-verification
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

            // [AUDIT LOG] Record OTO acceptance
            \App\Models\WorkOrderLog::create([
                'work_order_id' => $oto->work_order_id,
                'user_id' => Auth::id(),
                'step' => 'PRODUCTION',
                'action' => 'OTO_ACCEPTED',
                'description' => "Customer SETUJU OTO: {$oto->proposed_services} (Harga: {$oto->total_oto_price}). Order kembali ke Produksi."
            ]);
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

        // [AUDIT LOG] Record OTO rejection
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $oto->work_order_id,
            'user_id' => Auth::id(),
            'step' => 'SELESAI',
            'action' => 'OTO_REJECTED',
            'description' => "Customer MENOLAK OTO: {$oto->proposed_services}" . ($request->note ? ". Alasan: {$request->note}" : '')
        ]);
        
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

        // [AUDIT LOG] Record OTO cancellation
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $oto->work_order_id,
            'user_id' => Auth::id(),
            'step' => 'SELESAI',
            'action' => 'OTO_CANCELLED',
            'description' => "OTO dibatalkan oleh CX. Jasa: {$oto->proposed_services}"
        ]);
        
        $oto->delete(); // Soft delete
        
        return back()->with('success', 'OTO dibatalkan manual.');
    }
}
