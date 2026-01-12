<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!doctype html>
<html lang="en">

<?php

require 'Head.php';

?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Create Customer';
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
                                        <i class="mdi mdi-account-plus"></i> Create Customer
                                    </h5>
                                </div>

                                <form id="addCustomerForm" action="include/customer_server.php?add_customer_data=true"
                                    method="POST">

                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Customer Name</label>
                                                <input type="text" name="customer_name" class="form-control"
                                                    placeholder="Enter Customer Name" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="customer_email" class="form-control"
                                                    placeholder="Enter Customer Email">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" name="customer_phone" class="form-control"
                                                    placeholder="Enter Phone Number">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">POP / Area</label>
                                                <select name="customer_pop_branch" class="form-select" required>
                                                    <option value="">--- Select ---</option>
                                                    <?php
                                                    $pop = $con->query('SELECT * FROM pop_branch');
                                                    while ($row = $pop->fetch_assoc()) {
                                                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Connection Via</label>
                                                <select name="customer_type" class="form-select">
                                                    <option value="">--- Select ---</option>
                                                    <?php
                                                    $types = $con->query('SELECT * FROM customer_type');
                                                    while ($row = $types->fetch_assoc()) {
                                                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">VLAN</label>
                                                <input type="text" name="customer_vlan" class="form-control"
                                                    placeholder="Enter VLAN">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">IP Address</label>
                                                <input type="text" name="customer_ip" class="form-control"
                                                    placeholder="Enter IP Address">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="customer_status" class="form-select">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                            

                                            <?php include 'Component/customer_service_section.php'; ?>


                                        </div>

                                    </div>

                                    <div class="card-footer text-end">
                                        <a href="customers.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Create Customer
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
        // $('select').select2({
        //     width: '100%'
        // });
        $('#addCustomerForm').submit(function(e) {
            e.preventDefault();
            /*Get the submit button*/
            var submitBtn = $('#addCustomerForm').find('button[type="submit"]');

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
                        $('#addCustomerModal').modal('hide');
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
    </script>
</body>

</html>
