<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oto_contact_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oto_id')->constrained()->onDelete('cascade');
            $table->foreignId('contacted_by')->constrained('users')->onDelete('cascade');
            $table->enum('contact_method', ['WHATSAPP', 'PHONE', 'EMAIL', 'IN_PERSON']);
            $table->text('notes')->nullable();
            $table->enum('customer_response', ['INTERESTED', 'NOT_INTERESTED', 'NEED_TIME', 'NO_ANSWER'])->nullable();
            $table->timestamps();
            
            $table->index('oto_id');
            $table->index('contacted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oto_contact_logs');
    }
};
