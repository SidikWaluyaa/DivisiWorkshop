<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('work_order_photos', 'is_printed')) {
                $table->boolean('is_printed')->default(false)->after('is_spk_cover');
            }
            if (!Schema::hasColumn('work_order_photos', 'print_settings')) {
                $table->text('print_settings')->nullable()->after('is_printed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_order_photos', function (Blueprint $table) {
            $table->dropColumn(['is_printed', 'print_settings']);
        });
    }
};
