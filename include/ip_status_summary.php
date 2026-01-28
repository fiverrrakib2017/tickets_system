
<?php
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';

$total_bandwidth=$con->query("
    SELECT SUM(DISTINCT total) AS total_bandwidth 
    FROM customers
")->fetch_assoc()['total_bandwidth'];

$total_ip = $con->query("
    SELECT COUNT(DISTINCT ping_ip) AS total 
    FROM customers
")->fetch_assoc()['total'];

$up_ip = $con->query("
    SELECT COUNT(DISTINCT ping_ip) AS up_ip
    FROM customers
    WHERE ping_ip_status = 'online'
")->fetch_assoc()['up_ip'];

$down_ip = $con->query("
    SELECT COUNT(DISTINCT ping_ip) AS down_ip
    FROM customers
    WHERE ping_ip_status != 'online'
")->fetch_assoc()['down_ip'];

?>