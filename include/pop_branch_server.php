<?php 
include 'db_connect.php';

/******** Add POP Branch Script ******************/
if (isset($_GET['add_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $pop_branch = trim($_POST['pop_branch_name']);

    /* Validate POP Branch */
    __validate_input($pop_branch, 'POP Branch');
    
    /* Insert into  table */
    $result = $con->query("INSERT INTO pop_branch(`name`) VALUES('$pop_branch')");
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

/******** Update POP Branch Script ******************/
if (isset($_GET['update_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $pop_branch = trim($_POST['pop_branch_name']);
    $id = trim($_POST['id']);
    /* Validate POP Branch */
    __validate_input($pop_branch, 'POP Branch');
    /* Check if POP Branch already exists */
    $check_pop_branch = $con->query("SELECT * FROM pop_branch WHERE name='$pop_branch' AND id != '$id'");
    if ($check_pop_branch->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'POP Branch Already exists!',
        ]);
        exit();
    }
    /* Update the POP Branch in the database */
    $result = $con->query("UPDATE pop_branch SET name='$pop_branch' WHERE id='$id'");
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
/*Delete POP Branch Script*/
if (isset($_GET['delete_pop_branch_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM pop_branch WHERE id='$id'");
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