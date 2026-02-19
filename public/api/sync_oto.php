<?php
/**
 * API to export OTO (One Time Offer) data for automation/sync
 * 
 * Usage: GET /api/sync_oto.php?token=YOUR_SECURE_TOKEN_HERE
 * Optional: &status=PENDING_CX,CONTACTED,ACCEPTED (filter by status)
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

// 3. Build Query
$where = "WHERE 1=1";

// Optional status filter
if (!empty($_GET['status'])) {
    $statuses = array_map(function($s) use ($mysqli) {
        return "'" . $mysqli->real_escape_string(trim($s)) . "'";
    }, explode(',', $_GET['status']));
    $where .= " AND o.status IN (" . implode(',', $statuses) . ")";
}

$query = "SELECT 
            o.id,
            o.spk_number,
            o.customer_name,
            o.customer_phone,
            o.title,
            o.oto_type,
            o.proposed_services,
            o.total_normal_price,
            o.total_oto_price,
            o.total_discount,
            o.discount_percent,
            o.dp_required,
            o.dp_paid,
            o.status,
            o.valid_until,
            o.created_at,
            o.customer_responded_at,
            o.cx_contacted_at,
            o.cx_contact_method,
            o.cx_notes,
            o.cx_follow_up_count,
            wo.custom_name AS item_name,
            wo.category AS item_category,
            wo.priority,
            creator.name AS created_by_name,
            cx.name AS cx_assigned_name
          FROM otos o
          LEFT JOIN work_orders wo ON o.work_order_id = wo.id
          LEFT JOIN users creator ON o.created_by = creator.id
          LEFT JOIN users cx ON o.cx_assigned_to = cx.id
          {$where}
          ORDER BY o.created_at DESC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
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
