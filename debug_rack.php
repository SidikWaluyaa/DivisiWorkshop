<?php

use App\Models\StorageRack;

// Search for 'A1' or 'A-01' in all states
$codes = ['A1', 'A-01', 'a1', 'a-01'];

foreach ($codes as $code) {
    echo "Checking code: '$code'\n";
    $racks = StorageRack::withTrashed()->where('rack_code', $code)->get();
    
    if ($racks->isEmpty()) {
        echo "  - Not found.\n";
    } else {
        foreach ($racks as $rack) {
            echo "  - Found! ID: {$rack->id}, Category: '{$rack->category}', Status: '{$rack->status}', Deleted At: " . ($rack->deleted_at ?? 'NULL') . "\n";
        }
    }
}
