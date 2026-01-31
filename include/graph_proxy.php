<?php
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';

$interface   = $_GET['interface'] ?? '';
$file        = $_GET['file'] ?? '';
$customer_id = $_GET['customer_id'] ?? '';

if (!$interface || !$file || !$customer_id) {
    http_response_code(400);
    exit('Invalid request');
}

/*-----------Get Customer Info----------*/
$stmt = $con->query("SELECT ping_ip, port FROM customers WHERE id = $customer_id");
$customer = $stmt->fetch_assoc();
if (!$customer) {
    http_response_code(404);
    exit('Customer not found');
}

/*-----------Allow only expected files----------*/
$allowed = ['daily.gif', 'weekly.gif', 'monthly.gif', 'yearly.gif'];
if (!in_array($file, $allowed)) {
    http_response_code(403);
    exit('Not allowed');
}

/*-----------BUILD URL ----------*/
$image_url = "http://{$customer['ping_ip']}:{$customer['port']}/graphs/iface/"
           . urlencode($interface) . "/$file";
/*-----------Fetch Image----------*/
$image = @file_get_contents($image_url);

if ($image === false) {
    http_response_code(404);
    exit("Graph not found: $image_url");
}

header('Content-Type: image/gif');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo $image;
exit;
