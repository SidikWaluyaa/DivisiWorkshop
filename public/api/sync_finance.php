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

// 3. Build Filter
$where = "WHERE created_at > '2026-02-01 00:00:00'";

// Optional status filter
if (!empty($_GET['status'])) {
    $statusList = explode(',', $_GET['status']);
    $safeStatus = [];
    foreach ($statusList as $s) {
        $safeStatus[] = "'" . $mysqli->real_escape_string(trim($s)) . "'";
    }
    $where .= " AND status IN (" . implode(',', $safeStatus) . ")";
}

// 4. Query Data
// Use JOIN to ensure the report follows the central data (Live)
$query = "SELECT
            status,
            spk_number,
            customer_name,
            customer_phone,
            CASE 
                WHEN status_pembayaran = 'L' THEN 'L'
                WHEN status_pembayaran = 'DP/Cicil' THEN 'BL'
                WHEN status_pembayaran = 'Belum Bayar' THEN 'BB'
                ELSE 'BB'
            END as status_pembayaran,
            payment_method AS payment_type,
            total_paid AS amount_paid,
            total_transaksi AS total_bill,
            discount,
            shipping_cost,
            sisa_tagihan AS remaining_balance,
            invoice_awal AS invoice_awal_url,
            invoice_akhir AS invoice_akhir_url,
            created_at,
            updated_at
        FROM work_orders
        $where
        ORDER BY created_at DESC";

$result = $mysqli->query($query);
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    exit;
}

// 4. Format Data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// 5. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
