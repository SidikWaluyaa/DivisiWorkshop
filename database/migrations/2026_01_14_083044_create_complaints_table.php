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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->string('customer_name')->nullable(); // Snapshot for easier access
            $table->string('customer_phone')->nullable(); // Snapshot for easier access
            $table->enum('category', ['QUALITY', 'LATE', 'SERVICE', 'DAMAGE', 'OTHER']);
            $table->text('description');
            $table->text('photos')->nullable(); // Changed from json to text for MariaDB compatibility
            $table->enum('status', ['PENDING', 'PROCESS', 'RESOLVED', 'REJECTED'])->default('PENDING');
            $table->text('admin_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
