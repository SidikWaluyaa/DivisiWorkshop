<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderRndProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RndProgressController extends Controller
{
    /**
     * Tampilkan form mobile upload untuk teknisi di smartphone (via token QR)
     */
    public function showUploadForm($token)
    {
        // Cari record progress berdasarkan upload_token
        $initialProgress = WorkOrderRndProgress::where('upload_token', $token)->first();

        if (!$initialProgress) {
            abort(404, 'Token upload R&D tidak valid atau telah kadaluarsa.');
        }

        $order = $initialProgress->workOrder;

        // Ambil seluruh riwayat progres SPK ini
        $history = WorkOrderRndProgress::where('work_order_id', $order->id)
            ->whereNotNull('photo_path')
            ->orderBy('created_at', 'asc')
            ->get();

        $nextStageNumber = $history->count() + 1;

        return view('rnd.mobile-upload', compact('token', 'order', 'history', 'nextStageNumber', 'initialProgress'));
    }

    /**
     * Kompresi dan simpan foto ke WebP berukuran optimal (Max 1600px, Kualitas 80)
     */
    private function compressAndStorePhoto($file, string $spkNumber, string $prefix = 'rnd'): string
    {
        $directory = 'rnd_progress';
        $filename = $prefix . '_' . Str::slug($spkNumber) . '_' . time() . '_' . Str::random(6) . '.webp';
        $relativeStorePath = $directory . '/' . $filename;
        $fullPath = storage_path('app/public/' . $relativeStorePath);

        // Pastikan folder tujuan ada
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        try {
            $imageContent = file_get_contents($file->getRealPath());
            $source = @imagecreatefromstring($imageContent);

            if ($source !== false) {
                // Perbaiki orientasi EXIF (terutama foto dari smartphone)
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($file->getRealPath());
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $source = imagerotate($source, 180, 0);
                                break;
                            case 6:
                                $source = imagerotate($source, -90, 0);
                                break;
                            case 8:
                                $source = imagerotate($source, 90, 0);
                                break;
                        }
                    }
                }

                $width = imagesx($source);
                $height = imagesy($source);
                $maxDim = 1600;

                // Resize jika salah satu dimensi melebihi 1600px dengan menjaga rasio aspek
                if ($width > $maxDim || $height > $maxDim) {
                    if ($width > $height) {
                        $newWidth = $maxDim;
                        $newHeight = (int) round($height * ($maxDim / $width));
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = (int) round($width * ($maxDim / $height));
                    }

                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    // Pertahankan transparansi bila ada (PNG/WebP)
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);

                    imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($source);
                    $source = $resized;
                }

                // Simpan dalam format WebP dengan kualitas 80 (rasio kompresi optimal)
                imagewebp($source, $fullPath, 80);
                imagedestroy($source);

                return $relativeStorePath;
            }
        } catch (\Throwable $e) {
            \Log::warning("Gagal kompresi WebP R&D: " . $e->getMessage());
        }

        // Fallback jika kompresi GD gagal
        return $file->storeAs('rnd_progress', $filename, 'public');
    }

    /**
     * Proses penyimpanan progres dari form mobile smartphone teknisi
     */
    public function storeMobileUpload(Request $request, $token)
    {
        $initialProgress = WorkOrderRndProgress::where('upload_token', $token)->firstOrFail();
        $order = $initialProgress->workOrder;

        $request->validate([
            'stage_title' => 'required|string|max:255',
            'result_status' => 'required|string|in:IN_PROGRESS,SUCCESS,NEED_REVISION,FAILED',
            'notes' => 'nullable|string|max:2000',
            'photo' => 'required|image|max:25600', // Maks 25MB
            'technician_name' => 'nullable|string|max:100',
        ]);

        // Simpan foto terkompresi WebP
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->compressAndStorePhoto($request->file('photo'), $order->spk_number, 'rnd_mobile');
        }

        $notesContent = $request->notes . ($request->filled('technician_name') ? "\n[Teknisi: {$request->technician_name}]" : "");

        // Jika terdapat placeholder inisialisasi yang belum memiliki foto, perbarui record tersebut
        $placeholder = $order->rndProgresses()->whereNull('photo_path')->first();
        if ($placeholder) {
            $placeholder->update([
                'user_id' => auth()->id() ?? null,
                'stage_title' => $request->stage_title,
                'notes' => $notesContent,
                'photo_path' => $photoPath,
                'result_status' => $request->result_status,
                'created_at' => now(),
                'report_url' => route('rnd.report', $placeholder->report_token ?? $initialProgress->report_token),
            ]);
        } else {
            // Jika sudah ada progres foto sebelumnya, buat record tahap baru
            WorkOrderRndProgress::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id() ?? null,
                'stage_title' => $request->stage_title,
                'notes' => $notesContent,
                'photo_path' => $photoPath,
                'result_status' => $request->result_status,
                'upload_token' => $token,
                'report_token' => $initialProgress->report_token ?? Str::random(64),
                'report_url' => route('rnd.report', $initialProgress->report_token),
            ]);
        }

        // Catat ke audit trail work_order_logs jika ada
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id() ?? null,
            'step' => 'R&D',
            'action' => 'RND_PROGRESS_UPLOADED',
            'description' => "Progres R&D diunggah via Mobile HP: '{$request->stage_title}' (Status: {$request->result_status})"
        ]);

        return redirect()->route('rnd.upload', $token)->with('success', "Progres tahap '{$request->stage_title}' berhasil dikompres dan tersimpan!");
    }

    /**
     * Tampilkan Single Living Report publik interaktif (tanpa login)
     */
    public function showLivingReport($token)
    {
        // Cari salah satu record yang memiliki report_token ini
        $anyProgress = WorkOrderRndProgress::where('report_token', $token)->first();

        if (!$anyProgress) {
            abort(404, 'Tautan Living Report R&D tidak ditemukan.');
        }

        $order = $anyProgress->workOrder;

        // Ambil seluruh riwayat progres terurut kronologis
        $progresses = WorkOrderRndProgress::where('work_order_id', $order->id)
            ->whereNotNull('photo_path')
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung metrik
        $totalStages = $progresses->count();
        $countSuccess = $progresses->where('result_status', 'SUCCESS')->count();
        $countRevision = $progresses->where('result_status', 'NEED_REVISION')->count();
        $countFailed = $progresses->where('result_status', 'FAILED')->count();
        $countInProgress = $progresses->where('result_status', 'IN_PROGRESS')->count();

        return view('rnd.report-living', compact(
            'token', 
            'order', 
            'progresses', 
            'totalStages', 
            'countSuccess', 
            'countRevision', 
            'countFailed', 
            'countInProgress'
        ));
    }

    public function storeAdminProgress(Request $request, $orderId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        // Izinkan Admin, Owner, Workshop, Teknisi, atau user yang memiliki hak akses manageOrder
        if (!$user->isAdmin() && !$user->isOwner() && !$user->isWorkshop() && !$user->can('manageOrder', WorkOrder::class)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunggah progres R&D.');
        }

        $order = WorkOrder::findOrFail($orderId);

        $request->validate([
            'stage_title' => 'required|string|max:255',
            'result_status' => 'required|string|in:IN_PROGRESS,SUCCESS,NEED_REVISION,FAILED',
            'notes' => 'nullable|string|max:2000',
            'photo' => 'required|image|max:25600', // Maks 25MB
        ]);

        // Simpan foto terkompresi WebP
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->compressAndStorePhoto($request->file('photo'), $order->spk_number, 'rnd_admin');
        }

        // Cek jika terdapat placeholder inisialisasi yang belum memiliki foto
        $placeholder = $order->rndProgresses()->whereNull('photo_path')->first();
        if ($placeholder) {
            $placeholder->update([
                'user_id' => auth()->id(),
                'stage_title' => $request->stage_title,
                'notes' => $request->notes,
                'photo_path' => $photoPath,
                'result_status' => $request->result_status,
                'created_at' => now(),
                'report_url' => route('rnd.report', $placeholder->report_token),
            ]);
        } else {
            $initialProgress = $order->rndProgresses()->first();
            $uploadToken = $initialProgress->upload_token ?? Str::random(64);
            $reportToken = $initialProgress->report_token ?? Str::random(64);

            WorkOrderRndProgress::create([
                'work_order_id' => $order->id,
                'user_id' => auth()->id(),
                'stage_title' => $request->stage_title,
                'notes' => $request->notes,
                'photo_path' => $photoPath,
                'result_status' => $request->result_status,
                'upload_token' => $uploadToken,
                'report_token' => $reportToken,
                'report_url' => route('rnd.report', $reportToken),
            ]);
        }

        // Audit log
        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => 'R&D',
            'action' => 'RND_PROGRESS_UPLOADED',
            'description' => "Progres R&D ditambahkan langsung oleh Admin: '{$request->stage_title}' (Status: {$request->result_status})"
        ]);

        return redirect()->back()->with('success', "Progres R&D '{$request->stage_title}' berhasil dikompres dan disimpan!");
    }

    /**
     * Update data progres R&D dari halaman Admin Order Detail
     */
    public function updateAdminProgress(Request $request, $orderId, $progressId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        if (!$user->isAdmin() && !$user->isOwner() && !$user->can('manageOrder', WorkOrder::class)) {
            abort(403, 'Hanya Admin atau Owner yang memiliki hak akses untuk mengedit progres R&D.');
        }

        $order = WorkOrder::findOrFail($orderId);
        $progress = WorkOrderRndProgress::where('work_order_id', $order->id)->findOrFail($progressId);

        $request->validate([
            'stage_title' => 'required|string|max:255',
            'result_status' => 'required|string|in:IN_PROGRESS,SUCCESS,NEED_REVISION,FAILED',
            'notes' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:25600', // Opsional jika foto diganti
        ]);

        $oldTitle = $progress->stage_title;
        $photoPath = $progress->photo_path;

        if ($request->hasFile('photo')) {
            // Hapus foto lama dari storage disk
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $this->compressAndStorePhoto($request->file('photo'), $order->spk_number, 'rnd_admin_edit');
        }

        $progress->update([
            'stage_title' => $request->stage_title,
            'result_status' => $request->result_status,
            'notes' => $request->notes,
            'photo_path' => $photoPath,
        ]);

        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => 'R&D',
            'action' => 'RND_PROGRESS_UPDATED',
            'description' => "Progres R&D diperbarui oleh Admin: '{$oldTitle}' ➔ '{$request->stage_title}' (Status: {$request->result_status})"
        ]);

        return redirect()->back()->with('success', "Progres R&D '{$request->stage_title}' berhasil diperbarui!");
    }

    /**
     * Hapus data progres R&D dari halaman Admin Order Detail
     */
    public function destroyAdminProgress($orderId, $progressId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        if (!$user->isAdmin() && !$user->isOwner() && !$user->can('manageOrder', WorkOrder::class)) {
            abort(403, 'Hanya Admin atau Owner yang memiliki hak akses untuk menghapus progres R&D.');
        }

        $order = WorkOrder::findOrFail($orderId);
        $progress = WorkOrderRndProgress::where('work_order_id', $order->id)->findOrFail($progressId);

        $stageTitle = $progress->stage_title ?? 'Tahap R&D';
        $photoPath = $progress->photo_path;

        // Hapus berkas fisik foto dari storage
        if ($photoPath && Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }

        // Cek jumlah total record progres R&D untuk order ini
        $totalProgressCount = $order->rndProgresses()->count();

        if ($totalProgressCount <= 1) {
            // Jika record terakhir, jangan drop baris agar token SPK QR tidak hilang, melainkan reset ke placeholder
            $progress->update([
                'stage_title' => null,
                'notes' => null,
                'photo_path' => null,
                'result_status' => 'IN_PROGRESS',
            ]);
        } else {
            $progress->delete();
        }

        \App\Models\WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => auth()->id(),
            'step' => 'R&D',
            'action' => 'RND_PROGRESS_DELETED',
            'description' => "Progres R&D dihapus oleh Admin: '{$stageTitle}'"
        ]);

        return redirect()->back()->with('success', "Progres R&D '{$stageTitle}' berhasil dihapus!");
    }
}
