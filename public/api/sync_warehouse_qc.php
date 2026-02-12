<?php
/**
 * Simple PHP API to export Work Order Warehouse QC Status for Google Sheets Sync
 * 
 * Usage: GET /api/sync_warehouse_qc.php?token=YOUR_SECURE_TOKEN_HERE
 */

// Configuration
// SECURITY WARNING: Change this token to something random and keep it secret!
$valid_token = 'SECRET_TOKEN_12345'; 

// Database Configuration
// Note: In production, it's better to parse the .env file. 
// These values should match your local/server database setup.
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'sistem_workshop';

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
// Fetching all records ordered by Warehouse QC date as requested
$query = "SELECT 
            spk_number, 
            customer_phone, 
            customer_name, 
            warehouse_qc_status as status_qc,
            warehouse_qc_at
          FROM work_orders 
          ORDER BY warehouse_qc_at DESC";

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
