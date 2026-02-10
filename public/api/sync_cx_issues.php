<?php
/**
 * Simple PHP API to export CX Issues for Google Sheets Sync
 * 
 * Usage: GET /api/sync_cx_issues.php?token=YOUR_SECURE_TOKEN_HERE
 */

// Configuration
// SECURITY WARNING: Change this token to something random and keeps it secret!
$valid_token = 'SECRET_TOKEN_12345'; 

// Database Configuration
// Based on your .env file
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'sistem_workshop';

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
// Fetching latest CX issues with Order and Reporter info
$query = "SELECT 
            cx.id,
            cx.spk_number,
            cx.customer_name,
            cx.type as issue_type,
            cx.category as issue_category,
            cx.description as issue_description,
            cx.suggested_services,
            cx.status as issue_status,
            cx.resolution,
            cx.resolution_notes,
            cx.photos,
            u.name as reporter_name,
            cx.created_at,
            cx.resolved_at
          FROM cx_issues cx
          LEFT JOIN users u ON cx.reported_by = u.id
          ORDER BY cx.created_at DESC 
          LIMIT 500";

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
    'count' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT);

$mysqli->close();
