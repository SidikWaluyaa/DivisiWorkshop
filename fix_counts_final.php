<?php

use App\Models\StorageRack;
use App\Models\StorageAssignment;

echo "Fixing Rack Counts...\n";

$racks = StorageRack::all();

foreach ($racks as $rack) {
    // Count active assignments that MATCH the rack's category
    // This is critical strictly for the Strict Mode logic
    $realCount = StorageAssignment::where('rack_code', $rack->rack_code)
        ->where('category', $rack->category) 
        ->stored()
        ->count();

    if ($rack->current_count !== $realCount) {
        echo "Rack {$rack->rack_code} ({$rack->category}): Fixed count {$rack->current_count} -> {$realCount}\n";
        $rack->update(['current_count' => $realCount]);
    }
}

echo "Done.\n";
