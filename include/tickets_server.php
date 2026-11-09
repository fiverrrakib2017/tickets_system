<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

/*----------- Mark Ticket Completed------------------*/
if(isset($_GET['mark_ticket_completed']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $ticket_id = isset($_POST['ticket_id']) ? trim($_POST['ticket_id']) : '';

    /* Validate Ticket ID */
    __validate_input($ticket_id, 'Ticket ID');

    /* Fetch ticket info before update for logging */
    $ticket_subject = "Unknown";
    $stmt_chk = $con->prepare("SELECT subject FROM ticket WHERE id = ? LIMIT 1");
    $stmt_chk->bind_param('i', $ticket_id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    if($res_chk->num_rows > 0){
        $ticket_subject = $res_chk->fetch_assoc()['subject'] ?? 'Unknown';
    }
    $stmt_chk->close();

    /* Update Ticket Status */
    $end_date = date('Y-m-d H:i:s');
    $update = $con->prepare("UPDATE ticket SET ticket_type = 'Complete', parcent = '100%', enddate = ? WHERE id = ?");
    $update->bind_param('si', $end_date, $ticket_id);
    $result = $update->execute();
    $update->close();

    if($result){
        /*----------- Log User Activity -----------*/
        $log_desc = "Marked Ticket ID: $ticket_id as Completed (Subject: '$ticket_subject')";
        if(function_exists('system_log')){
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message'  =>  'Ticket marked as completed successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message'  =>  'Error updating ticket status.'
        ]);
    }

    exit;
}

/*----------- Add Ticket Data------------------*/
if(isset($_GET['add_ticket_data']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $customer_id            = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    $ticket_for             = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type          = isset($_POST['complain_type']) ? trim($_POST['complain_type']) : '';
    $assign_user_id         = isset($_POST['assign_user_id']) ? trim($_POST['assign_user_id']) : '';
    $note                   = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $priority               = isset($_POST['ticket_priority']) ? trim($_POST['ticket_priority']) : '';
    $customer_note          = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : '';
    $noc_note               = isset($_POST['noc_note']) ? trim($_POST['noc_note']) : '';
    $customer_subject       = isset($_POST['customer_subject']) ? trim($_POST['customer_subject']) : '';
    $customer_description   = isset($_POST['customer_description']) ? trim($_POST['customer_description']) : '';

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
    if(!isset($_SESSION['customer']['id'])){
        if(empty($complain_type)) {
            echo json_encode([
                'success' => false,
                'message'  =>  'Complain Type is required.'
            ]);
            exit();
        }
        if(empty($assign_user_id)) {
            echo json_encode([
                'success' => false,
                'message'  =>  'Assign To is required.'
            ]);
            exit();
        }
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
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message'  =>  'You already have an active ticket.'
        ]);
        exit();
    }
    $stmt->close();

    /*-------------Upload Ticket File-------------*/
    $ticket_file = function_exists('__upload_file') ? __upload_file($_FILES['customer_attachments'] ?? null) : null;
    
    /* ---------- Get Customer POP ---------- */
    $popStmt = $con->prepare("SELECT pop_id FROM customers WHERE id = ? LIMIT 1");
    $popStmt->bind_param('i', $customer_id);
    $popStmt->execute();
    $popResult = $popStmt->get_result();
    $customerPopId = null;
    if($popResult->num_rows > 0){
        $customerPopId = $popResult->fetch_assoc()['pop_id'] ?? null;
    }
    $popStmt->close();

    /* ---------- Insert Ticket ---------- */
    $create_date = date('Y-m-d H:i:s');
    $startDate = date('Y-m-d H:i:s');
    
    $insert = $con->prepare("
        INSERT INTO ticket 
        (
            customer_id,
            ticket_type,
            assign_user_id,
            ticketfor,
            pop_id,
            complain_type,
            startdate,
            enddate,
            parcent,
            priority,
            customer_note,
            noc_note,
            subject,
            description,
            attachments,
            create_date
        )
        VALUES (
            ?, 'Active', ?, ?, ?, ?, ?, NULL, '0%', ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $insert->bind_param(
        'iisssssssssss',
        $customer_id,
        $assign_user_id,
        $ticket_for,
        $customerPopId,
        $complain_type,
        $startDate,
        $priority,
        $customer_note,
        $noc_note,
        $customer_subject,
        $customer_description,
        $ticket_file,
        $create_date
    );
    
    $result = $insert->execute();
    $ticket_id = $insert->insert_id;
    $insert->close();

    if($result){
        /*----------- Log User Activity -----------*/
        $log_desc = "Created new ticket ID: $ticket_id for Customer ID: $customer_id (Subject: '$customer_subject', Priority: $priority)";
        if(function_exists('system_log')){
            system_log($con, 'INSERT', $log_desc);
        } 

        echo json_encode([
            'success' => true,
            'message'  =>  'Ticket created successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message'  =>  'Error creating ticket.'
        ]);
    }

    exit;
}

/*----------- Add Management Ticket Data------------------*/
if (isset($_GET['add_management_ticket_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_for             = 'Mac Client';
    $complain_type          = null;
    $assign_user_id         = isset($_POST['assign_user_id']) ? trim($_POST['assign_user_id']) : '';
    $note                   = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $priority               = isset($_POST['ticket_priority']) ? trim($_POST['ticket_priority']) : '';
    $customer_note          = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : '';
    $noc_note               = isset($_POST['noc_note']) ? trim($_POST['noc_note']) : '';
    $customer_subject       = isset($_POST['customer_subject']) ? trim($_POST['customer_subject']) : '';
    $customer_description   = isset($_POST['customer_description']) ? trim($_POST['customer_description']) : '';
    
    /*---------- Check Management or Fiber Ticket ----------*/
    $is_management_ticket = 0; 
    
    if (isset($_GET['fiber_team_ticket'])) {
        $is_management_ticket = 2;
    } else {
        $is_management_ticket = 1;
    }
    
    if(empty($ticket_for)) {
       echo json_encode([
            'success' => false,
            'message'  =>  'Ticket For is required.'
        ]);
        exit();
    }
   if(empty($customer_subject)) {
        echo json_encode([
            'success' => false,
            'message'  =>  'Subject is required.'
        ]);
        exit();
    }
    if(empty($assign_user_id)) {
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

    /*-------------Upload Ticket File-------------*/
    $ticket_file = __upload_file($_FILES['customer_attachments'] ?? null);
    
    /* ---------- Insert Ticket ---------- */
    $create_date = date('Y-m-d H:i:s');
    
    $insert = $con->prepare("
        INSERT INTO ticket 
        (
            is_management_ticket,
            ticket_type,
            assign_user_id,
            ticketfor,
            complain_type,
            startdate,
            enddate,
            parcent,
            priority,
            customer_note,
            noc_note,
            subject,
            description,
            attachments,
            create_date
        )
        VALUES (
            ?,'Active', ?,  ?, ?, ?, NULL, '0%', ?, ?, ?, ?, ?, ?, ?
        )
    ");


    $startDate = date('Y-m-d H:i:s');

    $insert->bind_param(
        'iissssssssss',
        $is_management_ticket,
        $assign_user_id,
        $ticket_for,
        $complain_type,
        $startDate,
        $priority,
        $customer_note,
        $noc_note,
        $customer_subject,
        $customer_description,
        $ticket_file,
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

/*----------- Update Management Ticket Data------------------*/
if (isset($_GET['update_management_ticket_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $ticket_id              = (int)($_POST['ticket_id'] ?? 0);
    $ticket_for             = trim($_POST['ticket_for'] ?? '');
    $complain_type          = (int)($_POST['complain_type'] ?? 0);
    $assign_to              = (int)($_POST['assign_user_id'] ?? 0);
    $priority               = (int)($_POST['ticket_priority'] ?? 0);
    $customer_note          = trim($_POST['customer_note'] ?? '');
    $noc_note               = trim($_POST['noc_note'] ?? '');
    $customer_subject       = trim($_POST['customer_subject'] ?? '');
    $customer_description   = trim($_POST['customer_description'] ?? '');

    

    /* ---------- Get old attachment ---------- */
    $old = $con->prepare("SELECT attachments FROM ticket WHERE id=?");
    $old->bind_param("i", $ticket_id);
    $old->execute();
    $oldFile = $old->get_result()->fetch_assoc()['attachments'] ?? null;

    /* ---------- Upload file ---------- */
    if (isset($_FILES['customer_attachments']) && $_FILES['customer_attachments']['error'] === 0) {
        $ticket_file = __upload_file($_FILES['customer_attachments'], $oldFile);
    } else {
        $ticket_file = $oldFile;
    }

    /* ---------- Update ---------- */
    $stmt = $con->prepare("
        UPDATE ticket SET
            ticketfor=?,
            assign_user_id=?,
            complain_type=?,
            priority=?,
            customer_note=?,
            noc_note=?,
            subject=?,
            description=?,
            attachments=?,
            create_date=NOW()
        WHERE id=?
    ");

    $stmt->bind_param(
        "siiisssssi",
        $ticket_for,
        $assign_to,
        $complain_type,
        $priority,
        $customer_note,
        $noc_note,
        $customer_subject,
        $customer_description,
        $ticket_file,
        $ticket_id
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Management Ticket updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error'
        ]);
    }

    exit;
}
/*----------- update Ticket Data------------------*/
if (isset($_GET['update_ticket_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
     
    $ticket_id              = (int)($_POST['ticket_id'] ?? 0);
    $customer_id            = (int)($_POST['customer_id'] ?? 0);
    $ticket_for             = trim($_POST['ticket_for'] ?? '');
    $complain_type          = (int)($_POST['complain_type'] ?? 0);
    $assign_to              = (int)($_POST['assign_user_id'] ?? 0);
    $pop_branch             = (int)($_POST['pop_branch'] ?? 0);
    $priority               = (int)($_POST['ticket_priority'] ?? 0);
    $customer_note          = trim($_POST['customer_note'] ?? '');
    $noc_note               = trim($_POST['noc_note'] ?? '');
    $customer_subject       = trim($_POST['customer_subject'] ?? '');
    $customer_description   = trim($_POST['customer_description'] ?? '');

    /* ---------- Validation ---------- */
    if ($ticket_id <= 0 || $customer_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ticket or customer']);
        exit;
    }

    /* ---------- Get old ticket data for logging diff ---------- */
    $stmt_old = $con->prepare("SELECT * FROM ticket WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $ticket_id);
    $stmt_old->execute();
    $oldTicket = $stmt_old->get_result()->fetch_assoc();
    $stmt_old->close();

    if (!$oldTicket) {
        echo json_encode(['success' => false, 'message' => 'Ticket not found']);
        exit;
    }

    $oldFile = $oldTicket['attachments'] ?? null;

    /* ---------- Upload file ---------- */
    if (isset($_FILES['customer_attachments']) && $_FILES['customer_attachments']['error'] === 0) {
        $ticket_file = function_exists('__upload_file') ? __upload_file($_FILES['customer_attachments'], $oldFile) : $oldFile;
    } else {
        $ticket_file = $oldFile;
    }

    /* ---------- Update ---------- */
    $stmt = $con->prepare("
        UPDATE ticket SET
            customer_id=?,
            ticketfor=?,
            pop_id=?,
            assign_user_id=?,
            complain_type=?,
            priority=?,
            customer_note=?,
            noc_note=?,
            subject=?,
            description=?,
            attachments=?,
            create_date=NOW()
        WHERE id=?
    ");

    $stmt->bind_param(
        "isiiiisssssi",
        $customer_id,
        $ticket_for,
        $pop_branch,
        $assign_to,
        $complain_type,
        $priority,
        $customer_note,
        $noc_note,
        $customer_subject,
        $customer_description,
        $ticket_file,
        $ticket_id
    );

    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Track Changes (Diff Calculation) -----------*/
        $changes = [];
        
        if (($oldTicket['customer_id'] ?? '') != $customer_id) {
            $changes[] = "Customer ID: [Old: '{$oldTicket['customer_id']}' -> New: '$customer_id']";
        }
        if (($oldTicket['ticketfor'] ?? '') !== $ticket_for) {
            $changes[] = "Ticket For: [Old: '{$oldTicket['ticketfor']}' -> New: '$ticket_for']";
        }
        if (($oldTicket['pop_id'] ?? '') != $pop_branch) {
            $changes[] = "POP ID: [Old: '{$oldTicket['pop_id']}' -> New: '$pop_branch']";
        }
        if (($oldTicket['assign_user_id'] ?? '') != $assign_to) {
            $changes[] = "Assign User ID: [Old: '{$oldTicket['assign_user_id']}' -> New: '$assign_to']";
        }
        if (($oldTicket['complain_type'] ?? '') != $complain_type) {
            $changes[] = "Complain Type: [Old: '{$oldTicket['complain_type']}' -> New: '$complain_type']";
        }
        if (($oldTicket['priority'] ?? '') != $priority) {
            $changes[] = "Priority: [Old: '{$oldTicket['priority']}' -> New: '$priority']";
        }
        if (($oldTicket['subject'] ?? '') !== $customer_subject) {
            $changes[] = "Subject: [Old: '{$oldTicket['subject']}' -> New: '$customer_subject']";
        }

        $changes_summary = !empty($changes) ? implode(' | ', $changes) : "No major fields modified";
        $log_desc = "Updated Ticket ID: $ticket_id. Changes -> { $changes_summary }";

        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        } elseif (function_exists('logUserActivity')) {
            logUserActivity($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Ticket updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error'
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



/*-------- Add Ticket topic  Script ----------*/
if (isset($_GET['add_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_name = trim($_POST['topic_name']);

    /* Validate Topic Name */
    __validate_input($topic_name, 'Topic Name');

    /* Check if topic name already exists */
    $stmt_check = $con->prepare("SELECT id FROM ticket_topic WHERE topic_name = ? LIMIT 1");
    $stmt_check->bind_param("s", $topic_name);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Topic Name already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /* Insert into table using Prepared Statement */
    $stmt = $con->prepare("INSERT INTO ticket_topic (topic_name) VALUES (?)");
    $stmt->bind_param("s", $topic_name);
    
    if ($stmt->execute()) {
        $topic_id = $stmt->insert_id;
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Added new ticket topic: '$topic_name' (ID: $topic_id)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        } elseif (function_exists('logUserActivity')) {
            logUserActivity($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Topic!',
        ]);
        exit();
    }
}

/*-------- Update ticket topic  Script ----------*/
if (isset($_GET['update_ticket_topic__data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $topic_name = trim($_POST['topic_name']);
    $id = trim($_POST['id']);

    /*----------- Validate ID and Topic Name ----------*/
    __validate_input($id, 'Topic ID');
    __validate_input($topic_name, 'Topic Name');

    /*---------- Fetch old topic name for logging diff/changes ----------*/
    $old_name = "";
    $stmt_old = $con->prepare("SELECT topic_name FROM ticket_topic WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    if ($res_old->num_rows > 0) {
        $row_old = $res_old->fetch_assoc();
        $old_name = $row_old['topic_name'] ?? '';
    }
    $stmt_old->close();

    /*--------- Check if topic name already exists ----------- */
    $stmt_check = $con->prepare("SELECT id FROM ticket_topic WHERE topic_name = ? AND id != ? LIMIT 1");
    $stmt_check->bind_param("si", $topic_name, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Topic Name Already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /*--------- Update the topic in the database using Prepared Statement --------*/
    $stmt = $con->prepare("UPDATE ticket_topic SET topic_name = ? WHERE id = ?");
    $stmt->bind_param("si", $topic_name, $id);

    if ($stmt->execute()) {
        $stmt->close();

        /*----------- Log User Activity with Changes -----------*/
        $log_desc = "Updated ticket topic ID: $id (Name changed from '$old_name' to '$topic_name')";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
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
/*-------------------- Delete Ticket Topic Script --------------------*/
if (isset($_GET['delete_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Fetch topic name before delete for accurate logging -----------*/
    $topic_name = "Unknown";
    $stmt_check = $con->prepare("SELECT topic_name FROM ticket_topic WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $topic_name = $row_check['topic_name'] ?? 'Unknown';
    }
    $stmt_check->close();

    /*----------- Delete from ticket_topic table (Prepared Statement) -----------*/
    $stmt = $con->prepare("DELETE FROM ticket_topic WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted ticket topic ID: $id (Name: '$topic_name')";
        
        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        } elseif (function_exists('logUserActivity')) {
            logUserActivity($con, 'DELETE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete topic!',
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
     
    $ticket_id   = trim($_POST['ticket_id']);
    $ticket_type = trim($_POST['ticket_type']);
    $progress    = trim($_POST['progress']);
    $assign_to   = trim($_POST['assign_to']);
    $comment     = trim($_POST['comment']);

    /* Validate inputs */
    __validate_input($ticket_id, 'Ticket ID');
    __validate_input($comment, 'Comment');
    __validate_input($assign_to, 'Assign To');

    /* Fetch ticket subject for activity logging */
    $ticket_subject = "Unknown";
    $stmt_chk = $con->prepare("SELECT subject FROM ticket WHERE id = ? LIMIT 1");
    $stmt_chk->bind_param("i", $ticket_id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    if ($res_chk->num_rows > 0) {
        $ticket_subject = $res_chk->fetch_assoc()['subject'] ?? 'Unknown';
    }
    $stmt_chk->close();

    /* Insert into ticket_details table using Prepared Statement */
    $stmt_detail = $con->prepare("INSERT INTO ticket_details (tcktid, status, datetm, comments, parcent, assign_user_id) VALUES (?, ?, NOW(), ?, ?, ?)");
    $stmt_detail->bind_param("isssi", $ticket_id, $ticket_type, $comment, $progress, $assign_to);
    $result_detail = $stmt_detail->execute();
    $stmt_detail->close();

    /* Update ticket status, progress, and assign user */
    if ($progress == '100%') {
        $enddate = date('Y-m-d H:i:s');
        $stmt_update = $con->prepare("UPDATE ticket SET ticket_type = ?, parcent = ?, assign_user_id = ?, enddate = ? WHERE id = ?");
        $stmt_update->bind_param("ssisi", $ticket_type, $progress, $assign_to, $enddate, $ticket_id);
    } else {
        $stmt_update = $con->prepare("UPDATE ticket SET ticket_type = ?, parcent = ?, assign_user_id = ? WHERE id = ?");
        $stmt_update->bind_param("ssii", $ticket_type, $progress, $assign_to, $ticket_id);
    }
    $result_update = $stmt_update->execute();
    $stmt_update->close();

    if ($result_detail && $result_update) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Added comment & updated Ticket ID: $ticket_id (Subject: '$ticket_subject', Status: $ticket_type, Progress: $progress)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }

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

/*---------Ticket Reports ---------*/

if (isset($_GET['get_tickets_report_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date   = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

    $errors = [];
    $params = [];
    $where_clauses = ["1=1"]; 

    /* Date validation*/
    if ($start_date !== '') {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
            $where_clauses[] = "DATE(create_date) >= ?";
            $params[] = $start_date;
        } else {
            $errors[] = "Invalid start date format.";
        }
    }

    if ($end_date !== '') {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
            $where_clauses[] = "DATE(create_date) <= ?";
            $params[] = $end_date;
        } else {
            $errors[] = "Invalid end date format.";
        }
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
        exit;
    }

    $where_sql = implode(' AND ', $where_clauses);

    
    $query = "
        SELECT 
            DATE(create_date) as report_date,
            assign_user_id,
            COUNT(*) as total_ticket,
            SUM(CASE WHEN ticket_type='Complete' THEN 1 ELSE 0 END) as completed_ticket,
            SUM(CASE WHEN ticket_type='Active' THEN 1 ELSE 0 END) as active_ticket,
            SUM(CASE WHEN ticket_type='Pending' THEN 1 ELSE 0 END) as pending_ticket
        FROM ticket
        WHERE $where_sql
        GROUP BY DATE(create_date), assign_user_id
        ORDER BY report_date ASC
    ";

    $stmt = $con->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $con->error]);
        exit;
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $rows_html = '';
    if ($result->num_rows > 0) {
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            $asg_id = intval($row['assign_user_id']);
            $asg_name = '---';
            $asgRes = $con->query("SELECT `fullname` as `name` FROM users WHERE id = $asg_id");
            if ($asgRes && $asgRes->num_rows > 0) {
                $asgRow = $asgRes->fetch_assoc();
                $asg_name = $asgRow['name'];
            }

            $view_url = "tickets_report_list.php?date={$row['report_date']}&assign_id={$asg_id}";

            $rows_html .= "<tr>
                <td>{$i}</td>
                <td>" . date('d M Y', strtotime($row['report_date'])) . "</td>
                <td>{$asg_name}</td>
                <td><span class='badge bg-secondary'>{$row['total_ticket']}</span></td>
                <td><span class='badge bg-warning'>{$row['pending_ticket']}</span></td>
                <td><span class='badge bg-danger'>{$row['active_ticket']}</span></td> 
                <td><span class='badge bg-success'>{$row['completed_ticket']}</span></td>
                <td><a href='{$view_url}' class='btn btn-sm btn-primary'><i class='fas fa-eye'></i></a></td>
            </tr>";
            $i++;
        }
    } else {
        $rows_html = '<tr><td colspan="7" class="text-center">No data found</td></tr>';
    }

    $html = '
    <div class="table-responsive">
        <table id="tickets_report_data_table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Assigned</th>
                    <th>Total</th>
                    <th>Pending</th> 
                    <th>Active</th>
                    <th>Completed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>' . $rows_html . '</tbody>
        </table>
    </div>';

    echo json_encode(['success' => true, 'html' => $html]);
    exit;
}
/* ------- Noc Note ----------------*/
if (isset($_GET['add_noc_note']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    /* Validate Name */
    __validate_input($name, 'NOC Note');

    /* Check if NOC Note already exists */
    $stmt_check = $con->prepare("SELECT id FROM noc_note WHERE name = ? LIMIT 1");
    $stmt_check->bind_param("s", $name);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'NOC Note already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /* Insert into table using Prepared Statement */
    $stmt = $con->prepare("INSERT INTO noc_note (`name`) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) {
        $note_id = $stmt->insert_id;
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Added new NOC Note: '$name' (ID: $note_id)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add NOC Note!',
        ]);
        exit();
    }
}

/*-------------- Update NOC Note --------------------------*/
if (isset($_GET['update_noc_note']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id   = trim($_POST['id']);
    $name = trim($_POST['name']);

    /* Validate ID and Name */
    __validate_input($id, 'NOC Note ID');
    __validate_input($name, 'NOC Note');

    /* Fetch old name for logging diff/changes */
    $old_name = "";
    $stmt_old = $con->prepare("SELECT name FROM noc_note WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    if ($res_old->num_rows > 0) {
        $row_old = $res_old->fetch_assoc();
        $old_name = $row_old['name'] ?? '';
    }
    $stmt_old->close();

    /* Check if note already exists (excluding current ID) */
    $stmt_check = $con->prepare("SELECT id FROM noc_note WHERE name = ? AND id != ? LIMIT 1");
    $stmt_check->bind_param("si", $name, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Note Already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /* Update the service in the database using Prepared Statement */
    $stmt = $con->prepare("UPDATE noc_note SET `name` = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        $stmt->close();

        /*----------- Log User Activity with Changes -----------*/
        $log_desc = "Updated NOC Note ID: $id (Name changed from '$old_name' to '$name')";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update NOC Note!',
        ]);
        exit();
    }
}

if (isset($_GET['get_noc_note']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM noc_note WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM noc_note");
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
/*-------------------- Delete NOC Note Script --------------------*/
if (isset($_GET['delete_noc_note_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Fetch NOC Note name before delete for accurate logging -----------*/
    $note_name = "Unknown";
    $stmt_check = $con->prepare("SELECT name FROM noc_note WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $note_name = $row_check['name'] ?? 'Unknown';
    }
    $stmt_check->close();

    /*----------- Delete from noc_note table (Prepared Statement) -----------*/
    $stmt = $con->prepare("DELETE FROM noc_note WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted NOC Note ID: $id (Name: '$note_name')";
        
        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        } elseif (function_exists('logUserActivity')) {
            logUserActivity($con, 'DELETE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete NOC Note!',
        ]);
        exit();
    }
}

/*----------- Add Internal Ticket Data------------------*/
if (isset($_GET['add_internal_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $category_id          = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
    $sub_category_id      = isset($_POST['sub_category_id']) ? trim($_POST['sub_category_id']) : '';
    $pop_id               = isset($_POST['pop_branch']) ? trim($_POST['pop_branch']) : '';
    $upstream_id          = isset($_POST['upstream_id']) ? trim($_POST['upstream_id']) : '';
    $ticket_severity      = isset($_POST['ticket_severity']) ? trim($_POST['ticket_severity']) : '';
    $subject              = isset($_POST['customer_subject']) ? trim($_POST['customer_subject']) : '';
    $customer_description = isset($_POST['customer_description']) ? trim($_POST['customer_description']) : '';
    $customer_ids         = isset($_POST['customer_id']) ? $_POST['customer_id'] : [];
    $status               = 'Active';
    $ticket_no            = 'INT-' . time();

    /* ---------- Validation ---------- */
    if (empty((int)$category_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Category is required.'
        ]);
        exit();
    }

    if (empty((int)$sub_category_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Sub Category is required.'
        ]);
        exit();
    }

    if (empty($ticket_severity)) {
        echo json_encode([
            'success' => false,
            'message' => 'Severity is required.'
        ]);
        exit();
    }

    if (empty($subject)) {
        echo json_encode([
            'success' => false,
            'message' => 'Subject is required.'
        ]);
        exit();
    }

    /*------------- Upload Ticket File -------------*/
    $attachment = function_exists('__upload_file') ? __upload_file($_FILES['customer_attachments'] ?? null) : null;

    $created_by = (int)($_SESSION['uid'] ?? 0);
    $startDate  = date('Y-m-d H:i:s');

    mysqli_begin_transaction($con);

    try {
        /* If specific customers are selected, insert linked customer tickets */
        if (!empty($customer_ids) && is_array($customer_ids)) {
            $stmt_linked = $con->prepare("
                INSERT INTO ticket (
                    customer_id, ticket_type, assign_user_id, ticketfor, pop_id, 
                    complain_type, startdate, enddate, parcent, priority, 
                    customer_note, noc_note, subject, description, attachments, create_date
                ) VALUES (?, 'Active', ?, 'Bandwidth Client', ?, ?, ?, NULL, '0%', '3', 'Auto generated from internal ticket', NULL, ?, ?, ?, NOW())
            ");

            foreach ($customer_ids as $customer_id) {
                $cust_id_int = (int)$customer_id;
                $stmt_linked->bind_param("iisissss", $cust_id_int, $created_by, $pop_id, $sub_category_id, $startDate, $subject, $customer_description, $attachment);
                $stmt_linked->execute();
            }
            $stmt_linked->close();
        }

        /* ---------- Insert Internal Ticket ---------- */
        $stmt_internal = $con->prepare("
            INSERT INTO internal_tickets (
                ticket_no, category_id, subcategory_id, pop_id, upstream_id, 
                severity, status, subject, description, attachment, opened_at, created_at, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, 'Active', ?, ?, ?, NOW(), NOW(), ?)
        ");
        
        $stmt_internal->bind_param(
            "siiisssssi",
            $ticket_no,
            $category_id,
            $sub_category_id,
            $pop_id,
            $upstream_id,
            $ticket_severity,
            $subject,
            $customer_description,
            $attachment,
            $created_by
        );
        
        $stmt_internal->execute();
        $internal_ticket_id = $stmt_internal->insert_id;
        $stmt_internal->close();

        mysqli_commit($con);

        /*----------- Log User Activity -----------*/
        $log_desc = "Created new internal ticket: '$ticket_no' (Subject: '$subject', Severity: $ticket_severity, ID: $internal_ticket_id)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Internal ticket created successfully.'
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $e->getMessage()
        ]);
    }

    exit;
}

/*----------- Update Internal Ticket Data ------------------*/
if (isset($_GET['update_internal_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
     
    $ticket_id              = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
    $category_id            = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
    $sub_category_id        = isset($_POST['sub_category_id']) ? trim($_POST['sub_category_id']) : '';
    $pop_branch             = isset($_POST['pop_branch']) ? trim($_POST['pop_branch']) : '';
    $ticket_severity        = isset($_POST['ticket_severity']) ? trim($_POST['ticket_severity']) : '';
    $subject                = isset($_POST['customer_subject']) ? trim($_POST['customer_subject']) : '';
    $customer_description   = isset($_POST['customer_description']) ? trim($_POST['customer_description']) : '';
    $status                 = isset($_POST['ticket_status']) ? trim($_POST['ticket_status']) : 'open';

    /* ---------- Validation ---------- */
    if ($ticket_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Ticket ID is required.'
        ]);
        exit();
    }

    if (empty((int)$category_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Category is required.'
        ]);
        exit();
    }

    if (empty((int)$sub_category_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Sub Category is required.'
        ]);
        exit();
    }

    /*------------- Fetch Old Ticket Data for Diff Log -------------*/
    $stmt_old = $con->prepare("SELECT * FROM internal_tickets WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $ticket_id);
    $stmt_old->execute();
    $oldTicket = $stmt_old->get_result()->fetch_assoc();
    $stmt_old->close();

    if (!$oldTicket) {
        echo json_encode(['success' => false, 'message' => 'Internal Ticket not found']);
        exit();
    }

    /*------------- Upload Ticket File -------------*/
    $attachment = $oldTicket['attachment'] ?? null;
    if (!empty($_FILES['customer_attachments']['name'])) {
        $uploaded_file = function_exists('__upload_file') ? __upload_file($_FILES['customer_attachments'], $attachment) : null;
        if (!empty($uploaded_file)) {
            $attachment = $uploaded_file;
        }
    }

    $updated_by = (int)($_SESSION['uid'] ?? 0);

    /* ---------- Close time & downtime calculation ---------- */
    if ($status == 'Complete' && $oldTicket['status'] != 'Complete') {
        $stmt_update = $con->prepare("
            UPDATE internal_tickets SET
                category_id = ?, subcategory_id = ?, pop_id = ?, severity = ?, 
                status = ?, subject = ?, description = ?, attachment = ?, 
                updated_by = ?, updated_at = NOW(), closed_at = NOW(), 
                downtime_minutes = TIMESTAMPDIFF(MINUTE, opened_at, NOW())
            WHERE id = ?
        ");
        $stmt_update->bind_param("siisssssii", $category_id, $sub_category_id, $pop_branch, $ticket_severity, $status, $subject, $customer_description, $attachment, $updated_by, $ticket_id);
    } else {
        $stmt_update = $con->prepare("
            UPDATE internal_tickets SET
                category_id = ?, subcategory_id = ?, pop_id = ?, severity = ?, 
                status = ?, subject = ?, description = ?, attachment = ?, 
                updated_by = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt_update->bind_param("siisssssii", $category_id, $sub_category_id, $pop_branch, $ticket_severity, $status, $subject, $customer_description, $attachment, $updated_by, $ticket_id);
    }

    $result = $stmt_update->execute();
    $stmt_update->close();

    if ($result) {
        /*----------- Track Changes (Diff Calculation) -----------*/
        $changes = [];
        if (($oldTicket['subject'] ?? '') !== $subject) $changes[] = "Subject: [Old: '{$oldTicket['subject']}' -> New: '$subject']";
        if (($oldTicket['status'] ?? '') !== $status) $changes[] = "Status: [Old: '{$oldTicket['status']}' -> New: '$status']";
        if (($oldTicket['severity'] ?? '') !== $ticket_severity) $changes[] = "Severity: [Old: '{$oldTicket['severity']}' -> New: '$ticket_severity']";

        $changes_summary = !empty($changes) ? implode(' | ', $changes) : "No major fields modified";
        $log_desc = "Updated Internal Ticket ID: $ticket_id ('{$oldTicket['ticket_no']}'). Changes -> { $changes_summary }";

        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Ticket updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $con->error
        ]);
    }

    exit;
}

/*---------Update Internal Tickets Assign Team------------*/
if(isset($_GET['update_assigned_team']) && $_SERVER['REQUEST_METHOD'] == 'POST'){

    $ticket_id      = (int)($_POST['ticket_id'] ?? 0);
    $assigned_team  = trim($_POST['assigned_team'] ?? '');
    $updated_by     = (int)($_SESSION['uid'] ?? 0);

    if ($ticket_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Ticket ID']);
        exit;
    }

    $stmt = $con->prepare("UPDATE internal_tickets SET assigned_team = ?, updated_by = ? WHERE id = ?");
    $stmt->bind_param("sii", $assigned_team, $updated_by, $ticket_id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        $log_desc = "Updated Assigned Team for Internal Ticket ID: $ticket_id to '$assigned_team'";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }
    }

    echo json_encode([
        'success' => $result ? true : false,
        'message' => $result ? 'Assigned team updated successfully' : 'Database error'
    ]);
    exit;
}

/*---------Update Internal Tickets Status------------*/
if (isset($_GET['update_ticket_status']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $ticket_id  = (int)($_POST['ticket_id'] ?? 0);
    $status     = trim($_POST['status'] ?? '');
    $updated_by = (int)($_SESSION['uid'] ?? 0);
    $rca_note   = isset($_POST['rca_note']) ? trim($_POST['rca_note']) : '';

    if ($ticket_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Ticket ID']);
        exit;
    }

    if ($status == 'Complete' && empty($rca_note)) {
        echo json_encode([
            'success' => false,
            'message' => 'RCA note is required to close ticket'
        ]);
        exit;
    }

    mysqli_begin_transaction($con);

    try {
        if ($status == 'Complete') {
            $stmt_pop = $con->prepare("SELECT pop_id, ticket_no FROM internal_tickets WHERE id = ? LIMIT 1");
            $stmt_pop->bind_param("i", $ticket_id);
            $stmt_pop->execute();
            $ticket_row = $stmt_pop->get_result()->fetch_assoc();
            $stmt_pop->close();

            $get_pop_id = (int)($ticket_row['pop_id'] ?? 0);
            $ticket_num = $ticket_row['ticket_no'] ?? '';
            $enddate    = date('Y-m-d H:i:s');

            /*----- linked customer tickets Complete -------*/
            $stmt_linked_complete = $con->prepare("
                UPDATE ticket 
                SET ticket_type = 'Complete', parcent = '100%', enddate = ?, noc_note = ? 
                WHERE pop_id = ? AND ticket_type = 'Active'
            ");
            $stmt_linked_complete->bind_param("ssi", $enddate, $rca_note, $get_pop_id);
            $stmt_linked_complete->execute();
            $stmt_linked_complete->close();

            $stmt_update_internal = $con->prepare("
                UPDATE internal_tickets 
                SET status = ?, updated_by = ?, rca_note = ?, closed_at = NOW(), 
                    downtime_minutes = TIMESTAMPDIFF(MINUTE, opened_at, NOW()) 
                WHERE id = ?
            ");
            $stmt_update_internal->bind_param("sisi", $status, $updated_by, $rca_note, $ticket_id);
        } else {
            $stmt_update_internal = $con->prepare("UPDATE internal_tickets SET status = ?, updated_by = ? WHERE id = ?");
            $stmt_update_internal->bind_param("sii", $status, $updated_by, $ticket_id);
        }

        $stmt_update_internal->execute();
        $stmt_update_internal->close();

        mysqli_commit($con);

        /*----------- Log User Activity -----------*/
        $log_desc = "Updated Internal Ticket ID: $ticket_id status to '$status'";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        } 

        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

/*----------- Add NOC Incident Ticket Data------------------*/
if (isset($_GET['add_noc_incident_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $incident_summary = isset($_POST['incident_summary']) ? trim($_POST['incident_summary']) : '';
    $status           = isset($_POST['status']) ? trim($_POST['status']) : 'Active';
    
    if (empty($incident_summary)) {
        echo json_encode([
            'success' => false,
            'message' => 'Incident Summary is required.'
        ]);
        exit();
    }

    /* ---------- Insert NOC Incident Ticket ---------- */
    $stmt = $con->prepare("INSERT INTO noc_incident (incident_summary, status, create_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $incident_summary, $status);
    $result = $stmt->execute();
    $incident_id = $stmt->insert_id;
    $stmt->close();

    if ($result) {
        $log_desc = "Created new NOC Incident Ticket ID: $incident_id (Summary: '$incident_summary')";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Incident Ticket Created successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $con->error
        ]);
    }

    exit;
}

/*----------- Update NOC Incident Ticket Data ------------------*/
if (isset($_GET['update_noc_incident_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id               = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
    $incident_summary = isset($_POST['incident_summary']) ? trim($_POST['incident_summary']) : '';
    $status           = isset($_POST['status']) ? trim($_POST['status']) : 'Active';

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Incident Ticket ID.'
        ]);
        exit();
    }

    if (empty($incident_summary)) {
        echo json_encode([
            'success' => false,
            'message' => 'Incident Summary is required.'
        ]);
        exit();
    }

    /* ---------- Update NOC Incident Ticket ---------- */
    $stmt = $con->prepare("UPDATE noc_incident SET incident_summary = ?, status = ? WHERE id = ? LIMIT 1");
    $stmt->bind_param("ssi", $incident_summary, $status, $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        $log_desc = "Updated NOC Incident Ticket ID: $id";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Incident Ticket Updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $con->error
        ]);
    }

    exit();
}

/*----------- Delete NOC Incident Ticket Data ------------------*/
if (isset($_GET['delete_noc_incident_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
   
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Incident Ticket ID.'
        ]);
        exit();
    }

    /* Fetch summary for logging before delete */
    $summary = "Unknown";
    $stmt_chk = $con->prepare("SELECT incident_summary FROM noc_incident WHERE id = ? LIMIT 1");
    $stmt_chk->bind_param("i", $id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    if ($res_chk->num_rows > 0) {
        $summary = $res_chk->fetch_assoc()['incident_summary'] ?? 'Unknown';
    }
    $stmt_chk->close();

    /* ---------- Delete NOC Incident Ticket ---------- */
    $stmt = $con->prepare("DELETE FROM noc_incident WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        $log_desc = "Deleted NOC Incident Ticket ID: $id (Summary: '$summary')";
        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Incident Ticket Deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $con->error
        ]);
    }

    exit();
}

/*----------- Add Client Visit Ticket Data ------------------*/
if (isset($_GET['add_client_visit_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $assign_user_id = isset($_POST['assign_user_id']) ? intval($_POST['assign_user_id']) : 0;
    $title          = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description    = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status         = isset($_POST['status']) ? trim($_POST['status']) : '';
    $create_date    = isset($_POST['create_date']) ? trim($_POST['create_date']) : '';
    
    if (empty($status)) {
        $status = 'Active';
    }

    if (!empty($create_date)) {
        $create_date = date('Y-m-d H:i:s', strtotime($create_date));
    } else {
        $create_date = date('Y-m-d H:i:s');
    }

    /* ---------- Validation ---------- */
    if (empty($title)) {
        echo json_encode([
            'success' => false,
            'message' => 'Title is required.'
        ]);
        exit();
    }

    /* ---------- Prepared Statement Insert Query ---------- */
    $stmt = $con->prepare("INSERT INTO client_visit (assign_user_id, title, description, status, create_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $assign_user_id, $title, $description, $status, $create_date);
    
    $result = $stmt->execute();
    $visit_id = $stmt->insert_id;
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Created new Client Visit Ticket ID: $visit_id (Title: '$title', Status: $status)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Client Visit Created successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $con->error
        ]);
    }

    exit;
}

/*----------- Update Client Visit Ticket Data ------------------*/
if (isset($_GET['update_client_visit_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id             = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $assign_user_id = isset($_POST['assign_user_id']) ? intval($_POST['assign_user_id']) : 0;
    $title          = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description    = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status         = isset($_POST['status']) ? trim($_POST['status']) : 'Active';
    $create_date    = isset($_POST['create_date']) ? trim($_POST['create_date']) : '';

    /* ---------- Validation ---------- */
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Record ID.'
        ]);
        exit();
    }

    if (empty($title)) {
        echo json_encode([
            'success' => false,
            'message' => 'Title is required.'
        ]);
        exit();
    }

    if (!empty($create_date)) {
        $create_date = date('Y-m-d H:i:s', strtotime($create_date));
    } else {
        $create_date = date('Y-m-d H:i:s');
    }

    /* ---------- Fetch Old Record for Diff Log ---------- */
    $stmt_old = $con->prepare("SELECT * FROM client_visit WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $oldVisit = $stmt_old->get_result()->fetch_assoc();
    $stmt_old->close();

    if (!$oldVisit) {
        echo json_encode(['success' => false, 'message' => 'Client Visit record not found']);
        exit();
    }

    /* ---------- Prepared Statement Update Query ---------- */
    $stmt = $con->prepare("UPDATE client_visit 
                           SET assign_user_id = ?, title = ?, description = ?, status = ?, create_date = ? 
                           WHERE id = ?");
    
    $stmt->bind_param("issssi", $assign_user_id, $title, $description, $status, $create_date, $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Track Changes (Diff Calculation) -----------*/
        $changes = [];
        if (($oldVisit['title'] ?? '') !== $title) $changes[] = "Title: [Old: '{$oldVisit['title']}' -> New: '$title']";
        if (($oldVisit['status'] ?? '') !== $status) $changes[] = "Status: [Old: '{$oldVisit['status']}' -> New: '$status']";
        if (($oldVisit['assign_user_id'] ?? 0) != $assign_user_id) $changes[] = "Assign User ID: [Old: '{$oldVisit['assign_user_id']}' -> New: '$assign_user_id']";

        $changes_summary = !empty($changes) ? implode(' | ', $changes) : "No major fields modified";
        $log_desc = "Updated Client Visit ID: $id. Changes -> { $changes_summary }";

        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Client Visit updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: Update failed'
        ]);
    }

    exit();
}

/* ---------- Delete Client Visit Ticket Data ---------- */
if (isset($_GET['delete_client_visit_tickets_data']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* ---------- Validation ---------- */
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Record ID.'
        ]);
        exit();
    }

    /* ---------- Fetch Title Before Delete for Logging ---------- */
    $visit_title = "Unknown";
    $stmt_chk = $con->prepare("SELECT title FROM client_visit WHERE id = ? LIMIT 1");
    $stmt_chk->bind_param("i", $id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    if ($res_chk->num_rows > 0) {
        $visit_title = $res_chk->fetch_assoc()['title'] ?? 'Unknown';
    }
    $stmt_chk->close();

    /* ---------- Prepared Statement Delete Query ---------- */
    $stmt = $con->prepare("DELETE FROM client_visit WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted Client Visit ID: $id (Title: '$visit_title')";
        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Client Visit deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: Delete failed'
        ]);
    }

    exit();
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




function __upload_file($file, $oldFile = null)
{
    
    if (!isset($file) || $file['error'] !== 0) {
        return $oldFile;
    }

    $allowed = ['jpg','jpeg','png','pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return $oldFile;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return $oldFile;
    }

    $newName = uniqid('ticket_', true) . '.' . $ext;
    $destination = '../assets/tickets/' . $newName;
    move_uploaded_file($file['tmp_name'], $destination);

    if ($oldFile && file_exists('../assets/tickets/'.$oldFile)) {
        unlink('../assets/tickets/'.$oldFile);
    }

    return $newName;
}
