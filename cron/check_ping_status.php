<?php 

/**----------Enable error reporting--------------**/ 
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

/*----GET Customer info------------*/

if($con->query("SELECT * FROM customers")){
    $customers = $con->query("SELECT * FROM customers")->fetch_all(MYSQLI_ASSOC);
    foreach ($customers as $customer) {
        $ping_ip = $customer['ping_ip'];
        $customer_id = $customer['id'];
        /*-------Ping and get stats--------*/ 
        if($ping_ip !== "" || $ping_ip !== null){
            $pingStats = customer_ping_status($ping_ip);
        }
        /*-----Prepare SQL to update ping stats-------*/ 
        $stmt = $con->prepare("UPDATE customers SET 
            ping_ip_status = ?,
            ping_sent = ?,
            ping_received = ?,
            ping_lost = ?,
            ping_min_ms = ?,
            ping_max_ms = ?,
            ping_avg_ms = ?
            WHERE id = ?");

        $stmt->bind_param(
            "sssssssi",
            $pingStats['status'],
            $pingStats['sent'],
            $pingStats['received'],
            $pingStats['lost'],
            $pingStats['min_ms'],
            $pingStats['max_ms'],
            $pingStats['avg_ms'],
            $customer_id
        );

        $stmt->execute();
        $stmt->close();
        
    }
}



?>




