<?php
include 'include/security_token.php';
include 'include/db_connect.php';
include 'include/functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$device = null;
$ports_data = [];
$total_ports = 0;
$up_ports = 0;
$down_ports = 0;
$sysdescr = "N/A";
$sysuptime = "N/A";

if (isset($_GET['id'])) {
    $device_id = intval($_GET['id']);

   
    $stmt = $con->prepare("SELECT * FROM devices WHERE id = ?");
    $stmt->bind_param("i", $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $device = $result->fetch_assoc();

    if ($device) {
        $ip = $device['ip_address'];
        $community = !empty($device['snmp_community']) ? $device['snmp_community'] : 'public';

        if ($device['snmp_enabled'] == 1) {
            snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
            
            /*-------System Description--------*/ 
            $raw_descr = @snmpget($ip, $community, ".1.3.6.1.2.1.1.1.0", 1000000, 2);
            if ($raw_descr !== false) {
                $sysdescr = str_replace('STRING: ', '', $raw_descr);
            }

            /*-------System Name--------*/ 
            $raw_name = @snmpget($ip, $community, ".1.3.6.1.2.1.1.5.0", 1000000, 2);
            if ($raw_name !== false) {
                $sysname = trim(str_replace(['STRING:', '"'], '', $raw_name));
            }
            /*-------OS Version---------*/
            $raw_descr = @snmpget($ip, $community, ".1.3.6.1.2.1.1.1.0", 1000000, 2);
            if ($raw_descr !== false) {
                $clean_descr = trim(str_replace(['STRING:', '"'], '', $raw_descr));
                if (preg_match('/(Cisco [^\r\n,]+|NX-OS [^\r\n,]+|RouterOS [^\r\n,]+)/i', $clean_descr, $matches)) {
                    $os_version = $matches[0];
                } else {
                    $os_version = strtok($clean_descr, ",");
                }
            }
            /*-------Serial Number (Entity MIB)--------*/ 
            $raw_serial = @snmpget($ip, $community, ".1.3.6.1.2.1.47.1.1.1.1.11.1", 1000000, 2);
            if ($raw_serial !== false) {
                $serial_no = trim(str_replace(['STRING:', '"'], '', $raw_serial));
            }
            /*--------System Uptime--------*/ 
            $raw_uptime = @snmpget($ip, $community, ".1.3.6.1.2.1.1.3.0", 1000000, 2);
            if ($raw_uptime !== false) {
                $uptime_data = format_snmp_uptime($raw_uptime);
                $sysuptime = $uptime_data['uptime'];
                $last_reboot = $uptime_data['reboot_time'];
            }
            

            /*-------Value Retrieval Mode Set (Plain Numbers)--------*/
            if (function_exists('snmp_set_valueretrieval')) {
                snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
            }

            /*-------Interface Name & Status--------*/
            $ifNames = @snmprealwalk($ip, $community, ".1.3.6.1.2.1.2.2.1.2", 1000000, 2);
            $ifOperStatus = @snmprealwalk($ip, $community, ".1.3.6.1.2.1.2.2.1.8", 1000000, 2);

            if ($ifNames && $ifOperStatus) {
                $status_by_index = [];
                foreach ($ifOperStatus as $oid => $val) {
                    $idx = substr($oid, strrpos($oid, '.') + 1);
                    
                    /*----------Value clean up-------*/ 
                    preg_match('/\d+/', $val, $matches);
                    $status_by_index[$idx] = isset($matches[0]) ? (int)$matches[0] : 2;
                }

                foreach ($ifNames as $oid => $name) {
                    /*-------Interface Index--------*/
                    $index = substr($oid, strrpos($oid, '.') + 1);

                    /*-------Interface Status Check--------*/
                    $raw_status = isset($status_by_index[$index]) ? $status_by_index[$index] : 2;

                    /*-------Value Cleaning--------*/ 
                    $clean_name = trim(str_replace(['STRING: ', '"'], '', $name));

                    /*-----1=up, 2=down-----*/ 
                    $is_up = ($raw_status === 1);

                    if ($is_up) {
                        $up_ports++;
                    } else {
                        $down_ports++;
                    }

                    $ports_data[] = [
                        'index'  => $index,
                        'name'   => $clean_name,
                        'status' => $is_up ? 'up' : 'down'
                    ];
                }
                
                $total_ports = count($ports_data);
            }
        }
    }
}

/*-------- Uptime Convert Helper Function --------*/
function format_snmp_uptime(string $uptime_raw) {
    if (empty($uptime_raw)) return ["uptime" => "N/A", "reboot_time" => "N/A"];

    $ticks = 0;
    if (preg_match('/\(([\d]+)\)/', $uptime_raw, $matches)) {
        $ticks = intval($matches[1]);
    } else {
        preg_match('/[\d]+/', $uptime_raw, $matches);
        $ticks = isset($matches[0]) ? intval($matches[0]) : 0;
    }

    if ($ticks <= 0) return ["uptime" => "N/A", "reboot_time" => "N/A"];

    $total_seconds = floor($ticks / 100);

    $days = floor($total_seconds / (24 * 3600));
    $remainder = $total_seconds % (24 * 3600);

    $hours = floor($remainder / 3600);
    $remainder %= 3600;

    $minutes = floor($remainder / 60);
    $seconds = $remainder % 60;

    $output = [];
    if ($days > 0) {
        $output[] = $days . ($days == 1 ? ' day' : ' days');
    }

    $time_parts = [];
    if ($hours > 0 || $days > 0) $time_parts[] = "{$hours}h";
    if ($minutes > 0 || $hours > 0 || $days > 0) $time_parts[] = "{$minutes}m";
    $time_parts[] = "{$seconds}s";

    $time_string = implode(' ', $time_parts);
    $final_uptime = !empty($output) ? implode(', ', $output) . ', ' . $time_string : $time_string;

    $reboot_parts = [];
    if ($days > 0) {
        $reboot_parts[] = $days . ($days == 1 ? ' day' : ' days');
    }
    if ($hours > 0) {
        $reboot_parts[] = "{$hours}h";
    }
    if ($minutes > 0) {
        $reboot_parts[] = "{$minutes}m";
    }
    if (empty($reboot_parts)) {
        $reboot_parts[] = "{$seconds}s";
    }

    $reboot_time_ago = implode(' ', $reboot_parts) . ' ago';

    return [
        "uptime" => $final_uptime,
        "reboot_time" => $reboot_time_ago
    ];
}
?>

<!doctype html>
<html lang="en">
<?php 
$extra_css = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
require 'Head.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.css" />

<body data-sidebar="dark">
    <div id="layout-wrapper">
        <?php $page_title = 'Device Profile'; include 'Header.php'; ?>

        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <?php include 'Sidebar_menu.php'; ?>
            </div>
        </div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    
                    <!-- Breadcrumb -->
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <p class="text-primary mb-0">
                                        <a href="index.php">Dashboard</a> / 
                                        <a href="devices.php">Devices Profile</a> / 
                                        <strong><?= htmlspecialchars($device['name'] ?? 'Unknown Device') ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($device): ?>
                    <!-- Device & Port Counters -->
                    <div class="row">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white">
                                    <h5 class="mb-0">Device Info: <?= htmlspecialchars($device['name']) ?> (<?= htmlspecialchars($device['ip_address']) ?>)</h5>
                                    
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-light border px-3 py-2 text-dark">
                                            <i class="fas fa-network-wired text-primary mr-1"></i> Total Ports: 
                                            <span class="text-primary"><?= $total_ports ?></span>
                                        </span>

                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle mr-1"></i> Ports Up: 
                                            <span><?= $up_ports ?></span>
                                        </span>

                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle mr-1"></i> Ports Down: 
                                            <span><?= $down_ports ?></span>
                                        </span>
                                       <!-- Terminal Modal Launcher Button -->
                                        <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#ajaxTerminalModal">
                                            <i class="fas fa-terminal mr-1"></i> Open Terminal
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Device Details Overview -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-white rounded border h-100">
                                                <h6 class="text-muted border-bottom pb-2 mb-3">
                                                    <i class="fas fa-microchip text-primary"></i> Hardware & System Details
                                                </h6>
                                                <!-- Vendor -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">Vendor:</span>
                                                    <span class="badge bg-soft-primary text-primary px-2 py-1">
                                                        <?= htmlspecialchars($device['vendor'] ?? '---') ?>
                                                    </span>
                                                </div>
                                                <!-- Hardware / Model -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">Hardware:</span>
                                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($hardware ?? $device['model'] ?? 'N/A') ?></span>
                                                </div>
                                                <!-- System Name -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">System Name:</span>
                                                    <span class="text-primary font-weight-bold"><?= htmlspecialchars($sysname ?? '---') ?></span>
                                                </div>
                                                <!-- Operating System -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">OS Version:</span>
                                                    <span class="text-dark"><?= htmlspecialchars($os_version ?? '---') ?></span>
                                                </div>
                                                <!-- Serial Number -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">Serial Number:</span>
                                                    <code><?= htmlspecialchars($serial_no ?? '---') ?></code>
                                                </div>
                                                <!-- Cached IP -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">Cached IP:</span>
                                                    <code><?= htmlspecialchars($cached_ip ?? $device['ip_address'] ?? '---') ?></code>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column: System Info -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-white rounded border h-100">
                                                <h6 class="text-muted border-bottom pb-2 mb-3">
                                                    <i class="fas fa-microchip text-success"></i> SNMP Diagnostics
                                                </h6>
                                                <div class="mb-2">
                                                    <span class="text-secondary font-weight-bold d-block mb-1">System Description:</span>
                                                    <small class="text-dark bg-light p-2 d-block rounded border font-monospace" title="<?= htmlspecialchars($sysdescr) ?>">
                                                        <?= htmlspecialchars($sysdescr) ?>
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <span class="text-secondary font-weight-bold">System Uptime:</span>
                                                    <span class="badge bg-success px-2 py-1">
                                                        <i class="fas fa-clock mr-1"></i> <?= htmlspecialchars($sysuptime) ?>
                                                    </span>
                                                </div>
                                                <!-- Last Reboot -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary font-weight-bold">Last Reboot:</span>
                                                    <span class="text-muted small">
                                                        <i class="fas fa-history mr-1"></i> <?= htmlspecialchars($last_reboot ?? '---') ?>
                                                    </span>
                                                </div>

                                                <!-- Location -->
                                                <div class="d-flex justify-content-between align-items-center mb-0">
                                                    <span class="text-secondary font-weight-bold">Location:</span>
                                                    <span>
                                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> 
                                                        <?= htmlspecialchars($device['location'] ?? '---') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<?php
include 'include/security_token.php';
include 'include/db_connect.php';
// include 'include/functions.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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

        <?php $page_title = 'NOC Incident';
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
                                            <p class="text-primary mb-0 hover-cursor">&nbsp;/&nbsp;<a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor"><a href="#">NOC Incident</a></p>
                                           
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
                                <!-- Add Ticket Button -->
                                <a  class="btn btn-primary" href="noc_incident_create.php" class="btn btn-success">
                                    <i class="fas fa-ticket-alt me-1"></i> Add New NOC Incident
                                </a>
                            </div>

                                <div class="card-body">
                                    <div class="table-responsive ">
                                       <table id="tickets_table" 
                                            class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th style="width: 60px;">ID</th>
                                                    <th>Incident Summary</th>
                                                    <th style="width: 100px;">Status</th>
                                                    <th style="width: 180px;">Create Date</th>
                                                    <th style="width: 100px;">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody id="tickets-list">

                                                <?php

                                                $where = [];

                                                if (isset($_GET['filter']) && $_GET['filter'] == 'today') {
                                                    $todayDate = date('Y-m-d');
                                                    $where[] = "DATE(create_date) = '$todayDate'";
                                                }

                                                $whereSql = '';

                                                if (!empty($where)) {
                                                    $whereSql = 'WHERE ' . implode(' AND ', $where);
                                                }

                                                $result = $con->query("
                                                    SELECT *
                                                    FROM noc_incident
                                                    $whereSql
                                                    ORDER BY id DESC
                                                ");

                                                if ($result && $result->num_rows > 0):

                                                    while ($row = $result->fetch_assoc()):
                                                ?>

                                                    <tr>

                                                        <td>
                                                            <?= htmlspecialchars($row['id']) ?>
                                                        </td>

                                                        <!-- Incident Summary -->
                                                        <td style="max-width: 400px;">

                                                            <div
                                                                class="text-truncate"
                                                                style="max-width: 400px;"
                                                                title="<?= htmlspecialchars($row['incident_summary'] ?? '') ?>">

                                                                <?= htmlspecialchars($row['incident_summary'] ?? '---') ?>

                                                            </div>

                                                        </td>

                                                        <!-- Status -->
                                                        <td>

                                                            <?php

                                                            $status_class = 'bg-secondary';

                                                            switch ($row['status']) {

                                                                case 'Active':
                                                                    $status_class = 'bg-danger';
                                                                    break;

                                                                case 'Pending':
                                                                    $status_class = 'bg-primary';
                                                                    break;

                                                                case 'Complete':
                                                                    $status_class = 'bg-success';
                                                                    break;
                                                            }

                                                            ?>

                                                            <span class="badge <?= $status_class ?>">
                                                                <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
                                                            </span>

                                                        </td>

                                                        <!-- Create Date -->
                                                        <td>
                                                            <?= date('d M Y h:i A', strtotime($row['create_date'])) ?>
                                                        </td>

                                                        <!-- Action -->
                                                        <td>

                                                            <a href="noc_incident_edit.php?id=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-primary"
                                                            title="Edit">

                                                                <i class="fas fa-edit"></i>

                                                            </a>

                                                             <button type="button" name="delete_button" data-id="<?php echo $row['id']; ?>"
                                                                class="btn-sm btn btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php
                                                    endwhile;

                                                else:
                                                ?>

                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            No tickets found
                                                        </td>
                                                    </tr>

                                                <?php endif; ?>

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
    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="fa fa-trash"></i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <h4 class="modal-title w-100 d-none" id="DeleteId"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="True">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="DeleteConfirm">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
           
            $('#tickets_table').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "targets": [2],
                    "orderable": false,
                }],
            });
            /*--------- Delete Script ------*/
            $(document).on('click', "button[name='delete_button']", function() {
                var id = $(this).data('id');
                $('#DeleteId').text(id);
                $('#deleteModal').modal('show');

                $('#DeleteConfirm').off('click').on('click', function() {
                    $.ajax({
                        url: "include/tickets_server.php?delete_noc_incident_tickets_data=true",
                        type: "POST",
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#deleteModal').modal('hide');
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error("Error deleting : " + xhr.responseText);
                        }
                    });
                });
            });
            /*---------Assign Team Modal Fill------------*/
            $('#assignTeamModal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);

                let ticket_id = button.data('ticket-id');
                let assigned_team = button.data('assigned-team');

                $('#assign_ticket_id').val(ticket_id);
                $('#assigned_team_select').val(assigned_team);
            });


            /*------------ Status Modal Fill------------=*/
            $('#statusModal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);

                let ticket_id = button.data('ticket-id');
                let ticket_status = button.data('ticket-status');

                $('#ticket_status_select').on('change', function () {

                    let status = $(this).val();

                    if (status === 'Complete') {
                        $('#rca_box').show();
                    } else {
                        $('#rca_box').hide();
                        $('#rca_note').val('');
                    }
                });

                $('#status_ticket_id').val(ticket_id);
                $('#ticket_status_select').val(ticket_status);
            });


            /*------------ Update Assigned Team------------*/
            $('#saveAssignedTeam').on('click', function () {

                let ticket_id = $('#assign_ticket_id').val();
                let assigned_team = $('#assigned_team_select').val();

                $.ajax({
                    url: 'include/tickets_server.php?update_assigned_team=true',
                    type: 'POST',
                    data: {
                        ticket_id: ticket_id,
                        assigned_team: assigned_team
                    },
                    dataType: 'json',
                    success: function (response) {

                        toastr.success(response.message);

                        if (response.success) {
                            $('#assignTeamModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function () {
                         toastr.error('Something went wrong');
                    }
                });
            });


            /*------------ Update Ticket Status------------*/
            $('#saveStatus').on('click', function () {

                let ticket_id = $('#status_ticket_id').val();
                let status = $('#ticket_status_select').val();
                let rca_note = $('#rca_note').val();

                /*-------------Validation rule---------*/
                if (status === 'Complete' && rca_note.trim() === '') {
                    toastr.error('RCA Note is required when closing a ticket');
                    return;
                }
                $.ajax({
                    url: 'include/tickets_server.php?update_ticket_status=true',
                    type: 'POST',
                    data: {
                        ticket_id: ticket_id,
                        status: status,
                        rca_note: rca_note
                    },
                    dataType: 'json',
                    success: function (response) {

                        toastr.success(response.message);

                        if (response.success) {
                            $('#statusModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function () {
                         toastr.error('Something went wrong');
                    }
                });
            });
        });
    </script>
</body>

</html>

                                    <h5 class="mt-4 mb-3">Interfaces / Ports List</h5>
                                    <!-- Ports Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="">
                                                <tr>
                                                    <th>Index</th>
                                                    <th>Port Name</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($ports_data)): ?>
                                                    <?php foreach($ports_data as $port): ?>
                                                        <tr>
                                                            <td><?= $port['index'] ?></td>
                                                            <td><?= htmlspecialchars($port['name']) ?></td>
                                                            <td>
                                                                <?php if($port['status'] === 'up'): ?>
                                                                    <span class="badge bg-success">UP</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">DOWN</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center">No interfaces found or SNMP failed.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-danger">Device not found!</div>
                    <?php endif; ?>

                </div>
            </div>
            <?php include 'Footer.php'; ?>
        </div>
    </div>

    <!-- cPanel Style Terminal Modal -->
    <div class="modal fade" id="ajaxTerminalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark text-light border-secondary">
                <div class="modal-header border-secondary py-2 bg-black">
                    <h6 class="modal-title text-success font-monospace mb-0">
                        <i class="fas fa-terminal mr-2"></i> CLI Terminal: <?= htmlspecialchars($device['name']) ?> (<?= htmlspecialchars($device['ip_address']) ?>)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-3 font-monospace" style="background-color: #0b0e14; color: #33ff33; min-height: 400px; max-height: 500px; overflow-y: auto;" id="terminal-screen">
                    <div id="terminal-log">
                        <div class="text-muted">Welcome to Device CLI Bridge. Type a command (e.g. <code>show pon power detail</code>, <code>display optical-module</code>, <code>ping 8.8.8.8</code>) and press Enter.</div>
                        <hr class="border-secondary my-2">
                    </div>
                    
                    <!-- Command Prompt Input Line -->
                    <div class="d-flex align-items-center mt-2">
                        <span class="text-warning mr-2"><?= htmlspecialchars($device['name']) ?>&gt;</span>
                        <input type="text" id="terminal-input" class="form-control form-control-sm bg-transparent text-light border-0 p-0 font-monospace shadow-none" placeholder="Type command here..." style="outline: none;" autofocus>
                    </div>
                </div>

                <div class="modal-footer border-secondary py-1 bg-black justify-content-between">
                    <small class="text-muted">Status: <span id="term-status" class="text-success">Ready</span></small>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearTerminal()">Clear Screen</button>
                </div>
            </div>
        </div>
    </div>

    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.js"></script>
    <script>
        const deviceId = <?= json_encode($device['id']) ?>;

        document.getElementById('terminal-input').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                
                let command = this.value.trim();
                if (command === '') return;

                // Clear input box
                this.value = '';

                if (command.toLowerCase() === 'clear') {
                    clearTerminal();
                    return;
                }

                let termLog = document.getElementById('terminal-log');
                let statusSpan = document.getElementById('term-status');

                // Append executed command to output window
                termLog.innerHTML += `<div class="mt-2"><strong class="text-warning"><?= htmlspecialchars($device['name']) ?>&gt;</strong> <span class="text-white">${escapeHtml(command)}</span></div>`;
                termLog.innerHTML += `<div class="text-info small" id="loading-indicator">Executing command, please wait...</div>`;

                // Scroll to bottom
                let termScreen = document.getElementById('terminal-screen');
                termScreen.scrollTop = termScreen.scrollHeight;

                statusSpan.innerText = "Processing...";
                statusSpan.className = "text-warning";

                // Send AJAX request
                fetch('include/terminal_execution.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `device_id=${deviceId}&command=${encodeURIComponent(command)}`
                })
                .then(response => response.json())
                .then(data => {
                    // Remove loading text
                    let loader = document.getElementById('loading-indicator');
                    if (loader) loader.remove();

                    if (data.status === 'success') {
                        termLog.innerHTML += `<pre class="text-light bg-dark p-2 rounded border border-secondary mt-1 mb-0" style="white-space: pre-wrap; font-size: 13px;">${escapeHtml(data.output)}</pre>`;
                    } else {
                        termLog.innerHTML += `<div class="text-danger mt-1">Error: ${escapeHtml(data.message)}</div>`;
                    }

                    statusSpan.innerText = "Ready";
                    statusSpan.className = "text-success";
                    termScreen.scrollTop = termScreen.scrollHeight;
                })
                .catch(error => {
                    let loader = document.getElementById('loading-indicator');
                    if (loader) loader.remove();

                    termLog.innerHTML += `<div class="text-danger mt-1">AJAX Error: ${error.message}</div>`;
                    statusSpan.innerText = "Error";
                    statusSpan.className = "text-danger";
                    termScreen.scrollTop = termScreen.scrollHeight;
                });
            }
        });

        function clearTerminal() {
            document.getElementById('terminal-log').innerHTML = '<div class="text-muted">Screen cleared. Ready for commands...</div>';
        }

        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
        </script>
</body>
</html>