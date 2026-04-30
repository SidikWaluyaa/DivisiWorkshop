<?php
/**
 * API to export Finance Payments (Revenue) for Google Sheets Sync - Version 2
 * Usage: GET /api/sync_finance_v2.php?token=YOUR_SECURE_TOKEN_HERE
 */

/***********************
 * LOAD ENV
 ***********************/
$envPath = __DIR__ . '/../../.env';
$env = [];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (strpos($line, '=') === false) continue;

        [$key, $value] = explode('=', $line, 2);
        $value = trim($value);

        if (preg_match('/^"(.+)"$/', $value, $matches) || preg_match("/^'(.+)'$/", $value, $matches)) {
            $value = $matches[1];
        }

        $env[trim($key)] = $value;
    }
}

/***********************
 * CONFIG
 ***********************/
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345';
$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

/***********************
 * SECURITY CHECK
 ***********************/
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/***********************
 * DB CONNECTION
 ***********************/
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

/***********************
 * QUERY (VERSION 2)
 ***********************/
$query = "SELECT 
    i.invoice_number as nomor_invoice, 
    c.name AS customer_name,
    c.phone AS customer_phone, 
    i.status AS status_pembayaran_gabungan,
    i.paid_amount AS amount_paid,
    i.total_amount AS total_bill,
    i.shipping_cost,
    (i.total_amount + COALESCE(i.shipping_cost, 0) - i.paid_amount - i.discount) AS remaining_balance,
    i.discount,
    i.invoice_dp_url,
    i.invoice_final_url,
    i.invoice_full_url,
    i.dp_unique_code,
    i.final_unique_code,
    i.total_dp_with_code as total_dp,
    i.total_pelunasan_with_code as total_pelunasan,
    i.spk_status,
    i.estimasi_selesai,
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

/***********************
 * FORMAT DATA
 ***********************/
$data = [];

while ($row = $result->fetch_assoc()) {

    $statusPembayaran = $row['status_pembayaran_gabungan'] ?? '';

    // Convert ke format Bot WA
    $botStatus = 'BB'; // default Belum Bayar
    if ($statusPembayaran === 'Lunas') $botStatus = 'L';
    if ($statusPembayaran === 'DP/Cicil') $botStatus = 'BL';

    $formatted_row = [
        'status' => 'IN_PROGRESS',
        'spk_number' => $row['nomor_invoice'],
        'customer_name' => $row['customer_name'],
        'customer_phone' => $row['customer_phone'],
        'status_pembayaran' => $botStatus,
        'spk_status' => $row['spk_status'] ?? 'BELUM SELESAI',
        'amount_paid' => $row['amount_paid'],
        'total_bill' => $row['total_bill'],
        'discount' => $row['discount'],
        'shipping_cost' => $row['shipping_cost'] ?? 0,
        'remaining_balance' => $row['remaining_balance'],
        'invoice_dp_url' => $row['invoice_dp_url'] ?? null,
        'invoice_final_url' => $row['invoice_final_url'] ?? null,
        'invoice_full_url' => $row['invoice_full_url'] ?? null,
        'dp_unique_code' => $row['dp_unique_code'] ?? 0,
        'final_unique_code' => $row['final_unique_code'] ?? 0,
        'total_dp' => $row['total_dp'] ?? 0,
        'total_pelunasan' => $row['total_pelunasan'] ?? 0,
        'estimasi_selesai' => $row['estimasi_selesai'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];

    $data[] = $formatted_row;
}

/***********************
 * RETURN JSON
 ***********************/
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
