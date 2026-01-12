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

        $input = $request->spk_number;

        // Check if input looks like a phone number (numeric, at least 9 digit)
        // Clean non-numeric characters for check
        $cleanInput = preg_replace('/[^0-9]/', '', $input);
        
        $isPhone = is_numeric($cleanInput) && strlen($cleanInput) >= 9;

        if ($isPhone) {
            // Search by Phone Number
            // Remove 'Active Only' constraint to ensure they can see Finished/Taken active orders too
            $orders = WorkOrder::where('customer_phone', 'LIKE', "%{$input}%")
                        ->with(['services', 'logs.user', 'materials', 'photos'])
                        ->orderBy('created_at', 'desc')
                        ->get();
            
            // If no active orders found, maybe check history? 
            // For now, just show what we found.
            
        } else {
            // Search by SPK (Exact match)
            $order = WorkOrder::where('spk_number', $input)
                        ->with(['services', 'logs.user', 'materials', 'photos'])
                        ->first();
            
            $orders = $order ? collect([$order]) : collect();
        }

        if ($orders->isEmpty()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan (SPK/No WA). Silakan periksa kembali.'
                ]);
            }
            return back()->with('error', 'Data tidak ditemukan (SPK/No WA). Silakan periksa kembali.');
        }

        if ($request->ajax()) {
            // Transform data for modal
            $data = $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'spk_number' => $order->spk_number,
                    'customer_name' => $order->customer_name,
                    'shoe_brand' => $order->shoe_brand . ' - ' . $order->shoe_type,
                    'shoe_color' => $order->shoe_color,
                    'status' => $order->status,
                    'total_price' => number_format($order->total_price, 0, ',', '.'),
                    'entry_date' => $order->entry_date->format('d/m/Y'),
                    'estimation_date' => $order->estimation_date ? $order->estimation_date->format('d/m/Y') : '-',
                    'detail_url' => route('tracking.index') . '?spk_number=' . $order->spk_number // Simple link to full detail
                ];
            });

            return response()->json([
                'success' => true,
                'is_phone' => $isPhone,
                'data' => $data
            ]);
        }

        return view('tracking.result', compact('orders', 'input', 'isPhone'));
    }
}
