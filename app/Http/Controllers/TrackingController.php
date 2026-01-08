<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'spk_number' => 'required|string'
        ]);

        $order = WorkOrder::where('spk_number', $request->spk_number)
                    ->with(['services', 'logs.user', 'materials'])
                    ->first();

        if (!$order) {
            return back()->with('error', 'Nomor SPK tidak ditemukan. Silakan periksa kembali.');
        }

        return view('tracking.result', compact('order'));
    }
}
