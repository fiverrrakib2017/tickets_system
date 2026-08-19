<?php
session_start();
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';


if (!empty($_SESSION['uid'])) {

    $user_id = intval($_SESSION['uid']);

    $stmt = $con->prepare("
        UPDATE users 
        SET lastlogin = NOW() 
        WHERE id = ?
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo json_encode([
        'status' => true
    ]);

    exit;
}

echo json_encode([
    'status' => false
]);