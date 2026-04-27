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

        <?php $page_title = 'NOC & Backbone';
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
                                            <p class="text-primary mb-0 hover-cursor"><a href="#">NOC & Backbone</a></p>
                                           
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
                                <a href="internal_tickets_create.php" class="btn btn-success">
                                    <i class="fas fa-ticket-alt me-1"></i> Add New Ticket
                                </a>
                            </div>

                                <div class="card-body">
                                    <div class="table-responsive ">
                                        <table id="tickets_table" class="table table-bordered dt-responsive nowrap"style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Ticket No</th>
                                                    <th>POP</th>
                                                    <th>Category</th>
                                                    <th>Sub Category</th>
                                                    <th>Assign Team</th>
                                                    <th>Status</th>
                                                    <th>Subject</th>
                                                    <th>Severity</th>
                                                    <th>Create By</th>
                                                    <th>Update By</th>
                                                    <th>Create Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody id="tickets-list">
                                                <?php
                                                $where = [];

                                                if (isset($_GET['department'])) {
                                                    if ($_GET['department'] == 'noc_backbone') {
                                                        $where[] = "t.pop_id != 0";
                                                    }

                                                    if ($_GET['department'] == 'upstream') {
                                                        $where[] = "t.upstream_id != 0";
                                                    }
                                                }

                                                if (isset($_GET['filter']) && $_GET['filter'] == 'today') {
                                                    $todayDate = date('Y-m-d');
                                                    $where[] = "DATE(t.created_at) = '$todayDate'";
                                                }

                                                $whereSql = '';

                                                if (!empty($where)) {
                                                    $whereSql = 'WHERE ' . implode(' AND ', $where);
                                                }
                                                $result = $con->query("
                                                        SELECT 
                                                            t.id,
                                                            t.ticket_no,
                                                            t.subject,
                                                            t.severity,
                                                            t.status,
                                                            t.downtime_minutes,
                                                            t.assigned_team,
                                                            t.created_at,
                                                            t.created_by,
                                                            t.updated_by,

                                                            p.name AS pop_name,
                                                            c.name AS category_name,
                                                            s.name AS subcategory_name,

                                                            uc.fullname AS created_by_name,
                                                            uu.fullname AS updated_by_name

                                                        FROM internal_tickets t

                                                        LEFT JOIN pop_branch p 
                                                            ON p.id = t.pop_id

                                                        LEFT JOIN ticket_categories c 
                                                            ON c.id = t.category_id

                                                        LEFT JOIN ticket_subcategories s 
                                                            ON s.id = t.subcategory_id

                                                        LEFT JOIN users uc
                                                            ON uc.id = t.created_by

                                                        LEFT JOIN users uu
                                                            ON uu.id = t.updated_by
                                                            $whereSql
                                                        ORDER BY t.id DESC
                                                    ");

                                                if ($result && $result->num_rows > 0):
                                                    while ($row = $result->fetch_assoc()):
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['ticket_no']) ?></td>

                                                        <td><?= htmlspecialchars($row['pop_name'] ?? '---') ?></td>

                                                        <td><?= htmlspecialchars($row['category_name']) ?></td>

                                                        <td><?= htmlspecialchars($row['subcategory_name']) ?></td>
                                                        <td>

                                                            <?= !empty($row['assigned_team']) 
                                                                ? ucfirst(str_replace('_', ' ', $row['assigned_team'])) 
                                                                : '---' ?>

                                                            <?php if ($row['status'] != 'closed' && $row['status'] != 'resolved'): ?>

                                                                <button 
                                                                    type="button"
                                                                    class="btn btn-sm btn-primary ms-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#assignTeamModal"
                                                                    data-ticket-id="<?= $row['id'] ?>"
                                                                    data-assigned-team="<?= $row['assigned_team'] ?>">
                                                                    <i class="fas fa-user-edit"></i>
                                                                </button>

                                                            <?php endif; ?>

                                                        </td>

                                                       <td>
                                                        <?php
                                                            $status_class = 'bg-secondary';

                                                            switch ($row['status']) {
                                                                case 'open':
                                                                    $status_class = 'bg-danger';
                                                                    break;

                                                                case 'in_progress':
                                                                    $status_class = 'bg-primary';
                                                                    break;

                                                                case 'pending_vendor':
                                                                    $status_class = 'bg-warning';
                                                                    break;

                                                                case 'resolved':
                                                                    $status_class = 'bg-info';
                                                                    break;

                                                                case 'closed':
                                                                    $status_class = 'bg-success';
                                                                    break;
                                                            }
                                                        ?>

                                                        <span class="badge <?= $status_class ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
                                                        </span>

                                                        <?php if ($row['status'] === 'closed' && !empty($row['downtime_minutes'])): ?>
                                                            <?php
                                                                $minutes = (int)$row['downtime_minutes'];
                                                                $hours = floor($minutes / 60);
                                                                $remaining_minutes = $minutes % 60;
                                                            ?>
                                                            <div class="small text-muted mt-1">
                                                                Downtime:
                                                                <?= $hours > 0 ? $hours . 'h ' : '' ?>
                                                                <?= $remaining_minutes ?>m
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if ($row['status'] !== 'closed' && $row['status'] !== 'resolved'): ?>
                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-primary ms-1"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#statusModal"
                                                                data-ticket-id="<?= $row['id'] ?>"
                                                                data-ticket-status="<?= $row['status'] ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>

                                                        <td><?= htmlspecialchars($row['subject']) ?></td>

                                                        <td>
                                                            <span class="badge bg-danger">
                                                                <?= ucfirst($row['severity']) ?>
                                                            </span>
                                                        </td>

                                                       <td>
                                                            <?= !empty($row['created_by_name']) 
                                                                ? htmlspecialchars($row['created_by_name']) 
                                                                : '---' ?>
                                                        </td>
                                                                                                                                                                                   <td>
                                                            <?= !empty($row['updated_by_name']) 
                                                                ? htmlspecialchars($row['updated_by_name']) 
                                                                : '---' ?>
                                                        </td>

                                                        <td><?= date('d M Y h:i A', strtotime($row['created_at'])) ?></td>

                                                        <td>
                                                            <a href="internal_ticket_edit.php?id=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <a href="internal_ticket_delete.php?id=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            <a href="internal_ticket_view.php?id=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-success">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    endwhile;
                                                else:
                                                ?>
                                                    <tr>
                                                        <td colspan="11" class="text-center">
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
    <!-- Modal for Send Message -->
    <div class="modal fade" id="assignTeamModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Update Assigned Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="assign_ticket_id">

                    <select id="assigned_team_select" class="form-select">
                        <option value="">---Select---</option>
                        <option value="noc">NOC</option>
                        <option value="fiber_team">Fiber Team</option>
                        <option value="system_admin">System Admin</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="saveAssignedTeam">
                        Update
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="status_ticket_id">

                    <select id="ticket_status_select" class="form-select">
                        <option value="">---Select---</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="pending_vendor">Pending Vendor</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                    <div class="mb-3" id="rca_box" style="display:none;">
                        <label class="form-label">RCA Note (Required for Close)</label>
                        <textarea id="rca_note" class="form-control" placeholder="Write root cause analysis..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="saveStatus">
                        Update
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script src="js/Ajax.js"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
           
            $('#tickets_table').DataTable();
            /** Delete Script **/
            $(document).on('click', "button[name='delete_button']", function() {
                var id = $(this).data('id');
                $('#DeleteId').text(id);
                $('#deleteModal').modal('show');

                $('#DeleteConfirm').off('click').on('click', function() {
                    $.ajax({
                        url: "include/customer_server.php?delete_customer_data=true",
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

                    if (status === 'closed') {
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
                if (status === 'closed' && rca_note.trim() === '') {
                    alert('RCA Note is required when closing a ticket');
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
