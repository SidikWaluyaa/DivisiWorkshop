<?php
/**
 * API Sinkronisasi Google Sheets: Data Sepatu yang Sudah Diambil (Taken Orders)
 * 
 * Penggunaan:
 * 1. Default (Otomatis 3 bulan lalu dari hari ini dengan penanganan cerdas tanggal 31):
 *    GET /api/sync_taken_orders.php?token=YOUR_TOKEN
 * 
 * 2. Menggunakan Tanggal Acuan Tertentu (Simulasi tanggal eksekusi):
 *    GET /api/sync_taken_orders.php?token=YOUR_TOKEN&ref_date=2026-09-30
 * 
 * 3. Mengatur Interval Bulan Kustom (Default 3 bulan):
 *    GET /api/sync_taken_orders.php?token=YOUR_TOKEN&months=3
 * 
 * 4. Filter Langsung Berdasarkan Taken Date Tertentu:
 *    GET /api/sync_taken_orders.php?token=YOUR_TOKEN&taken_date=2026-06-30
 */

// 1. Baca Konfigurasi .env
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

// 2. Konfigurasi Kredensial Database & Token Keamanan
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345';
$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';
$db_port = (int)($env['DB_PORT'] ?? 3306);

// Set Headers untuk JSON & CORS (Google Sheets Apps Script / ImportData)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

// 3. Verifikasi Token Keamanan
if (!isset($_GET['token']) || $_GET['token'] !== $valid_token) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized: Token tidak valid atau tidak disertakan.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. Logika Perhitungan Tanggal (Solusi Tanggal 31 & Kalender)
/**
 * Menghitung daftar tanggal target taken_date X bulan yang lalu
 * Mengatasi permasalahan tanggal 31 (Bulan pendek vs Bulan panjang)
 * 
 * Aturan:
 * 1. Hari normal (1-29/30): Mengambil tanggal yang persis sama di X bulan lalu.
 * 2. Akhir Bulan Pendek (misal 30 Nov / 28 Feb): Menarik tanggal 30 DAN tanggal 31 (atau 28, 29, 30) dari bulan target 
 *    agar data tanggal 31 TIDAK TERLEWAT/HILANG.
 * 3. Tanggal 31 di Bulan Panjang jika targetnya Bulan Pendek (misal 31 Juli -> April cuma 30 hari):
 *    Di-skip / dilewati (0 tanggal) karena data 30 April sudah ditarik kemarin pada 30 Juli (mencegah DUPLIKASI).
 */
function calculateTargetTakenDates(string $referenceDateStr, int $monthsAgo = 3): array {
    $dt = new DateTime($referenceDateStr);
    $day = (int)$dt->format('d');
    $month = (int)$dt->format('m');
    $year = (int)$dt->format('Y');
    
    $daysInCurrentMonth = (int)$dt->format('t');
    $isCurrentMonthEnd = ($day === $daysInCurrentMonth);
    
    // Hitung bulan & tahun target
    $targetMonth = $month - $monthsAgo;
    $targetYear = $year;
    while ($targetMonth <= 0) {
        $targetMonth += 12;
        $targetYear -= 1;
    }
    
    $daysInTargetMonth = (int)(new DateTime(sprintf('%04d-%02d-01', $targetYear, $targetMonth)))->format('t');
    
    // Jika tanggal hari ini > jumlah hari di bulan target (Contoh: 31 Juli -> April hanya 30 hari)
    // Tanggal 30 April sudah diambil pada 30 Juli, jadi pada 31 Juli tidak ambil apa-apa untuk mencegah duplikasi.
    if ($day > $daysInTargetMonth) {
        return [
            'dates' => [],
            'reason' => "Tanggal $day tidak ada di bulan target ($targetYear-" . sprintf('%02d', $targetMonth) . " yang hanya memiliki $daysInTargetMonth hari). Sudah tercover di hari sebelumnya untuk mencegah duplikasi."
        ];
    }
    
    $targetDates = [sprintf('%04d-%02d-%02d', $targetYear, $targetMonth, $day)];
    
    // Jika hari ini adalah HARI TERAKHIR bulan berjalan DAN bulan target punya sisa hari lebih banyak
    // (Contoh: Hari ini 30 November -> target Agustus punya 31 hari)
    // Sapu hari ke-31 Agustus agar TIDAK TERLEWAT!
    if ($isCurrentMonthEnd && $daysInTargetMonth > $day) {
        for ($extraDay = $day + 1; $extraDay <= $daysInTargetMonth; $extraDay++) {
            $targetDates[] = sprintf('%04d-%02d-%02d', $targetYear, $targetMonth, $extraDay);
        }
    }
    
    return [
        'dates' => $targetDates,
        'reason' => 'Perhitungan kalender eksak dengan catch-up akhir bulan.'
    ];
}

// Tentukan tanggal target
$months = isset($_GET['months']) ? (int)$_GET['months'] : 3;
if ($months < 1) $months = 3;

$filterDates = [];
$calculationMeta = [];

if (isset($_GET['taken_date']) && !empty($_GET['taken_date'])) {
    // Mode Manual: User langsung menentukan tanggal taken_date
    $filterDates = [trim($_GET['taken_date'])];
    $calculationMeta = [
        'mode' => 'manual_taken_date',
        'target_dates' => $filterDates,
        'note' => 'Filter menggunakan parameter taken_date manual.'
    ];
} else {
    // Mode Otomatis / Reference Date
    $refDate = isset($_GET['ref_date']) && !empty($_GET['ref_date']) 
        ? trim($_GET['ref_date']) 
        : date('Y-m-d');
    
    $calcResult = calculateTargetTakenDates($refDate, $months);
    $filterDates = $calcResult['dates'];
    $calculationMeta = [
        'mode' => 'calculated_interval',
        'reference_date' => $refDate,
        'interval_months' => $months,
        'target_dates' => $filterDates,
        'note' => $calcResult['reason']
    ];
}

// Jika tanggal target kosong (misal kasus tanggal 31 di bulan pendek yang sudah tercover kemarin)
if (empty($filterDates)) {
    echo json_encode([
        'status' => 'success',
        'meta' => $calculationMeta,
        'count' => 0,
        'data' => [],
        'message' => 'Tidak ada data untuk tanggal ini (sudah terambil pada penutupan akhir bulan sebelumnya untuk mencegah duplikasi).'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Koneksi Database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal terhubung ke database: ' . $mysqli->connect_error
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 6. Query Data Berdasarkan Filter Tanggal
// Membentuk string tanggal untuk klausa IN ('YYYY-MM-DD', ...)
$escapedDates = array_map(function($d) use ($mysqli) {
    return "'" . $mysqli->real_escape_string($d) . "'";
}, $filterDates);
$inClause = implode(',', $escapedDates);

$query = "SELECT 
            customer_name,
            customer_phone,
            GROUP_CONCAT(DISTINCT spk_number ORDER BY spk_number ASC SEPARATOR '; ') AS spk_numbers,
            COUNT(DISTINCT spk_number) AS total_spk,
            GROUP_CONCAT(DISTINCT shoe_brand ORDER BY shoe_brand ASC SEPARATOR '; ') AS shoe_brands,
            DATE(MAX(taken_date)) AS tanggal_selesai_paling_baru,
            MAX(taken_date) AS taken_date_full
          FROM work_orders
          WHERE taken_date IS NOT NULL 
            AND DATE(taken_date) IN ($inClause)
          GROUP BY customer_name, customer_phone
          ORDER BY tanggal_selesai_paling_baru DESC, customer_name ASC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Query database gagal: ' . $mysqli->error
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $mysqli->close();
    exit;
}

// 7. Format Data untuk Google Sheets
$data = [];
while ($row = $result->fetch_assoc()) {
    // Bersihkan format nomor WhatsApp/telepon jika perlu
    $cleanPhone = preg_replace('/[^0-9]/', '', $row['customer_phone'] ?? '');
    if (str_starts_with($cleanPhone, '0')) {
        $cleanPhone = '62' . substr($cleanPhone, 1);
    }

    $data[] = [
        'customer_name' => $row['customer_name'],
        'customer_phone' => $row['customer_phone'],
        'whatsapp_phone' => $cleanPhone,
        'spk_number' => $row['spk_numbers'],
        'total_spk' => (int)$row['total_spk'],
        'shoe_brand' => $row['shoe_brands'],
        'tanggal_selesai_paling_baru' => $row['tanggal_selesai_paling_baru'],
        'taken_date_full' => $row['taken_date_full']
    ];
}

// 8. Output Response JSON
echo json_encode([
    'status' => 'success',
    'endpoint' => 'sync_taken_orders.php',
    'meta' => $calculationMeta,
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$mysqli->close();
