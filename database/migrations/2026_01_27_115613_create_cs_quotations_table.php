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
        Schema::create('cs_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cs_lead_id')->constrained()->cascadeOnDelete();
            $table->string('quotation_number')->unique(); // QT-YYYYMMDD-XXX
            $table->integer('version')->default(1); // Untuk tracking revisi
            
            // Items - Array of services
            $table->json('items'); // [{service_name, description, price, qty}, ...]
            
            // Pricing
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('discount_type')->default('AMOUNT'); // AMOUNT or PERCENTAGE
            $table->decimal('total', 12, 2);
            
            // Notes & Terms
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            
            // Status & Timeline
            $table->enum('status', ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'REVISED'])->default('DRAFT');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Validity
            $table->date('valid_until')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['cs_lead_id', 'version']);
            $table->index('quotation_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_quotations');
    }
};
