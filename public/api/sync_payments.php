<?php
/**
 * Simple PHP API to export individual Payment Transactions (Kas Masuk) for Google Sheets Sync
 * 1 Baris = 1 Transaksi Pembayaran
 * 
 * Usage: GET /api/sync_payments.php?token=YOUR_SECURE_TOKEN_HERE&date_from=2026-10-01&date_to=2026-10-31
 */

// Configuration
$valid_token = 'SECRET_TOKEN_12345'; 

// Database Configuration (Default / Fallback)
$db_host = '127.0.0.1';
$db_user = 'sql_info_shoewor';
$db_pass = '16d2a1344b13c';
$db_name = 'sql_info_shoewor';

// Load credentials dynamically from .env if available
$env_file = __DIR__ . '/../../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            $val = trim($val, '"\'');
            if ($key === 'SYNC_API_TOKEN') $valid_token = $val;
            if ($key === 'DB_HOST') $db_host = $val;
            if ($key === 'DB_USERNAME') $db_user = $val;
            if ($key === 'DB_PASSWORD') $db_pass = $val;
            if ($key === 'DB_DATABASE') $db_name = $val;
        }
    }
}

// Set Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow Google Sheets to access

// 1. Security Check
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Invalid or missing token.']);
    exit;
}

// 2. Database Connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

// 3. Filter Parameters (date_from, date_to, date_field, updated_since)
$whereClauses = [];

// A. Filter updated_since (sync bertahap)
if (!empty($_GET['updated_since'])) {
    $updatedSince = trim($_GET['updated_since']);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $updatedSince)) {
        $updatedSince .= ' 00:00:00';
    }
    $escapedUpdatedSince = $mysqli->real_escape_string($updatedSince);
    $whereClauses[] = "op.updated_at >= '{$escapedUpdatedSince}'";
}

// B. Filter date_from & date_to dengan date_field dinamis
$allowedDateFields = [
    'payment_date' => 'COALESCE(op.paid_at, op.created_at)',
    'paid_at'      => 'op.paid_at',
    'created_at'   => 'op.created_at',
    'updated_at'   => 'op.updated_at',
];

$dateFieldParam = isset($_GET['date_field']) ? trim($_GET['date_field']) : 'payment_date';
$targetDateField = $allowedDateFields[$dateFieldParam] ?? 'COALESCE(op.paid_at, op.created_at)';

if (!empty($_GET['date_from'])) {
    $dateFrom = trim($_GET['date_from']);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
        $dateFrom .= ' 00:00:00';
    }
    $escapedDateFrom = $mysqli->real_escape_string($dateFrom);
    $whereClauses[] = "{$targetDateField} >= '{$escapedDateFrom}'";
}

if (!empty($_GET['date_to'])) {
    $dateTo = trim($_GET['date_to']);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
        $dateTo .= ' 23:59:59';
    }
    $escapedDateTo = $mysqli->real_escape_string($dateTo);
    $whereClauses[] = "{$targetDateField} <= '{$escapedDateTo}'";
}

// Default fallback jika tidak ada parameter filter tanggal sama sekali
if (empty($whereClauses)) {
    $whereClauses[] = "COALESCE(op.paid_at, op.created_at) > '2026-02-01 00:00:00'";
}

$whereSql = implode(' AND ', $whereClauses);

// 4. Query Data
// 1 baris = 1 transaksi pembayaran
$query = "SELECT 
            op.id as payment_id,
            COALESCE(
                i_direct.invoice_number,
                i_wo.invoice_number,
                CASE WHEN op.spk_number_snapshot LIKE 'INV-%' THEN op.spk_number_snapshot ELSE NULL END,
                wo.spk_number,
                op.spk_number_snapshot,
                ''
            ) as invoice_number,
            COALESCE(
                c_direct.name,
                c_wo.name,
                op.customer_name_snapshot,
                ''
            ) as customer_name,
            COALESCE(op.paid_at, op.created_at) as payment_date,
            CASE 
                WHEN UPPER(op.type) IN ('BEFORE', 'DP') THEN 'DP'
                WHEN UPPER(op.type) IN ('AFTER', 'PELUNASAN', 'LUNAS_AWAL') THEN 'PELUNASAN'
                WHEN UPPER(op.type) = 'OTO' THEN 'OTO'
                WHEN UPPER(op.type) = 'ONGKIR' THEN 'ONGKIR'
                WHEN UPPER(op.type) = 'TAMBAH_JASA' THEN 'TAMBAH_JASA'
                ELSE COALESCE(NULLIF(UPPER(op.type), ''), 'LAIN-LAIN')
            END as payment_category,
            COALESCE(NULLIF(op.payment_method, ''), 'Transfer') as payment_method,
            op.amount_total as gross_cash_in,
            CASE 
                WHEN UPPER(op.type) = 'ONGKIR' THEN 0
                WHEN op.amount_service > 0 THEN op.amount_service
                WHEN op.amount_shipping > 0 THEN GREATEST(0, op.amount_total - op.amount_shipping)
                ELSE op.amount_total
            END as net_service_amount,
            CASE 
                WHEN op.amount_shipping > 0 THEN op.amount_shipping
                WHEN UPPER(op.type) = 'ONGKIR' THEN op.amount_total
                ELSE 0
            END as shipping_amount,
            COALESCE(op.notes, '') as notes,
            op.is_verified,
            op.created_at,
            op.updated_at
          FROM order_payments op
          LEFT JOIN invoices i_direct ON op.invoice_id = i_direct.id
          LEFT JOIN customers c_direct ON i_direct.customer_id = c_direct.id
          LEFT JOIN work_orders wo ON op.work_order_id = wo.id
          LEFT JOIN invoices i_wo ON wo.invoice_id = i_wo.id
          LEFT JOIN customers c_wo ON i_wo.customer_id = c_wo.id
          WHERE {$whereSql}
          ORDER BY COALESCE(op.paid_at, op.created_at) DESC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 5. Format Data
$data = [];
while ($row = $result->fetch_assoc()) {
    $row['payment_id'] = (int)$row['payment_id'];
    $row['gross_cash_in'] = (float)$row['gross_cash_in'];
    $row['net_service_amount'] = (float)$row['net_service_amount'];
    $row['shipping_amount'] = (float)$row['shipping_amount'];
    $row['is_verified'] = (int)$row['is_verified'];
    $data[] = $row;
}

// 6. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
