<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceDashboardApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        config(['app.dashboard_api_key' => 'test-secret-key']);
    }

    /** @test */
    public function it_denies_access_without_api_key()
    {
        $response = $this->getJson('/api/v1/finance/dashboard');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_fetch_finance_dashboard_data()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15, 12, 0, 0));

        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '628123456789',
        ]);

        // 1. Create invoices
        $invoice1 = Invoice::create([
            'invoice_number' => 'INV-20260615-0001',
            'customer_id' => $customer->id,
            'total_amount' => 100000.00,
            'paid_amount' => 105000.00,
            'discount' => 10000.00,
            'shipping_cost' => 15000.00,
            'status' => 'Lunas',
            'due_date' => Carbon::now()->addDays(7),
            'created_at' => Carbon::now(),
        ]);

        $invoice2 = Invoice::create([
            'invoice_number' => 'INV-20260615-0002',
            'customer_id' => $customer->id,
            'total_amount' => 200000.00,
            'paid_amount' => 50000.00,
            'discount' => 0.00,
            'shipping_cost' => 0.00,
            'status' => 'DP/Cicil',
            'due_date' => Carbon::now()->subDays(2), // Overdue
            'created_at' => Carbon::now(),
        ]);

        // 2. Create invoice payments
        InvoicePayment::create([
            'invoice_id' => $invoice1->id,
            'amount' => 105000.00, // Total = 100k + 15k - 10k
            'payment_date' => Carbon::now()->toDateString(),
            'verified' => true,
            'created_by' => $this->user->id,
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice2->id,
            'amount' => 50000.00,
            'payment_date' => Carbon::now()->toDateString(),
            'verified' => true,
            'created_by' => $this->user->id,
        ]);

        // Unverified payment (should not be in cash received)
        InvoicePayment::create([
            'invoice_id' => $invoice2->id,
            'amount' => 30000.00,
            'payment_date' => Carbon::now()->toDateString(),
            'verified' => false,
            'created_by' => $this->user->id,
        ]);

        $response = $this->withHeader('X-API-KEY', 'test-secret-key')
            ->getJson('/api/v1/finance/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'metrics' => [
                        'total_invoiced_value',
                        'total_cash_received',
                        'total_outstanding_receivables',
                        'collection_rate',
                    ],
                    'status_breakdown' => [
                        'belum_bayar',
                        'dp_cicil',
                        'lunas',
                    ],
                    'overdue_invoices' => [
                        'count',
                        'total_outstanding',
                        'items',
                    ],
                    'chart_data' => [
                        'labels',
                        'cash_inflow',
                    ],
                    'recent_payments',
                    'period',
                    'metadata',
                ],
                'message',
            ]);

        $data = $response->json('data');
        
        // Assert invoice 1: invoiced value = 100k + 15k - 10k = 105k
        // Assert invoice 2: invoiced value = 200k + 0k - 0k = 200k
        // Total Invoiced: 305k
        $this->assertEquals(305000.00, $data['metrics']['total_invoiced_value']);

        // Cash received: 105k + 50k = 155k (unverified payment excluded)
        $this->assertEquals(155000.00, $data['metrics']['total_cash_received']);

        // Collection rate: (155k / 305k) * 100 = 50.82
        $this->assertEquals(50.82, $data['metrics']['collection_rate']);

        // Overdue count should be 1 (invoice 2 is overdue and not Lunas)
        $this->assertEquals(1, $data['overdue_invoices']['count']);
    }
}
