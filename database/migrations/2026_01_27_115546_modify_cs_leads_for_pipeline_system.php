<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cs_leads', function (Blueprint $table) {
            // Source Tracking
            $table->string('source')->default('WhatsApp')->after('customer_province'); // WhatsApp, Instagram, Website, Referral, Walk-in
            $table->text('source_detail')->nullable()->after('source'); // Detail tambahan
            
            // Response Time Tracking
            $table->timestamp('first_contact_at')->nullable()->after('source_detail');
            $table->timestamp('first_response_at')->nullable()->after('first_contact_at');
            $table->integer('response_time_minutes')->nullable()->after('first_response_at'); // Calculated
            
            // Priority & Value
            $table->enum('priority', ['HOT', 'WARM', 'COLD'])->default('WARM')->after('response_time_minutes');
            $table->decimal('expected_value', 12, 2)->nullable()->after('priority'); // Estimasi nilai order
            
            // Lost Tracking
            $table->text('lost_reason')->nullable()->after('expected_value');
            
            // Conversion Tracking
            $table->foreignId('converted_to_work_order_id')->nullable()->after('lost_reason')->constrained('work_orders')->nullOnDelete();
            
            // Rename last_updated_at to last_activity_at for clarity
            $table->renameColumn('last_updated_at', 'last_activity_at');
        });
        
        // Update existing status values
        DB::statement("UPDATE cs_leads SET status = 'GREETING' WHERE status = 'NEW'");
        DB::statement("UPDATE cs_leads SET status = 'GREETING', priority = 'COLD' WHERE status = 'INV_GREETING'");
        DB::statement("UPDATE cs_leads SET status = 'KONSULTASI', priority = 'COLD' WHERE status = 'INV_KONSULTASI'");
        DB::statement("UPDATE cs_leads SET status = 'CONVERTED' WHERE status = 'CLOSED'");
        
        // Add new status option: CLOSING
        // Note: Status column already exists, we just need to ensure it accepts new values
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_leads', function (Blueprint $table) {
            $table->dropForeign(['converted_to_work_order_id']);
            $table->dropColumn([
                'source',
                'source_detail',
                'first_contact_at',
                'first_response_at',
                'response_time_minutes',
                'priority',
                'expected_value',
                'lost_reason',
                'converted_to_work_order_id',
            ]);
            
            $table->renameColumn('last_activity_at', 'last_updated_at');
        });
    }
};
