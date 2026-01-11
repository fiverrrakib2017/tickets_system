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


/******** Add Customer  Script ******************/
if (isset($_GET['add_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = trim($_POST['name']);

    /* Validate Customer Name */
    __validate_input($customer_name, 'Customer Name');


    /* Insert into  table */
    $result = $con->query("INSERT INTO ticket_assign(name) VALUES('$customer_name')");

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
/******** Update ticket assign  Script ******************/
if (isset($_GET['update_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $assign_name = trim($_POST['name']);
    $id = trim($_POST['id']);
    /* Validate Assign Name  name */
    __validate_input($assign_name, 'Assign Name');
    /* Check if assign name already exists */
    $check_assign_name = $con->query("SELECT * FROM ticket_assign WHERE name='$assign_name' AND id != '$id'");
    if ($check_assign_name->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Assign Name Already exists!',
        ]);
        exit();
    }
    /* Update the assign in the database */
    $result = $con->query("UPDATE ticket_assign SET name='$assign_name' WHERE id='$id'");
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

if (isset($_GET['get_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM ticket_assign WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM ticket_assign");
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
/*Delete ticket assign Script*/
if (isset($_GET['delete_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM ticket_assign WHERE id='$id'");
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