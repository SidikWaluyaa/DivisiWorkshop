<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->default('v1.0.0');
            $table->string('title');
            $table->enum('category', ['FEATURE_UPDATE', 'MAINTENANCE', 'SYSTEM_NOTICE', 'BUG_FIX'])->default('FEATURE_UPDATE');
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('target_roles')->nullable(); // e.g. ["all"] or ["admin", "cs", "gudang", "finance", "workshop"]
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_announcements');
    }
};
