<?php
/**
 * API to export Finance Payments (Revenue) for Google Sheets Sync
 * 
 * Usage: GET /api/sync_finance.php?token=YOUR_SECURE_TOKEN_HERE
 */

// Auto-read .env for database credentials
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

// 3. Query Data
// Use JOIN to ensure the report follows the central data (Live)
$query = "SELECT 
            p.id as payment_id,
            w.spk_number,
            w.customer_name,
            w.customer_phone,
            (SELECT GROUP_CONCAT(COALESCE(ws.custom_service_name, s.name, 'Layanan') SEPARATOR ' | ') 
             FROM work_order_services ws 
             LEFT JOIN services s ON ws.service_id = s.id 
             WHERE ws.work_order_id = p.work_order_id) as services,
            p.type as payment_type,
            p.amount_total as amount_paid,
            w.total_transaksi as total_bill,
            w.discount,
            w.shipping_cost,
            w.sisa_tagihan as remaining_balance,
            CASE 
                WHEN w.status_pembayaran = 'L' THEN 'L'
                WHEN w.status_pembayaran = 'DP/Cicil' THEN 'BL'
                WHEN w.status_pembayaran = 'Belum Bayar' THEN 'BB'
                ELSE COALESCE(w.status_pembayaran, 'BB')
            END as status_pembayaran,
            p.payment_method,
            p.paid_at,
            u.name as pic_finance,
            p.notes as audit_notes,
            -- Pre-generated Links from Database
            w.invoice_awal as invoice_awal_url,
            w.invoice_akhir as invoice_akhir_url
          FROM order_payments p
          JOIN work_orders w ON p.work_order_id = w.id
          LEFT JOIN users u ON p.pic_id = u.id
          ORDER BY p.paid_at DESC
          LIMIT 1000";

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
