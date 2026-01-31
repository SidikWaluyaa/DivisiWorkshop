<?php

use App\Models\StorageRack;
use App\Models\StorageAssignment;

echo "Starting Global Rack Count Reset...\n";
echo "-----------------------------------\n";

$racks = StorageRack::all();
$updated = 0;

foreach ($racks as $rack) {
    // Strict Count: Only count assignments that MATCH the rack's category
    $actualCount = StorageAssignment::where('rack_code', $rack->rack_code)
        ->where('category', $rack->category)
        ->stored()
        ->count();

    if ($rack->current_count !== $actualCount) {
        echo "[FIX] Rack {$rack->rack_code} ({$rack->category}): {$rack->current_count} -> {$actualCount}\n";
        $rack->update(['current_count' => $actualCount]);
        $updated++;
    }
}

echo "-----------------------------------\n";
echo "Completed. Total racks updated: {$updated}\n";
