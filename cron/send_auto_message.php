<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Dhaka");

    if(!isset($_SERVER['DOCUMENT_ROOT'])||$_SERVER['DOCUMENT_ROOT']==''){
        $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    }
    include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

    /* Get App Settings */
    $get_app_settings_data = $con->query("SELECT * FROM app_settings LIMIT 1");
    $_settings = $get_app_settings_data->fetch_assoc();

    /*----------Check SMS Enable On*/
    if (!isset($_settings['auto_sms_enabled']) || $_settings['auto_sms_enabled'] != 'on') {
        die("SMS sending is disabled in settings.");
    }
    if(empty($_settings['sms_api_url']) || empty($_settings['sms_api_key']) || empty($_settings['sms_sender_id'])) {
        die("SMS API settings are incomplete.");
    }
    if(empty($_settings['sms_template'])) {
        die("SMS template is empty.");
    }
    if(empty($_settings['sms_dayes_before']) || !is_numeric($_settings['sms_dayes_before']) || (int)$_settings['sms_dayes_before'] < 1) {
        die("Invalid number of days before expiry for sending SMS.");
    }
    if(!empty($_settings['auto_sms_enabled']) && $_settings['auto_sms_enabled'] = 'on') {
        /* --------Get Target Expire Date-------- */
        $sms_days_before = !empty($_settings['sms_dayes_before']) ? (int)$_settings['sms_dayes_before'] : 2;
        $target_expire_date = date('Y-m-d', strtotime("+$sms_days_before days"));
        //echo $target_expire_date; exit;
        /*------------ Get Customers -------------- */
        $cstmr = $con->query("SELECT * FROM customers WHERE expiredate='$target_expire_date' 
            AND (is_send_message IS NULL OR is_send_message != 1)");

        
        $messages = []; 
        if ($cstmr && $cstmr->num_rows > 0) {
            while ($rows = $cstmr->fetch_assoc()) {
                // echo '<pre>';
                //     print_r($rows);
                // echo '</pre>';exit; 
                $customer_id    = $rows["id"];
                $username       = $rows["username"];
                $raw_phone      = $rows["mobile"];
                $expiredate     = $rows["expiredate"];
                $area           = ($q = $con->query("SELECT name FROM area_list WHERE id=".(int)$rows['area'])) ? ($q->fetch_assoc()['name'] ?? "Unknown") : "Unknown";
                $pop            = ($q = $con->query("SELECT name FROM add_pop WHERE id=".(int)$rows['pop'])) ? ($q->fetch_assoc()['pop'] ?? "Unknown") : "Unknown";
                $package_name   = $rows["package_name"];
                /*------------ Phone validate with regex--------------------*/
                if (preg_match('/(\+8801[3-9][0-9]{8}|01[3-9][0-9]{8})/', $raw_phone, $matches)) {
                    $phone = $matches[0];
                } else {
                    $phone = null;
                }

                /*------------ Send SMS -------------------*/
                if (!empty($phone)) {
                    $sms_api_url    = trim($_settings['sms_api_url']);
                    $sms_api_key    = trim($_settings['sms_api_key']);
                    $sms_sender_id  = trim($_settings['sms_sender_id']);
                    $original_message = isset($_settings["sms_template"]) ? trim($_settings["sms_template"]) : '';

                
                    if (empty($original_message)) {
                        continue;
                    }

                    /*---------------- Placeholder Replace-------------------------*/
                    $personalized_message = $original_message;
                    $personalized_message = str_replace("{id}", $customer_id, $personalized_message);
                    $personalized_message = str_replace("{username}", $username, $personalized_message);
                    $personalized_message = str_replace("{mobile}", $phone, $personalized_message);
                    $personalized_message = str_replace("{area}", $area, $personalized_message);
                    $personalized_message = str_replace("{package_name}", $package_name, $personalized_message);
                    $personalized_message = str_replace("{expiredate}", $expiredate, $personalized_message);

                    $messages[] = [
                        'customer_id' => $customer_id,  
                        "to" => $phone,
                        "message" => $personalized_message
                    ];
                }
            }
        }
        
        /* If we have messages then send */
        if (!empty($messages)) {
            foreach ($messages as $msg) {
                $customer_id = (int)$msg['customer_id'];
                $data = [
                    "api_key"  => $_settings['sms_api_key'],
                    "senderid" => $_settings['sms_sender_id'],
                    "number"   => $msg['to'],
                    "message"  => $msg['message']
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $_settings['sms_api_url']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($ch);
                curl_close($ch);

                $responseData = json_decode($response, true);

                if (isset($responseData['response_code']) && $responseData['response_code'] == 202) {
                    echo "Message sent successfully to {$msg['to']}<br>";
                    /*----------Call Sms Log Function------------*/ 
                    sms_logs($msg['to'], $msg['message'], 1);

                    /*----------Update customer------------*/ 
                    $con->query("UPDATE customers SET is_send_message='1' WHERE id=$customer_id");
                } else {
                    echo "Failed to send message to {$msg['to']}: " . ($responseData['error_message'] ?? 'Unknown Error') . "<br>";
                    /*----------Call Sms Log Function------------*/ 
                    sms_logs($msg['to'], $msg['message'], 2);
                }
            }
        }
    }

?>
