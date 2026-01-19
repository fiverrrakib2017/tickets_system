<?php
/**----------Enable error reporting--------------**/ 
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/lib/ZktecoApi.php';

date_default_timezone_set('Asia/Dhaka');

/*---Today Date*/
$today = date('Y-m-d');



$zkteco = new ZktecoApi($con);
$transactions = $zkteco->get_today_transactions();
if (!$transactions) {
    echo "Failed to fetch transactions or no transactions found.\n";
    exit;
}

foreach ($transactions as $trx) {
    $emp_code = $trx['emp_code'];
    $punch_time = date('Y-m-d H:i:s', strtotime($trx['punch_time']));
    /*-------Get Employee Code From DB-------*/  
    $empQuery = $con->prepare("SELECT id FROM employees WHERE employee_code = ?");
    $empQuery->bind_param("s", $emp_code);
    $empQuery->execute();
    $empResult = $empQuery->get_result();

    if ($empResult->num_rows == 0) {
        continue;
    }

    $employee = $empResult->fetch_assoc();
    $employee_id = $employee['id'];

    /*-------Check Attendance Already Marked or Not-------*/
    $attQuery = $con->prepare("SELECT * FROM attendances WHERE employee_id = ? AND duty_date = ?");
    $attQuery->bind_param("is", $employee_id, $today);
    $attQuery->execute();
    $attResult = $attQuery->get_result();

    if ($attResult->num_rows == 0) {
        $status = 'Present';
        $shift_id = 1;
        $time_in = $punch_time;
        $time_out = null;
        $working_time = null;
        $over_time = null;

        $insert = $con->prepare("INSERT INTO attendances (employee_id, duty_date, shift_id, time_in, time_out, working_time, over_time, status)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("isisssss", $employee_id, $today, $shift_id, $time_in, $time_out, $working_time, $over_time, $status);
        $insert->execute();
        /*-------Send SMS if enabled------*/ 
        $_query = $con->query("SELECT zkteco_message_service, zkteco_present_message FROM app_settings LIMIT 1");
        $_settings = $_query->fetch_assoc();
        if (!empty($_settings) && $_settings['zkteco_message_service'] == 'on') {
            /*------- Get employee phone number------*/
            $phoneQuery = $con->prepare("SELECT name,phone_number FROM employees WHERE id = ?");
            $phoneQuery->bind_param("i", $employee_id);
            $phoneQuery->execute();
            $phoneResult = $phoneQuery->get_result();
            if ($phoneResult->num_rows > 0) {
                $employeeData   = $phoneResult->fetch_assoc();
                $employee_name  = $employeeData['name'];
                $phone          = $employeeData['phone_number'];
                $message        = $_settings['zkteco_present_message'];

                /*-----Replace Placeholder*/
                $message        = str_replace("{name}", $employee_name, $message);
                $message        = str_replace("{date}", $today, $message);
                //$message        = str_replace("{time}", $time_in, $message);
                /*-----Send SMS*/
                send_zkteco_sms($phone, $message);
            }
        }

    } else {
        $attendance = $attResult->fetch_assoc();
        $time_in = $attendance['time_in'];
        $time_out = $attendance['time_out'];

        if (empty($time_out) || $punch_time > $time_out) {
            $update = $con->prepare("UPDATE attendances SET time_out = ? WHERE id = ?");
            $update->bind_param("si", $punch_time, $attendance['id']);
            $update->execute();
        }
    }
}

echo "Transaction sync complete.\n";


/*---------- Send SMS via API ----------*/
function send_zkteco_sms($phone, $message) {    
    $errors = [];

    /* Validate phone number and message */
    if (empty($phone)) {
        $errors['phone'] = "Phone Number is required.";
    }
    if (empty($message)) {
        $errors['message'] = "Message is required.";
    }

    /* Return errors if validation fails */
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
    

    /* SMS API details */
    $url = "http://bulksmsbd.net/api/smsapi";
    $api_key = "WC1N6AFA4gVRZLtyf8z9";
    $senderid = "SR WiFi";
    
    /* Prepare data */
    $data = [
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $phone,
        "message" => $message
    ];

    /* Initialize cURL */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    /* Execute request */
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    return $responseData;
    // if ($responseData['response_code'] == 202) {
    //     echo json_encode([
    //         'success' => true,
    //         'message' => $responseData['success_message']
    //     ]);
    // } else {
    //     echo json_encode([
    //         'success' => false,
    //         'error' => $responseData['error_message'] ?: 'An error occurred.'
    //     ]);
    // }
    // return true; 

}