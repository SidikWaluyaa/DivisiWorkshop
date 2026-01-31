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
        Schema::create('cs_spk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cs_lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete(); // Link setelah dibuat
            $table->string('spk_number')->unique(); // Auto-generated: SPK-CS-YYYYMMDD-XXX
            $table->foreignId('customer_id')->constrained();
            
            // Order Details
            $table->json('services'); // Selected services dari quotation
            $table->decimal('total_price', 12, 2);
            
            // Payment
            $table->decimal('dp_amount', 12, 2)->default(0);
            $table->enum('dp_status', ['PENDING', 'PAID', 'WAIVED'])->default('PENDING');
            $table->timestamp('dp_paid_at')->nullable();
            $table->string('payment_method')->nullable(); // Cash, Transfer, dll
            $table->text('payment_notes')->nullable();
            
            // Delivery & Instructions
            $table->date('expected_delivery_date')->nullable();
            $table->text('special_instructions')->nullable();
            
            // Shoe Details (optional - bisa diisi nanti di gudang)
            $table->string('shoe_brand')->nullable();
            $table->string('shoe_type')->nullable();
            $table->string('shoe_color')->nullable();
            
            // Documents
            $table->string('pdf_path')->nullable(); // Path to generated PDF
            
            // Status & Handover
            $table->enum('status', ['DRAFT', 'WAITING_DP', 'DP_PAID', 'HANDED_TO_WORKSHOP'])->default('DRAFT');
            $table->timestamp('handed_at')->nullable();
            $table->foreignId('handed_by')->nullable()->constrained('users'); // CS yang serahkan
            
            $table->timestamps();
            
            // Indexes
            $table->index('spk_number');
            $table->index(['cs_lead_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_spk');
    }
};
