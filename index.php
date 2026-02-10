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
                    <div class="row">
                    <?php include 'Component/recent_ticket.php'; ?>
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Tickets Reports this month</h4>
                                    <div class="" id="tickets_report">

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
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>


    <?php include 'script.php'; ?>
    <script type="text/javascript"></script>
    <script>
         $('#tickets_table').DataTable();
         $('#dashboard_customers_table').DataTable();
         let today = new Date();

        /*-------First Month Start Date-------*/
        let start_date = new Date(today.getFullYear(), today.getMonth(), 1);

        /*-------First Month End Date-------*/
        let end_date = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        /*-----------Format Date------------*/
        function formatDate(date) {
            let d = date.getDate().toString().padStart(2, '0');
            let m = (date.getMonth() + 1).toString().padStart(2, '0');
            let y = date.getFullYear();
            return `${y}-${m}-${d}`;
        }

        start_date = formatDate(start_date);
        end_date   = formatDate(end_date);

         $.ajax({
            url: 'include/tickets_server.php?get_tickets_report_data=true',
            type: 'POST',
            dataType: 'json',
            data: {start_date: start_date, end_date:end_date},
            success: function(response) {
                if(response.success==true){
                    
                    $("#tickets_report").html(response.html);
                    $("#tickets_report_data_table").DataTable();
                   
                    
                }
                
                if(response.success==false) {
                    toastr.error(response.message);
                    $("#_data").html('<tr id="no-data"><td colspan="10" class="text-center">No data available</td></tr>');
                }
            },
        });
    </script>
</body>

</html>
