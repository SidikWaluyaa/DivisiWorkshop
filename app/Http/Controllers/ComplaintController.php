<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        return redirect()->route('tracking.index'); // Form is now in tracking page
    }

    public function store(Request $request)
    {
        $request->validate([
            'spk_number' => 'required|string',
            'customer_phone' => 'required|string',
            'category' => 'required|in:QUALITY,LATE,SERVICE,DAMAGE,OTHER',
            'description' => 'required|string|min:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 1. Validate SPK & Phone
        // Normalize Phone: Remove non-digits, remove leading 62 or 0
        $rawPhone = preg_replace('/[^0-9]/', '', $request->customer_phone);
        $cleanPhone = $rawPhone;
        
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = substr($cleanPhone, 2);
        }
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        // Search strictly by SPK, but flexible on Phone (contains core number)
        // This allows '0812' input to match '62812' in DB and vice versa
        $workOrder = WorkOrder::where('spk_number', $request->spk_number)
            ->where('customer_phone', 'LIKE', '%' . $cleanPhone . '%')
            ->first();

        if (!$workOrder) {
            return back()->withErrors(['spk_number' => 'Data Order tidak ditemukan atau nomor HP tidak sesuai.']);
        }

        // 2. Handle Photos with Optimization
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Generate a unique filename
                $filename = uniqid('complaint_') . '.webp';
                $path = 'complaints/' . $filename;
                $fullPath = storage_path('app/public/' . $path);

                // Ensure directory exists
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                // Compress Image (Native PHP GD)
                $source = imagecreatefromstring(file_get_contents($photo));
                if ($source !== false) {
                     // Resize if width > 1024
                    $width = imagesx($source);
                    $height = imagesy($source);
                    $maxWidth = 1024;

                    if ($width > $maxWidth) {
                        $newWidth = $maxWidth;
                        $newHeight = floor($height * ($maxWidth / $width));
                        $destination = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($source);
                        $source = $destination;
                    }

                    // Save as WebP (Quality 75)
                    imagewebp($source, $fullPath, 75);
                    imagedestroy($source);
                    
                    $photoPaths[] = $path;
                } else {
                    // Fallback to original storage if GD fails
                    $photoPaths[] = $photo->store('complaints', 'public');
                }
            }
        }

        // 3. Create Complaint
        $complaint = Complaint::create([
            'work_order_id' => $workOrder->id,
            'customer_name' => $workOrder->customer_name,
            'customer_phone' => $workOrder->customer_phone,
            'category' => $request->category,
            'description' => $request->description,
            'photos' => $photoPaths,
            'status' => 'PENDING',
        ]);

        return redirect()->route('complaints.success', $complaint->id);
    }

    public function success(Complaint $complaint)
    {
        return view('complaints.success', compact('complaint'));
    }
}
