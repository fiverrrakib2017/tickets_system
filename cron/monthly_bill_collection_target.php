<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Dhaka");

    if(!isset($_SERVER['DOCUMENT_ROOT'])||$_SERVER['DOCUMENT_ROOT']==''){
        $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    }

    include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/app/repository/Target_repository.php';

    $now        = new DateTime('now'); 
    $month      = date('F');
    $year       = date('Y');

    if (!class_exists('Target_repository')) {
        echo "Target_repository Class not found!";
        exit; 
    } 
    
    $repository = new Target_repository($con);
    
    $_customer_due_amount = $repository->get_customer_due_amount();
    
    $_customer_monthly_bill_amount = $repository->get_customer_monthly_bill_amount();
    $total_target = $_customer_due_amount + $_customer_monthly_bill_amount;
    if (!$repository->target_exists($month, $year)) {
        if ($repository->save_target($month, $year, $total_target)) {
            echo "Target inserted successfully for $month $year. Total target = $total_target";
        } else {
            echo " Insert failed.";
        }
    } else {
        echo " Target for $month $year already exists!";
    }


?>
