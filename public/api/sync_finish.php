<?php
/**
 * API to export Finished Work Orders with Photos for confirmation
 * 
 * Usage: GET /api/sync_finish.php?token=YOUR_SECURE_TOKEN_HERE
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
// Query specifically for finished items with their photos
$query = "SELECT 
            wo.id,
            wo.spk_number,
            wo.category as jenis_barang,
            wo.customer_name,
            wo.customer_phone,
            wo.status,
            wop.step,
            wo.finish_report_url as link_pdf
          FROM work_orders wo
          LEFT JOIN work_order_photos wop 
            ON wo.id = wop.work_order_id
          WHERE wop.step = 'FINISH'
          ORDER BY wo.created_at DESC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 4. Format Data
$data = [];
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'];

while ($row = $result->fetch_assoc()) {
    // Convert relative paths to absolute URLs
    if (!empty($row['finish_report_url']) && !str_starts_with($row['finish_report_url'], 'http')) {
        $row['finish_report_url'] = $baseUrl . '/' . ltrim($row['finish_report_url'], '/');
    }
    
    if (!empty($row['sample_photo']) && !str_starts_with($row['sample_photo'], 'http')) {
        // Handle 'storage/' prefix if missing
        $path = ltrim($row['sample_photo'], '/');
        if (!str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }
        $row['sample_photo'] = $baseUrl . '/' . $path;
    }
    
    $data[] = $row;
}

// 5. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
