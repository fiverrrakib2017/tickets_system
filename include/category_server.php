<?php 
include "db_connect.php";
  
/******** Add Category  Script ******************/
if (isset($_GET['add_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    /**Check Area Name is exist**/
    $check_ = $con->query("SELECT * FROM ticket_categories WHERE name='$name'");
    if ($check_->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Category Already exists!',
        ]);
        exit();
    }

    /* Validate Category name */
    __validate_input($name, 'Category Name');
    /* Insert into Category table */
    $result = $con->query("INSERT INTO ticket_categories(name) VALUES('$name')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Category Added successfully!',
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
if (isset($_GET['get_category_data'])) {
    $id = intval($_GET['id']);
    $result = $con->query("SELECT * FROM ticket_categories WHERE id = $id");

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
/******** Update Category  Script ******************/
if (isset($_GET['update_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    

     /* Validate Category name */
    __validate_input($name, 'Category Name');
    /* Insert into Category table */
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("UPDATE ticket_categories SET name='$name' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully!',
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

/*Delete  Script*/
if (isset($_GET['delete_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM ticket_categories WHERE id='$id'");
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


