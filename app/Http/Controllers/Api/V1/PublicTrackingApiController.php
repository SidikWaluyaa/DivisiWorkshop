<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
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

        $relations = [
            'services',
            'workOrderServices.service',
            'logs.user',
            'materials',
            'photos',
            'otos',
            'warranties',
            'revisions',
        ];

        // Query the WorkOrder with relationships
        $order = WorkOrder::where('spk_number', $spk)
            ->with($relations)
            ->first();

        // Fallback exact match using LIKE if exact = fails
        if (!$order) {
            $order = WorkOrder::where('spk_number', 'LIKE', $spk)
                ->with($relations)
                ->first();
        }

        // Fallback: Jika yang dicari adalah Nomor SPK Garansi (-GR) yang masih OPEN
        if (!$order) {
            $warrantyLookup = WorkOrderWarranty::where('garansi_spk_number', $spk)
                ->orWhere('garansi_spk_number', 'LIKE', $spk)
                ->latest()
                ->first();

            if ($warrantyLookup && $warrantyLookup->work_order_id) {
                $order = WorkOrder::where('id', $warrantyLookup->work_order_id)
                    ->with($relations)
                    ->first();
            }
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
        
        $isOnHold = in_array($currentStatusVal, [
            WorkOrderStatus::CX_FOLLOWUP->value ?? 'CX_FOLLOWUP',
            WorkOrderStatus::HOLD_FOR_CX->value ?? 'HOLD_FOR_CX',
            'CX_FOLLOWUP',
            'HOLD_FOR_CX'
        ]);

        // Deteksi Garansi Aktif (OPEN)
        // Catatan: kolom work_orders.is_warranty digunakan CS sebagai penanda paket "Bergaransi vs Non-Garansi",
        // sehingga status klaim garansi aktif hanya diambil dari work_order_warranties (OPEN) atau SPK Rework (-GR / parent_id).
        $openWarranty = $order->warranties->where('status', 'OPEN')->sortByDesc('id')->first();
        $isGrReworkSpk = (bool) preg_match('/-GR(-\d+)?$/i', (string) $order->spk_number) || !empty($order->parent_id);
        $isWarranty = (bool)$openWarranty || ($isGrReworkSpk && !in_array($currentStatusVal, ['SELESAI', 'DIAMBIL', 'BATAL', 'CANCELLED']));

        // Deteksi OTO Aktif (ACCEPTED / IN_PROGRESS / has_active_oto) & Penawaran OTO (PENDING)
        $activeOto = $order->otos->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])->sortByDesc('id')->first();
        $isOto = (bool)$order->has_active_oto || (bool)$activeOto;
        $pendingOto = !$isOto
            ? $order->otos->whereIn('status', ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER'])->sortByDesc('id')->first()
            : null;

        // Deteksi Revisi Aktif
        $openRevision = $order->revisions->where('status', 'OPEN')->sortByDesc('id')->first();
        $isRevising = ($currentStatusVal === 'REVISI' || (bool)$order->is_revising || (bool)$openRevision);

        $isSpecialServiceActive = ($isWarranty || $isOto || $isRevising);

        if ($isOnHold) {
            $prevVal = $order->previous_status instanceof \App\Enums\WorkOrderStatus ? $order->previous_status->value : $order->previous_status;
            $effectiveStatusVal = $prevVal ?: 'PRODUCTION';
        } elseif ($isSpecialServiceActive) {
            // Saat sedang OTO, Garansi, atau Revisi: tarik posisi ke Step 5: Service (PRODUCTION)
            $effectiveStatusVal = 'PRODUCTION';
        } else {
            $effectiveStatusVal = $currentStatusVal;
        }

        $currentIndex = array_search($effectiveStatusVal, $stageKeys);
        if ($currentIndex === false && is_object($order->status)) {
            $currentIndex = array_search($order->status->name, $stageKeys);
        }

        // Visual enhancement overrides from result.blade.php
        // 1. If in PRODUCTION / Special Service (OTO, Garansi, Revisi) -> keep at PRODUCTION / Service index (4)
        if ($isSpecialServiceActive || ($effectiveStatusVal === 'PRODUCTION' && ($order->is_production_finished || $order->is_revising))) {
            $currentIndex = 4; // Index of PRODUCTION
        }

        // 2. If in QC but QC is finished -> move visual indicator to SELESAI ONLY if not in OTO / Garansi / Revisi
        if (!$isSpecialServiceActive && $effectiveStatusVal === 'QC' && $order->is_qc_finished) {
            $currentIndex = 6; // Index of SELESAI
        }

        // Construct current status details
        $currentStatusDetails = [
            'code' => $currentStatusVal,
            'label' => '-',
            'description' => '-',
            'is_production_finished' => $isSpecialServiceActive ? false : (bool)$order->is_production_finished,
            'is_qc_finished' => $isSpecialServiceActive ? false : (bool)$order->is_qc_finished,
        ];

        if ($isWarranty) {
            $currentStatusDetails['code'] = 'GARANSI';
            $currentStatusDetails['label'] = 'Service (Garansi)';
            $currentStatusDetails['description'] = 'Order Anda sedang dalam proses pengerjaan klaim garansi.';
        } elseif ($isOto) {
            $currentStatusDetails['code'] = 'OTO';
            $currentStatusDetails['label'] = 'Service (OTO)';
            $currentStatusDetails['description'] = 'Order Anda sedang dalam proses pengerjaan layanan tambahan One Time Offer (OTO).';
        } elseif ($isRevising) {
            $currentStatusDetails['code'] = 'REVISI';
            $currentStatusDetails['label'] = 'Service (Revisi)';
            $currentStatusDetails['description'] = 'Order Anda sedang dalam penyesuaian pengerjaan ulang (revisi).';
        } elseif ($currentIndex !== false && isset($stageKeys[$currentIndex])) {
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
            if ($isSpecialServiceActive && $isCurrent) {
                $timestamp = null;
            } elseif ($key === 'DITERIMA') {
                $timestamp = $order->created_at;
            } elseif ($key === 'SELESAI' && $order->finished_date && !$isSpecialServiceActive) {
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
                'waktu' => ($isCompleted && $timestamp) ? $timestamp->format('Y-m-d H:i:s') : null
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
                'is_on_hold' => $isOnHold,
                'hold_title' => $isOnHold ? 'Konfirmasi Kendala Diperlukan' : null,
                'hold_message' => $isOnHold ? 'Tim workshop kami menemukan kendala teknis atau kondisi khusus pada sepatu Anda. Silakan lihat Laporan Kendala (CX Report) dan hubungi admin kami untuk konfirmasi tindakan.' : null,
                'report_issue_url' => $isOnHold ? route('cx-issues.report', $order->spk_number) : null,
                'is_oto' => $isOto,
                'oto_title' => $isOto ? 'Pengerjaan Layanan Tambahan (OTO)' : null,
                'oto_message' => $isOto
                    ? 'Sepatu Anda saat ini sedang berada di Stasiun Khusus OTO untuk pengerjaan layanan tambahan (One Time Offer) yang telah Anda setujui.' . ($activeOto && $activeOto->proposed_services ? ' Layanan OTO: ' . $activeOto->proposed_services . '.' : '')
                    : null,
                'has_oto_offer' => (bool)$pendingOto,
                'oto_offer_message' => $pendingOto
                    ? 'Layanan utama sepatu Anda telah selesai. Tim kami merekomendasikan penawaran khusus layanan tambahan: ' . $pendingOto->proposed_services . '. Silakan hubungi admin CX kami untuk konfirmasi.'
                    : null,
                'is_warranty' => $isWarranty,
                'warranty_spk_number' => $openWarranty ? $openWarranty->garansi_spk_number : ($isGrReworkSpk ? $order->spk_number : null),
                'warranty_title' => $isWarranty ? 'Pengerjaan Klaim Garansi' : null,
                'warranty_message' => $isWarranty
                    ? 'Sepatu Anda saat ini sedang dalam proses penanganan klaim garansi oleh tim teknisi kami untuk memastikan hasil perbaikan kembali optimal.' . ($openWarranty && $openWarranty->description ? ' Catatan Garansi: "' . $openWarranty->description . '"' : '')
                    : null,
                'is_revising' => $isRevising,
                'revision_title' => $isRevising ? 'Pengerjaan Ulang & Penyesuaian Kualitas (Revisi)' : null,
                'revision_message' => $isRevising ? 'Sepatu Anda saat ini sedang dalam proses penyesuaian pengerjaan ulang oleh tim spesialis kami untuk memastikan hasil akhir memenuhi standar mutu terbaik.' : null,
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
