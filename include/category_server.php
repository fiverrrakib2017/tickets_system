<?php 
include "db_connect.php";
include "functions.php";
 /******** Add Category  Script ******************/
if (isset($_GET['add_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    /*----------- Validate Category name -----------*/
    __validate_input($name, 'Category Name');

    /*----------- Check Category Name is exist (Prepared Statement) -----------*/
    $stmt = $con->prepare("SELECT id FROM ticket_categories WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Category Already exists!',
        ]);
        exit();
    }
    $stmt->close();

    /*----------- Insert into Category table (Prepared Statement) -----------*/
    $stmt = $con->prepare("INSERT INTO ticket_categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) {
        $category_id = $stmt->insert_id;

        /*----------- Log User Activity -----------*/
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', "Added new ticket category: '$name' (ID: $category_id)");
        }
        echo json_encode([
            'success' => true,
            'message' => 'Category Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Category!',
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
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);

    /*----------- Validate ID -----------*/
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Validate Category name -----------*/
    __validate_input($name, 'Category Name');

    /*----------- Check if the new name already exists for another ID -----------*/
    $stmt = $con->prepare("SELECT id FROM ticket_categories WHERE name = ? AND id != ? LIMIT 1");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Category name already exists!',
        ]);
        exit();
    }
    $stmt->close();

    /*----------- Update Category table (Prepared Statement) -----------*/
    $stmt = $con->prepare("UPDATE ticket_categories SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        /*----------- Log User Activity -----------*/
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', "Updated ticket category ID: $id to new name: '$name'");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Category!',
        ]);
        exit();
    }
}

/*-----------Delete Script-----------*/
if (isset($_GET['delete_category_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Optional: Fetch category name before delete for better logging -----------*/
    $cat_name = "";
    $stmt_check = $con->prepare("SELECT name FROM ticket_categories WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $cat_name = $row_check['name'];
    }
    $stmt_check->close();

    /*----------- Delete from Category table  -----------*/
    $stmt = $con->prepare("DELETE FROM ticket_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted ticket category ID: $id";
        if (!empty($cat_name)) {
            $log_desc .= " (Name: '$cat_name')";
        }

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

    /*----------- Check duplicate (Prepared Statement) -----------*/
    $stmt = $con->prepare("SELECT id FROM ticket_subcategories WHERE name = ? AND category_id = ? LIMIT 1");
    $stmt->bind_param("si", $name, $category_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Subcategory already exists!',
        ]);
        exit();
    }
    $stmt->close();

    /*----------- Insert into Subcategory table (Prepared Statement) -----------*/
    $stmt = $con->prepare("INSERT INTO ticket_subcategories (category_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $category_id, $name);
    
    if ($stmt->execute()) {
        $subcategory_id = $stmt->insert_id;
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Added new subcategory: '$name' under Category ID: $category_id (ID: $subcategory_id)";
        if (function_exists('system_log')) {
            system_log($con, 'INSERT', $log_desc);
        } 

        echo json_encode([
            'success' => true,
            'message' => 'Subcategory added successfully!',
        ]);
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add!',
        ]);
    }
    exit();
}

/*----------- Get SubCategory -----------*/
if (isset($_GET['get_subcategory_data'])) {
    /*-------
    Sub category id, , 
    category id boath are filter now-
    --------*/
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

    $where = [];

    if ($id > 0) {
        $where[] = "id = $id";
    }

    if ($category_id > 0) {
        $where[] = "category_id = $category_id";
    }

    $sql = "SELECT * FROM ticket_subcategories";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY id DESC";

    $result = $con->query($sql);

    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $data
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

    /*----------- Check if same name exists under the same category (excluding current ID) -----------*/
    $stmt_check = $con->prepare("SELECT id FROM ticket_subcategories WHERE name = ? AND category_id = ? AND id != ? LIMIT 1");
    $stmt_check->bind_param("sii", $name, $category_id, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo json_encode([
            'success' => false,
            'message' => 'Subcategory name already exists in this category!',
        ]);
        exit();
    }
    $stmt_check->close();

    /*----------- Update Subcategory (Prepared Statement) -----------*/
    $stmt = $con->prepare("UPDATE ticket_subcategories SET name = ?, category_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $name, $category_id, $id);

    if ($stmt->execute()) {
        $stmt->close();

        /*----------- Log User Activity -----------*/
        $log_desc = "Updated subcategory ID: $id to Name: '$name' under Category ID: $category_id";
        if (function_exists('system_log')) {
            system_log($con, 'UPDATE', $log_desc);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
    } else {
        $stmt->close();
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

    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }

    /*----------- Fetch subcategory name before delete for logging -----------*/
    $subcat_name = "";
    $stmt_check = $con->prepare("SELECT name FROM ticket_subcategories WHERE id = ? LIMIT 1");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $row_check = $res_check->fetch_assoc();
        $subcat_name = $row_check['name'];
    }
    $stmt_check->close();

    /*----------- Delete Subcategory (Prepared Statement) -----------*/
    $stmt = $con->prepare("DELETE FROM ticket_subcategories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        /*----------- Log User Activity -----------*/
        $log_desc = "Deleted subcategory ID: $id";
        if (!empty($subcat_name)) {
            $log_desc .= " (Name: '$subcat_name')";
        }

        if (function_exists('system_log')) {
            system_log($con, 'DELETE', $log_desc);
        }

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


