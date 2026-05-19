<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\JsonResponse;

class PublicTrackingApiController extends Controller
{
    /**
     * Fetch complete tracking details for a given SPK number.
     * Accessible by allowed CORS origins (e.g. shoeworkshop.id) without API keys.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'spk_number' => 'required|string'
        ]);

        $spk = trim($request->query('spk_number'));

        // Handle possible / or \ in input (matching TrackingController parseInput logic)
        if (str_contains($spk, '/') || str_contains($spk, '\\')) {
            $parts = preg_split('/[\/\\\\]/', $spk);
            $lastPart = end($parts);
            if (!empty($lastPart)) {
                $spk = trim((string)$lastPart);
            }
        }

        // Query the WorkOrder with relationships
        $order = WorkOrder::where('spk_number', $spk)
            ->whereNotIn('status', [
                WorkOrderStatus::CX_FOLLOWUP->value,
                WorkOrderStatus::HOLD_FOR_CX->value
            ])
            ->with(['services', 'workOrderServices', 'logs.user', 'materials', 'photos'])
            ->first();

        // Fallback exact match using LIKE if exact = fails
        if (!$order) {
            $order = WorkOrder::where('spk_number', 'LIKE', $spk)
                ->whereNotIn('status', [
                    WorkOrderStatus::CX_FOLLOWUP->value,
                    WorkOrderStatus::HOLD_FOR_CX->value
                ])
                ->with(['services', 'workOrderServices', 'logs.user', 'materials', 'photos'])
                ->first();
        }

        // Handle case where SPK is not found
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => "Data tidak ditemukan untuk SPK: '{$spk}'. Silakan periksa kembali."
            ], 404);
        }

        // Define tracking stages matching result.blade.php
        $stages = [
            'DITERIMA' => [
                'label' => 'Terima',
            ],
            'ASSESSMENT' => [
                'label' => 'Pengecekan',
            ],
            'PREPARATION' => [
                'label' => 'Cuci',
            ],
            'SORTIR' => [
                'label' => 'Persiapan Bahan',
            ],
            'PRODUCTION' => [
                'label' => 'Service',
            ],
            'QC' => [
                'label' => 'QC Checking',
            ],
            'SELESAI' => [
                'label' => 'Selesai Reparasi',
            ],
        ];

        $stageKeys = array_keys($stages);
        $currentStatusVal = is_object($order->status) ? $order->status->value : $order->status;
        
        $currentIndex = array_search($currentStatusVal, $stageKeys);
        if ($currentIndex === false && is_object($order->status)) {
            $currentIndex = array_search($order->status->name, $stageKeys);
        }

        // Visual enhancement overrides from result.blade.php
        // 1. If in PRODUCTION but production is finished or revising -> move visual indicator to QC
        if ($currentStatusVal === 'PRODUCTION' && ($order->is_production_finished || $order->is_revising)) {
            $currentIndex = 5; // Index of QC
        }

        // 2. If in QC but QC is finished -> move visual indicator to SELESAI
        if ($currentStatusVal === 'QC' && $order->is_qc_finished) {
            $currentIndex = 6; // Index of SELESAI
        }

        // Construct current status details
        $currentStatusDetails = [
            'code' => $currentStatusVal,
            'label' => '-',
            'description' => '-',
            'is_production_finished' => (bool)$order->is_production_finished,
            'is_qc_finished' => (bool)$order->is_qc_finished,
        ];

        if ($currentIndex !== false && isset($stageKeys[$currentIndex])) {
            $activeKey = $stageKeys[$currentIndex];
            $currentStatusDetails['label'] = $stages[$activeKey]['label'];

            $nextLabel = ($currentIndex < count($stages) - 1) ? $stages[$stageKeys[$currentIndex + 1]]['label'] : null;
            
            $desc = "Order Anda sedang dalam proses " . strtolower($stages[$activeKey]['label']) . ".";
            if ($nextLabel) {
                $desc .= " Langkah berikutnya: " . $nextLabel . ".";
            } else {
                $desc .= " Terima kasih telah mempercayakan sepatu Anda kepada kami!";
            }
            $currentStatusDetails['description'] = $desc;
        }

        // Format timeline with complete status dates/timestamps
        $timeline = [];
        foreach ($stages as $key => $stageInfo) {
            $index = array_search($key, $stageKeys);
            $isCompleted = $index <= $currentIndex;
            $isCurrent = $index === $currentIndex;

            // Get exact timestamp
            $timestamp = null;
            if ($key === 'DITERIMA') {
                $timestamp = $order->created_at;
            } elseif ($key === 'SELESAI' && $order->finished_date) {
                $timestamp = $order->finished_date;
            } else {
                $log = $order->logs->where('step', $key)->sortByDesc('created_at')->first();
                if ($log) {
                    $timestamp = $log->created_at;
                }
            }

            $timeline[$key] = [
                'label' => $stageInfo['label'],
                'is_completed' => $isCompleted,
                'is_current' => $isCurrent,
                'waktu' => $timestamp ? $timestamp->format('Y-m-d H:i:s') : null
            ];
        }

        // Gather services
        $services = $order->workOrderServices->map(function ($detail) {
            return [
                'service_name' => $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan'),
                'category' => $detail->category_name,
                'cost' => (float)$detail->cost
            ];
        });

        // Gather photos
        $beforePhotoUrl = $order->spk_cover_photo_url;
        
        $afterPhoto = $order->photos->where('step', 'FINISH')->last() 
            ?? $order->photos->where('step', 'SELESAI')->last();
        $afterPhotoUrl = $afterPhoto ? $afterPhoto->photo_url : null;
        
        $heroPhoto = $order->photos->where('is_public', true)->last();
        $heroPhotoUrl = $heroPhoto ? $heroPhoto->photo_url : null;

        // Compile response
        return response()->json([
            'success' => true,
            'message' => "Data pelacakan ditemukan.",
            'data' => [
                'spk_number' => $order->spk_number,
                'priority' => $order->priority,
                'customer_name' => $order->customer_name,
                'shoe' => [
                    'brand' => $order->shoe_brand,
                    'type' => $order->shoe_type,
                    'color' => $order->shoe_color,
                    'size' => $order->shoe_size,
                ],
                'current_status' => $currentStatusDetails,
                'visual_photos' => [
                    'before_photo_url' => $beforePhotoUrl,
                    'after_photo_url' => $afterPhotoUrl,
                    'hero_photo_url' => $heroPhotoUrl,
                ],
                'services' => $services,
                'timeline' => $timeline
            ]
        ]);
    }
}
