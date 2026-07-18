<?php
/**
 * Simple PHP API to export Work Order Warranties for Google Sheets Sync
 * 
 * Usage: GET /api/sync_warranties.php?token=YOUR_SECURE_TOKEN_HERE
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
$query = "SELECT 
            wow.id,
            wow.work_order_id,
            wo.spk_number AS original_spk_number,
            wo.customer_name,
            wo.customer_phone,
            wo.shoe_brand,
            wo.shoe_type,
            wow.garansi_spk_number,
            wow.description,
            wow.penggunaan,
            wow.photos,
            wow.status AS warranty_status,
            uc.name AS created_by_name,
            uf.name AS finished_by_name,
            wow.finished_at,
            wow.created_at,
            wow.updated_at
          FROM work_order_warranties wow
          LEFT JOIN work_orders wo ON wow.work_order_id = wo.id
          LEFT JOIN users uc ON wow.created_by = uc.id
          LEFT JOIN users uf ON wow.finished_by = uf.id
          ORDER BY wow.created_at DESC";

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
$baseUrl = isset($_SERVER['HTTP_HOST']) ? ($protocol . "://" . $_SERVER['HTTP_HOST']) : '';

while ($row = $result->fetch_assoc()) {
    // Decode photos if present
    if (!empty($row['photos']) && $row['photos'] !== 'null' && $row['photos'] !== '[]') {
        $photos = json_decode($row['photos'], true);
        if (is_array($photos)) {
            $formattedPhotos = [];
            foreach ($photos as $photo) {
                if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
                    $formattedPhotos[] = $photo;
                } else {
                    $formattedPhotos[] = $baseUrl . '/storage/' . ltrim($photo, '/');
                }
            }
            $row['photos'] = implode(', ', $formattedPhotos);
        }
    } else {
        $row['photos'] = '';
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
