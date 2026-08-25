<?php
include 'include/security_token.php';
include 'include/db_connect.php';
include 'include/functions.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$devices = [];

$query = $con->query("
    SELECT
        id,
        name,
        hostname,
        ip_address,
        device_type,
        vendor,
        model,
        location,
        status,
        monitoring_enabled,
        snmp_enabled,
        last_seen,
        last_poll,
        created_at
    FROM devices
    ORDER BY id DESC
");

while ($row = $query->fetch_assoc()) {
    $devices[] = $row;
}

?>

<!doctype html>
<html lang="en">
<?php 
$extra_css  = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
require 'Head.php';

?>

<body data-sidebar="dark">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Device Management';
        include 'Header.php'; ?>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <?php include 'Sidebar_menu.php'; ?>

                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                     <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="d-flex align-items-end flex-wrap">
                                    <div class="mr-md-3 mr-xl-5">
                                        <div class="d-flex">
                                            <i class="mdi mdi-home text-muted hover-cursor"></i>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp;
                                                <a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                             <p class="text-primary mb-0 hover-cursor">
                                                <a href="devices.php">Devices Management</a>
                                            </p>
                                        </div>
                                    </div>
                                    <br>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="card-header customer_card_header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background-color: white;">
    
                                    <!-- Add Device Button -->
                                    <a href="add-device.php" class="btn btn-success">
                                        <i class="fas fa-server me-1"></i> Add New Device
                                    </a>
                                    <!-- Device Status Counters -->
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-light border px-3 py-2 font-weight-bold">
                                            <i class="fas fa-list text-primary mr-1"></i> Total: 
                                            <span class="text-primary"><?= count($devices) ?></span>
                                        </span>

                                        <span class="badge bg-success px-3 py-2 font-weight-bold">
                                            <i class="fas fa-check-circle mr-1"></i> Online: 
                                            <span>
                                                <?= count(array_filter( $devices,fn($d) => $d['status'] === 'up')) ?>
                                            </span>
                                        </span>

                                        <span class="badge bg-danger px-3 py-2 font-weight-bold">
                                            <i class="fas fa-times-circle mr-1"></i> Offline: 
                                            <span>
                                                 <?= count(array_filter(
                                                    $devices,
                                                    fn($d) => $d['status'] === 'down'
                                                )) ?>
                                            </span>
                                        </span>

                                        <span class="badge bg-warning px-3 py-2 font-weight-bold text-dark">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Warning: 
                                            <span>
                                                <?= count(array_filter(
                                                    $devices,
                                                    fn($d) => $d['status'] === 'unknown'
                                                )) ?>
                                            </span>
                                        </span>
                                    </div>

                                </div>

                                <div class="card-body">
                                     <!-- Statistics -->
                                    <div class="table-responsive ">
                                        <table id="devicesTable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>

                                                    <th>#</th>

                                                    <th>Device Name</th>

                                                    <th>IP Address</th>

                                                    <th>Type</th>

                                                    <th> Vendor</th>

                                                    <th>Location</th>

                                                    <th>Status</th>

                                                    <th>Last Seen</th>

                                                    <th> Action </th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($devices as $index => $device): ?>

                                                <tr>

                                                    <td><?= $index + 1 ?></td>


                                                    <td>

                                                        <a
                                                            href="device-details.php?id=<?= (int)$device['id'] ?>"
                                                            class="font-weight-bold"
                                                        >

                                                            <i class="fas fa-server text-primary mr-1"></i>

                                                            <?= htmlspecialchars($device['name']) ?>

                                                        </a>

                                                        <?php if (!empty($device['hostname'])): ?>

                                                            <br>

                                                            <small class="text-muted">

                                                                <?= htmlspecialchars($device['hostname']) ?>

                                                            </small>

                                                        <?php endif; ?>

                                                    </td>


                                                    <td>

                                                        <code><?= htmlspecialchars($device['ip_address']) ?></code>

                                                    </td>


                                                    <td>

                                                        <?php

                                                        $typeIcons = [
                                                            'router' => 'fa-route',
                                                            'switch' => 'fa-network-wired',
                                                            'olt' => 'fa-project-diagram',
                                                            'server' => 'fa-server',
                                                            'firewall' => 'fa-shield-alt',
                                                            'access_point' => 'fa-wifi',
                                                            'other' => 'fa-cube'
                                                        ];

                                                        $icon = $typeIcons[$device['device_type']] ?? 'fa-cube';

                                                        ?>

                                                        <i class="fas <?= $icon ?> mr-1"></i>

                                                        <?= ucwords(str_replace('_', ' ', $device['device_type'])) ?>

                                                    </td>


                                                    <td>

                                                        <?= htmlspecialchars($device['vendor'] ?: '-') ?>

                                                        <?php if (!empty($device['model'])): ?>

                                                            <br>

                                                            <small class="text-muted">

                                                                <?= htmlspecialchars($device['model']) ?>

                                                            </small>

                                                        <?php endif; ?>

                                                    </td>


                                                    <td>

                                                        <?= htmlspecialchars($device['location'] ?: '-') ?>

                                                    </td>


                                                    <td>

                                                        <?php if ($device['status'] === 'up'): ?>

                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                Online
                                                            </span>

                                                        <?php elseif ($device['status'] === 'down'): ?>

                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle mr-1"></i>
                                                                Offline
                                                            </span>

                                                        <?php elseif ($device['status'] === 'disabled'): ?>

                                                            <span class="badge bg-secondary">
                                                                Disabled
                                                            </span>

                                                        <?php else: ?>

                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-question-circle mr-1"></i>
                                                                Unknown
                                                            </span>

                                                        <?php endif; ?>

                                                    </td>


                                                    <td>

                                                        <?php if ($device['last_seen']): ?>

                                                            <?= date(
                                                                'd M Y, h:i A',
                                                                strtotime($device['last_seen'])
                                                            ) ?>

                                                        <?php else: ?>

                                                            <span class="text-muted">
                                                                Never
                                                            </span>

                                                        <?php endif; ?>

                                                    </td>


                                                    <td>


                                                        <a href="device-details.php?id=<?= (int)$device['id'] ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>


                                                        <a href="edit-device.php?id=<?= (int)$device['id'] ?>" class="btn btn-sm btn-primary" title="Edit"> <i class="fas fa-edit"></i></a>


                                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteDevice(<?= (int)$device['id'] ?>)" title="Delete"> <i class="fas fa-trash"></i></button>


                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php include 'Footer.php'; ?>

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
   
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script type="text/javascript">
        $('#devicesTable').DataTable({
            "order": [
                [0, "desc"]
            ],
            "columnDefs": [{
                "targets": [2],
                "orderable": false,
            }],
        });
    </script>
</body>

</html>
