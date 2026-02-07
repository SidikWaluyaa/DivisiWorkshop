<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPhotoJob;
use App\Models\WorkOrder;
use App\Models\WorkOrderPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ChunkUploadController extends Controller
{
    /**
     * Handles the file upload
     *
     * @param Request $request
     * @param $workOrderId
     * @return \Illuminate\Http\JsonResponse
     * @throws UploadMissingFileException
     */
    public function upload(Request $request, $workOrderId)
    {
        // 1. Create File Receiver
        $chunkPath = storage_path('app/chunks');
        if (!file_exists($chunkPath)) {
            mkdir($chunkPath, 0775, true);
        }

        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // 2. Check if the upload is successful
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // Receive the file
            $file = $save->getFile();

            $order = WorkOrder::findOrFail($workOrderId);
            
            // 1. Quota Check (Enterprise Standard: Max 50 photos)
            $count = WorkOrderPhoto::where('work_order_id', $order->id)->count();
            if ($count >= 50) {
                unlink($file->getPathname());
                return response()->json([
                    'success' => false,
                    'message' => 'Batas maksimal 50 foto per SPK telah tercapai.'
                ], 422);
            }

            // 2. Post-Upload Validation (Security: Ensure it's actually an image after reassembly)
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['file' => $file],
                ['file' => 'image|max:15360'] // 15MB max
            );

            if ($validator->fails()) {
                unlink($file->getPathname());
                return response()->json([
                    'success' => false,
                    'message' => 'File hasil penggabungan tidak valid atau bukan gambar.'
                ], 422);
            }

            $sequence = $count + 1;

            // 3. Concurrency-Safe Naming (Sequence + Random Hash)
            $safeSpk = str_replace(['/', '\\', ' '], '-', $order->spk_number);
            $hash = bin2hex(random_bytes(2)); // 4 chars random
            $extension = $file->getClientOriginalExtension();
            $fileName = "{$safeSpk}_{$sequence}_{$hash}.{$extension}";

            // Final path
            $finalPath = "photos/orders/{$order->id}/{$fileName}";
            
            // Save to disk
            $file->storeAs("photos/orders/{$order->id}", $fileName, 'public');

            // 4. Create DB Record
            $photo = WorkOrderPhoto::create([
                'work_order_id' => $order->id,
                'step' => $request->step ?? 'workshop_documentation',
                'file_path' => $finalPath,
                'caption' => $request->caption,
                'is_public' => $request->boolean('is_public', true),
                'user_id' => Auth::id(),
            ]);

            // 5. Delete from temporary chunk storage
            unlink($file->getPathname());

            return response()->json([
                'success' => true,
                'photo_id' => $photo->id,
                'path' => Storage::url($finalPath),
                'message' => "File uploaded successfully."
            ]);
        }

        // We are currently receiving chunks, return current progress
        $handler = $save->handler();

        return response()->json([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
    }
}
