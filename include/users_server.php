<?php 
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
	

/*Add User Script*/
if (isset($_GET['add_user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    /* -------- Sanitize -------- */
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    /* -------- Validation -------- */
    if ($fullname === '') {
        exit(json_encode(['success'=>false,'message'=>'Full name is required']));
    }

    if ($username === '') {
        exit(json_encode(['success'=>false,'message'=>'Username is required']));
    }

    if ($password === '') {
        exit(json_encode(['success'=>false,'message'=>'Password is required']));
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit(json_encode(['success'=>false,'message'=>'Valid email is required']));
    }

    if ($mobile === '' || !preg_match('/^[0-9]{10,15}$/', $mobile)) {
        exit(json_encode(['success'=>false,'message'=>'Valid mobile number required']));
    }

    if ($role === '') {
        exit(json_encode(['success'=>false,'message'=>'Role is required']));
    }

    /* -------- Check username exists -------- */
    if (is_unique_column($con, 'users', 'username', $username) === true) {
        exit(json_encode(['success'=>false,'message'=>'Username already exists']));
    }

    /* -------- Check email exists -------- */
    if (is_unique_column($con, 'users', 'email', $email) === true) {
        exit(json_encode(['success'=>false,'message'=>'Email already exists']));
    }

    /* -------- User Type -------- */
    $user_type = ($role === 'Super Admin') ? 1 : 2;

    /* -------- Hash Password -------- */
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    /* -------- Insert Query -------- */
    $sql = "
        INSERT INTO users 
        (user_type, fullname, username, password, mobile, email, role, lastlogin)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param(
        "issssss",
        $user_type,
        $fullname,
        $username,
        $password,
        $mobile,
        $email,
        $role
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User added successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Insert failed',
            'error'   => $stmt->error
        ]);
    }

    exit;
}


if (isset($_GET['get_user']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = intval($_GET['id']);

    /* Prepare the SQL statement*/
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    /*Execute the statement*/ 
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

    /*Close the statement*/
    $stmt->close();
    $con->close();

    /* Return the response as JSON*/
    echo json_encode($response);
    exit;
}


/* ---------- Update User Script ---------- */
if (isset($_GET['update_user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    require 'functions.php';

    /* -------- Sanitize -------- */
    $user_id  = (int)($_POST['id'] ?? 0);
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    /* -------- Validation -------- */
    if ($user_id <= 0) {
        exit(json_encode(['success'=>false,'message'=>'Invalid user ID']));
    }

    if ($fullname === '') {
        exit(json_encode(['success'=>false,'message'=>'Full name is required']));
    }

    if ($username === '') {
        exit(json_encode(['success'=>false,'message'=>'Username is required']));
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit(json_encode(['success'=>false,'message'=>'Valid email is required']));
    }

    if ($mobile === '' || !preg_match('/^[0-9]{10,15}$/', $mobile)) {
        exit(json_encode(['success'=>false,'message'=>'Valid mobile number required']));
    }

    if ($role === '') {
        exit(json_encode(['success'=>false,'message'=>'Role is required']));
    }

    /* -------- Check Existing User -------- */
    $old = $con->prepare("SELECT username,email,password FROM users WHERE id=?");
    $old->bind_param("i", $user_id);
    $old->execute();
    $oldUser = $old->get_result()->fetch_assoc();

    if (!$oldUser) {
        exit(json_encode(['success'=>false,'message'=>'User not found']));
    }

    /* -------- Duplicate Username Check -------- */
    if ($username !== $oldUser['username']) {
        if (is_unique_column($con, 'users', 'username', $username)) {
            exit(json_encode(['success'=>false,'message'=>'Username already exists']));
        }
    }

    /* -------- Duplicate Email Check -------- */
    if ($email !== $oldUser['email']) {
        if (is_unique_column($con, 'users', 'email', $email)) {
            exit(json_encode(['success'=>false,'message'=>'Email already exists']));
        }
    }

    /* -------- User Type -------- */
    $user_type = ($role === 'Super Admin') ? 1 : 2;

    /* -------- Password Handling -------- */
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            UPDATE users SET
                user_type = ?,
                fullname  = ?,
                username  = ?,
                password  = ?,
                mobile    = ?,
                email     = ?,
                role      = ?
            WHERE id = ?
        ";

        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "issssssi",
            $user_type,
            $fullname,
            $username,
            $password,
            $mobile,
            $email,
            $role,
            $user_id
        );

    } 

    /* -------- Execute -------- */
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Update failed',
            'error'   => $stmt->error
        ]);
    }

    exit;
}

/*====================== Users Data==============================*/
if (isset($_GET['get_user_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    require('datatable.php');
    $table = 'users';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'fullname',
            'dt' => 1,
        ),
        array(
            'db' => 'username',
            'dt' => 2,
        ),
        array(
            'db' => 'mobile',
            'dt' => 3,
        ),
        array(
            'db' => 'email',
            'dt' => 4,
        ),
        array(
            'db' => 'role',
            'dt' => 5,
        ),
        array(
            'db' => 'lastlogin',
            'dt' => 6,
            'formatter' => function ($d, $row) {
                $date = new DateTime($d);
                return $date->format("d F Y (g.i a)");
            },
        ),
        array(
            'db' => 'id',
            'dt' => 7,
            'formatter' => function ($d, $row) {
                return ' 
                    <button type="button" name="edit_button" class="btn-sm btn btn-info" data-id="'.$d.'"><i class="fas fa-edit"></i></button>
                    <a class="btn-sm btn btn-success" href="users_profile.php?id=' .
                        $d .
                        '"><i class="fas fa-eye"></i>
                    </a>';
            },
        ),

    );
    $condition = "";
    if (!empty($_SESSION['user_pop'])) {
        $condition .= "pop_id = '" . intval($_SESSION['user_pop']) . "'";
    }

    echo json_encode(
        SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, $condition)
    );
    exit; 
}
/*====================== Users Disable And Enable ==============================*/
if (isset($_GET['user_disable_enable']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $newStatus = isset($_POST['new_status']) ? (int)$_POST['new_status'] : 0;
   
    if ($userId > 0 && ($newStatus === 0 || $newStatus === 1)) {
        $stmt = $con->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $newStatus, $userId);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'User status updated successfully.',
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update user status: ' . $stmt->error,
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user ID or status.',
        ]);
    }
    exit;
}
/*====================== User Logs Data==============================*/
if (isset($_GET['get_user_logs_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    require('datatable.php');
    $table = 'user_login_log';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'username',
            'dt' => 1,
        ),
        array(
            'db' => 'ip',
            'dt' => 2,
        ),
        array(
            'db' => 'time',
            'dt' => 3,
            'formatter' => function ($d, $row) {
                $date = new DateTime($d);
                return $date->format("d F Y (g.i a)");
            },
        ),
        array(
            'db' => 'status',
            'dt' => 4,
            'formatter' => function ($d, $row) {
                if ($d == "Success") {
                    return '<span class="badge bg-success">Success</span>';
                } else {
                    return '<span class="badge bg-danger">Failed</span>';
                }
            },
        ),

    );

    
    $condition = "";
    if (!empty($_GET['username'])) {
        $username = $_GET['username'];
        $condition = "username = '" . $username . "'";
    }

    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
    exit; 
}

?>