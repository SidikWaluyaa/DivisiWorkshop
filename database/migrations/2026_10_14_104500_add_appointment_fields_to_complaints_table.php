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
        Schema::table('complaints', function (Blueprint $table) {
            $table->dateTime('appointment_date')->nullable()->after('admin_notes');
            $table->string('appointment_location')->default('WORKSHOP')->nullable()->after('appointment_date');
            $table->string('appointment_status')->default('SCHEDULED')->nullable()->after('appointment_location');
            $table->text('appointment_notes')->nullable()->after('appointment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn([
                'appointment_date',
                'appointment_location',
                'appointment_status',
                'appointment_notes',
            ]);
        });
    }
};
