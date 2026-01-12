<?php
date_default_timezone_set('Asia/Dhaka');
include "include/security_token.php";
include "include/db_connect.php";
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if(isset($_GET['clid'])){
    $clid = $_GET['clid'];
    $customer_query = $con->query("SELECT * FROM customers WHERE id='$clid'");
    if($customer_query->num_rows > 0){
        $customer = $customer_query->fetch_assoc();
        $fullname = $customer['customer_name'];
        $mobile = $customer['customer_phone'];
        $profile_pic = $customer['profile_image'];
        $createdate = $customer['created_at'];
    } else {
        header("Location: customers.php");
        exit();
    }
} else {
    header("Location: customers.php");
    exit();
}
?>
<!doctype html>
<!doctype html>
<html lang="en">
<?php 
$extra_css  = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
require 'Head.php';

?>
<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php  $page_title = "Profile";include "Header.php";?>
        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!--- Sidemenu -->
                <?php include "Sidebar_menu.php"; ?>
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
                                        <div class="">
                                            <div class="card  p-3 mb-4 bg-white rounded text-center">
                                                <div class="card-body">
                                                    <div class="d-flex flex-column align-items-center profile">
                                                        <!-- Profile Image -->

                                                        <img src="assets/images/<?php echo $profile_pic ?? 'avatar.png'; ?>"
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
                                                            <h4 class="text-primary fw-bold"><?php echo $fullname ?? 'N/A'; ?></h4>
                                                            <p class="text-muted mb-1">
                                                                <span class="badge bg-secondary">#
                                                                    <?php echo $clid; ?></span>
                                                            </p>
                                                            <p class="text-dark fw-semibold">
                                                                <i class="fas fa-phone-alt text-success"></i>
                                                                <?php echo $mobile; ?>
                                                            </p>

                                                            <!-- User Since -->
                                                            <small class="text-muted">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <?php
                                                                $createdate = new DateTime(
                                                                    $createdate
                                                                );
                                                                echo $createdate->format(
                                                                    "d M, Y"
                                                                );
                                                                ?>
                                                            </small>

                                                           

                                                            <!-- Action Buttons -->
                                                            <div class="mt-3">
                                                                <a href="profile_edit.php?clid=<?php echo $clid; ?>"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-edit"></i> Edit Profile
                                                                </a>

                                                            </div>
                                                        </div>
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
                                                                    <table  id="tickets_table" class="table table-striped table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Complain Type</th>
                                                                                <th>Ticket Type</th>
                                                                                <th>Form Date</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody></tbody>
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


            <!-- Modal for Ticket -->
            <?php require "modal/tickets_modal.php"; ?>
          
            <?php include "Footer.php"; ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include "script.php"; ?>
  
    <!-- Include Tickets js File -->
    <script src="js/tickets.js"></script>
    <script type="text/javascript">
        $('#tickets_table').dataTable();


    </script>
</body>

</html>
