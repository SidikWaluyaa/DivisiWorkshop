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
        // 1. Add station column to users table
        if (!Schema::hasColumn('users', 'station')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('station')->nullable()->after('specialization')->comment('PREPARATION, SOLING, UPPER, TREATMENT, QC');
            });
        }

        // 2. Create technician_services pivot table
        if (!Schema::hasTable('technician_services')) {
            Schema::create('technician_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'service_id']);
            });
        }

        // 3. Auto-seed initial station column for existing technicians based on specialization
        $users = DB::table('users')->whereNotNull('specialization')->get();
        foreach ($users as $u) {
            $spec = strtoupper($u->specialization ?? '');
            $station = 'TREATMENT'; // Default for repair

            if (str_contains($spec, 'WASH') || str_contains($spec, 'CUCI') || str_contains($spec, 'PREP')) {
                $station = 'PREPARATION';
            } elseif (str_contains($spec, 'SOL REPAIR') || str_contains($spec, 'SOLING') || str_contains($spec, 'SOL')) {
                $station = 'SOLING';
            } elseif (str_contains($spec, 'UPPER') || str_contains($spec, 'JAHIT')) {
                $station = 'UPPER';
            } elseif (str_contains($spec, 'QC')) {
                $station = 'QC';
            } elseif (str_contains($spec, 'TREATMENT') || str_contains($spec, 'REPAINT') || str_contains($spec, 'CLEAN UP')) {
                $station = 'TREATMENT';
            }

            DB::table('users')->where('id', $u->id)->update(['station' => $station]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_services');

        if (Schema::hasColumn('users', 'station')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('station');
            });
        }
    }
};
