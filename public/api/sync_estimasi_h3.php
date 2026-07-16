<?php
/**
 * Standalone API to export H-3 ESTIMATION DATA for Google Sheets Sync (AppScript)
 * Monitors orders that have 3 days or less remaining until deadline (including overdue).
 * 
 * Usage: GET /api/sync_estimasi_h3.php?token=YOUR_SECURE_TOKEN_HERE
 */

// 1. Load Database Credentials & Token from .env
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

// Configuration
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345'; 
$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';

// Set Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 2. Security Check
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Invalid or missing token.']);
    exit;
}

// 3. Database Connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

// 4. Set Timezone for MySQL
$db_tz = $env['DB_TIMEZONE'] ?? '+07:00';
$mysqli->query("SET time_zone = '$db_tz'");

// 5. Query H-3 Estimation Data
// Exclude finished/cancelled/donated orders.
$query = "SELECT 
            id,
            spk_number, 
            customer_name, 
            customer_phone,
            shoe_brand,
            shoe_type,
            status,
            estimation_date,
            new_estimation_date,
            COALESCE(new_estimation_date, estimation_date) as effective_estimation_date,
            DATEDIFF(COALESCE(new_estimation_date, estimation_date), CURDATE()) as sisa_hari,
            CASE 
                WHEN DATEDIFF(COALESCE(new_estimation_date, estimation_date), CURDATE()) < 0 THEN 'OVERDUE'
                WHEN DATEDIFF(COALESCE(new_estimation_date, estimation_date), CURDATE()) = 0 THEN 'DUE TODAY'
                ELSE CONCAT(DATEDIFF(COALESCE(new_estimation_date, estimation_date), CURDATE()), ' HARI LAGI')
            END as status_estimasi
        FROM work_orders 
        WHERE status NOT IN ('SELESAI', 'DIANTAR', 'HISTORY', 'BATAL', 'DONASI', 'SPK_PENDING')
        AND (
            (new_estimation_date IS NOT NULL AND DATEDIFF(new_estimation_date, CURDATE()) <= 3)
            OR 
            (new_estimation_date IS NULL AND estimation_date IS NOT NULL AND DATEDIFF(estimation_date, CURDATE()) <= 3)
        )
        ORDER BY sisa_hari ASC, id ASC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 6. Format Data
$data = [];
$appUrl = rtrim($env['APP_URL'] ?? 'http://localhost', '/');

while ($row = $result->fetch_assoc()) {
    $row['detail_url'] = $appUrl . '/admin/orders/' . $row['id'];
    $data[] = $row;
}

// 7. Return JSON
echo json_encode([
    'status' => 'success',
    'module' => 'H-3 Estimation Monitor',
    'timestamp' => date('Y-m-d H:i:s'),
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
