<?php
if (!isset($_SESSION)) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/include/datatable.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/FileUploader.php';

/*------------Update Customer Ping IP--------------*/
if(isset($_GET['update_customer_ping_ip']) && $_SERVER['REQUEST_METHOD']==='POST'){
    $input = json_decode(file_get_contents('php://input'), true);

    $customer_id = isset($input['customer_id']) ? (int)$input['customer_id'] : 0;
    $customer_ping_ip = isset($input['customer_ping_ip']) ? trim($input['customer_ping_ip']) : '';

    if($customer_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Customer ID.'
        ]);
        exit();
    }

    /*-----------Update Customer Ping ip----------*/ 
    $stmt = $con->prepare("UPDATE customers SET ping_ip = ? WHERE id = ?");
    $stmt->bind_param('si', $customer_ping_ip, $customer_id);

    if($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Customer IP Updated Successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: '.$stmt->error
        ]);
    }

    exit();
   
}

/*-----GET Customer Ping IP-----*/
if(isset($_GET['get_customer_ping_ip']) && $_SERVER['REQUEST_METHOD']==='GET'){
    $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

    if($customer_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Customer ID.'
        ]);
        exit();
    }

    $stmt = $con->prepare("SELECT ping_ip FROM customers WHERE id = ?");
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'ping_ip' => $row['ping_ip']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: '.$stmt->error
        ]);
    }

    exit();
}


if(isset($_GET['update_customer_link']) && $_SERVER['REQUEST_METHOD']==='POST'){
    $input = json_decode(file_get_contents('php://input'), true);

    $customer_id = isset($input['customer_id']) ? (int)$input['customer_id'] : 0;
    $customer_link = isset($input['customer_link']) ? trim($input['customer_link']) : '';

    if($customer_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Customer ID.'
        ]);
        exit();
    }

    // Update database
    $stmt = $con->prepare("UPDATE customers SET customer_link = ? WHERE id = ?");
    $stmt->bind_param('si', $customer_link, $customer_id);

    if($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Customer link updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: '.$stmt->error
        ]);
    }

    exit();
   
}
/*------------ Customer Search List ----------*/
if (isset($_GET['search_customer']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $search = $con->real_escape_string(trim($_GET['search_customer']));
    $customers = $con->query("
      SELECT DISTINCT
                c.id,
                c.customer_name,
                c.customer_email,
                c.customer_phone
            FROM customers c
            LEFT JOIN customer_invoice ci ON ci.customer_id = c.id
            LEFT JOIN customer_service s ON s.id = ci.service_id
            WHERE 
                c.id = 'iig'
                OR c.customer_name LIKE '%$search%'
                OR c.customer_email LIKE '%$search%'
                OR c.customer_phone LIKE '%$search%'
                OR s.name LIKE '%$search%';
                  
    ");
    $data = [];
    while ($row = $customers->fetch_assoc()) {         
        $data[] = $row;
    }
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

    exit;
}
if(isset($_GET['get_customers_data']) && $_SERVER['REQUEST_METHOD']=='GET'){

    $table = 'customers';
    $primaryKey = 'id';

    $columns = array(
        array( 'db' => 'id',                'dt' => 0 ),
        array( 'db' => 'customer_name',     'dt' => 1 ),
        array( 'db' => 'customer_email',    'dt' => 2 ),
        array( 'db' => 'customer_phone',    'dt' => 3 ),
        array( 'db' => 'customer_location', 'dt' => 4 ),
        array( 'db' => 'customer_type_id',  'dt' => 5 ),
        array( 'db' => 'customer_ip',        'dt' => 6 ),
        array( 'db' => 'customer_bandwidth','dt' => 7 ),
        array( 'db' => 'status',             'dt' => 8,
            'formatter' => function ($d) {
                return $d == 1 ? 'Active' : 'Inactive';
            }
        ),
    );
   
    
    /* Output JSON for DataTables to handle*/
    echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns));
    exit; 
}
/*-------------------- Add Customer --------------------*/
if (isset($_GET['add_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name          = trim($_POST['customer_name']);
    $customer_email         = trim($_POST['customer_email']);
    $customer_type          = trim($_POST['customer_type']);
    $customer_pop_branch    = trim($_POST['customer_pop_branch']);
    $customer_vlan          = trim($_POST['customer_vlan']);
    $private_customer_ip    = trim($_POST['private_customer_ip']);
    $service_type           = isset($_POST['service_type'])? trim($_POST['service_type']): null; 
    $customer_status        = trim($_POST['customer_status']);
    $service_customer_type  = trim($_POST['service_customer_type']);
    

    
    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';
    // exit;
    /* Validate Customer Name */
    __validate_input($customer_name, 'Customer Name');

    /* get  customer total */
    $total_limit = 0;
    if(isset($_POST['limit']) && is_array($_POST['limit'])){
        foreach($_POST['limit'] as $lim){
            $total_limit += (int)$lim;
        }
    }
    /*-------------NID PDF File Upload start------------- */
    // if(isset($_FILES['nid_file']) && $_FILES['nid_file']['name'] != '') {
    //     $upload_object = new FileUploader('../assets/customer',['pdf'],['application/pdf'],5000000 // 1MB
    //     );
    //     $nid_file_name = $upload_object->upload('nid_file');
    //     echo '<pre>';
    //     print_r($nid_file_name);
    //     echo '</pre>';exit;
        
        
       
    //     $nid_file = $nid_file_name;
        
    // } else {
    //     $nid_file = '';
    // }
    // exit; 
    /*-------------Service Agreement File Upload start------------- */
    // $service_agreement_file = $_FILES['service_agreement_file'];
    // $service_agreement_file_name = $service_agreement_file['name'];
    // $service_agreement_file_tmp = $service_agreement_file['tmp_name'];
    // $service_agreement_file_size = $service_agreement_file['size'];
    // $service_agreement_file_error = $service_agreement_file['error'];
    // $service_agreement_file_type = $service_agreement_file['type'];

    // $service_agreement_file_ext = explode('.', $service_agreement_file_name);
    // $service_agreement_file_actual_ext = strtolower(end($service_agreement_file_ext));

    // $allowed = array('pdf');
    // echo '<pre>';
    // print_r($service_agreement_file); 
    // echo '</pre>';
    // exit; 

    // if (in_array($service_agreement_file_actual_ext, $allowed)) {
    //     if ($service_agreement_file_error === 0) {
    //         if ($service_agreement_file_size < 1000000) {
    //             $service_agreement_file_name_new = uniqid('', true) . '.' . $service_agreement_file_actual_ext;
    //             $service_agreement_file_destination = '../assets/service_agreement/' . $service_agreement_file_name_new;
    //             move_uploaded_file($service_agreement_file_tmp, $service_agreement_file_destination);
    //         } else {
    //             echo json_encode([
    //                 'success' => false,
    //                 'message' => 'File size should be less than 1MB',
    //             ]);
    //             exit();
    //         }
    //     } else {
    //         echo json_encode([
    //             'success' => false,
    //             'message' => 'There was an error uploading your file',
    //         ]);
    //         exit();
    //     }
    // }
    // exit; 
    /*-------------Service Agreement File Upload End------------- */

    /* Insert into  table */
    $result = $con->query("INSERT INTO customers(`customer_name`,`customer_email`,`pop_id`,`customer_type_id`,`customer_vlan`,`private_customer_ip`,`service_type`,`status`,`total`,`service_customer_type`) VALUES('$customer_name','$customer_email','$customer_pop_branch','$customer_type','$customer_vlan','$private_customer_ip','$service_type','$customer_status','$total_limit','$service_customer_type')");

    $get_customer_id=$con->insert_id;

    /*------------- Bandwidth Service------------- */
    if(isset($_POST['service_customer_type'])==1){
        $service_ids = $_POST['service_id'] ??[];
        $limits = $_POST['limit'] ?? [];
        $total_limit = 0;
        foreach($limits as $lim){
            $total_limit += (int)$lim;
        }
        /*-------------Check Validation-------------*/

        if(count($service_ids) != count($limits)){
            /*-------DELETE Customer And service data-----------*/
            $con->query("DELETE FROM customers WHERE id='$get_customer_id'");
            $con->query("DELETE FROM customer_invoice WHERE customer_id='$get_customer_id'");
            echo json_encode([
                'success' => false,
                'message' => 'Service and Limit mismatch!',
            ]);
            exit();

        }
        foreach($service_ids as $index => $service_id){
            $limit = isset($limits[$index]) ? (int)$limits[$index] : 0;
            $con->query("INSERT INTO customer_invoice(`customer_id`,`service_id`,`customer_limit`) VALUES($get_customer_id,'$service_id','$limit')");
        }
    }
    /*------------- Mac Reseller Service------------- */
    if(isset($_POST['service_customer_type'])==2){
        $service_mac_reseller_packages = $_POST['service_mac_reseller_package'];
        $service_mac_reseller_customer_counts = $_POST['service_mac_reseller_customer_count'];
        /*-------------Check Validation-------------*/
        if(count($service_mac_reseller_packages) != count($service_mac_reseller_customer_counts)){
            /*-------DELETE Customer And service data-----------*/
            $con->query("DELETE FROM customers WHERE id='$get_customer_id'");
            $con->query("DELETE FROM customer_invoice WHERE customer_id='$get_customer_id'");
            echo json_encode([
                'success' => false,
                'message' => 'Mac Reseller Package and Customer Count mismatch!',
            ]);
            exit();

        }
        foreach($service_mac_reseller_packages as $index => $package){
            $customer_count = isset($service_mac_reseller_customer_counts[$index]) ? (int)$service_mac_reseller_customer_counts[$index] : 0;
            $con->query("INSERT INTO mac_reseller_customer_inv(`customer_id`,`package_count`,`total_customer`) VALUES($get_customer_id,'$package','$customer_count')");
        }
    }
    if(isset($_POST['customer_phones']) && is_array($_POST['customer_phones'])){
        $customer_phones = $_POST['customer_phones'];
        foreach($customer_phones as $phone){
            $con->query("INSERT INTO customer_phones(`customer_id`,`phone_number`) VALUES($get_customer_id,'$phone')");
        }
    }
    if(isset($_POST['customer_public_ip']) && is_array($_POST['customer_public_ip'])){
        $customer_ip_address= $_POST['customer_public_ip'];
        foreach($customer_ip_address as $ip_address){
            if(!empty($ip_address)){
                $con->query("INSERT INTO customer_public_ip_address(`customer_id`,`ip_address`)VALUES('$get_customer_id','$ip_address')");
            }
        }
    }

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Pool!',
        ]);
        exit();
    }
}
/******** Update customer data Script ******************/
if (isset($_GET['update_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id            = trim($_POST['customer_id']);
    $customer_name          = trim($_POST['customer_name']);
    $customer_email         = trim($_POST['customer_email']);
    $customer_type          = trim($_POST['customer_type']);
    $customer_pop_branch    = trim($_POST['customer_pop_branch']);
    $customer_vlan          = trim($_POST['customer_vlan']);
    $private_customer_ip    = trim($_POST['private_customer_ip']);
    $service_type           = trim($_POST['service_type']); //nttn or overhead
    $customer_status        = trim($_POST['customer_status']);
    

    /* Validate Customer Name */
    //__validate_input($customer_name, 'Customer Name');

    /* get  customer total */
    $total_limit = 0;
    if(isset($_POST['limit']) && is_array($_POST['limit'])){
        foreach($_POST['limit'] as $lim){
            $total_limit += (int)$lim;
        }
    }

    /* Update  table */
    $result = $con->query("UPDATE customers SET `customer_name`='$customer_name',`customer_email`='$customer_email',`pop_id`='$customer_pop_branch',`customer_type_id`='$customer_type',`customer_vlan`='$customer_vlan',`private_customer_ip`='$private_customer_ip',`service_type`='$service_type',`status`='$customer_status',`total`='$total_limit' WHERE id='$customer_id'");
   
    if(isset($_POST['customer_phones']) && is_array($_POST['customer_phones'])){
        /*-------------Delete Existing Phone Number----------------*/
        $con->query("DELETE FROM customer_phones WHERE customer_id='$customer_id'");
        $customer_phones = $_POST['customer_phones'];
        foreach($customer_phones as $phone){
            if(!empty($phone)){
                $con->query("INSERT INTO customer_phones(`customer_id`,`phone_number`) VALUES($customer_id,'$phone')");
            }
        }
    }
    if(isset($_POST['customer_public_ip']) && is_array($_POST['customer_public_ip'])){
        /*-------------Delete Existing public ip address----------------*/
        $con->query("DELETE  FROM  customer_public_ip_address WHERE customer_id='$customer_id'");
        $customer_ip_address=$_POST['customer_public_ip'];
        foreach($customer_ip_address as $ip_address){
            if(!empty($ip_address)){
                $con->query("INSERT INTO customer_public_ip_address(`customer_id`,`ip_address`)VALUES('$customer_id','$ip_address')");
            }
        }
    }
    /*------- Delete existing services------*/
    $con->query("DELETE FROM customer_invoice WHERE customer_id='$customer_id'");

    if(isset($_POST['service_id']) && is_array($_POST['service_id'])){
        $service_ids = $_POST['service_id'];
        $limits = $_POST['limit'];
        foreach($service_ids as $index => $service_id){
            $limit = isset($limits[$index]) ? (int)$limits[$index] : 0;
            $con->query("INSERT INTO customer_invoice(`customer_id`,`service_id`,`customer_limit`) VALUES($customer_id,'$service_id','$limit')");
        }
    }

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update customer!',
        ]);
        exit();
    }
}

if (isset($_GET['get_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM customers WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM customers");
    }

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => isset($_GET['id']) ? ($data[0] ?? []) : $data,
    ]);
    exit();
}
/*Delete customer Script*/
if (isset($_GET['delete_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM customers WHERE id='$id'");
    $con->close();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete!',
        ]);
        exit();
    }
}


/******** Add Customer Type Script ******************/
if (isset($_GET['add_customer_type_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_type = trim($_POST['customer_type_name']);

    /* Validate Customer Type */
    __validate_input($customer_type, 'Customer Type');

    
    /* Insert into  table */
    $result = $con->query("INSERT INTO customer_type(`name`) VALUES('$customer_type')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Pool!',
        ]);
        exit();
    }
}

/******** Update Customer Type Script ******************/
if (isset($_GET['update_customer_type_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_type = trim($_POST['customer_type_name']);
    $id = trim($_POST['id']);
    /* Validate Customer Type */
    __validate_input($customer_type, 'Customer Type');
    /* Check if customer type already exists */
    $check_customer_type = $con->query("SELECT * FROM customer_type WHERE name='$customer_type' AND id != '$id'");
    if ($check_customer_type->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer Type Already exists!',
        ]);
        exit();
    }
    /* Update the customer type in the database */
    $result = $con->query("UPDATE customer_type SET name='$customer_type' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Topic!',
        ]);
        exit();
    }
}

if (isset($_GET['get_customer_type_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM customer_type WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM customer_type");
    }

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => isset($_GET['id']) ? ($data[0] ?? []) : $data,
    ]);
    exit();
}
/*Delete customer type Script*/
if (isset($_GET['delete_customer_type_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM customer_type WHERE id='$id'");
    $con->close();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete!',
        ]);
        exit();
    }
}

/******** Add Customer services type Script ******************/
if (isset($_GET['add_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_service = trim($_POST['customer_service_name']);

    /* Validate Customer Service */
    __validate_input($customer_service, 'Customer Service');

    
    /* Insert into  table */
    $result = $con->query("INSERT INTO customer_service(`name`) VALUES('$customer_service')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Pool!',
        ]);
        exit();
    }
}

/******** Update Customer Service Script ******************/
if (isset($_GET['update_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_service = trim($_POST['customer_service_name']);
    $id = trim($_POST['id']);
    /* Validate Customer Service */
    __validate_input($customer_service, 'Customer Service');
    /* Check if customer service already exists */
    $check_customer_service = $con->query("SELECT * FROM customer_service WHERE name='$customer_service' AND id != '$id'");
    if ($check_customer_service->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer Service Already exists!',
        ]);
        exit();
    }
    /* Update the customer service in the database */
    $result = $con->query("UPDATE customer_service SET name='$customer_service' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Topic!',
        ]);
        exit();
    }
}

if (isset($_GET['get_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM customer_service WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM customer_service");
    }

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => isset($_GET['id']) ? ($data[0] ?? []) : $data,
    ]);
    exit();
}
/*Delete customer service Script*/
if (isset($_GET['delete_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM customer_service WHERE id='$id'");
    $con->close();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete!',
        ]);
        exit();
    }
}




function __validate_input($value, $field)
{
    if (empty($value) || $value === '---Select---') {
        echo json_encode([
            'success' => false,
            'message' => '' . $field . ' is required!',
        ]);
        exit();
    }
}



?>