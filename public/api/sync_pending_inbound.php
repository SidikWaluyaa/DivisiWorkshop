<?php
/**
 * API to export SPK_PENDING data without Inbound Tracking Number for Google Sheets Sync
 * 
 * Usage: 
 *   GET /api/sync_pending_inbound.php?token=YOUR_SECURE_TOKEN_HERE
 * 
 * Optional Parameters:
 *   &days=30           -> Filter SPK dibuat dalam N hari terakhir (<= N hari)
 *   &days_exact=11     -> Filter SPK tepat N hari lalu (DATEDIFF = N)
 *   &start_date=Y-m-d  -> Filter tanggal mulai (created_at >= start_date)
 *   &end_date=Y-m-d    -> Filter tanggal selesai (created_at <= end_date)
 *   &limit=100         -> Batas jumlah baris (default: semua data)
 */

date_default_timezone_set('Asia/Jakarta');

// 1. Load Database Credentials & Token from .env
$envPath = __DIR__ . '/../../.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) continue;
        if (strpos($trimmed, '=') === false) continue;
        [$key, $value] = explode('=', $trimmed, 2);
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

// Set Headers for JSON & Cross-Origin (Google Sheets / Google Apps Script)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2. Security Check (Token Authentication)
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized: Invalid or missing token.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 3. Database Connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $mysqli->connect_error
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$mysqli->set_charset('utf8mb4');

// 4. Build Query Conditions
// Base condition: SPK_PENDING and no customer tracking number (inbound resi)
$whereClauses = [
    "status = 'SPK_PENDING'",
    "(customer_tracking_number IS NULL OR TRIM(customer_tracking_number) = '')"
];

// Optional: Filter by exact age in days (e.g., ?days_exact=11)
if (isset($_GET['days_exact']) && is_numeric($_GET['days_exact'])) {
    $daysExact = (int) $_GET['days_exact'];
    $whereClauses[] = "DATEDIFF(NOW(), created_at) = {$daysExact}";
}

// Optional: Filter by maximum age in days (e.g., ?days=30 for created within last 30 days)
if (isset($_GET['days']) && is_numeric($_GET['days'])) {
    $daysMax = (int) $_GET['days'];
    $whereClauses[] = "DATEDIFF(NOW(), created_at) <= {$daysMax}";
}

// Optional: Filter by date range (?start_date=2026-09-01&end_date=2026-09-30)
if (!empty($_GET['start_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['start_date'])) {
    $startDate = $mysqli->real_escape_string($_GET['start_date']);
    $whereClauses[] = "DATE(created_at) >= '{$startDate}'";
}
if (!empty($_GET['end_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['end_date'])) {
    $endDate = $mysqli->real_escape_string($_GET['end_date']);
    $whereClauses[] = "DATE(created_at) <= '{$endDate}'";
}

$whereSql = implode(' AND ', $whereClauses);

// Optional: Limit
$limitSql = '';
if (isset($_GET['limit']) && is_numeric($_GET['limit']) && (int)$_GET['limit'] > 0) {
    $limit = (int) $_GET['limit'];
    $limitSql = " LIMIT {$limit}";
}

// 5. Execute Query (Strictly selecting customer_name, customer_phone, spk_number, created_at)
$query = "SELECT 
            customer_name, 
            customer_phone, 
            spk_number, 
            created_at 
          FROM work_orders 
          WHERE {$whereSql}
          ORDER BY created_at DESC, id DESC
          {$limitSql}";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Query failed: ' . $mysqli->error
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $mysqli->close();
    exit;
}

// 6. Format Data (Ensure exact strict 4 fields per row)
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'customer_name'  => $row['customer_name'] ?? '',
        'customer_phone' => $row['customer_phone'] ?? '',
        'spk_number'     => $row['spk_number'] ?? '',
        'created_at'     => $row['created_at'] ?? ''
    ];
}

$result->free();
$mysqli->close();

// 7. Output JSON Response
echo json_encode([
    'status'      => 'success',
    'module'      => 'SPK Pending Inbound (Tanpa Resi)',
    'description' => 'Data Work Orders SPK_PENDING tanpa resi pengiriman inbound (customer_tracking_number kosong)',
    'count'       => count($data),
    'data'        => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
