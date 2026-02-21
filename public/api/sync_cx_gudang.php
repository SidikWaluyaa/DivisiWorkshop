<?php
/**
 * API to export CX Issues FROM GUDANG (Reception QC Reject) for Google Sheets Sync
 * 
 * Usage: GET /api/sync_cx_gudang.php?token=YOUR_SECURE_TOKEN_HERE
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

// 3. Query - Only GUDANG source CX Issues
$query = "SELECT 
            ci.id,
            ci.work_order_id,
            ci.spk_number,
            ci.customer_name,
            ci.customer_phone,
            ci.source,
            ci.category,
            ci.description,
            ci.desc_upper,
            ci.desc_sol,
            ci.desc_kondisi_bawaan,
            ci.rec_service_1,
            ci.rec_service_2,
            ci.sug_service_1,
            ci.sug_service_2,
            ci.suggested_services,
            ci.recommended_services,
            ci.photos,
            ci.status,
            ci.resolution,
            ci.resolution_notes,
            ci.created_at,
            ci.resolved_at,
            u.name as reported_by_name
          FROM cx_issues ci
          LEFT JOIN users u ON ci.reported_by = u.id
          WHERE ci.source = 'GUDANG'
          ORDER BY ci.created_at DESC";

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
    if (!empty($row['photos'])) {
        $photos = json_decode($row['photos'], true);
        if (is_array($photos)) {
            $row['photos'] = array_map(function($path) use ($baseUrl) {
                return $baseUrl . '/' . ltrim($path, '/');
            }, $photos);
        } else {
            $row['photos'] = [];
        }
    } else {
        $row['photos'] = [];
    }
    $data[] = $row;
}

// 5. Return JSON
echo json_encode([
    'status' => 'success',
    'source' => 'GUDANG',
    'description' => 'Follow Up dari QC Gudang (Reception Reject)',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
