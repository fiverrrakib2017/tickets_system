<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';
include 'include/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!doctype html>
<html lang="en">

<?php 

require 'Head.php';



?>
<style>

.stat-eye {
    display: none;
    color: #6c757d;
    transition: color 0.3s ease;
}
.stat-item:hover .stat-eye {
    display: inline;
}
.stat-eye:hover {
    color: #0d6efd; 
}


</style>
<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php
        $page_title = 'Welcome To Dashboard';
        include 'Header.php';
        ?>

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
                    <div class="row mb-2">
                        <div class="col-md-6 col-sm-12">
                            <!-- New Request -->
                            <a href="con_request.php" class="btn btn-warning  mb-1">   <i class="fas fa-user-clock"></i> New Request
                                <?php
                               
                                
                                ?>
                            </a>
                            <!-- Add Customer -->
                            <button type="button" data-bs-toggle="modal" data-bs-target="#addCustomerModal"
                                class=" btn btn-success mb-1">  <i class="fas fa-user-plus"></i> Add
                                Customer</button>
                        </div>
                    </div>       
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php include 'Footer.php';?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->                  
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>


<?php include 'modal/customer_modal.php';?>
<?php include 'script.php'; ?>
<script type="text/javascript">



    
    
</script>

</body>

</html>
