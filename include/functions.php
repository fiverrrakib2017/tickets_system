<?php 



// function get_ticket_count($con, $value){
//     $ticket_count = 0;
//     $sql = "SELECT COUNT(*) as ticket_count FROM ticket WHERE ticket_type = ?";
//     $stmt = $con->prepare($sql);
//     $stmt->bind_param('s', $value);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($result && $row = $result->fetch_assoc()) {
//         $ticket_count = $row['ticket_count'] ?? 0;
//     }
//     $stmt->close();
//     return $ticket_count;
// }

/**
 * Check if a value exists in a specific column of a database table
 *
 * @param mysqli $con The database connection
 * @param string $table The name of the table
 * @param string $column The column to check
 * @param string $value The value to check for uniqueness
 * @return bool True if the value exists, false otherwise
 */
// function isUniqueColumn($con, $table, $column, $value, $exclude=NULL)
// {
//     $condition=""; 
//     $types = "s";
//     if(isset($exclude) && !empty($exclude)){
//         $condition ='AND id != ?'; 
//         $types .= "i"; 
//     }
    

//     $query = "SELECT COUNT(*) as count FROM $table WHERE $column = ? $condition ";
//     $stmt = $con->prepare($query);
//     if ($stmt) {
//         if (!empty($exclude)) {
//             $stmt->bind_param($types, $value, $exclude); 
//         } else {
//             $stmt->bind_param("s", $value); 
//         }
        
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $row = $result->fetch_assoc();
//         return $row['count'] > 0;
//     }
//     return false;
//     exit; 
// }


// function send_message ($phone, $message) {
//     $errors = [];
//     /* Return errors if validation fails */
//     if (!empty($errors)) {
//         echo json_encode([
//             'success' => false,
//             'errors' => $errors
//         ]);
//         exit;
//     }

//     /* SMS API details */
//     $url = "http://bulksmsbd.net/api/smsapi";
//     $api_key = "WC1N6AFA4gVRZLtyf8z9";
//     $senderid = "SR WiFi";
    
//     /* Prepare data */
//     $data = [
//         "api_key" => $api_key,
//         "senderid" => $senderid,
//         "number" => $phone,
//         "message" => $message
//     ];

//     /* Initialize cURL */
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_POST, 1);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//     /* Execute request */
//     $response = curl_exec($ch);
//     curl_close($ch);

//     $responseData = json_decode($response, true);

//     if ($responseData['response_code'] == 202) {
//         return   json_encode([
//             'success' => true,
//             'message' => $responseData['success_message']
//         ]);
//         /*Insert SMS Logs*/
//         sms_logs($phone,$message,'1');
//     } else {
//         return json_encode([
//             'success' => false,
//             'error' => $responseData['error_message'] ?: 'An error occurred.'
//         ]);
//         /*Insert SMS Logs*/
//         sms_logs($phone,$message,'0');
//     }
//     exit;

// }


/* SMS Logs Function */
// function sms_logs($mobile_number, $message, $status, $created_at = '', $updated_at = '') {
//     include 'db_connect.php';
//     if (empty($created_at)) {
//         $created_at = date('Y-m-d H:i:s');
//     }
//     if (empty($updated_at)) {
//         $updated_at = date('Y-m-d H:i:s');
//     }

//     /* Get Customer Data */
//     $mobile_number = $con->real_escape_string($mobile_number);
//     $customers = $con->query("SELECT * FROM `customers` WHERE `mobile` LIKE '%" . $mobile_number . "%' LIMIT 1")->fetch_array();

//     if ($customers) {
//         /* Insert Data into sms_logs */
//         $stmt = $con->prepare("INSERT INTO sms_logs (pop_id, area_id, customer_id, phone_number, message, sent_at, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
//         $stmt->bind_param(
//             "iiissssss",
//             $customers['pop'],
//             $customers['area'],
//             $customers['id'],
//             $mobile_number,
//             $message,
//             $created_at,
//             $status,
//             $created_at,
//             $updated_at
//         );
//         $stmt->execute();
//         $stmt->close();
//     }
// }
if(!function_exists('time_ago')){
    function time_ago($startdate)
{
    /*Convert startdate to a timestamp*/
    $startTimestamp = strtotime($startdate);
    $currentTimestamp = time();

    /* Calculate the difference in seconds*/
    $difference = $currentTimestamp - $startTimestamp;

    /*Define time intervals*/
    $units = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'min' => 60,
        'second' => 1,
    ];

    /*Check for each time unit*/
    foreach ($units as $unit => $value) {
        if ($difference >= $value) {
            $time = floor($difference / $value);
            return '' . ' ' . $time . ' ' . $unit . ($time > 1 ? 's' : '') . '';
        }
    }
    /*If the difference is less than a second*/
    return ' just now';
    exit; 
}
}
if(!function_exists('acctual_work')) {
    
    function acctual_work($startdate, $enddate)
    {
        $startTimestamp = strtotime($startdate);
        $endTimestamp = strtotime($enddate);
        $time_difference = $endTimestamp - $startTimestamp;

        /*--------Define time periods in seconds-----*/ 
        $units = [
            'year' => 365 * 24 * 60 * 60,
            'month' => 30 * 24 * 60 * 60,
            'week' => 7 * 24 * 60 * 60,
            'day' => 24 * 60 * 60,
            'hour' => 60 * 60,
            'minute' => 60,
            'second' => 1,
        ];

        /*-------Determine the appropriate time period---*/ 
        foreach ($units as $unit => $value) {
            if ($time_difference >= $value) {
                $count = floor($time_difference / $value);
                return $count . ' ' . $unit . ($count > 1 ? 's' : '') . ' ';
            }
        }

        return 'just now';
    }
}
if(!function_exists('ticket_priority')) {
    function ticket_priority($priority){
        switch ($priority) {
            case 1:
                return 'Low';
            case 2:
                return 'Normal';
            case 3:
                return 'Standard';
            case 4:
                return 'Medium';
            case 5:
                return 'High';
            case 6:
                return 'Very High';
            default:
                return 'Unknown';
        }
    }
}
/**
 * Get services of a customer 
 *
 * @param mysqli $con
 * @param int $customer_id
 * @param bool $withLink
 * @return string
 */

if(!function_exists('get_customer_services')) {
    function get_customer_services($customer_id){
        include 'db_connect.php';
        $service_sql = "SELECT 
                        cs.id AS service_id,
                        cs.name,
                        ci.customer_limit 
                    FROM customer_invoice ci
                    JOIN customer_service cs 
                        ON ci.service_id = cs.id
                    WHERE ci.customer_id = '{$customer_id}'";

    $service_result = $con->query($service_sql);

    if ($service_result->num_rows > 0) {
        $serviceLinks = [];

        while ($row = $service_result->fetch_assoc()) {

            $serviceName  = htmlspecialchars($row['name']);
            $serviceLimit = (int)$row['customer_limit'];
            $serviceId    = (int)$row['service_id'];

           $serviceLinks[] = '
        <a href="customers.php?service_id='.$serviceId.'"
        class="text-decoration-none text-primary fw-semibold d-flex justify-content-between align-items-center w-100">

            <span class="text-truncate">
                '.$serviceName.'
            </span>

            <span class="text-muted fw-normal fs-12 ms-3" style="white-space: nowrap;">
                '.$serviceLimit.' Mbps
            </span>

        </a>
        ';




        }
        return implode('<br>', $serviceLinks);

    } else {
        return 'N/A';
    }
    }
}

/*-----------Save Bandwidth Service-------------*/
if(!function_exists('save_bandwidth_service')){
    function save_bandwidth_service($con, $customer_id,$service_ids,$limits){
       
        if(count($service_ids) != count($limits)){
            return false;
        }

        $con->query("DELETE FROM mac_reseller_customer_inv WHERE customer_id='$customer_id'");
        $con->query("DELETE FROM customer_invoice WHERE customer_id='$customer_id'");

        foreach($service_ids as $index => $service_id){
            $limit = (int)($limits[$index] ?? 0);
            $con->query(" INSERT INTO customer_invoice(customer_id, service_id, customer_limit)
                VALUES('$customer_id','$service_id','$limit') ");
        }
        return true;
    }
}

if(!function_exists('save_mac_reseller_service')){
    function save_mac_reseller_service($con, $customer_id, $packages, $counts){
        if(count($packages) != count($counts)){
            return false;
        }

        $con->query("DELETE FROM mac_reseller_customer_inv WHERE customer_id='$customer_id'");
        $con->query("DELETE FROM customer_invoice WHERE customer_id='$customer_id'");

        foreach($packages as $i => $package){
            $count = (int)($counts[$i] ?? 0);
            $con->query(" INSERT INTO mac_reseller_customer_inv(customer_id, package_count, total_customer)
                VALUES('$customer_id','$package','$count')");
        }
        return true;
    }
}

/*----------Save Customer Phone Number-------------*/
if(!function_exists('save_customer_phones')){
    function save_customer_phones($con, $customer_id, $phone_numbers){
        $con->query("DELETE FROM customer_phones WHERE customer_id='$customer_id'");

        foreach($phone_numbers as $phone_number){
            $phone_number = $con->real_escape_string(trim($phone_number));
            if(!empty($phone_number)){
                $con->query(" INSERT INTO customer_phones(customer_id, phone_number)
                    VALUES('$customer_id','$phone_number') ");
            }
        }
        return true;
    }
}
/*----------Save Customer Public IP-------------*/
if(!function_exists('save_customer_public_ip')){
    function save_customer_public_ip($con, $customer_id, $public_ips){
        $con->query("DELETE FROM customer_public_ip_address WHERE customer_id='$customer_id'");

        foreach($public_ips as $public_ip){
            $public_ip = $con->real_escape_string(trim($public_ip));
            if(!empty($public_ip)){
                $con->query(" INSERT INTO customer_public_ip_address(customer_id, ip_address)
                    VALUES('$customer_id','$public_ip') ");
            }
        }
        return true;
    }
}
/*--------------Rollback Customer Service-------------*/
if(!function_exists('rollback_customer')){
    function rollback_customer($con,$customer_id,$message='Operation failed'){
        /*-----------------delete related data first------------------*/ 
        $con->query("DELETE FROM customer_invoice WHERE customer_id='$customer_id'");
        $con->query("DELETE FROM mac_reseller_customer_inv WHERE customer_id='$customer_id'");
        $con->query("DELETE FROM customer_phones WHERE customer_id='$customer_id'");
        $con->query("DELETE FROM customer_public_ip_address WHERE customer_id='$customer_id'");

        // delete main customer
        $con->query("DELETE FROM customers WHERE id='$customer_id'");

        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        exit;
    }
}

if(!function_exists('get_customer_mac_reseller_services')) {
    function get_customer_mac_reseller_services($customer_id){
        include 'db_connect.php';

    $_result = $con->query("SELECT * FROM mac_reseller_customer_inv WHERE customer_id = '{$customer_id}'");

    if ($_result->num_rows > 0) {
        $customer_links = [];

        while ($row = $_result->fetch_assoc()) {

            $customer_id  = htmlspecialchars($row['customer_id']);
            $package_count = (int)$row['package_count'];
            $total_customer    = (int)$row['total_customer'];

           $customer_links[] = '
        <a href="customers.php"
        class="text-decoration-none text-primary fw-semibold d-flex justify-content-between align-items-center w-100">

            <span class="text-truncate">
                '.$package_count.' MBPS
            </span>

            <span class="text-muted fw-normal fs-12 ms-3" style="white-space: nowrap;">
                '.$total_customer.' Customers
            </span>

        </a>
        ';




        }
        return implode('<br>', $customer_links);

    } else {
        return 'N/A';
    }
    }
}


/*----------------Get Tickets-----------------------*/
if (!function_exists('get_tickets')) {
    function get_tickets(mysqli $con, array $options = []) {

        $where = [];
        $limit = '';
        $order = 'ORDER BY t.id DESC';

       /*------ Default: Non-Management Tickets Only ------*/
        $where[] = "(t.is_management_ticket IS NULL OR t.is_management_ticket = 0)";

        /*---- Customer Filter ----*/
        if (!empty($options['customer_id'])) {
            $where[] = "t.customer_id = " . (int)$options['customer_id'];
        }
        /*---- User Filter ----*/
        if (!empty($options['user_id'])) {
            $where[] = "t.assign_user_id = " . (int)$options['user_id'];
        }

        /*---- Status Filter ----*/
        if (!empty($options['status'])) {
            switch ($options['status']) {
                case 'open':
                    $where[] = "t.ticket_type = 'Active'";
                    break;

                case 'pending':
                    $where[] = "t.ticket_type = 'Pending'";
                    break;

                case 'resolved':
                    $where[] = "t.ticket_type = 'Complete'";
                    break;
            }
        }

        /*---- Date Filter ----*/
        if (!empty($options['ticket_date'])) {

            $date = date('Y-m-d', strtotime($options['ticket_date']));
        
            $start = $date . ' 00:00:00';
            $end   = date('Y-m-d', strtotime($date . ' +1 day')) . ' 00:00:00';
        
            $where[] = "t.create_date >= '{$start}' AND t.create_date < '{$end}'";
        }
        /*---- Date Range Filter (Start Date & End Date) ----*/
        if (!empty($options['date_from']) && !empty($options['date_to'])) {
            $date_from = date('Y-m-d', strtotime($options['date_from'])) . ' 00:00:00';
            $date_to   = date('Y-m-d', strtotime($options['date_to'])) . ' 23:59:59';
            
            $where[] = "t.create_date >= '{$date_from}' AND t.create_date <= '{$date_to}'";
        } elseif (!empty($options['date_from'])) {
            /*--------if only start date is set---------*/ 
            $date_from = date('Y-m-d', strtotime($options['date_from'])) . ' 00:00:00';
            $where[] = "t.create_date >= '{$date_from}'";
        } elseif (!empty($options['date_to'])) {
            /*--------if only end date is set---------*/ 
            $date_to = date('Y-m-d', strtotime($options['date_to'])) . ' 23:59:59';
            $where[] = "t.create_date <= '{$date_to}'";
        }
        /*---- Performance Filter ----*/
        if (!empty($options['performance'])) {

            switch ($options['performance']) {

                case 'monthly':
                    $where[] = "MONTH(t.create_date) = MONTH(CURDATE()) 
                                AND YEAR(t.create_date) = YEAR(CURDATE())";
                    break;

                case 'weekly':
                    $where[] = "YEARWEEK(t.create_date, 1) = YEARWEEK(CURDATE(), 1)";
                    break;

                case 'daily':
                    $where[] = "DATE(t.create_date) = CURDATE()";
                    break;

                case 'yearly':
                    $where[] = "YEAR(t.create_date) = YEAR(CURDATE())";
                    break;
            }
        }
        
        /*---------Assigned To---------*/
        if (!empty($options['assign_user_id'])) {
            $where[] = "t.assign_user_id = " . (int)$options['assign_user_id'];
        }

        /*---- Today Filter ----*/
        if (!empty($options['today'])) {
            $where[] = "DATE(t.create_date) = CURDATE()";
        }

        /*---- Limit ----*/
        if (!empty($options['limit'])) {
            $limit = "LIMIT " . (int)$options['limit'];
        }

        $where_sql = !empty($where)
            ? 'WHERE ' . implode(' AND ', $where)
            : '';

        $sql = "
            SELECT 
                t.*,
                c.customer_name,
                pb.name AS pop_name,
                tu.fullname AS assigned_name,
                tt.topic_name AS issue_name,
                GROUP_CONCAT(DISTINCT cp.phone_number SEPARATOR '<br>') AS phones
            FROM ticket t
            LEFT JOIN customers c ON t.customer_id = c.id
            LEFT JOIN pop_branch pb ON t.pop_id = pb.id
            LEFT JOIN users tu ON t.assign_user_id = tu.id
            LEFT JOIN ticket_topic tt ON t.complain_type = tt.id
            LEFT JOIN customer_phones cp ON c.id = cp.customer_id
            $where_sql
            GROUP BY t.id
            $order
            $limit
        ";

        return $con->query($sql);
    }
}

/*---------------- Get Management Tickets -----------------------*/
if (!function_exists('get_management_tickets')) {

    function get_management_tickets(mysqli $con, array $options = [])
    {
        $where = [];
        $params = [];
        $types = '';

        $order = 'ORDER BY t.id DESC';
        $limit = '';

        /*------ Default: Management Tickets Only ------*/
        $where[] = "t.is_management_ticket = 1";

        /*------ User Filter ------*/
        if (isset($options['user_id']) && $options['user_id'] !== '') {
            $where[] = "t.assign_user_id = ?";
            $params[] = (int) $options['user_id'];
            $types .= 'i';
        }

        /*------ Status Filter ------*/
        if (!empty($options['status'])) {

            switch (strtolower(trim($options['status']))) {

                case 'open':
                    $where[] = "t.ticket_type = ?";
                    $params[] = 'Active';
                    $types .= 's';
                    break;

                case 'pending':
                    $where[] = "t.ticket_type = ?";
                    $params[] = 'Pending';
                    $types .= 's';
                    break;

                case 'resolved':
                    $where[] = "t.ticket_type = ?";
                    $params[] = 'Complete';
                    $types .= 's';
                    break;
            }
        }

        /*------ Date Filter ------*/
        $ticketDate = $options['ticket_date'] ?? '';

        if ($ticketDate !== '') {

            $date = DateTime::createFromFormat('!Y-m-d', $ticketDate);

            if ($date && $date->format('Y-m-d') === $ticketDate) {

                $start = $date->format('Y-m-d 00:00:00');
                $end = (clone $date)
                    ->modify('+1 day')
                    ->format('Y-m-d 00:00:00');

                $where[] = "t.create_date >= ? AND t.create_date < ?";
                $params[] = $start;
                $params[] = $end;
                $types .= 'ss';
            }
        }

        /*------ Date Range Filter ------*/
        $dateFrom = $options['date_from'] ?? '';
        $dateTo   = $options['date_to'] ?? '';

        /*
         * Date Range is applied only when ticket_date
         * is not supplied.
         */
        if ($ticketDate === '') {

            $fromDate = null;
            $toDate = null;

            if ($dateFrom !== '') {
                $parsedFrom = DateTime::createFromFormat('!Y-m-d', $dateFrom);

                if ($parsedFrom &&
                    $parsedFrom->format('Y-m-d') === $dateFrom) {
                    $fromDate = $parsedFrom;
                }
            }

            if ($dateTo !== '') {
                $parsedTo = DateTime::createFromFormat('!Y-m-d', $dateTo);

                if ($parsedTo &&
                    $parsedTo->format('Y-m-d') === $dateTo) {
                    $toDate = $parsedTo;
                }
            }

            if ($fromDate !== null && $toDate !== null) {

                if ($fromDate <= $toDate) {

                    $start = $fromDate->format('Y-m-d 00:00:00');
                    $end = (clone $toDate)
                        ->modify('+1 day')
                        ->format('Y-m-d 00:00:00');

                    $where[] = "t.create_date >= ? AND t.create_date < ?";
                    $params[] = $start;
                    $params[] = $end;
                    $types .= 'ss';
                }

            } elseif ($fromDate !== null) {

                $start = $fromDate->format('Y-m-d 00:00:00');

                $where[] = "t.create_date >= ?";
                $params[] = $start;
                $types .= 's';

            } elseif ($toDate !== null) {

                $end = (clone $toDate)
                    ->modify('+1 day')
                    ->format('Y-m-d 00:00:00');

                $where[] = "t.create_date < ?";
                $params[] = $end;
                $types .= 's';
            }
        }

        /*------ Performance Filter ------*/
        if (!empty($options['performance'])) {

            switch (strtolower(trim($options['performance']))) {

                case 'daily':

                    $where[] = "t.create_date >= CURDATE()
                                AND t.create_date < CURDATE() + INTERVAL 1 DAY";
                    break;

                case 'weekly':

                    $where[] = "t.create_date >= DATE_SUB(
                                    CURDATE(),
                                    INTERVAL WEEKDAY(CURDATE()) DAY
                                )
                                AND t.create_date < DATE_ADD(
                                    DATE_SUB(
                                        CURDATE(),
                                        INTERVAL WEEKDAY(CURDATE()) DAY
                                    ),
                                    INTERVAL 7 DAY
                                )";
                    break;

                case 'monthly':

                    $where[] = "t.create_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                AND t.create_date < DATE_FORMAT(
                                    CURDATE() + INTERVAL 1 MONTH,
                                    '%Y-%m-01'
                                )";
                    break;

                case 'yearly':

                    $where[] = "t.create_date >= MAKEDATE(YEAR(CURDATE()), 1)
                                AND t.create_date < MAKEDATE(YEAR(CURDATE()) + 1, 1)";
                    break;
            }
        }

        /*------ Assigned User Filter ------*/
        if (
            isset($options['assign_user_id']) &&
            $options['assign_user_id'] !== ''
        ) {
            $where[] = "t.assign_user_id = ?";
            $params[] = (int) $options['assign_user_id'];
            $types .= 'i';
        }

        /*------ Today Filter ------*/
        if (!empty($options['today'])) {

            $where[] = "t.create_date >= CURDATE()
                        AND t.create_date < CURDATE() + INTERVAL 1 DAY";
        }

        /*------ WHERE Clause ------*/
        $whereSql = 'WHERE ' . implode(' AND ', $where);

        /*------ Limit ------*/
        if (
            isset($options['limit']) &&
            filter_var($options['limit'], FILTER_VALIDATE_INT) !== false &&
            (int) $options['limit'] > 0
        ) {
            $limit = 'LIMIT ' . (int) $options['limit'];
        }

        /*------ SQL Query ------*/
        $sql = "
            SELECT
                t.*,
                tu.fullname AS assigned_name,
                tt.topic_name AS issue_name
            FROM ticket AS t
            LEFT JOIN users AS tu
                ON t.assign_user_id = tu.id
            LEFT JOIN ticket_topic AS tt
                ON t.complain_type = tt.id
            $whereSql
            $order
            $limit
        ";

        /*------ Prepare Statement ------*/
        $stmt = $con->prepare($sql);

        if (!$stmt) {
            error_log(
                'get_management_tickets prepare error: ' . $con->error
            );
            return false;
        }

        /*------ Bind Parameters ------*/
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        /*------ Execute Query ------*/
        if (!$stmt->execute()) {
            error_log(
                'get_management_tickets execute error: ' . $stmt->error
            );
            $stmt->close();
            return false;
        }

        /*------ Return Result ------*/
        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return false;
        }

        /*
         * Keep the statement alive while the result is being used.
         * Store the statement reference on the result object.
         */
        $result->management_ticket_stmt = $stmt;

        return $result;
    }
}


/*-----------Function to ping IP and get statistics-----------*/ 
if(!function_exists('customer_ping_status')){
    function customer_ping_status($ip, $count = 4, $timeout = 1) {
        $os = strtoupper(substr(PHP_OS, 0, 3));
        $stats = [
            'status' => 'offline',
            'sent' => 0,
            'received' => 0,
            'lost' => 0,
            'min_ms' => 0,
            'max_ms' => 0,
            'avg_ms' => 0,
        ];

        if ($os === 'WIN') {
            $cmd = "ping -n $count -w " . ($timeout*1000) . " " . escapeshellarg($ip);
        } else {
            $cmd = "ping -c $count -W $timeout " . escapeshellarg($ip);
        }

        exec($cmd, $output, $result);

        if ($result === 0 || $result === 1) {
            $stats['status'] = ($result === 0) ? 'online' : 'offline';

            $stats['sent'] = $count;

            $outputStr = implode("\n", $output);

            // Received / Lost
            if (preg_match('/(\d+) received/i', $outputStr, $m)) {
                $stats['received'] = (int)$m[1];
            } elseif (preg_match('/Lost = (\d+)/i', $outputStr, $m)) { 
                $stats['lost'] = (int)$m[1];
                $stats['received'] = $count - $stats['lost'];
            }

            $stats['lost'] = $count - $stats['received'];

            if (preg_match('/min\/avg\/max\/mdev = ([\d\.]+)\/([\d\.]+)\/([\d\.]+)/i', $outputStr, $m)) { 
                $stats['min_ms'] = round((float)$m[1]);
                $stats['avg_ms'] = round((float)$m[2]);
                $stats['max_ms'] = round((float)$m[3]);
            } elseif (preg_match('/Minimum = (\d+)ms, Maximum = (\d+)ms, Average = (\d+)ms/i', $outputStr, $m)) { 
                $stats['min_ms'] = (int)$m[1];
                $stats['max_ms'] = (int)$m[2];
                $stats['avg_ms'] = (int)$m[3];
            }
        }

        return $stats;
    }

}

/*------------Unque column-----------------*/
if(!function_exists('is_unique_column')){
    function is_unique_column($con, $table, $column, $value, $exclude=NULL)
    {
        $condition=""; 
        $types = "s";
        if(isset($exclude) && !empty($exclude)){
            $condition ='AND id != ?'; 
            $types .= "i"; 
        }
        
    
        $query = "SELECT COUNT(*) as count FROM $table WHERE $column = ? $condition ";
        $stmt = $con->prepare($query);
        if ($stmt) {
            if (!empty($exclude)) {
                $stmt->bind_param($types, $value, $exclude); 
            } else {
                $stmt->bind_param("s", $value); 
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }
        return false;
        exit; 
    }
}
/*--------Get Role Name Func-------------*/
if(!function_exists('get_role_name')) {
    function get_role_name($con, int $role_id) {
        $role_id = (int) $role_id;
        if ($role_id <= 0) {
            return '';
        }

        $stmt = $con->prepare("SELECT `name` FROM `roles` WHERE id = ? LIMIT 1");

        if (!$stmt) {
            return '';
        }

        $stmt->bind_param("i", $role_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $role = $result->fetch_assoc();

        $stmt->close();

        return trim($role['name'] ?? '');
    }
}

if(!function_exists('get_department_name')) {
    function get_department_name($con, int $department_id) {
        $department_id = (int) $department_id;
        if ($department_id <= 0) {
            return '';
        }

        $stmt = $con->prepare("SELECT `department_name` FROM `department` WHERE id = ? LIMIT 1");

        if (!$stmt) {
            return '';
        }

        $stmt->bind_param("i", $department_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $role = $result->fetch_assoc();

        $stmt->close();

        return trim($role['department_name'] ?? '');
    }
}

?>
