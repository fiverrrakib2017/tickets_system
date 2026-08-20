<?php
include 'include/security_token.php';
include 'include/db_connect.php';
include 'include/functions.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['clid'])){
    $clid = $_GET['clid'];
    $user=$con->query("SELECT * FROM users WHERE id='$clid'")->fetch_assoc();

    $top_today  = false; 
    $top_week   = false; 
    $top_month  = false; 
    $top_year   = false;
    
    /*---------Today Top Performer----------*/
    $sql = "
        SELECT tu.id
        FROM users tu
        JOIN ticket t ON t.assign_user_id = tu.id
        WHERE t.ticket_type = 'Complete'
        AND DATE(t.enddate) = CURDATE()
        GROUP BY tu.id
        ORDER BY COUNT(t.id) DESC
        LIMIT 1
    ";
    $result = $con->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $top_today = ((int)$row['id'] === $clid);
    }

    /*------- This Week Top Performer -------*/
    $sql = "
        SELECT tu.id
        FROM users tu
        JOIN ticket t ON t.assign_user_id = tu.id
        WHERE t.ticket_type = 'Complete'
        AND YEARWEEK(t.enddate, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY tu.id
        ORDER BY COUNT(t.id) DESC
        LIMIT 1
    ";

    $result = $con->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $top_week = ((int)$row['id'] === $clid);
    }
    /*---- This Month Top Performer -----*/
    $sql = "
        SELECT tu.id
        FROM users tu
        JOIN ticket t ON t.assign_user_id = tu.id
        WHERE t.ticket_type = 'Complete'
        AND MONTH(t.enddate) = MONTH(CURDATE())
        AND YEAR(t.enddate) = YEAR(CURDATE())
        GROUP BY tu.id
        ORDER BY COUNT(t.id) DESC
        LIMIT 1
    ";

    $result = $con->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $top_month = ((int)$row['id'] === $clid);
    }


    /*--------- This Year Top Performer ---------*/
    $sql = "
        SELECT tu.id
        FROM users tu
        JOIN ticket t ON t.assign_user_id = tu.id
        WHERE t.ticket_type = 'Complete'
        AND YEAR(t.enddate) = YEAR(CURDATE())
        GROUP BY tu.id
        ORDER BY COUNT(t.id) DESC
        LIMIT 1
    ";

    $result = $con->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $top_year = ((int)$row['id'] === $clid);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ticket System Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'style.php'; ?>
</head>
<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php $page_title = 'Users';
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
                        <div class="container">
                            <div class="main-body">
                                <div class="row gutters-sm">
                                    <div class="col-md-4 mb-3">
                                        <div class="card  p-3 mb-4 bg-white rounded text-center">
                                            <div class="card-body">
                                                <div class="d-flex flex-column align-items-center profile">
                                                    <!-- Profile Image -->

                                                    <img src="assets/images/<?php echo $user['profile_pic'] ?? 'avatar.png'; ?>"
                                                        class="rounded-circle border border-3 border-primary shadow-sm"
                                                        width="120" height="120" id="profilePreview"/>
                                                        <!-- Upload Button -->
                                                        <form id="profileImageForm" enctype="multipart/form-data" class="mt-2">
                                                            <label for="profileImageUpload" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-upload"></i> Change Photo
                                                            </label>
                                                            <input type="file" name="profile_image" id="profileImageUpload" accept="image/*" hidden />
                                                        </form>

                                                    <!-- Profile Details -->
                                                    <div class="mt-3">
                                                        <h6 class="text-primary fw-bold"><?php echo $user['fullname'] ?? '---'; ?></h6>

                                                       
                                                        <p class="text-muted mb-1">
                                                            <span class="badge bg-secondary">#
                                                                <?php echo $user['id']; ?></span>
                                                        </p>

                                                        <!-- User Since -->
                                                        <small class="text-muted">
                                                            <i class="far fa-calendar-alt"></i>
                                                           <?php
                                                            echo !empty($user['created_at'])
                                                                ? (new DateTime($user['created_at']))->format('d M, Y')
                                                                : '-';
                                                            ?>
                                                        </small>
                                                       
                                                        <!-- Action Buttons -->
                                                        <div class="mt-3">
                                                            <a href="user_profile_edit.php?clid=<?php echo $clid; ?>"
                                                            class="btn btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Edit Profile
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card border-0 rounded-4 shadow-sm">
                                            <div class="card-body p-0">
                                                
                                               
                                                <!-- Fullname -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-account me-2 text-primary fs-5"></i>
                                                            <span class="fw-bold">Fullname:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($user['fullname'] ?? '---'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- Email -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-email-outline me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Email:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['email'] ?? '---'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- Phone -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-phone me-2 text-info fs-5"></i>
                                                            <span class="fw-bold">Phone:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            <?php echo $user['mobile'];  ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- VLAN -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-network me-2 text-warning fs-5"></i>
                                                            <span class="fw-bold">Role:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($user['role'] ?? ''); ?></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-8">
                                        
                                        <div class="container">
                                           <div class="row">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <!-- Nav tabs -->
                                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                                            <ul class="nav nav-tabs nav-tabs-custom flex-nowrap overflow-auto w-100" role="tablist" style="gap: 5px;">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-bs-toggle="tab" href="#tickets" role="tab">
                                                                        <i class="mdi mdi-ticket-outline me-1"></i> Tickets
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <!-- Tab panes -->
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tickets" role="tabpanel">
                                                                <div class="table-responsive">
                                                                   <table id="tickets_table" class="table table-bordered dt-responsive nowrap"
                                                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                                        <thead>
                                                                            <?php include 'Table/tickets_head.php';?>
                                                                        </thead>
                                                                    <tbody id="tickets-list">
                                                                    <?php     
                                                                        $tickets = get_tickets($con, [
                                                                            'user_id' => $user['id']
                                                                        ]);
                                                                        include 'Table/tickets.php';
                                                                    ?>

                                                                    </tbody>

                                                                    </table>
                                                                </div>
                                                            </div>
                                                           
                                                        </div>
                                                    </div>
                                                </div>
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

            <?php include 'Footer.php'; ?>

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Add Modal -->
    <div class="modal fade bs-example-modal-lg" id="addModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span class="mdi mdi-account-check mdi-18px"></span> &nbsp;Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="include/users_server.php?add_user=true" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                        placeholder="Enter Your Fullname" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="username">User Name</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter Your Username" />

                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input type="text" class="form-control" id="password" name="password"
                                        placeholder="Enter Your Password" />
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mobile">Mobile no.</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                        placeholder="Enter Your Mobile Number" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter Your Email" />

                                </div>
                                <div class="form-group mb-3">
                                    <label for="role">Role</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="">Select</option>
                                        <option value="Super Admin">Super Admin</option>
                                        <option value="Normal User">Normal User</option>
                                    </select>
                                </div>

                            </div>
                        </div>



                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button data-bs-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade bs-example-modal-lg" id="editModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span class="mdi mdi-account-check mdi-18px"></span> &nbsp;Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="include/users_server.php?update_user=true" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group mb-3 d-none">
                                    <label for="fullname">User id</label>
                                    <input type="text" id="id" name="id" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                        placeholder="Enter Your Fullname" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="username">User Name</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter Your Username" />

                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input type="text" class="form-control" id="password" name="password"
                                        placeholder="Enter Your Password" />
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mobile">Mobile no.</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                        placeholder="Enter Your Mobile Number" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter Your Email" />

                                </div>
                                <div class="form-group mb-3">
                                    <label for="role">Role</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="">Select</option>
                                        <option value="Super Admin">Super Admin</option>
                                        <option value="Normal User">Normal User</option>
                                       
                                    </select>
                                </div>

                            </div>
                        </div>



                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button data-bs-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
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
            $('#tickets_table').DataTable();
            function confirmDelete(userId) {
                if (confirm("Are you sure you want to delete this user?")) {
                    window.location.href = "user_delete.php?id=" + userId;
                }
            }
            /*-------- Add------*/
            $('#addModal form').submit(function(e) {
                e.preventDefault();

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
                    }
                });
            });
            $(document).on('click',"button[name='edit_button']",function(){
                var id=$(this).data('id');
                $.ajax({
                    url: "include/users_server.php?get_user=true", 
                    type: "GET",
                    data: { id: id }, 
                    dataType:'json',
                    success: function(response) {
                        if (response.success) {
                        $('#editModal').modal('show');
                        $('#editModal input[name="id"]').val(response.data.id);
                        $('#editModal input[name="fullname"]').val(response.data.fullname);
                        $('#editModal input[name="username"]').val(response.data.username);
                        $('#editModal input[name="password"]').val(response.data.password);
                        $('#editModal input[name="mobile"]').val(response.data.mobile);
                        $('#editModal input[name="email"]').val(response.data.email);
                        $('#editModal select[name="role"]').val(response.data.role);
                        } else {
                            toastr.error("Error fetching data for edit: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to fetch department details');
                    }
                });
            });
            /*------------Update The data from the database table---------*/
            $('#editModal form').submit(function(e){
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
                            $('#editModal').modal('hide');
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
