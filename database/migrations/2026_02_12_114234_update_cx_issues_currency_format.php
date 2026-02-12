<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Columns to update in cx_issues
        $columns = [
            'recommended_services',
            'suggested_services',
            'rec_service_1',
            'rec_service_2',
            'sug_service_1',
            'sug_service_2'
        ];

        DB::table('cx_issues')->orderBy('id')->chunk(100, function ($issues) use ($columns) {
            foreach ($issues as $issue) {
                $updates = [];
                $needsUpdate = false;

                foreach ($columns as $column) {
                    $original = $issue->$column;
                    if (!$original) continue;

                    // If already has Rp, skip
                    if (strpos($original, 'Rp ') !== false) continue;

                    // Format: (175000.00) -> (Rp 175.000)
                    $formatted = preg_replace_callback('/\(([\d.]+)\)/', function($matches) {
                        $price = (float) $matches[1];
                        return '(Rp ' . number_format($price, 0, ',', '.') . ')';
                    }, $original);

                    if ($formatted !== $original) {
                        $updates[$column] = $formatted;
                        $needsUpdate = true;
                    }
                }

                if ($needsUpdate) {
                    DB::table('cx_issues')
                        ->where('id', $issue->id)
                        ->update($updates);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migration cleanup usually doesn't need a down method for data fixing 
        // unless we want to revert formatting, which might be complex.
    }
};
