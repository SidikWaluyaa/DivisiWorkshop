<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use App\Livewire\Warehouse\Dashboard;

class WarehousePiutangBeforeTest extends TestCase
{
    use DatabaseTransactions;

    protected $warehouseUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouseUser = User::create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'gudang',
        ]);
    }

    /** @test */
    public function authorized_roles_can_filter_piutang_before_by_status()
    {
        // 1. Create a customer
        $customer = Customer::create([
            'name' => 'Customer Test HP',
            'phone' => '628123456789',
        ]);

        // 2. Create Invoice 1: Belum Bayar
        $invoiceUnpaid = Invoice::create([
            'invoice_number' => 'INV-TEST-UNPAID',
            'customer_id' => $customer->id,
        ]);
        $invoiceUnpaid->workOrders()->create([
            'spk_number' => 'SPK-TEST-UNPAID',
            'customer_name' => 'Customer Test HP',
            'status' => WorkOrderStatus::DITERIMA->value,
            'waktu' => Carbon::now(),
            'total_transaksi' => 100000,
        ]);
        $invoiceUnpaid->syncFinancials();

        // 3. Create Invoice 2: DP/Cicil
        $invoiceDp = Invoice::create([
            'invoice_number' => 'INV-TEST-DP',
            'customer_id' => $customer->id,
        ]);
        $invoiceDp->workOrders()->create([
            'spk_number' => 'SPK-TEST-DP',
            'customer_name' => 'Customer Test HP',
            'status' => WorkOrderStatus::DITERIMA->value,
            'waktu' => Carbon::now(),
            'total_transaksi' => 200000,
        ]);
        // Create verified payment of 100,000 for this invoice to make status 'DP/Cicil'
        \App\Models\OrderPayment::create([
            'invoice_id' => $invoiceDp->id,
            'amount_total' => 100000,
            'is_verified' => true,
            'payment_method' => 'TRANSFER',
        ]);
        $invoiceDp->syncFinancials();

        // Test Livewire component filtering
        $testAll = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->set('piutangBeforeStatus', 'all');

        $invoicesAll = $testAll->instance()->piutangBeforeOrders;
        $invoiceNumbers = collect($invoicesAll)->pluck('invoice_number');
        $this->assertTrue($invoiceNumbers->contains('INV-TEST-UNPAID'));
        $this->assertTrue($invoiceNumbers->contains('INV-TEST-DP'));

        $testUnpaid = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->set('piutangBeforeStatus', 'Belum Bayar');

        $invoicesUnpaid = $testUnpaid->instance()->piutangBeforeOrders;
        $invoiceNumbersUnpaid = collect($invoicesUnpaid)->pluck('invoice_number');
        $this->assertTrue($invoiceNumbersUnpaid->contains('INV-TEST-UNPAID'));
        $this->assertFalse($invoiceNumbersUnpaid->contains('INV-TEST-DP'));
        // outstanding cards
        $this->assertEquals(100000, $testUnpaid->instance()->totalPiutangBeforeAmount);

        $testDp = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->set('piutangBeforeStatus', 'DP/Cicil');

        $invoicesDp = $testDp->instance()->piutangBeforeOrders;
        $invoiceNumbersDp = collect($invoicesDp)->pluck('invoice_number');
        $this->assertFalse($invoiceNumbersDp->contains('INV-TEST-UNPAID'));
        $this->assertTrue($invoiceNumbersDp->contains('INV-TEST-DP'));
        $this->assertEquals(100000, $testDp->instance()->totalPiutangBeforeAmount);
    }

    /** @test */
    public function authorized_roles_can_export_piutang_before_pdf()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-piutang-before-pdf', [
                'status' => 'Belum Bayar',
                'search' => '',
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function authorized_roles_can_export_piutang_before_excel()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-piutang-before-excel', [
                'status' => 'all',
                'search' => '',
            ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('Content-Type'));
    }
}
