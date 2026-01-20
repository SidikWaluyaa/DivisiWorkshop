<?php

/**
 * Migration Category: OTO System
 * Purpose: Create OTO (One-Time Offer) table for upsell management
 * Module: OTO
 * Dependencies: 2024_01_01_000005_create_work_orders_table.php
 * Date: 2026-01-19
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            
            // OTO Info
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('oto_type', ['UPSELL', 'CROSS_SELL', 'PROMO'])->default('UPSELL');
            
            // Services & Pricing (JSON format)
            // Example: [{"service_id": 1, "service_name": "Deep Clean", "normal_price": 150000, "oto_price": 75000, "discount": 50}]
            $table->longText('proposed_services');
            $table->decimal('total_normal_price', 10, 2);
            $table->decimal('total_oto_price', 10, 2);
            $table->decimal('total_discount', 10, 2);
            $table->decimal('discount_percent', 5, 2);
            
            // Timing
            $table->integer('estimated_days')->default(2);
            $table->timestamp('valid_until');
            
            // Status
            $table->enum('status', [
                'PENDING_CUSTOMER',
                'ACCEPTED',
                'REJECTED',
                'EXPIRED',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED'
            ])->default('PENDING_CUSTOMER');
            
            // Customer Response
            $table->timestamp('customer_responded_at')->nullable();
            $table->text('customer_note')->nullable();
            
            // Execution
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Priority
            $table->integer('priority_score')->default(0);
            $table->boolean('is_fast_track')->default(true);
            
            // Finance
            $table->decimal('dp_required', 10, 2)->default(0);
            $table->boolean('dp_paid')->default(false);
            $table->timestamp('dp_paid_at')->nullable();
            
            // Material
            $table->boolean('materials_reserved')->default(false);
            $table->boolean('materials_confirmed')->default(false);
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('work_order_id');
            $table->index('status');
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otos');
    }
};
