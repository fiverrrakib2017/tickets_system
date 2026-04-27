<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';
include 'functions.php';
if (!isset($_SESSION)) {
    session_start();
}

/*----------- Mark Ticket Completed------------------*/
if(isset($_GET['mark_ticket_completed']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $ticket_id = isset($_POST['ticket_id']) ? trim($_POST['ticket_id']) : '';

    /* Validate Ticket ID */
    __validate_input($ticket_id, 'Ticket ID');

    /* Update Ticket Status */
    $end_date = date('Y-m-d H:i:s');
    $update = $con->prepare("UPDATE ticket SET ticket_type='Complete',parcent='100%', enddate=? WHERE id=?");
    $update->bind_param('si', $end_date, $ticket_id);
    $result = $update->execute();

    if($result){
        echo json_encode([
            'success' => true,
            'message'  =>  'Ticket marked as completed successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message'  =>  'Error: ' . $update->error
        ]);
    }

    exit;
}

/*----------- Add Ticket Data------------------*/
if(isset($_GET['add_ticket_data']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
   
    $customer_id            = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    $ticket_for             = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type          = isset($_POST['complain_type']) ? trim($_POST['complain_type']) : '';
    $assign_to              = isset($_POST['assign_to']) ? trim($_POST['assign_to']) : '';
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
        if(empty($assign_to)) {
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

    // if ($stmt->num_rows > 0) {
    //     echo json_encode([
    //         'success' => false,
    //         'message'  =>  'You already have an active ticket.'
    //     ]);
    //     exit();
    // }

    /*-------------Upload Ticket File-------------*/
    $ticket_file = __upload_file($_FILES['customer_attachments'] ?? null);
    

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
            subject,
            description,
            attachments,
            create_date
        )
        VALUES (
            ?, 'Active', ?, ?, ?, ?, ?, NULL, '0%', ?, ?, ?, ?, ?, ?, ?
        )
    ");


    $startDate = date('Y-m-d H:i:s');

    $insert->bind_param(
        'iisssssssssss',
        $customer_id,
        $assign_to,
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
     
    $ticket_id              = (int)($_POST['ticket_id'] ?? 0);
    $customer_id            = (int)($_POST['customer_id'] ?? 0);
    $ticket_for             = trim($_POST['ticket_for'] ?? '');
    $complain_type          = (int)($_POST['complain_type'] ?? 0);
    $assign_to              = (int)($_POST['assign_to'] ?? 0);
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
            customer_id=?,
            ticketfor=?,
            pop_id=?,
            asignto=?,
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

    if ($stmt->execute()) {
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

/******** Ticket Reports ******************/

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
            asignto,
            COUNT(*) as total_ticket,
            SUM(CASE WHEN ticket_type='Complete' THEN 1 ELSE 0 END) as completed_ticket,
            SUM(CASE WHEN ticket_type='Active' THEN 1 ELSE 0 END) as active_ticket,
            SUM(CASE WHEN ticket_type='Pending' THEN 1 ELSE 0 END) as pending_ticket
        FROM ticket
        WHERE $where_sql
        GROUP BY DATE(create_date), asignto
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
            $asg_id = intval($row['asignto']);
            $asg_name = 'N/A';
            $asgRes = $con->query("SELECT `name` FROM ticket_assign WHERE id = $asg_id");
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

    /* Validate  Name */
    __validate_input($name, 'NOC Note');

    
    /* Insert into  table */
    $result = $con->query("INSERT INTO noc_note(`name`) VALUES( '$name')");
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
/*-------------- Update Value Added service--------------------------*/
if (isset($_GET['update_noc_note']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);

    
    /* Validate Service Name */
    __validate_input($name, 'NOC Note');
    /* Check if service already exists */
    $check_service = $con->query("SELECT * FROM noc_note WHERE name='$name' AND id != '$id'");
    if ($check_service->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Note Already exists!',
        ]);
        exit();
    }
    /* Update the service in the database */
    $result = $con->query("UPDATE noc_note SET `name`='$name' WHERE id='$id'");
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
/*Delete customer type Script*/
if (isset($_GET['delete_noc_note_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM noc_note WHERE id='$id'");
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
    $status = 'open';
    $ticket_no = 'INT-' . time();

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
    $attachment = __upload_file($_FILES['customer_attachments'] ?? null);

    /* ---------- Safe variable mapping ---------- */
    $subcategory_id = (int)$sub_category_id;
    $severity = mysqli_real_escape_string($con, $ticket_severity);
    $description = mysqli_real_escape_string($con, $customer_description);
    $subject = mysqli_real_escape_string($con, $subject);
    $created_by = (int)$_SESSION['uid'];
 
    if (!empty($customer_ids)) {

        foreach ($customer_ids as $customer_id) {

            $customer_id = (int)$customer_id;

            $con->query("
                INSERT INTO ticket (
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
                    subject,
                    description,
                    attachments,
                    create_date
                ) VALUES (
                    '$customer_id',
                    'Active',
                     NULL,
                    'Bandwidth Client',
                    '$pop_id',
                    '$sub_category_id',
                    NOW(),
                    NULL,
                    '0',
                    '3',
                    'Auto generated from internal ticket',
                    NULL,
                    '$subject',
                    '$customer_description',
                    '$attachment',
                    NOW()
                )
            ");
        }
    }

    /* ---------- Insert Ticket ---------- */
    $result = $con->query("
        INSERT INTO internal_tickets
        (
            ticket_no,
            category_id,
            subcategory_id,
            pop_id,
            upstream_id,
            severity,
            status,
            subject,
            description,
            attachment,
            opened_at,
            created_at,
            created_by
        )
        VALUES
        (
            '$ticket_no',
            '$category_id',
            '$subcategory_id',
            '$pop_id',
            '$upstream_id',
            '$severity',
            '$status',
            '$subject',
            '$description',
            '$attachment',
            NOW(),
            NOW(),
            '$created_by'
        )
    ");

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Internal ticket created successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $con->error
        ]);
    }

    exit;
}
/*----------- Update Internal Ticket Data ------------------*/
if (isset($_GET['update_internal_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
      
    $ticket_id             = isset($_POST['ticket_id']) ? trim($_POST['ticket_id']) : '';
    $category_id           = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
    $sub_category_id       = isset($_POST['sub_category_id']) ? trim($_POST['sub_category_id']) : '';
    $pop_branch            = isset($_POST['pop_branch']) ? trim($_POST['pop_branch']) : '';
    $ticket_severity       = isset($_POST['ticket_severity']) ? trim($_POST['ticket_severity']) : '';
    $subject               = isset($_POST['customer_subject']) ? trim($_POST['customer_subject']) : '';
    $customer_description  = isset($_POST['customer_description']) ? trim($_POST['customer_description']) : '';
    $status                = isset($_POST['ticket_status']) ? trim($_POST['ticket_status']) : 'open';

    /* ---------- Validation ---------- */
    if (empty((int)$ticket_id)) {
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

    /*------------- Upload Ticket File -------------*/
    $attachment_sql = '';

    if (!empty($_FILES['customer_attachments']['name'])) {
        $attachment = __upload_file($_FILES['customer_attachments']);

        if (!empty($attachment)) {
            $attachment_sql = ", attachment = '$attachment'";
        }
    }

    $updated_by = (int) $_SESSION['uid'];

    /* ---------- Close time & downtime calculation ---------- */
    $extra_status_sql = '';

    if ($status == 'closed') {
        $extra_status_sql = ",
            closed_at = NOW(),
            downtime_minutes = TIMESTAMPDIFF(MINUTE, opened_at, NOW())
        ";
    }

    /* ---------- Update Query ---------- */
    $result = $con->query("
        UPDATE internal_tickets SET
            category_id = '$category_id',
            subcategory_id = '$sub_category_id',
            pop_id = '$pop_branch',
            severity = '$ticket_severity',
            status = '$status',
            subject = '$subject',
            description = '$customer_description',
            updated_by = '$updated_by',
            updated_at = NOW()
            $attachment_sql
            $extra_status_sql
        WHERE id = '$ticket_id'
    ");

    if ($result) {
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

if(isset($_GET['update_assigned_team']) && $_SERVER['REQUEST_METHOD'] == 'POST'){

    $ticket_id      = (int) $_POST['ticket_id'];
    $assigned_team  = trim($_POST['assigned_team']);
    $updated_by     = (int) $_SESSION['uid'];

    $result = $con->query("
        UPDATE internal_tickets
        SET assigned_team = '$assigned_team',
        updated_by = '$updated_by'
        WHERE id = '$ticket_id'
    ");

    echo json_encode([
        'success' => $result ? true : false,
        'message' => $result ? 'Assigned team updated successfully' : $con->error
    ]);
    exit;
}
if (isset($_GET['update_ticket_status']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $ticket_id  = (int) $_POST['ticket_id'];
    $status     = trim($_POST['status']);
    $updated_by = (int) $_SESSION['uid'];
    $rca_note   = isset($_POST['rca_note']) ? trim($_POST['rca_note']) : '';
    $safe_rca_note = mysqli_real_escape_string($con, $rca_note);
    $extra_sql  = '';

    if ($status == 'closed' && empty($rca_note)) {
        echo json_encode([
            'success' => false,
            'message' => 'RCA note is required to close ticket'
        ]);
        exit;
    }

    mysqli_begin_transaction($con);

    try {

        if ($status == 'closed') {

            $ticket_data = $con->query("
                SELECT pop_id 
                FROM internal_tickets 
                WHERE id = $ticket_id
            ");

            $ticket_row = $ticket_data->fetch_assoc();
            $get_pop_id = (int)$ticket_row['pop_id'];

            $enddate = date('Y-m-d H:i:s');

            /* linked customer tickets resolved */
            $update_ticket = $con->query("
                UPDATE ticket 
                SET 
                    ticket_type = 'Complete',
                    parcent     = '100%',
                    enddate     = '$enddate',
                    noc_note    = '$safe_rca_note'
                WHERE pop_id    = $get_pop_id
                AND ticket_type = 'Active'
            ");

            if (!$update_ticket) {
                throw new Exception($con->error);
            }

            $extra_sql = ",
                rca_note = '$safe_rca_note',
                closed_at = NOW(),
                downtime_minutes = TIMESTAMPDIFF(MINUTE, opened_at, NOW())
            ";
        }

        $result = $con->query("
            UPDATE internal_tickets
            SET 
                status = '$status',
                updated_by = '$updated_by'
                $extra_sql
            WHERE id = '$ticket_id'
        ");

        if (!$result) {
            throw new Exception($con->error);
        }

        mysqli_commit($con);

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
