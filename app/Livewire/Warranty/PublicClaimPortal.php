<?php

namespace App\Livewire\Warranty;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WorkOrder;
use App\Models\WarrantyClaim;
use App\Enums\WorkOrderStatus;
use App\Utils\ImageHelper;
use Illuminate\Support\Facades\Log;

class PublicClaimPortal extends Component
{
    use WithFileUploads;

    // Form inputs
    public $spk_number = '';
    public $customer_phone = '';
    
    // Claim details
    public $problem_description = '';
    public $penggunaan = '';
    public $problem_photos = [];
    public $google_review_photo;

    // Component states
    public $step = 1; // 1: Validation, 2: Submit Form, 3: Success Screen, 4: Tracking Screen
    public $work_order_id = null;
    public $order_details = [];
    public $existing_claim = null;

    // Validation rules
    protected function rules()
    {
        if ($this->step === 1) {
            return [
                'spk_number' => 'required|string',
                'customer_phone' => 'required|string',
            ];
        }

        return [
            'problem_description' => 'required|string|min:10|max:1000',
            'penggunaan' => 'required|string|min:5|max:100',
            'problem_photos' => 'required|array|min:1|max:3',
            'problem_photos.*' => 'image|max:20480',
            'google_review_photo' => 'required|image|max:20480', // Max 20MB
        ];
    }

    protected $messages = [
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
    ];

    /**
     * Remove a photo from problem_photos array
     */
    public function removeProblemPhoto($index)
    {
        if (isset($this->problem_photos[$index])) {
            array_splice($this->problem_photos, $index, 1);
        }
    }

    /**
     * Step 1: Validate SPK and Phone Number
     */
    public function checkWarranty()
    {
        $this->step = 1;
        $this->validate([
            'spk_number' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        // Normalize spaces or dashes
        $spk = trim($this->spk_number);
        $phone = trim($this->customer_phone);

        // Find work order matching SPK and phone
        // We will do a loose check on phone number (e.g. last 9 digits) to prevent strict formatting mismatch
        $phoneSuffix = substr(preg_replace('/[^0-9]/', '', $phone), -9);

        if (empty($phoneSuffix)) {
            session()->flash('error', 'Nomor telepon tidak valid.');
            return;
        }

        $order = WorkOrder::where('spk_number', $spk)
            ->where(function($query) use ($phoneSuffix) {
                $query->where('customer_phone', 'like', '%' . $phoneSuffix)
                      ->orWhere('customer_phone', 'like', '%' . substr($phoneSuffix, 1));
            })->first();

        if (!$order) {
            session()->flash('error', 'Kombinasi Nomor SPK dan Nomor WhatsApp tidak ditemukan di sistem.');
            return;
        }

        // Check if status is SELESAI
        $statusVal = $order->status;
        if ($statusVal instanceof \BackedEnum) {
            $statusVal = $statusVal->value;
        }
        $statusStr = is_string($statusVal) ? $statusVal : (string)$statusVal;

        if (strcasecmp(trim($statusStr), 'SELESAI') !== 0) {
            session()->flash('error', 'Klaim garansi hanya bisa diajukan untuk pengerjaan yang sudah berstatus SELESAI. Status SPK Anda saat ini: ' . ($statusStr ?: 'KOSONG'));
            return;
        }

        // Check warranty duration and expiry
        if (!$order->warranty_expires_at) {
            session()->flash('error', 'Layanan pada SPK ini tidak memiliki fasilitas garansi.');
            return;
        }

        if ($order->warranty_expires_at->isPast()) {
            session()->flash('error', 'Masa berlaku garansi Anda telah berakhir pada tanggal ' . $order->warranty_expires_at->format('d M Y') . '.');
            return;
        }

        // Check if there is already any claim for this SPK
        $existingClaim = WarrantyClaim::where('work_order_id', $order->id)
            ->latest()
            ->first();

        if ($existingClaim) {
            $this->work_order_id = $order->id;
            $this->existing_claim = $existingClaim;
            $this->order_details = [
                'customer_name' => $order->customer_name,
                'shoe_brand' => $order->shoe_brand,
                'shoe_type' => $order->shoe_type,
                'shoe_color' => $order->shoe_color,
                'warranty_expires_at' => $order->warranty_expires_at->format('d M Y'),
                'days_left' => now()->diffInDays($order->warranty_expires_at, false),
            ];
            $this->step = 4; // Redirect to tracking step
            return;
        }

        // Match found and active! Proceed to step 2
        $this->work_order_id = $order->id;
        $this->order_details = [
            'customer_name' => $order->customer_name,
            'shoe_brand' => $order->shoe_brand,
            'shoe_type' => $order->shoe_type,
            'shoe_color' => $order->shoe_color,
            'warranty_expires_at' => $order->warranty_expires_at->format('d M Y'),
            'days_left' => now()->diffInDays($order->warranty_expires_at, false),
        ];

        $this->step = 2;
    }

    /**
     * Restart/New claim submission (e.g. after rejection)
     */
    public function startNewClaim()
    {
        $this->existing_claim = null;
        $this->problem_description = '';
        $this->penggunaan = '';
        $this->problem_photos = [];
        $this->google_review_photo = null;
        $this->step = 2; // Directly go to submit form since SPK/Phone is already validated
    }

    /**
     * Step 2: Submit Claim with Images and Compression
     */
    public function submitClaim()
    {
        $this->validate([
            'problem_description' => 'required|string|min:10|max:1000',
            'penggunaan' => 'required|string|min:5|max:100',
            'problem_photos' => 'required|array|min:1|max:3',
            'problem_photos.*' => 'image|max:20480',
            'google_review_photo' => 'required|image|max:20480',
        ]);

        $order = WorkOrder::find($this->work_order_id);
        if (!$order) {
            session()->flash('error', 'Terjadi kesalahan sistem, silakan muat ulang halaman.');
            $this->step = 1;
            return;
        }

        try {
            // Compress and Save Problem Photos (Multiple)
            $problemPaths = [];
            foreach ($this->problem_photos as $index => $photo) {
                $probFilename = 'CLAIM_PROB_' . $order->spk_number . '_' . time() . '_' . ($index + 1);
                $problemPaths[] = ImageHelper::convertToJpg($photo, 'warranty-claims', $probFilename);
            }

            // Compress and Save Google Review Photo
            $revFilename = 'CLAIM_REV_' . $order->spk_number . '_' . time();
            $reviewPath = ImageHelper::convertToJpg($this->google_review_photo, 'warranty-claims', $revFilename);

            // Save Claim record
            WarrantyClaim::create([
                'work_order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'spk_number' => $order->spk_number,
                'problem_description' => $this->problem_description,
                'penggunaan' => $this->penggunaan,
                'problem_photo' => $problemPaths,
                'google_review_photo' => $reviewPath,
                'status' => 'PENDING',
            ]);

            // Set to success state
            $this->step = 3;

        } catch (\Exception $e) {
            Log::error('Error submitting warranty claim: ' . $e->getMessage());
            session()->flash('error', 'Gagal memproses gambar. Pastikan file gambar Anda valid.');
        }
    }

    /**
     * Reset and go back to step 1
     */
    public function resetPortal()
    {
        $this->reset([
            'spk_number',
            'customer_phone',
            'problem_description',
            'penggunaan',
            'problem_photos',
            'google_review_photo',
            'step',
            'work_order_id',
            'order_details',
            'existing_claim'
        ]);
    }

    public function render()
    {
        return view('livewire.warranty.public-claim-portal')
            ->layout('layouts.warranty-claim'); // Dedicated premium layout with dot-grid background & Inter font
    }
}
