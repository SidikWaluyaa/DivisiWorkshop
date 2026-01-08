<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Imports\OrdersImport;
use App\Enums\WorkOrderStatus;
use Maatwebsite\Excel\Facades\Excel;

class ReceptionController extends Controller
{
    protected \App\Services\WorkflowService $workflow;

    public function __construct(\App\Services\WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // Show orders that are currently in 'DITERIMA' status
        $orders = WorkOrder::where('status', WorkOrderStatus::DITERIMA->value)
                    ->orderBy('created_at', 'asc')
                    ->get();
                    
        return view('reception.index', compact('orders'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new OrdersImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport & SPK dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function printTag($id)
    {
        $order = WorkOrder::findOrFail($id);
        // Return a print view/pdf for the barcode tag
        return view('reception.print-tag', compact('order'));
    }

    public function process($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            // Move to ASSESSMENT directly
            $this->workflow->updateStatus($order, WorkOrderStatus::ASSESSMENT, 'Dikirim dari Reception ke Assessment');

            return redirect()->back()->with('success', 'Sepatu berhasil dikirim ke Assessment!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        try {
            // Delete all data to allow fresh import
            // Constraints: child tables first
            \Illuminate\Support\Facades\DB::table('work_order_services')->delete();
            \Illuminate\Support\Facades\DB::table('work_order_materials')->delete();
            \App\Models\WorkOrderLog::query()->delete();
            WorkOrder::query()->delete();
            
            return redirect()->back()->with('success', 'Semua data Work Order berhasil dihapus. Silakan Import ulang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal reset data: ' . $e->getMessage());
        }
    }
}
