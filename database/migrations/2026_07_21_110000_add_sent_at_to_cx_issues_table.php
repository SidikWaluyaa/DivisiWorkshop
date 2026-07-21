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
        Schema::table('cx_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('cx_issues', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('shipping_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_issues', function (Blueprint $table) {
            if (Schema::hasColumn('cx_issues', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
        });
    }
};
