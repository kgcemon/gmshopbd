<?php
include "connection.php";

// Function to send JSON response
function sendResponse($status, $message, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Get webhook data
$rawData = file_get_contents('php://input');
$headers = getallheaders();

// Log incoming webhook data
error_log("Webhook Raw Data: " . $rawData);
error_log("Webhook Headers: " . json_encode($headers));

// Handle both JSON and form data
$postData = json_decode($rawData, true);
if (json_last_error() === JSON_ERROR_NONE) {
    $_POST = $postData;
}

// Check for required fields
if (!isset($_POST['sender']) || !isset($_POST['sms'])) {
    sendResponse(false, 'Missing required fields', 400);
}

$security_key = $_POST['security_key'] ?? '';
$sender = $_POST['sender'];
$sms = $_POST['sms'];
$date_time = $_POST['date_time'] ?? date('Y-m-d H:i:s');

// Validate request method and security key
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $security_key !== SECURITY_KEY) {
    sendResponse(false, 'Unauthorized request', 401);
}

// Check if sender is in allowed list
if (!in_array($sender, ALLOWED_SENDERS)) {
    sendResponse(true, 'SMS received', 200);
}

// Add to queue (example with Redis)
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379); // Redis server address
    $queueData = json_encode($_POST);
    $redis->lPush('sms_queue', $queueData);
    $redis->close();
    sendResponse(true, 'SMS added to queue', 200);
} catch (Exception $e) {
    error_log("Redis error: " . $e->getMessage());
    sendResponse(false, 'Failed to add SMS to queue', 500);
}
?>