<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    $ticket_query = $con->prepare('SELECT * FROM internal_tickets WHERE id = ?');
    $ticket_query->bind_param('i', $ticket_id);
    $ticket_query->execute();
    $ticket_result = $ticket_query->get_result();
    if($ticket_result->num_rows > 0) {
        $ticket = $ticket_result->fetch_assoc();
    } else {
        die('Ticket not found.');
    }
} else {
    die('Invalid request.');
}
?>

<!doctype html>
<html lang="en">

<?php

require 'Head.php';

?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Ticket Edit';
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
                        <div class="col-xl-8 col-lg-10 mx-auto">

                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="mdi mdi-ticket-account"></i> Ticket Edit
                                    </h5>
                                </div>

                                <form id="addTicketForm" action="include/tickets_server.php?update_internal_tickets_data=true"
                                    method="POST" enctype="multipart/form-data">

                                    <div class="card-body">

                                        <div class="row">
                                            <?php include 'Component/internal_tickets_form.php'; ?>
                                            
                                        </div>

                                    </div>

                                    <div class="card-footer text-end">
                                        <a href="internal_tickets.php" class="btn btn-danger">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Update Ticket
                                        </button>
                                    </div>

                                </form>

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
    <script type="text/javascript"></script>
    <script type="text/javascript">
        $('select').select2({
            width: '100%'
        });

        $('#addTicketForm').submit(function(e) {
            e.preventDefault();

            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();

            submitBtn.html(
                `<span class="spinner-border spinner-border-sm" role="status"></span> Loading...`
            ).prop('disabled', true);

            var form = this;
            var url = $(this).attr('action');

            var formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                processData: false,
                contentType: false, 
                dataType: 'json',

                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 500);
                    } else {
                        toastr.error(response.message);
                    }
                },

                error: function(xhr) {
                    toastr.error('Something went wrong');
                },

                complete: function() {
                    submitBtn.html(originalBtnText).prop('disabled', false);
                }
            });
        });
    </script>
</body>

</html>
