<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WarrantyClaim;
use App\Utils\ImageHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PublicWarrantyClaimApiController extends Controller
{
    /**
     * Helper to perform all business rules validation on an order for warranty claim.
     *
     * @param string $spk
     * @param string $phone
     * @return array
     */
    private function validateOrderForClaim(string $spk, string $phone): array
    {
        $spk = trim($spk);
        $phone = trim($phone);
        
        $phoneSuffix = substr(preg_replace('/[^0-9]/', '', $phone), -9);
        if (empty($phoneSuffix)) {
            return [
                'success' => false,
                'message' => 'Nomor telepon tidak valid.',
                'code' => 400
            ];
        }

        $order = WorkOrder::where('spk_number', $spk)
            ->where(function($query) use ($phoneSuffix) {
                $query->where('customer_phone', 'like', '%' . $phoneSuffix)
                      ->orWhere('customer_phone', 'like', '%' . substr($phoneSuffix, 1));
            })->first();

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Kombinasi Nomor SPK dan Nomor WhatsApp tidak ditemukan di sistem.',
                'code' => 404
            ];
        }

        // Check if status is SELESAI
        $statusVal = $order->status;
        if ($statusVal instanceof \BackedEnum) {
            $statusVal = $statusVal->value;
        }
        $statusStr = is_string($statusVal) ? $statusVal : (string)$statusVal;

        if (strcasecmp(trim($statusStr), 'SELESAI') !== 0) {
            return [
                'success' => false,
                'message' => 'Klaim garansi hanya bisa diajukan untuk pengerjaan yang sudah berstatus SELESAI. Status SPK Anda saat ini: ' . ($statusStr ?: 'KOSONG'),
                'code' => 400
            ];
        }

        // Check warranty duration and expiry
        if (!$order->warranty_expires_at) {
            return [
                'success' => false,
                'message' => 'Layanan pada SPK ini tidak memiliki fasilitas garansi.',
                'code' => 400
            ];
        }

        if ($order->warranty_expires_at->isPast()) {
            return [
                'success' => false,
                'message' => 'Masa berlaku garansi Anda telah berakhir pada tanggal ' . $order->warranty_expires_at->format('d M Y') . '.',
                'code' => 400
            ];
        }

        // Check if there is already a PENDING or APPROVED claim for this SPK
        $existingClaim = WarrantyClaim::where('work_order_id', $order->id)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->first();

        if ($existingClaim) {
            if ($existingClaim->status === 'PENDING') {
                $msg = 'Pengajuan klaim garansi untuk SPK ini sedang ditinjau oleh Divisi CX. Harap tunggu konfirmasi.';
            } else {
                $msg = 'Klaim garansi untuk SPK ini sudah disetujui sebelumnya dan sedang dalam proses perbaikan.';
            }
            return [
                'success' => false,
                'message' => $msg,
                'code' => 400
            ];
        }

        return [
            'success' => true,
            'order' => $order
        ];
    }

    /**
     * Check if SPK and customer phone matches for warranty claim.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'spk_number' => 'required|string',
            'customer_phone' => 'required|string',
        ], [
            'spk_number.required' => 'Nomor SPK wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp / Telepon wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $res = $this->validateOrderForClaim($request->spk_number, $request->customer_phone);
        if (!$res['success']) {
            return response()->json([
                'success' => false,
                'message' => $res['message']
            ], $res['code']);
        }

        $order = $res['order'];

        return response()->json([
            'success' => true,
            'message' => 'Layanan garansi tersedia dan aktif.',
            'data' => [
                'work_order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'shoe_brand' => $order->shoe_brand,
                'shoe_type' => $order->shoe_type,
                'shoe_color' => $order->shoe_color,
                'warranty_expires_at' => $order->warranty_expires_at->format('d M Y'),
                'days_left' => now()->diffInDays($order->warranty_expires_at, false),
            ]
        ]);
    }

    /**
     * Submit warranty claim.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'spk_number' => 'required|string',
            'customer_phone' => 'required|string',
            'problem_description' => 'required|string|min:10|max:1000',
            'penggunaan' => 'required|string|min:5|max:100',
            'problem_photos' => 'required|array|min:1|max:3',
            'problem_photos.*' => 'image|max:20480', // Max 20MB
            'google_review_photo' => 'required|image|max:20480', // Max 20MB
        ], [
            'spk_number.required' => 'Nomor SPK wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp / Telepon wajib diisi.',
            'problem_description.required' => 'Deskripsi keluhan wajib diisi.',
            'problem_description.min' => 'Deskripsi keluhan minimal 10 karakter.',
            'penggunaan.required' => 'Penggunaan sepatu wajib diisi.',
            'penggunaan.min' => 'Penggunaan sepatu minimal 5 karakter.',
            'penggunaan.max' => 'Penggunaan sepatu maksimal 100 karakter.',
            'problem_photos.required' => 'Foto bukti kerusakan wajib diunggah.',
            'problem_photos.array' => 'Foto bukti kerusakan harus berupa kelompok gambar.',
            'problem_photos.min' => 'Wajib mengunggah minimal 1 foto kerusakan.',
            'problem_photos.max' => 'Maksimal mengunggah 3 foto kerusakan.',
            'problem_photos.*.image' => 'File bukti kerusakan harus berupa gambar.',
            'problem_photos.*.max' => 'Ukuran gambar bukti kerusakan maksimal 20MB.',
            'google_review_photo.required' => 'Foto bukti review Google wajib diunggah.',
            'google_review_photo.image' => 'File harus berupa gambar.',
            'google_review_photo.max' => 'Ukuran gambar maksimal 20MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Re-validate order checks to protect API entrypoint and ensure order still qualifies
        $res = $this->validateOrderForClaim($request->spk_number, $request->customer_phone);
        if (!$res['success']) {
            return response()->json([
                'success' => false,
                'message' => $res['message']
            ], $res['code']);
        }

        $order = $res['order'];

        try {
            return DB::transaction(function() use ($request, $order) {
                // Compress and Save Problem Photos (Multiple)
                $problemPaths = [];
                foreach ($request->file('problem_photos') as $index => $photo) {
                    $probFilename = 'CLAIM_PROB_' . $order->spk_number . '_' . time() . '_' . ($index + 1);
                    $problemPaths[] = ImageHelper::convertToJpg($photo, 'warranty-claims', $probFilename);
                }

                // Compress and Save Google Review Photo
                $revFilename = 'CLAIM_REV_' . $order->spk_number . '_' . time();
                $reviewPath = ImageHelper::convertToJpg($request->file('google_review_photo'), 'warranty-claims', $revFilename);

                // Save Claim record
                $claim = WarrantyClaim::create([
                    'work_order_id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'spk_number' => $order->spk_number,
                    'problem_description' => $request->problem_description,
                    'penggunaan' => $request->penggunaan,
                    'problem_photo' => $problemPaths,
                    'google_review_photo' => $reviewPath,
                    'status' => 'PENDING',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Klaim garansi berhasil diajukan.',
                    'data' => $claim
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('API Error submitting warranty claim: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pengajuan atau gambar. Silakan coba kembali.'
            ], 500);
        }
    }
}
