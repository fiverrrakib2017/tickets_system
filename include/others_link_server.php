<?php
include 'db_connect.php';
/******** Add Others Link  Script ******************/
if (isset($_GET['add_others_link_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $link_name = trim($_POST['name']);
    $link_url = trim($_POST['link']);

    /* Validate Link Name  name */
    __validate_input($link_name, 'Link Name');
    /* Insert into  table */
    $result = $con->query("INSERT INTO others_link(name, link) VALUES('$link_name', '$link_url')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Link!',
        ]);
        exit();
    }
}
/******** Update others link  Script ******************/
if (isset($_GET['update_others_link_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $link_name = trim($_POST['name']);
    $link_url = trim($_POST['link']);
    $id = trim($_POST['id']);
    /* Validate Link Name  name */
    __validate_input($link_name, 'Link Name');
    /* Check if link name already exists */
    $check_link_name = $con->query("SELECT * FROM others_link WHERE name='$link_name' AND id != '$id'");
    if ($check_link_name->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Link Name Already exists!',
        ]);
        exit();
    }
    /* Update the link in the database */
    $result = $con->query("UPDATE others_link SET name='$link_name', link='$link_url' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Link!',
        ]);
        exit();
    }
}

if (isset($_GET['get_others_link_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM others_link WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM others_link");
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
/*Delete others link Script*/
if (isset($_GET['delete_others_link_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM others_link WHERE id='$id'");
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
