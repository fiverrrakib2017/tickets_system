<?php
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';

header('Content-Type: application/json');

$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$message     = trim($_POST['message'] ?? '');

if($customer_id <= 0 || $message === ''){
    echo json_encode(['status' => 'error']);
    exit;
}

$sender = 'admin';

$stmt = $con->prepare("
    INSERT INTO live_chats (customer_id, sender, message, created_at)
    VALUES (?, ?, ?, NOW())
");

$stmt->bind_param("iss", $customer_id, $sender, $message);

if($stmt->execute()){
    echo json_encode([
        'status'  => 'success',
        'message' => htmlspecialchars($message),
        'time'    => date('h:i A')
    ]);
}else{
    echo json_encode(['status' => 'error']);
}
