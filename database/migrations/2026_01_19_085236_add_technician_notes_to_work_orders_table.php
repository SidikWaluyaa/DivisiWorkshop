<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            // New column for specific Technical Instructions from Assessment
            $table->text('technician_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('technician_notes');
        });
    }
};
