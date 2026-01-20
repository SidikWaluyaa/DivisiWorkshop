<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            // Accessories tracking
            $table->string('accessories_tali', 20)->nullable()->comment('Simpan/Nempel/Tidak Ada');
            $table->string('accessories_insole', 20)->nullable();
            $table->string('accessories_box', 20)->nullable();
            $table->text('accessories_other')->nullable();
            
            // Warehouse QC
            $table->string('warehouse_qc_status', 20)->nullable()->comment('lolos/tidak_lolos');
            $table->text('warehouse_qc_notes')->nullable();
            $table->unsignedBigInteger('warehouse_qc_by')->nullable();
            $table->timestamp('warehouse_qc_at')->nullable();
            
            // Foreign key
            $table->foreign('warehouse_qc_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['warehouse_qc_by']);
            $table->dropColumn([
                'accessories_tali',
                'accessories_insole',
                'accessories_box',
                'accessories_other',
                'warehouse_qc_status',
                'warehouse_qc_notes',
                'warehouse_qc_by',
                'warehouse_qc_at',
            ]);
        });
    }
};
