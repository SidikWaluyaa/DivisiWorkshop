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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['PERCENTAGE', 'FIXED', 'BUNDLE', 'BOGO']);
            
            // Discount Details
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('For PERCENTAGE type (e.g., 20.00 = 20%)');
            $table->decimal('discount_amount', 12, 2)->nullable()->comment('For FIXED type (e.g., 50000)');
            $table->decimal('max_discount_amount', 12, 2)->nullable()->comment('Maximum discount cap');
            $table->decimal('min_purchase_amount', 12, 2)->nullable()->comment('Minimum purchase requirement');
            
            // Validity Period
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->boolean('is_active')->default(true);
            
            // Applicability
            $table->enum('applicable_to', ['ALL_SERVICES', 'SPECIFIC_SERVICES', 'CATEGORIES'])->default('ALL_SERVICES');
            $table->enum('customer_tier', ['ALL', 'VIP', 'REGULAR', 'NEW'])->default('ALL');
            
            // Usage Limits
            $table->integer('max_usage_total')->nullable()->comment('Total maximum usage across all customers');
            $table->integer('max_usage_per_customer')->default(1)->comment('Maximum usage per customer');
            $table->integer('current_usage_count')->default(0)->comment('Current usage counter');
            
            // Stacking & Priority
            $table->boolean('is_stackable')->default(false)->comment('Can be stacked with other promos');
            $table->integer('priority')->default(0)->comment('Higher number = higher priority');
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index(['valid_from', 'valid_until']);
            $table->index('is_active');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
