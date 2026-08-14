<?php
/**
 * Simple PHP API to export Work Order Warehouse QC Status for Google Sheets Sync
 * 
 * Usage: GET /api/sync_warehouse_qc.php?token=YOUR_SECURE_TOKEN_HERE
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
header('Access-Control-Allow-Origin: *'); // Allow external access (e.g. Google Sheets)

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
            spk_number, 
            customer_phone, 
            customer_name, 
            warehouse_qc_status as status_qc,
            warehouse_qc_notes,
            suggested_services,
            before_report_url,
            warehouse_qc_at
          FROM work_orders 
          WHERE warehouse_qc_at > '2026-03-05 00:00:00'
          ORDER BY warehouse_qc_at ASC";

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
    $rec1 = '';
    $rec2 = '';
    
    if (!empty($row['suggested_services'])) {
        $services = is_string($row['suggested_services']) ? json_decode($row['suggested_services'], true) : $row['suggested_services'];
        if (is_array($services)) {
            $formattedList = [];
            foreach ($services as $svc) {
                if (is_string($svc)) {
                    $formattedList[] = $svc;
                } elseif (is_array($svc)) {
                    $name = $svc['name'] ?? ($svc['service_name'] ?? '');
                    $price = isset($svc['price']) ? ' (Rp ' . number_format($svc['price'], 0, ',', '.') . ')' : '';
                    if ($name) {
                        $formattedList[] = $name . $price;
                    }
                }
            }
            if (isset($formattedList[0])) {
                $rec1 = $formattedList[0];
            }
            if (isset($formattedList[1])) {
                $rec2 = $formattedList[1];
            }
        }
    }

    $data[] = [
        'spk_number' => $row['spk_number'],
        'customer_phone' => $row['customer_phone'],
        'customer_name' => $row['customer_name'],
        'status_qc' => $row['status_qc'],
        'warehouse_qc_notes' => $row['warehouse_qc_notes'] ?? '',
        'rec_service_1' => $rec1,
        'rec_service_2' => $rec2,
        'before_report_url' => $row['before_report_url'],
        'warehouse_qc_at' => $row['warehouse_qc_at'],
    ];
}

// 5. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();