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
        Schema::create('warehouse_disbursements', function (Blueprint $table) {
            $table->id();
            $table->string('disbursement_number')->unique();
            $table->string('spk_number')->nullable()->index();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('disbursement_date');
            $table->foreignId('user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_disbursements');
    }
};
