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

/*------------update_mikrotik_info--------------*/
if(isset($_GET['update_mikrotik_info']) && $_SERVER['REQUEST_METHOD']==='POST'){
    $input = json_decode(file_get_contents('php://input'), true);

    $customer_id = isset($input['customer_id']) ? (int)$input['customer_id'] : 0;
    $mikrotik_ip = isset($input['mikrotik_ip']) ? trim($input['mikrotik_ip']) : '';
    $mikrotik_port = isset($input['mikrotik_port']) ? trim($input['mikrotik_port']) : '';

    if($customer_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Customer ID.'
        ]);
        exit();
    }

    /*-----------Update Customer Mikrotik Info----------*/ 
    $stmt = $con->prepare("UPDATE customers SET ping_ip = ?, port = ? WHERE id = ?");
    $stmt->bind_param('ssi', $mikrotik_ip, $mikrotik_port, $customer_id);

    if($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Customer Mikrotik Info Updated Successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: '.$stmt->error
        ]);
    }

    exit();
   
}

/*-----GET Customer POP ID-----*/
if(isset($_GET['get_customer_pop_id']) && $_SERVER['REQUEST_METHOD']==='GET'){
    $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

    if($customer_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Customer ID.'
        ]);
        exit();
    }

    $stmt = $con->prepare("SELECT `pop_id` FROM customers WHERE id = ?");
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'pop_id' => $row['pop_id']
        ]);
        exit; 
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: '.$stmt->error
        ]);
        exit; 
    }

    exit;
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
            SELECT 
                c.id,
                c.customer_name,
                c.customer_email,
                c.ping_ip,
                c.ping_ip_status,
                GROUP_CONCAT(cp.phone_number SEPARATOR ', ') AS phones
            FROM customers c
            LEFT JOIN customer_invoice ci ON ci.customer_id = c.id
            LEFT JOIN customer_service s ON s.id = ci.service_id
            LEFT JOIN customer_phones cp ON cp.customer_id = c.id
            WHERE 
                c.customer_name LIKE '%$search%'
                OR c.customer_email LIKE '%$search%'
                OR c.ping_ip LIKE '%$search%'
                OR s.name LIKE '%$search%'
                OR cp.phone_number LIKE '%$search%'
            GROUP BY 
                c.id, c.customer_name, c.customer_email, c.ping_ip, c.ping_ip_status
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
    $customer_username      = trim($_POST['customer_username']);
    $customer_password      = trim($_POST['customer_password']);
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

    /*-------- Validate Customer Name ---------*/
    __validate_input($customer_name, 'Customer Name');
    /*-------- Validate Username Name ---------*/
    __validate_input($customer_username, 'Customer Username');
    /*-------- Validate Password Name ---------*/
    __validate_input($customer_password, 'Customer Password');

    /* -------- Check username exists -------- */
    if (is_unique_column($con, 'users', 'username', $customer_username) === true) {
        exit(json_encode(['success'=>false,'message'=>'Username already exists']));
    }
    /* get  customer total */
    $total_limit = 0;
    if(isset($_POST['limit']) && is_array($_POST['limit'])){
        foreach($_POST['limit'] as $lim){
            $total_limit += (int)$lim;
        }
    }
    /*-------------NID PDF File Upload ------------- */
    $nid_file = __upload_file($_FILES['nid_file'] ?? null);
    /*-------------Service Agreement File Upload ------------- */
    $service_agreement_file = __upload_file($_FILES['service_agreement_file'] ?? null);

    /* Insert into  table */
    $result = $con->query("INSERT INTO customers(`customer_name`,`customer_email`,`username`,`password`,`pop_id`,`customer_type_id`,`customer_vlan`,`private_customer_ip`,`service_type`,`status`,`total`,`nid_file`,`service_agreement_file`,`service_customer_type`) VALUES('$customer_name','$customer_email','$customer_username','$customer_password','$customer_pop_branch','$customer_type','$customer_vlan','$private_customer_ip','$service_type','$customer_status','$total_limit','$nid_file','$service_agreement_file','$service_customer_type')");

    $get_customer_id=$con->insert_id;

    /*------------- Bandwidth Service------------- */
    if($_POST['service_customer_type'] == 1){
        if(!save_bandwidth_service( $con,  $get_customer_id, $_POST['service_id'] ?? [],$_POST['limit'] ?? [])){
            rollback_customer($con, $get_customer_id, 'Service mismatch');
        }
    }
   
    /*------------- Mac Reseller Service------------- */
    if($_POST['service_customer_type'] == 2){
        if(!save_mac_reseller_service(
            $con,
            $get_customer_id,
            $_POST['service_mac_reseller_package'],
            $_POST['service_mac_reseller_customer_count']
        )){
            rollback_customer($con, $get_customer_id, 'Mac reseller mismatch');
        }
    }
    /*-------------Insert Phone Numbers-------------*/
    if(isset($_POST['customer_phones']) && is_array($_POST['customer_phones'])){
        save_customer_phones($con, $get_customer_id, $_POST['customer_phones']);
    }
    if(isset($_POST['customer_public_ip']) && is_array($_POST['customer_public_ip'])){
        save_customer_public_ip($con, $get_customer_id, $_POST['customer_public_ip']);
    }

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Added new customer: '$customer_name' (Username: $customer_username, ID: $get_customer_id)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }
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
/*-------------------- Update customer data Script ----------------------*/
if (isset($_GET['update_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id            = trim($_POST['customer_id']);
    $customer_name          = trim($_POST['customer_name']);
    $customer_email         = trim($_POST['customer_email']);
    $customer_username      = trim($_POST['customer_username']);
    $customer_password      = trim($_POST['customer_password']);
    $customer_type          = trim($_POST['customer_type']);
    $customer_pop_branch    = trim($_POST['customer_pop_branch']);
    $customer_vlan          = trim($_POST['customer_vlan']);
    $private_customer_ip    = trim($_POST['private_customer_ip']);
    $service_type           = isset($_POST['service_type']) ? trim($_POST['service_type']) : null; 
    $customer_status        = trim($_POST['customer_status']);
    $service_customer_type  = trim($_POST['service_customer_type']);

    /*-------- Validate Inputs ---------*/
    __validate_input($customer_name, 'Customer Name');
    __validate_input($customer_username, 'Customer Username');
    __validate_input($customer_password, 'Customer Password');
    
    /* -------- Check Existing User -------- */
    $old = $con->prepare("SELECT * FROM customers WHERE id = ? LIMIT 1");
    $old->bind_param("i", $customer_id);
    $old->execute();
    $oldUser = $old->get_result()->fetch_assoc();

    if (!$oldUser) {
        exit(json_encode(['success' => false, 'message' => 'User not found']));
    }

    /* -------- Duplicate Username Check -------- */
    if ($customer_username !== ($oldUser['username'] ?? '')) {
        if (is_unique_column($con, 'customers', 'username', $customer_username)) {
            exit(json_encode(['success' => false, 'message' => 'Username already exists']));
        }
    }
    
    /* ---------- Get old attachment ---------- */
    $old_nid_File = $oldUser['nid_file'] ?? '';
    $old_service_agreement_file = $oldUser['service_agreement_file'] ?? '';
    
    /* ---------- Upload file ---------- */
    if (isset($_FILES['nid_file']) && $_FILES['nid_file']['error'] === 0) {
        $nid_file = __upload_file($_FILES['nid_file'], $old_nid_File);
    } else {
        $nid_file = $old_nid_File;
    }

    if (isset($_FILES['service_agreement_file']) && $_FILES['service_agreement_file']['error'] === 0) {
        $service_agreement_file = __upload_file($_FILES['service_agreement_file'], $old_service_agreement_file);
    } else {
        $service_agreement_file = $old_service_agreement_file;
    }

    /* get customer total */
    $total_limit = 0;
    if (isset($_POST['limit']) && is_array($_POST['limit'])) {
        foreach ($_POST['limit'] as $lim) {
            $total_limit += (int)$lim;
        }
    }

    /* Update table */
    $result = $con->query("UPDATE customers SET `customer_name`='$customer_name',`customer_email`='$customer_email',`username`='$customer_username',`password`='$customer_password',`pop_id`='$customer_pop_branch',`customer_type_id`='$customer_type',`customer_vlan`='$customer_vlan',`private_customer_ip`='$private_customer_ip',`service_type`='$service_type',`status`='$customer_status',`total`='$total_limit', service_customer_type='$service_customer_type', `nid_file`='$nid_file', `service_agreement_file`='$service_agreement_file' WHERE id='$customer_id'");
    
    /*------------- Bandwidth Service------------- */
    if ($_POST['service_customer_type'] == 1) {
        if (!save_bandwidth_service($con, $customer_id, $_POST['service_id'] ?? [], $_POST['limit'] ?? [])) {
            rollback_customer($con, $customer_id, 'Service mismatch');
        }
    }
   
    /*------------- Mac Reseller Service------------- */
    if ($_POST['service_customer_type'] == 2) {
        if (!save_mac_reseller_service($con, $customer_id, $_POST['service_mac_reseller_package'] ?? [], $_POST['service_mac_reseller_customer_count'] ?? [])) {
            rollback_customer($con, $customer_id, 'Mac reseller mismatch');
        }
    }

    /*-------------Update Phone Numbers-------------*/
    if (isset($_POST['customer_phones']) && is_array($_POST['customer_phones'])) {
        save_customer_phones($con, $customer_id, $_POST['customer_phones']);
    }
    if (isset($_POST['customer_public_ip']) && is_array($_POST['customer_public_ip'])) {
        save_customer_public_ip($con, $customer_id, $_POST['customer_public_ip']);
    }

    if ($result) {
        /*----------- Safe Professional Old vs New Diff Activity Log -----------*/
        $changes = [];
        
        $old_name = $oldUser['customer_name'] ?? '';
        if ($old_name !== $customer_name) {
            $changes[] = "Name: [Old: '$old_name' -> New: '$customer_name']";
        }

        $old_uname = $oldUser['username'] ?? '';
        if ($old_uname !== $customer_username) {
            $changes[] = "Username: [Old: '$old_uname' -> New: '$customer_username']";
        }

        $old_email = $oldUser['customer_email'] ?? '';
        if ($old_email !== $customer_email) {
            $changes[] = "Email: [Old: '$old_email' -> New: '$customer_email']";
        }

        $old_pop = $oldUser['pop_id'] ?? '';
        if ($old_pop !== $customer_pop_branch) {
            $changes[] = "POP Branch ID: [Old: '$old_pop' -> New: '$customer_pop_branch']";
        }

        $old_type = $oldUser['customer_type_id'] ?? '';
        if ($old_type !== $customer_type) {
            $changes[] = "Customer Type ID: [Old: '$old_type' -> New: '$customer_type']";
        }

        $old_vlan = $oldUser['customer_vlan'] ?? '';
        if ($old_vlan !== $customer_vlan) {
            $changes[] = "VLAN: [Old: '$old_vlan' -> New: '$customer_vlan']";
        }

        $old_ip = $oldUser['private_customer_ip'] ?? '';
        if ($old_ip !== $private_customer_ip) {
            $changes[] = "Private IP: [Old: '$old_ip' -> New: '$private_customer_ip']";
        }

        $old_status = $oldUser['status'] ?? '';
        if ($old_status !== $customer_status) {
            $changes[] = "Status: [Old: '$old_status' -> New: '$customer_status']";
        }

        $old_total = $oldUser['total'] ?? 0;
        if ($old_total != $total_limit) {
            $changes[] = "Total Limit: [Old: '$old_total' -> New: '$total_limit']";
        }

        $changes_summary = !empty($changes) ? implode(' | ', $changes) : "No major fields modified";
        $log_desc = "Updated Customer ID: $customer_id ('$customer_name'). Changes -> { $changes_summary }";

        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        } 

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
/*-------------------- Delete customer Script --------------------*/
if (isset($_GET['delete_customer_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Fetch customer details before delete for accurate logging -----------*/
    $customer_name = "Unknown";
    $customer_username = "";
    $stmt_check = $con->prepare("SELECT customer_name, username FROM customers WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $customer_name = $row_check['customer_name'];
        $customer_username = $row_check['username'];
    }
    $stmt_check->close();

    /*----------- Delete from customers table  -----------*/
    $stmt = $con->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted customer: '$customer_name' (Username: $customer_username, ID: $id)";
        
        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete customer!',
        ]);
        exit();
    }
}

/******** Add Customer Type Script ******************/
if (isset($_GET['add_customer_type_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_type = trim($_POST['customer_type_name']);

    /* Validate Customer Type */
    __validate_input($customer_type, 'Customer Type');

    /* Check if customer type already exists */
    $stmt_check = $con->prepare("SELECT id FROM customer_type WHERE name = ? LIMIT 1");
    $stmt_check->bind_param("s", $customer_type);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Customer Type already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /* Insert into table using Prepared Statement */
    $stmt = $con->prepare("INSERT INTO customer_type (`name`) VALUES (?)");
    $stmt->bind_param("s", $customer_type);
    
    if ($stmt->execute()) {
        $type_id = $stmt->insert_id;
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Added new customer type: '$customer_type' (ID: $type_id)";
        if (function_exists('logUserActivity')) {
            logUserActivity($con, 'INSERT', $log_desc);
        } elseif (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Customer Type added successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Customer Type!',
        ]);
        exit();
    }
}

/*-------Update Customer Type Script ----------*/
if (isset($_GET['update_customer_type_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_type = trim($_POST['customer_type_name']);
    $id = trim($_POST['id']);

    /* Validate Customer Type and ID */
    __validate_input($id, 'Customer Type ID');
    __validate_input($customer_type, 'Customer Type');

    /* Fetch old name for logging diff/changes */
    $old_name = "";
    $stmt_old = $con->prepare("SELECT name FROM customer_type WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    if ($res_old->num_rows > 0) {
        $row_old = $res_old->fetch_assoc();
        $old_name = $row_old['name'];
    }
    $stmt_old->close();

    /* Check if customer type already exists (excluding current ID) */
    $stmt_check = $con->prepare("SELECT id FROM customer_type WHERE name = ? AND id != ? LIMIT 1");
    $stmt_check->bind_param("si", $customer_type, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Customer Type already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /* Update the customer type in the database using Prepared Statement */
    $stmt = $con->prepare("UPDATE customer_type SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $customer_type, $id);

    if ($stmt->execute()) {
        $stmt->close();

        /*----------- Log User Activity with Changes -----------*/
        $log_desc = "Updated customer type ID: $id (Name changed from '$old_name' to '$customer_type')";
        if (function_exists('logUserActivity')) {
            logUserActivity($con, 'UPDATE', $log_desc);
        } elseif (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Customer Type updated successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Customer Type!',
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

/*--------- Add Customer services type Script--------------*/
if (isset($_GET['add_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_service = trim($_POST['customer_service_name']);

    /* Validate Customer Service */
    __validate_input($customer_service, 'Customer Service');

    /* Check if customer service already exists */
    $stmt_check = $con->prepare("SELECT id FROM customer_service WHERE name = ? LIMIT 1");
    $stmt_check->bind_param("s", $customer_service);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Customer Service already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /* Insert into table using Prepared Statement */
    $stmt = $con->prepare("INSERT INTO customer_service (`name`) VALUES (?)");
    $stmt->bind_param("s", $customer_service);
    
    if ($stmt->execute()) {
        $service_id = $stmt->insert_id;
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Added new customer service: '$customer_service' (ID: $service_id)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        } elseif (function_exists('logUserActivity')) {
            logUserActivity($con, 'INSERT', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Customer Service added successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Customer Service!',
        ]);
        exit();
    }
}

/*-------- Update Customer Service Script ----------*/
if (isset($_GET['update_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_service = trim($_POST['customer_service_name']);
    $id = trim($_POST['id']);

    /* Validate ID and Customer Service */
    __validate_input($id, 'Customer Service ID');
    __validate_input($customer_service, 'Customer Service');

    /* Fetch old name for logging diff/changes */
    $old_name = "";
    $stmt_old = $con->prepare("SELECT name FROM customer_service WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    if ($res_old->num_rows > 0) {
        $row_old = $res_old->fetch_assoc();
        $old_name = $row_old['name'] ?? '';
    }
    $stmt_old->close();

    /*-------- Check if customer service already exists---------------*/
    $stmt_check = $con->prepare("SELECT id FROM customer_service WHERE name = ? AND id != ? LIMIT 1");
    $stmt_check->bind_param("si", $customer_service, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Customer Service already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /*----------- Update the customer service in the database using Prepared Statement ---------*/
    $stmt = $con->prepare("UPDATE customer_service SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $customer_service, $id);

    if ($stmt->execute()) {
        $stmt->close();

        /*----------- Log User Activity with Changes -----------*/
        $log_desc = "Updated customer service ID: $id (Name changed from '$old_name' to '$customer_service')";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Customer Service updated successfully!',
        ]);
        exit();
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Customer Service!',
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


/*-------------------- Delete customer service Script --------------------*/
if (isset($_GET['delete_customer_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Fetch service name before delete for accurate logging -----------*/
    $service_name = "Unknown";
    $stmt_check = $con->prepare("SELECT name FROM customer_service WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $service_name = $row_check['name'] ?? 'Unknown';
    }
    $stmt_check->close();

    /*----------- Delete from customer_service table (Prepared Statement) -----------*/
    $stmt = $con->prepare("DELETE FROM customer_service WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted customer service ID: $id (Name: '$service_name')";
        
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
            'message' => 'Failed to delete customer service!',
        ]);
        exit();
    }
}
if (isset($_GET['get_customer']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $pop_id = isset($_GET['pop_id']) ? trim($_GET['pop_id']) : '';

    $data = [];
    $result = false;

    if (!empty($id) && is_numeric($id)) {

        $id = (int)$id;

        $result = $con->query("
            SELECT * 
            FROM customers 
            WHERE id = '$id'
        ");

    } elseif (!empty($pop_id) && is_numeric($pop_id)) {

        $pop_id = (int)$pop_id;

        $result = $con->query("
            SELECT * 
            FROM customers 
            WHERE pop_id = '$pop_id'
        ");
    }

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => !empty($id) ? ($data[0] ?? []) : $data,
    ]);

    exit();
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
    $destination = '../assets/customer/' . $newName;
    move_uploaded_file($file['tmp_name'], $destination);

    if ($oldFile && file_exists('../assets/customer/'.$oldFile)) {
        unlink('../assets/customer/'.$oldFile);
    }

    return $newName;
}



?>