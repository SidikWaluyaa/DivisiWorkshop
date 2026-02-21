<?php
/**
 * Single API to handle Customer Address Verification
 * Handles both GET (Fetch) and POST (Update)
 * 
 * Usage: 
 * GET  /api/address_verification.php?token=XYZ
 * POST /api/address_verification.php (with token and address in body)
 */

// 1. Load Database Credentials from .env
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

$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_user = $env['DB_USERNAME'] ?? 'sql_info_shoewor';
$db_pass = $env['DB_PASSWORD'] ?? '16d2a1344b13c';
$db_name = $env['DB_DATABASE'] ?? 'sql_info_shoewor';

// Set Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// 2. Database Connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// 3. Handle GET Request (Fetch Data or Proxy Regional API)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if it's a proxy request for regional data
    if (isset($_GET['proxy_path'])) {
        $path = $_GET['proxy_path'];
        
        // Safety: Only allow alphanumeric, underscores, and forward slashes
        if (!preg_match('/^[a-zA-Z0-9_\/]+$/', $path)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid proxy path format']);
            exit;
        }

        $remote_url = "https://emsifa.github.io/api-wilayah-indonesia/api/{$path}.json";
        
        $context = stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: PHP\r\n"
            ]
        ]);
        
        $data = @file_get_contents($remote_url, false, $context);
        if ($data === false) {
            http_response_code(502);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch regional data']);
        } else {
            echo $data;
        }
        exit;
    }

    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Token is required']);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, name, phone, email, address, city, city_id, province, province_id, district, district_id, village, village_id, postal_code FROM customers WHERE address_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if (!$customer) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Customer not found or token invalid']);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $customer
    ]);
    exit;
}

// 4. Handle POST Request (Update Data)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle both form-data and raw JSON input
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    $token = $input['token'] ?? '';
    $address = $input['address'] ?? '';
    $province = $input['province'] ?? '';
    $province_id = $input['province_id'] ?? '';
    $city = $input['city'] ?? '';
    $city_id = $input['city_id'] ?? '';
    $district = $input['district'] ?? '';
    $district_id = $input['district_id'] ?? '';
    $village = $input['village'] ?? '';
    $village_id = $input['village_id'] ?? '';
    $postal_code = $input['postal_code'] ?? '';

    if (empty($token) || empty($address)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Token and Address are required']);
        exit;
    }

    // Update query
    $stmt = $mysqli->prepare("UPDATE customers SET address = ?, province = ?, province_id = ?, city = ?, city_id = ?, district = ?, district_id = ?, village = ?, village_id = ?, postal_code = ? WHERE address_token = ?");
    $stmt->bind_param("sssssssssss", 
        $address, 
        $province, $province_id,
        $city, $city_id,
        $district, $district_id,
        $village, $village_id,
        $postal_code, 
        $token
    );
    
    if ($stmt->execute()) {
        $mysqli->query("UPDATE work_orders SET customer_address = '$address' WHERE customer_phone = (SELECT phone FROM customers WHERE address_token = '$token') AND (status NOT IN ('SELESAI', 'BATAL', 'DONASI') OR status IS NULL)");
        
        if ($mysqli->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Address updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'No changes made or customer not found'
            ]);
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update database: ' . $mysqli->error]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);

$mysqli->close();
