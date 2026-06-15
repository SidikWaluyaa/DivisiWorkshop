<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceDashboardWebTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with access to finance
        $this->adminUser = User::create([
            'name' => 'Admin Finance',
            'email' => 'admin_fin@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create regular user without access to finance
        $this->regularUser = User::create([
            'name' => 'Regular CS',
            'email' => 'regular_cs@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'cs',
        ]);
    }

    /** @test */
    public function guests_cannot_access_finance_dashboard()
    {
        $response = $this->get(route('finance.dashboard'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function unauthorized_roles_cannot_access_finance_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('finance.dashboard'));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_roles_can_access_finance_dashboard()
    {
        // Seeding customer and invoice
        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '628123456789',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-20260615-0001',
            'customer_id' => $customer->id,
            'total_amount' => 100000.00,
            'paid_amount' => 100000.00,
            'discount' => 10000.00,
            'shipping_cost' => 15000.00,
            'status' => 'Lunas',
            'due_date' => Carbon::now()->addDays(7),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('finance.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Dashboard Finance');

        // Test Livewire component directly
        \Livewire\Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Finance\Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Dashboard Finance')
            ->assertViewHas('data');
    }

    /** @test */
    public function guests_cannot_export_finance_pdf()
    {
        $response = $this->get(route('finance.dashboard.export-pdf', ['tab' => 'invoices']));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function unauthorized_roles_cannot_export_finance_pdf()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('finance.dashboard.export-pdf', ['tab' => 'invoices']));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_roles_can_export_invoices_pdf()
    {
        // Seeding customer and invoice
        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '628123456789',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-20260615-0001',
            'customer_id' => $customer->id,
            'total_amount' => 100000.00,
            'paid_amount' => 100000.00,
            'discount' => 10000.00,
            'shipping_cost' => 15000.00,
            'status' => 'Lunas',
            'due_date' => Carbon::now()->addDays(7),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('finance.dashboard.export-pdf', [
                'tab' => 'invoices',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'status' => 'L',
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Laporan-Invoices-', $response->headers->get('Content-Disposition'));
    }

    /** @test */
    public function authorized_roles_can_export_payments_pdf()
    {
        // Seeding customer, invoice, and payment
        $customer = Customer::create([
            'name' => 'Jane Doe',
            'phone' => '628123456780',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-20260615-0002',
            'customer_id' => $customer->id,
            'total_amount' => 150000.00,
            'paid_amount' => 50000.00,
            'discount' => 0.00,
            'shipping_cost' => 0.00,
            'status' => 'DP/Cicil',
            'due_date' => Carbon::now()->addDays(7),
        ]);

        \App\Models\InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => 50000.00,
            'payment_date' => Carbon::now(),
            'type' => 'BEFORE',
            'verified' => true,
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('finance.dashboard.export-pdf', [
                'tab' => 'payments',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'type' => 'BEFORE',
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Laporan-Payments-', $response->headers->get('Content-Disposition'));
    }
}
