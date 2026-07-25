<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDuplicateMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds to consolidate duplicate materials.
     */
    public function run(): void
    {
        $this->command->info("--- STARTING WAREHOUSE MASTER DATA MATERIAL CLEANUP ---");

        // Tables to update material_id
        $tables = [
            'warehouse_purchase_items',
            'warehouse_disbursement_items',
            'material_transactions',
            'material_request_items',
            'material_reservations',
            'purchases',
            'work_order_details'
        ];

        // Find duplicate materials grouped by name, size, type, unit
        $duplicates = DB::table('materials')
            ->select('name', 'size', 'type', 'unit', DB::raw('COUNT(*) as count'))
            ->groupBy('name', 'size', 'type', 'unit')
            ->having('count', '>', 1)
            ->get();

        $this->command->info("Found " . $duplicates->count() . " duplicate material groups to merge.\n");

        DB::beginTransaction();

        try {
            $totalDeleted = 0;
            $totalUpdatedRelations = 0;

            foreach ($duplicates as $dup) {
                // Get all materials in this group
                $items = DB::table('materials')
                    ->where('name', $dup->name)
                    ->where('size', $dup->size)
                    ->where('type', $dup->type)
                    ->where('unit', $dup->unit)
                    ->orderBy('id', 'asc')
                    ->get();

                $primary = $items->first();
                $dupsToMerge = $items->slice(1);

                $mergedStock = $primary->stock;
                
                $this->command->info("Merging Group: '{$dup->name}' | Size: '{$dup->size}' | Type: '{$dup->type}'");
                $this->command->line("  -> Primary ID: {$primary->id} (Stock: {$primary->stock})");

                foreach ($dupsToMerge as $d) {
                    $this->command->line("  -> Merging Duplicate ID: {$d->id} (Stock: {$d->stock})");
                    $mergedStock += $d->stock;

                    // Update foreign keys in referencing tables
                    foreach ($tables as $table) {
                        if (Schema::hasTable($table)) {
                            $affected = DB::table($table)
                                ->where('material_id', $d->id)
                                ->update(['material_id' => $primary->id]);
                            if ($affected > 0) {
                                $this->command->line("     - Updated {$affected} rows in table '{$table}'");
                                $totalUpdatedRelations += $affected;
                            }
                        }
                    }

                    // Delete the duplicate material record
                    DB::table('materials')->where('id', $d->id)->delete();
                    $totalDeleted++;
                }

                // Update primary material's stock to consolidated stock
                DB::table('materials')
                    ->where('id', $primary->id)
                    ->update(['stock' => $mergedStock]);

                $this->command->line("  -> Primary ID: {$primary->id} consolidated stock is now: {$mergedStock}");
                $this->command->line("--------------------------------------------------------");
            }

            DB::commit();
            $this->command->info("COMMIT SUCCESSFUL! Database is cleaned.");
            $this->command->info("Summary:");
            $this->command->line("- Total Duplicate Material Records Merged & Deleted: {$totalDeleted}");
            $this->command->line("- Total Related DB Rows Updated: {$totalUpdatedRelations}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("FATAL ERROR OCCURRED! TRANSACTION ROLLED BACK.");
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
