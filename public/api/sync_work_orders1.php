<?php
/**
 * Simple PHP API to export Work Orders for Google Sheets Sync
 * 
 * Usage: GET /api/sync_work_orders.php?token=YOUR_SECURE_TOKEN_HERE
 */

// Configuration
// SECURITY WARNING: Change this token to something random and keeps it secret!
$valid_token = 'SECRET_TOKEN_12345'; 

// Database Configuration
// Based on your .env file
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
// Fetching latest 500 records to avoid timeouts
$query = "SELECT 
            wo.id, 
            wo.spk_number as ticket_number, 
            wo.customer_name, 
            wo.customer_phone, 
            cl.channel,
            wo.shoe_brand as brand, 
            wo.shoe_type as type, 
            wo.category, 
            wo.status_pembayaran as payment_status, 
            wo.status as order_status, 
            wo.total_transaksi as total_price, 
            wo.created_at, 
            wo.estimation_date,
            wo.waktu
          FROM work_orders wo
          LEFT JOIN (
            SELECT t1.customer_phone, t1.channel
            FROM cs_leads t1
            JOIN (
                SELECT MAX(id) as max_id
                FROM cs_leads
                WHERE deleted_at IS NULL
                GROUP BY customer_phone
            ) t2 ON t1.id = t2.max_id
          ) cl ON (
            CASE 
                WHEN wo.customer_phone LIKE '628%' THEN SUBSTRING(wo.customer_phone, 3)
                WHEN wo.customer_phone LIKE '08%' THEN SUBSTRING(wo.customer_phone, 2)
                WHEN wo.customer_phone LIKE '+628%' THEN SUBSTRING(wo.customer_phone, 4)
                ELSE wo.customer_phone
            END
          ) = (
            CASE 
                WHEN cl.customer_phone LIKE '628%' THEN SUBSTRING(cl.customer_phone, 3)
                WHEN cl.customer_phone LIKE '08%' THEN SUBSTRING(cl.customer_phone, 2)
                WHEN cl.customer_phone LIKE '+628%' THEN SUBSTRING(cl.customer_phone, 4)
                ELSE cl.customer_phone
            END
          )
          WHERE wo.created_at > '2026-02-10 00:00:00'
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
