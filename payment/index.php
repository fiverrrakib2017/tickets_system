<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {

    $amount = $_POST['amount'];
    $transaction_id = uniqid("TXN_");
   
    $api_url = "https://payment.futureictbd.com/api";
    $api_key = "3168815956989cb4be67c615201410979846272776989cb4be67c81322820681"; // Get from PipraPay admin panel
    $payload = [
        "amount"         => $amount,
        "currency"       => "BDT",
        "transaction_id" => $transaction_id,
        "customer_name"  => "Test User",
        "customer_email" => "test@mail.com",
        "success_url"    => "https://103.146.16.154/payment/success.php",
        "fail_url"       => "https://103.146.16.154/payment/fail.php",
        "cancel_url"     => "https://103.146.16.154/payment/fail.php"
    ];


    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . "/create-payment");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $api_key,
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // Execute request
    $response = curl_exec($ch);
echo '<pre>';
print_r($response);
echo '</pre>';

    
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Payment Test</title>
</head>
<body>
    <h2>Test Payment</h2>

    <form method="POST">
        <label>Amount:</label>
        <input type="number" name="amount" required>
        <br><br>
        <button type="submit" name="pay_now">Pay Now</button>
    </form>

</body>
</html>
