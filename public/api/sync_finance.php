<?php
/**
 * API to export Finance Payments (Revenue) for Google Sheets Sync
 * 
 * Usage: GET /api/sync_finance.php?token=YOUR_SECURE_TOKEN_HERE
 */

// 1. Load Database Credentials & Token from .env (Robust Version)
$envPath = __DIR__ . '/../../.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $value = trim($value);
        // Strip quotes if they exist
        if (preg_match('/^"(.+)"$/', $value, $matches) || preg_match("/^'(.+)'$/", $value, $matches)) {
            $value = $matches[1];
        }
        $env[trim($key)] = $value;
    }
}

// Configuration
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345';
$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';

// Set Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 1. Security Check
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Database Connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

// 3. Query Data from NEW invoices table
$query = "SELECT 
    i.invoice_number AS spk_number_induk, 
    c.name AS customer_name,
    c.phone AS customer_phone, 
    i.status AS status_pembayaran_gabungan,
    i.paid_amount AS amount_paid,
    i.total_amount AS total_bill,
    i.shipping_cost,
    (i.total_amount + COALESCE(i.shipping_cost, 0) - i.paid_amount - i.discount) AS remaining_balance,
    i.discount,
    i.invoice_awal_url,
    i.invoice_akhir_url,
    i.created_at,
    i.updated_at
FROM 
    invoices i
LEFT JOIN 
    customers c ON i.customer_id = c.id
WHERE 
    i.created_at > '2026-02-01 00:00:00'
ORDER BY 
    i.created_at DESC";

$result = $mysqli->query($query);
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    exit;
}

// 4. Format Data & Tentukan URL Logika
$data = [];
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

while ($row = $result->fetch_assoc()) {
    $statusPembayaran = $row['status_pembayaran_gabungan'];
    
    // Convert status string to Bot WA format if necessary
    $botStatus = 'BB'; // default
    if ($statusPembayaran === 'Lunas') $botStatus = 'L';
    if ($statusPembayaran === 'DP/Cicil') $botStatus = 'BL';
    
    // Use URLs from database
    $invoice_awal_url = $row['invoice_awal_url'] ?? null;
    $invoice_akhir_url = $row['invoice_akhir_url'] ?? null;
    
    // Susun data rapi seperti format JSON lama agar Bot WA tidak kaget (Backwards Compatible)
    $formatted_row = [
        'status' => 'IN_PROGRESS', // Hardcode default or derive from child work orders if really needed, but generally if invoiced, it's at least IN_PROGRESS or SELESAI
        'spk_number' => $row['spk_number_induk'], // Nota Gabungan (INV-xxxx)
        'customer_name' => $row['customer_name'],
        'customer_phone' => $row['customer_phone'],
        'status_pembayaran' => $botStatus,
        'payment_type' => 'TRANSFER', // placeholder, can be enhanced to read from payments table later
        'amount_paid' => $row['amount_paid'],
        'total_bill' => $row['total_bill'],
        'discount' => $row['discount'],
        'shipping_cost' => $row['shipping_cost'] ?? 0,
        'remaining_balance' => $row['remaining_balance'],
        'invoice_awal_url' => $invoice_awal_url,
        'invoice_akhir_url' => $invoice_akhir_url,
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];
    
    $data[] = $formatted_row;
}

// 5. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
