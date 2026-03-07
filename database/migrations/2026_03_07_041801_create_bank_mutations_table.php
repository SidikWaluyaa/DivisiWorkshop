<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_mutations', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('bank_code', 20)->nullable();
            $table->enum('mutation_type', ['CR', 'DB'])->default('CR');
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->index(['invoice_number', 'amount']);
            $table->index('used');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_mutations');
    }
};
