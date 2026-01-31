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
        Schema::create('cs_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cs_lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // CS yang melakukan aktivitas
            $table->enum('type', ['CHAT', 'CALL', 'EMAIL', 'MEETING', 'NOTE', 'STATUS_CHANGE', 'QUOTATION_SENT', 'QUOTATION_ACCEPTED', 'QUOTATION_REJECTED']);
            $table->string('channel')->nullable(); // WhatsApp, Phone, Email, Instagram, dll
            $table->text('content'); // Isi komunikasi/note
            $table->json('metadata')->nullable(); // Data tambahan (attachments, duration, dll)
            $table->timestamps();
            
            // Index untuk performance
            $table->index(['cs_lead_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_activities');
    }
};
