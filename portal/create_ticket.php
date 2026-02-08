<?php
date_default_timezone_set('Asia/Dhaka');
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Tickets Management </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- DataTables -->
    <link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet"
        type="text/css">

    <!-- Bootstrap Touchspin -->
    <link href="../assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css">

    <!-- Select2 -->
    <link href="../assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Datepicker -->
    <link href="../assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

    <!-- Spectrum Colorpicker -->
    <link href="../assets/libs/spectrum-colorpicker2/spectrum.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Css -->
    <link href="../assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">

    <!-- Icons Css -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css">

    <!-- App Css -->
    <link href="../assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">

    <!-- Toastr Css -->
    <link rel="stylesheet" type="text/css" href="../css/toastr/toastr.min.css">

    <!-- Delete Modal Css -->
    <link rel="stylesheet" type="text/css" href="../css/deleteModal.css">

    <!-- Chartist Chart -->
    <link href="../assets/libs/chartist/chartist.min.css" rel="stylesheet" type="text/css">

    <!-- C3 Chart Css -->
    <link href="../assets/libs/c3/c3.min.css" rel="stylesheet" type="text/css">

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <!-- Extra CSS per page -->
    <?php if (!empty($extra_css)) {
        echo $extra_css;
    } ?>
</head>

<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="">


        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" style="margin-left: 0px !important; ">

            <div class="page-content" style="margin-top: 5px !important; padding: 0px !important;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="container">
                            <div class="main-body">
                                    <button type="button" onclick="history.back()" class="btn btn-danger mb-2">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </button>
                                <div class="row gutters-sm">
                                    <div class="row">
                                        <div class="col-xl-8 col-lg-10 mx-auto">

                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h5 class="mb-0">
                                                        <i class="mdi mdi-ticket-account"></i> Create Ticket
                                                    </h5>
                                                </div>

                                                <form id="addTicketForm"
                                                    action="../include/tickets_server.php?add_ticket_data=true"
                                                    method="POST" enctype="multipart/form-data">

                                                    <div class="card-body">

                                                        <div class="row">
                                                            <?php include '../Component/tickets_form.php'; ?>

                                                        </div>

                                                    </div>

                                                    <div class="card-footer text-end">
                                                        <button type="button" onclick="history.back()" class="btn btn-danger">
                                                            <i class="fas fa-arrow-left"></i> Back
                                                        </button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-save"></i> Create Ticket
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <!-- Modal for Ticket -->
            <?php
            // require "modal/tickets_modal.php";
            ?>

        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <!-- JAVASCRIPT -->
    <script src="../assets/libs/jquery/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../assets/libs/node-waves/waves.min.js"></script>
    <script src="../assets/libs/select2/js/select2.min.js"></script>
    <script src="../assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>

    <!-- Required datatable js -->
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

    <!-- Buttons examples -->
    <script src="../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="../assets/libs/jszip/jszip.min.js"></script>
    <script src="../assets/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="../assets/libs/pdfmake/build/vfs_fonts.js"></script>
    <script src="../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

    <!-- Responsive examples -->
    <script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- Toastr -->
    <script src="../js/toastr/toastr.min.js"></script>
    <script src="../js/toastr/toastr.init.js"></script>

    <!-- Datatable init js -->
    <script src="../assets/js/pages/datatables.init.js"></script>

    <!-- App Js -->
    <script src="../assets/js/app.js"></script>

    <!-- Peity chart -->
    <script src="../assets/libs/peity/jquery.peity.min.js"></script>

    <!-- C3 Chart -->
    <script src="../assets/libs/d3/d3.min.js"></script>
    <script src="../assets/libs/c3/c3.min.js"></script>

    <!-- jQuery Knob -->
    <script src="../assets/libs/jquery-knob/jquery.knob.min.js"></script>

    <!-- Dashboard init -->
    <script src="../assets/js/pages/dashboard.init.js"></script>

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    <!-- Fluid Meter -->
    <script src="../assets/js/js-fluid-meter.js"></script>
    <!-- Form Advanced Init -->
    <!-- <script src="assets/js/pages/form-advanced.init.js"></script>  -->

    <!-- Plugin Js for Charts -->
    <script src="../assets/libs/chartist/chartist.min.js"></script>
    <script src="../assets/libs/chartist-plugin-tooltips/chartist-plugin-tooltip.min.js"></script>

    <!-- form wizard -->
    <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>

    <!-- form wizard init -->
    <script src="../assets/js/pages/form-wizard.init.js"></script>
    <!-- Counter-Up -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>

    <!-- Include Tickets js File -->
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
