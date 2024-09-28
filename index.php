<?php

require 'cred.php';

echo '
<html>
<head>
<title>FLG Website PayPal Information</title>
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

<body>';

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
<form method="GET" action="" onsubmit="return validateDateRange()">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" required>
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" required>
    <input type="submit" value="Load Transactions">
</form>
<br />';

// Display the readable dates
echo "Displaying transactions from <b>$readableStartDate</b> to <b>$readableEndDate</b>.<br /><br />";

// Step 3: Get the transactions based on selected dates
$transactions = getTransactions($accessToken, $transactionsUrl, $startDateISO, $endDateISO);

$transactions = array_filter($transactions, 'is_array');

echo '<p><i>Please note due to PayPal API limitations, only 31 days can be displayed at a time.</i></p>';

echo '<hr />';

echo '<ol>';

// Step 4: Loop through transactions and display
$total = 0; // Initialize total balance

foreach ($transactions as $transaction) {
    foreach ($transaction as $key => $object) {
        if(!empty($object['payer_info']['payer_name'])) {
            if($object['transaction_info']['transaction_amount']['value'] < 0) {
                echo '<li><span style="color: red;">' . date("l, F j, Y g:iA", strtotime($object['transaction_info']['transaction_initiation_date'])) . ': ' . $object['payer_info']['payer_name']['alternate_full_name'] . ' : $' . $object['transaction_info']['transaction_amount']['value'] . '</span></li>';
            } else {
                echo '<li><span style="color: green;">' . date("l, F j, Y g:iA", strtotime($object['transaction_info']['transaction_initiation_date'])) . ' : ' . $object['transaction_info']['transaction_id'] . ' : $' . $object['transaction_info']['transaction_amount']['value'] . '</span></li>';
            }
        }

        if(isset($object['transaction_info']['available_balance']['value'])) {
            $total = $object['transaction_info']['available_balance']['value'];
        }
    }
}

echo '</ol>';

echo '<p style="font-size: 24px;"><b>Total account balance: $' . $total . '.</b></p>';

echo '
</body>
</html>';

?>