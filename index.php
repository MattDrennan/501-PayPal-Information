<?php

require 'cred.php';

echo '
<html>
<head>
<title>FLG Website PayPal Information</title>
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

// Step 2: Define the start date 31 days ago in ISO 8601 format with UTC timezone
$startDate = date("Y-m-d\TH:i:s\Z", strtotime("-31 days")); // 31 days ago in UTC

// Step 2.1: Ensure correct timezone for current time
date_default_timezone_set('UTC'); // Set the desired timezone

// Step 2.2: Get the current date and time in the correct ISO 8601 format with timezone offset
$endDate = date("Y-m-d\TH:i:s") . "Z"; // Using 'Z' for UTC format

// Convert dates to a more readable format
$readableStartDate = date("l, F j, Y g:iA", strtotime($startDate)); // e.g., Monday, August 22, 2024 5:00PM
$readableEndDate = date("l, F j, Y g:iA", strtotime($endDate));     // e.g., Monday, September 28, 2024 5:00PM

// Display the readable dates
echo "Start Date: $readableStartDate<br /><br />";
echo "End Date: $readableEndDate";

// Step 3: Get the transactions
$transactions = getTransactions($accessToken, $transactionsUrl, $startDate, $endDate);

$transactions = array_filter($transactions, 'is_array');

echo '<p><i>Please note due to PayPal API limitations, only the last 31 days can be displayed at a time.</i></p>';

echo '<hr />';

echo '<ol>';

// Step 4: Loop through transactions and display
foreach ($transactions as $transaction) {
	//echo "<pre>";
	//print_r($transaction);
	//echo "</pre>";
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