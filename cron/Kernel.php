<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Dhaka");

/**-----------Include File-------------**/
include 'scheduler.php';



/**-----------Define Task-------------**/
$scheduler = new Scheduler();

/*----------1 Minute-----------*/
$scheduler->everyMinute(__DIR__ . '/check_ping_status.php');

/*----------Every Day 10AM-----------*/
// $scheduler->dailyAt('10:00', __DIR__ . '/send_auto_message.php');

/*----------Every Month 12AM-----------*/
//$scheduler->monthlyAt('00:00', __DIR__ . '/monthly_bill_collection_target.php');

/*----------task execute -----------*/
$scheduler->run();

?>