<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/datatable.php';


/*---------------Show Department Data-----------*/
if (isset($_GET['show_department_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'department';
    $primaryKey = 'id';
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'department_name',
            'dt' => 1,
        ),
        array(
            'db' => 'id',
            'dt' => 2,
            'formatter' => function ($d, $row) {
                $actions=''; 

                    $actions .= '
                        <button
                            type="button"
                            name="edit_button"
                            data-id="' . $row['id'] . '"
                            class="btn btn-sm btn-primary"
                            title="Edit Department">
                            <i class="fas fa-edit"></i>
                        </button>
                    ';
                    $actions .= '
                        <button
                            type="button"
                            name="delete_button"
                            data-id="' . $row['id'] . '"
                            class="btn btn-sm btn-danger"
                            title="Delete Department">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                return $actions;
                
            },
        ),

    );

    $condition = "";
    /* Output JSON for DataTables to handle*/
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
}

if (isset($_GET['get_all_department']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $con->query("SELECT id,department_name FROM department");
    $data = [];
    while ($row = $result->fetch_array()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if (isset($_GET['add_department']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $department_name = isset($_POST["department_name"]) ? trim($_POST["department_name"]) : '';
    
    /* Validate Department Name */
    if (empty($department_name)) {
        $errors['department_name'] = "Departmnet Name is required.";
    }
    /* If validation errors exist, return errors */
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit;
    }
    /*Insert query*/
    $stmt = $con->prepare("INSERT INTO department (`department_name`) VALUES (?)");
    $stmt->bind_param('s', $department_name);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Department Added Successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $stmt->error,
        ]);
    }

    $stmt->close();
}

if (isset($_GET['get_department']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $department_id = intval($_GET['id']);

    /* Prepare the SQL statement*/
    $stmt = $con->prepare("SELECT * FROM department WHERE id = ?");
    $stmt->bind_param("i", $department_id);

    // Execute the statement
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
if (isset($_GET['update_department']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_id = $_POST['id'];
    $department_name = $_POST['department_name'];

    /* Prepare an SQL statement*/
    $stmt = $con->prepare("UPDATE `department` SET department_name = ? WHERE id = ?");

    /* Bind parameters*/
    $stmt->bind_param("si", $department_name, $department_id);

    /* Execute the statement*/
    if ($stmt->execute()) {
        $response = array("success" => true, "message" => "Update Successfully");
    } else {
        $response = array("success" => false, "message" => "Error: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();

    /* Close the database connection*/
    $con->close();

    /*Return the response as JSON*/
    echo json_encode($response);
    exit;
}
if (isset($_POST['delete_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);

    /*Prepare the SQL statement*/
    $stmt = $con->prepare("DELETE FROM department WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");

        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    /*Close the statement and connection*/
    $stmt->close();
    $con->close();

    /* Return the response as JSON*/
    echo json_encode($response);
    exit;
}

/**--------------Designation Section----------------***/
if (isset($_GET['show_designation_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'designation';
    $primaryKey = 'id';
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'designation_name',
            'dt' => 1,
        ),
        array(
            'db' => 'id',
            'dt' => 2,
            'formatter' => function ($d, $row) {
                return '
				<button type="button" name="edit_button" data-id=' . $row['id'] . ' class="btn-sm btn btn-primary"> <i class="fas fa-edit"></i></button>

				<button type="button" name="delete_button" data-id=' . $row['id'] . ' class="btn-sm btn btn-danger"> <i class="fas fa-trash"></i></button>';
            },
        ),

    );

    $condition = "";
    /* Output JSON for DataTables to handle*/
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
}

if (isset($_GET['get_all_designation']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $con->query("SELECT id,designation_name FROM designation");
    $data = [];
    while ($row = $result->fetch_array()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if (isset($_GET['add_designation']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $designation_name = isset($_POST["designation_name"]) ? trim($_POST["designation_name"]) : '';

    /* Validate designation Name */
    if (empty($designation_name)) {
        $errors['designation_name'] = "Designation Name is required.";
    }
    /* If validation errors exist, return errors */
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit;
    }
    /*Insert query*/
    $stmt = $con->prepare("INSERT INTO designation (`designation_name`) VALUES (?)");
    $stmt->bind_param('s', $designation_name);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Designation Added Successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $stmt->error,
        ]);
    }

    $stmt->close();
}

if (isset($_GET['get_designation']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $designation_id = intval($_GET['id']);

    /* Prepare the SQL statement*/
    $stmt = $con->prepare("SELECT * FROM designation WHERE id = ?");
    $stmt->bind_param("i", $designation_id);

    // Execute the statement
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
if (isset($_GET['update_designation']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $designation_id = $_POST['id'];
    $designation_name = $_POST['designation_name'];

    /* Prepare an SQL statement*/
    $stmt = $con->prepare("UPDATE `designation` SET designation_name = ? WHERE id = ?");

    /* Bind parameters*/
    $stmt->bind_param("si", $designation_name, $designation_id);

    /* Execute the statement*/
    if ($stmt->execute()) {
        $response = array("success" => true, "message" => "Update Successfully");
    } else {
        $response = array("success" => false, "message" => "Error: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();

    /* Close the database connection*/
    $con->close();

    /*Return the response as JSON*/
    echo json_encode($response);
    exit;
}
if (isset($_POST['delete_designation_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);

    /*Prepare the SQL statement*/
    $stmt = $con->prepare("DELETE FROM designation WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");

        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    /*Close the statement and connection*/
    $stmt->close();
    $con->close();

    /* Return the response as JSON*/
    echo json_encode($response);
    exit;
}

/*==================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================*/
if (isset($_GET['show_shift_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'shifts';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'shift_name',
            'dt' => 1,
        ),
        array(
            'db' => 'start_time',
            'dt' => 2,
            'formatter' => function ($d, $row) {
                return date('h:i A', strtotime($d));
            },
        ),
        array(
            'db' => 'end_time',
            'dt' => 3,
            'formatter' => function ($d, $row) {
                return date('h:i A', strtotime($d));
            },
        ),
        array(
            'db' => 'id',
            'dt' => 4,
            'formatter' => function ($d, $row) {
                $actions=''; 
                if(hasPermission('hr_shift_edit')){
                    $actions.='<button type="button" name="edit_button" data-id=' . $row['id'] . ' class="btn-sm btn btn-primary"> <i class="fas fa-edit"></i></button>';
                }
                if(hasPermission('hr_shift_delete')){
                    $actions .='<button type="button" name="delete_button" data-id=' . $row['id'] . ' class="btn-sm btn btn-danger"> <i class="fas fa-trash"></i></button>';
                }
                    
                return $actions;
            },
        ),

    );

    $condition = "";
    /* Output JSON for DataTables to handle*/
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
}
/*----------Get All Shift---------*/
if (isset($_GET['get_all_shift']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $con->query("SELECT id,shift_name,start_time,end_time FROM shifts");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if (isset($_GET['add_shift']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $shift_name = isset($_POST["shift_name"]) ? trim($_POST["shift_name"]) : '';
    $start_time = isset($_POST["start_time"]) ? trim($_POST["start_time"]) : '';
    $end_time = isset($_POST["end_time"]) ? trim($_POST["end_time"]) : '';

    /* Validate Department Name */
    if (empty($shift_name)) {
        $errors['shift_name'] = "Shift Name is required.";
    }
    if (empty($start_time)) {
        $errors['start_time'] = "Start Time is required.";
    }
    if (empty($end_time)) {
        $errors['end_time'] = "End Time is required.";
    }
    /* If validation errors exist, return errors */
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit;
    }
    /*Insert query*/
    $stmt = $con->prepare("INSERT INTO `shifts` (`shift_name`,`start_time`,`end_time`) VALUES (?,?,?)");
    $stmt->bind_param('sss', $shift_name, $start_time, $end_time);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added Successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $stmt->error,
        ]);
    }

    $stmt->close();
}

if (isset($_GET['get_shift']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $_id = intval($_GET['id']);

    /* Prepare the SQL statement*/
    $stmt = $con->prepare("SELECT * FROM shifts WHERE id = ?");
    $stmt->bind_param("i", $_id);

    // Execute the statement
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

if (isset($_GET['update_shift']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $shift_name = $_POST['shift_name'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    /* Prepare an SQL statement*/
    $stmt = $con->prepare("UPDATE `shifts` SET shift_name = ?, start_time=?,end_time=? WHERE id = ?");

    /* Bind parameters*/
    $stmt->bind_param("sssi", $shift_name, $start_time, $end_time, $id);

    /* Execute the statement*/
    if ($stmt->execute()) {
        $response = array("success" => true, "message" => "Update Successfully");
    } else {
        $response = array("success" => false, "message" => "Error: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();

    /* Close the database connection*/
    $con->close();

    /*Return the response as JSON*/
    echo json_encode($response);
    exit;
}

if (isset($_POST['shift_delete_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);

    /*Prepare the SQL statement*/
    $stmt = $con->prepare("DELETE FROM `shifts` WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");

        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    /*Close the statement and connection*/
    $stmt->close();
    $con->close();

    /* Return the response as JSON*/
    echo json_encode($response);
    exit;
}

/*==================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================*/
if (isset($_GET['show_employee_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'employees';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'name',
            'dt' => 1,
            'formatter' => function($d, $row) use ($con) {
                $employee_id = $row['id'];
                return '<a href="employee_profile.php?id=' . $employee_id . '" class="text-primary fw-bold">' . $d . '</a>';
            }
            
        ),
        array(
            'db' => 'phone_number',
            'dt' => 2,
        ),
        array(
            'db' => 'email',
            'dt' => 3,
        ),
        array(
            'db' => 'designation',
            'dt' => 4,
        ),
        array(
            'db' => 'department',
            'dt' => 5,
            'formatter' => function($d, $row) use ($con) {
                $department_id = $d;
                $all_department = $con->query("SELECT * FROM department WHERE id=$department_id");
                $row = $all_department->fetch_array();
                return $row['department_name'] ?? '---';
            }
        ),
        array(
            'db' => 'postal_code',
            'dt' => 6,
        ),
        array(
            'db' => 'joining_date',
            'dt' => 7,
        ),
        array(
            'db' => 'id',
            'dt' => 8,
            'formatter' => function ($d, $row) {

                $employee_id = (int) $row['id'];

                $actions = '';

                // View Employee
                if (hasPermission('hr_employee_view')) {

                    $actions .= '
                        <a
                            href="employee_profile.php?id=' . $employee_id . '"
                            class="btn btn-success btn-sm"
                            title="View Employee">
                            <i class="fas fa-eye"></i>
                        </a>
                    ';
                }

                // Edit Employee
                if (hasPermission('hr_employee_edit')) {

                    $actions .= '
                        <button
                            type="button"
                            name="edit_button"
                            data-id="' . $employee_id . '"
                            class="btn btn-sm btn-primary"
                            title="Edit Employee">
                            <i class="fas fa-edit"></i>
                        </button>
                    ';
                }

                return $actions;
            },
        ),

    );

    $condition = "";
    /* Output JSON for DataTables to handle*/
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
}

if (isset($_GET['get_all_employee']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $con->query("SELECT id,name FROM employees");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

/*------------- Add Employee -------------*/
if (isset($_GET['add_employee']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {

        /*------------- Start Transaction -------------*/
        $con->begin_transaction();

        $errors = [];

        $employee_code              = isset($_POST["employee_code"]) ? trim($_POST["employee_code"]) : '';
        $name                       = isset($_POST["name"]) ? trim($_POST["name"]) : '';
        $father_name                = isset($_POST["father_name"]) ? trim($_POST["father_name"]) : '';
        $mother_name                = isset($_POST["mother_name"]) ? trim($_POST["mother_name"]) : '';
        $nid                        = isset($_POST["nid"]) ? trim($_POST["nid"]) : '';
        $birth_date                 = isset($_POST["birth_date"]) ? trim($_POST["birth_date"]) : '';
        $gender                     = isset($_POST["gender"]) ? trim($_POST["gender"]) : '';
        $phone_number               = isset($_POST["phone_number"]) ? trim($_POST["phone_number"]) : '';
        $email                      = isset($_POST["email"]) ? trim($_POST["email"]) : '';
        $address                    = isset($_POST["address"]) ? trim($_POST["address"]) : '';
        $division                   = isset($_POST["division"]) ? trim($_POST["division"]) : '';
        $district                   = isset($_POST["district"]) ? trim($_POST["district"]) : '';
        $upazila                    = isset($_POST["upazila"]) ? trim($_POST["upazila"]) : '';
        $postal_code                = isset($_POST["postal_code"]) ? trim($_POST["postal_code"]) : '';
        $joining_date               = isset($_POST["joining_date"]) ? trim($_POST["joining_date"]) : '';
        $designation                = isset($_POST["designation"]) ? trim($_POST["designation"]) : '';
        $department                 = isset($_POST["department"]) ? trim($_POST["department"]) : '';
        $include_zkteco_device      = isset($_POST["include_zkteco_device"]) ? trim($_POST["include_zkteco_device"]) : '';
        $zkteco_device_card_no      = isset($_POST["zkteco_device_card_no"]) ? trim($_POST["zkteco_device_card_no"]) : '';
        $shift_id                   = isset($_POST["shift_id"]) ? trim($_POST["shift_id"]) : '';
        $status                     = isset($_POST["status"]) ? 'Active' : 'Inactive';
        $username                   = isset($_POST["username"]) ? trim($_POST["username"]) : '';
        $password                   = isset($_POST["password"]) ? trim($_POST["password"]) : '';

        /*-------Salary Fields----------*/
        $basic_salary               = isset($_POST["basic_salary"]) ? trim($_POST["basic_salary"]) : '';
        $house_allowance            = isset($_POST["house_allowance"]) ? trim($_POST["house_allowance"]) : '';
        $medical_allowance          = isset($_POST["medical_allowance"]) ? trim($_POST["medical_allowance"]) : '';
        $other_allowance            = isset($_POST["other_allowance"]) ? trim($_POST["other_allowance"]) : '';
        $tax                        = isset($_POST["tax"]) ? trim($_POST["tax"]) : '';
        $net_salary                 = isset($_POST["net_salary"]) ? trim($_POST["net_salary"]) : '';
        $effective_from             = isset($_POST["effective_from"]) ? trim($_POST["effective_from"]) : '';

        /*Basic validation for some fields*/
        if (empty($employee_code)) {
            $errors['employee_code'] = "Employee Code is required.";
        }

        if (empty($name)) {
            $errors['name'] = "Employee Name is required.";
        }

        if (empty($phone_number)) {
            $errors['phone_number'] = "Phone Number is required.";
        }

        if (empty($email)) {
            $errors['email'] = "Email is required.";
        }

        if (empty($username)) {
            $errors['username'] = "Username is required.";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required.";
        }

        if (empty($include_zkteco_device)) {
            $errors['include_zkteco_device'] = "Device Enable & Disable is required.";
        }

        /*---------------Salary Validation-------------*/
        if (empty($basic_salary)) {
            $errors['basic_salary'] = "Basic Salary is required.";
        }

        if (empty($net_salary)) {
            $errors['net_salary'] = "Net Salary is required.";
        }

        /*--------Check Username already exist--------*/
        if (isUniqueColumn($con, 'employees', 'username', $username)) {

            echo json_encode([
                'success' => false,
                'message' => 'Username already exists!',
            ]);

            exit;
        }

        /*-------Check Email already exist--------*/
        if (isUniqueColumn($con, 'employees', 'email', $email)) {

            echo json_encode([
                'success' => false,
                'message' => 'Email already exists!',
            ]);

            exit;
        }

        if (!empty($errors)) {

            echo json_encode([
                'success' => false,
                'errors' => $errors,
            ]);

            exit;
        }

        /* Insert query */
        $stmt = $con->prepare("INSERT INTO `employees`
            (`employee_code`,`shift_id`, `name`, `father_name`, `mother_name`, `nid`, `birth_date`, `gender`,
            `phone_number`, `email`, `address`,`username`,`password`, `division`, `district`, `upazila`, `postal_code`,
            `joining_date`, `designation`, `department`,  `status`, `include_zkteco_device`, `zkteco_device_card_no`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            'sssssssssssssssssssisss',
            $employee_code, $shift_id, $name, $father_name, $mother_name, $nid, $birth_date, $gender,
            $phone_number, $email, $address, $username, $password, $division, $district, $upazila, $postal_code,
            $joining_date, $designation, $department, $status, $include_zkteco_device, $zkteco_device_card_no
        );

        $result = $stmt->execute();

        $employee_id = $stmt->insert_id;

        /*-------Insert Salary---------*/
        $employee_salary_stmt = $con->prepare(" INSERT INTO employee_salaries
            (employee_id, basic_salary, house_allowance, medical_allowance, other_allowance, tax, net_salary, effective_from, is_current)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");

        $employee_salary_stmt->bind_param(
            "idddddds",
            $employee_id,
            $basic_salary,
            $house_allowance,
            $medical_allowance,
            $other_allowance,
            $tax,
            $net_salary,
            $effective_from
        );

        if (!$employee_salary_stmt->execute()) {

            throw new Exception($employee_salary_stmt->error);
        }

        if ($result) {

            if($include_zkteco_device === 'enable' && !empty($zkteco_device_card_no)) {

                $_root = $_SERVER['DOCUMENT_ROOT'];

                include $_root.'/lib/ZktecoApi.php';

                $zkteco = new ZktecoApi($con);

                $transactions = $zkteco->add_employee([
                    'include_zkteco_device' => 'enable',
                    'code'                  => $employee_code,
                    'name'                  => $name,
                    'department'            => '1',
                    'area'                  => [2],
                    'birth_date'            => $birth_date,
                    'zkteco_device_card_no' => $zkteco_device_card_no,
                ]);
            }

            /*------------- Commit Transaction -------------*/
            $con->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Employee added successfully!',
            ]);

        } else {

            throw new Exception($stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {

        /*------------- Rollback Transaction -------------*/
        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
    }
}

if (isset($_GET['get_employee']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $_id = intval($_GET['id']);

    $stmt = $con->prepare("
        SELECT e.*, 
               s.basic_salary, 
               s.house_allowance, 
               s.medical_allowance, 
               s.other_allowance, 
               s.tax, 
               s.net_salary, 
               s.effective_from
        FROM employees e
        LEFT JOIN employee_salaries s 
            ON s.employee_id = e.id AND s.is_current = 1
        WHERE e.id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $response = ["success" => true, "data" => $data];
        } else {
            $response = ["success" => false, "message" => "No record found!"];
        }
    } else {
        $response = ["success" => false, "message" => $stmt->error];
    }

    $stmt->close();
    echo json_encode($response);
    exit;
}
if (isset($_GET['edit_employee']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $id                     = isset($_POST["id"]) ? trim($_POST["id"]) : ''; 
    $employee_code          = isset($_POST["employee_code"]) ? trim($_POST["employee_code"]) : '';
    $name                   = isset($_POST["name"]) ? trim($_POST["name"]) : '';
    $father_name            = isset($_POST["father_name"]) ? trim($_POST["father_name"]) : '';
    $mother_name            = isset($_POST["mother_name"]) ? trim($_POST["mother_name"]) : '';
    $nid                    = isset($_POST["nid"]) ? trim($_POST["nid"]) : '';
    $birth_date             = isset($_POST["birth_date"]) ? trim($_POST["birth_date"]) : '';
    $gender                 = isset($_POST["gender"]) ? trim($_POST["gender"]) : '';
    $phone_number           = isset($_POST["phone_number"]) ? trim($_POST["phone_number"]) : '';
    $email                  = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $address                = isset($_POST["address"]) ? trim($_POST["address"]) : '';
    $division               = isset($_POST["division"]) ? trim($_POST["division"]) : '';
    $district               = isset($_POST["district"]) ? trim($_POST["district"]) : '';
    $upazila                = isset($_POST["upazila"]) ? trim($_POST["upazila"]) : '';
    $postal_code            = isset($_POST["postal_code"]) ? trim($_POST["postal_code"]) : '';
    $joining_date           = isset($_POST["joining_date"]) ? trim($_POST["joining_date"]) : '';
    $designation            = isset($_POST["designation"]) ? trim($_POST["designation"]) : '';
    $department             = isset($_POST["department"]) ? trim($_POST["department"]) : '';
    $shift_id               = isset($_POST["shift_id"]) ? trim($_POST["shift_id"]) : '';
    $status                 = isset($_POST["status"]) ? trim($_POST["status"]) : '';
    $username               = isset($_POST["username"]) ? trim($_POST["username"]) : '';
    $password               = isset($_POST["password"]) ? trim($_POST["password"]) : '';
    /*-------Salary Fields----------*/
    $basic_salary               = isset($_POST["basic_salary"]) ? trim($_POST["basic_salary"]) : '';
    $house_allowance            = isset($_POST["house_allowance"]) ? trim($_POST["house_allowance"]) : '';
    $medical_allowance          = isset($_POST["medical_allowance"]) ? trim($_POST["medical_allowance"]) : '';
    $other_allowance            = isset($_POST["other_allowance"]) ? trim($_POST["other_allowance"]) : '';
    $tax                        = isset($_POST["tax"]) ? trim($_POST["tax"]) : '';
    $net_salary                 = isset($_POST["net_salary"]) ? trim($_POST["net_salary"]) : '';
    $effective_from             = isset($_POST["effective_from"]) ? trim($_POST["effective_from"]) : '';
    $errors = [];

   /*Basic validation for some fields*/
    if (empty($employee_code)) {
        echo json_encode([
			'success' => false,
			'message' => 'Employee Code is required.',
		]);
		exit;
    }
    if (empty($name)) {
        echo json_encode([
			'success' => false,
			'message' => 'Employee Name is required.',
		]);
		exit;
    }
    if (empty($phone_number)) {
        echo json_encode([
			'success' => false,
			'message' => 'Phone Number is required.',
		]);
		exit;
    }
    if (empty($email)) {
        echo json_encode([
			'success' => false,
			'message' => 'Email is required.',
		]);
		exit;
    }
    if (empty($username)) {
        echo json_encode([
			'success' => false,
			'message' => 'Username is required.',
		]);
		exit;
    }
    if (empty($password)) {
        echo json_encode([
			'success' => false,
			'message' => 'Password is required.',
		]);
		exit;
    }
    if (empty($basic_salary)) {
        echo json_encode([
			'success' => false,
			'message' => "Basic Salary is required.",
		]);
		exit;
    }
    if (empty($net_salary)) {
        echo json_encode([
			'success' => false,
			'message' => "Net Salary is required.",
		]);
		exit;
    }

    

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }


    /*Check Username  already exist*/
	if (isUniqueColumn($con, 'employees', 'username', $username, $id)) {
		echo json_encode([
            'success' => false,
            'message' => 'Username already exists!',
        ]);
        exit;
	}
	/*Check  Email already exist*/
	if (isUniqueColumn($con, 'employees', 'email', $email , $id)) {
		echo json_encode([
            'success' => false,
            'message' => 'Email already exists!',
        ]);
        exit;
	}
 

    $con->begin_transaction();

    try {

        /*----------Update Employee---------*/
        $stmt = $con->prepare("
        UPDATE employees SET 
        employee_code=?,shift_id=?, name=?, father_name=?, mother_name=?, nid=?, birth_date=?, gender=?,
        phone_number=?, email=?, address=?, username=?, password=?, division=?, district=?, upazila=?, postal_code=?,
        joining_date=?, designation=?, department=?
        WHERE id=?
        ");

        $stmt->bind_param(
            'sssssssssssssssssssii',
            $employee_code,$shift_id, $name, $father_name, $mother_name, $nid, $birth_date, $gender,
            $phone_number, $email, $address, $username, $password, $division, $district, $upazila, $postal_code,
            $joining_date, $designation, $department, $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Employee update failed");
        }
        /*----------Check Current Salary---------*/
        $check = $con->prepare("
            SELECT * FROM employee_salaries 
            WHERE employee_id=? AND is_current=1
        ");
        $check->bind_param("i", $id);
        $check->execute();
        $current_salary = $check->get_result()->fetch_assoc();

        /*----------Compare Salary---------*/
        $is_changed = false;

        if (!$current_salary) {
            $is_changed = true;
        } else {
            if (
                $current_salary['basic_salary'] != $basic_salary ||
                $current_salary['house_allowance'] != $house_allowance ||
                $current_salary['medical_allowance'] != $medical_allowance ||
                $current_salary['other_allowance'] != $other_allowance ||
                $current_salary['tax'] != $tax ||
                $current_salary['net_salary'] != $net_salary
            ) {
                $is_changed = true;
            }
        }
        /*--------Salary Update --------*/
        if ($is_changed) {

            /*---- old salary inactive-----*/
            $con->query("UPDATE employee_salaries SET is_current=0 WHERE employee_id=$id");

            /*----------- insert new salary--------*/
            $stmt2 = $con->prepare("
            INSERT INTO employee_salaries
            (employee_id, basic_salary, house_allowance, medical_allowance, other_allowance, tax, net_salary, effective_from, is_current)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");

            $stmt2->bind_param(
                "idddddds",
                $id,
                $basic_salary,
                $house_allowance,
                $medical_allowance,
                $other_allowance,
                $tax,
                $net_salary,
                $effective_from
            );

            if (!$stmt2->execute()) {
                throw new Exception("Salary update failed");
            }
        }

       
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Employee & Salary updated successfully'
        ]);

    } catch (Exception $e) {

        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

 /*------------- Employee Delete Data -------------*/
if (isset($_POST['employee_delete_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = intval($_POST['id']);

    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid employee ID!"
        ]);

        exit;
    }

    try {

        /*------------- Start Transaction -------------*/
        $con->begin_transaction();

        /*------------- Get Employee Info -------------*/
        $stmt = $con->prepare("
            SELECT 
                employee_code,
                include_zkteco_device,
                zkteco_device_card_no
            FROM employees
            WHERE id = ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if (!$result || $result->num_rows == 0) {
            throw new Exception("Employee not found!");
        }

        $row = $result->fetch_assoc();

        $emp_code       = $row['employee_code'] ?? '';
        $zkteco_device  = $row['include_zkteco_device'] ?? '';
        $device_card_no = $row['zkteco_device_card_no'] ?? '';

        $stmt->close();

        /*------------- Delete From ZKTeco Device -------------*/
        if (
            $zkteco_device === 'enable' &&
            !empty($device_card_no) &&
            !empty($emp_code)
        ) {

            $_root = $_SERVER['DOCUMENT_ROOT'];

            include_once $_root . '/lib/ZktecoApi.php';

            $zkteco = new ZktecoApi($con);

            $zkResponse = $zkteco->delete_employee($emp_code);

            if (!$zkResponse['success']) {

                throw new Exception(
                    "Failed to delete employee from ZKTeco device! " .
                    ($zkResponse['message'] ?? '')
                );
            }
        }

        /*------------- Delete Related Employee Data -------------*/

        $tables = [

            'leaves',
            'employee_advances',
            'employee_loans',
            'employee_loan_installments',
            'employee_payrolls',
            'employee_salaries',
            'employee_tasks',
            'employee_task_feedback'

        ];

        foreach ($tables as $table) {

            $table = trim($table);

            if (empty($table)) {
                continue;
            }

            $sql = "DELETE FROM `$table` WHERE employee_id = ?";

            $stmt = $con->prepare($sql);

            
            if (!$stmt) {
                continue;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }

        /*------------- Finally Delete Employee -------------*/
        $stmt = $con->prepare("
            DELETE FROM employees 
            WHERE id = ?
        ");

        if (!$stmt) {
            throw new Exception(
                "Employee delete prepare failed: " . $con->error
            );
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {

            throw new Exception(
                "Employee delete failed: " . $stmt->error
            );
        }

        if ($stmt->affected_rows <= 0) {

            throw new Exception(
                "No employee deleted from database."
            );
        }

        $stmt->close();

        /*------------- Commit Transaction -------------*/
        $con->commit();

        echo json_encode([
            "success" => true,
            "message" => "Employee and all related data deleted successfully."
        ]);

    } catch (Exception $e) {

        /*------------- Rollback Transaction -------------*/
        $con->rollback();

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    $con->close();

    exit;
}


/*--------------Leave-----------------*/
if (isset($_GET['show_leave_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'leaves';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'employee_id',
            'dt' => 1,
            'formatter' => function($d, $row) use ($con) {
                $employee_id = $d;
                $all_employee = $con->query("SELECT name FROM employees WHERE id=$employee_id");
                $row = $all_employee->fetch_array();
                return $row['name'];
            }
        ),
        array(
            'db' => 'leave_type',
            'dt' => 2,
        ),
        array(
            'db' => 'leave_reason',
            'dt' => 3,
            // 'formatter'=>function($d, $row) {
            //     /* Check if the string contains '<br>' and remove everything after it*/
            //     if (strpos($d, '<br>') !== false) {
            //         $d = substr($d, 0, strpos($d, '<br>'));
            //     }
            //     if (strlen($d) > 30) {
            //         return substr($d, 0, 30) . '...'; 
            //     } else {
            //         return $d;
            //     }
            // }
            'formatter'=>function($d, $row) {
            /*to find the position of '<br>'*/
            // function findPosition($haystack, $needle) {
            //     $length = customStrlen($needle);
            //     for ($i = 0; $i <= customStrlen($haystack) - $length; $i++) {
            //         $match = true;
            //         for ($j = 0; $j < $length; $j++) {
            //             if ($haystack[$i + $j] !== $needle[$j]) {
            //                 $match = false;
            //                 break;
            //             }
            //         }
            //         if ($match) {
            //             return $i;
            //         }
            //     }
            //     return false;
            // }

            // function customSubstr($string, $start, $length = null) {
            //     $result = '';
            //     $strLength = customStrlen($string);
            //     if ($length === null) {
            //         $length = $strLength - $start;
            //     }
            //     for ($i = $start; $i < $start + $length && $i <pr $strLength; $i++) {
            //         $result .= $string[$i];
            //     }
            //     return $result;
            // }
            // function customStrlen($string) {
            //     $length = 0;
            //     while (isset($string[$length])) {
            //         $length++;
            //     }
            //     return $length;
            // }
            // $position = findPosition($d, '<br>');
            // if ($position !== false) {
            //     $d = customSubstr($d, 0, $position);
            // }
            // if (customStrlen($d) > 30) {
            //     return customSubstr($d, 0, 30) . '...';
            // } else {
            //     return $d; 
            // }
            if (strpos($d, '<br>') !== false) {
                $d = substr($d, 0, strpos($d, '<br>'));
            }
            if (strlen($d) > 30) {
                return substr($d, 0, 30) . '...'; 
            } else {
                return $d;
            }
        }


        ),
        array(
            'db' => 'leave_status',
            'dt' => 4,
            'formatter'=>function($d, $row){
                if($d == 'Approved'){
                    return '<span class="badge bg-success">Approved</span>';
                }elseif($d=='Rejected'){
                    return '<span class="badge bg-danger">Rejected</span>';
                }
                else{
                    return '<span class="badge bg-danger">Pending</span>';
                }
            }
        ),
        array(
            'db' => 'start_date',
            'dt' => 5,
            'formatter' => function($d, $row) {
            // function customDateFormat($dateString) {
            //     $timestamp = customStrToTime($dateString);
            //     if ($timestamp === false) {
            //         return $dateString; 
            //     }
            //     $day = customDate('d', $timestamp);
            //     $month = customDate('M', $timestamp);
            //     $year = customDate('Y', $timestamp);
            //     return $day . ' ' . $month . ' ' . $year;
            // }
            // function customStrToTime($dateString) {
            //     $dateParts = explode('-', $dateString);
            //     if (count($dateParts) !== 3) {
            //         return false; 
            //     }
            //     return mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0]);
            // }
            // function customDate($format, $timestamp) {
            //     $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            //     $day = date('d', $timestamp);
            //     $month = date('m', $timestamp);
            //     $year = date('Y', $timestamp);

            //     switch ($format) {
            //         case 'd':
            //             return $day;
            //         case 'M':
            //             return $months[$month - 1];
            //         case 'Y':
            //             return $year;
            //         default:
            //             return '';
            //     }
            // }
            // return customDateFormat($d);
            return date('d M Y', strtotime($d)); 
        }

        ),
        array(
            'db' => 'end_date',
            'dt' => 6,
            'formatter' => function($d, $row) {
                return date('d M Y', strtotime($d)); 
            }
        ),
        array(
            'db' => 'id',
            'dt' => 7,
            'formatter' => function ($d, $row) {
                $actions='';
                if(hasPermission('hr_leave_edit')){
                    $actions .= '
                        <button
                            type="button"
                            name="edit_button"
                            data-id="' . $row['id'] . '"
                            class="btn btn-sm btn-primary"
                            title="Edit Leave">
                            <i class="fas fa-edit"></i>
                        </button>
                    ';
                }
                if(hasPermission('hr_leave_delete')){
                    $actions .= '
                        <button
                            type="button"
                            name="delete_button"
                            data-id="' . $row['id'] . '"
                            class="btn btn-sm btn-danger"
                            title="Delete Leave">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }
                return $actions;
            },
        ),

    );

    $condition = "";
    /* Output JSON for DataTables to handle*/
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
}

if (isset($_GET['add_leave']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    /*Retrieving form data*/
    $employee_id = isset($_POST["employee_id"]) ? trim($_POST["employee_id"]) : '';
    $leave_type = isset($_POST["leave_type"]) ? trim($_POST["leave_type"]) : '';
    $leave_reason = isset($_POST["leave_reason"]) ? trim($_POST["leave_reason"]) : '';
    $leave_status = isset($_POST["leave_status"]) ? trim($_POST["leave_status"]) : '';

    $start_date = isset($_POST["start_date"]) ? trim($_POST["start_date"]) : '';
    $end_date = isset($_POST["end_date"]) ? trim($_POST["end_date"]) : '';

    /*Basic validation for some fields*/
    if (empty($employee_id)) {
        $errors['employee_id'] = "Employee Name is required.";
    }
    if (empty($leave_type)) {
        $errors['leave_type'] = "Leave Type is required.";
    }
    if (empty($leave_reason)) {
        $errors['leave_reason'] = "Leave Reason is required.";
    }
    if (empty($leave_status)) {
        $errors['leave_status'] = "Leave Status is required.";
    }
    if (empty($start_date)) {
        $errors['start_date'] = "Start Date is required.";
    }
    if (empty($end_date)) {
        $errors['end_date'] = "End Date is required.";
    }

    /* If validation errors exist, return errors */
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit;
    }

    /* Insert query */
    $stmt = $con->prepare("INSERT INTO `leaves`(`employee_id`, `leave_type`, `leave_reason`, `leave_status`, `start_date`, `end_date`)VALUES(?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        'isssss',
        $employee_id, $leave_type, $leave_reason, $leave_status, $start_date, $end_date
    );

    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $stmt->error,
        ]);
    }

    $stmt->close();
}

if (isset($_GET['get_leave']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $_id = intval($_GET['id']);

    /* Prepare the SQL statement*/
    $stmt = $con->prepare("SELECT * FROM `leaves` WHERE id = ?");
    $stmt->bind_param("i", $_id);

    // Execute the statement
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


if (isset($_GET['edit_leave']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $errors = [];
    /*Retrieving form data*/
    $id = isset($_POST["id"]) ? trim($_POST["id"]) : ''; 
    $employee_id = isset($_POST["employee_id"]) ? trim($_POST["employee_id"]) : '';
    $leave_type = isset($_POST["leave_type"]) ? trim($_POST["leave_type"]) : '';
    $leave_reason = isset($_POST["leave_reason"]) ? trim($_POST["leave_reason"]) : '';
    $leave_status = isset($_POST["leave_status"]) ? trim($_POST["leave_status"]) : '';

    $start_date = isset($_POST["start_date"]) ? trim($_POST["start_date"]) : '';
    $end_date = isset($_POST["end_date"]) ? trim($_POST["end_date"]) : '';

    /*Basic validation for some fields*/
    if (empty($employee_id)) {
        $errors['employee_id'] = "Employee Name is required.";
    }
    if (empty($leave_type)) {
        $errors['leave_type'] = "Leave Type is required.";
    }
    if (empty($leave_reason)) {
        $errors['leave_reason'] = "Leave Reason is required.";
    }
    if (empty($leave_status)) {
        $errors['leave_status'] = "Leave Status is required.";
    }
    if (empty($start_date)) {
        $errors['start_date'] = "Start Date is required.";
    }
    if (empty($end_date)) {
        $errors['end_date'] = "End Date is required.";
    }

    /* If validation errors exist, return errors */
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit;
    }

    /*Update query*/
    $stmt = $con->prepare("UPDATE `leaves` SET `employee_id`=?, `leave_type`=?, `leave_reason`=?, `leave_status`=?, `start_date`=?, `end_date`=?  WHERE `id`=?");
    $stmt->bind_param('isssssi', $employee_id, $leave_type, $leave_reason, $leave_status, $start_date, $end_date, $id);
   
    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $stmt->error
        ]);
    }

    $stmt->close();
}

if (isset($_POST['leave_delete_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);

    /*Prepare the SQL statement*/
    $stmt = $con->prepare("DELETE FROM `leaves` WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");

        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    /*Close the statement and connection*/
    $stmt->close();
    $con->close();

    /* Return the response as JSON*/
    echo json_encode($response);
    exit;
}
/*--------------Attendance----------*/
if (isset($_GET['show_attendance_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = 'attendances';
    $primaryKey = 'id';

    $columns = array(
        array(
            'db' => 'id',
            'dt' => 0,
        ),
        array(
            'db' => 'employee_id', 
            'dt' => 1,
            'formatter'=>function($d, $row) use($con){
                $all_employee=$con->query("SELECT `employee_code` FROM employees WHERE id=$d");
                $row=$all_employee->fetch_array();
                return  $row['employee_code'] ?? '---';
            }
        ),
        array(
            'db' => 'employee_id',
            'dt' => 2, 
            'formatter' => function($d, $row) use ($con) {
                $employee_id = intval($d); 
                $all_employee = $con->query("SELECT name FROM employees WHERE id=$employee_id");
                $emp_row = $all_employee->fetch_array();
                
                if ($emp_row) {
                    $name = htmlspecialchars($emp_row['name']);
                    return '<a href="employee_profile.php?id=' . $employee_id . '" class="text-primary fw-bold">' . $name . '</a>';
                } else {
                    return '---';
                }
            }
        ),

        array(
            'db' => 'employee_id',
            'dt' => 3, 
            'formatter'=>function($d, $row) use($con){
                $employee_id=$d; 
                $all_employee=$con->query("SELECT phone_number FROM employees WHERE id=$employee_id");
                $row=$all_employee->fetch_array();
                return $row['phone_number'] ?? '---';
            }
        ),
       array(
            'db' => 'employee_id',
            'dt' => 4,
            'formatter' => function($d, $row) use($con){

                $employee_id = (int)$d;

                if(empty($employee_id)){
                    return '---';
                }

                /*-------Employee Query-----*/ 
                $employee_query = $con->query("
                    SELECT department 
                    FROM employees 
                    WHERE id = $employee_id
                ");

                if(!$employee_query || $employee_query->num_rows == 0){
                    return '---';
                }

                $employee = $employee_query->fetch_assoc();

                $department_id = (int)($employee['department'] ?? 0);

                if(empty($department_id)){
                    return '---';
                }

                /*--------Department Query-----*/ 
                $department_query = $con->query("
                    SELECT department_name 
                    FROM department 
                    WHERE id = $department_id
                ");

                if(!$department_query || $department_query->num_rows == 0){
                    return '---';
                }

                $department = $department_query->fetch_assoc();

                return $department['department_name'] ?? '---';
            }
        ),
        array(
            'db' => 'duty_date',
            'dt' => 5,
        ),
        array(
            'db' => 'shift_id',
            'dt' => 6, 
            'formatter'=>function($d, $row) use($con){
                $shift_id=$d; 
                $all_shift=$con->query("SELECT shift_name FROM `shifts` WHERE id=$shift_id");
                $row=$all_shift->fetch_array();
                return $row['shift_name'];
            }
        ),
        array(
            'db' => 'time_in',
            'dt' => 7,
            'formatter' => function($d, $row) {
               return _formatTime($d);
            }
        ),
        array(
            'db' => 'time_out',
            'dt' =>8,
            'formatter' => function($d, $row) {
              return _formatTime($d);
            }
        ),
        array(
            'db' => 'status',
            'dt' => 9,
            'formatter' => function($d, $row) {
                if ($d == 'Present') { 
                    return '<span class="badge bg-success">Present</span>';
                }
                return '<span class="badge bg-danger">Absent</span>';
            }
        ),
        array(
            'db' => 'id',
            'dt' => 10,
            'formatter' => function ($d, $row) {
                $employee_id = $row['employee_id']; 
                return '
                    <a href="employee_profile.php?id=' . $employee_id . '" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                   
                ';
                //  <button type="button" name="edit_button" data-id="' . $row['id'] . '" class="btn btn-sm btn-warning">
                //         <i class="fas fa-edit"></i>
                //     </button>
                //     <button type="button" name="delete_button" data-id="' . $row['id'] . '" class="btn btn-sm btn-danger">
                //         <i class="fas fa-trash"></i>
                //     </button>
            },
        ),


    );
    $condition = "";
    /* If Status is provided, */
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $status = trim($_GET['status']);
        if ($status == 'today_present') {
            $status = "Present";
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.status = '" . $status . "'";
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.duty_date = '" . date('Y-m-d') . "'";
        }elseif($status == 'today_attendance'){
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.duty_date = '" . date('Y-m-d') . "'";
        } elseif ($status == 'today_absent') {
            $status = "Absent";
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.status = '" . $status . "'";
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.duty_date = '" . date('Y-m-d') . "'";
        } elseif ($status == 'late_attendance') {
            $status = "Late";
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.status = '" . $status . "'";
        }else {
            $condition .= (!empty($condition) ? " AND " : "") . "attendances.status = '" . $status . "'";
        }
    }

    
    if(!empty($_GET['from_date']) && !empty($_GET['to_date'])){
        $from_date=$_GET['from_date'];
        $to_date=$_GET['to_date'];
        $condition .="duty_date BETWEEN '$from_date' AND '$to_date'";
    }
    /* Output JSON for DataTables to handle*/
    echo json_encode(
        SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
    exit; 
}
/*----------Attendance Report------------------*/
if (isset($_GET['show_attendance_report_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $from_date = isset($_GET["from_date"]) ? trim($_GET["from_date"]) : '';
    $to_date = isset($_GET["to_date"]) ? trim($_GET["to_date"]) : '';

    /*------Validation check-----*/  
    if (empty($from_date) || empty($to_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Please select both from and to dates']);
        exit;
    }
    if( $from_date > $to_date ) {
        echo json_encode(['status' => 'error', 'message' => 'From date cannot be greater than To date']);
        exit;
    }
   if (isset($_GET['employee_id']) && !empty($_GET['employee_id']) && $_GET['employee_id'] !== '---Select---') {
        $employee_id = intval($_GET['employee_id']);
        $stmt = $con->prepare("SELECT * FROM `attendances` WHERE employee_id=? AND duty_date BETWEEN ? AND ? ORDER BY id DESC");
        $stmt->bind_param("iss", $employee_id, $from_date, $to_date);
    } else {
        $stmt = $con->prepare("SELECT * FROM `attendances` WHERE duty_date BETWEEN ? AND ? ORDER BY id DESC");
        $stmt->bind_param("ss", $from_date, $to_date);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $response = $result->fetch_all(MYSQLI_ASSOC);

    $html = '';

    foreach ($response as $row) {
        /*-----Get Employee Info--------*/
        $employee_id = $con->query("SELECT  `id` FROM `employees` WHERE id='" . $row['employee_id'] . "'")->fetch_array()['id'];
        $employee_name = $con->query("SELECT  `name` FROM `employees` WHERE id='" . $row['employee_id'] . "'")->fetch_array()['name'];
        $employee_code = $con->query("SELECT `employee_code` FROM `employees` WHERE id='" . $row['employee_id'] . "'")->fetch_array()['employee_code'];
        $employee_phone_number = $con->query("SELECT  `phone_number` FROM `employees` WHERE id='" . $row['employee_id'] . "'")->fetch_array()['phone_number'];

        /*-----Get Employee Department--------*/
        $department_id = $con->query("SELECT department FROM `employees` WHERE id='" . $row['employee_id'] . "'")->fetch_array()['department'];
        $get_department_name = $con->query("SELECT department_name FROM `department` WHERE id='" . $department_id . "'")->fetch_array()['department_name'];

        /*-----Get Employee Duty Date Formate--------*/
        $duty_date = date('d M Y', strtotime($row['duty_date']));
        
        /*-----Get Shift Name--------*/
        $shift_id = $row['shift_id'];
        $shift_name = $con->query("SELECT shift_name FROM `shifts` WHERE id='" . $shift_id . "'")->fetch_array()['shift_name'];
        /*-----Format Time In & Time Out--------*/
        $time_in  = _formatTime($row['time_in']);
        $time_out = _formatTime($row['time_out']);

        /*-----Get Status--------*/
        $status = $row['status'];
        if( $status == 'Present'){
            $status = '<span class="badge bg-success">Present</span>';
        }else{
            $status = '<span class="badge bg-danger">Absent</span>';
        }

        $html .= '<tr>';
        $html .= '<td>'.$row['id'].'</td>';
        $html .= '<td>'.$employee_code.'</td>';
        $html .= '<td><a href="employee_profile.php?id='.$employee_id.'">'.$employee_name.'</a></td>';
        $html .= '<td>'.$employee_phone_number.'</td>';
        $html .= '<td>'.$get_department_name.'</td>';
        $html .= '<td>'.$duty_date.'</td>';
        $html .= '<td>'.$shift_name.'</td>';
        $html .= '<td>' . $time_in . '</td>';
        $html .= '<td>' . $time_out . '</td>';
        $html .= '<td>'.$status.'</td>';
        $html .= '</tr>';
    }
    echo json_encode(['success' => true, 'data' => $html]);
}
/*----------Add Attendance Data------------------*/
if (isset($_GET['add_attendance']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $time_zone = 'Asia/Dhaka';
   

    $employee_id = isset($_POST["employee_id"]) ? trim($_POST["employee_id"]) : '';
    $duty_date   = isset($_POST["duty_date"]) ? trim($_POST["duty_date"]) : '';
    $shift_id    = isset($_POST["shift_id"]) ? trim($_POST["shift_id"]) : '';
    $time_in     = isset($_POST["time_in"]) ? trim($_POST["time_in"]) : '';
    $time_out    = isset($_POST["time_out"]) ? trim($_POST["time_out"]) : '';

    /*---------- Basic Validation ----------*/
    if (empty($employee_id)) $errors['employee_id'] = "Employee Name is required.";
    if (empty($duty_date))   $errors['duty_date']   = "Duty Date is required.";
    if (empty($shift_id))    $errors['shift_id']    = "Shift Time is required.";

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    /*---------- Duplicate Check ----------*/
    $check_stmt = $con->prepare("SELECT id FROM `attendances` WHERE employee_id = ? AND duty_date = ?");
    $check_stmt->bind_param('is', $employee_id, $duty_date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Attendance for this employee on this date already exists.'
        ]);
        exit;
    }

    /*---------- Time Zone Setup ----------*/
    $tz = new DateTimeZone($time_zone);
    $start_time = new DateTime($time_in, $tz);
    $end_time   = new DateTime($time_out ?: $time_in, $tz); 

    /*---------- Shift Calculation ----------*/
    $shift_start = new DateTime('09:00', $tz);
    $shift_end   = new DateTime('17:00', $tz);

    /* Total Working Hours */
    $working_interval = $start_time->diff($end_time);
    $working_time = $working_interval->format('%H:%I:%S');

    /* Over Time */
    if ($end_time > $shift_end) {
        $over_time_interval = $shift_end->diff($end_time);
        $over_time = $over_time_interval->format('%H:%I:%S');
    } else {
        $over_time = '00:00:00';
    }

    /*---------- Attendance Status ----------*/
    $attendance_status = '';
    if(isset($_POST['attendance_status'])){
        if($_POST['attendance_status'] == '1'){
            $attendance_status = 'Present';
        }else{
            $attendance_status = 'Absent';
        }
    }

    /*---------- Insert Query ----------*/
    $stmt = $con->prepare("INSERT INTO `attendances` 
        (`employee_id`, `duty_date`, `shift_id`, `time_in`, `time_out`, `working_time`, `over_time`, `status`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssss', $employee_id, $duty_date, $shift_id, $time_in, $time_out, $working_time, $over_time, $attendance_status);
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Attendance added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    exit;
}
/*----------GET Attendance Data------------------*/
if (isset($_GET['get_attendance_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $_id = intval($_GET['id']);
    $stmt = $con->prepare("SELECT * FROM `attendances` WHERE id = ?");
    $stmt->bind_param("i", $_id);
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
    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}

/*----------Update Attendance Data------------------*/
if (isset($_GET['update_attendance_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $time_zone = 'Asia/Dhaka';

    $attendance_id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
    $employee_id   = isset($_POST["employee_id"]) ? trim($_POST["employee_id"]) : '';
    $duty_date     = isset($_POST["duty_date"]) ? trim($_POST["duty_date"]) : '';
    $shift_id      = isset($_POST["shift_id"]) ? trim($_POST["shift_id"]) : '';
    $time_in       = isset($_POST["time_in"]) ? trim($_POST["time_in"]) : '';
    $time_out      = isset($_POST["time_out"]) ? trim($_POST["time_out"]) : '';

    /*---------- Basic Validation ----------*/
    if (empty($attendance_id)) $errors['attendance_id'] = "Attendance ID missing.";
    if (empty($employee_id))   $errors['employee_id']   = "Employee Name is required.";
    if (empty($duty_date))     $errors['duty_date']     = "Duty Date is required.";
    if (empty($shift_id))      $errors['shift_id']      = "Shift Time is required.";
    if (empty($time_in))       $errors['time_in']       = "Time In is required.";

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit;
    }

    /*---------- Time Calculation ----------*/
    $tz = new DateTimeZone($time_zone);
    $start_time = new DateTime($time_in, $tz);
    $end_time   = new DateTime($time_out ?: $time_in, $tz); 

    $shift_start = new DateTime('09:00', $tz);
    $shift_end   = new DateTime('17:00', $tz);

    // Working time
    $working_interval = $start_time->diff($end_time);
    $working_time = $working_interval->format('%H:%I:%S');

    // Over time
    if ($end_time > $shift_end) {
        $over_time_interval = $shift_end->diff($end_time);
        $over_time = $over_time_interval->format('%H:%I:%S');
    } else {
        $over_time = '00:00:00';
    }

    /*---------- Check Attendance Exists ----------*/
    $check_stmt = $con->prepare("SELECT id FROM `attendances` WHERE id = ?");
    $check_stmt->bind_param('i', $attendance_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Attendance record not found!',
        ]);
        exit;
    }

    /*---------- Update Attendance ----------*/
    $update_stmt = $con->prepare("
        UPDATE `attendances`
        SET employee_id = ?, duty_date = ?, shift_id = ?, time_in = ?, time_out = ?, working_time = ?, over_time = ?, status = 'Present'
        WHERE id = ?
    ");
    $update_stmt->bind_param(
        'issssssi',
        $employee_id,
        $duty_date,
        $shift_id,
        $time_in,
        $time_out,
        $working_time,
        $over_time,
        $attendance_id
    );

    $update_result = $update_stmt->execute();

    if ($update_result) {
        echo json_encode([
            'success' => true,
            'message' => 'Attendance updated successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update attendance: ' . $update_stmt->error
        ]);
    }

    $update_stmt->close();
    $check_stmt->close();
    exit;
}


if (isset($_POST['attendance_delete_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("DELETE FROM `attendances` WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*--------------- Employee Advance ---------------*/
if (isset($_GET['show_employee_advance']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $table = 'employee_advances';
    $primaryKey = 'id';

    $columns = array(

        // ID
        array(
            'db' => 'id',
            'dt' => 0
        ),

        // Employee Name
        array(
            'db' => 'employee_id',
            'dt' => 1,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT name 
                    FROM employees 
                    WHERE id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp = $result->fetch_assoc();

                    $name = htmlspecialchars($emp['name'] ?? '---');

                    return '<a href="employee_profile.php?id=' . $employee_id . '" 
                                class="text-primary fw-bold">'
                                . $name .
                            '</a>';
                }

                return '---';
            }
        ),

        // Department
        array(
            'db' => 'employee_id',
            'dt' => 2,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT department.department_name
                    FROM employees
                    LEFT JOIN department 
                    ON department.id = employees.department
                    WHERE employees.id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $data = $result->fetch_assoc();

                    return htmlspecialchars(
                        $data['department_name'] ?? '---'
                    );
                }

                return '---';
            }
        ),

        // Amount
        array(
            'db' => 'amount',
            'dt' => 3,
            'formatter' => function ($d) {

                return number_format((float)$d, 2);
            }
        ),

        // Description
        array(
            'db' => 'description',
            'dt' => 4,
            'formatter' => function ($d) {

                return !empty($d)
                    ? htmlspecialchars($d)
                    : '---';
            }
        ),

        // Advance Date
        array(
            'db' => 'advance_date',
            'dt' => 5,
            'formatter' => function ($d) {

                if (empty($d) || $d == '0000-00-00') {
                    return '---';
                }

                return function_exists('_formate_date')
                    ? _formate_date($d)
                    : date('d M, Y', strtotime($d));
            }
        ),

        // Status
        array(
            'db' => 'status',
            'dt' => 6,
            'formatter' => function ($d) {

                if ($d == 'Approved') {

                    return '<span class="badge bg-success">
                                Approved
                            </span>';
                }

                if ($d == 'Rejected') {

                    return '<span class="badge bg-danger">
                                Rejected
                            </span>';
                }

                return '<span class="badge bg-warning text-dark">
                            Pending
                        </span>';
            }
        ),

        // Approved By
        array(
            'db' => 'approve_by',
            'dt' => 7,
            'formatter' => function ($d, $row) use ($con) {

                if (empty($d)) {
                    return '---';
                }

                $approve_by = intval($d);

                $stmt = $con->prepare("
                    SELECT fullname
                    FROM users
                    WHERE id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $approve_by);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $user = $result->fetch_assoc();

                    return htmlspecialchars(
                        $user['fullname'] ?? '---'
                    );
                }

                return '---';
            }
        ),

        // Approve Date
        array(
            'db' => 'approve_date',
            'dt' => 8,
            'formatter' => function ($d) {

                if (
                    empty($d) ||
                    $d == '0000-00-00' ||
                    $d == '0000-00-00 00:00:00'
                ) {
                    return '---';
                }

                return function_exists('_formate_date')
                    ? _formate_date($d)
                    : date('d M, Y h:i A', strtotime($d));
            }
        ),

        // Action Buttons
        array(
            'db' => 'id',
            'dt' => 9,
            'formatter' => function ($d, $row) {

                $buttons = '';

                $status = $row['status'] ?? '';

                if ($status === 'Pending') {

                    $buttons .= '
                        <button type="button"
                            class="btn btn-sm btn-success"
                            name="approve_btn"
                            data-id="' . intval($d) . '">

                            <i class="fas fa-check"></i> Approve
                        </button>
                    ';

                    $buttons .= '
                        <button type="button"
                            class="btn btn-sm btn-danger"
                            name="reject_button"
                            data-id="' . intval($d) . '">

                            <i class="fas fa-times"></i> Reject
                        </button>
                    ';
                }

                $buttons .= '
                    <button type="button"
                        class="btn btn-sm btn-primary"
                        name="edit_button"
                        data-id="' . intval($d) . '">

                        <i class="fas fa-edit"></i>
                    </button>
                ';

                $buttons .= '
                    <button type="button"
                        class="btn btn-sm btn-danger"
                        name="delete_button"
                        data-id="' . intval($d) . '">

                        <i class="fas fa-trash"></i>
                    </button>
                ';

                return $buttons;
            }
        ),
    );

    /*--------------- Conditions ---------------*/

    $condition = "";

    // From Date
    if (!empty($_GET['from_date'])) {

        $from_date = mysqli_real_escape_string(
            $con,
            $_GET['from_date']
        );

        $condition .= (!empty($condition) ? " AND " : "") .
            "advance_date >= '$from_date'";
    }

    // To Date
    if (!empty($_GET['to_date'])) {

        $to_date = mysqli_real_escape_string(
            $con,
            $_GET['to_date']
        );

        $condition .= (!empty($condition) ? " AND " : "") .
            "advance_date <= '$to_date'";
    }

    // Status
    if (!empty($_GET['status'])) {

        $status = mysqli_real_escape_string(
            $con,
            $_GET['status']
        );

        $condition .= (!empty($condition) ? " AND " : "") .
            "status = '$status'";
    }

    echo json_encode(
        SSP::complex(
            $_GET,
            $sql_details,
            $table,
            $primaryKey,
            $columns,
            null,
            $condition
        )
    );

    exit;
}
/*----------Add Employee Advance Data------------------*/
if (isset($_GET['add_employee_advance']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

   
    $errors = [];
    $time_zone = 'Asia/Dhaka';
   

    $employee_id    = isset($_POST["employee_id"]) ? trim($_POST["employee_id"]) : '';
    $amount         = isset($_POST["amount"]) ? trim($_POST["amount"]) : '';
    $description    = isset($_POST["description"]) ? trim($_POST["description"]) : '';
    $advance_date   = isset($_POST["advance_date"]) ? trim($_POST["advance_date"]) : '';

    /*---------- Basic Validation ----------*/
    if (empty($employee_id)) $errors['employee_id']         = "Employee Name is required.";
    if (empty($amount))   $errors['amount']                 = "Amount is required.";
    if (empty($advance_date))    $errors['advance_date']    = "Advance Date is required.";

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    /*---------- Duplicate Check ----------*/
    $check_stmt = $con->prepare("SELECT id FROM `employee_advances` WHERE employee_id = ? AND advance_date = ? AND status = 'Pending' ");
    $check_stmt->bind_param('is', $employee_id, $advance_date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Employee Advance already exists.'
        ]);
        exit;
    }
    
    /*---------- Insert Query ----------*/
    $stmt = $con->prepare("INSERT INTO employee_advances 
        (employee_id, amount, description, advance_date, status, approve_by, approve_date) 
        VALUES (?, ?, ?, ?, 'Approved', ?, NOW())");

    $stmt->bind_param("isssi", 
        $employee_id, 
        $amount, 
        $description, 
        $advance_date, 
        $_SESSION['uid']
    );
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    exit;
}
if (isset($_GET['get_employee_advance_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $_id = intval($_GET['id']);
    $stmt = $con->prepare("SELECT * FROM `employee_advances` WHERE id = ?");
    $stmt->bind_param("i", $_id);
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
    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*----------Update Employee Advance ------------------*/
if (isset($_GET['update_employee_advance']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
 $errors = [];
    $time_zone = 'Asia/Dhaka';
   

    $employee_id    = isset($_POST["employee_id"]) ? trim($_POST["employee_id"]) : '';
    $amount         = isset($_POST["amount"]) ? trim($_POST["amount"]) : '';
    $description    = isset($_POST["description"]) ? trim($_POST["description"]) : '';
    $advance_date   = isset($_POST["advance_date"]) ? trim($_POST["advance_date"]) : '';

    /*---------- Basic Validation ----------*/
    if (empty($employee_id)) $errors['employee_id']         = "Employee Name is required.";
    if (empty($amount))   $errors['amount']                 = "Amount is required.";
    if (empty($advance_date))    $errors['advance_date']    = "Advance Date is required.";

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    
    /*---------- Update Query ----------*/
    $stmt = $con->prepare("UPDATE employee_advances SET employee_id = ?, amount = ?, description = ?, advance_date = ? WHERE id = ?");

    $stmt->bind_param("isssi", 
        $employee_id, 
        $amount, 
        $description, 
        $advance_date, 
        $_POST['id']
    );
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Update successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    exit;
}
if (isset($_POST['delete_advance_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("DELETE FROM `employee_advances` WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
if (isset($_POST['approve_employee_advance_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("UPDATE `employee_advances` SET status = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Approved successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
if (isset($_POST['reject_employee_advance_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("UPDATE `employee_advances` SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Rejected successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*--------------- Employee Salaries ---------------*/
if (isset($_GET['show_employee_salaries']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $table = 'employee_salaries';
    $primaryKey = 'id';

    $columns = array(

        array('db' => 'id', 'dt' => 0),

        /*-------Profile Image----*/ 
        array(
            'db' => 'id',
            'dt' => 1,
            'formatter' => function ($d, $row) {
                return '<img src="http://103.146.16.154/profileImages/avatar.png"
                            width="40"
                            height="40"
                            class="rounded-circle"/>';
            }
        ),

        /*----- Employee Name----*/
        array(
            'db' => 'employee_id',
            'dt' => 2,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("SELECT name FROM employees WHERE id = ?");
                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp_row = $result->fetch_assoc();

                    $name = htmlspecialchars($emp_row['name'] ?? '---');

                    return '<a href="employee_profile.php?id=' . $employee_id . '" 
                                class="text-primary fw-bold">'
                                . $name .
                            '</a>';
                }

                return '---';
            }
        ),

        /*-------Department-----*/ 
        array(
            'db' => 'employee_id',
            'dt' => 3,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT department.department_name
                    FROM employees
                    LEFT JOIN department 
                    ON department.id = employees.department
                    WHERE employees.id = ?
                ");

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $data = $result->fetch_assoc();

                    return htmlspecialchars($data['department_name'] ?? '---');
                }

                return '---';
            }
        ),

        /*------Designation-----*/ 
        array(
            'db' => 'employee_id',
            'dt' => 4,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("SELECT designation FROM employees WHERE id = ?");
                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp = $result->fetch_assoc();

                    return htmlspecialchars($emp['designation'] ?? '---');
                }

                return '---';
            }
        ),

        /*--------Salary Fields-----*/ 
        array(
            'db' => 'basic_salary',
            'dt' => 5,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        array(
            'db' => 'house_allowance',
            'dt' => 6,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        array(
            'db' => 'medical_allowance',
            'dt' => 7,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        array(
            'db' => 'other_allowance',
            'dt' => 8,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        array(
            'db' => 'tax',
            'dt' => 9,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        array(
            'db' => 'net_salary',
            'dt' => 10,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        /*--------Effective From------*/ 
        array(
            'db' => 'effective_from',
            'dt' => 11,
            'formatter' => function ($d) {

                if (empty($d) || $d == '0000-00-00') {
                    return '---';
                }

                return date('d M, Y', strtotime($d));
            }
        ),

        /*---------Current Status------*/ 
        array(
            'db' => 'is_current',
            'dt' => 12,
            'formatter' => function ($d) {

                if ($d == '1') {
                    return '<span class="badge bg-success">New</span>';
                }

                return '<span class="badge bg-primary">Old</span>';
            }
        ),

    );

    $condition = "";

    echo json_encode(
        SSP::complex(
            $_GET,
            $sql_details,
            $table,
            $primaryKey,
            $columns,
            null,
            $condition
        )
    );

    exit;
}
/*--------------- Employee Loans ---------------*/
if (isset($_GET['show_employee_loan']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $table = 'employee_loans';
    $primaryKey = 'id';

    $columns = array(

        array(
            'db' => 'id',
            'dt' => 0
        ),

        // Employee Image
        array(
            'db' => 'id',
            'dt' => 1,
            'formatter' => function ($d, $row) {

                return '<img src="http://103.146.16.154/profileImages/avatar.png"
                            width="40"
                            height="40"
                            class="rounded-circle"/>';
            }
        ),

        // Employee Name
        array(
            'db' => 'employee_id',
            'dt' => 2,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT name 
                    FROM employees 
                    WHERE id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp_row = $result->fetch_assoc();

                    $name = htmlspecialchars($emp_row['name'] ?? '---');

                    return '<a href="employee_profile.php?id=' . $employee_id . '" 
                                class="text-primary fw-bold">'
                                . $name .
                            '</a>';
                }

                return '---';
            }
        ),

        // Department
        array(
            'db' => 'employee_id',
            'dt' => 3,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT department.department_name
                    FROM employees
                    LEFT JOIN department
                    ON department.id = employees.department
                    WHERE employees.id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $data = $result->fetch_assoc();

                    return htmlspecialchars(
                        $data['department_name'] ?? '---'
                    );
                }

                return '---';
            }
        ),

        // Designation
        array(
            'db' => 'employee_id',
            'dt' => 4,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT designation
                    FROM employees
                    WHERE id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp = $result->fetch_assoc();

                    return htmlspecialchars(
                        $emp['designation'] ?? '---'
                    );
                }

                return '---';
            }
        ),

        // Loan Amount
        array(
            'db' => 'loan_amount',
            'dt' => 5,
            'formatter' => function ($d) {

                return number_format((float)$d, 2);
            }
        ),

        // Installment Amount
        array(
            'db' => 'installment_amount',
            'dt' => 6,
            'formatter' => function ($d) {

                return number_format((float)$d, 2);
            }
        ),

        // Total Installments
        array(
            'db' => 'total_installments',
            'dt' => 7,
            'formatter' => function ($d) {

                return intval($d);
            }
        ),

        // Paid Installments
        array(
            'db' => 'paid_installments',
            'dt' => 8,
            'formatter' => function ($d) {

                return intval($d);
            }
        ),

        // Date Issued
        array(
            'db' => 'date_issued',
            'dt' => 9,
            'formatter' => function ($d) {

                if (empty($d) || $d == '0000-00-00') {
                    return '---';
                }

                return date('d M, Y', strtotime($d));
            }
        ),

        // Status
        array(
            'db' => 'status',
            'dt' => 10,
            'formatter' => function ($d) {

                if ($d == 'Approved') {

                    return '<span class="badge bg-success">
                                Approved
                            </span>';
                }

                if ($d == 'Pending') {

                    return '<span class="badge bg-warning text-dark">
                                Pending
                            </span>';
                }

                if ($d == 'Rejected') {

                    return '<span class="badge bg-danger">
                                Rejected
                            </span>';
                }

                return '<span class="badge bg-secondary">
                            Unknown
                        </span>';
            }
        ),

        // Created At
        array(
            'db' => 'created_at',
            'dt' => 11,
            'formatter' => function ($d) {

                if (empty($d)) {
                    return '---';
                }

                return function_exists('_formate_date')
                    ? _formate_date($d)
                    : date('d M, Y h:i A', strtotime($d));
            }
        ),

        // Approved By
        array(
            'db' => 'approved_by',
            'dt' => 12,
            'formatter' => function ($d, $row) use ($con) {

                if (empty($d)) {
                    return '---';
                }

                $approved_by = intval($d);

                $stmt = $con->prepare("
                    SELECT fullname
                    FROM users
                    WHERE id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $approved_by);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $user = $result->fetch_assoc();

                    return htmlspecialchars(
                        $user['fullname'] ?? '---'
                    );
                }

                return '---';
            }
        ),

        // Approved Date
        array(
            'db' => 'approved_date',
            'dt' => 13,
            'formatter' => function ($d) {

                if (empty($d) || $d == '0000-00-00 00:00:00') {
                    return '---';
                }

                return function_exists('_formate_date')
                    ? _formate_date($d)
                    : date('d M, Y h:i A', strtotime($d));
            }
        ),

        // Action Buttons
        array(
            'db' => 'id',
            'dt' => 14,
            'formatter' => function ($d, $row) {

                $buttons = '';

                $status = $row['status'] ?? '';

                if ($status === 'Pending') {

                    $buttons .= '
                        <button type="button"
                            class="btn btn-sm btn-success"
                            name="approve_btn"
                            data-id="' . intval($d) . '">

                            <i class="fas fa-check"></i> Approve
                        </button>
                    ';

                    $buttons .= '
                        <button type="button"
                            class="btn btn-sm btn-danger"
                            name="reject_button"
                            data-id="' . intval($d) . '">

                            <i class="fas fa-times"></i> Reject
                        </button>
                    ';
                }

                $buttons .= '
                    <a href="employee_loan_edit.php?id=' . intval($d) . '"
                        class="btn btn-sm btn-primary">

                        <i class="fas fa-edit"></i>
                    </a>
                ';

                $buttons .= '
                    <button type="button"
                        class="btn btn-sm btn-danger"
                        name="delete_button"
                        data-id="' . intval($d) . '">

                        <i class="fas fa-trash"></i>
                    </button>
                ';

                return $buttons;
            }
        ),
    );

    $condition = "";

    echo json_encode(
        SSP::complex(
            $_GET,
            $sql_details,
            $table,
            $primaryKey,
            $columns,
            null,
            $condition
        )
    );

    exit;
}
/*---------------Add Employee Loans ---------------*/
if (isset($_GET['add_employee_loan']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];
    $time_zone = 'Asia/Dhaka';
   

    $employee_id        = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
    $loan_amount        = isset($_POST['loan_amount']) ? floatval($_POST['loan_amount']) : 0;
    $installment_amount = isset($_POST['installment_amount']) ? floatval($_POST['installment_amount']) : 0;
    $total_installments = isset($_POST['total_installments']) ? intval($_POST['total_installments']) : 0;
    $paid_installments  = isset($_POST['paid_installments']) ? intval($_POST['paid_installments']) : 0;
    $date_issued        = isset($_POST['date_issued']) ? $_POST['date_issued'] : null;

    /*---------- Basic Validation ----------*/
    if (empty($employee_id)) {
        $errors['employee_id'] = "Employee Name is required.";
    }
    if (empty($loan_amount)) {
        $errors['amount'] = "Amount is required.";
    }
    if (empty($installment_amount)) {
        $errors['installment_amount'] = "Installment Amount is required.";
    }
    if (empty($total_installments)) {
        $errors['total_installments'] = "Total Installment Amount is required.";
    }
    if (empty($paid_installments)) {
        $errors['paid_installments'] = "Paid Installment Amount is required.";
    }
    if (empty($date_issued)) {
        $errors['issue_date'] = "Issue Date is required.";
    }

    /*---------- Duplicate Check ----------*/
    $check_stmt =  $con->prepare("
                        SELECT id 
                        FROM employee_loans
                        WHERE employee_id = ?
                        AND status IN ('Pending','Approved')
                        LIMIT 1
                    ");
    $check_stmt->bind_param('i', $employee_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Employee Loan already exists.'
        ]);
        exit;
    }
    
    /*---------- Insert Query ----------*/
    $stmt = $con->prepare("INSERT INTO employee_loans(employee_id, loan_amount, installment_amount, total_installments, paid_installments, date_issued, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), NOW())");

    $stmt->bind_param(
            "iddiis",
            $employee_id,
            $loan_amount,
            $installment_amount,
            $total_installments,
            $paid_installments,
            $date_issued
        );
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    exit;
}

/*---------------Approve Employee Loans ---------------*/
if (isset($_POST['approve_employee_loan_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id             = intval($_POST['id']);
    $approve_by     = $_SESSION['uid'];
    $approve_date   = date('Y-m-d'); 

    $stmt = $con->prepare("UPDATE employee_loans 
                           SET status = 'Approved', 
                               approved_by = ?, 
                               approved_date = ? 
                           WHERE id = ?");

    $stmt->bind_param("isi", $approve_by, $approve_date, $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Approved successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*---------------Reject Employee Loans ---------------*/
if (isset($_POST['reject_employee_loan_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("UPDATE `employee_loans` SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Rejected successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*--------------- Update Employee Loans ---------------*/
if (isset($_GET['update_employee_loan']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];

    $id                 = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $employee_id        = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
    $loan_amount        = isset($_POST['loan_amount']) ? floatval($_POST['loan_amount']) : 0;
    $installment_amount = isset($_POST['installment_amount']) ? floatval($_POST['installment_amount']) : 0;
    $total_installments = isset($_POST['total_installments']) ? intval($_POST['total_installments']) : 0;
    $paid_installments  = isset($_POST['paid_installments']) ? intval($_POST['paid_installments']) : 0;
    $date_issued        = isset($_POST['date_issued']) ? $_POST['date_issued'] : null;

    /*---------- Basic Validation ----------*/
    if (empty($employee_id)) {
        $errors['employee_id'] = "Employee Name is required.";
    }

    if (empty($loan_amount)) {
        $errors['loan_amount'] = "Loan Amount is required.";
    }

    if (empty($installment_amount)) {
        $errors['installment_amount'] = "Installment Amount is required.";
    }

   

    if (empty($date_issued)) {
        $errors['date_issued'] = "Issue Date is required.";
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }

    /*---------- Duplicate Check ----------*/
    $check_stmt = $con->prepare("
                    SELECT id
                    FROM employee_loans
                    WHERE employee_id = ?
                    AND status IN ('Pending','Approved')
                    AND id != ?
                    LIMIT 1
                ");

    $check_stmt->bind_param("ii", $employee_id, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {

        echo json_encode([
            'success' => false,
            'message' => 'Employee already has an active loan.'
        ]);

        exit;
    }

    /*---------- Update Query ----------*/
    $stmt = $con->prepare("
        UPDATE employee_loans
        SET
            employee_id = ?,
            loan_amount = ?,
            installment_amount = ?,
            total_installments = ?,
            paid_installments = ?,
            date_issued = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->bind_param(
                "iddiisi",
                $employee_id,
                $loan_amount,
                $installment_amount,
                $total_installments,
                $paid_installments,
                $date_issued,
                $id
            );

    $result = $stmt->execute();

    if ($result) {

        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!'
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => $stmt->error
        ]);

    }

    $stmt->close();
    exit;
}
/*--------------- Employee Payroll List ---------------*/
if (isset($_GET['show_employee_payroll']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $table = 'employee_payrolls';
    $primaryKey = 'id';

    $columns = array(

        array(
            'db' => 'id',
            'dt' => 0
        ),

        /*--------Employee Image-----*/ 
        array(
            'db' => 'id',
            'dt' => 1,
            'formatter' => function ($d, $row) {

                return '<img src="http://103.146.16.154/profileImages/avatar.png"
                            width="40"
                            height="40"
                            class="rounded-circle"/>';
            }
        ),

        /*----------Employee Name------*/ 
        array(
            'db' => 'employee_id',
            'dt' => 2,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("SELECT name FROM employees WHERE id = ?");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp_row = $result->fetch_assoc();

                    $name = htmlspecialchars($emp_row['name'] ?? '---');

                    return '<a href="employee_profile.php?id=' . $employee_id . '" 
                                class="text-primary fw-bold">'
                                . $name .
                            '</a>';
                }

                return '---';
            }
        ),

        /*------- Department-----*/
        array(
            'db' => 'employee_id',
            'dt' => 3,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT department.department_name
                    FROM employees
                    LEFT JOIN department 
                    ON department.id = employees.department
                    WHERE employees.id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $data = $result->fetch_assoc();

                    return htmlspecialchars($data['department_name'] ?? '---');
                }

                return '---';
            }
        ),

        /*--------Designation-----_*/ 
        array(
            'db' => 'employee_id',
            'dt' => 4,
            'formatter' => function ($d, $row) use ($con) {

                $employee_id = intval($d);

                if ($employee_id <= 0) {
                    return '---';
                }

                $stmt = $con->prepare("
                    SELECT designation 
                    FROM employees 
                    WHERE id = ?
                ");

                if (!$stmt) {
                    return '---';
                }

                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $emp = $result->fetch_assoc();

                    return htmlspecialchars($emp['designation'] ?? '---');
                }

                return '---';
            }
        ),

        /*-------------Month Year-------*/ 
        array(
            'db' => 'month_year',
            'dt' => 5,
            'formatter' => function ($d) {

                if (empty($d)) {
                    return '---';
                }

                return htmlspecialchars($d);
            }
        ),

        /*--------Basic Salary------*/ 
        array(
            'db' => 'basic_salary',
            'dt' => 6,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        /*----------Advance Salary------*/ 
        array(
            'db' => 'advance_salary',
            'dt' => 7,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        /*----------Loan Deduction------*/ 
        array(
            'db' => 'loan_deduction',
            'dt' => 8,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        /*--------Tax---------*/ 
        array(
            'db' => 'tax',
            'dt' => 9,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        /*--------Net Salary------*/ 
        array(
            'db' => 'net_salary',
            'dt' => 10,
            'formatter' => function ($d) {
                return number_format((float)$d, 2);
            }
        ),

        /*---------Payment Date------*/ 
        array(
            'db' => 'payment_date',
            'dt' => 11,
            'formatter' => function ($d) {

                if (empty($d) || $d == '0000-00-00') {
                    return '---';
                }

                return date('d M, Y', strtotime($d));
            }
        ),

        /*------------Payment Method-------*/ 
        array(
            'db' => 'payment_method',
            'dt' => 12,
            'formatter' => function ($d) {

                return !empty($d)
                    ? htmlspecialchars($d)
                    : '---';
            }
        ),

        /*-----------Status--------*/ 
        array(
            'db' => 'status',
            'dt' => 13,
            'formatter' => function ($d) {

                if ($d == 'Paid') {

                    return '<span class="badge bg-success">
                                Paid
                            </span>';
                }

                if ($d == 'Unpaid') {

                    return '<span class="badge bg-danger">
                                Unpaid
                            </span>';
                }

                return '<span class="badge bg-secondary">
                            Unknown
                        </span>';
            }
        ),

        /*-------Created At------*/ 
        array(
            'db' => 'created_at',
            'dt' => 14,
            'formatter' => function ($d) {

                if (empty($d)) {
                    return '---';
                }

                return function_exists('_formate_date')
                    ? _formate_date($d)
                    : date('d M, Y h:i A', strtotime($d));
            }
        ),

        /*-----------Action Buttons-------*/ 
        array(
            'db' => 'id',
            'dt' => 15,
            'formatter' => function ($d, $row) {

                $d = intval($d);

                $buttons = '';

                $buttons .= '
                    <a href="employee_payroll_view.php?id=' . $d . '"
                        class="btn btn-sm btn-success">
                        <i class="fas fa-eye"></i>
                    </a>
                ';

                $buttons .= '
                    <button type="button"
                        class="btn btn-sm btn-danger"
                        name="delete_button"
                        data-id="' . $d . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';

                return $buttons;
            }
        ),
    );

    $condition = "";

    echo json_encode(
        SSP::complex(
            $_GET,
            $sql_details,
            $table,
            $primaryKey,
            $columns,
            null,
            $condition
        )
    );

    exit;
}
if (isset($_POST['delete_employee_payroll_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("DELETE FROM `employee_payrolls` WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*------------------------Get Employee Salary---------------*/
if (isset($_GET['get_employee_salary']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
        
    $result = $con->query("SELECT *  FROM employee_salaries  WHERE employee_id = $employee_id LIMIT 1");

    if ($result && $result->num_rows > 0) {

        $data = $result->fetch_assoc();
       $total_allowance =
        ($data['house_allowance'] ?? 0) +
        ($data['medical_allowance'] ?? 0) +
        ($data['other_allowance'] ?? 0);

        echo json_encode([
            'basic_salary'      => $data['basic_salary'],
            'total_allowance'   => $total_allowance,
            'tax'               => $data['tax'],
            'loan_deduction'    => _get_employee_loan_installment($con, $employee_id),
        ]);

    } else {

        echo json_encode([
            'basic_salary' => 0,
            'total_allowance' => 0,
            'tax' => 0, 
            'loan_deduction' => 0,
        ]);
    }

    exit;
}
/*------------------------Get Employee Advance Salary By Month---------------*/
if (isset($_POST['get_advance_salary_by_month']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
    $month_year  = isset($_POST['month_year']) ? $_POST['month_year'] : '';

    if ($employee_id <= 0 || empty($month_year)) {
        echo json_encode(['total_advance' => 0]);
        exit;
    }

    /*----------Get Employee Loan Info-----------------*/
    $result = $con->query("SELECT * FROM employee_advances WHERE employee_id = $employee_id LIMIT 1");
    $employee_advance_info = $result->fetch_assoc();
    $result->free();


    
    $stmt = $con->prepare("
        SELECT SUM(amount) as total_advance
        FROM employee_advances
        WHERE employee_id = ?
        AND status = 'Approved'
        AND DATE_FORMAT(advance_date, '%Y-%m') = ?
    ");

    if ($stmt) {
        $stmt->bind_param("is", $employee_id, $month_year);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        echo json_encode([
            'total_advance' => $row['total_advance'] ?? 0, 
        ]);
    } else {
        // query prepare failed
        echo json_encode(['total_advance' => 0]);
    }

    exit;
}
if (isset($_GET['employee_payroll_create']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $con->begin_transaction();

    try {

        /*----------- Input -----------*/
        $employee_id     = intval($_POST['employee_id']);
        $month_year      = $_POST['month_year'];
        $payment_method  = $_POST['payment_method'];
        $payment_date    = $_POST['payment_date'];
        $status          = $_POST['status'];

        $basic_salary    = floatval($_POST['basic_salary']);
        $allowances      = floatval($_POST['allowances'] ?? 0);
        $tax             = floatval($_POST['tax']);
        $advance_salary  = floatval($_POST['advance_salary']);
        $loan_deduction  = floatval($_POST['loan_deduction']);
        $net_salary      = floatval($_POST['net_salary']);

        /* ----------- Duplicate payroll check -----------*/
        $check = $con->query("
            SELECT id FROM employee_payrolls 
            WHERE employee_id = $employee_id 
            AND month_year = '$month_year'
            LIMIT 1
        ");

        if ($check->num_rows > 0) {
            throw new Exception("Payroll already exists for this month");
        }

        /*------------Get Salary Info-----------*/
        $stmt = $con->prepare("SELECT id FROM employee_salaries WHERE is_current = 1 AND employee_id = ? LIMIT 1");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();

        $result = $stmt->get_result();

        $get_salary_id = null;

        if ($row = $result->fetch_assoc()) {
            $get_salary_id = $row['id'];
        }
       

        /*----------- Insert Payroll -----------*/
        $stmt = $con->prepare("
            INSERT INTO employee_payrolls 
            (employee_id, salary_id, month_year, basic_salary, advance_salary, loan_deduction, tax, net_salary, payment_date, payment_method, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param(
            "iisddddssss",
            $employee_id,
            $get_salary_id,
            $month_year,
            $basic_salary,
            $advance_salary,
            $loan_deduction,
            $tax,
            $net_salary,
            $payment_date,
            $payment_method,
            $status
        );

        $stmt->execute();

        /*----------- Loan Handling-----------*/
        if ($loan_deduction > 0) {

            /*-----------Insert installment-----------*/
            $stmt2 = $con->prepare("
                INSERT INTO employee_loan_installments 
                (employee_id, amount, installment_date)
                VALUES (?, ?, ?)
            ");

            $stmt2->bind_param("ids", $employee_id, $loan_deduction, $payment_date);
            $stmt2->execute();

            /*----------- Update paid_installments --------------*/
            $con->query("
                UPDATE employee_loans 
                SET paid_installments = paid_installments + 1
                WHERE employee_id = $employee_id
            ");
            $loan = $con->query("
                SELECT loan_amount 
                FROM employee_loans 
                WHERE employee_id = $employee_id
                LIMIT 1
            ")->fetch_assoc();

            $paid = $con->query("
                SELECT COALESCE(SUM(amount),0) as total_paid
                FROM employee_loan_installments
                WHERE employee_id = $employee_id
            ")->fetch_assoc()['total_paid'];
        }
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Payroll created successfully'
        ]);

    } catch (Exception $e) {

        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
/*-----------Add Employee Daily Task-----------------*/
if (isset($_GET['add_employee_daily_task']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $con->begin_transaction();

    try {

        /*----------- Input -----------*/
        $employee_id        = intval($_POST['employee_id']);
        $task_description   = $_POST['task_description'] ?? '';
        $satisfaction_level = intval($_POST['satisfaction_level'] ?? 0);
        $vision_target      = intval($_POST['vision_target'] ?? 0); 
        $status             = "Active"; 
        $created_at         = date('Y-m-d H:i:s');

        if (empty($task_description) || $satisfaction_level === 0 || $vision_target === 0) {
            throw new Exception("All fields are required.");
        }

        /*----------- Insert Statement -----------*/
        $stmt = $con->prepare("
            INSERT INTO daily_task_updates 
            (employee_id, task_description, satisfaction_level, vision_target, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $con->error);
        }

        $stmt->bind_param(
            "isiiss",
            $employee_id,
            $task_description,
            $satisfaction_level,
            $vision_target,
            $status,
            $created_at
        );

        $stmt->execute();
        $stmt->close();
        
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Daily task update added successfully.'
        ]);

    } catch (Exception $e) {

        $con->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if (isset($_GET['get_employee_daily_task_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $_id = intval($_GET['id']);
    $stmt = $con->prepare("SELECT * FROM `daily_task_updates` WHERE id = ?");
    $stmt->bind_param("i", $_id);
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
    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/*-----------Update Employee Daily Task-----------------*/
if (isset($_GET['update_empoyee_daily_task']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $con->begin_transaction();
    try {
        /*----------- Input Handling -----------*/
        $task_id            = intval($_POST['task_id']);
        $task_description   = $_POST['task_description'] ?? '';
        $satisfaction_level = intval($_POST['satisfaction_level'] ?? 0);
        $vision_target      = intval($_POST['vision_target'] ?? 0);
        $status             = $_POST['edit_status']; 

        /*---------Validation Check----------*/ 
        if ($task_id === 0 || empty($task_description) || $satisfaction_level === 0 || $vision_target === 0) {
            throw new Exception("Invalid input data or missing fields.");
        }

        /*----------- Update Statement -----------*/
        $stmt = $con->prepare("
            UPDATE daily_task_updates 
            SET task_description = ?, satisfaction_level = ?, vision_target = ?, status=?
            WHERE id = ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $con->error);
        }

        $stmt->bind_param("siisi", $task_description, $satisfaction_level, $vision_target, $status, $task_id);

        $stmt->execute();
        $stmt->close();
        
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Daily task update modified successfully.'
        ]);

    } catch (Exception $e) {
        $con->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
/*-----------Delete Employee Daily Task-----------------*/
if (isset($_POST['delete_employee_daily_task_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("DELETE FROM `daily_task_updates` WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
/**----------------------------------Employee Task Management----------------------------------*/
if(isset($_GET['show_employee_task']) && $_SERVER['REQUEST_METHOD'] == 'GET'){
     $table = 'employee_tasks';
    $primaryKey = 'id';

    $columns = array(

        array('db' => 'id', 'dt' => 0),
        array(
            'db' => 'title',
            'dt' => 1, 
            'formatter' => function($d, $row) {
                return '<a href="employee_task_profile.php?id=' . $row['id'] . '" class="text-primary fw-bold">' . $d . '</a>'; 
            }
        ),
       array(
            'db' => 'description',
            'dt' => 2,
            'formatter' => function($d, $row) {

                // ensure string
                $text = isset($d) ? (string)$d : '';

                $short = (mb_strlen($text) > 30) 
                    ? mb_substr($text, 0, 30) . '...' 
                    : $text;

                return '<span title="'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'">'.$short.'</span>';
            }
        ),
      

        array(
            'db' => 'assigned_by',
            'dt' => 3,
            'formatter' => function($d, $row) use ($con) {
                $employee_id = intval($d); 
                $all_employee = $con->query("SELECT fullname FROM users WHERE id=$employee_id");
                $emp_row = $all_employee->fetch_array();
                
                if ($emp_row) {
                    $name = htmlspecialchars($emp_row['fullname']);
                    return '<a href="users_profile.php?id=' . $employee_id . '" class="text-primary fw-bold">' . $name . '</a>';
                } else {
                    return '---';
                }
            }
        ),
        array(
            'db' => 'assigned_to',
            'dt' => 4,
            'formatter' => function($d, $row) use ($con) {
                $employee_id = intval($d); 
                $all_employee = $con->query("SELECT name FROM employees WHERE id=$employee_id");
                $emp_row = $all_employee->fetch_array();
                
                if ($emp_row) {
                    $name = htmlspecialchars($emp_row['name']);
                    return '<a href="employee_profile.php?id=' . $employee_id . '" class="text-primary fw-bold">' . $name . '</a>';
                } else {
                    return '---';
                }
            }
        ),
        array(
            'db' => 'id',
            'dt' => 5,
            'formatter' => function($d, $row) use ($con) {

                $task_id = (int)$d;

                $query = "
                    SELECT feedback 
                    FROM employee_task_feedback 
                    WHERE task_id = $task_id 
                    ORDER BY create_date DESC 
                    LIMIT 1
                ";

                $result = $con->query($query);

                $feedback_text = '';

                if ($result && $result->num_rows > 0) {
                    $feedback = $result->fetch_assoc();
                    if (!empty($feedback['feedback'])) {
                        $feedback_text = (string)$feedback['feedback'];
                    }
                }

                if ($feedback_text !== '') {

                    $short_feedback = mb_strlen($feedback_text) > 30 
                        ? mb_substr($feedback_text, 0, 30) . '...' 
                        : $feedback_text;

                    return '
                        <div>
                            <span>'.htmlspecialchars($short_feedback, ENT_QUOTES, 'UTF-8').'</span>
                            <button class="btn btn-sm btn-info view-feedback ms-2" data-id="'.$task_id.'">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    ';
                }

                return '<span class="text-muted">No feedback</span>';
            }
        ),
        
        array(
            'db' => 'priority',
            'dt' => 6,
            'formatter' => function($d){
                if($d == 'High'){
                    return '<span class="badge bg-success">High</span>';
                }elseif($d == 'Medium'){
                    return '<span class="badge bg-success">Medium</span>';
                }elseif($d == 'Low'){
                    return '<span class="badge bg-danger">Low</span>';
                }
            }
        ),
        array(
            'db' => 'status',
            'dt' => 7,
            'formatter' => function($d){
                if($d == 'Pending'){
                    return '<span class="badge bg-danger">Pending</span>';
                }elseif($d == 'In Progress'){
                    return '<span class="badge bg-warning">In Progress</span>';
                }elseif($d == 'Completed'){
                    return '<span class="badge bg-success">Completed</span>';
                }elseif($d == 'Cancelled'){
                    return '<span class="badge bg-danger">Cancelled</span>';
                }
                return '<span class="badge bg-dark">'.$d.'</span>';
            }
        ),
        array(
            'db' => 'created_at',
            'dt' => 8,
            'formatter' => function($d){
                //return _formate_date($d);
                return date('d-m-Y', strtotime($d));
            }
        ),
        
        array(
            'db' => 'id',
            'dt' => 9,
              'formatter' => function($d, $row){

                $buttons = '';
                if ($row['status'] !== 'Completed') {
                    $buttons .= '
                        <button type="button" 
                            class="btn btn-sm btn-success"
                            name="task_completed_btn" 
                            data-id="'.$d.'">
                            <i class="fas fa-check"></i> Completed
                        </button>
                    ';
                }

                $buttons .= '
                    <button type="button" 
                        class="btn btn-sm btn-danger"
                        name="delete_button" 
                        data-id="'.$d.'">
                        <i class="fas fa-trash"></i>
                    </button>';
                $buttons .= '
                    <button type="button" 
                        class="btn btn-sm btn-primary"
                        name="edit_button" 
                        data-id="'.$d.'">
                        <i class="fas fa-edit"></i>
                    </button>';

                return $buttons;
            }
        ),
    );

    $condition = "";
    // if(isset($_GET['status']) && $_GET['status'] != ''){
    //     $status = $_GET['status'];
    //     $condition .= (!empty($condition) ? " AND " : "") . "status = '" . $status . "'";
    // }
    if(isset($_GET['status']) && $_GET['status'] != ''){

        $allowed_status = ['Pending','In Progress','Completed','Cancelled'];

        $status = $_GET['status'];

        if(in_array($status, $allowed_status)){
            $condition = "status = '".$con->real_escape_string($status)."'";
        }
    }

   

    echo json_encode(
        SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $condition)
    );
    exit;   
}
if (isset($_GET['create_employee_task']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $con->begin_transaction();

    try {

        /*----------- Input -----------*/
        $title           = $_POST['title'];
        $description     = $_POST['description'];
        $assigned_by     = intval($_POST['assigned_by']);
        $assigned_to     = intval($_POST['assigned_to']);
        $priority        = $_POST['priority'];
        $status          = "Pending";
        $create_date     = $_POST['create_date'];



        /*----------- Insert Task -----------*/
        $stmt = $con->prepare("
            INSERT INTO employee_tasks 
            (title, description, assigned_by, assigned_to, priority, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssiisss",
            $title,
            $description,
            $assigned_by,
            $assigned_to,
            $priority,
            $status,
            $create_date
        );

        $stmt->execute();
        $stmt->close();
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Task created successfully'
        ]);

    } catch (Exception $e) {

        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if (isset($_GET['get_employee_task_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $_id = intval($_GET['id']);
    $stmt = $con->prepare("SELECT * FROM `employee_tasks` WHERE id = ?");
    $stmt->bind_param("i", $_id);
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
    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
if (isset($_GET['update_employee_task']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $con->begin_transaction();

    try {

        /*----------- Input -----------*/
        $id             = $_POST['id'];
        $title           = $_POST['title'];
        $description     = $_POST['description'];
        $assigned_by     = intval($_POST['assigned_by']);
        $assigned_to     = intval($_POST['assigned_to']);
        $priority        = $_POST['priority'];
        $status          = $_POST['status'];
        $create_date     = $_POST['create_date'];



        /*----------- Update Task -----------*/
        $stmt = $con->prepare("
            UPDATE employee_tasks 
            SET 
                title = ?, 
                description = ?, 
                assigned_by = ?, 
                assigned_to = ?, 
                priority = ?, 
                status = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ssiissi",
            $title,
            $description,
            $assigned_by,
            $assigned_to,
            $priority,
            $status,
            $id
        );

        $stmt->execute();
        $stmt->close();
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Task Update successfully'
        ]);

    } catch (Exception $e) {

        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if (isset($_POST['delete_employee_task_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("DELETE FROM `employee_tasks` WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Record deleted successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
if(isset($_GET['get_task_feedback']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $task_id = (int)$_POST['task_id'];

    $query = "SELECT feedback, create_date 
        FROM employee_task_feedback 
        WHERE task_id = $task_id 
        ORDER BY create_date DESC";

    $result = $con->query($query);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo '<div class="border rounded p-2 mb-2">';
            echo '<p class="mb-1">'.htmlspecialchars($row['feedback']).'</p>';
            echo '<small class="text-muted">'.date("d M Y, h:i A", strtotime($row['create_date'])).'</small>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-muted">No feedback found</p>';
    }
    exit; 

}
/*---------------Completed Employee Task ---------------*/
if (isset($_POST['completed_employee_task_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id             = intval($_POST['id']);

    $stmt = $con->prepare("UPDATE  employee_tasks 
                           SET status = 'Completed'
                           WHERE id = ?");

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array("success" => true, "message" => "Completed successfully!");
        } else {
            $response = array("success" => false, "message" => "No record found with the provided ID!");
        }
    } else {
        $response = array("success" => false, "message" => "Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $con->close();
    echo json_encode($response);
    exit;
}
function _get_employee_loan_installment($con, $employee_id){

    $employee_id = intval($employee_id);

    if ($employee_id <= 0) {
        return 0;
    }

    /*--------- Loan Info ----------*/
    $loan = $con->query("
        SELECT id, loan_amount, installment_amount, status
        FROM employee_loans
        WHERE employee_id = $employee_id
        LIMIT 1
    ")->fetch_assoc();

    if (!$loan || $loan['status'] == 'Completed') {
        return 0;
    }

    /*--------- Total Paid ----------*/
    $paid_row = $con->query("
        SELECT COALESCE(SUM(amount),0) as total_paid
        FROM employee_loan_installments
        WHERE employee_id = $employee_id
    ")->fetch_assoc();

    $total_paid = $paid_row['total_paid'];

    /*------------Remaining ------------*/
    $remaining = $loan['loan_amount'] - $total_paid;

    $monthly_installment = $loan['installment_amount'];

    /*----------Loan Deduction --------------*/
    if ($remaining <= 0) {
        return 0;
    } elseif ($remaining < $monthly_installment) {
        return $remaining;
    } else {
        return $monthly_installment;
    }
}
function _formatTime($d) {
    if (empty($d) || $d == '0000-00-00 00:00:00' || $d=='00:00:00' || $d==null) {
        return '---';
    }
    return date('h:i A', strtotime($d));
}
function _formate_date($d){
    if (empty($d) || $d == '0000-00-00 00:00:00' || $d=='00:00:00' || $d==null) {
        return '---';
    }
    return date('d-m-Y', strtotime($d));
}