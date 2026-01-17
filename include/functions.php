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
function isUniqueColumn($con, $table, $column, $value, $exclude=NULL)
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
                class="text-decoration-none text-primary fw-semibold">
                    '.$serviceName.'
                    <span class="badge bg-light text-dark fw-normal ms-2">
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

?>
