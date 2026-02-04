<?php
session_start();
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';

$customer_id = $_SESSION['customer_id'];

$result = $conn->query(
    "SELECT * FROM live_chats 
     WHERE customer_id = $customer_id 
     ORDER BY id ASC"
);

while($row = $result->fetch_assoc()){
    if($row['sender'] == 'customer'){
        echo "<div style='text-align:right;margin-bottom:8px;'>
                <span class='badge bg-primary'>{$row['message']}</span>
              </div>";
    }else{
        echo "<div style='text-align:left;margin-bottom:8px;'>
                <span class='badge bg-secondary'>{$row['message']}</span>
              </div>";
    }
}
