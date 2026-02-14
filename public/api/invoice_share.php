<?php
/**
 * Dynamic PDF Invoice Share Engine (Laravel Integrated Version)
 * 
 * Usage: GET /api/invoice_share.php?token=[TOKEN]&type=[awal|akhir]
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// 1. Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use Dompdf\Dompdf;
use Dompdf\Options;

// 2. Validate Parameters
$token = $_GET['token'] ?? '';
$type = $_GET['type'] ?? 'awal'; // awal | akhir

if (empty($token)) {
    http_response_code(400);
    die('Invalid Access: Token missing');
}

// 3. Find Work Order using Eloquent
$order = WorkOrder::where('invoice_token', $token)->first();

if (!$order) {
    http_response_code(404);
    die('Invalid Access: Order not found or token invalid');
}

// 4. Additional Validation for Final Invoice
if ($type === 'akhir' && $order->status_pembayaran !== 'L') {
    http_response_code(403);
    die('Final invoice is only available for fully paid orders.');
}

// 5. Render Blade Template
try {
    // We pass is_pdf to trigger specific styling in the Blade template
    $html = view('finance.print-invoice', ['order' => $order, 'is_pdf' => true])->render();
    
    // Debug HTML if requested
    if (isset($_GET['debug_html'])) {
        // We pass is_pdf to trigger specific styling in the Blade template
        $html = view('finance.print-invoice', ['order' => $order, 'is_pdf' => true])->render();
        echo $html;
        exit;
    }
    
    // 6. PDF Configuration
    // Flag to the view that this is a public share link
    $is_public = true;
    
    // Check format (Default to 'html' for premium web view)
    $format = $_GET['format'] ?? 'html';

    if ($format === 'pdf') {
        // PDF Generation Logic (Keep it as an option)
        // We pass is_pdf to trigger specific styling in the Blade template
        $html = view('finance.print-invoice', ['order' => $order, 'is_pdf' => true, 'is_public' => $is_public])->render();
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Allow loading static Tailwind via CDN link
        $options->set('defaultFont', 'sans-serif');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "Invoice-" . str_replace('/', '-', $order->spk_number) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }

    // Default: Premium Web View
    echo view('finance.print-invoice', [
        'order' => $order,
        'is_public' => true
    ]);
    exit;

} catch (\Exception $e) {
    http_response_code(500);
    if (config('app.debug')) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "An error occurred while generating the invoice PDF.";
    }
}
