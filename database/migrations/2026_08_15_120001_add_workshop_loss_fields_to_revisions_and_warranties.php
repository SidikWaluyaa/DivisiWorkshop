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
                if (!Schema::hasColumn('work_order_revisions', 'loss_amount')) {
                    $table->decimal('loss_amount', 12, 2)->nullable()->default(0.00)->after('status');
                }
                if (!Schema::hasColumn('work_order_revisions', 'loss_category')) {
                    $table->string('loss_category', 50)->nullable()->after('loss_amount');
                }
                if (!Schema::hasColumn('work_order_revisions', 'loss_description')) {
                    $table->text('loss_description')->nullable()->after('loss_category');
                }
                if (!Schema::hasColumn('work_order_revisions', 'responsible_party')) {
                    $table->string('responsible_party', 100)->nullable()->after('loss_description');
                }
            });
        }

        if (Schema::hasTable('work_order_warranties')) {
            Schema::table('work_order_warranties', function (Blueprint $table) {
                if (!Schema::hasColumn('work_order_warranties', 'loss_amount')) {
                    $table->decimal('loss_amount', 12, 2)->nullable()->default(0.00)->after('status');
                }
                if (!Schema::hasColumn('work_order_warranties', 'loss_category')) {
                    $table->string('loss_category', 50)->nullable()->after('loss_amount');
                }
                if (!Schema::hasColumn('work_order_warranties', 'loss_description')) {
                    $table->text('loss_description')->nullable()->after('loss_category');
                }
                if (!Schema::hasColumn('work_order_warranties', 'responsible_party')) {
                    $table->string('responsible_party', 100)->nullable()->after('loss_description');
                }
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
                $columns = [];
                if (Schema::hasColumn('work_order_revisions', 'loss_amount')) $columns[] = 'loss_amount';
                if (Schema::hasColumn('work_order_revisions', 'loss_category')) $columns[] = 'loss_category';
                if (Schema::hasColumn('work_order_revisions', 'loss_description')) $columns[] = 'loss_description';
                if (Schema::hasColumn('work_order_revisions', 'responsible_party')) $columns[] = 'responsible_party';
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('work_order_warranties')) {
            Schema::table('work_order_warranties', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('work_order_warranties', 'loss_amount')) $columns[] = 'loss_amount';
                if (Schema::hasColumn('work_order_warranties', 'loss_category')) $columns[] = 'loss_category';
                if (Schema::hasColumn('work_order_warranties', 'loss_description')) $columns[] = 'loss_description';
                if (Schema::hasColumn('work_order_warranties', 'responsible_party')) $columns[] = 'responsible_party';
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
