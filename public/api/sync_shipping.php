<?php
/**
 * Simple PHP API to export Shippings for Google Sheets Sync
 * 
 * Usage: GET /api/sync_shipping.php?token=YOUR_SECURE_TOKEN_HERE
 */

// Configuration
$valid_token = 'SECRET_TOKEN_12345'; 

// Database Configuration
$db_host = '127.0.0.1';
$db_user = 'sql_info_shoewor';
$db_pass = '16d2a1344b13c';
$db_name = 'sql_info_shoewor';

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
            id, 
            work_order_id,
            tanggal_masuk, 
            customer_name, 
            customer_phone, 
            spk_number, 
            is_verified, 
            kategori_pengiriman,
            tanggal_pengiriman,
            pic,
            resi_pengiriman,
            created_at
          FROM shippings 
          ORDER BY tanggal_masuk DESC";

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
    // Cast is_verified to boolean/string for sheet readability
    $row['is_verified'] = $row['is_verified'] ? 'Yes' : 'No';
    $data[] = $row;
}

// 5. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
