<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('Material Upper');
            $table->string('sub_category')->nullable();
            $table->string('size')->nullable();
            $table->integer('stock')->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('min_stock')->default(5);
            $table->string('status')->default('Ready');
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
