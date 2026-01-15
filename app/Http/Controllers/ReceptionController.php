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


    public function exportExcel()
    {
        return Excel::download(new \App\Exports\ReceptionExport, 'gudang_penerimaan_' . date('Y-m-d_His') . '.xlsx');
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
            if ($request->priority == 'Prioritas') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } elseif ($request->priority == 'Reguler') {
                $query->whereIn('priority', ['Reguler', 'Normal']);
            } else {
                $query->where('priority', $request->priority);
            }
        }

        // Sort and Paginate
        // Prioritize: Prioritas/Urgent/Express (1) > Reguler/Normal (2)
        $query->orderByRaw("CASE 
            WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 1 
            ELSE 2 
        END ASC");
        
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
            DB::transaction(function () use ($request) {
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


    public function store(Request $request)
    {
        $validated = $request->validate([
            'spk_number' => 'nullable|string|max:255|unique:work_orders,spk_number',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:100',
            'entry_date' => 'required|date',
            'estimation_date' => 'required|date|after_or_equal:entry_date',
            'priority' => 'required|in:Normal,Urgent,Express,Reguler,Prioritas',
        ]);

        // Auto-generate SPK if not provided
        if (empty($validated['spk_number'])) {
            $validated['spk_number'] = 'SPK-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        }

        $validated['status'] = WorkOrderStatus::DITERIMA->value;
        $validated['current_location'] = 'Gudang Penerimaan';
        $validated['created_by'] = \Illuminate\Support\Facades\Auth::id();

        try {
            $order = WorkOrder::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil ditambahkan!',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan order: ' . $e->getMessage()
            ], 500);
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
            DB::beginTransaction();
            
            foreach ($request->ids as $id) {
                $order = WorkOrder::find($id);
                
                if ($order) {
                    // Delete related records
                    $order->services()->detach();
                    $order->materials()->detach();
                    $order->logs()->delete();
                    $order->photos()->delete();
                    $order->complaints()->delete();
                    
                    // Now soft delete the order
                    $order->delete();
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', count($request->ids) . ' data order berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    

    public function updateShoeInfo(Request $request, $id)
    {
        $validated = $request->validate([
            'shoe_brand' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:50',
            'shoe_color' => 'required|string|max:100',
        ]);

        try {
            $order = WorkOrder::findOrFail($id);
            $order->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Info sepatu berhasil diupdate!',
                'shoe_brand' => $order->shoe_brand,
                'shoe_size' => $order->shoe_size,
                'shoe_color' => $order->shoe_color,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update info sepatu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email|max:255'
        ]);

        try {
            $order = WorkOrder::findOrFail($id);
            $order->customer_email = $request->email;
            $order->save();

            return response()->json([
                'success' => true, 
                'message' => 'Email berhasil diupdate.',
                'email' => $order->customer_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal update email: ' . $e->getMessage()
            ]);
        }
    }

    public function sendEmail($id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            
            if (!$order->customer_email) {
                return response()->json(['success' => false, 'message' => 'Email customer tidak tersedia.']);
            }

            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderReceived($order));

            return response()->json(['success' => true, 'message' => 'Nota digital berhasil dikirim ke email customer.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }
}
