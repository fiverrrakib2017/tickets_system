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
    $customer_note = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : '';
    $noc_note = isset($_POST['noc_note']) ? trim($_POST['noc_note']) : '';

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
        (
            customer_id,
            ticket_type,
            asignto,
            ticketfor,
            pop_id,
            complain_type,
            startdate,
            enddate,
            parcent,
            priority,
            customer_note,
            noc_note,
            create_date
        )
        VALUES (
            ?, 'Active', ?, ?, ?, ?, ?, NULL, '0%', ?, ?, ?, ?
        )
    ");


    $startDate = date('Y-m-d H:i:s');

    $insert->bind_param(
        'iissssssss',
        $customer_id,
        $assign_to,
        $ticket_for,
        $customerPopId,
        $complain_type,
        $startDate,
        $priority,
        $customer_note,
        $noc_note,
        $create_date
    );
    $result = $insert->execute();
    if($result){
        echo json_encode([
            'success' => true,
            'message'  =>  'Ticket created successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message'  =>  'Error: ' . $insert->error
        ]);
    }

    exit;
}
/*----------- update Ticket Data------------------*/
if (isset($_GET['update_ticket_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = isset($_POST['ticket_id']) ? trim($_POST['ticket_id']) : '';
    $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    $ticket_for = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type = isset($_POST['complain_type']) ? trim($_POST['complain_type']) : '';
    $assign_to = isset($_POST['assign_to']) ? trim($_POST['assign_to']) : '';
    $note = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $priority = isset($_POST['ticket_priority']) ? trim($_POST['ticket_priority']) : '';
    $customer_note = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : '';
    $noc_note = isset($_POST['noc_note']) ? trim($_POST['noc_note']) : '';

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
    /* ---------- Update Ticket ---------- */
    $update = $con->prepare("
        UPDATE ticket 
        SET 
            customer_id = ?,
            ticketfor = ?,
            asignto = ?,
            complain_type = ?,
            priority = ?,
            customer_note = ?,
            noc_note = ?
        WHERE id = ?
    ");

    $update->bind_param(
        'isssssss',
        $customer_id,
        $ticket_for,
        $assign_to,
        $complain_type,
        $priority,
        $customer_note,
        $noc_note,
        $ticket_id
    );
    $result = $update->execute();
    if($result){
        echo json_encode([
            'success' => true,
            'message'  =>  'Ticket updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message'  =>  'Error: ' . $update->error
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

/*--------Add Ticket Comment Script------------------*/
if (isset($_GET['add_ticket_comment']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
     
    $ticket_id = trim($_POST['ticket_id']);
    $ticket_type = trim($_POST['ticket_type']);
    $progress = trim($_POST['progress']);
    $assign_to = trim($_POST['assign_to']);
    $comment = trim($_POST['comment']);

    /* Validate ticket ID */
    __validate_input($ticket_id, 'Ticket ID');
    __validate_input($comment, 'Comment');
    __validate_input($assign_to, 'Assign To');

    /* Insert into ticket_details table */
    $result = $con->query("INSERT INTO ticket_details(tcktid, status, datetm,comments,parcent,asignto) VALUES('$ticket_id', '$ticket_type', NOW(), '$comment', '$progress', '$assign_to')");

    $con->query("UPDATE ticket SET ticket_type='$ticket_type', parcent='$progress', asignto='$assign_to' WHERE id='$ticket_id'");
    if(isset($progress) && $progress == '100%'){
        $enddate = date('Y-m-d H:i:s');
        $con->query("UPDATE ticket SET enddate='$enddate' WHERE id='$ticket_id'");
    }
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Comment added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add comment!',
        ]);
        exit();
    }
}
/* -------Function to calculate actual work time */
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