<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update the enum to include new statuses
        DB::statement("ALTER TABLE otos MODIFY COLUMN status ENUM(
            'PENDING_CX',
            'CONTACTED',
            'PENDING_CUSTOMER',
            'ACCEPTED',
            'REJECTED',
            'EXPIRED',
            'IN_PROGRESS',
            'COMPLETED',
            'CANCELLED'
        ) DEFAULT 'PENDING_CX'");
        
        // Then add new columns
        Schema::table('otos', function (Blueprint $table) {
            $table->foreignId('cx_assigned_to')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->timestamp('cx_contacted_at')->nullable()->after('cx_assigned_to');
            $table->enum('cx_contact_method', ['WHATSAPP', 'PHONE', 'EMAIL', 'IN_PERSON'])->nullable()->after('cx_contacted_at');
            $table->text('cx_notes')->nullable()->after('cx_contact_method');
            $table->integer('cx_follow_up_count')->default(0)->after('cx_notes');
        });
    }

    public function down(): void
    {
        Schema::table('otos', function (Blueprint $table) {
            $table->dropForeign(['cx_assigned_to']);
            $table->dropColumn([
                'cx_assigned_to',
                'cx_contacted_at',
                'cx_contact_method',
                'cx_notes',
                'cx_follow_up_count'
            ]);
        });
        
        // Revert enum to original
        DB::statement("ALTER TABLE otos MODIFY COLUMN status ENUM(
            'PENDING_CUSTOMER',
            'ACCEPTED',
            'REJECTED',
            'EXPIRED',
            'IN_PROGRESS',
            'COMPLETED',
            'CANCELLED'
        ) DEFAULT 'PENDING_CUSTOMER'");
    }
};
