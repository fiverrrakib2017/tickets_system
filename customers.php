<?php
include 'include/security_token.php';
include 'include/db_connect.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

?>

<!doctype html>
<html lang="en">
<?php 
$extra_css  = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
require 'Head.php';

?>

<body data-sidebar="dark">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Customers';
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
                                            <p class="text-primary mb-0 hover-cursor">&nbsp;/&nbsp;<a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor">Customers</p>
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
                               <div class="card-header customer_card_header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background-color: white;">
                                <!-- Add Customer Button -->
                                <button data-bs-toggle="modal" data-bs-target="#addCustomerModal" type="button" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i> Add New Customer
                                </button>
                            </div>

                                <div class="card-body">
                                    <div class="table-responsive ">
                                        <table id="customers_table" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Location</th>
                                                    <th>Type</th>
                                                    <th>IP</th>
                                                    <th>Bandwidth</th>
                                                    <th>Status</th> 
                                                    <th>Action</th> 
                                                </tr>
                                            </thead>
                                            <tbody id="customer-list">
                                            <?php
                                                $sql = "SELECT * FROM customers ORDER BY id DESC";
                                                $result = mysqli_query($con, $sql);

                                                while ($rows = mysqli_fetch_assoc($result)) {

                                                ?>

                                                    <tr>
                                                        <td><?php echo $rows['id']; ?></td>
                                                        <td><?php echo $rows["customer_name"]; ?></td>
                                                        <td><?php echo $rows["customer_email"]; ?></td>
                                                        <td><?php echo $rows["customer_phone"]; ?></td>
                                                        <td><?php echo $rows["customer_location"]; ?></td>
                                                        <td><?php echo $rows["customer_type_id"]; ?></td>
                                                        <td><?php echo $rows["customer_ip"]; ?></td>
                                                        <td><?php echo $rows["customer_bandwidth"]; ?></td>
                                                        <td><?php echo $rows["status"]; ?></td>

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

    <!-- Modal for Send Message -->
     <?php include 'modal/message_modal.php'; ?>
   
    <!------------------  Customer Modal ------------------>
    <?php require 'modal/customer_modal.php'; ?>
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script src="js/Ajax.js"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <script type="text/javascript">
        

        $(document).ready(function() {
            $('#customers_table').DataTable();
        });


    </script>
</body>

</html>
