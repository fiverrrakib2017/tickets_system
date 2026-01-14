<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';
include 'functions.php';
if (!isset($_SESSION)) {
    session_start();
}

/*----------- Add Ticket Data------------------*/
if(isset($_GET['add_ticket_data']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
   
    $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    $ticket_for = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type = isset($_POST['complain_type']) ? trim($_POST['complain_type']) : '';
    $assign_to = isset($_POST['assign_to']) ? trim($_POST['assign_to']) : '';
    $note = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $priority = isset($_POST['ticket_priority']) ? trim($_POST['ticket_priority']) : '';

    /* ---------- Validation ---------- */
    if(empty((int)$customer_id)) {
       echo json_encode([
            'success' => false,
            'message'  =>  'Customer ID is required.'
        ]);
        exit();
    }
    if(empty($ticket_for)) {
       echo json_encode([
            'success' => false,
            'message'  =>  'Ticket For is required.'
        ]);
        exit();
    }
    if(empty($complain_type)) {
       echo json_encode([
            'success' => false,
            'message'  =>  'Complain Type is required.'
        ]);
        exit();
    }
    if(empty($assign_to)) {
       echo json_encode([
            'success' => false,
            'message'  =>  'Assign To is required.'
        ]);
        exit();
    }
    if(empty($priority)) {
       echo json_encode([
            'success' => false,
            'message'  =>  'Ticket Priority is required.'
        ]);
        exit();
    }

    /* ---------- Check Active Ticket ---------- */
    $stmt = $con->prepare("
        SELECT id 
        FROM ticket 
        WHERE customer_id = ? AND ticket_type != 'Complete'
        LIMIT 1
    ");
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message'  =>  'You already have an active ticket.'
        ]);
        exit();
    }

    
    /* ---------- Get Customer POP ---------- */
    $popStmt = $con->prepare("SELECT pop_id FROM customers WHERE id = ?");
    $popStmt->bind_param('i', $customer_id);
    $popStmt->execute();
    $popResult = $popStmt->get_result();
    $customerPopId = $popResult->fetch_assoc()['pop_id'] ?? null;

   
    /* ---------- Insert Ticket ---------- */
    $create_date = date('Y-m-d H:i:s');
    
    $insert = $con->prepare("
        INSERT INTO ticket 
        (customer_id, ticket_type, asignto, ticketfor, pop_id, complain_type,
         startdate, enddate, notes, parcent, priority, create_date)
        VALUES (?, 'Active', ?, ?, ?, ?, ?, NULL, ?, '0%', ?, ?)
    ");

    $startDate = date('Y-m-d H:i:s');

    $insert->bind_param(
        'ississsis',
        $customer_id,
        $assign_to,
        $ticket_for,
        $customerPopId,
        $complain_type,
        $startDate,
        $note,
        $priority,
        $create_date
    );

    if ($insert->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket Added Successfully'
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'error'   => $insert->error
        ]);
    }

    exit;
}

if (isset($_POST['get_complain_type']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $con->query('SELECT id,topic_name FROM ticket_topic WHERE user_type=1');
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}
/******** Ticket Update ******************/
if (isset($_GET['update_ticket_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $ticket_type = isset($_POST['ticket_type']) ? trim($_POST['ticket_type']) : '';
    $assigned = isset($_POST['assigned']) ? trim($_POST['assigned']) : '';
    $ticket_for = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type = isset($_POST['complain_type']) ? trim($_POST['complain_type']) : '';
    $from_date= isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
    $end_date= isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
    $user_type= isset($_POST['user_type']) ? trim($_POST['user_type']) : '1';
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $errors = [];
    if (empty($id)) $errors['id'] = 'Ticket ID is required.';
    if (empty($ticket_type)) $errors['ticket_type'] = 'Ticket Type is required.';
    if (empty($assigned)) $errors['assigned'] = 'Assigned field is required.';
    if (empty($ticket_for)) $errors['ticket_for'] = 'Ticket For is required.';
    if (empty($complain_type)) $errors['complain_type'] = 'Complain Type is required.';
    if (empty($from_date)) $errors['from_date'] = 'From Date is required.';
    // if (!empty($end_date) && strtotime($end_date) < strtotime($from_date)) {
    //     $errors['end_date'] = 'End Date cannot be earlier than From Date.';
    // }
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit();
    }
    // echo 'okkkk'; exit; 
    $stmt = $con->prepare("UPDATE ticket SET ticket_type=?, asignto=?, ticketfor=?, complain_type=?, startdate=?, enddate=?, notes=?, user_type=? WHERE id=?");
    $stmt->bind_param('sisssssii', $ticket_type, $assigned, $ticket_for, $complain_type, $from_date, $end_date, $note, $user_type, $id);    
    $result = $stmt->execute();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket updated successfully.',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $stmt->error,
        ]);
    }
    $stmt->close();
    exit();
}


/******** Add Ticket topic  Script ******************/
if (isset($_GET['add_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_name = trim($_POST['topic_name']);

    /* Validate Topic Name  name */
    __validate_input($topic_name, 'Topic Name');
    /* Insert into  table */
    $result = $con->query("INSERT INTO ticket_topic(topic_name) VALUES('$topic_name')");
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
/******** Update ticket topic  Script ******************/
if (isset($_GET['update_ticket_topic__data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $topic_name = trim($_POST['topic_name']);
    $id = trim($_POST['id']);
    /* Validate Topic Name  name */
    __validate_input($topic_name, 'Topic Name');
    /* Check if topic name already exists */
    $check_topic_name = $con->query("SELECT * FROM ticket_topic WHERE topic_name='$topic_name' AND id != '$id'");
    if ($check_topic_name->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Topic Name Already exists!',
        ]);
        exit();
    }
    /* Update the topic in the database */
    $result = $con->query("UPDATE ticket_topic SET topic_name='$topic_name' WHERE id='$id'");
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

if (isset($_GET['get_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM ticket_topic WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM ticket_topic");
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
/*Delete ticket topic Script*/
if (isset($_GET['delete_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM ticket_topic WHERE id='$id'");
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

/******** Add Ticket Assign  Script ******************/
if (isset($_GET['add_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $assign_name = trim($_POST['name']);

    /* Validate Assign Name  name */
    __validate_input($assign_name, 'Assign Name');
    /* Insert into  table */
    $result = $con->query("INSERT INTO ticket_assign(name) VALUES('$assign_name')");
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



function acctual_work($startdate, $enddate)
{
    $startTimestamp = strtotime($startdate);
    $endTimestamp = strtotime($enddate);
    $time_difference = $endTimestamp - $startTimestamp;

    // Define time periods in seconds
    $units = [
        'year' => 365 * 24 * 60 * 60,
        'month' => 30 * 24 * 60 * 60,
        'week' => 7 * 24 * 60 * 60,
        'day' => 24 * 60 * 60,
        'hour' => 60 * 60,
        'minute' => 60,
        'second' => 1,
    ];

    // Determine the appropriate time period
    foreach ($units as $unit => $value) {
        if ($time_difference >= $value) {
            $count = floor($time_difference / $value);
            return $count . ' ' . $unit . ($count > 1 ? 's' : '') . ' ';
        }
    }

    return 'just now';
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