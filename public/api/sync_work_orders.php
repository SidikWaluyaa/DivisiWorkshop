<?php
/**
 * Simple PHP API to export Work Orders for Google Sheets Sync
 * 
 * Usage: GET /api/sync_work_orders.php?token=YOUR_SECURE_TOKEN_HERE
 */

// Configuration
// SECURITY WARNING: Change this token to something random and keeps it secret!
$valid_token = 'SECRET_TOKEN_12345'; 

// 0. Load .env Configuration (Full Stack Style)
function getEnvValue($key, $default = null) {
    $path = __DIR__ . '/../../.env';
    if (!file_exists($path)) return $default;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        if (trim($name) === $key) {
            return trim($value, '"\' ');
        }
    }
    return $default;
}

// Database Configuration from Environment
$db_host = getEnvValue('DB_HOST', '127.0.0.1');
$db_user = getEnvValue('DB_USERNAME', 'root');
$db_pass = getEnvValue('DB_PASSWORD', '');
$db_name = getEnvValue('DB_DATABASE', 'sistem_workshop');

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

// 3. Query Data
// Fetching latest 500 records to avoid timeouts
$query = "SELECT 
            id, 
            spk_number as ticket_number, 
            customer_name, 
            customer_phone, 
            shoe_brand as brand, 
            shoe_type as type, 
            category, 
            status_pembayaran as payment_status, 
            status as order_status, 
            total_transaksi as total_price, 
            created_at, 
            estimation_date 
          FROM work_orders 
          ORDER BY created_at DESC 
          LIMIT 500";

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
