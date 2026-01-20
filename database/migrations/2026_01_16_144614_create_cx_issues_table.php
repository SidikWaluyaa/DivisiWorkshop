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
        Schema::create('cx_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users');
            $table->string('type')->default('FOLLOW_UP'); // FOLLOW_UP, QC_FAIL
            $table->string('category')->nullable(); // Teknis, Material, Estimasi, etc.
            $table->text('description')->nullable();
            $table->longText('photos')->nullable();
            $table->string('status')->default('OPEN'); // OPEN, RESOLVED, CANCELLED
            $table->string('resolution')->nullable(); // LANJUT, TAMBAH_JASA, KOMPLAIN, CANCEL
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cx_issues');
    }
};
