<?php
date_default_timezone_set("Asia/Dhaka");

/* Master Database Connection */
$con = new mysqli("localhost", "root", "", "ticket_system",3307);

/* Check connection */
if ($con->connect_error) {
    die("Database connection failed: " . $con->connect_error);
}

/* SQL details (for DataTables / other libraries) */
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'ticket_system',
    'host' => 'localhost',
);
?>
