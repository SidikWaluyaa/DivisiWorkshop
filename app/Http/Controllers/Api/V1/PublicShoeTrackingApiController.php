<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
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

            $query->where(function ($q) use ($spk) {
                $q->where('spk_number', $spk)
                  ->orWhere('spk_number', 'LIKE', $spk);
            });
        } else {
            $id = (int) $request->query('id');
            $query->where('id', $id);
        }

        $relations = ['workOrderServices.service', 'otos', 'warranties', 'revisions'];

        // Eager load workOrderServices and their related service configurations
        $order = $query->with($relations)->first();

        // Fallback: Jika yang dicari adalah Nomor SPK Garansi (-GR) yang masih OPEN
        if (!$order && $request->filled('spk_number')) {
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

        $statusVal = is_object($order->status) ? $order->status->value : $order->status;
        $isOnHold = in_array($statusVal, [
            WorkOrderStatus::CX_FOLLOWUP->value ?? 'CX_FOLLOWUP',
            WorkOrderStatus::HOLD_FOR_CX->value ?? 'HOLD_FOR_CX',
            'CX_FOLLOWUP',
            'HOLD_FOR_CX'
        ]);

        // Deteksi Garansi Aktif (OPEN)
        $openWarranty = $order->warranties->where('status', 'OPEN')->sortByDesc('id')->first();
        $isWarranty = (bool)$openWarranty || ((bool)$order->is_warranty && !in_array($statusVal, ['SELESAI', 'DIAMBIL', 'BATAL', 'CANCELLED']));

        // Deteksi OTO Aktif (ACCEPTED / IN_PROGRESS / has_active_oto) & Penawaran OTO (PENDING)
        $activeOto = $order->otos->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])->sortByDesc('id')->first();
        $isOto = (bool)$order->has_active_oto || (bool)$activeOto;
        $pendingOto = !$isOto
            ? $order->otos->whereIn('status', ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER'])->sortByDesc('id')->first()
            : null;

        // Deteksi Revisi Aktif
        $openRevision = $order->revisions->where('status', 'OPEN')->sortByDesc('id')->first();
        $isRevising = ($statusVal === 'REVISI' || (bool)$order->is_revising || (bool)$openRevision);

        if ($isWarranty) {
            $effectiveCode = 'GARANSI';
            $effectiveLabel = 'Service (Garansi)';
        } elseif ($isOto) {
            $effectiveCode = 'OTO';
            $effectiveLabel = 'Service (OTO)';
        } elseif ($isRevising) {
            $effectiveCode = 'REVISI';
            $effectiveLabel = 'Service (Revisi)';
        } else {
            $effectiveCode = $statusVal;
            $effectiveLabel = is_object($order->status) ? $order->status->label() : '-';
        }

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
                'status' => $effectiveCode,
                'status_label' => $effectiveLabel,
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
                'warranty_spk_number' => $openWarranty ? $openWarranty->garansi_spk_number : ($order->is_warranty ? $order->spk_number : null),
                'warranty_title' => $isWarranty ? 'Pengerjaan Klaim Garansi' : null,
                'warranty_message' => $isWarranty
                    ? 'Sepatu Anda saat ini sedang dalam proses penanganan klaim garansi oleh tim teknisi kami untuk memastikan hasil perbaikan kembali optimal.' . ($openWarranty && $openWarranty->description ? ' Catatan Garansi: "' . $openWarranty->description . '"' : '')
                    : null,
                'is_revising' => $isRevising,
                'revision_title' => $isRevising ? 'Pengerjaan Ulang & Penyesuaian Kualitas (Revisi)' : null,
                'revision_message' => $isRevising ? 'Sepatu Anda saat ini sedang dalam proses penyesuaian pengerjaan ulang oleh tim spesialis kami untuk memastikan hasil akhir memenuhi standar mutu terbaik.' : null,
                'services' => $services
            ]
        ]);
    }
}
