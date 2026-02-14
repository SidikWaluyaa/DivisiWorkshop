<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix unique codes yang melebihi 999.
     * - Transaksi LUNAS (status_pembayaran = 'L'): set null (tidak perlu kode unik lagi)
     * - Transaksi AKTIF (belum lunas): regenerate ke range 100-999
     */
    public function up(): void
    {
        // Step 1: Transaksi yang sudah LUNAS → null-kan kode uniknya
        DB::table('work_orders')
            ->where('unique_code', '>', 999)
            ->where('status_pembayaran', 'L')
            ->update(['unique_code' => null]);

        // Step 2: Transaksi AKTIF yang masih > 999 → regenerate
        $activeOrders = DB::table('work_orders')
            ->where('unique_code', '>', 999)
            ->where(function ($q) {
                $q->whereNull('status_pembayaran')
                  ->orWhere('status_pembayaran', '!=', 'L');
            })
            ->get();

        if ($activeOrders->isEmpty()) {
            return; // Tidak ada yang perlu diperbaiki
        }

        // Ambil semua kode yang sedang aktif dipakai (100-999)
        $usedCodes = DB::table('work_orders')
            ->whereNotNull('unique_code')
            ->where('unique_code', '>=', 100)
            ->where('unique_code', '<=', 999)
            ->where(function ($q) {
                $q->whereNull('status_pembayaran')
                  ->orWhere('status_pembayaran', '!=', 'L');
            })
            ->pluck('unique_code')
            ->toArray();

        // Buat pool kode yang tersedia
        $allCodes = range(100, 999);
        $availableCodes = array_values(array_diff($allCodes, $usedCodes));
        shuffle($availableCodes);

        $index = 0;
        foreach ($activeOrders as $order) {
            if ($index < count($availableCodes)) {
                $newCode = $availableCodes[$index];
                $index++;
            } else {
                // Darurat: semua slot penuh, pakai random (sangat jarang)
                $newCode = rand(100, 999);
            }

            DB::table('work_orders')
                ->where('id', $order->id)
                ->update(['unique_code' => $newCode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak bisa di-rollback karena kode lama sudah tidak diketahui
    }
};
