<?php
/**
 * API Sinkronisasi Data SPK R&D untuk Google Sheets
 * 
 * Mengambil data gabungan antara tabel work_orders (SPK R&D)
 * dan tabel work_order_rnd_progress (Jurnal Progres R&D).
 * 
 * Penggunaan:
 * 1. Default (Flat table row - ideal untuk ditarik langsung ke tabel Google Sheets):
 *    GET /api/sync_spk_rnd.php?token=YOUR_TOKEN
 * 
 * 2. Filter berdasarkan Status SPK:
 *    GET /api/sync_spk_rnd.php?token=YOUR_TOKEN&status=PRODUKSI
 * 
 * 3. Filter berdasarkan Rentang Tanggal SPK:
 *    GET /api/sync_spk_rnd.php?token=YOUR_TOKEN&start_date=2026-10-01&end_date=2026-10-31
 * 
 * 4. Format Terkelompok (Nested array per SPK):
 *    GET /api/sync_spk_rnd.php?token=YOUR_TOKEN&format=grouped
 */

date_default_timezone_set('Asia/Jakarta');

// 1. Baca Konfigurasi dari file .env
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

// 2. Konfigurasi Token Keamanan & Database
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345';
$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';
$db_port = (int)($env['DB_PORT'] ?? 3306);

// 3. Set Headers untuk JSON & CORS (Google Sheets / Google Apps Script)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 4. Verifikasi Token Keamanan
$client_token = $_GET['token'] ?? '';
if (empty($client_token) || $client_token !== $valid_token) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized: Token autentikasi tidak valid atau belum diisi.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Koneksi Database MySQL
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $mysqli->connect_error
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
$mysqli->set_charset('utf8mb4');

// 6. Parameter Filter
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
$format = isset($_GET['format']) ? strtolower(trim($_GET['format'])) : 'flat';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

// 7. Bangun Query SQL
$whereClauses = [
    "(wo.priority = 'R&D' OR wo.spk_number LIKE 'RD-%')",
    "wo.deleted_at IS NULL"
];

if (!empty($filter_status)) {
    $safe_status = $mysqli->real_escape_string($filter_status);
    $whereClauses[] = "wo.status = '{$safe_status}'";
}

if (!empty($start_date)) {
    $safe_start = $mysqli->real_escape_string($start_date);
    $whereClauses[] = "wo.created_at >= '{$safe_start} 00:00:00'";
}

if (!empty($end_date)) {
    $safe_end = $mysqli->real_escape_string($end_date);
    $whereClauses[] = "wo.created_at <= '{$safe_end} 23:59:59'";
}

$whereSql = implode(' AND ', $whereClauses);

$query = "SELECT 
            wo.id AS work_order_id,
            wo.spk_number,
            wo.customer_name,
            wo.customer_phone,
            wo.status,
            wo.priority,
            wo.created_at AS spk_created_at,
            prog.id AS progress_id,
            prog.stage_title,
            prog.notes,
            prog.report_url,
            prog.result_status,
            prog.created_at AS progress_created_at
          FROM work_orders wo
          LEFT JOIN work_order_rnd_progress prog 
            ON wo.id = prog.work_order_id
          WHERE {$whereSql}
          ORDER BY wo.id DESC, prog.created_at ASC";

if ($limit > 0) {
    $query .= " LIMIT " . $limit;
}

$result = $mysqli->query($query);
if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Query gagal dieksekusi: ' . $mysqli->error
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $mysqli->close();
    exit;
}

// 8. Pemrosesan Data Output
if ($format === 'grouped') {
    // Mode Grouped: 1 item per SPK dengan array kumpulan progress
    $groupedData = [];
    while ($row = $result->fetch_assoc()) {
        $woId = $row['work_order_id'];
        if (!isset($groupedData[$woId])) {
            $groupedData[$woId] = [
                'work_order_id'   => (int)$row['work_order_id'],
                'spk_number'      => $row['spk_number'] ?? '',
                'customer_name'   => $row['customer_name'] ?? '',
                'customer_phone'  => $row['customer_phone'] ?? '',
                'status'          => $row['status'] ?? '',
                'spk_created_at'  => $row['spk_created_at'] ?? '',
                'total_progress'  => 0,
                'progress_list'   => []
            ];
        }

        if (!empty($row['progress_id']) || !empty($row['stage_title'])) {
            $groupedData[$woId]['progress_list'][] = [
                'stage_title'         => $row['stage_title'] ?? '',
                'notes'               => $row['notes'] ?? '',
                'report_url'          => $row['report_url'] ?? '',
                'result_status'       => $row['result_status'] ?? '',
                'progress_created_at' => $row['progress_created_at'] ?? ''
            ];
            $groupedData[$woId]['total_progress']++;
        }
    }
    $finalData = array_values($groupedData);
} else {
    // Mode Flat (Default): Sesuai struktur baris tabular untuk Google Sheet
    $finalData = [];
    while ($row = $result->fetch_assoc()) {
        $finalData[] = [
            'spk_number'          => $row['spk_number'] ?? '',
            'customer_name'       => $row['customer_name'] ?? '',
            'customer_phone'      => $row['customer_phone'] ?? '',
            'status'              => $row['status'] ?? '',
            'stage_title'         => $row['stage_title'] ?? '',
            'notes'               => $row['notes'] ?? '',
            'report_url'          => $row['report_url'] ?? '',
            'result_status'       => $row['result_status'] ?? '',
            'progress_created_at' => $row['progress_created_at'] ?? '',
            'spk_created_at'      => $row['spk_created_at'] ?? ''
        ];
    }
}

// 9. Output Respon JSON
echo json_encode([
    'status'        => 'success',
    'total_records' => count($finalData),
    'timestamp'     => date('Y-m-d H:i:s'),
    'data'          => $finalData
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$mysqli->close();
