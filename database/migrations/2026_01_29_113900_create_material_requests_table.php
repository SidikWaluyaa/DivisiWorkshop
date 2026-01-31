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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique()->comment('Format: REQ-YYYY-0001');
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('oto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->enum('type', ['SHOPPING', 'PRODUCTION_PO'])->comment('SHOPPING: budget request, PRODUCTION_PO: purchase order');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'PURCHASED', 'CANCELLED'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->decimal('total_estimated_cost', 15, 2)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
