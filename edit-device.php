<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$device_id = (int)$_GET['id'];

/* Customer Info */
$device = $con->query("
    SELECT * FROM devices 
    WHERE id = $device_id
")->fetch_assoc();

?>

<!doctype html>
<html lang="en">

<?php

require 'Head.php';

?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Edit Device';
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
                                        <i class="mdi mdi-account-edit"></i>Edit Device
                                    </h5>
                                </div>

                                <form id="editDeviceForm" action="include/device_server.php?update_device_data=true"
                                    method="POST">

                                    <div class="card-body">

                                        <div class="row">
                                            <?php include 'Component/device_form.php'; ?>
                                        </div>

                                    </div>

                                    <div class="card-footer text-end">
                                        <a href="devices.php" class="btn btn-danger">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-server"></i> Update Deivce
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
    <script type="text/javascript">
        
        $('#editDeviceForm').submit(function(e) {
            e.preventDefault();
            var submitBtn = $('#editDeviceForm').find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
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
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 500);
                    } else {
                        if (response.errors && Array.isArray(response.errors)) {
                            response.errors.forEach(function(err) {
                                toastr.error(err);
                            });
                        } 
                        else if (response.message) {
                            toastr.error(response.message);
                        } 
                        else {
                            toastr.error('Something went wrong!');
                        }
                    }
                },


                error: function(xhr, status, error) {
                    /*--------- Handle  errors --------*/
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
    </script>
</body>

</html>
