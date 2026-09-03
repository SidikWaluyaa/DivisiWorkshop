<?php

namespace App\Livewire\Public;

use App\Models\Invoice;
use App\Models\OrderPayment;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentConfirmation extends Component
{
    use WithFileUploads;

    public $token = '';
    public $invoice = null;
    public $invoiceNotFound = false;
    
    // Form Inputs
    public $amount = '';
    public $payment_method = 'BCA';
    public $proof_image;
    public $notes = '';

    // Success State
    public $isSubmitted = false;
    public $submittedPaymentId = null;

    protected $queryString = ['token'];

    public function mount()
    {
        if (!empty($this->token)) {
            $this->loadInvoice($this->token);
        }
    }

    public function loadInvoice($token)
    {
        $token = trim($token);
        
        // Handle if token contains full URL (e.g., from scanned QR URL)
        if (str_contains($token, 'token=')) {
            parse_str(parse_url($token, PHP_URL_QUERY), $params);
            $token = $params['token'] ?? $token;
        } elseif (str_contains($token, '/')) {
            $parts = explode('/', $token);
            $token = end($parts);
        }

        $this->token = $token;

        $invoice = Invoice::with([
            'customer',
            'workOrders.workOrderServices.service'
        ])
        ->where('invoice_number', $token)
        ->first();

        if (!$invoice) {
            // Fallback: Check if token is SPK Number
            $workOrder = WorkOrder::where('spk_number', $token)->first();
            if ($workOrder && $workOrder->invoice) {
                $invoice = $workOrder->invoice()->with(['customer', 'workOrders.workOrderServices.service'])->first();
            }
        }

        if ($invoice) {
            $this->invoice = $invoice;
            $this->invoiceNotFound = false;
            $this->dispatch('swal:toast', icon: 'success', title: 'Invoice ' . $invoice->invoice_number . ' Berhasil Dimuat!');
        } else {
            $this->invoice = null;
            $this->invoiceNotFound = true;
            $this->dispatch('swal:toast', icon: 'error', title: 'Data Invoice tidak ditemukan. Pastikan QR Code / Token valid.');
        }
    }

    public function resetInvoice()
    {
        $this->invoice = null;
        $this->token = '';
        $this->invoiceNotFound = false;
        $this->amount = '';
        $this->proof_image = null;
        $this->notes = '';
        $this->isSubmitted = false;
        $this->submittedPaymentId = null;
    }

    public function submitPayment()
    {
        if (!$this->invoice) {
            $this->dispatch('swal:toast', icon: 'error', title: 'Silakan scan atau upload QR Code Invoice terlebih dahulu.');
            return;
        }

        // Clean amount (remove non-digits if formatted)
        $cleanAmount = preg_replace('/[^0-9]/', '', (string)$this->amount);

        $this->validate([
            'amount'         => 'required',
            'payment_method' => 'required|in:BCA,Mandiri,QRIS,Lainnya',
            'proof_image'    => 'required|image|max:10240', // Max 10MB
            'notes'          => 'nullable|string|max:500',
        ], [
            'amount.required'         => 'Nominal pembayaran wajib diisi.',
            'payment_method.required' => 'Pilih rekening bank tujuan.',
            'proof_image.required'    => 'Upload foto bukti transfer / struk pembayaran.',
            'proof_image.image'       => 'Berkas bukti bayar harus berupa gambar (JPG/PNG).',
            'proof_image.max'         => 'Ukuran berkas maksimal 10MB.',
        ]);

        if (empty($cleanAmount) || (int)$cleanAmount <= 0) {
            $this->addError('amount', 'Nominal transfer harus lebih dari Rp 0.');
            return;
        }

        try {
            // Compress & optimize image server-side (max 1200px, 75% quality)
            $filename = 'proof_' . $this->invoice->invoice_number . '_' . time() . '_' . \Illuminate\Support\Str::random(6) . '.jpg';
            $fullRelativePath = 'payment_proofs/' . $filename;

            try {
                $manager = \Intervention\Image\ImageManager::gd();
                $img = $manager->read($this->proof_image->getRealPath());
                $img->scaleDown(1200, 1200);
                $encoded = $img->toJpeg(75);
                Storage::disk('public')->put($fullRelativePath, (string) $encoded);
                $path = $fullRelativePath;
            } catch (\Throwable $imgErr) {
                Log::warning("Intervention Image fallback for payment proof: " . $imgErr->getMessage());
                $path = $this->proof_image->store('payment_proofs', 'public');
            }

            $firstWorkOrder = $this->invoice->workOrders->first();
            $spkNumbers = $this->invoice->workOrders->pluck('spk_number')->filter()->join(', ');
            if (empty($spkNumbers)) {
                $spkNumbers = $this->invoice->spk_number ?? 'SPK-' . $this->invoice->invoice_number;
            }

            $paymentType = ($this->invoice->paid_amount > 0) ? 'after' : 'before';

            $orderPayment = OrderPayment::create([
                'invoice_id'              => $this->invoice->id,
                'work_order_id'           => $firstWorkOrder?->id,
                'spk_number_snapshot'     => $spkNumbers,
                'type'                    => $paymentType,
                'amount_total'            => (float)$cleanAmount,
                'amount_service'          => (float)$cleanAmount,
                'amount_shipping'         => 0,
                'payment_method'          => $this->payment_method,
                'paid_at'                 => now(),
                'proof_image'             => $path,
                'is_verified'             => false,
                'notes'                   => ($this->notes ? $this->notes . ' ' : '') . '[Upload Mandiri dari Customer]',
                'customer_name_snapshot'  => $this->invoice->customer->name ?? '-',
                'customer_phone_snapshot' => $this->invoice->customer->phone ?? '-',
                'total_bill_snapshot'     => (float)$this->invoice->total_amount,
                'discount_snapshot'       => (float)($this->invoice->discount_amount ?? 0),
                'shipping_cost_snapshot'  => (float)($this->invoice->shipping_cost ?? 0),
                'balance_snapshot'        => (float)max(0, $this->invoice->total_amount - $this->invoice->paid_amount),
            ]);

            // Record audit log on work orders
            foreach ($this->invoice->workOrders as $wo) {
                $wo->logs()->create([
                    'user_id'     => null,
                    'step'        => 'PAYMENT',
                    'action'      => 'CUSTOMER_PAYMENT_UPLOADED',
                    'description' => "Customer mengunggah bukti pembayaran Rp " . number_format($cleanAmount, 0, ',', '.') . " via {$this->payment_method} (Menunggu Verifikasi Finance).",
                ]);
            }

            $this->isSubmitted = true;
            $this->submittedPaymentId = $orderPayment->id;
            $this->dispatch('swal:toast', icon: 'success', title: 'Bukti pembayaran berhasil dikirim!');

        } catch (\Throwable $e) {
            Log::error("Customer Payment Submit Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal mengirim bukti pembayaran: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.payment-confirmation')
            ->layout('layouts.public-portal', ['title' => 'Konfirmasi Pembayaran — Shoe Workshop']);
    }
}
