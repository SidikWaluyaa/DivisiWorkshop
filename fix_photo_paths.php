<?php

use App\Models\WorkOrderPhoto;

$photos = WorkOrderPhoto::all();
$count = 0;

foreach ($photos as $photo) {
    if ($photo->file_path && !filter_var($photo->file_path, FILTER_VALIDATE_URL)) {
        $photo->file_path = asset('storage/' . $photo->file_path);
        $photo->save();
        $count++;
    }
}

echo "Selesai! Berhasil memperbarui $count foto menjadi URL lengkap.\n";
