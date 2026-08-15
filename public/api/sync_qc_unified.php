<?php
/**
 * Unified API to export ALL Warehouse QC Records (Both QC Lolos & QC Reject)
 * for Google Sheets Integration.
 * 
 * Usage: 
 * GET /api/sync_qc_unified.php?token=YOUR_SECURE_TOKEN_HERE
 * Optional parameters:
 *  - &start_date=2026-03-05
 *  - &end_date=2026-08-14
 *  - &status_qc=lolos (or reject)
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

// 3. Filtering Logic
$whereConditions = ["wo.warehouse_qc_at IS NOT NULL"];

if (!empty($_GET['start_date'])) {
    $startDate = $mysqli->real_escape_string($_GET['start_date']);
    $whereConditions[] = "wo.warehouse_qc_at >= '{$startDate} 00:00:00'";
} else {
    // Default cutoff date
    $whereConditions[] = "wo.warehouse_qc_at >= '2026-03-05 00:00:00'";
}

if (!empty($_GET['end_date'])) {
    $endDate = $mysqli->real_escape_string($_GET['end_date']);
    $whereConditions[] = "wo.warehouse_qc_at <= '{$endDate} 23:59:59'";
}

if (!empty($_GET['status_qc'])) {
    $statusQc = $mysqli->real_escape_string($_GET['status_qc']);
    $whereConditions[] = "wo.warehouse_qc_status = '{$statusQc}'";
}

$whereClause = implode(' AND ', $whereConditions);

// 4. Query Data (LEFT JOIN work_orders with cx_issues & users)
$query = "SELECT 
            wo.id AS work_order_id,
            wo.spk_number, 
            wo.customer_phone, 
            wo.customer_name, 
            wo.warehouse_qc_status AS status_qc,
            wo.warehouse_qc_notes,
            wo.suggested_services AS wo_suggested_services,
            wo.before_report_url,
            wo.warehouse_qc_at,
            ci.desc_upper,
            ci.desc_sol,
            ci.desc_kondisi_bawaan,
            ci.rec_service_1 AS cx_rec_service_1,
            ci.rec_service_2 AS cx_rec_service_2,
            ci.sug_service_1 AS cx_sug_service_1,
            ci.sug_service_2 AS cx_sug_service_2,
            ci.suggested_services AS cx_suggested_services,
            ci.recommended_services AS cx_recommended_services,
            ci.status AS cs_followup_status,
            u.name AS qc_by_name
          FROM work_orders wo
          LEFT JOIN cx_issues ci ON ci.work_order_id = wo.id AND ci.source = 'GUDANG'
          LEFT JOIN users u ON wo.warehouse_qc_by = u.id
          WHERE {$whereClause}
          ORDER BY wo.warehouse_qc_at ASC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 5. Format Output
$serverHost = $_SERVER['HTTP_HOST'] ?? 'sistemworkshop.test';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$baseUrl = $protocol . "://" . $serverHost;

$data = [];
while ($row = $result->fetch_assoc()) {
    $statusQc = strtolower($row['status_qc'] ?? 'lolos');
    $isReject = ($statusQc === 'reject');

    // Parse Services from JSON if wo_suggested_services is set
    $parsedWoServices = [];
    if (!empty($row['wo_suggested_services'])) {
        $servicesData = is_string($row['wo_suggested_services']) ? json_decode($row['wo_suggested_services'], true) : $row['wo_suggested_services'];
        if (is_array($servicesData)) {
            foreach ($servicesData as $svc) {
                if (is_string($svc)) {
                    $parsedWoServices[] = $svc;
                } elseif (is_array($svc)) {
                    $name = $svc['name'] ?? ($svc['service_name'] ?? '');
                    $price = isset($svc['price']) && $svc['price'] !== '' ? ' (Rp ' . number_format((int)$svc['price'], 0, ',', '.') . ')' : '';
                    if ($name) {
                        $parsedWoServices[] = $name . $price;
                    }
                }
            }
        }
    }

    // Recommended Services (Rec 1 & Rec 2)
    $rec1 = !empty($row['cx_rec_service_1']) ? $row['cx_rec_service_1'] : ($parsedWoServices[0] ?? '');
    $rec2 = !empty($row['cx_rec_service_2']) ? $row['cx_rec_service_2'] : ($parsedWoServices[1] ?? '');

    // Suggested / Optional Services (Sug 1 & Sug 2)
    $sug1 = !empty($row['cx_sug_service_1']) ? $row['cx_sug_service_1'] : ($parsedWoServices[2] ?? '');
    $sug2 = !empty($row['cx_sug_service_2']) ? $row['cx_sug_service_2'] : ($parsedWoServices[3] ?? '');

    // Links
    // 1. link_before_report (For QC Lolos & General Orders)
    $beforeReportUrl = $row['before_report_url'] ?? '';

    // 2. report_url (Public QC Reject Report URL - For QC Reject)
    $rejectReportUrl = $isReject ? ($baseUrl . '/reception/qc-reject/' . urlencode($row['spk_number'])) : '';

    $data[] = [
        'spk_number' => $row['spk_number'],
        'customer_name' => $row['customer_name'] ?? '',
        'customer_phone' => $row['customer_phone'] ?? '',
        'status_qc' => $statusQc,
        'warehouse_qc_at' => $row['warehouse_qc_at'],
        'warehouse_qc_notes' => $row['warehouse_qc_notes'] ?? '',
        
        // Rejection Details (Only for Reject)
        'desc_upper' => $isReject ? ($row['desc_upper'] ?? '') : '',
        'desc_sol' => $isReject ? ($row['desc_sol'] ?? '') : '',
        'desc_kondisi_bawaan' => $isReject ? ($row['desc_kondisi_bawaan'] ?? '') : '',
        
        // Recommended & Suggested Services
        'rec_service_1' => $rec1,
        'rec_service_2' => $rec2,
        'sug_service_1' => $sug1,
        'sug_service_2' => $sug2,
        
        // Links
        'before_report_url' => $beforeReportUrl,
        'report_url' => $rejectReportUrl,
        
        // CS Metadata
        'cs_followup_status' => $isReject ? ($row['cs_followup_status'] ?? 'OPEN') : '-',
        'qc_by' => $row['qc_by_name'] ?? ''
    ];
}

// 6. Return Clean JSON Response
echo json_encode([
    'status' => 'success',
    'total_count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
