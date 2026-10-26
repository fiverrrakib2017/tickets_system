<?php
date_default_timezone_set('Asia/Dhaka');
/**----------Enable error reporting--------------**/ 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*-------- Database Connection------*/
if (file_exists('include/db_connect.php')) {
    include_once 'include/db_connect.php';
}
/*---------Security Token-------*/ 
if (file_exists('include/security_token.php')) {
    include_once 'include/security_token.php';
}
/*-------User Rights--------*/ 
if (file_exists('include/users_right.php')) {
    include_once  'include/users_right.php';
}
/*--------POP Security---------*/ 
if (file_exists('include/pop_security.php')) {
    include_once 'include/pop_security.php';
}

// checkPermission('role_management');
?>

<!doctype html>
<html lang="en">

<?php require 'Head.php';?>
<body data-sidebar="dark">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Role Management';
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
                                            <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Dashboard&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor">Role Management</p>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="card">
                            <div class="card-header customer_card_header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background-color: white;">
                                <!-- Add Role Button -->
                                <button type="button" data-bs-toggle="modal" data-bs-target="#add_role_modal" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i> Add New Role
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive ">
                                    <table id="role_management_datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="role_management_data">

                                        </tbody>
                                    </table>
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


    <div class="modal fade" id="add_role_modal" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeModalLabel">Create Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add_role_form">
                    <div class="modal-body">
                        <div class="mb-3 row align-items-center">
                            <label for="role_name" class="col-sm-4 col-form-label">Role Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" Placeholder="Enter Role Name" id="role_name" name="role_name" required>
                                    
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="btnSubmitRole">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_role_modal" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeModalLabel">Update Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_role_form" action="include/role_server.php?update_role_data=true">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="mb-3 row align-items-center">
                            <label for="role_name" class="col-sm-4 col-form-label">Role Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" Placeholder="Enter Role Name" id="role_name" name="role_name" required>
                                    
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END layout-wrapper -->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script  type="text/javascript">
        $(document).ready(function() {
            $('#role_management_datatable').DataTable({
                "searching": true,
                "paging": true,
                "info": true,
                "order": [
                    [1, "desc"]
                ],
                "lengthChange": true,
                "processing": true,
                "serverSide": true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                language: {
                    zeroRecords: "No matching Role found",
                    processing: `
                        <div class="d-flex justify-content-center align-items-center py-2">
                            <div class="spinner-border text-primary me-2" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                            <span>Loading Data...</span>
                        </div>`
                },
               
                "ajax": {
                    url: 'include/role_server.php?get_all_role_data=1',
                    type: 'GET',
                    cache: true,
                    data: function(d) {

                    },
                    beforeSend: function() {
                        $("#role_management_datatable .dataTables_empty").html(
                            '<img src="assets/images/loading.gif" style="background-color: transparent"/>'
                            );
                    },

                },
            });
            /*----------Delete Role Button---------------*/
            $(document).on('click', '.delete_role_button', function () {
                let id = $(this).data('id');
                if (!confirm('Are you sure want to delete this Role?')) {
                    return;
                }
                $.ajax({
                    url: 'include/role_server.php?delete_role_data=1',
                    type: 'POST',
                    data: {id: id},
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#role_management_datatable').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            });
            /*----------Add Role---------------*/
            $('#add_role_form').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let submitBtn = $('#btnSubmitRole');

                submitBtn.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: 'include/role_server.php?add_role_data=1',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        submitBtn.prop('disabled', false).text('Create Role');

                        if (response.success) {
                            toastr.success(response.message);
                            $('#add_role_modal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } 
                    },
                    error: function () {
                        submitBtn.prop('disabled', false).text('Create Role');
                        alert('An error occurred. Please try again.');
                    }
                });
            });
            $(document).on('click',"button[name='edit_role_button']",function(){
                var id=$(this).data('id');
                $.ajax({
                    url: "include/role_server.php?get_role_data=true", 
                    type: "GET",
                    data: { id: id }, 
                    dataType:'json',
                    success: function(response) {
                        if (response.success) {
                        $('#edit_role_modal').modal('show');
                        $('#edit_role_modal input[name="id"]').val(response.data.id);
                        $('#edit_role_modal input[name="role_name"]').val(response.data.name);
                        
                        } else {
                            toastr.error("Error fetching data for edit: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to fetch details');
                    }
                });
            });
            /*------------Update The data from the database table-------------*/
            $('#edit_role_modal form').submit(function(e){
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();

                /*---------Get the submit button-------------*/
                var submitBtn = form.find('button[type="submit"]');

                /*--------Save the original button text--------*/
                var originalBtnText = submitBtn.html();

                /*--------Change button text to loading state--------*/
                    

                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();
                /*------------- Use Ajax to send the delete request ------------*/
                $.ajax({
                    type:'POST',
                    'url':url,
                    data: formData,
                    dataType:'json',
                    beforeSend: function () {
                        form.find(':input').prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#edit_role_modal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 500);

                        }
                        if(response.success==false){
                            toastr.error(response.message);
                        }
                    },

                    error: function (xhr, status, error) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    },
                    complete:function(){
                        form.find(':input').prop('disabled', false);
                    }
                });
            });
        });
    </script>

</body>

</html>
