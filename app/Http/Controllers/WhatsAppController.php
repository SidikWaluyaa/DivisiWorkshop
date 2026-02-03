<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Contracts\MessagingService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected MessagingService $messagingService;

    public function __construct(MessagingService $messagingService)
    {
        $this->messagingService = $messagingService;
    }

    public function send(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        $type = $request->input('type', 'general'); // e.g. received, production, finish

        $message = $this->generateMessage($order, $type);
        
        // Format Number for WA URL (628...)
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        
        // Redirect to WhatsApp Web/App
        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
        
        return redirect()->away($waUrl);
        return redirect()->away($waUrl);
    }

    /**
     * Test sending a template message via Cekat API.
     */
    public function sendTemplateTest(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Example Template: "order_status_update"
        // Params: {{1}} = Name, {{2}} = SPK, {{3}} = Status
        
        // GET CONFIG FROM ENV
        $templateId = env('CEKAT_DEFAULT_TEMPLATE_ID');

        if (empty($templateId)) {
            return back()->with('error', 'Konfigurasi CEKAT_DEFAULT_TEMPLATE_ID belum ada di .env');
        }
        
        // Prepare parameters based on what the template likely needs
        $params = [
            $order->customer_name,
            $order->spk_number,
            $order->status
        ];

        // Pass Phone, Template ID, and Params
        $result = $this->messagingService->sendTemplate($order->customer_phone, $templateId, $params);

        if ($result['success']) {
            return back()->with('success', 'Template Whatsapp berhasil dikirim!');
        } else {
            // Show full result (error + debug_info) for troubleshooting
            return back()->with('error', 'Gagal kirim Template: ' . json_encode($result));
        }
    }

    // Helper to format currency
    private function formatRupiah($amount)
    {
        return 'Rp' . number_format($amount, 0, ',', '.');
    }

    private function generateMessage(WorkOrder $order, string $type): string
    {
        $name = $order->customer_name;
        $spk = $order->spk_number;
        $shoe = "{$order->shoe_brand} {$order->shoe_type} ({$order->shoe_color})";

        switch ($type) {
            case 'received':
                return "Halo Kak {$name}! \n\nTerima kasih sudah mempercayakan perawatan sepatu kakak di Shoe Workshop. \nOrder #{$spk} ({$shoe}) sudah kami terima dengan aman dan sedang kami cek kondisinya. \n\nKami akan kabari lagi saat pengerjaan dimulai ya!";

            case 'production':
                return "Update Sepatu Kak {$name} \n\nSekarang sepatu {$shoe} kakak sedang masuk tahap pengerjaan (Cuci/Reparasi) oleh teknisi kami. Mohon doanya agar hasilnya maksimal ya! \n\nTerima kasih sudah menunggu.";

            case 'finish':
                return "Kabar Gembira! \n\nSepatu {$shoe} punya Kak {$name} sudah SELESAI dan lolos Quality Control. \nHasilnya sudah bersih & rapi siap untuk diajak jalan-jalan lagi.\n\nSilakan bisa diambil di workshop kami atau hubungi kami untuk pengiriman ya.";

            default:
                $statusLabel = $order->status instanceof \App\Enums\WorkOrderStatus ? $order->status->value : $order->status;
                return "Halo Kak {$name}, update status untuk order #{$spk}: {$statusLabel}. Terima kasih!";
        }
    }
}
