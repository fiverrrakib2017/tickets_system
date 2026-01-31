<?php
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$interface = $_GET['interface'] ?? '';
$period    = $_GET['period'] ?? 'day'; 
$customer_id = $_GET['customer_id'] ?? '';
if (!$interface) exit('Invalid');

/*-----------Get Customer Info----------*/
$stmt = $con->query("SELECT ping_ip, port FROM customers WHERE id = $customer_id");
$customer = $stmt->fetch_assoc();
if (!$customer) {
    http_response_code(404);
    exit('Customer not found');
}

/*-----------BUILD URL ----------*/
$url = "http://{$customer['ping_ip']}:{$customer['port']}/graphs/iface/"
           . urlencode($interface);
$html = @file_get_contents($url);
if (!$html) exit('No data');

preg_match_all('/<p>(.*?)<\/p>/si', $html, $matches);

$map = [
    'day'   => 0,
    'week'  => 1,
    'month' => 2,
    'year'  => 3,
];

$index = $map[$period] ?? 0;

if (!isset($matches[1][$index])) {
    exit('No info');
}

echo '<div>';
echo strip_tags($matches[1][$index], '<b><br>');
echo '</div>';
