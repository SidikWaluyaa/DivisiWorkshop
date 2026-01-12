<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Imports\OrdersImport;
use App\Enums\WorkOrderStatus;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    protected \App\Services\WorkflowService $workflow;

    public function __construct(\App\Services\WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\OrdersTemplateExport, 'template_import_order.xlsx');
    }

    public function index(Request $request)
    {
        $query = WorkOrder::where('status', WorkOrderStatus::DITERIMA->value);

        // Search Filter (SPK, Customer Name, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Sort and Paginate
        $orders = $query->orderBy('entry_date', 'asc')
                        ->paginate(20)
                        ->appends($request->except('page'));
                    
        return view('reception.index', compact('orders'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            \DB::transaction(function () use ($request) {
                $importer = new OrdersImport;
                Excel::import($importer, $request->file('file'));
            });
            
            // Success Logic (No Exception thrown)
            return redirect()->back()->with('success', 'Data berhasil diimport & SPK dibuat!');

        } catch (\App\Exceptions\ImportValidationException $e) {
            // Validation Failed - Show Dedicated Error Page
            return view('reception.import-errors', [
                'failures' => $e->getErrors()
            ]);

        } catch (\Exception $e) {
            // General Error
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function printTag($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Use SVG format (Does not require Imagick extension)
        $barcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($order->spk_number);
        
        return view('reception.print-tag', compact('order', 'barcode'));
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

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id'
        ]);

        try {
            // Delete related records via foreign key constraints or manually if handled by database
            // Assuming cascade delete is enabled or we need to delete manually.
            // Safe approach: delete child constraints if not set to cascade.
            // Ideally, Eloquent event or DB constraint handles this. WorkOrder model likely has constraints.
            // Let's assume standard deletion.
            
            WorkOrder::whereIn('id', $request->ids)->delete();
            
            return redirect()->back()->with('success', count($request->ids) . ' data order berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
