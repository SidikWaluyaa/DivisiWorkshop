<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    public function index()
    {
        return view('admin.system.index');
    }

    public function reset(Request $request)
    {
        // Require password confirmation for security (optional, but good practice)
        // For now, simple confirmation
        
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // 1. Workshop & Core Transactions
            DB::table('complaints')->truncate();
            DB::table('work_order_logs')->truncate();
            DB::table('work_order_photos')->truncate();
            DB::table('work_order_materials')->truncate(); // Pivot
            DB::table('work_order_services')->truncate(); // Pivot
            DB::table('work_orders')->truncate();
            DB::table('order_payments')->truncate();
            
            // 2. Storage & Warehouse
            DB::table('storage_assignments')->truncate();
            
            // 3. Customer Service (CS) Pipeline
            DB::table('cs_activities')->truncate();
            DB::table('cs_spk_items')->truncate();
            DB::table('cs_spk')->truncate();
            DB::table('cs_quotation_items')->truncate();
            DB::table('cs_quotations')->truncate();
            DB::table('cs_leads')->truncate();

            // 4. Customer Experience (CX) & OTO
            DB::table('cx_issues')->truncate();
            DB::table('oto_contact_logs')->truncate();
            DB::table('material_reservations')->truncate();
            DB::table('otos')->truncate();

            // 5. Procurement / Material Requests
            DB::table('material_request_items')->truncate();
            DB::table('material_requests')->truncate();
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Log::warning('Global Transactional Reset performed by user: ' . ($request->user()->id ?? 'Unknown'));

            return redirect()->back()->with('success', 'Seluruh Data Transaksi berhasil di-reset. Master Data aman.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }

    public function cleanupOrphanedStorage()
    {
        try {
            $orphanedCount = DB::table('storage_assignments')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('work_orders')
                        ->whereRaw('work_orders.id = storage_assignments.work_order_id');
                })->delete();

            return redirect()->back()->with('success', "Berhasil membersihkan {$orphanedCount} data storage tak bertuan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membersihkan storage: ' . $e->getMessage());
        }
    }
}
