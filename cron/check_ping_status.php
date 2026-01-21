<?php 

/**----------Enable error reporting--------------**/ 
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

/*------------- Check Customer IP Ping Status -------------*/

$result = $con->query("SELECT * FROM customers");

if ($result && $result->num_rows > 0) {

    $customers = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($customers as $customer) {

        $customer_id = (int)$customer['id'];
        $ping_ip     = trim($customer['ping_ip']);

        if (empty($ping_ip)) {
            continue;
        }

        $old_status = $customer['ping_ip_status'];

        $pingStats = customer_ping_status($ping_ip);

        $new_status = $pingStats['status'];

        $offline_since    = $customer['offline_since'];
        $offline_duration = (int)$customer['offline_duration'];

        /*---------- Status Change Logic ----------*/

        if ($new_status === 'offline') {

            if ($old_status !== 'offline') {
                $offline_since    = date('Y-m-d H:i:s');
                $offline_duration = 0;
            } else {
                if (!empty($offline_since)) {
                    $offline_duration = time() - strtotime($offline_since);
                }
            }

        } else {
            $offline_since    = null;
            $offline_duration = 0;
        }

        /*---------- Update Database ----------*/

        $stmt = $con->prepare("
            UPDATE customers SET 
                ping_ip_status   = ?,
                ping_sent        = ?,
                ping_received    = ?,
                ping_lost        = ?,
                ping_min_ms      = ?,
                ping_max_ms      = ?,
                ping_avg_ms      = ?,
                offline_since    = ?,
                offline_duration = ?
            WHERE id = ?
        ");

        if (!$stmt) {
            continue;
        }

        $stmt->bind_param(
            "ssssssssii",
            $new_status,
            $pingStats['sent'],
            $pingStats['received'],
            $pingStats['lost'],
            $pingStats['min_ms'],
            $pingStats['max_ms'],
            $pingStats['avg_ms'],
            $offline_since,
            $offline_duration,
            $customer_id
        );

        $stmt->execute();
        $stmt->close();
    }
}



/*----Check Pop Branch  ip ping status------------*/
if($con->query("SELECT * FROM pop_branch")){
    $branches = $con->query("SELECT * FROM pop_branch")->fetch_all(MYSQLI_ASSOC);

    foreach ($branches as $branch) {

        $branch_id = $branch['id'];
        $ping_ip   = $branch['router_ip'];
        $old_status = $branch['ping_ip_status'];

        if (empty($ping_ip)) {
            continue;
        }

        $pingStats = customer_ping_status($ping_ip);
        $new_status = $pingStats['status'];

        $offline_since = $branch['offline_since'];
        $offline_duration = $branch['offline_duration'];

        /*--------- Status Change Logic ---------*/

        if ($new_status === 'offline') {

            if ($old_status !== 'offline') {
                /*--------- First time offline-----*/
                $offline_since = date('Y-m-d H:i:s');
                $offline_duration = 0;
            } else {
                /*----Still offline → calculate duration-------*/ 
                $offline_duration = time() - strtotime($offline_since);
            }

        } else {
            /*----------Came back online → reset-------*/ 
            $offline_since = null;
            $offline_duration = 0;
        }

        /*--------- Update Database ---------*/

        $stmt = $con->prepare("
            UPDATE pop_branch SET 
                ping_ip_status = ?,
                ping_sent = ?,
                ping_received = ?,
                ping_lost = ?,
                ping_min_ms = ?,
                ping_max_ms = ?,
                ping_avg_ms = ?,
                offline_since = ?,
                offline_duration = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ssssssssii",
            $new_status,
            $pingStats['sent'],
            $pingStats['received'],
            $pingStats['lost'],
            $pingStats['min_ms'],
            $pingStats['max_ms'],
            $pingStats['avg_ms'],
            $offline_since,
            $offline_duration,
            $branch_id
        );

        $stmt->execute();
        $stmt->close();
    }

}


?>




