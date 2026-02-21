<?php
/**
 * API to export CUSTOMER DATA for Google Sheets Sync (AppScript)
 * Includes verified address details.
 * 
 * Usage: GET /api/sync_customers.php?token=YOUR_SECURE_TOKEN_HERE
 */

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
$valid_token = $env['SYNC_API_TOKEN'] ?? 'SECRET_TOKEN_12345'; // Default fallback if not in .env
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

// 4. Query All Customer Data with latest addresses
$query = "SELECT 
            id,
            name,
            phone,
            email,
            address,
            province,
            city,
            district,
            village,
            postal_code,
            address_token,
            address_verification_url,
            created_at,
            updated_at
          FROM customers 
          ORDER BY updated_at DESC";

$result = $mysqli->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $mysqli->error]);
    $mysqli->close();
    exit;
}

// 5. Format Data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// 6. Return JSON
echo json_encode([
    'status' => 'success',
    'module' => 'Customers',
    'description' => 'Data Pelanggan dengan Alamat Terverifikasi',
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
