<?php

require 'cred.php';

echo '
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FLG Website PayPal Information</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background: linear-gradient(135deg, #003d7a 0%, #1a1a1a 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

header {
    background: linear-gradient(135deg, #003d7a 0%, #1a4d8f 100%);
    color: white;
    padding: 40px 30px;
    text-align: center;
}

header h1 {
    font-size: 28px;
    margin-bottom: 8px;
    font-weight: 600;
    color: #00d4ff;
}

header p {
    font-size: 14px;
    opacity: 0.9;
}

.content {
    padding: 40px 30px;
}

.form-section {
    background: #f0f7ff;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 30px;
    border-left: 4px solid #003d7a;
}

.form-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 600px) {
    .form-group {
        grid-template-columns: 1fr;
    }
}

label {
    display: block;
    font-weight: 600;
    color: #003d7a;
    margin-bottom: 8px;
    font-size: 14px;
}

input[type="date"] {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #00d4ff;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
    font-family: inherit;
    background: white;
}

input[type="date"]:focus {
    outline: none;
    border-color: #003d7a;
    box-shadow: 0 0 0 3px rgba(0, 61, 122, 0.1);
}

.button-group {
    display: flex;
    gap: 12px;
}

input[type="submit"] {
    flex: 1;
    padding: 12px 24px;
    background: linear-gradient(135deg, #003d7a 0%, #1a4d8f 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

input[type="submit"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 61, 122, 0.4);
}

input[type="submit"]:active {
    transform: translateY(0);
}

.date-range {
    background: #e0f7ff;
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    border-left: 4px solid #00d4ff;
    font-size: 15px;
    color: #003d7a;
}

.note {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    font-size: 14px;
    color: #856404;
}

.transactions-section h2 {
    color: #003d7a;
    margin-bottom: 20px;
    font-size: 20px;
    font-weight: 600;
}

.transaction-list {
    list-style: none;
}

.transaction-item {
    padding: 15px;
    margin-bottom: 12px;
    border-radius: 6px;
    background: #f8f9fa;
    border-left: 4px solid #00d4ff;
    font-size: 14px;
    transition: all 0.2s;
}

.transaction-item:hover {
    background: #e0f7ff;
    border-left-color: #003d7a;
    transform: translateX(4px);
}

.transaction-item.credit {
    background: #f0fdf4;
    border-left-color: #22c55e;
}

.transaction-item.credit:hover {
    background: #e7fce4;
}

.transaction-item.debit {
    background: #fef2f2;
    border-left-color: #ef4444;
}

.transaction-item.debit:hover {
    background: #fee2e2;
}

.transaction-date {
    display: block;
    font-weight: 600;
    color: #003d7a;
    margin-bottom: 6px;
    font-size: 13px;
}

.transaction-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.transaction-info {
    flex: 1;
    min-width: 200px;
}

.transaction-amount {
    font-size: 16px;
    font-weight: 700;
}

.credit .transaction-amount {
    color: #22c55e;
}

.debit .transaction-amount {
    color: #ef4444;
}

.balance-section {
    background: linear-gradient(135deg, #003d7a 0%, #1a4d8f 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    margin-top: 30px;
}

.balance-label {
    font-size: 14px;
    font-weight: 600;
    opacity: 0.9;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.balance-amount {
    font-size: 42px;
    font-weight: 700;
    color: #00d4ff;
}

hr {
    border: none;
    height: 2px;
    background: #e0e0e0;
    margin: 30px 0;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state p {
    font-size: 16px;
}
</style>
<script>
// JavaScript function to validate date range
function validateDateRange() {
    var startDate = new Date(document.getElementById("start_date").value);
    var endDate = new Date(document.getElementById("end_date").value);
    var timeDiff = endDate - startDate;
    var daysDiff = timeDiff / (1000 * 3600 * 24);

    if (startDate > endDate) {
        alert("The start date must be less than or equal to the end date.");
        return false; // Prevent form submission
    }

    if (daysDiff > 31) {
        alert("The date range cannot be more than 31 days.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}

// JavaScript function to set default dates
function setDefaultDates() {
    var endDate = new Date(); // Current date
    var startDate = new Date();
    startDate.setDate(endDate.getDate() - 31); // 31 days before current date

    // Format dates as YYYY-MM-DD
    document.getElementById("start_date").value = startDate.toISOString().split(\'T\')[0];
    document.getElementById("end_date").value = endDate.toISOString().split(\'T\')[0];
}

// Set default dates on page load
window.onload = setDefaultDates;
</script>
</head>

<body>
<div class="container">
    <header>
        <h1>PayPal Transaction Report</h1>
        <p>Florida Garrison - Financial Overview</p>
    </header>
    <div class="content">';

// Function to get the access token
function getAccessToken($clientId, $clientSecret, $authUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $authUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    
    $result = curl_exec($ch);
    if(empty($result)) {
        die("Error: No response.");
    } else {
        $json = json_decode($result);
        return $json->access_token;
    }

    curl_close($ch);
}

// Function to get the transactions
function getTransactions($accessToken, $transactionsUrl, $startDate, $endDate) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $transactionsUrl . "?start_date=$startDate&end_date=$endDate&fields=all");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ));

    $result = curl_exec($ch);
    if(empty($result)) {
        die("Error: No response.");
    } else {
        $json = json_decode($result, true);
        return $json;
    }

    curl_close($ch);
}

// Step 1: Get the access token
$accessToken = getAccessToken($clientId, $clientSecret, $authUrl);

// Set default values if no date is selected
if (!isset($_GET['start_date'])) {
    $startDate = date("Y-m-d", strtotime("-31 days"));
} else {
    $startDate = $_GET['start_date'];
}

if (!isset($_GET['end_date'])) {
    $endDate = date("Y-m-d");
} else {
    $endDate = $_GET['end_date'];
}

// Convert selected dates to ISO 8601 format with UTC timezone for API
$startDateISO = $startDate . "T00:00:00Z";
$endDateISO = $endDate . "T23:59:59Z";

// Convert dates to a more readable format
$readableStartDate = date("l, F j, Y", strtotime($startDate)); // e.g., Monday, August 22, 2024
$readableEndDate = date("l, F j, Y", strtotime($endDate));     // e.g., Monday, September 28, 2024

// Display the form with date selection and validation
echo '
<div class="form-section">
    <form method="GET" action="" onsubmit="return validateDateRange()">
        <div class="form-group">
            <div>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
        </div>
        <div class="button-group">
            <input type="submit" value="Load Transactions">
        </div>
    </form>
</div>';

// Display the readable dates
echo "<div class=\"date-range\">
    <strong>Report Period:</strong> $readableStartDate to $readableEndDate
</div>";

// Display the API limitation note
echo '<div class="note">
    <strong>📋 Note:</strong> Due to PayPal API limitations, only 31 days of transactions can be displayed at a time.
</div>';

// Step 3: Get the transactions based on selected dates
$transactions = getTransactions($accessToken, $transactionsUrl, $startDateISO, $endDateISO);

$transactions = array_filter($transactions, 'is_array');

echo '<div class="transactions-section">
    <h2>Transactions</h2>
    <ul class="transaction-list">';

// Step 4: Loop through transactions and display
$total = 0; // Initialize total balance

foreach ($transactions as $transaction) {
    foreach ($transaction as $key => $object) {
        if(!empty($object['payer_info']['payer_name'])) {
            $transactionDate = date("l, F j, Y g:iA", strtotime($object['transaction_info']['transaction_initiation_date']));
            $amount = $object['transaction_info']['transaction_amount']['value'];
            
            if($amount < 0) {
                // Debit/Outgoing transaction
                $name = $object['payer_info']['payer_name']['alternate_full_name'];
                echo '<li class="transaction-item debit">
                    <span class="transaction-date">' . $transactionDate . '</span>
                    <div class="transaction-details">
                        <div class="transaction-info">
                            <strong>Payment to:</strong> ' . htmlspecialchars($name) . '
                        </div>
                        <div class="transaction-amount">-$' . abs($amount) . '</div>
                    </div>
                </li>';
            } else {
                // Credit/Incoming transaction
                $transactionId = $object['transaction_info']['transaction_id'];
                echo '<li class="transaction-item credit">
                    <span class="transaction-date">' . $transactionDate . '</span>
                    <div class="transaction-details">
                        <div class="transaction-info">
                            <strong>Transaction ID:</strong> ' . htmlspecialchars($transactionId) . '
                        </div>
                        <div class="transaction-amount">+$' . $amount . '</div>
                    </div>
                </li>';
            }
        }

        if(isset($object['transaction_info']['available_balance']['value'])) {
            $total = $object['transaction_info']['available_balance']['value'];
        }
    }
}

echo '</ul>
</div>';

echo '<div class="balance-section">
    <div class="balance-label">Total Account Balance</div>
    <div class="balance-amount">$' . number_format($total, 2) . '</div>
</div>';

echo '
    </div>
</div>
</body>
</html>';

?>