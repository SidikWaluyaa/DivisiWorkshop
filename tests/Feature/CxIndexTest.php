<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Cx\Index as CxIndex;

class CxIndexTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_can_display_active_follow_up_work_orders()
    {
        $wo = WorkOrder::create([
            'spk_number' => 'SPK-ACTIVE-001',
            'customer_name' => 'Customer Active',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(5),
        ]);

        DB::table('cx_issues')->insert([
            'work_order_id' => $wo->id,
            'reported_by' => $this->adminUser->id,
            'status' => 'OPEN',
            'source' => 'WORKSHOP_PROD',
            'description' => 'Kendala teknis produksi',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(CxIndex::class)
            ->assertSee('Customer Active')
            ->assertSee('SPK-ACTIVE-001');
    }

    /** @test */
    public function it_filters_and_sorts_work_orders_stuck_more_than_3_days_longest_stuck_first()
    {
        // 1. Order stuck > 3 days (WS source, stuck 4 days ago)
        $woStuckWS = WorkOrder::create([
            'spk_number' => 'SPK-STUCK-WS',
            'customer_name' => 'Stuck Workshop',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now()->subDays(10),
            'estimation_date' => Carbon::now()->addDays(5),
        ]);
        DB::table('cx_issues')->insert([
            'work_order_id' => $woStuckWS->id,
            'reported_by' => $this->adminUser->id,
            'status' => 'OPEN',
            'source' => 'WORKSHOP_PROD',
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);

        // 2. Order stuck > 3 days (Gudang source, stuck 8 days ago - should be first!)
        $woStuckGudang = WorkOrder::create([
            'spk_number' => 'SPK-STUCK-GD',
            'customer_name' => 'Stuck Gudang',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now()->subDays(10),
            'estimation_date' => Carbon::now()->addDays(5),
        ]);
        DB::table('cx_issues')->insert([
            'work_order_id' => $woStuckGudang->id,
            'reported_by' => $this->adminUser->id,
            'status' => 'OPEN',
            'source' => 'GUDANG',
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        // 3. New Order (not stuck)
        $woNew = WorkOrder::create([
            'spk_number' => 'SPK-NEW',
            'customer_name' => 'New Order',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(5),
        ]);
        DB::table('cx_issues')->insert([
            'work_order_id' => $woNew->id,
            'reported_by' => $this->adminUser->id,
            'status' => 'OPEN',
            'source' => 'WORKSHOP_PROD',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Test filter and sorting order (Stuck Gudang [8 days ago] should be before Stuck Workshop [4 days ago])
        $test = Livewire::actingAs($this->adminUser)
            ->test(CxIndex::class)
            ->set('delay_filter', 'stuck_3_days')
            ->assertSee('Stuck Workshop')
            ->assertSee('Stuck Gudang')
            ->assertDontSee('New Order');

        $html = $test->html();
        $posGudang = strpos($html, 'Stuck Gudang');
        $posWS = strpos($html, 'Stuck Workshop');

        $this->assertTrue($posGudang !== false && $posWS !== false);
        $this->assertTrue($posGudang < $posWS, 'Stuck Gudang (stuck 8 days) should be sorted before Stuck Workshop (stuck 4 days)');
    }

    /** @test */
    public function it_filters_and_sorts_work_orders_by_estimation_date_nearest_and_overdue_first()
    {
        // 1. Order with estimation in 2 days (near)
        $woNear = WorkOrder::create([
            'spk_number' => 'SPK-NEAR',
            'customer_name' => 'Near Est',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(2),
        ]);
        DB::table('cx_issues')->insert([
            'work_order_id' => $woNear->id,
            'status' => 'OPEN',
            'source' => 'WORKSHOP_PROD',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Order overdue (estimation 1 day ago - should be first!)
        $woOverdue = WorkOrder::create([
            'spk_number' => 'SPK-OVERDUE',
            'customer_name' => 'Overdue Est',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now()->subDays(5),
            'estimation_date' => Carbon::now()->subDays(1),
        ]);
        DB::table('cx_issues')->insert([
            'work_order_id' => $woOverdue->id,
            'status' => 'OPEN',
            'source' => 'WORKSHOP_PROD',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Order with far estimation (5 days in future)
        $woFar = WorkOrder::create([
            'spk_number' => 'SPK-FAR',
            'customer_name' => 'Far Est',
            'customer_phone' => '628123456789',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(5),
        ]);
        DB::table('cx_issues')->insert([
            'work_order_id' => $woFar->id,
            'status' => 'OPEN',
            'source' => 'WORKSHOP_PROD',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Test filter and sorting order (Overdue Est [-1 day] should be before Near Est [+2 days])
        $test = Livewire::actingAs($this->adminUser)
            ->test(CxIndex::class)
            ->set('est_filter', 'est_3_days')
            ->assertSee('Near Est')
            ->assertSee('Overdue Est')
            ->assertDontSee('Far Est');

        $html = $test->html();
        $posOverdue = strpos($html, 'Overdue Est');
        $posNear = strpos($html, 'Near Est');

        $this->assertTrue($posOverdue !== false && $posNear !== false);
        $this->assertTrue($posOverdue < $posNear, 'Overdue Est should be sorted before Near Est');
    }
}
