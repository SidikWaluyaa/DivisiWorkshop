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
        Schema::table('materials', function (Blueprint $table) {
            $table->string('type')->after('name')->default('Material Upper');
            $table->string('sub_category')->nullable()->after('type');
        });
        
        // Drop old category if exists
        if (Schema::hasColumn('materials', 'category')) {
             Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('category');
             });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['type', 'sub_category']);
            $table->string('category')->nullable();
        });
    }
};
