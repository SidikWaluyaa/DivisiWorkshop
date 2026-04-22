<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\WorkOrder;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InvoiceAddSpk extends Component
{
    public $invoiceId;
    public $search = '';
    public $selectedSpks = [];
    public $isOpen = false;

    protected $listeners = ['openAddSpkModal' => 'openModal'];

    public function mount($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->search = '';
        $this->selectedSpks = [];
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function linkSpks()
    {
        if (empty($this->selectedSpks)) {
            session()->flash('error', 'Silakan pilih setidaknya satu SPK.');
            return;
        }

        $invoice = Invoice::findOrFail($this->invoiceId);

        DB::beginTransaction();
        try {
            // Update WorkOrders to link to this invoice
            WorkOrder::whereIn('id', $this->selectedSpks)
                ->whereNull('invoice_id')
                ->update(['invoice_id' => $invoice->id]);

            // Trigger sync
            $invoice->syncFinancials();
            $invoice->syncSpkStatus();

            DB::commit();
            
            session()->flash('success', count($this->selectedSpks) . ' SPK berhasil ditambahkan ke Invoice.');
            return redirect()->route('finance.invoices.show', $invoice->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menambahkan SPK: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $invoice = Invoice::with('customer')->find($this->invoiceId);
        $availableSpks = [];

        if ($invoice && ($this->isOpen || $this->search)) {
            $query = WorkOrder::query()
                ->whereNull('invoice_id')
                ->whereNull('deleted_at')
                ->whereNotIn('status', [
                    \App\Enums\WorkOrderStatus::SPK_PENDING,
                    \App\Enums\WorkOrderStatus::BATAL,
                    \App\Enums\WorkOrderStatus::DONASI
                ]);

            $phone = $invoice->customer?->phone;
            $name = $invoice->customer?->name;

            $query->where(function ($q) use ($phone, $name) {
                if ($phone) {
                    $q->where('customer_phone', $phone);
                }
                if ($name) {
                    $q->orWhere('customer_name', 'LIKE', "%{$name}%");
                }
            });

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('spk_number', 'LIKE', "%{$this->search}%")
                      ->orWhere('shoe_brand', 'LIKE', "%{$this->search}%")
                      ->orWhere('shoe_type', 'LIKE', "%{$this->search}%");
                });
            }

            $availableSpks = $query->latest()->take(10)->get();
        }

        return view('livewire.finance.invoice-add-spk', [
            'invoice' => $invoice,
            'availableSpks' => $availableSpks
        ]);
    }
}
