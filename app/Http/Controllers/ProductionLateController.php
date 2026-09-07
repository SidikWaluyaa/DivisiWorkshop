<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductionLateController extends Controller
{
    /**
     * Display the Late Production monitoring page.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::productionLate();
        
        // Status Filter
        if ($request->filled('status')) {
            $status = strtoupper($request->status);
            if (in_array($status, ['LATE', 'WARNING', 'ON TRACK'])) {
                $query->having('warning_status', '=', $status);
            }
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->paginate(50)->withQueryString();
        
        return view('production.late-info', compact('orders'));
    }

    /**
     * Update the description/reason for a late production order via AJAX.
     */
    public function updateDescription(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'description' => 'nullable|string'
        ]);

        $order = WorkOrder::findOrFail($request->id);
        $order->late_description = $request->description;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Deskripsi berhasil diperbarui.'
        ]);
    }

    /**
     * Update the new estimation date for a late production order via AJAX.
     */
    public function updateNewEstimationDate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'new_estimation_date' => 'nullable|date'
        ]);

        $order = WorkOrder::findOrFail($request->id);
        $order->new_estimation_date = $request->new_estimation_date;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Estimasi baru berhasil diperbarui.'
        ]);
    }

    /**
     * Update the material arrival date for a production order via AJAX.
     */
    public function updateMaterialArrivalDate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'material_arrival_date' => 'nullable|date'
        ]);

        $order = WorkOrder::findOrFail($request->id);
        $order->material_arrival_date = $request->material_arrival_date;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tanggal kedatangan material berhasil diperbarui.'
        ]);
    }

    /**
     * Update the material name for a production order via AJAX.
     */
    public function updateMaterialName(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'material_name' => 'nullable|string|max:255'
        ]);

        $order = WorkOrder::findOrFail($request->id);
        $order->material_name = $request->material_name;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Nama material berhasil diperbarui.'
        ]);
    }

    /**
     * Handle material photo upload for a production order via AJAX.
     */
    public function uploadMaterialPhoto(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'photo' => 'required|image|max:5120' // Max 5MB
        ]);

        $order = WorkOrder::findOrFail($request->id);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($order->material_photo_path) {
                Storage::disk('public')->delete(str_replace('storage/', '', $order->material_photo_path));
            }

            $path = $request->file('photo')->store('material_photos', 'public');
            $order->material_photo_path = 'storage/' . $path;
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Foto material berhasil diunggah.',
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengunggah foto.'
        ], 400);
    }

    /**
     * Display a dedicated page for material information (photo and arrival date).
     */
    public function materialInfo($id)
    {
        $order = WorkOrder::findOrFail($id);
        
        return view('production.material-info', compact('order'));
    }

    /**
     * Delete the material photo for a production order via AJAX.
     */
    public function deleteMaterialPhoto(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id'
        ]);

        $order = WorkOrder::findOrFail($request->id);

        if ($order->material_photo_path) {
            Storage::disk('public')->delete(str_replace('storage/', '', $order->material_photo_path));
            $order->material_photo_path = null;
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Foto material berhasil dihapus.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Tidak ada foto untuk dihapus.'
        ], 400);
    }

    /**
     * Export late production orders to Excel for field physical audit.
     */
    public function export(Request $request)
    {
        $query = WorkOrder::productionLate()
            ->with([
                'workOrderServices',
                'services',
                'prodUpperBy',
                'prodSolBy',
                'qcJahitBy',
                'storageAssignments.rack'
            ]);
        
        // Status Filter
        if ($request->filled('status')) {
            $status = strtoupper($request->status);
            if (in_array($status, ['LATE', 'WARNING', 'ON TRACK'])) {
                $query->having('warning_status', '=', $status);
            }
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->get();
        $fileName = 'Audit_Cek_Fisik_Produksi_Terlambat_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ProductionLateExport($orders, $request->status, $request->search),
            $fileName
        );
    }
}

