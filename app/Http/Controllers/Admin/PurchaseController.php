<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['material', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $purchases = $query->get();

        $stats = [
            'total_pending' => Purchase::where('status', 'pending')->count(),
            'total_ordered' => Purchase::where('status', 'ordered')->count(),
            'total_unpaid' => Purchase::where('payment_status', 'unpaid')->sum('total_price'),
            'total_outstanding' => Purchase::whereIn('payment_status', ['unpaid', 'partial'])
                ->get()
                ->sum(function($p) { return $p->total_price - $p->paid_amount; }),
        ];

        return view('admin.purchases.index', compact('purchases', 'stats'));
    }

    public function create()
    {
        $materials = Material::orderBy('name')->get();
        return view('admin.purchases.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'order_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['po_number'] = 'PO-' . date('Ymd') . '-' . str_pad(Purchase::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
        $validated['status'] = 'pending';
        $validated['payment_status'] = 'unpaid';
        $validated['created_by'] = Auth::id();

        Purchase::create($validated);

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase order created successfully!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['material', 'creator']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $materials = Material::orderBy('name')->get();
        return view('admin.purchases.edit', compact('purchase', 'materials'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,ordered,received,cancelled',
            'order_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];

        // If status changed to received, update material stock
        if ($validated['status'] === 'received' && $purchase->status !== 'received') {
            $material = Material::find($validated['material_id']);
            $material->stock += $validated['quantity'];
            $material->save();
            
            $validated['received_date'] = now();
        }

        $purchase->update($validated);

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase updated successfully!');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return back()->with('error', 'Cannot delete received purchase!');
        }

        $purchase->delete();
        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase deleted successfully!');
    }

    public function updatePayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:' . $purchase->total_price,
        ]);

        $newPaidAmount = $purchase->paid_amount + $validated['paid_amount'];
        
        $purchase->paid_amount = $newPaidAmount;
        
        if ($newPaidAmount >= $purchase->total_price) {
            $purchase->payment_status = 'paid';
            
            // Auto-update status to 'ordered' if currently pending
            if ($purchase->status === 'pending') {
                $purchase->status = 'ordered';
            }
        } elseif ($newPaidAmount > 0) {
            $purchase->payment_status = 'partial';
        }
        
        $purchase->save();

        return back()->with('success', 'Payment updated successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:purchases,id',
        ]);

        Purchase::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.purchases.index')->with('success', count($request->ids) . ' pembelian berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $query = Purchase::with(['material', 'creator'])
            ->orderBy('created_at', 'desc');

        $rangeLabel = 'Semua Waktu';
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            
            $query->whereBetween('created_at', [
                $start->startOfDay(),
                $end->endOfDay()
            ]);
            
            $rangeLabel = $start->format('d M Y') . ' - ' . $end->format('d M Y');
        }

        $purchases = $query->get();

        // Calculate Analytics for this period
        $analytics = [
            'total_spend' => $purchases->sum('total_price'),
            'total_transactions' => $purchases->count(),
            'avg_rating' => $purchases->whereNotNull('quality_rating')->avg('quality_rating') ?? 0,
            'top_supplier' => '-',
        ];

        // Find Top Supplier
        if ($purchases->isNotEmpty()) {
            $topSupplier = $purchases->whereNotNull('supplier_name')
                ->groupBy('supplier_name')
                ->map(function ($row) {
                    return $row->sum('total_price');
                })
                ->sortDesc()
                ->keys()
                ->first();
            
            if ($topSupplier) {
                $analytics['top_supplier'] = $topSupplier;
            }
        }

        $pdf = Pdf::loadView('admin.purchases.pdf', compact('purchases', 'analytics', 'rangeLabel'));
        
        return $pdf->stream('laporan-pembelian-' . date('Y-m-d') . '.pdf');
    }
}
