<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CxOverdueApiTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Force MySQL connection to point to the dedicated isolated testing database to protect main database
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sistem_workshop_testing',
        ]);

        $this->user = User::factory()->create();
        config(['app.dashboard_api_key' => 'test-secret-key']);
    }

    /** @test */
    public function it_can_fetch_overdue_data()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 2, 12, 0, 0));

        // 1. Order with Estimation Date (Overdue 5 days)
        WorkOrder::factory()->create([
            'spk_number' => 'S-2601-01-0001-QA',
            'status' => WorkOrderStatus::PRODUCTION,
            'estimation_date' => Carbon::now()->subDays(5),
            'waktu' => Carbon::now()->subDays(10),
        ]);

        // 2. Order without Estimation Date (In PRODUCTION for 5 days, SLA is 3 days)
        WorkOrder::factory()->create([
            'spk_number' => 'S-2601-01-0002-QA',
            'status' => WorkOrderStatus::PRODUCTION,
            'estimation_date' => null,
            'waktu' => Carbon::now()->subDays(5),
        ]);

        $response = $this->withHeader('X-API-KEY', 'test-secret-key')
            ->getJson('/api/v1/cx-overdue');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'scoreboard',
                'orders'
            ]);

        $orders = $response->json('orders');
        
        // Assertions for order 1
        $order1 = collect($orders)->where('spk_number', 'S-2601-01-0001-QA')->first();
        $this->assertEquals(5, $order1['days_overdue']);

        // Assertions for order 2 (Corrected logic: no SLA subtraction for PRODUCTION fallback)
        $order2 = collect($orders)->where('spk_number', 'S-2601-01-0002-QA')->first();
        // Entry 5 days ago -> 5 days overdue
        $this->assertEquals(5, $order2['days_overdue']);
    }

    /** @test */
    public function scoreboard_counts_correctly()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 2, 12, 0, 0));

        // 1. PRODUCTION (SLA 3) - Entry 5 days ago -> 5 days late (no SLA subtraction for active stage fallback)
        WorkOrder::factory()->create([
            'status' => WorkOrderStatus::PRODUCTION,
            'waktu' => Carbon::now()->subDays(5),
            'estimation_date' => null
        ]);

        $response = $this->withHeader('X-API-KEY', 'test-secret-key')
            ->getJson('/api/v1/cx-overdue');

        $scoreboard = $response->json('scoreboard');
        
        // No SLA applied to active stage fallback: entry was 5 days ago -> 5 days overdue
        $this->assertEquals(1, $scoreboard['PRODUCTION']['overdue_count']);
        $this->assertEquals(5, $scoreboard['PRODUCTION']['total_days_overdue']);
    }
}
