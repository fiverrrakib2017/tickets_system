<?php 

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id <= 0) {
        header("location:users.php?error=invalid_id");
        exit();
    }

    /*----------- Fetch User Details before delete for logging -----------*/
    $username = "Unknown";
    $stmt_chk = $con->prepare("SELECT username FROM users WHERE id = ? LIMIT 1");
    if ($stmt_chk) {
        $stmt_chk->bind_param("i", $id);
        $stmt_chk->execute();
        $res_chk = $stmt_chk->get_result();
        if ($res_chk->num_rows > 0) {
            $row_chk = $res_chk->fetch_assoc();
            $username = $row_chk['username'] ?? 'Unknown';
        }
        $stmt_chk->close();
    }

    /*----------- Delete User using Prepared Statement -----------*/
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            /*----------- Log User Activity -----------*/
            $log_desc = "Deleted User ID: $id (Username: '$username')";
            if (function_exists('system_log')) {
                system_log($con, 'DELETE', $log_desc);
            } elseif (function_exists('logUserActivity')) {
                logUserActivity($con, 'DELETE', $log_desc);
            }

            header("location:users.php?success=deleted");
            exit();
        }
    }

    header("location:users.php?error=delete_failed");
    exit();
}

?>