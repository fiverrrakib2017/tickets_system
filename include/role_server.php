<?php
/**----------Enable error reporting--------------**/ 
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/datatable.php';

date_default_timezone_set('Asia/Dhaka');

/*-------- GET Role Management Data ---------*/
if (isset($_GET['get_all_role_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'roles';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        
        array(
            'db' => 'name',
            'dt' => 1,
            'formatter' => function ($d, $row) {
               return $d; 
            }
        ),
        array(
            'db' => 'id',
            'dt' => 2,
            'formatter' => function ($d, $row) {
                
                $isSuperAdmin = ($row['name'] === 'Super Admin' || $d == 1);

                if ($isSuperAdmin) {
                    return '<span class="badge badge-secondary p-2"><i class="fas fa-lock mr-1"></i> System Role</span>';
                }

                $actionHtml = '<div class="d-flex align-items-center gap-1">';
                
                /*----------Assign Permission Button--------*/ 
                $actionHtml .= '<a href="permission_management.php?role_id='.$d.'" class="btn btn-sm btn-primary" title="Assign Permission">
                                    <i class="fas fa-shield-alt mr-1"></i> Assign Permission
                                </a>';

                /*-----------Edit Button-----------*/ 
                $actionHtml .= '<button type="button" name="edit_role_button" class="btn btn-sm btn-info edit_role_button" data-id="'.$d.'" title="Edit Role">
                                    <i class="fas fa-edit"></i>
                                </button>';

                /*-----------Delete Button ---------*/ 
                $actionHtml .= '<button type="button" class="btn btn-sm btn-danger delete_role_button" data-id="'.$d.'" title="Delete Role">
                                    <i class="fas fa-trash"></i>
                                </button>';

                $actionHtml .= '</div>';

                return $actionHtml;
            }
        ),
    );

    header('Content-Type: application/json');
    echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns));
    exit;
}

/*--------Add Role Data Script---------*/
if (isset($_GET['add_role_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    /*-------------- Sanitize and validate inputs --------------*/
    $role_name = isset($_POST["role_name"]) ? trim($_POST["role_name"]) : '';

    /*------- Validate Name -------*/
    if (empty($role_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Name is required!'
        ]);
        exit;
    }

    /*------- Prepare SQL Query -------*/
    $query = "INSERT INTO `roles`(`name`,`created_at`)VALUES(?, NOW())";

    $stmt = $con->prepare($query);

    if ($stmt) {
        
        $stmt->bind_param("s",$role_name);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Role added successfully!'
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add Role !'
            ]);
            exit;
        }
    }

    echo json_encode([
        'success' => false,
        'message' => 'Database query preparation failed!'
    ]);
    exit;
}

if (isset($_GET['get_role_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $role_id = intval($_GET['id']);
    /*------- Prepare the SQL statement------*/
    $stmt = $con->prepare("SELECT * FROM `roles` WHERE id = ?");
    $stmt->bind_param("i", $role_id);
    /*-----Execute the statement-------*/ 
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $response = array("success" => true, "data" => $data);
        } else {
            $response = array("success" => false, "message" => "No record found!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }
    /*--------------Close the statement--------------*/
    $stmt->close();
    $con->close();

    /*-------------- Return the response as JSON--------------*/
    echo json_encode($response);
    exit;
}

/*--------Update Role data Script---------*/
if (isset($_GET['update_role_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    /*-------------- Sanitize and validate inputs --------------*/
    $id          = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
    $role_name   = isset($_POST["role_name"]) ? trim($_POST["role_name"]) : '';

    /*------- Validate ID -------*/
    if (empty($id) || $id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Role ID!'
        ]);
        exit;
    }
    /*------- Validate Name -------*/
    if (empty($role_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Name is required!'
        ]);
        exit;
    }
    /*------- Prepare SQL Update Query -------*/
    $query = "UPDATE `roles` SET `name` = ? WHERE `id` = ?";

    $stmt = $con->prepare($query);

    if ($stmt) {
        
        $stmt->bind_param("si",$role_name,$id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Role updated successfully!'
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update Role!'
            ]);
            exit;
        }
    }

    echo json_encode([
        'success' => false,
        'message' => 'Database query preparation failed!'
    ]);
    exit;
}

/*------- Delete Role Data Script -------*/
if (isset($_GET['delete_role_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

    /*------- Validate ID -------*/
    if (empty($id) || $id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Role ID!'
        ]);
        exit;
    }

    /*------- Check if record exists -------*/
    $checkQuery = "SELECT `id` FROM `roles` WHERE `id` = ?";
    $checkStmt  = $con->prepare($checkQuery);
    
    if ($checkStmt) {
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Role record not found!'
            ]);
            exit;
        }
    }

    /*------- Prepare SQL Delete Query -------*/
    $query = "DELETE FROM `roles` WHERE `id` = ?";
    $stmt  = $con->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Role deleted successfully!'
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete Role Device!'
            ]);
            exit;
        }
    }

    echo json_encode([
        'success' => false,
        'message' => 'Database query preparation failed!'
    ]);
    exit;
}

/*--------Update Role Permissions Script---------*/
if (isset($_GET['update_role_permissions']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    /*-------------- Sanitize and validate inputs --------------*/
    $role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;
    $permissions = isset($_POST['permissions']) && is_array($_POST['permissions']) ? $_POST['permissions'] : [];

    if ($role_id === 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid Role Selected!'
        ]);
        exit;
    }

    /*-----------Super Admin Protection-----------*/ 
    $roleStmt = $con->prepare("SELECT `name` FROM roles WHERE id = ?");
    $roleStmt->bind_param("i", $role_id);
    $roleStmt->execute();
    $roleData = $roleStmt->get_result()->fetch_assoc();
    $roleStmt->close();

    if ($roleData && $roleData['name'] === 'Super Admin') {
        echo json_encode([
            'success' => false, 
            'message' => 'Super Admin permissions cannot be modified manually!'
        ]);
        exit;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        /*--------------Delete all old permissions for this role---------*/
        $deleteStmt = $con->prepare("DELETE FROM `role_permissions` WHERE role_id = ?");
        $deleteStmt->bind_param("i", $role_id);
        $deleteStmt->execute();
        $deleteStmt->close();

        /*------------Insert newly selected permissions---------*/
        if (!empty($permissions)) {
            $insertStmt = $con->prepare("INSERT INTO `role_permissions` (role_id, permission_id) VALUES (?, ?)");

            foreach ($permissions as $perm_id) {
                $perm_id_int = intval($perm_id);
                $insertStmt->bind_param("ii", $role_id, $perm_id_int);
                $insertStmt->execute();
            }
            $insertStmt->close();
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Permissions updated successfully!'
        ]);
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
    }
    exit;
}
