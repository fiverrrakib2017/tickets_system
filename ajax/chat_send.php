<?php
session_start();
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';

$customer_id = $_SESSION['customer_id']; 
$message = trim($_POST['message']);

if($message != ''){
    $stmt = $conn->prepare(
        "INSERT INTO live_chats 
        (customer_id, sender, message) 
        VALUES (?, 'customer', ?)"
    );
    $stmt->bind_param("is", $customer_id, $message);
    $stmt->execute();
}
