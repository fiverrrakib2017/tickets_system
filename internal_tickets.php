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
                                            <p class="text-primary mb-0 hover-cursor"><a href="internal_tickets_head.php">NOC & Backbone</a></p>
                                           
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
                                        <table id="tickets_table" class="table table-bordered dt-responsive nowrap"
       style="border-collapse: collapse; border-spacing: 0; width: 100%;">

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
            <th>Create Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody id="tickets-list">
        <?php
        $result = $con->query("
            SELECT 
                t.id,
                t.ticket_no,
                t.subject,
                t.severity,
                t.status,
                t.assigned_team,
                t.created_at,
                t.created_by,

                p.name AS pop_name,
                c.name AS category_name,
                s.name AS subcategory_name

            FROM internal_tickets t
            LEFT JOIN pop_branch p 
                ON p.id = t.pop_id
            LEFT JOIN ticket_categories c 
                ON c.id = t.category_id
            LEFT JOIN ticket_subcategories s 
                ON s.id = t.subcategory_id
            ORDER BY t.id DESC
        ");

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($row['ticket_no']) ?></td>

                <td><?= htmlspecialchars($row['pop_name']) ?></td>

                <td><?= htmlspecialchars($row['category_name']) ?></td>

                <td><?= htmlspecialchars($row['subcategory_name']) ?></td>

                <td>
                    <?= !empty($row['assigned_team']) 
                        ? ucfirst(str_replace('_', ' ', $row['assigned_team'])) 
                        : 'N/A' ?>
                </td>

                <td>
                    <span class="badge bg-warning">
                        <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
                    </span>
                </td>

                <td><?= htmlspecialchars($row['subject']) ?></td>

                <td>
                    <span class="badge bg-danger">
                        <?= ucfirst($row['severity']) ?>
                    </span>
                </td>

                <td><?= htmlspecialchars($row['created_by']) ?></td>

                <td><?= date('d M Y h:i A', strtotime($row['created_at'])) ?></td>

                <td>
                    <a href="internal_ticket_edit.php?id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="delete_internal_ticket.php?id=<?= $row['id'] ?>"
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
     <?php include 'modal/message_modal.php'; ?>
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
        });
    </script>
</body>

</html>
