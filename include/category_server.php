<?php 
include "db_connect.php";
  
/******** Add Category  Script ******************/
if (isset($_GET['add_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    /**-----------Check Category  Name is exist-----------**/
    $check_ = $con->query("SELECT * FROM ticket_categories WHERE name='$name'");
    if ($check_->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Category Already exists!',
        ]);
        exit();
    }

    /*----------- Validate Category name -----------*/
    __validate_input($name, 'Category Name');
    /*----------- Insert into Category table -----------*/
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
/*----------- Update Category  Script -----------*/
if (isset($_GET['update_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    

     /*----------- Validate Category name -----------*/
    __validate_input($name, 'Category Name');
    /*----------- Insert into Category table -----------*/
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

/*-----------Delete  Script-----------*/
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


/*----------- Add SubCategory -----------------*/
if (isset($_GET['add_subcategory_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['sub_category_name']);
    $category_id = intval($_POST['category_id']);

    __validate_input($name, 'Subcategory Name');
    __validate_input($category_id, 'Category');

    /*----------- Check duplicate -----------*/
    $check_ = $con->query("SELECT * FROM ticket_subcategories WHERE name='$name' AND category_id='$category_id'");
    if ($check_->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Subcategory already exists!',
        ]);
        exit();
    }

    $result = $con->query("INSERT INTO ticket_subcategories(category_id, name) VALUES('$category_id','$name')");

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Subcategory added successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add!',
        ]);
    }
    exit();
}

/*----------- Get SubCategory -----------*/
if (isset($_GET['get_subcategory_data'])) {
    $id = intval($_GET['id']);
    $result = $con->query("SELECT * FROM ticket_subcategories WHERE id = $id");

    $data = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'data' => $data ?? [],
    ]);
    exit();
}

/******** Update ********/
if (isset($_GET['update_subcategory_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = intval($_POST['id']);
    $name = trim($_POST['sub_category_name']);
    $category_id = intval($_POST['category_id']);

    __validate_input($id, 'ID');
    __validate_input($name, 'Subcategory Name');
    __validate_input($category_id, 'Category');

    $result = $con->query("UPDATE ticket_subcategories SET name='$name', category_id='$category_id' WHERE id='$id'");

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update!',
        ]);
    }
    exit();
}

/******** Delete ********/
if (isset($_GET['delete_subcategory_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = intval($_POST['id']);

    $result = $con->query("DELETE FROM ticket_subcategories WHERE id='$id'");

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete!',
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


