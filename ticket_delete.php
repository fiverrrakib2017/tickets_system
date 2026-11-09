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
        header("location:tickets.php?error=invalid_id");
        exit();
    }

    /*----------- Fetch Ticket Subject/Details before delete for logging -----------*/
    $ticket_subject = "Unknown";
    $stmt_chk = $con->prepare("SELECT subject, customer_id FROM ticket WHERE id = ? LIMIT 1");
    $stmt_chk->bind_param("i", $id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    if ($res_chk->num_rows > 0) {
        $row_chk = $res_chk->fetch_assoc();
        $ticket_subject = $row_chk['subject'] ?? 'Unknown';
    }
    $stmt_chk->close();

    /*----------- Delete Ticket using Prepared Statement -----------*/
    $stmt = $con->prepare("DELETE FROM ticket WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted Ticket ID: $id (Subject: '$ticket_subject')";
        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        }

        header("location:tickets.php?success=deleted");
        exit();
    } else {
        header("location:tickets.php?error=delete_failed");
        exit();
    }
}

?>