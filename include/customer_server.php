<?php
if (!isset($_SESSION)) {
    session_start();
}
//date_default_timezone_set('Asia/Dhaka');
include 'db_connect.php';
include 'functions.php';
require 'datatable.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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
/******** Add Customer  Script ******************/
if (isset($_GET['add_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $customer_type = trim($_POST['customer_type']);
    $customer_pop_branch = trim($_POST['customer_pop_branch']);
    $customer_vlan = trim($_POST['customer_vlan']);
    $customer_ip = trim($_POST['customer_ip']);
    $customer_bandwidth = trim($_POST['customer_bandwidth']);
    $customer_status = trim($_POST['customer_status']);


    /* Validate Customer Name */
    __validate_input($customer_name, 'Customer Name');


    /* Insert into  table */
    $result = $con->query("INSERT INTO customers(`customer_name`,`customer_email`,`customer_phone`,`pop_id`,`customer_type_id`,`customer_vlan`,`customer_ip`,`customer_bandwidth`,`status`) VALUES('$customer_name','$customer_email','$customer_phone','$customer_pop_branch','$customer_type','$customer_vlan','$customer_ip','$customer_bandwidth','$customer_status')");

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
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $customer_pop_branch = trim($_POST['customer_pop_branch']);
    $customer_type = trim($_POST['customer_type']);
    $customer_vlan = trim($_POST['customer_vlan']);
    $customer_ip = trim($_POST['customer_ip']);
    $customer_bandwidth = trim($_POST['customer_bandwidth']);
    $id = trim($_POST['id']);
    /* Validate Customer Name */
    __validate_input($customer_name, 'Customer Name');
    /* Check if customer name already exists */
    $check_customer_name = $con->query("SELECT * FROM customers WHERE customer_name='$customer_name' AND id != '$id'");
    if ($check_customer_name->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer Name Already exists!',
        ]);
        exit();
    }
    /* Update the customer in the database */
    $result = $con->query("UPDATE customers SET customer_name='$customer_name',customer_email='$customer_email',customer_phone='$customer_phone',pop_id='$customer_pop_branch',customer_type_id='$customer_type',customer_vlan='$customer_vlan',customer_ip='$customer_ip',customer_bandwidth='$customer_bandwidth' WHERE id='$id'");
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