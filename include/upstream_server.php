<?php 
include "db_connect.php";
//   ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/*--------- Add Upstream  Script -----------*/
if (isset($_GET['add_upstream_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    /**-----------Check Upstream  Name is exist-----------**/
    $check_ = $con->query("SELECT * FROM upstream WHERE `name`='$name'");
    if ($check_->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Upstream Already exists!',
        ]);
        exit();
    }

    /*----------- Validate Upstream name -----------*/
    __validate_input($name, 'Upstream Name');
    /*----------- Insert into Upstream table -----------*/
    $result = $con->query("INSERT INTO upstream(`name`) VALUES('$name')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Upstream Added successfully!',
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
if (isset($_GET['get_upstream_data'])) {
    $id = intval($_GET['id']);
    $result = $con->query("SELECT * FROM upstream WHERE id = $id");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode([
        'success' => true,
        'data' => $data[0] ?? [],
    ]);
    exit();
}
/*----------- Update Upstream  Script -----------*/
if (isset($_GET['update_upstream_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    

     /*----------- Validate Upstream name -----------*/
    __validate_input($name, 'Upstream Name');
    /*----------- Insert into Upstream table -----------*/
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("UPDATE upstream SET name='$name' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Upstream updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Pool!',
        ]);
        exit();
    }
}

/*-----------Delete  Script-----------*/
if (isset($_GET['delete_upstream_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM upstream WHERE id='$id'");
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


