<?php
/**
 * Simple PHP API to export Work Orders for Google Sheets Sync
 * 
 * Usage: GET /api/sync_work_orders.php?token=YOUR_SECURE_TOKEN_HERE
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
    $whereClauses[] = "wo.updated_at >= '{$escapedUpdatedSince}'";
}

// B. Filter date_from & date_to dengan date_field dinamis
$allowedDateFields = [
    'created_at'    => 'wo.created_at',
    'entry_date'    => 'wo.entry_date',
    'finished_date' => 'wo.finished_date',
    'taken_date'    => 'wo.taken_date',
    'updated_at'    => 'wo.updated_at',
];

$dateFieldParam = isset($_GET['date_field']) ? trim($_GET['date_field']) : 'created_at';
$targetDateField = $allowedDateFields[$dateFieldParam] ?? 'wo.created_at';

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
    $whereClauses[] = "wo.created_at > '2026-02-10 00:00:00'";
}

$whereSql = implode(' AND ', $whereClauses);

// 4. Query Data
// Fetching latest records using indexed joins for high performance (sub-15ms)
$query = "SELECT 
            wo.id, 
            wo.spk_number as ticket_number, 
            i.invoice_number,
            wo.customer_name, 
            wo.customer_phone, 
            wo.shoe_brand as brand, 
            wo.shoe_type as type, 
            wo.category, 
            wo.status_pembayaran as payment_status, 
            wo.status as order_status, 
            COALESCE(
                NULLIF(wo.channel, ''),
                cl_spk.channel,
                cl_direct.channel,
                CASE 
                    WHEN wo.spk_number LIKE 'N-%' OR wo.spk_number LIKE 'O-%' OR wo.spk_number LIKE 'P-%' THEN 'ONLINE'
                    ELSE 'OFFLINE'
                END
            ) as channel,
            wo.cs_code,
            wo.total_transaksi as total_price, 
            wo.entry_date,
            wo.finished_date,
            wo.taken_date,
            wo.created_at, 
            wo.updated_at,
            wo.deleted_at,
            CASE 
                WHEN wo.deleted_at IS NOT NULL OR wo.status = 'CANCELLED' THEN 1 
                ELSE 0 
            END as is_cancelled,
            wo.estimation_date,
            wo.waktu
          FROM work_orders wo
          LEFT JOIN invoices i ON wo.invoice_id = i.id
          LEFT JOIN cs_spk spk ON wo.id = spk.work_order_id AND spk.deleted_at IS NULL
          LEFT JOIN cs_leads cl_spk ON spk.cs_lead_id = cl_spk.id AND cl_spk.deleted_at IS NULL
          LEFT JOIN cs_leads cl_direct ON wo.id = cl_direct.converted_to_work_order_id AND cl_direct.deleted_at IS NULL
          WHERE {$whereSql}
          ORDER BY wo.created_at DESC";

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
    $row['id'] = (int)$row['id'];
    $row['is_cancelled'] = (int)$row['is_cancelled'];
    $data[] = $row;
}

// 6. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
