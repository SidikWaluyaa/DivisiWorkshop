<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Shipping::with('workOrder');

        // Filter by Search (Customer Name, Phone, SPK, Resi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('spk_number', 'like', "%{$search}%")
                  ->orWhere('resi_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by Status (Verifikasi)
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        // Filter by Tanggal Masuk
        if ($request->filled('date_start')) {
            $query->whereDate('tanggal_masuk', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('tanggal_masuk', '<=', $request->date_end);
        }

        $shippings = $query->orderBy('tanggal_masuk', 'desc')
            ->paginate(50)
            ->withQueryString();

        // Get technicians for the PIC dropdown (Gudang role)
        $technicians = \App\Models\User::where('role', 'gudang')->get();

        return view('shipping.index', compact('shippings', 'technicians'));
    }

    public function update(Request $request, $id)
    {
        $shipping = \App\Models\Shipping::findOrFail($id);

        $validated = $request->validate([
            'is_verified' => 'boolean',
            'kategori_pengiriman' => 'nullable|string|in:Ojek Online,Ambil Sendiri,Ekspedisi',
            'tanggal_pengiriman' => 'nullable|date',
            'pic' => 'nullable|string|max:255',
            'resi_pengiriman' => 'nullable|string|max:255',
        ]);

        $shipping->update([
            'is_verified' => $request->has('is_verified') ? $request->is_verified : false,
            'kategori_pengiriman' => $request->kategori_pengiriman,
            'tanggal_pengiriman' => $request->tanggal_pengiriman,
            'pic' => $request->pic,
            'resi_pengiriman' => $request->resi_pengiriman,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data pengiriman berhasil diperbarui.']);
        }

        return back()->with('success', 'Data pengiriman berhasil diperbarui.');
    }
}
