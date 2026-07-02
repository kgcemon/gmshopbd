<?php
include "connection.php";

define("SECURITY_KEY", "1234");
define("ALLOWED_SENDERS", ["bKash", "NAGAD", "Nagad", "16216"]);
define("BKASH_NUMBERS", ["bKash", "16247"]);
define("NAGAD_NUMBERS", ["NAGAD", "16167"]);
define("ROCKET_NUMBERS", ["Rocket", "16216"]);

// Function to process SMS
function processSMS($smsData, $conn) {
    $sender = $smsData['sender'];
    $sms = $smsData['sms'];
    $date_time = $smsData['date_time'] ?? date('Y-m-d H:i:s');

    // Log all incoming SMS
    error_log(sprintf("SMS Received - Sender: %s, Message: %s, Time: %s", $sender, $sms, $date_time));

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
        error_log("Not a valid payment notification");
        return;
    }

    // Check for duplicate transaction
    $status = "unused";
    $txn_id_escaped = $conn->real_escape_string($txn_id);
    $transection_checker = $conn->query("SELECT * FROM getway WHERE trxID = '$txn_id_escaped'");

    if (!$transection_checker) {
        error_log("Database query error: " . $conn->error);
        return;
    }

    if ($transection_checker->num_rows > 0) {
        error_log("Transaction ID already exists");
        return;
    }

    // Insert new transaction
    $stmt = $conn->prepare("INSERT INTO getway (paymentMethod, paymentNumber, trxID, amount, date, status) VALUES (?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        error_log("Prepare statement error: " . $conn->error);
        return;
    }

    $stmt->bind_param("ssssss", $sender, $payment_number, $txn_id, $amount, $date_time, $status);

    if ($stmt->execute()) {
        error_log("Payment SMS processed and stored successfully");
    } else {
        error_log("Database error: " . $stmt->error);
    }

    $stmt->close();
}

// Queue processing
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379); // Redis server address

    while (true) {
        $queueData = $redis->rPop('sms_queue');
        if ($queueData) {
            $smsData = json_decode($queueData, true);
            if ($smsData) {
                // Database connection
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                if ($conn->connect_error) {
                    error_log("Database connection failed: " . $conn->connect_error);
                    continue;
                }

                processSMS($smsData, $conn);
                $conn->close();
            } else {
                error_log("Failed to decode SMS data");
            }
        } else {
            // If queue is empty, sleep for a while
            sleep(1);
        }
    }

    $redis->close();
} catch (Exception $e) {
    error_log("Redis error: " . $e->getMessage());
}
?>