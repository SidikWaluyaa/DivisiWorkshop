<?php
/**
 * Simple PHP API to export CX Issues for Google Sheets Sync
 * 
 * Usage: GET /api/sync_cx_issues.php?token=YOUR_SECURE_TOKEN_HERE
 *        GET /api/sync_cx_issues.php?token=YOUR_SECURE_TOKEN_HERE&source=GUDANG
 *        GET /api/sync_cx_issues.php?token=YOUR_SECURE_TOKEN_HERE&source=WORKSHOP
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

// 3. Build Query with optional source filter
$whereConditions = ["ci.created_at > '2026-02-01 00:00:00'"];
$sourceLabel = 'ALL';

if (isset($_GET['source'])) {
    $filterSource = strtoupper($_GET['source']);
    if ($filterSource === 'GUDANG') {
        $whereConditions[] = "ci.source = 'GUDANG'";
        $sourceLabel = 'GUDANG';
    } elseif ($filterSource === 'WORKSHOP') {
        $whereConditions[] = "ci.source IN ('WORKSHOP_PREP', 'WORKSHOP_SORTIR', 'WORKSHOP_PROD', 'WORKSHOP_QC')";
        $sourceLabel = 'WORKSHOP';
    } elseif ($filterSource === 'MANUAL') {
        $whereConditions[] = "ci.source = 'MANUAL'";
        $sourceLabel = 'MANUAL';
    } elseif (in_array($filterSource, ['WORKSHOP_PREP', 'WORKSHOP_SORTIR', 'WORKSHOP_PROD', 'WORKSHOP_QC'])) {
        $whereConditions[] = "ci.source = '" . $mysqli->real_escape_string($filterSource) . "'";
        $sourceLabel = $filterSource;
    }
}

$whereClause = implode(' AND ', $whereConditions);

$query = "SELECT ci.*, u.name as reported_by_name
          FROM cx_issues ci
          LEFT JOIN users u ON ci.reported_by = u.id
          WHERE {$whereClause}
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
    // Decode photos if present
    if (!empty($row['photos'])) {
        $photos = json_decode($row['photos'], true);
        if (is_array($photos)) {
            // Convert to full URLs
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
    'source_filter' => $sourceLabel,
    'available_filters' => ['ALL', 'GUDANG', 'WORKSHOP', 'MANUAL', 'WORKSHOP_PREP', 'WORKSHOP_SORTIR', 'WORKSHOP_PROD', 'WORKSHOP_QC'],
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
