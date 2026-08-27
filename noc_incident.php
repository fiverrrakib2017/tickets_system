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
