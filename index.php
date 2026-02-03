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
                    

                    <?php include 'Component/dashboard_card.php'; ?>
                    <?php include 'Component/dashboard_ip_card.php'; ?>
                    <?php include 'Component/chart.php'; ?>
                    <?php include 'Component/recent_ticket.php'; ?>

                    
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
    <script>
         $('#tickets_table').DataTable();
         $('#dashboard_customers_table').DataTable();
    </script>
</body>

</html>
