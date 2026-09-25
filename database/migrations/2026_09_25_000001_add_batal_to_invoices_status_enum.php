<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Belum Bayar', 'DP/Cicil', 'Lunas', 'Batal') NOT NULL DEFAULT 'Belum Bayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Belum Bayar', 'DP/Cicil', 'Lunas') NOT NULL DEFAULT 'Belum Bayar'");
    }
};
