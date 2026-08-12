<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GAP Resolution PRD-SRS v1.0 (SRS §4.2, FR-5.3):
     * Tambah atribut manajemen pool teknisi:
     * - workshop_pool: sortir atau produksi (menentukan di pool mana teknisi bekerja)
     * - availability_status: tersedia / sedang_mengerjakan / off
     * - is_support: flag apakah teknisi ini merupakan dukungan lintas pool
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('workshop_pool', 20)->nullable()->after('specialization')
                  ->comment('Pool workshop teknisi: sortir atau produksi');
            $table->string('availability_status', 30)->default('tersedia')->after('workshop_pool')
                  ->comment('Status ketersediaan teknisi: tersedia, sedang_mengerjakan, off');
            $table->boolean('is_support')->default(false)->after('availability_status')
                  ->comment('Flag teknisi support lintas pool');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['workshop_pool', 'availability_status', 'is_support']);
        });
    }
};
