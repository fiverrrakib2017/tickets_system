<?php 
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
/*---------------- Add POP Branch Script ----------------*/
if (isset($_GET['add_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $pop_branch                 = trim($_POST['pop_branch_name']);
    $manager_name               = trim($_POST['manager_name'] ?? '');
    $phone_number               = trim($_POST['phone_number'] ?? '');
    $battery                    = trim($_POST['battery'] ?? 0);
    $ips                        = trim($_POST['ips'] ?? 0);
    $router_ip                  = trim($_POST['router_ip'] ?? '');

    /*---------------- Validate POP Branch ----------------*/
    __validate_input($pop_branch, 'POP Branch');
    __validate_input($manager_name, 'Manager Name');
    __validate_input($phone_number, 'Phone Number');
    
    /*---------------- Check if POP Branch already exists ----------------*/
    $stmt_check = $con->prepare("SELECT id FROM pop_branch WHERE name = ? LIMIT 1");
    $stmt_check->bind_param("s", $pop_branch);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'POP Branch already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /*---------------- Insert into table using Prepared Statement ----------------*/
    $stmt = $con->prepare("INSERT INTO pop_branch (`name`, `manager_name`, `phone_number`, `battery`, `ips`, `router_ip`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $pop_branch, $manager_name, $phone_number, $battery, $ips, $router_ip);
    
    if ($stmt->execute()) {
        $branch_id = $stmt->insert_id;
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Added new POP Branch: '$pop_branch' (Manager: $manager_name, ID: $branch_id)";
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
            'message' => 'Failed to add POP Branch!',
        ]);
        exit();
    }
}

/*---------------- Update POP Branch Script ----------------*/
if (isset($_GET['update_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $pop_branch        = trim($_POST['pop_branch_name']);
    $manager_name      = trim($_POST['manager_name'] ?? '');
    $phone_number      = trim($_POST['phone_number'] ?? '');
    $battery           = trim($_POST['battery'] ?? 0);
    $ips               = trim($_POST['ips'] ?? 0);
    $router_ip         = trim($_POST['router_ip'] ?? '');
    $id                = trim($_POST['id']);

    /*---------------- Validate ID and POP Branch ----------------*/
    __validate_input($id, 'POP Branch ID');
    __validate_input($pop_branch, 'POP Branch');

    /*---------------- Fetch old branch info for logging diff/changes ----------------*/
    $old_name = "";
    $stmt_old = $con->prepare("SELECT name FROM pop_branch WHERE id = ? LIMIT 1");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    if ($res_old->num_rows > 0) {
        $row_old = $res_old->fetch_assoc();
        $old_name = $row_old['name'] ?? '';
    }
    $stmt_old->close();

    /*---------------- Check if POP Branch already exists ---------------- */
    $stmt_check = $con->prepare("SELECT id FROM pop_branch WHERE name = ? AND id != ? LIMIT 1");
    $stmt_check->bind_param("si", $pop_branch, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'POP Branch Already exists!',
        ]);
        exit();
    }
    $stmt_check->close();

    /*---------------- Update the POP Branch in the database using Prepared Statement ----------------*/
    $stmt = $con->prepare("UPDATE pop_branch SET name = ?, manager_name = ?, phone_number = ?, battery = ?, ips = ?, router_ip = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $pop_branch, $manager_name, $phone_number, $battery, $ips, $router_ip, $id);

    if ($stmt->execute()) {
        $stmt->close();

        /*----------- Log User Activity with Changes -----------*/
        $log_desc = "Updated POP Branch ID: $id (Name changed from '$old_name' to '$pop_branch', Manager: $manager_name)";
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
            'message' => 'Failed to update POP Branch!',
        ]);
        exit();
    }
}

if (isset($_GET['get_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM pop_branch WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM pop_branch");
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
/*-------------------- Delete POP Branch Script --------------------*/
if (isset($_GET['delete_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Fetch POP branch name before delete for accurate logging -----------*/
    $branch_name = "Unknown";
    $stmt_check = $con->prepare("SELECT name FROM pop_branch WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $branch_name = $row_check['name'] ?? 'Unknown';
    }
    $stmt_check->close();

    /*----------- Delete from pop_branch table (Prepared Statement) -----------*/
    $stmt = $con->prepare("DELETE FROM pop_branch WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted POP Branch ID: $id (Name: '$branch_name')";
        
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
            'message' => 'Failed to delete POP branch!',
        ]);
        exit();
    }
}



/*-----GET Customer Ping IP-----*/
if(isset($_GET['get_pop_branch_ping_ip']) && $_SERVER['REQUEST_METHOD']==='GET'){
    $pop_id = isset($_GET['pop_id']) ? (int)$_GET['pop_id'] : 0;

    if($pop_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid POP Branch ID.'
        ]);
        exit();
    }

    $stmt = $con->prepare("SELECT router_ip FROM pop_branch WHERE id = ?");
    $stmt->bind_param('i', $pop_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'router_ip' => $row['router_ip']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: '.$stmt->error
        ]);
    }

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





?>