<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';


 $customer_id = (int)$_GET['customer_id'];



$customer = $con->query(" SELECT ping_ip FROM customers WHERE id = $customer_id")->fetch_assoc();

$router_ip = $customer['ping_ip'];
$community = "starcomm";

/*------------ Interface Name OID -----------*/
$oid = '1.3.6.1.2.1.2.2.1.2';
/*---------- SNMP Walk ----------*/
$snmp = @snmpwalk($router_ip, $community, $oid);

$interfaces = [];
if ($snmp !== false) {
    foreach ($snmp as $line) {
        preg_match('/"([^"]+)"/', $line, $match);
        if (!empty($match[1])) {
            $interfaces[] = [
                'name' => $match[1]
            ];
        }
    }
}
echo json_encode($interfaces);
