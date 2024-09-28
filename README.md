# 501-PayPal-Information
 Used to disclose PayPal transactions to the club.

# How To Setup

1. Create 'cred.php' file.

2. Put this in the file:

```
<?php

// Replace with your actual PayPal API credentials
$clientId = 'CLIENT_ID_HERE';
$clientSecret = 'CLIENT_SECRET_HERE';

// PayPal API URLs
$authUrl = "https://api-m.paypal.com/v1/oauth2/token"; // For sandbox use https://api-m.sandbox.paypal.com/v1/oauth2/token
$transactionsUrl = "https://api-m.paypal.com/v1/reporting/transactions"; // For sandbox use https://api-m.sandbox.paypal.com/v1/reporting/transactions

?>
```