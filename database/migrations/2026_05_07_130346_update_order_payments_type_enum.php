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
        // Using raw statement because ENUM modification is best handled this way in MySQL
        DB::statement("ALTER TABLE order_payments MODIFY COLUMN type ENUM('before', 'after', 'TAMBAH_JASA', 'LUNAS_AWAL')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE order_payments MODIFY COLUMN type ENUM('before', 'after')");
    }
};
