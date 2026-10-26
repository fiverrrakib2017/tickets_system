<?php
date_default_timezone_set('Asia/Dhaka');

/**---------- Error Reporting Setup --------------**/ 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*-------- Load Database & Config Files ------*/
require BASE_PATH . '/include/db_connect.php';

$permissions_config = require BASE_PATH . '/App/Service/Permission.php';

if (!is_array($permissions_config)) {
    die("❌ Error: Permissions file must return an array!\n");
}

/*------------Enable Exception mode for MySQLi---------*/ 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    echo "===============================================\n";
    echo "       STARTING PERMISSION SYNC PROCESS        \n";
    echo "===============================================\n\n";

    /*------ Database Tables Creation (Schema Definition)--------*/
    $con->query("
        CREATE TABLE IF NOT EXISTS `roles` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(50) NOT NULL UNIQUE,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $con->query("
        CREATE TABLE IF NOT EXISTS `permissions` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(100) NOT NULL,
          `module` VARCHAR(50) NOT NULL,
          `slug` VARCHAR(100) NOT NULL UNIQUE,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $con->query("
        CREATE TABLE IF NOT EXISTS `role_permissions` (
          `role_id` INT NOT NULL,
          `permission_id` INT NOT NULL,
          PRIMARY KEY (`role_id`, `permission_id`),
          FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
          FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "[✓] Database tables checked/created successfully.\n";

    /*-----------Ensure 'Super Admin' Role Exists----------*/
    $superAdminRoleName = "Super Admin";
    $roleStmt =$con->prepare("SELECT id FROM roles WHERE name = ?");
    $roleStmt->bind_param("s", $superAdminRoleName);$roleStmt->execute();
    $roleResult =$roleStmt->get_result();

    if ($roleRow =$roleResult->fetch_assoc()) {
        $superAdminRoleId =$roleRow['id'];
    } else {
        $insertRoleStmt =$con->prepare("INSERT INTO roles (name) VALUES (?)");
        $insertRoleStmt->bind_param("s", $superAdminRoleName);$insertRoleStmt->execute();
        $superAdminRoleId =$insertRoleStmt->insert_id;
        $insertRoleStmt->close();
        echo "[+] Role Created: {$superAdminRoleName}\n";
    }
    $roleStmt->close();

    /*-------------Collect All Slugs From Array & Existing Database------*/
    $fileSlugs = [];$newAddedCount = 0;

    $checkStmt      =$con->prepare("SELECT id FROM permissions WHERE slug = ?");
    $insertStmt     =$con->prepare("INSERT INTO permissions (name, module, slug) VALUES (?, ?, ?)");
    $assignRoleStmt =$con->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");

    foreach ($permissions_config as $module =>$permissions) {
        if (is_array($permissions)) {
            foreach ($permissions as $slug =>$name) {
             
                $fileSlugs[] =$slug;

                /*------Check If Permission Already Exists-------*/
                $checkStmt->bind_param("s", $slug);
                $checkStmt->execute();$checkStmt->store_result();

                if ($checkStmt->num_rows === 0) {
                    /*--------------Insert New Permission-------*/
                    $insertStmt->bind_param("sss", $name,$module, $slug);$insertStmt->execute();
                    $permissionId =$insertStmt->insert_id;
                    echo "[+] Added New Permission: {$slug} ({$name})\n";
                    $newAddedCount++;
                } else {
                    /*--------------Get Existing Permission ID-------*/
                    $getIdStmt =$con->prepare("SELECT id FROM permissions WHERE slug = ?");
                    $getIdStmt->bind_param("s", $slug);
                    $getIdStmt->execute();$permissionId = $getIdStmt->get_result()->fetch_assoc()['id'];$getIdStmt->close();
                }

                $checkStmt->free_result();
                /*--------------Assign Permission to Super Admin Role-------*/
                $assignRoleStmt->bind_param("ii", $superAdminRoleId, $permissionId);$assignRoleStmt->execute();
            }
        }
    }

    $checkStmt->close();
    $insertStmt->close();$assignRoleStmt->close();

    /*--------------Clean Up Removed Permissions (Pruning/Deleting)--------*/
    $dbSlugsResult =$con->query("SELECT id, slug FROM permissions");
    $deletedCount = 0;

    if ($dbSlugsResult->num_rows > 0) {
        $deleteStmt =$con->prepare("DELETE FROM permissions WHERE id = ?");

        while ($row =$dbSlugsResult->fetch_assoc()) {
            if (!in_array($row['slug'], $fileSlugs)) {$deleteStmt->bind_param("i", $row['id']);$deleteStmt->execute();
                echo "[-] Removed Deleted Permission: {$row['slug']}\n";
                $deletedCount++;
            }
        }
        $deleteStmt->close();
    }

    /*-------------Final Summary Report-----------*/
    echo "\n-----------------------------------------------\n";
    echo "SYNC SUMMARY:\n";
    echo "  - New Permissions Added: {$newAddedCount}\n";
    echo "  - Removed Permissions  : {$deletedCount}\n";
    echo "===============================================\n";
    echo "🎉 Sync Completed Successfully!\n";

} catch (mysqli_sql_exception $e) {
    die("❌ Database Error: " . $e->getMessage() . "\n");
}