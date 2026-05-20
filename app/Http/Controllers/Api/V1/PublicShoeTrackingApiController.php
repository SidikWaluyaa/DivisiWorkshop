<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PublicShoeTrackingApiController extends Controller
{
    /**
     * Track a shoe order using spk_number or database ID.
     * Returns a lightweight, clean JSON response containing shoe details and services.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function track(Request $request): JsonResponse
    {
        // Validate that at least one of spk_number or id is provided
        $validator = Validator::make($request->all(), [
            'spk_number' => 'required_without:id|string|nullable',
            'id' => 'required_without:spk_number|integer|nullable'
        ], [
            'required_without' => 'Silakan masukkan nomor SPK atau ID database untuk melacak.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $query = WorkOrder::query();

        if ($request->filled('spk_number')) {
            $spk = trim($request->query('spk_number'));

            // Normalize SPK if it contains slash / or backslash \ (copied from tracking utility)
            if (str_contains($spk, '/') || str_contains($spk, '\\')) {
                $parts = preg_split('/[\/\\\\]/', $spk);
                $lastPart = end($parts);
                if (!empty($lastPart)) {
                    $spk = trim((string)$lastPart);
                }
            }

            // Exclude private/restricted statuses for public safety (like CX followups/holds)
            $query->where(function ($q) use ($spk) {
                $q->where('spk_number', $spk)
                  ->orWhere('spk_number', 'LIKE', $spk);
            })->whereNotIn('status', [
                WorkOrderStatus::CX_FOLLOWUP->value,
                WorkOrderStatus::HOLD_FOR_CX->value
            ]);
        } else {
            $id = (int) $request->query('id');
            $query->where('id', $id)
                  ->whereNotIn('status', [
                      WorkOrderStatus::CX_FOLLOWUP->value,
                      WorkOrderStatus::HOLD_FOR_CX->value
                  ]);
        }

        // Eager load workOrderServices and their related service configurations
        $order = $query->with(['workOrderServices.service'])->first();

        // Handle not found
        if (!$order) {
            $identifier = $request->filled('spk_number') 
                ? "SPK '" . $request->query('spk_number') . "'" 
                : "ID '" . $request->query('id') . "'";

            return response()->json([
                'success' => false,
                'message' => "Data pelacakan tidak ditemukan untuk {$identifier}. Silakan periksa kembali."
            ], 404);
        }

        // Map services taken
        $services = $order->workOrderServices->map(function ($detail) {
            return [
                'service_name' => $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan'),
                'category_name' => $detail->category_name,
                'cost' => (float)$detail->cost,
                'status' => $detail->status ?? 'PENDING'
            ];
        });

        // Compile clean lightweight response
        return response()->json([
            'success' => true,
            'message' => "Data pelacakan sepatu ditemukan.",
            'data' => [
                'id' => $order->id,
                'spk_number' => $order->spk_number,
                'shoe_brand' => $order->shoe_brand,
                'shoe_type' => $order->shoe_type,
                'shoe_size' => $order->shoe_size,
                'shoe_color' => $order->shoe_color,
                'status' => is_object($order->status) ? $order->status->value : $order->status,
                'status_label' => is_object($order->status) ? $order->status->label() : '-',
                'services' => $services
            ]
        ]);
    }
}
