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
        Schema::create('work_order_rnd_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('stage_title', 255);
            $table->text('notes')->nullable();
            $table->string('photo_path', 500)->nullable();
            $table->string('result_status', 50)->default('IN_PROGRESS'); // IN_PROGRESS | SUCCESS | NEED_REVISION | FAILED
            $table->string('report_token', 64)->nullable();
            $table->string('upload_token', 64)->nullable();
            $table->string('report_url', 500)->nullable();
            $table->timestamps();

            $table->index('work_order_id');
            $table->index('report_token');
            $table->index('upload_token');
            $table->index('result_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_rnd_progress');
    }
};
