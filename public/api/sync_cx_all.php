<?php
/**
 * Standalone API to export CX Issues with complete Work Order details for Google Sheets Sync (AppScript)
 * Includes 'created_at' and 'sent_at' at the beginning of each row as requested.
 * 
 * Usage: GET /api/sync_cx_all.php?token=YOUR_SECURE_TOKEN_HERE
 */

date_default_timezone_set('Asia/Jakarta');

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
$valid_token = $env['SYNC_API_TOKEN'] ?? $env['WORK_ORDER_SYNC_TOKEN'] ?? 'SECRET_TOKEN_12345';
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

// 4. Build Query
$query = "SELECT 
            ci.id as cx_id,
            ci.spk_number,
            ci.created_at as cx_created_at,
            ci.sent_at as cx_sent_at,
            ci.customer_name as issue_customer_name,
            ci.customer_phone as issue_customer_phone,
            wo.customer_name as order_customer_name,
            wo.customer_phone as order_customer_phone,
            wo.shoe_brand,
            wo.shoe_type,
            wo.shoe_color,
            wo.shoe_size,
            ci.source,
            ci.category,
            ci.description,
            ci.kendala,
            ci.opsi_solusi,
            ci.estimasi_tambahan,
            ci.status as issue_status,
            ci.shipping_status,
            ci.resolved_at as cx_resolved_at,
            u_reporter.name as reported_by_name,
            u_resolver.name as resolved_by_name,
            u_handler.name as cx_handler_name,
            wo.status as order_status,
            wo.entry_date,
            wo.estimation_date,
            wo.new_estimation_date,
            ci.photos
          FROM cx_issues ci
          LEFT JOIN work_orders wo ON ci.work_order_id = wo.id
          LEFT JOIN users u_reporter ON ci.reported_by = u_reporter.id
          LEFT JOIN users u_resolver ON ci.resolved_by = u_resolver.id
          LEFT JOIN users u_handler ON wo.cx_handler_id = u_handler.id
          WHERE ci.source IN ('WORKSHOP_PREP', 'WORKSHOP_SORTIR', 'WORKSHOP_PROD', 'WORKSHOP_QC')
            AND ci.created_at >= '2026-05-01 00:00:00'
          ORDER BY ci.created_at DESC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 5. Format Data
$data = [];
$baseUrl = $env['APP_URL'] ?? 'http://localhost';
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'];
}

while ($row = $result->fetch_assoc()) {
    // Determine report URL containing photos & details
    $reportUrl = '';
    if (!empty($row['spk_number'])) {
        $reportUrl = $baseUrl . '/cx-issue/' . urlencode($row['spk_number']) . '/report';
    }

    // Customer Name & Phone Fallback
    $customerName = !empty($row['order_customer_name']) ? $row['order_customer_name'] : (!empty($row['issue_customer_name']) ? $row['issue_customer_name'] : '-');
    $customerPhone = !empty($row['order_customer_phone']) ? $row['order_customer_phone'] : (!empty($row['issue_customer_phone']) ? $row['issue_customer_phone'] : '-');

    // Format Dates nicely
    $waktuMasukCx = !empty($row['cx_created_at']) ? date('Y-m-d H:i:s', strtotime($row['cx_created_at'])) : '-';
    $waktuKlikSend = !empty($row['cx_sent_at']) ? date('Y-m-d H:i:s', strtotime($row['cx_sent_at'])) : '-';
    
    $tglMasukSpk = !empty($row['entry_date']) ? date('Y-m-d H:i:s', strtotime($row['entry_date'])) : '-';
    $tglSelesaiCx = !empty($row['cx_resolved_at']) ? date('Y-m-d H:i:s', strtotime($row['cx_resolved_at'])) : '-';
    
    $estDate = '-';
    if (!empty($row['new_estimation_date'])) {
        $estDate = date('Y-m-d', strtotime($row['new_estimation_date']));
    } elseif (!empty($row['estimation_date'])) {
        $estDate = date('Y-m-d', strtotime($row['estimation_date']));
    }

    // Structure array exactly with created_at and sent_at at the very beginning
    $formattedRow = [
        'id' => $row['cx_id'],
        'spk_number' => $row['spk_number'] ?? '-',
        'waktu_masuk_cx' => $waktuMasukCx,
        'waktu_klik_send' => $waktuKlikSend,
        'customer_name' => $customerName,
        'customer_phone' => $customerPhone,
        'shoe_brand' => $row['shoe_brand'] ?? '-',
        'shoe_type' => $row['shoe_type'] ?? '-',
        'shoe_color' => $row['shoe_color'] ?? '-',
        'shoe_size' => $row['shoe_size'] ?? '-',
        'source' => $row['source'] ?? '-',
        'category' => $row['category'] ?? '-',
        'description' => $row['description'] ?? '-',
        'kendala' => $row['kendala'] ?? '-',
        'opsi_solusi' => $row['opsi_solusi'] ?? '-',
        'estimasi_tambahan' => $row['estimasi_tambahan'] ?? '',
        'status_kendala' => $row['issue_status'] ?? 'OPEN',
        'status_pengiriman' => $row['shipping_status'] ?? 'HOLD',
        'reported_by_name' => $row['reported_by_name'] ?? '-',
        'resolved_by_name' => $row['resolved_by_name'] ?? '-',
        'cx_handler_name' => $row['cx_handler_name'] ?? 'Unassigned',
        'order_status' => $row['order_status'] ?? '-',
        'tanggal_spk_masuk' => $tglMasukSpk,
        'estimasi_selesai' => $estDate,
        'tanggal_selesai_cx' => $tglSelesaiCx,
        'report_url' => $reportUrl,
    ];

    $data[] = $formattedRow;
}

// 6. Return JSON
echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
