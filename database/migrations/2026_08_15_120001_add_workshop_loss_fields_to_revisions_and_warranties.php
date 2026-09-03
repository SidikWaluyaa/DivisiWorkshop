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
        if (Schema::hasTable('work_order_revisions')) {
            Schema::table('work_order_revisions', function (Blueprint $table) {
                $table->decimal('loss_amount', 12, 2)->nullable()->default(0.00)->after('status');
                $table->string('loss_category', 50)->nullable()->after('loss_amount');
                $table->text('loss_description')->nullable()->after('loss_category');
                $table->string('responsible_party', 100)->nullable()->after('loss_description');
            });
        }

        if (Schema::hasTable('work_order_warranties')) {
            Schema::table('work_order_warranties', function (Blueprint $table) {
                $table->decimal('loss_amount', 12, 2)->nullable()->default(0.00)->after('status');
                $table->string('loss_category', 50)->nullable()->after('loss_amount');
                $table->text('loss_description')->nullable()->after('loss_category');
                $table->string('responsible_party', 100)->nullable()->after('loss_description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('work_order_revisions')) {
            Schema::table('work_order_revisions', function (Blueprint $table) {
                $table->dropColumn(['loss_amount', 'loss_category', 'loss_description', 'responsible_party']);
            });
        }

        if (Schema::hasTable('work_order_warranties')) {
            Schema::table('work_order_warranties', function (Blueprint $table) {
                $table->dropColumn(['loss_amount', 'loss_category', 'loss_description', 'responsible_party']);
            });
        }
    }
};
