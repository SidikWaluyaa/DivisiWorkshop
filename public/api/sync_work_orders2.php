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

// Dynamic .env loading to support both local (Laragon) and production (aaPanel)
$env_file = dirname(__DIR__, 2) . '/.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    $lines = explode("\n", $env_content);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            $val = trim($val, '"\' ');
            if ($key === 'DB_HOST') $db_host = $val;
            if ($key === 'DB_USERNAME') $db_user = $val;
            if ($key === 'DB_PASSWORD') $db_pass = $val;
            if ($key === 'DB_DATABASE') $db_name = $val;
        }
    }
}

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
            COALESCE(
                cl_spk.channel,
                cl_direct.channel,
                cl_phone.channel,
                CASE 
                    WHEN wo.spk_number LIKE 'N-%' OR wo.spk_number LIKE 'O-%' OR wo.spk_number LIKE 'P-%' THEN 'ONLINE'
                    ELSE 'OFFLINE'
                END
            ) as channel,
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
          LEFT JOIN cs_spk spk ON (wo.id = spk.work_order_id OR wo.spk_number = spk.spk_number) AND spk.deleted_at IS NULL
          LEFT JOIN cs_leads cl_spk ON spk.cs_lead_id = cl_spk.id AND cl_spk.deleted_at IS NULL
          LEFT JOIN cs_leads cl_direct ON wo.id = cl_direct.converted_to_work_order_id AND cl_direct.deleted_at IS NULL
          LEFT JOIN (
              SELECT t1.customer_phone, t1.channel
              FROM cs_leads t1
              JOIN (
                  SELECT MAX(id) as max_id
                  FROM cs_leads
                  WHERE deleted_at IS NULL
                  GROUP BY customer_phone
              ) t2 ON t1.id = t2.max_id
          ) cl_phone ON (
              CASE 
                  WHEN wo.customer_phone LIKE '628%' THEN SUBSTRING(wo.customer_phone, 3)
                  WHEN wo.customer_phone LIKE '08%' THEN SUBSTRING(wo.customer_phone, 2)
                  WHEN wo.customer_phone LIKE '+628%' THEN SUBSTRING(wo.customer_phone, 4)
                  ELSE wo.customer_phone
              END
          ) = (
              CASE 
                  WHEN cl_phone.customer_phone LIKE '628%' THEN SUBSTRING(cl_phone.customer_phone, 3)
                  WHEN cl_phone.customer_phone LIKE '08%' THEN SUBSTRING(cl_phone.customer_phone, 2)
                  WHEN cl_phone.customer_phone LIKE '+628%' THEN SUBSTRING(cl_phone.customer_phone, 4)
                  ELSE cl_phone.customer_phone
              END
          )
          WHERE wo.created_at > '2026-02-10 00:00:00' AND wo.deleted_at IS NULL
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
