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

    .noti-icon {
        position: relative;
    }

    .noti-icon i {
        font-size: 22px;
        color: #495057;
    }

    .noti-dot {
        position: absolute;
        top: 10px;
        right: 8px;
        font-size: 11px;
        padding: 2px 6px;
    }


    .chat-dropdown{
        width:320px;
        padding:0;
    }

    .chat-item{
        display:flex;
        align-items:center;
        gap:10px;
        padding:10px;
    }

    .chat-item:hover{
        background:#f8f9fa;
    }

    .chat-avatar{
        width:40px;
        height:40px;
        border-radius:50%;
        object-fit:cover;
    }

    .chat-info{
        flex:1;
        overflow:hidden;
    }

    .chat-name{
        font-weight:600;
        font-size:14px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .chat-text{
        font-size:13px;
        color:#6c757d;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .chat-time{
        font-size:12px;
        color:#adb5bd;
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
