<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Services\CekatService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected CekatService $cekatService;

    public function __construct(CekatService $cekatService)
    {
        $this->cekatService = $cekatService;
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
                return "Halo Kak {$name}, update status untuk order #{$spk}: {$order->status}. Terima kasih!";
        }
    }
}
