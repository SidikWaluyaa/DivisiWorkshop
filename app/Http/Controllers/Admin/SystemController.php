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
            
            // Transactional Tables
            DB::table('complaints')->truncate();
            DB::table('work_order_logs')->truncate();
            DB::table('work_order_photos')->truncate();
            DB::table('work_order_materials')->truncate(); // Pivot
            DB::table('work_order_services')->truncate(); // Pivot
            DB::table('work_orders')->truncate();
            
            // Optional: Reset Purchases if they are considered transactional test data
            // DB::table('purchases')->truncate(); 
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Log::warning('Database reset performed by user: ' . ($request->user()->id ?? 'Unknown'));

            return redirect()->back()->with('success', 'Data Transaksi berhasil di-reset. Master Data (User, Layanan, Material) aman.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }
}
