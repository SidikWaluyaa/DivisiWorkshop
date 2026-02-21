<?php
/**
 * API to export OTO (One Time Offer) data
 * 
 * Usage:
 * GET /api/sync_oto.php?token=YOUR_TOKEN
 * Optional:
 * &status=PENDING_CX,CONTACTED,ACCEPTED
 */

// ================================
// AUTO LOAD .ENV (Robust Version)
// ================================
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

// ================================
// CONFIG
// ================================
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345';

$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';

// ================================
// HEADERS
// ================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ================================
// SECURITY CHECK
// ================================
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit;
}

// ================================
// CONNECT DB
// ================================
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database Connection Failed',
        'detail' => $mysqli->connect_error
    ]);
    exit;
}

// ================================
// BUILD FILTER
// ================================
$where = "WHERE 1=1";

// Filter by status (optional)
if (!empty($_GET['status'])) {
    $statusList = explode(',', $_GET['status']);
    $safeStatus = [];
    foreach ($statusList as $s) {
        $safeStatus[] = "'" . $mysqli->real_escape_string(trim($s)) . "'";
    }
    $where .= " AND status IN (" . implode(',', $safeStatus) . ")";
}

// ================================
// QUERY (OTOS ONLY)
// ================================
$sql = "
SELECT
    id,
    work_order_id,
    spk_number,
    customer_name,
    customer_phone,
    title,
    description,
    oto_type,
    proposed_services,
    total_normal_price,
    total_oto_price,
    total_discount,
    discount_percent,
    estimated_days,
    valid_until,
    status,
    created_at,
    updated_at
FROM otos
{$where}
ORDER BY created_at DESC
";

// ================================
// EXECUTE
// ================================
$result = $mysqli->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Query Failed',
        'detail' => $mysqli->error
    ]);
    $mysqli->close();
    exit;
}

// ================================
// FETCH DATA
// ================================
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// ================================
// RESPONSE
// ================================
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
