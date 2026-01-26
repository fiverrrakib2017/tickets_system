<?php 

 include("db_connect.php");
/******** Add Value Added Service Script ******************/
if (isset($_GET['add_value_add_service']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = trim($_POST['name']);
    $service_link = trim($_POST['link']);

    /* Validate Service Name */
    __validate_input($service_name, 'Service Name');

    
    /* Insert into  table */
    $result = $con->query("INSERT INTO value_added_service(`service_name`, `service_link`) VALUES( '$service_name', '$service_link')");
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
if (isset($_GET['update_value_add_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    $service_name = trim($_POST['name']);
    $service_link = trim($_POST['link']);

    
    /* Validate Service Name */
    __validate_input($service_name, 'Service Name');
    /* Check if service already exists */
    $check_service = $con->query("SELECT * FROM value_added_service WHERE service_name='$service_name' AND id != '$id'");
    if ($check_service->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Service Already exists!',
        ]);
        exit();
    }
    /* Update the service in the database */
    $result = $con->query("UPDATE value_added_service SET `service_name`='$service_name', `service_link`='$service_link' WHERE id='$id'");
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

if (isset($_GET['get_value_add_service']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM value_added_service WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM value_added_service");
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
if (isset($_GET['delete_value_add_service_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM value_added_service WHERE id='$id'");
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