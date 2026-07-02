<?php
include "connection.php";

// Define constants
define("SECURITY_KEY", "1234");
define("ALLOWED_SENDERS", ["bKash", "NAGAD", "Nagad", "16216"]);
define("BKASH_NUMBERS", ["bKash", "16247"]);
define("NAGAD_NUMBERS", ["NAGAD", "16167"]);
define("ROCKET_NUMBERS", ["Rocket", "16216"]);

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

$security_key = $_POST['security_key'] ?? '1234';
$sender = $_POST['sender'];
$sms = $_POST['sms'];
$date_time = $_POST['date_time'] ?? date('Y-m-d H:i:s');

// Log all incoming SMS
error_log(sprintf("SMS Received - Sender: %s, Message: %s, Time: %s", $sender, $sms, $date_time));



// Check if sender is in allowed list
if (!in_array($sender, ALLOWED_SENDERS)) {
    sendResponse(true, 'SMS received', 200);
}

// Check database connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    sendResponse(false, 'Database connection failed', 500);
}

$txn_id = '';
$amount = '';
$payment_number = '';
$is_valid_sms = false;

// Validate bKash SMS - Type 1
if ((in_array($sender, BKASH_NUMBERS) || strcasecmp($sender, "bKash") === 0) 
    && strpos($sms, "You have received") !== false 
    && preg_match('/You have received Tk ([\d,]+(?:\.\d{2})?) from (\d+).* TrxID (\w+)/', $sms, $matches)) {
    $amount = str_replace(',', '', $matches[1]);
    $payment_number = $matches[2];
    $txn_id = $matches[3];
    $is_valid_sms = true;
}
// Validate bKash SMS - Type 2
elseif ((in_array($sender, BKASH_NUMBERS) || strcasecmp($sender, "bKash") === 0) 
    && strpos($sms, "Cash In") !== false 
    && preg_match('/Cash In Tk ([\d,]+(?:\.\d{2})?) from (\d+).* TrxID (\w+)/', $sms, $matches)) {
    $amount = str_replace(',', '', $matches[1]);
    $payment_number = $matches[2];
    $txn_id = $matches[3];
    $is_valid_sms = true;
}
// Validate Nagad SMS
elseif ((in_array($sender, NAGAD_NUMBERS) || strcasecmp($sender, "NAGAD") === 0) 
    && (preg_match('/Amount: Tk ([\d,]+(?:\.\d{2})?).*Sender: (\d+).*TxnID: (\w+).*Balance: Tk ([\d,]+\.\d{2})/s', $sms, $matches)
        || preg_match('/Amount: Tk ([\d,]+(?:\.\d{2})?).*Uddokta: (\d+).*TxnID: (\w+).*Balance: ([\d,]+\.\d{2})/s', $sms, $matches))) {

    $amount = str_replace(',', '', $matches[1]);
    $payment_number = $matches[2]; // Sender or Uddokta Number
    $txn_id = $matches[3]; // Transaction ID
    $balance = str_replace(',', '', $matches[4]); // Balance
    $is_valid_sms = true;
}

// Validate Rocket SMS
elseif ((in_array($sender, ROCKET_NUMBERS) || strcasecmp($sender, "Rocket") === 0)
    && strpos($sms, "received from") !== false
    && preg_match('/Tk([\d,]+(?:\.\d{2})?) received from A\/C:\**\*(\d+).* TxnId:(\d+)/', $sms, $matches)) {
    $amount = str_replace(',', '', $matches[1]);
    $payment_number = $matches[2]; // Corrected: Extracting from the regex match
    $txn_id = $matches[3];
    $is_valid_sms = true;
}

// Handle non-payment SMS from allowed senders
if (!$is_valid_sms) {
    sendResponse(true, 'SMS received but not a valid payment notification', 200);
}

// Check for duplicate transaction
$status = "unused";
$txn_id_escaped = $conn->real_escape_string($txn_id);
$transection_checker = $conn->query("SELECT * FROM getway WHERE trxID = '$txn_id_escaped'");

if (!$transection_checker) {
    error_log("Database query error: " . $conn->error);
    sendResponse(false, 'Database query error', 500);
}

if ($transection_checker->num_rows > 0) {
    sendResponse(true, 'Transaction ID already exists', 200);
}

// Insert new transaction
$stmt = $conn->prepare("INSERT INTO getway (paymentMethod, paymentNumber, trxID, amount, date, status) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    error_log("Prepare statement error: " . $conn->error);
    sendResponse(false, 'Database prepare error', 500);
}

$stmt->bind_param("ssssss", $sender, $payment_number, $txn_id, $amount, $date_time, $status);

if ($stmt->execute()) {
    sendResponse(true, 'Payment SMS processed and stored successfully', 200);
} else {
    error_log("Database error: " . $stmt->error);
    sendResponse(false, 'Error storing payment SMS in database', 500);
}

$stmt->close();
$conn->close();
?>
