<?php
include("include/security_token.php");
include("include/db_connect.php");
?>

<!doctype html>
<html lang="en">

<?php require 'Head.php';?>

<body data-sidebar="dark">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Tickets Topic';
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
                                            <p class="text-primary mb-0 hover-cursor">Ticket Topic</p>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between align-items-end flex-wrap">
                                    <button class="btn btn-primary mt-2 mt-xl-0 mdi mdi-account-plus mdi-18px" data-bs-toggle="modal" data-bs-target="#addModal" style="margin-bottom: 12px;">&nbsp;&nbsp;Add New Topic</button>
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
                                                    <th>Ticket Topics</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $sql = "SELECT * FROM ticket_topic";
                                                $result = mysqli_query($con, $sql);

                                                while ($rows = mysqli_fetch_assoc($result)) {

                                                ?>

                                                    <tr>
                                                        <td><?php echo $rows['id']; ?></td>
                                                        <td><?php echo $rows["topic_name"]; ?></td>

                                                        <td style="text-align:right">

                                                            <button type="button" name="edit_button" data-id="<?php echo $rows['id']; ?>" class="btn-sm btn btn-info"><i class="fas fa-edit"></i></button>
                                                            <button type="button" name="delete_button" data-id="<?php echo $rows['id']; ?>" class="btn-sm btn btn-danger"><i class="fas fa-trash"></i></button>
                                                         

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
                            class="mdi mdi-lan mdi-18px"></span> &nbsp;Ticket Topic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="include/tickets_server.php?add_ticket_topic_data=true" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>Ticket Topic</label>
                                        <input type="text" name="topic_name" class="form-control" placeholder="Enter Ticket Topic">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add New Topic</button>
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
                            class="mdi mdi-lan mdi-18px"></span> &nbsp;Update Ticket Topic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="include/tickets_server.php?update_ticket_topic__data=true" method="POST" enctype="multipart/form-data">
                      <input type="text" name="id" id="id" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>Ticket Topic</label>
                                        <input type="text" name="topic_name" class="form-control" placeholder="Enter Ticket Topic">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Topic</button>
                    </div>
                </form>
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
                    url: "include/tickets_server.php?get_ticket_topic_data=true",
                    type: "GET",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editModal').modal('show');
                            $('#editModal input[name="id"]').val(response.data.id);
                            $('#editModal input[name="topic_name"]').val(response.data.topic_name);
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
                        url: "include/tickets_server.php?delete_ticket_topic_data=true",
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