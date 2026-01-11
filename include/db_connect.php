<?php
date_default_timezone_set("Asia/Dhaka");
//SET GLOBAL time_zone = '+6:00';

/*Master Databese Connection*/
$con = new mysqli("localhost","root","","ticket_system");
$sql_details = array(
    'user' => 'root', 
    'pass' => '',
    'db'   => 'ticket_system',
    'host' => 'localhost'
);

/* Check connection*/
if (mysqli_connect_errno()) {
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
 exit();
}

/*Company Databese Connection*/


?>