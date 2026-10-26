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
// checkPermission('user_logs');
?>
<!doctype html>
<html lang="en">
<?php include 'Head.php'; ?>

<body data-sidebar="dark">




    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'User logs';
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
                                            <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;<a
                                                    href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor">Users log</p>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>














                    <div class="row">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="users_logs_datatable" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>

                                                    <th>ID</th>
                                                    <th>User Name</th>
                                                    <th>IP</th>
                                                    <th>Time</th>
                                                    <th>Status</th>

                                                </tr>
                                            </thead>
                                            <tbody></tbody>
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



    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>


    <!-- JAVASCRIPT -->
    <?php include 'script.php'; ?>
    <script>
        $(document).ready(function() {
            table = $('#users_logs_datatable').DataTable({
                "searching": true,
                "paging": true,
                "info": false,
                "lengthChange": true,
                "processing": true,
                "serverSide": true,
                "zeroRecords": "No matching records found",
                "ajax": {
                    url: "include/users_server.php?get_user_logs_data=true",
                    type: 'GET',
                },
                "buttons": [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        titleAttr: 'Copy',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        titleAttr: 'Excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        titleAttr: 'CSV',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        },
                        orientation: 'landscape',
                        pageSize: "LEGAL",
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        titleAttr: 'PDF'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        titleAttr: 'Print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-list"></i> Column Visibility',
                        titleAttr: 'Column Visibility'
                    }
                ],
            });
        });
    </script>

</body>

</html>
