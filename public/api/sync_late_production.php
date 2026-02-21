<?php
/**
 * Standalone API to export LATE PRODUCTION DATA for Google Sheets Sync (AppScript)
 * Monitors orders that are overdue or approaching deadline.
 * 
 * Usage: GET /api/sync_late_production.php?token=YOUR_SECURE_TOKEN_HERE
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
        // Strip quotes if they exist
        if (preg_match('/^"(.+)"$/', $value, $matches) || preg_match("/^'(.+)'$/", $value, $matches)) {
            $value = $matches[1];
        }
        $env[trim($key)] = $value;
    }
}

// Configuration
// Check SYNC_API_TOKEN first, then fallback to SECRET_TOKEN_12345
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345'; 
$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';

// Set Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow external access (e.g. Google Sheets)

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

// 4. Set Timezone for MySQL (Sync with App)
$db_tz = $env['DB_TIMEZONE'] ?? '+07:00';
$mysqli->query("SET time_zone = '$db_tz'");

// 5. Query Late Production Data
// Filters for orders in PRODUCTION status
$query = "SELECT 
            spk_number, 
            customer_name, 
            estimation_date,
            new_estimation_date,
            late_description,
            DATEDIFF(estimation_date, NOW()) as sisa_hari,
            CASE 
                WHEN DATEDIFF(estimation_date, NOW()) < 0 THEN 'LATE'
                WHEN DATEDIFF(estimation_date, NOW()) <= 5 THEN 'WARNING'
                ELSE 'ON TRACK'
            END as warning_status,
            CASE 
                WHEN DATEDIFF(estimation_date, NOW()) < 0 THEN 1
                WHEN DATEDIFF(estimation_date, NOW()) <= 5 THEN 2
                ELSE 3
            END as priority_scale
          FROM work_orders 
          WHERE status = 'PRODUCTION'
          ORDER BY priority_scale ASC, sisa_hari ASC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 6. Format Data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// 7. Return JSON
echo json_encode([
    'status' => 'success',
    'module' => 'Late Production Monitor',
    'timestamp' => date('Y-m-d H:i:s'),
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
