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
        Schema::create('cs_leads', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->index(); // Main identifier for leads
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_province')->nullable();
            
            $table->string('status')->default('NEW'); // NEW, KONSULTASI, INV_GREETING, INV_KONSULTASI, CLOSING
            
            $table->foreignId('cs_id')->constrained('users'); // Assigned CS
            $table->timestamp('last_updated_at')->useCurrent(); // For Invest Logic
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_leads');
    }
};
