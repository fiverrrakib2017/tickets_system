<?php
include("include/security_token.php");
include("include/db_connect.php");
function _formate_duration($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return "{$h}h {$m}m";
}

?>

<!doctype html>
<html lang="en">

<?php require 'Head.php';?>

<body data-sidebar="dark">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'POP Branch ';
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
                                            <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;<a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor">POP Branch</p>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between align-items-end flex-wrap">
                                    <button class="btn btn-primary mt-2 mt-xl-0 mdi mdi-account-plus mdi-18px" data-bs-toggle="modal" data-bs-target="#addModal" style="margin-bottom: 12px;">&nbsp;&nbsp;Add New POP Branch</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-md-6 float-md-right grid-margin-sm-0">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="ticket_topic_datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>POP Branch</th>
                                                    <th>Manager Name</th>
                                                    <th>Router/Switch IP</th>
                                                    <th>Phone Number</th>
                                                    <th>Battery</th>
                                                    <th>IPS</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $sql = "SELECT * FROM pop_branch ORDER BY id DESC";
                                                $result = mysqli_query($con, $sql);

                                                while ($rows = mysqli_fetch_assoc($result)) {

                                                ?>

                                                    <tr>
                                                        <td><?php echo $rows['id']; ?></td>
                                                        <td>
                                                            <!-- Customer / POP Name -->
                                                            <div class="fw-semibold mb-1">
                                                                <a href="customers.php?pop_branch=<?php echo $rows['id']; ?>" class="text-decoration-none">
                                                                    <?php echo htmlspecialchars($rows['name']); ?>
                                                                </a>
                                                            </div>
                                                            <?php if ($rows['ping_ip_status'] === 'offline') { ?>
                                                                <span class="text-danger small">
                                                                    Offline for <?php echo _formate_duration($rows['offline_duration']); ?>
                                                                </span>
                                                            <?php } ?>
    
                                                           <!-- Status + Ping -->
                                                            <div class="d-flex flex-column gap-1 small text-muted">

                                                                <!-- Online / Offline Status -->
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <?php if ($rows['ping_ip_status'] === 'online') { ?>
                                                                        <span class="badge bg-success">
                                                                            <i class="fas fa-wifi me-1"></i> Online
                                                                        </span>
                                                                    <?php } else { ?>
                                                                        <span class="badge bg-danger">
                                                                            <i class="fas fa-wifi-slash me-1"></i> Offline
                                                                        </span>
                                                                    <?php } ?>
                                                                </div>

                                                                <!-- Ping Statistics -->
                                                                <div class="d-flex flex-wrap gap-3">

                                                                    <span>
                                                                        <i class="fas fa-paper-plane text-primary me-1"></i>
                                                                        Sent: <strong><?php echo (int)$rows['ping_sent']; ?></strong>
                                                                    </span>

                                                                    <span>
                                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                                        Recv: <strong><?php echo (int)$rows['ping_received']; ?></strong>
                                                                    </span>

                                                                    <span>
                                                                        <i class="fas fa-times-circle text-danger me-1"></i>
                                                                        Lost: <strong><?php echo (int)$rows['ping_lost']; ?></strong>
                                                                    </span>

                                                                    <span>
                                                                        <i class="fas fa-arrow-down text-info me-1"></i>
                                                                        Min: <strong><?php echo (int)$rows['ping_min_ms']; ?> ms</strong>
                                                                    </span>

                                                                    <span>
                                                                        <i class="fas fa-arrow-up text-warning me-1"></i>
                                                                        Max: <strong><?php echo (int)$rows['ping_max_ms']; ?> ms</strong>
                                                                    </span>

                                                                    <span>
                                                                        <i class="fas fa-chart-line text-secondary me-1"></i>
                                                                        Avg: <strong><?php echo (int)$rows['ping_avg_ms']; ?> ms</strong>
                                                                    </span>

                                                                </div>

                                                            </div>

                                                        </td>

                                                        <td>
                                                            <?=$rows['manager_name'];?>
                                                        </td>
                                                        <td>
                                                            <?=$rows['router_ip'];?>
                                                        </td>
                                                        <td>
                                                            <?=$rows['phone_number'];?>
                                                        </td>
                                                        <td>
                                                            <?=$rows['battery'];?>
                                                        </td>
                                                        <td>
                                                            <?=$rows['ips'];?>
                                                        </td>

                                                        <td style="text-align:right">

                                                            <button type="button" name="edit_button" data-id="<?php echo $rows['id']; ?>" class="btn-sm btn btn-info"><i class="fas fa-edit"></i></button>
                                                            <button type="button" name="delete_button" data-id="<?php echo $rows['id']; ?>" class="btn-sm btn btn-danger"><i class="fas fa-trash"></i></button>
                                                            <button 
                                                                    type="button"
                                                                    class="btn-sm btn btn-dark terminal-btn"
                                                                    data-ip="<?= $rows['router_ip'] ?>?>"
                                                                >
                                                                    <i class="fas fa-terminal"></i> Terminal
                                                                </button>




                                                        </td>
                                                    </tr>
                                                <?php } ?>

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
    <!--Add Modal -->
    <div class="modal fade " tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="addModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span
                            class="mdi mdi-lan mdi-18px"></span> &nbsp;Customer Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="include/pop_branch_server.php?add_pop_branch_data=true" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>POP Branch</label>
                                        <input type="text" name="pop_branch_name" class="form-control" placeholder="Enter POP Branch">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Manager Name</label>
                                        <input type="text" name="manager_name" class="form-control" placeholder="Enter Manager Name">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Battery</label>
                                        <input type="number" name="battery" class="form-control" placeholder="Enter Battery Count">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>IPS</label>
                                        <input type="number" name="ips" class="form-control" placeholder="Enter Battery IPS">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Router/Switch IP</label>
                                        <input type="text" name="router_ip" class="form-control" placeholder="Enter Router/Switch IP">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add New POP Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
     <!--Edit Modal -->        
    <div class="modal fade bs-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span
                            class="mdi mdi-lan mdi-18px"></span> &nbsp;Update POP Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="include/pop_branch_server.php?update_pop_branch_data=true" method="POST" enctype="multipart/form-data">
                      <input type="text" name="id" id="id" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>POP Branch</label>
                                        <input type="text" name="pop_branch_name" class="form-control" placeholder="Enter POP Branch">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Manager Name</label>
                                        <input type="text" name="manager_name" class="form-control" placeholder="Enter Manager Name">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Battery</label>
                                        <input type="number" name="battery" class="form-control" placeholder="Enter Battery Count">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>IPS</label>
                                        <input type="number" name="ips" class="form-control" placeholder="Enter Battery IPS">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Router/Switch IP</label>
                                        <input type="text" name="router_ip" class="form-control" placeholder="Enter Router/Switch IP">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update POP Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>     
    <!-- Modal -->
        <!-- Modal -->
        <div class="modal fade" id="terminalModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Router Terminal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <input type="text" id="telnetUsername" class="form-control mb-1" placeholder="Username">
                    <input type="password" id="telnetPassword" class="form-control" placeholder="Password">
                    <button id="telnetConnectBtn" class="btn btn-primary btn-sm mt-2">Connect</button>
                </div>

                <pre id="terminalBody" style="height:400px; background:#000; color:#0f0; padding:10px; overflow:auto;"></pre>
                <input type="text" id="terminalInput" class="form-control mt-2" placeholder="Type command & press Enter" disabled>
            </div>
            </div>
        </div>
        </div>

                                  
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
    <!-- JAVASCRIPT -->
    <?php include 'script.php'; ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#ticket_topic_datatable').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "targets": [2],
                    "orderable": false,
                }],
            });
            $('#addModal form').submit(function(e) {
                e.preventDefault();
                /*Get the submit button*/
                var submitBtn = $('#addModal form').find('button[type="submit"]');

                /* Save the original button text*/
                var originalBtnText = submitBtn.html();

                /*Change button text to loading state*/
                submitBtn.html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>`
                );
                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();
                $.ajax({
                    type: 'POST',
                    'url': url,
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            toastr.error(response.message);
                        }
                    },


                    error: function(xhr, status, error) {
                        /** Handle  errors **/
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                    }
                });
            });
             /** Edit Area Script **/
            $(document).on('click', "button[name='edit_button']", function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "include/pop_branch_server.php?get_pop_branch_data=true",
                    type: "GET",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editModal').modal('show');
                            $('#editModal input[name="id"]').val(response.data.id);
                            $('#editModal input[name="pop_branch_name"]').val(response.data.name);
                            $('#editModal input[name="manager_name"]').val(response.data.manager_name);
                            $('#editModal input[name="phone_number"]').val(response.data.phone_number);
                            $('#editModal input[name="battery"]').val(response.data.battery);
                            $('#editModal input[name="ips"]').val(response.data.ips);
                            $('#editModal input[name="router_ip"]').val(response.data.router_ip);
                        } else {
                            toastr.error("Error fetching data for edit: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to fetch  details');
                    }
                });
            });
            /** Update **/
            $('#editModal form').submit(function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();

                /*Get the submit button*/
                var submitBtn = form.find('button[type="submit"]');

                /*Save the original button text*/
                var originalBtnText = submitBtn.html();

                /*Change button text to loading state*/


                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();
                /** Use Ajax to send the delete request **/
                $.ajax({
                    type: 'POST',
                    'url': url,
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        form.find(':input').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 500);

                        }
                        if (response.success == false) {
                            toastr.error(response.message);
                        }
                    },

                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    },
                    complete: function() {
                        form.find(':input').prop('disabled', false);
                    }
                });
            });
            /** Delete Script **/
            $(document).on('click', "button[name='delete_button']", function() {
                var id = $(this).data('id');
                $('#DeleteId').text(id);
                $('#deleteModal').modal('show');

                $('#DeleteConfirm').off('click').on('click', function() {
                    $.ajax({
                        url: "include/pop_branch_server.php?delete_pop_branch_data=true",
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
                            toastr.error("Error deleting NAS: " + xhr.responseText);
                        }
                    });
                });
            }); 
        });
    </script>
    <script>
    document.querySelectorAll('.terminal-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            window.open(
                'http://103.112.206.139:7681',
                '_blank',
                'width=600,height=300'
            );
        });
    });
    </script>


</body>

</html>