<?php
date_default_timezone_set("Asia/Dhaka");

/* Master Database Connection */
$con = new mysqli("localhost", "admin", "src@54321", "ticket_system",3306);

/* Check connection */
if ($con->connect_error) {
    die("Database connection failed: " . $con->connect_error);
}

/* SQL details (for DataTables / other libraries) */
$sql_details = array(
    'user' => 'admin',
    'pass' => 'src@54321',
    'db'   => 'ticket_system',
    'host' => 'localhost',
);
?>
