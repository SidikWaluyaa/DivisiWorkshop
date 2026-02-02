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
        Schema::create('manual_storage_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('rack_code')->index(); // Loose reference to StorageRack
            $table->integer('quantity')->default(1);
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            
            // Status: 'stored', 'retrieved'
            $table->string('status')->default('stored')->index();
            
            $table->dateTime('in_date');
            $table->dateTime('out_date')->nullable();
            
            $table->foreignId('stored_by')->constrained('users');
            $table->foreignId('retrieved_by')->nullable()->constrained('users');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_storage_items');
    }
};
