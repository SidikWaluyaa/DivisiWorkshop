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

        $input = trim($request->spk_number); // Trim whitespace
        
        // Handle URL-like input (e.g. http://site.com/track/N-123)
        if (str_contains($input, '/') || str_contains($input, '\\')) {
            $parts = preg_split('/[\/\\\\]/', $input);
            $lastPart = end($parts);
            if (!empty($lastPart)) {
                $input = trim($lastPart);
            }
        }

        // Check if input looks like a phone number (numeric, at least 9 digit, NO LETTERS)
        $cleanInput = preg_replace('/[^0-9]/', '', $input);
        $hasLetters = preg_match('/[a-zA-Z]/', $input);
        
        $isPhone = !$hasLetters && is_numeric($cleanInput) && strlen($cleanInput) >= 9;

        \Illuminate\Support\Facades\Log::info("Tracking Search: Input=['{$input}'] Clean=['{$cleanInput}'] IsPhone=".($isPhone?'Yes':'No'));

        if ($isPhone) {
            // Search by Phone Number
            // Remove 'Active Only' constraint to ensure they can see Finished/Taken active orders too
            $orders = WorkOrder::where('customer_phone', 'LIKE', "%{$input}%")
                        ->with(['services', 'workOrderServices', 'logs.user', 'materials', 'photos'])
                        ->orderBy('created_at', 'desc')
                        ->get();
            
            // If no active orders found, maybe check history? 
            // For now, just show what we found.
            
        } else {
            // Search by SPK (Try exact match first, usually case-insensitive in MySQL)
            $order = WorkOrder::where('spk_number', $input)
                        ->with(['services', 'workOrderServices', 'logs.user', 'materials', 'photos'])
                        ->first();
            
            // Fallback: Try finding with LIKE if strict match fails (handles trailing spaces or hidden chars in DB better)
            if (!$order) {
                 $order = WorkOrder::where('spk_number', 'LIKE', $input)->first();
            }
            
            \Illuminate\Support\Facades\Log::info("SPK Search '{$input}' Result: " . ($order ? 'Found' : 'Not Found'));
            
            $orders = $order ? collect([$order]) : collect();
        }

        if ($orders->isEmpty()) {
            $hexInput = bin2hex($input);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan untuk: '{$input}' (Hex: {$hexInput}). Silakan periksa kembali."
                ]);
            }
            return back()->with('error', "Data tidak ditemukan untuk: '{$input}' (Hex: {$hexInput}). Silakan periksa kembali.");
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
