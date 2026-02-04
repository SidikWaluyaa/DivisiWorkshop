<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;

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

        $input = $this->parseInput($request->spk_number);
        $isPhone = $this->isPhoneNumber($input);

        \Illuminate\Support\Facades\Log::info("Tracking Search: Input=['{$input}'] IsPhone=" . ($isPhone ? 'Yes' : 'No'));

        $orders = $isPhone ? $this->searchByPhone($input) : $this->searchBySpk($input);

        if ($orders->isEmpty()) {
            return $this->handleNotFound($request, $input);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_phone' => $isPhone,
                'data' => $this->transformOrdersForAjax($orders)
            ]);
        }

        return view('tracking.result', compact('orders', 'input', 'isPhone'));
    }

    private function parseInput(string $input): string
    {
        $input = trim($input);
        if (str_contains($input, '/') || str_contains($input, '\\')) {
            $parts = preg_split('/[\/\\\\]/', $input);
            $lastPart = end($parts);
            if (!empty($lastPart)) {
                $input = trim((string)$lastPart);
            }
        }
        return $input;
    }

    private function isPhoneNumber(string $input): bool
    {
        $cleanInput = preg_replace('/[^0-9]/', '', $input);
        $hasLetters = (bool)preg_match('/[a-zA-Z]/', $input);
        return !$hasLetters && is_numeric($cleanInput) && strlen($cleanInput) >= 9;
    }

    private function searchByPhone(string $phone): \Illuminate\Support\Collection
    {
        return WorkOrder::where('customer_phone', 'LIKE', "%{$phone}%")
            ->whereNotIn('status', [
                WorkOrderStatus::CX_FOLLOWUP->value,
                WorkOrderStatus::HOLD_FOR_CX->value
            ])
            ->with(['services', 'workOrderServices', 'logs.user', 'materials', 'photos'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function searchBySpk(string $spk): \Illuminate\Support\Collection
    {
        $order = WorkOrder::where('spk_number', $spk)
            ->whereNotIn('status', [
                WorkOrderStatus::CX_FOLLOWUP->value,
                WorkOrderStatus::HOLD_FOR_CX->value
            ])
            ->with(['services', 'workOrderServices', 'logs.user', 'materials', 'photos'])
            ->first();

        if (!$order) {
            $order = WorkOrder::where('spk_number', 'LIKE', $spk)
                ->whereNotIn('status', [
                    WorkOrderStatus::CX_FOLLOWUP->value,
                    WorkOrderStatus::HOLD_FOR_CX->value
                ])
                ->first();
        }

        return $order ? collect([$order]) : collect();
    }

    private function transformOrdersForAjax(\Illuminate\Support\Collection $orders): \Illuminate\Support\Collection
    {
        return $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'shoe_brand' => $order->shoe_brand . ' - ' . $order->shoe_type,
                'shoe_color' => $order->shoe_color,
                'status' => $order->status,
                'total_price' => number_format($order->total_price ?? 0, 0, ',', '.'),
                'entry_date' => $order->entry_date->format('d/m/Y'),
                'estimation_date' => $order->estimation_date ? $order->estimation_date->format('d/m/Y') : '-',
                'detail_url' => route('tracking.index') . '?spk_number=' . $order->spk_number
            ];
        });
    }

    private function handleNotFound(Request $request, string $input)
    {
        $hexInput = bin2hex($input);
        $message = "Data tidak ditemukan untuk: '{$input}' (Hex: {$hexInput}). Silakan periksa kembali.";

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }

        return back()->with('error', $message);
    }
}
