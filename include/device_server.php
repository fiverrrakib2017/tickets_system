<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

if (isset($_GET['add_device_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    

    $errors = [];

    /*-------- Inputs Parsing & Sanitization------*/
    $name               = trim($_POST['name'] ?? '');
    $hostname           = trim($_POST['hostname'] ?? '');
    $ip_address         = trim($_POST['ip_address'] ?? '');

    $device_type        = $_POST['device_type'] ?? 'router';
    $vendor             = trim($_POST['vendor'] ?? '');
    $model              = trim($_POST['model'] ?? '');
    $serial_number      = trim($_POST['serial_number'] ?? '');

    $location           = trim($_POST['location'] ?? '');
    $description        = trim($_POST['description'] ?? '');

    $snmp_enabled       = isset($_POST['snmp_enabled']) ? 1 : 0;
    $snmp_version       = $_POST['snmp_version'] ?? '2c';
    $snmp_port          = intval($_POST['snmp_port'] ?? 161);
    $snmp_community     = trim($_POST['snmp_community'] ?? '');

    $monitoring_enabled = isset($_POST['monitoring_enabled']) ? 1 : 0;

    /* ------------ Validation ------------- */
    if ($name === '') {
        $errors[] = 'Device name is required.';
    }

    if ($ip_address === '') {
        $errors[] = 'IP address is required.';
    } elseif (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $errors[] = 'Invalid IP address format.';
    } else {
        $check_stmt = $con->prepare("SELECT id FROM devices WHERE ip_address = ? LIMIT 1");
        $check_stmt->bind_param("s", $ip_address);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $errors[] = 'This IP address already exists.';
        }
        $check_stmt->close();
    }

    if ($snmp_enabled && ($snmp_port < 1 || $snmp_port > 65535)) {
        $errors[] = 'Invalid SNMP port (must be between 1 and 65535).';
    }

    /*--------Validation Fail-------*/ 
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors'  => $errors
        ]);
        exit;
    }

    /* ------------- Transaction & Save ------------- */
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $con->begin_transaction();

        $stmt = $con->prepare("
            INSERT INTO devices (
                name, hostname, ip_address, device_type, vendor, 
                model, serial_number, location, description, 
                snmp_enabled, snmp_version, snmp_port, snmp_community, 
                monitoring_enabled
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssssisisi",
            $name,
            $hostname,
            $ip_address,
            $device_type,
            $vendor,
            $model,
            $serial_number,
            $location,
            $description,
            $snmp_enabled,
            $snmp_version,
            $snmp_port,
            $snmp_community,
            $monitoring_enabled
        );

        $stmt->execute();
        $stmt->close();
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Device added successfully.'
        ]);
        exit;

    } catch (mysqli_sql_exception $e) {
        $con->rollback();

        $errorMessage = 'Failed to add device.';

        if ($e->getCode() == 1062) {
            $errorMessage = 'This IP address already exists.';
        } else {
            $errorMessage = $e->getMessage();
        }

        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
        exit;
    } catch (Exception $e) {
        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => 'System error: ' . $e->getMessage()
        ]);
        exit;
    }
}
if (isset($_GET['update_device_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    /*-------- Inputs Parsing & Sanitization ------*/
    $device_id          = intval($_POST['device_id'] ?? 0);
    $name               = trim($_POST['name'] ?? '');
    $hostname           = trim($_POST['hostname'] ?? '');
    $ip_address         = trim($_POST['ip_address'] ?? '');

    $device_type        = $_POST['device_type'] ?? 'router';
    $vendor             = trim($_POST['vendor'] ?? '');
    $model              = trim($_POST['model'] ?? '');
    $serial_number      = trim($_POST['serial_number'] ?? '');

    $location           = trim($_POST['location'] ?? '');
    $description        = trim($_POST['description'] ?? '');

    $snmp_enabled       = isset($_POST['snmp_enabled']) ? 1 : 0;
    $snmp_version       = $_POST['snmp_version'] ?? '2c';
    $snmp_port          = intval($_POST['snmp_port'] ?? 161);
    $snmp_community     = trim($_POST['snmp_community'] ?? '');

    $monitoring_enabled = isset($_POST['monitoring_enabled']) ? 1 : 0;

    /* ------------ Validation ------------- */
    if ($device_id <= 0) {
        $errors[] = 'Invalid device ID.';
    }

    if ($name === '') {
        $errors[] = 'Device name is required.';
    }

    if ($ip_address === '') {
        $errors[] = 'IP address is required.';
    } elseif (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $errors[] = 'Invalid IP address format.';
    } else {
        $check_stmt = $con->prepare("SELECT id FROM devices WHERE ip_address = ? AND id != ? LIMIT 1");
        $check_stmt->bind_param("si", $ip_address, $device_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $errors[] = 'This IP address is already used by another device.';
        }
        $check_stmt->close();
    }

    if ($snmp_enabled && ($snmp_port < 1 || $snmp_port > 65535)) {
        $errors[] = 'Invalid SNMP port (must be between 1 and 65535).';
    }

    /*-------- Validation Fail -------*/ 
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors'  => $errors
        ]);
        exit;
    }

    /* ------------- Transaction & Update ------------- */
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $con->begin_transaction();

        $stmt = $con->prepare("
            UPDATE devices SET 
                name = ?, 
                hostname = ?, 
                ip_address = ?, 
                device_type = ?, 
                vendor = ?, 
                model = ?, 
                serial_number = ?, 
                location = ?, 
                description = ?, 
                snmp_enabled = ?, 
                snmp_version = ?, 
                snmp_port = ?, 
                snmp_community = ?, 
                monitoring_enabled = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssssssisisii",
            $name,
            $hostname,
            $ip_address,
            $device_type,
            $vendor,
            $model,
            $serial_number,
            $location,
            $description,
            $snmp_enabled,
            $snmp_version,
            $snmp_port,
            $snmp_community,
            $monitoring_enabled,
            $device_id
        );

        $stmt->execute();
        $stmt->close();

        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Device updated successfully.'
        ]);
        exit;

    } catch (mysqli_sql_exception $e) {
        $con->rollback();

        $errorMessage = 'Failed to update device.';

        if ($e->getCode() == 1062) {
            $errorMessage = 'This IP address already exists.';
        } else {
            $errorMessage = $e->getMessage();
        }

        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
        exit;
    } catch (Exception $e) {
        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => 'System error: ' . $e->getMessage()
        ]);
        exit;
    }
}
/*-----Delete customer Script--------*/
if (isset($_GET['delete_device_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM `devices` WHERE id='$id'");
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


?>