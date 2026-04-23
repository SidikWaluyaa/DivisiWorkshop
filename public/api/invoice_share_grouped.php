<?php
/**
 * Dynamic PDF Grouped Invoice Share Engine (Laravel Integrated Version)
 * 
 * Usage: GET /api/invoice_share_grouped.php?token=[SPK_CS_NUMBER]&type=[awal|akhir]
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Invoice;
use App\Models\WorkOrder;
use Dompdf\Dompdf;
use Dompdf\Options;

try {
    // 1. Validasi Akses (Hanya via token=invoice_number untuk keamanan Link Publik)
    $token = $_GET['token'] ?? null;
    $type = $_GET['type'] ?? 'awal'; // 'awal' atau 'akhir' (atau BL/L dari sistem baru)
    $format = $_GET['format'] ?? 'html'; // 'pdf' atau 'html' Default ke HTML (Web View)

    if (!$token) {
        http_response_code(400);
        die('Invalid Access: Token missing');
    }

    // 2. Setup Database Connection manually since we are outside normal request lifecycle
    $envPath = __DIR__ . '/../../.env';
    $env = [];
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (strpos($line, '=') === false) continue;
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }

    // Set simple connection for Eloquent (Assuming PDO is available)
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => $env['DB_HOST'] ?? '127.0.0.1',
        'database'  => $env['DB_DATABASE'] ?? 'forge',
        'username'  => $env['DB_USERNAME'] ?? 'forge',
        'password'  => $env['DB_PASSWORD'] ?? '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]);
    $capsule->bootEloquent();

    // 3. Find Master Invoice using Eloquent (By Token/Invoice Number Only for Security)
    $invoice = Invoice::with([
        'customer', 
        'workOrders' => function ($query) {
            $query->with(['workOrderServices.service', 'warehouseBeforePhotos']);
        }
    ])->where('invoice_number', $token)->first();

    if (!$invoice) {
        http_response_code(404);
        die('Invalid Access: Invoice not found');
    }

    // 4. Additional Validation for Final Invoice
    $status = $invoice->status;
    $isAkhir = ($type === 'akhir' || $type === 'L' || $type === 'FP' || $type === 'FULL');
    
    // Perbolehkan akses Akhir jika status sudah DP atau Lunas
    $canSeeAkhir = ($status === 'DP/Cicil' || $status === 'Lunas');
    
    if ($isAkhir && !$canSeeAkhir && $type !== 'FP') {
        http_response_code(403);
        die('Invoice belum ada pembayaran (DP/Lunas), tidak dapat melihat struk akhir.');
    }

    // 5. Render Blade Template
    // Bootstrap Laravel for view rendering
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $is_public = true;
    
    // Check format (Default to 'html' for premium web view)
    $format = $_GET['format'] ?? 'html';

    if ($format === 'pdf') {
        $html = view('finance.print-invoice-gabungan', [
            'invoice' => $invoice, 
            'is_pdf' => true, 
            'is_public' => $is_public,
            'type' => $type
        ])->render();
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "Invoice-Gabungan-" . str_replace('/', '-', $invoice->spk_number) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }

    echo view('finance.print-invoice-gabungan', [
        'invoice' => $invoice,
        'is_public' => true,
        'is_pdf' => false,
        'type' => $type
    ])->render();
    exit;

} catch (\Exception $e) {
    http_response_code(500);
    if (config('app.debug')) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "An error occurred while generating the grouped invoice.";
    }
}
